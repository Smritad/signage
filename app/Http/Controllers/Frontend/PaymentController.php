<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderDetail;
use App\Models\OrderStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    const PAYMENT_PENDING   = 'pending';
    const PAYMENT_PAID      = 'paid';
    const PAYMENT_COD       = 'cod';
    const PAYMENT_FAILED    = 'failed';
    const PAYMENT_CANCELLED = 'cancelled';
    const PAYMENT_EXPIRED   = 'expired';
    const PAYMENT_REFUNDED  = 'refunded';

    const METHOD_ONLINE = 'online';
    const METHOD_COD    = 'cod';

    /* ── round to whole rupees — charge, DB, and display all agree (no .00) ── */
    private function fmt(float $val): string
    {
        return number_format(round($val), 0, '.', '');
    }

    private function cleanImageFilename($input): ?string
    {
        if (empty($input)) return null;

        if (is_string($input) && str_starts_with(trim($input), '[')) {
            $decoded = json_decode($input, true);
            if (is_array($decoded) && !empty($decoded)) $input = $decoded[0];
        }
        if (is_array($input)) $input = $input[0] ?? null;
        if (empty($input)) return null;

        $input = trim((string) $input);
        $input = basename($input);
        if (str_contains($input, '?')) $input = strtok($input, '?');

        return $input ?: null;
    }

    public function storeTempOrder(Request $request)
    {
        $orderData = $request->order_data;
        $orderId   = 'TempOrder' . time() . rand(1000, 9999);
        Log::info('[storeTempOrder] Temp order created', ['temp_order_id' => $orderId]);
        session(["order_data_$orderId" => $orderData]);
        return response()->json(['status' => 'success', 'order_id' => $orderId]);
    }

    /* ══════════════════════════════════════════════════════════
     | MAIN ENTRY
     ══════════════════════════════════════════════════════════ */
    public function processPayment(Request $request)
    {
        $userId = Auth::guard('custom')->id();
        Log::info('[processPayment] START', ['user_id' => $userId, 'ip' => $request->ip()]);

        $orderData    = $request->input('order_data', []);
        $cartItems    = $orderData['cart_items']    ?? [];
        $customerInfo = $orderData['customer_info'] ?? [];
        $totals       = $orderData['totals']        ?? [];
        $paymentMode  = strtolower(trim($orderData['payment_method'] ?? self::METHOD_ONLINE));

        Log::info('[processPayment] Order data parsed', [
            'payment_mode'   => $paymentMode,
            'cart_count'     => count($cartItems),
            'total'          => $totals['total'] ?? 0,
            'customer_email' => $customerInfo['email'] ?? null,
        ]);

        if (empty($customerInfo['email'])) {
            Log::warning('[processPayment] Missing customer email — aborting');
            return response()->json(['error' => 'Customer email is required'], 400);
        }

        $totalAmount = $this->fmt(floatval($totals['total'] ?? 0));
        $isCod       = ($paymentMode === self::METHOD_COD);

        $billingAddress  = preg_replace('/[, ]*\b\d{6}\b/', '', $customerInfo['billing_address']  ?? '');
        $shippingAddress = preg_replace('/[, ]*\b\d{6}\b/', '', $customerInfo['shipping_address'] ?? '');

        Log::info('[processPayment] Building item arrays from cart');

        $productIds   = [];
        $productNames = [];
        $quantities   = [];
        $prices       = [];   // stored as whole-rupee strings
        $subtotals    = [];   // same
        $images       = [];
        $sizes        = [];
        $colors       = [];
        $offerIds     = [];
        $offerData    = [];

        foreach ($cartItems as $idx => $item) {
            $isOffer = !empty($item['is_offer']);
            $offerId = (int) ($item['offer_id'] ?? 0);

            $productIds[]   = $isOffer ? 0 : (int)($item['product_id'] ?? 0);
            $productNames[] = $item['product_name'] ?? '';
            $quantities[]   = (int)($item['quantity'] ?? 1);

            /* store as whole-rupee string — no float precision loss, no .00 */
            $prices[]    = $this->fmt((float)($item['price']   ?? 0));
            $subtotals[] = $this->fmt((float)($item['subtotal']
                            ?? ((float)($item['price'] ?? 0) * (int)($item['quantity'] ?? 1))));

            $sizes[]    = $item['size']  ?? '';
            $colors[]   = $item['print'] ?? '';
            $offerIds[] = $isOffer ? $offerId : 0;

            $rawImg   = $item['image'] ?? null;
            $images[] = $this->cleanImageFilename($rawImg) ?? 'default.png';

            Log::info("[processPayment] Cart item[$idx]", [
                'product_name' => $item['product_name'] ?? '',
                'is_offer'     => $isOffer,
                'offer_id'     => $offerId,
                'price'        => $item['price'] ?? 0,
                'qty'          => $item['quantity'] ?? 1,
            ]);

            if ($isOffer && $offerId > 0) {
                Log::info("[processPayment] Fetching offer data for offer_id=$offerId");
                $offer = DB::table('offers')->where('id', $offerId)->first();

                $cartRow = null;
                if (!empty($item['cart_id'])) {
                    $cartRow = DB::table('carts')->where('id', (int)$item['cart_id'])->first();
                    Log::info("[processPayment] Cart row fetched", [
                        'cart_id' => $item['cart_id'],
                        'found'   => (bool)$cartRow,
                    ]);
                }

                $selectedProducts = [];
                if ($cartRow && !empty($cartRow->combo_text)) {
                    $selectedProducts = json_decode($cartRow->combo_text, true) ?? [];
                    Log::info("[processPayment] Selected products from combo_text", ['count' => count($selectedProducts)]);
                }

                $offerData[] = [
                    'offer_id'         => $offerId,
                    'offer_name'       => $offer->offer_name        ?? ($item['product_name'] ?? ''),
                    'offer_price_type' => $offer->offer_price_type  ?? null,
                    'offer_price'      => $offer->offer_price       ?? null,
                    'offer_image'      => $offer->offer_image       ?? $this->cleanImageFilename($rawImg),
                    'final_price'      => $this->fmt((float)($item['price'] ?? 0)),
                    'mrp_total'        => $this->fmt((float)($item['mrp'] ?? $item['price'] ?? 0)),
                    'selected'         => $selectedProducts,
                ];
            } else {
                $offerData[] = null;
            }
        }

        $orderPayload = [
            'user_id'          => $userId,
            'customer_name'    => trim(($customerInfo['first_name'] ?? '') . ' ' . ($customerInfo['last_name'] ?? '')),
            'customer_email'   => $customerInfo['email']       ?? '',
            'customer_phone'   => $customerInfo['phone']       ?? '',
            'street'           => $customerInfo['street']      ?? '',
            'city'             => $customerInfo['city']        ?? '',
            'state'            => $customerInfo['state']       ?? '',
            'postal_code'      => $customerInfo['postal_code'] ?? '',
            'country'          => $customerInfo['country']     ?? '',
            'billing_address'  => trim($billingAddress),
            'shipping_address' => trim($shippingAddress),
            'description'      => $customerInfo['description'] ?? '',
            'total_price'      => $totalAmount,
            'status'           => $isCod ? 1 : 0,
            'payment_status'   => $isCod ? self::PAYMENT_COD : self::PAYMENT_PENDING,
            'payment_method'   => $isCod ? self::METHOD_COD  : self::METHOD_ONLINE,
            'product_ids'      => json_encode($productIds),
            'product_names'    => json_encode($productNames),
            'quantities'       => json_encode($quantities),
            'prices'           => json_encode($prices),
            'subtotals'        => json_encode($subtotals),
            'images'           => json_encode($images),
            'sizes'            => json_encode($sizes),
            'colors'           => json_encode($colors),
            'offer_ids'        => json_encode($offerIds),
            'offer_data'       => json_encode($offerData),
            'failure_reason'   => null,
            'updated_at'       => Carbon::now(),
        ];

        /* ── Retry: reuse existing failed/pending order ── */
        $order = null;
        if ($userId && !$isCod) {
            $existing = OrderDetail::where('user_id', $userId)
                ->whereIn('payment_status', [self::PAYMENT_FAILED, self::PAYMENT_PENDING, self::PAYMENT_EXPIRED])
                ->where('payment_method', self::METHOD_ONLINE)
                ->where('created_at', '>=', Carbon::now()->subHours(24))
                ->orderBy('created_at', 'desc')
                ->first();

            if ($existing) {
                $newOrderId = 'Order' . time() . rand(1000, 9999);
                Log::info('[processPayment] RETRY — reusing existing failed/pending order', [
                    'old_order_id' => $existing->order_id,
                    'new_order_id' => $newOrderId,
                    'old_status'   => $existing->payment_status,
                ]);
                $orderPayload['order_id'] = $newOrderId;
                $existing->update($orderPayload);
                $order = $existing->fresh();
                Log::info('[processPayment] Existing order updated for retry', ['order_id' => $newOrderId]);
            }
        }

        if (!$order) {
            $orderId = 'Order' . time() . rand(1000, 9999);
            $orderPayload['order_id']   = $orderId;
            $orderPayload['created_at'] = Carbon::now();
            Log::info('[processPayment] Creating NEW order', ['order_id' => $orderId]);
            $order = OrderDetail::create($orderPayload);
            Log::info('[processPayment] Order record created', ['db_id' => $order->id, 'order_id' => $orderId]);
        }

        /* ── FIX: update ALL user fields including city/state/country/street ── */
        if ($userId) {
            Log::info('[processPayment] Updating user profile', ['user_id' => $userId]);
            DB::table('custom_users')->where('id', $userId)->update([
                'name'             => trim(($customerInfo['first_name'] ?? '') . ' ' . ($customerInfo['last_name'] ?? '')),
                'mobile_no'        => $customerInfo['phone']        ?? '',
                'street'           => $customerInfo['street']       ?? '',
                'city'             => $customerInfo['city']         ?? '',
                'state'            => $customerInfo['state']        ?? '',
                'postal_code'      => $customerInfo['postal_code']  ?? '',
                'country'          => $customerInfo['country']      ?? '',
                'billing_address'  => $customerInfo['billing_address']  ?? '',
                'shipping_address' => $customerInfo['shipping_address'] ?? '',
                'updated_at'       => Carbon::now(),
            ]);
            Log::info('[processPayment] User profile updated', ['user_id' => $userId]);
        }

        /* ── COD path ── */
        if ($isCod) {
            Log::info('[processPayment] COD order — finalizing', ['order_id' => $order->order_id]);
            $this->finalizeCodOrder($order);
            Log::info('[processPayment] COD finalized', ['order_id' => $order->order_id]);
            return response()->json([
                'cod' => [
                    'success'      => true,
                    'order_id'     => $order->order_id,
                    'redirect_url' => route('order.confirm', ['order_id' => $order->order_id]),
                ],
            ]);
        }

        /* ═══ ONLINE — Cashfree ═══ */
        $appId     = "TEST108113612d802cb66f84b407ad9916311801";
        $secretKey = "cfsk_ma_test_67bf1fb60975ce59cf55abc683074d8a_1ff3a20a";
        $returnUrl = route('payment.verify', ['order_id' => $order->order_id]);

        $payload = [
            "order_id"         => $order->order_id,
            "order_amount"     => $totalAmount,
            "order_currency"   => "INR",
            "customer_details" => [
                "customer_id"    => (string)($userId ?? 'CUST' . rand(1000, 9999)),
                "customer_email" => $customerInfo['email'],
                "customer_phone" => $customerInfo['phone'] ?? '',
            ],
            "order_meta" => ["return_url" => $returnUrl],
        ];

        Log::info('[processPayment] Sending order to Cashfree', [
            'order_id'     => $order->order_id,
            'order_amount' => $totalAmount,
            'return_url'   => $returnUrl,
        ]);

        $headers = [
            "Content-Type: application/json",
            "x-client-id: $appId",
            "x-client-secret: $secretKey",
            "x-api-version: 2022-09-01",
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => "https://sandbox.cashfree.com/pg/orders",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => $headers,
        ]);
        $response  = curl_exec($curl);
        $curlError = curl_error($curl);
        $httpCode  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        Log::info('[processPayment] Cashfree API response', [
            'order_id'   => $order->order_id,
            'http_code'  => $httpCode,
            'curl_error' => $curlError ?: null,
            'response'   => $response,
        ]);

        if ($curlError) {
            $reason = 'Cashfree API curl error: ' . $curlError;
            Log::error('[processPayment] Curl error', ['order_id' => $order->order_id, 'reason' => $reason]);
            $this->markOrderFailed($order, $reason);
            return response()->json(['error' => $reason], 500);
        }

        $responseData = json_decode($response, true);
        if (!$responseData || !isset($responseData['payment_session_id'])) {
            $reason = 'Invalid Cashfree response — missing payment_session_id. Raw: ' . $response;
            Log::error('[processPayment] Invalid gateway response', ['order_id' => $order->order_id, 'reason' => $reason]);
            $this->markOrderFailed($order, $reason);
            return response()->json(['error' => 'Invalid JSON response', 'raw_response' => $response], 500);
        }

        Log::info('[processPayment] Cashfree session created', [
            'order_id'           => $order->order_id,
            'payment_session_id' => $responseData['payment_session_id'],
        ]);

        return response()->json([
            'cashfree' => [
                'payment_session_id' => $responseData['payment_session_id'],
                'order_id'           => $order->order_id,
                'order_amount'       => $totalAmount,
            ],
        ]);
    }

    /* ══════════════════════════════════════════════════════════
     | Decrement stock
     ══════════════════════════════════════════════════════════ */
    protected function decrementStock(OrderDetail $order): void
    {
        Log::info('[decrementStock] START', ['order_id' => $order->order_id]);

        $productIds = json_decode($order->product_ids, true) ?? [];
        $quantities = json_decode($order->quantities,  true) ?? [];
        $offerIds   = json_decode($order->offer_ids,   true) ?? [];
        $offerData  = json_decode($order->offer_data,  true) ?? [];

        foreach ($productIds as $i => $pid) {
            $oid = $offerIds[$i] ?? 0;

            if ($oid > 0) {
                $selected = $offerData[$i]['selected'] ?? [];
                Log::info("[decrementStock] Bundle row", ['offer_id' => $oid, 'child_count' => count($selected)]);
                foreach ($selected as $sel) {
                    if (!empty($sel['id'])) {
                        DB::table('products_details')->where('id', $sel['id'])->decrement('quantity', 1);
                        Log::info("[decrementStock] Decremented bundle product", ['product_id' => $sel['id']]);
                    }
                }
                continue;
            }

            if ($pid > 0) {
                $qty = (int)($quantities[$i] ?? 1);
                DB::table('products_details')->where('id', $pid)->decrement('quantity', $qty);
                Log::info("[decrementStock] Decremented product", ['product_id' => $pid, 'qty' => $qty]);
            }
        }

        Log::info('[decrementStock] DONE', ['order_id' => $order->order_id]);
    }

    /* ══════════════════════════════════════════════════════════
     | COD finalizer
     ══════════════════════════════════════════════════════════ */
    protected function finalizeCodOrder(OrderDetail $order)
    {
        Log::info('[finalizeCodOrder] START', ['order_id' => $order->order_id]);

        $this->decrementStock($order);

        Log::info('[finalizeCodOrder] Creating OrderStatus record');
        OrderStatus::create([
            'user_id'           => $order->user_id,
            'order_id'          => $order->order_id,
            'order_status'      => 'Order Placed',
            'payment_mode'      => $order->payment_method,
            'payment_status'    => self::PAYMENT_COD,
            'paid_amount'       => $order->total_price,
            'status_updated_at' => Carbon::now(),
            'status_updated_by' => $order->user_id,
        ]);

        /* FIX: update all user fields on COD too */
        if ($order->user_id) {
            Log::info('[finalizeCodOrder] Updating user profile', ['user_id' => $order->user_id]);
            DB::table('custom_users')->where('id', $order->user_id)->update([
                'name'             => $order->customer_name,
                'mobile_no'        => $order->customer_phone,
                'street'           => $order->street      ?? '',
                'city'             => $order->city        ?? '',
                'state'            => $order->state       ?? '',
                'postal_code'      => $order->postal_code ?? '',
                'country'          => $order->country     ?? '',
                'billing_address'  => $order->billing_address,
                'shipping_address' => $order->shipping_address,
                'updated_at'       => Carbon::now(),
            ]);
        }

        Log::info('[finalizeCodOrder] Clearing cart', ['user_id' => $order->user_id]);
        $this->clearUserCart($order->user_id);

        Log::info('[finalizeCodOrder] Generating and mailing invoice');
        $this->generateAndMailInvoice($order);

        Log::info('[finalizeCodOrder] DONE', ['order_id' => $order->order_id]);
    }

    /* ══════════════════════════════════════════════════════════
     | Payment verification
     ══════════════════════════════════════════════════════════ */
    public function verifyPayment(Request $request)
    {
        $orderId = $request->query('order_id');
        Log::info('[verifyPayment] START', ['order_id' => $orderId, 'ip' => $request->ip()]);

        try {
            if (!$orderId) {
                Log::warning('[verifyPayment] No order_id in request');
                return redirect()->route('show.checkout')->with('error', 'Order ID missing.');
            }

            $order = OrderDetail::where('order_id', $orderId)->first();
            if (!$order) {
                Log::warning('[verifyPayment] Order not found in DB', ['order_id' => $orderId]);
                return redirect()->route('show.checkout')->with('error', 'Order not found.');
            }

            Log::info('[verifyPayment] Order found', ['order_id' => $orderId, 'current_status' => $order->payment_status]);

            $appId     = "TEST108113612d802cb66f84b407ad9916311801";
            $secretKey = "cfsk_ma_test_67bf1fb60975ce59cf55abc683074d8a_1ff3a20a";

            /* Step 1: order-level status */
            Log::info('[verifyPayment] Fetching order status from Cashfree', ['order_id' => $orderId]);
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => "https://sandbox.cashfree.com/pg/orders/$orderId",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER     => [
                    "x-client-id: $appId",
                    "x-client-secret: $secretKey",
                    "x-api-version: 2022-09-01",
                ],
            ]);
            $response = curl_exec($curl);
            $curlErr  = curl_errno($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            Log::info('[verifyPayment] Cashfree order status response', [
                'order_id'  => $orderId,
                'http_code' => $httpCode,
                'curl_err'  => $curlErr,
                'response'  => $response,
            ]);

            if ($curlErr) {
                $reason = 'Curl error fetching order status: ' . $curlErr;
                Log::error('[verifyPayment] ' . $reason, ['order_id' => $orderId]);
                $this->markOrderFailed($order, $reason);
                return redirect()->route('show.checkout')->with('error', 'Payment verification failed.');
            }

            $data = json_decode($response, true);
            if (!$data) {
                $reason = 'Invalid JSON from Cashfree. Raw: ' . $response;
                Log::error('[verifyPayment] ' . $reason, ['order_id' => $orderId]);
                $this->markOrderFailed($order, $reason);
                return redirect()->route('show.checkout')->with('error', 'Invalid payment response.');
            }

            $cashfreeStatus = strtoupper($data['order_status'] ?? 'UNKNOWN');
            Log::info('[verifyPayment] Cashfree order_status', ['order_id' => $orderId, 'cashfree_status' => $cashfreeStatus]);

            /* Step 2: payment-level details */
            $paymentId     = null;
            $transactionId = null;
            $failureReason = null;

            try {
                Log::info('[verifyPayment] Fetching payment-level details', ['order_id' => $orderId]);
                $paymentResponse = Http::withHeaders([
                    "x-client-id"     => $appId,
                    "x-client-secret" => $secretKey,
                    "x-api-version"   => "2022-09-01",
                ])->get("https://sandbox.cashfree.com/pg/orders/$orderId/payments");

                $paymentData = $paymentResponse->json();
                Log::info('[verifyPayment] Payment-level data', ['order_id' => $orderId, 'data' => $paymentData]);

                if (!empty($paymentData) && is_array($paymentData)) {
                    $firstPayment       = $paymentData[0];
                    $paymentId          = $firstPayment['cf_payment_id']  ?? $firstPayment['payment_id']     ?? null;
                    $transactionId      = $firstPayment['bank_reference']  ?? $firstPayment['transaction_id'] ?? null;
                    $paymentLevelStatus = strtoupper($firstPayment['payment_status'] ?? '');
                    $paymentMessage     = $firstPayment['payment_message'] ?? null;
                    $errorDetails       = $firstPayment['error_details']   ?? null;

                    Log::info('[verifyPayment] Payment-level status', [
                        'order_id'             => $orderId,
                        'payment_level_status' => $paymentLevelStatus,
                        'payment_message'      => $paymentMessage,
                        'error_details'        => $errorDetails,
                        'cf_payment_id'        => $paymentId,
                        'bank_reference'       => $transactionId,
                    ]);

                    if (in_array($paymentLevelStatus, ['USER_DROPPED', 'FAILED', 'CANCELLED'])) {
                        $cashfreeStatus = $paymentLevelStatus;
                        $failureReason  = $paymentLevelStatus;
                        if ($paymentMessage) $failureReason .= ' — ' . $paymentMessage;
                        if ($errorDetails)   $failureReason .= ' | Details: ' . json_encode($errorDetails);

                        Log::warning('[verifyPayment] Payment-level failure', [
                            'order_id'       => $orderId,
                            'failure_reason' => $failureReason,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('[verifyPayment] Exception fetching payment details', [
                    'order_id' => $orderId,
                    'error'    => $e->getMessage(),
                    'trace'    => $e->getTraceAsString(),
                ]);
            }

            /* Step 3: map status */
            if ($cashfreeStatus === 'PAID') {
                $internalPaymentStatus = self::PAYMENT_PAID;
                $internalStatus        = 1;
            } elseif (in_array($cashfreeStatus, ['FAILED', 'USER_DROPPED', 'TERMINATED', 'CANCELLED'])) {
                $internalPaymentStatus = self::PAYMENT_FAILED;
                $internalStatus        = 2;
            } elseif ($cashfreeStatus === 'EXPIRED') {
                $internalPaymentStatus = self::PAYMENT_EXPIRED;
                $internalStatus        = 4;
            } else {
                $internalPaymentStatus = self::PAYMENT_PENDING;
                $internalStatus        = $order->status;
            }

            Log::info('[verifyPayment] Mapped internal status', [
                'order_id'                => $orderId,
                'internal_payment_status' => $internalPaymentStatus,
                'internal_status'         => $internalStatus,
            ]);

            $order->payment_id       = $paymentId;
            $order->payment_status   = $internalPaymentStatus;
            $order->status           = $internalStatus;
            $order->gateway_response = json_encode($data);
            $order->updated_at       = Carbon::now();
            $order->save();

            Log::info('[verifyPayment] Order record saved', ['order_id' => $orderId]);

            /* ── PAID ── */
            if ($cashfreeStatus === 'PAID') {
                Log::info('[verifyPayment] Payment SUCCESS', ['order_id' => $orderId]);
                OrderStatus::create([
                    'user_id'           => $order->user_id,
                    'order_id'          => $order->order_id,
                    'order_status'      => 'Payment Captured',
                    'payment_mode'      => $order->payment_method,
                    'payment_status'    => self::PAYMENT_PAID,
                    'payment_id'        => $paymentId,
                    'transaction_id'    => $transactionId,
                    'paid_amount'       => $order->total_price,
                    'status_updated_at' => Carbon::now(),
                    'status_updated_by' => $order->user_id,
                ]);
                $this->markOrderPaid($order, $paymentId);
                Log::info('[verifyPayment] Redirecting to confirmation', ['order_id' => $orderId]);
                return redirect()->route('order.confirm', ['order_id' => $order->order_id]);
            }

            /* ── FAILED / DROPPED / CANCELLED ── */
            if (in_array($cashfreeStatus, ['FAILED', 'USER_DROPPED', 'TERMINATED', 'CANCELLED'])) {
                $reason = $failureReason ?? ('Payment ' . strtolower($cashfreeStatus));
                Log::warning('[verifyPayment] Payment FAILED', ['order_id' => $orderId, 'reason' => $reason]);
                $this->markOrderFailed($order, $reason);
                return redirect()->route('show.checkout')->with('error', 'Payment failed. Please try again.');
            }

            /* ── EXPIRED ── */
            if ($cashfreeStatus === 'EXPIRED') {
                Log::warning('[verifyPayment] Payment EXPIRED', ['order_id' => $orderId]);
                $this->markOrderExpired($order, 'Payment session expired on Cashfree');
                return redirect()->route('show.checkout')->with('error', 'Payment link expired.');
            }

            Log::warning('[verifyPayment] Payment still PENDING', ['order_id' => $orderId, 'cashfree_status' => $cashfreeStatus]);
            return redirect()->route('show.checkout')->with('error', 'Payment is still pending.');

        } catch (\Exception $e) {
            Log::error('[verifyPayment] Unhandled exception', [
                'order_id' => $orderId ?? null,
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);
            return redirect()->route('show.checkout')->with('error', 'Payment verification failed.');
        }
    }

    /* ══════════════════════════════════════════════════════════
     | Status helpers
     ══════════════════════════════════════════════════════════ */
    protected function markOrderPaid(OrderDetail $order, $paymentId = null)
    {
        Log::info('[markOrderPaid] Marking PAID', ['order_id' => $order->order_id, 'payment_id' => $paymentId]);

        $order->update([
            'status'         => 1,
            'payment_status' => self::PAYMENT_PAID,
            'payment_method' => self::METHOD_ONLINE,
            'payment_id'     => $paymentId,
            'failure_reason' => null,
            'updated_at'     => Carbon::now(),
        ]);

        /* FIX: also update user profile on online payment success */
        if ($order->user_id) {
            Log::info('[markOrderPaid] Updating user profile on payment success', ['user_id' => $order->user_id]);
            DB::table('custom_users')->where('id', $order->user_id)->update([
                'name'             => $order->customer_name,
                'mobile_no'        => $order->customer_phone,
                'street'           => $order->street      ?? '',
                'city'             => $order->city        ?? '',
                'state'            => $order->state       ?? '',
                'postal_code'      => $order->postal_code ?? '',
                'country'          => $order->country     ?? '',
                'billing_address'  => $order->billing_address,
                'shipping_address' => $order->shipping_address,
                'updated_at'       => Carbon::now(),
            ]);
        }

        Log::info('[markOrderPaid] Decrementing stock', ['order_id' => $order->order_id]);
        $this->decrementStock($order);

        Log::info('[markOrderPaid] Clearing cart', ['order_id' => $order->order_id]);
        $this->clearUserCart($order->user_id);

        Log::info('[markOrderPaid] Generating invoice', ['order_id' => $order->order_id]);
        $this->generateAndMailInvoice($order);

        Log::info('[markOrderPaid] DONE', ['order_id' => $order->order_id]);
    }

    protected function markOrderFailed(OrderDetail $order, $reason = null)
    {
        Log::warning('[markOrderFailed] Marking FAILED', ['order_id' => $order->order_id, 'reason' => $reason]);
        $order->update([
            'status'         => 2,
            'payment_status' => self::PAYMENT_FAILED,
            'failure_reason' => $reason,
            'updated_at'     => Carbon::now(),
        ]);
        Log::warning('[markOrderFailed] DONE', ['order_id' => $order->order_id]);
    }

    protected function markOrderExpired(OrderDetail $order, $reason = null)
    {
        Log::warning('[markOrderExpired] Marking EXPIRED', ['order_id' => $order->order_id, 'reason' => $reason]);
        $order->update([
            'status'         => 4,
            'payment_status' => self::PAYMENT_EXPIRED,
            'failure_reason' => $reason,
            'updated_at'     => Carbon::now(),
        ]);
    }

    /* ══════════════════════════════════════════════════════════
     | Clear cart
     ══════════════════════════════════════════════════════════ */
    protected function clearUserCart($userId = null): void
    {
        if ($userId) {
            $deleted = DB::table('carts')->where('user_id', $userId)->delete();
            Log::info('[clearUserCart] Cleared by user_id', ['user_id' => $userId, 'rows_deleted' => $deleted]);
        } else {
            $sid = session()->getId();
            if ($sid) {
                $deleted = DB::table('carts')->where('session_id', $sid)->delete();
                Log::info('[clearUserCart] Cleared by session_id', ['session_id' => $sid, 'rows_deleted' => $deleted]);
            }
        }
    }

    /* ══════════════════════════════════════════════════════════
     | Admin actions
     ══════════════════════════════════════════════════════════ */
    public function cancelOrder(Request $request, $orderId)
    {
        Log::info('[cancelOrder] Admin cancel', ['order_id' => $orderId]);
        $order = OrderDetail::where('order_id', $orderId)->firstOrFail();

        if ($order->payment_status === self::PAYMENT_PAID && $order->payment_method === self::METHOD_ONLINE) {
            Log::warning('[cancelOrder] Cannot cancel paid online order', ['order_id' => $orderId]);
            return redirect()->back()->with('error', 'Paid online orders must be refunded, not cancelled.');
        }

        $order->update(['status' => 3, 'payment_status' => self::PAYMENT_CANCELLED, 'updated_at' => Carbon::now()]);
        Log::info('[cancelOrder] Order cancelled', ['order_id' => $orderId]);
        return redirect()->back()->with('success', 'Order cancelled.');
    }

    public function refundOrder(Request $request, $orderId)
    {
        Log::info('[refundOrder] Admin refund', ['order_id' => $orderId]);
        $order = OrderDetail::where('order_id', $orderId)->firstOrFail();

        if ($order->payment_status !== self::PAYMENT_PAID) {
            Log::warning('[refundOrder] Not paid — cannot refund', ['order_id' => $orderId]);
            return redirect()->back()->with('error', 'Only paid orders can be refunded.');
        }

        $order->update(['status' => 5, 'payment_status' => self::PAYMENT_REFUNDED, 'updated_at' => Carbon::now()]);
        Log::info('[refundOrder] Order refunded', ['order_id' => $orderId]);
        $this->generateAndMailInvoice($order, true);
        return redirect()->back()->with('success', 'Order refunded.');
    }

    public function markCodAsPaid(Request $request, $orderId)
    {
        Log::info('[markCodAsPaid] Admin marking COD paid', ['order_id' => $orderId]);
        $order = OrderDetail::where('order_id', $orderId)->firstOrFail();

        if ($order->payment_method !== self::METHOD_COD) {
            Log::warning('[markCodAsPaid] Not a COD order', ['order_id' => $orderId]);
            return redirect()->back()->with('error', 'This is not a COD order.');
        }

        $order->update(['payment_status' => self::PAYMENT_PAID, 'updated_at' => Carbon::now()]);
        Log::info('[markCodAsPaid] COD marked paid', ['order_id' => $orderId]);
        return redirect()->back()->with('success', 'COD payment confirmed.');
    }

    public function updatePaymentStatus(Request $request, $orderId)
    {
        $request->validate(['payment_status' => 'required|in:pending,paid,cod,failed,cancelled,expired,refunded']);
        Log::info('[updatePaymentStatus] Admin update', ['order_id' => $orderId, 'new_status' => $request->payment_status]);
        $order = OrderDetail::where('order_id', $orderId)->firstOrFail();
        $order->update(['payment_status' => $request->payment_status, 'updated_at' => Carbon::now()]);
        Log::info('[updatePaymentStatus] Done', ['order_id' => $orderId]);
        return redirect()->back()->with('success', 'Payment status updated.');
    }

    /* ══════════════════════════════════════════════════════════
     | INVOICE
     ══════════════════════════════════════════════════════════ */
    protected function generateAndMailInvoice(OrderDetail $order, $isRefund = false)
    {
        Log::info('[generateAndMailInvoice] START', ['order_id' => $order->order_id, 'is_refund' => $isRefund]);

        $invoiceNumber = $order->invoice_id ?: mt_rand(10000000, 99999999);
        $pdfDirectory  = public_path('/signage/invoices');

        if (!File::exists($pdfDirectory)) {
            File::makeDirectory($pdfDirectory, 0777, true, true);
            Log::info('[generateAndMailInvoice] Created invoice directory', ['path' => $pdfDirectory]);
        }

        $pdfPath = $pdfDirectory . '/invoice_' . $invoiceNumber . '.pdf';

        if (!$order->invoice_id) {
            $order->update(['invoice_id' => $invoiceNumber]);
            Log::info('[generateAndMailInvoice] Invoice number assigned', ['invoice_id' => $invoiceNumber]);
        }

        $invoiceItems = $this->buildInvoiceItems($order);
        Log::info('[generateAndMailInvoice] Invoice items built', ['item_count' => count($invoiceItems)]);

        try {
            Log::info('[generateAndMailInvoice] Generating PDF', ['pdf_path' => $pdfPath]);
            $pdf = Pdf::loadView('frontend.invoice_pdf', ['order' => $order, 'invoiceItems' => $invoiceItems]);
            $pdf->save($pdfPath);
            Log::info('[generateAndMailInvoice] PDF saved');

            $prefix = $isRefund ? 'Refund Processed' : 'Order Confirmation';
            Log::info('[generateAndMailInvoice] Sending mail', ['to' => $order->customer_email]);

            Mail::send('frontend.invoice_mail', ['order' => $order], function ($message) use ($order, $pdfPath, $prefix) {
                $message->to($order->customer_email)
                        ->cc('smrita@matrixbricks.com')
                        ->subject('Signage Wellness | ' . $prefix . ' - ' . $order->invoice_id)
                        ->attach($pdfPath);
            });

            Log::info('[generateAndMailInvoice] Mail sent', ['order_id' => $order->order_id]);

        } catch (\Exception $e) {
            Log::error('[generateAndMailInvoice] FAILED', [
                'order_id' => $order->order_id,
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);
        }
    }

    protected function buildInvoiceItems(OrderDetail $order): array
    {
        Log::info('[buildInvoiceItems] START', ['order_id' => $order->order_id]);

        $productIDs   = json_decode($order->product_ids,   true) ?? [];
        $productNames = json_decode($order->product_names, true) ?? [];
        $quantities   = json_decode($order->quantities,    true) ?? [];
        $prices       = json_decode($order->prices,        true) ?? [];
        $offerIds     = json_decode($order->offer_ids,     true) ?? [];
        $offerData    = json_decode($order->offer_data,    true) ?? [];

        $products = DB::table('products_details')
            ->whereIn('id', array_unique(array_filter($productIDs)))
            ->get()->keyBy('id');

        Log::info('[buildInvoiceItems] Products fetched', ['count' => $products->count()]);

        $invoiceItems = [];

        foreach ($productIDs as $i => $id) {
            $oid = $offerIds[$i] ?? 0;

            if ($oid > 0) {
                $od          = $offerData[$i] ?? [];
                $finalAmt    = (float)($od['final_price'] ?? $prices[$i] ?? 0);
                $mrpAmt      = (float)($od['mrp_total']   ?? $finalAmt);
                $discountAmt = max(0, $mrpAmt - $finalAmt);

                Log::info("[buildInvoiceItems] Offer row[$i]", ['offer_id' => $oid, 'final' => $finalAmt, 'mrp' => $mrpAmt]);

                $invoiceItems[] = [
                    'name'     => $od['offer_name'] ?? ($productNames[$i] ?? 'Bundle Offer'),
                    'quantity' => 1,
                    'rate'     => round($mrpAmt,      2),
                    'amount'   => round($finalAmt,    2),
                    'discount' => round($discountAmt, 2),
                    'isCombo'  => true,
                ];
                continue;
            }

            $product = $products[$id] ?? null;
            if (!$product) {
                Log::warning("[buildInvoiceItems] Product not found", ['product_id' => $id]);
                continue;
            }

            $qty         = (int)($quantities[$i] ?? 1);
            $paidPerU    = (float)($prices[$i] ?? 0);
            $mrpPerU     = (float)($product->price ?? $paidPerU);
            $name        = $productNames[$i] ?? $product->product_name;
            $totalMRP    = $mrpPerU  * $qty;
            $totalPaid   = $paidPerU * $qty;
            $discountAmt = max(0, $totalMRP - $totalPaid);

            Log::info("[buildInvoiceItems] Product row[$i]", ['product_id' => $id, 'name' => $name, 'qty' => $qty]);

            $invoiceItems[] = [
                'name'     => $name,
                'quantity' => $qty,
                'rate'     => round($totalMRP,    2),
                'amount'   => round($totalPaid,   2),
                'discount' => round($discountAmt, 2),
                'isCombo'  => false,
            ];
        }

        Log::info('[buildInvoiceItems] DONE', ['total_items' => count($invoiceItems)]);
        return $invoiceItems;
    }

    /* ══════════════════════════════════════════════════════════
     | ORDER CONFIRMATION PAGE
     ══════════════════════════════════════════════════════════ */
    public function order_confirmation(Request $request)
    {
        $orderId = $request->query('order_id');
        Log::info('[order_confirmation] Loading page', ['order_id' => $orderId]);

        $order = OrderDetail::where('order_id', $orderId)->first();
        if (!$order) {
            Log::warning('[order_confirmation] Order not found', ['order_id' => $orderId]);
            return redirect()->route('frontend.index')->with('error', 'Order not found.');
        }

        $order->city_name    = DB::table('main_cities')->where('id', $order->city)->value('name')       ?? 'N/A';
        $order->state_name   = DB::table('main_states')->where('id', $order->state)->value('name')      ?? 'N/A';
        $order->country_name = DB::table('main_countries')->where('id', $order->country)->value('name') ?? 'N/A';

        $productIDs   = json_decode($order->product_ids,   true) ?? [];
        $productNames = json_decode($order->product_names, true) ?? [];
        $prices       = json_decode($order->prices,        true) ?? [];
        $quantities   = json_decode($order->quantities,    true) ?? [];
        $orderImages  = json_decode($order->images,        true) ?? [];
        $offerIds     = json_decode($order->offer_ids,     true) ?? [];
        $offerData    = json_decode($order->offer_data,    true) ?? [];

        $products = DB::table('products_details')
            ->whereIn('id', array_unique(array_filter($productIDs)))
            ->get()->keyBy('id');

        Log::info('[order_confirmation] Building product list', ['item_count' => count($productIDs)]);

        $orderProducts = [];

        foreach ($productIDs as $i => $id) {
            $oid = $offerIds[$i] ?? 0;

            if ($oid > 0) {
                $od          = $offerData[$i] ?? [];
                $offerImg    = $od['offer_image'] ?? ($orderImages[$i] ?? 'default.png');
                $finalPrice  = (float)($od['final_price'] ?? $prices[$i] ?? 0);
                $mrpTotal    = (float)($od['mrp_total']   ?? $finalPrice);
                $discountAmt = max(0, $mrpTotal - $finalPrice);

                Log::info("[order_confirmation] Offer row[$i]", ['offer_id' => $oid]);

                $orderProducts[] = [
                    'name'             => $od['offer_name'] ?? ($productNames[$i] ?? 'Bundle Offer'),
                    'quantity'         => 1,
                    'fragrance'        => 'Bundle',
                    'price'            => $mrpTotal,
                    'offerPrice'       => $finalPrice,
                    'total'            => $mrpTotal,
                    'finalPrice'       => $finalPrice,
                    'discount'         => $discountAmt,
                    'hasOffer'         => $discountAmt > 0,
                    'isCombo'          => true,
                    'isOffer'          => true,
                    'image'            => asset('offerimage/' . $offerImg),
                    'imageIsFullUrl'   => true,
                    'selectedProducts' => $od['selected'] ?? [],
                ];
                continue;
            }

            $product = $products[$id] ?? null;
            if (!$product) {
                Log::warning("[order_confirmation] Product not found", ['product_id' => $id]);
                continue;
            }

            $qty         = (int)($quantities[$i] ?? 1);
            $mrpPerU     = (float)($product->price ?? 0);
            $paidPerU    = isset($prices[$i]) ? (float)$prices[$i] : $mrpPerU;
            $name        = $productNames[$i] ?? $product->product_name;
            $imageRaw    = $orderImages[$i] ?? null;

            if (empty($imageRaw)) {
                $pImages  = json_decode($product->images ?? '[]', true);
                $imageRaw = $pImages[0] ?? 'default.png';
            }

            $firstImage  = $this->cleanImageFilename($imageRaw) ?? 'default.png';
            $totalMRP    = $mrpPerU  * $qty;
            $totalOffer  = $paidPerU * $qty;
            $discountAmt = max(0, $totalMRP - $totalOffer);

            Log::info("[order_confirmation] Product row[$i]", ['product_id' => $id, 'name' => $name]);

            $orderProducts[] = [
                'name'           => $name,
                'quantity'       => $qty,
                'fragrance'      => 'NA',
                'price'          => $mrpPerU,
                'offerPrice'     => $paidPerU,
                'total'          => $totalMRP,
                'finalPrice'     => $totalOffer,
                'discount'       => $discountAmt,
                'hasOffer'       => $paidPerU < $mrpPerU,
                'isCombo'        => false,
                'isOffer'        => false,
                'image'          => $firstImage,
                'imageIsFullUrl' => false,
            ];
        }

        Log::info('[order_confirmation] Rendering view', ['order_id' => $orderId, 'product_count' => count($orderProducts)]);
        return view('frontend.order-confirmation', compact('order', 'orderProducts'));
    }

    /* ══════════════════════════════════════════════════════════
     | SHIPROCKET
     ══════════════════════════════════════════════════════════ */
    public function shiprocket($orderId)
    {
        Log::info('[shiprocket] START', ['order_id' => $orderId]);
        DB::table('order_details')->where('order_id', $orderId)->update(['is_shipped' => 1]);

        try {
            Log::info('[shiprocket] Authenticating');
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post('https://apiv2.shiprocket.in/v1/external/auth/login', [
                    'email'    => 'shweta@matrixbricks.com',
                    'password' => 'Dz1AkDSNn6Z^e2$A',
                ]);

            if ($response->failed()) {
                Log::error('[shiprocket] Auth failed', ['status' => $response->status(), 'body' => $response->body()]);
                return redirect()->back()->with('error', 'Shiprocket auth failed.');
            }

            $authData = $response->json();
            if (!isset($authData['token'])) {
                Log::error('[shiprocket] No token in auth response', ['response' => $authData]);
                return redirect()->back()->with('error', $authData['message'] ?? 'No token.');
            }

            Log::info('[shiprocket] Auth successful');
            Session::put('shiprocket_token', $authData['token']);
            Session::put('shiprocket_token_expiry', now()->addSeconds($authData['expires_in'] ?? 3600));

            $this->createShiprocketOrder($orderId, $authData['token']);
            Log::info('[shiprocket] Order created', ['order_id' => $orderId]);
            return redirect()->back()->with('success', 'Shiprocket order created.');

        } catch (\Exception $e) {
            Log::error('[shiprocket] Exception', ['order_id' => $orderId, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function createShiprocketOrder($orderId, $token = null)
    {
        Log::info('[createShiprocketOrder] START', ['order_id' => $orderId]);

        try {
            if (!$token) {
                $token = Session::get('shiprocket_token');
                $exp   = Session::get('shiprocket_token_expiry');
                if (!$token || now()->greaterThan($exp)) {
                    Log::info('[createShiprocketOrder] Token expired — re-authenticating');
                    $token = $this->shiprocket($orderId);
                }
            }

            $order = DB::table('order_details')->where('order_id', $orderId)->first();
            if (!$order) {
                Log::warning('[createShiprocketOrder] Order not found', ['order_id' => $orderId]);
                return back()->with('error', 'Order not found.');
            }

            $ba  = substr(preg_replace('/[^\p{L}\p{N}\/#,\-\.\(\) ]/u', '', $order->billing_address ?? ''), 0, 100);
            $ba2 = substr(preg_replace('/[^\p{L}\p{N}\/#,\-\.\(\) ]/u', '', $order->street         ?? ''), 0, 50);
            $bc  = DB::table('main_cities')->where('id', $order->city)->value('name')       ?? 'Mumbai';
            $bs  = DB::table('main_states')->where('id', $order->state)->value('name')      ?? 'Maharashtra';
            $bcn = DB::table('main_countries')->where('id', $order->country)->value('name') ?? 'India';
            $bp  = $order->postal_code ?? '400001';

            Log::info('[createShiprocketOrder] Address resolved', ['city' => $bc, 'state' => $bs, 'pincode' => $bp]);

            $parts = explode(' ', $order->customer_name ?? 'Customer');
            $fn    = $parts[0] ?? 'Customer';
            $ln    = $parts[1] ?? '';

            $decode = function ($v) {
                if (empty($v)) return [];
                $d = json_decode($v, true);
                if (is_string($d)) $d = json_decode($d, true);
                return is_array($d) ? $d : [];
            };

            $names    = $decode($order->product_names);
            $qtys     = $decode($order->quantities);
            $prcs     = $decode($order->prices);
            $offerIds = $decode($order->offer_ids);
            $offerDat = $decode($order->offer_data);

            $items = [];
            foreach ($names as $i => $n) {
                $oid = $offerIds[$i] ?? 0;

                if ($oid > 0) {
                    $selected = $offerDat[$i]['selected'] ?? [];
                    Log::info("[createShiprocketOrder] Expanding bundle offer[$i]", ['offer_id' => $oid, 'children' => count($selected)]);
                    foreach ($selected as $idx => $sel) {
                        $items[] = [
                            "name"          => substr(trim($sel['name'] ?? 'Bundle Item'), 0, 100),
                            "sku"           => 'BUNDLE-' . $oid . '-' . ($idx + 1),
                            "units"         => 1,
                            "selling_price" => 0,
                        ];
                    }
                    continue;
                }

                $items[] = [
                    "name"          => substr(trim($n), 0, 100),
                    "sku"           => 'SKU-' . ($i + 1),
                    "units"         => (int)  ($qtys[$i] ?? 1),
                    "selling_price" => (float)($prcs[$i] ?? 0),
                ];
            }

            if (count($items) === 0) {
                Log::error('[createShiprocketOrder] No items to ship', ['order_id' => $orderId]);
                return response()->json(['error' => 'No items.'], 422);
            }

            $weight  = (array_sum(array_map('intval', $qtys ?: [0])) ?: 1) * 0.15;
            $payload = [
                "order_id"               => $orderId,
                "order_date"             => now()->format('Y-m-d'),
                "pickup_location"        => 'warehouse',
                "channel_id"             => "0",
                "billing_customer_name"  => $fn,
                "billing_last_name"      => $ln,
                "billing_address"        => $ba,
                "billing_address_2"      => $ba2,
                "billing_city"           => $bc,
                "billing_pincode"        => $bp,
                "billing_state"          => $bs,
                "billing_country"        => $bcn,
                "billing_email"          => $order->customer_email ?? '',
                "billing_phone"          => $order->customer_phone ?? '',
                "shipping_is_billing"    => true,
                "shipping_customer_name" => $fn,
                "shipping_last_name"     => $ln,
                "shipping_address"       => $ba,
                "shipping_address_2"     => $ba2,
                "shipping_city"          => $bc,
                "shipping_pincode"       => $bp,
                "shipping_state"         => $bs,
                "shipping_country"       => $bcn,
                "shipping_email"         => $order->customer_email ?? '',
                "shipping_phone"         => $order->customer_phone ?? '',
                "order_items"            => $items,
                "payment_method"         => ($order->payment_method === 'cod') ? 'COD' : 'Prepaid',
                "sub_total"              => (float) $order->total_price,
                "length"                 => 8,
                "breadth"                => 4,
                "height"                 => 12,
                "weight"                 => $weight,
            ];

            Log::info('[createShiprocketOrder] Sending to Shiprocket', ['order_id' => $orderId, 'item_count' => count($items)]);

            $response = Http::withToken($token)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post('https://apiv2.shiprocket.in/v1/external/orders/create/adhoc', $payload);

            Log::info('[createShiprocketOrder] Shiprocket response', [
                'order_id'    => $orderId,
                'http_status' => $response->status(),
                'body'        => $response->body(),
            ]);

            if (!$response->successful()) {
                throw new \Exception("Shiprocket failed: " . $response->body());
            }

            $data = $response->json();
            if (!isset($data['order_id'])) {
                Log::error('[createShiprocketOrder] Missing Shiprocket order_id', ['data' => $data]);
                return response()->json(['error' => 'Missing Shiprocket order ID.'], 500);
            }

            DB::table('order_details')->where('order_id', $orderId)->update([
                'shipment_id'        => $data['shipment_id']        ?? null,
                'channel_order_id'   => $data['channel_order_id']   ?? null,
                'awb_code'           => $data['awb_code']           ?? null,
                'courier_company_id' => $data['courier_company_id'] ?? null,
                'courier_name'       => $data['courier_name']       ?? null,
                'courier_status'     => $data['status']             ?? null,
                'updated_at'         => now(),
            ]);

            Log::info('[createShiprocketOrder] Shiprocket order saved', ['order_id' => $orderId, 'shipment_id' => $data['shipment_id'] ?? null]);
            return $data;

        } catch (\Exception $e) {
            Log::error('[createShiprocketOrder] Exception', [
                'order_id' => $orderId,
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Shiprocket order creation failed'], 500);
        }
    }
}