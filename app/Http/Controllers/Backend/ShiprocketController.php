<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\OrderDetail;
use Exception;

class ShiprocketController extends Controller
{
    /* ══════════════════════════════════════════════════════════
     |  Shiprocket credentials & base URL
     ══════════════════════════════════════════════════════════ */
    private $srEmail    = 'shweta@matrixbricks.com';
    private $srPassword = 'Dz1AkDSNn6Z^e2$A';
    private $srBaseUrl  = 'https://apiv2.shiprocket.in/v1/external';

    /* ══════════════════════════════════════════════════════════
     |  LISTING — paid orders
     ══════════════════════════════════════════════════════════ */
    public function index()
    {
        $orders = OrderDetail::whereIn('payment_status', ['paid'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('backend.shiprocket.index', compact('orders'));
    }

    /* ══════════════════════════════════════════════════════════
     |  LISTING — failed orders
     ══════════════════════════════════════════════════════════ */
    public function showfailedOrderDetails()
    {
        $orders = OrderDetail::whereIn('payment_status', ['failed'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('backend.shiprocket.failed_data', compact('orders'));
    }

    /* ══════════════════════════════════════════════════════════
     |  LISTING — COD orders
     ══════════════════════════════════════════════════════════ */
    public function showCodOrderDetails()
    {
        $orders = OrderDetail::whereIn('payment_status', ['cod'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('backend.shiprocket.cod_data', compact('orders'));
    }

    /* ══════════════════════════════════════════════════════════
     |  MANUAL ORDER STATE  (admin dropdown)
     |  Cancelled by User → Refunded → Closed (or back to Active).
     |  payment_status is intentionally NOT changed so the order
     |  stays in its prepaid/COD list. Each change is logged with
     |  date/time + admin id in order_status_details.
     ══════════════════════════════════════════════════════════ */
    public function updateOrderState(Request $request, $id)
    {
        $request->validate([
            'order_state' => 'required|in:active,cancelled_by_user,refunded,closed',
            'remarks'     => 'nullable|string|max:1000',
        ]);

        $order = OrderDetail::findOrFail($id);
        $new   = $request->order_state === 'active' ? null : $request->order_state;

        $status = $order->status;
        if ($new === OrderDetail::STATE_CANCELLED_BY_USER) {
            $status = OrderDetail::STATUS_CANCELLED;
        } elseif ($new === OrderDetail::STATE_REFUNDED) {
            $status = OrderDetail::STATUS_REFUNDED;
        }

        $order->update([
            'order_state'    => $new,
            'order_state_at' => Carbon::now(),
            'status'         => $status,
            'updated_at'     => Carbon::now(),
        ]);

        $label = $new ? OrderDetail::stateOptions()[$new] : 'Active';

        // Admin's typed note (from the modal) — falls back to an auto message.
        $adminNote  = trim((string) $request->input('remarks', ''));
        $logRemarks = $adminNote !== ''
            ? $adminNote
            : 'Order status set to "' . $label . '" by admin';

        // Audit trail with date/time + admin id.
        try {
            DB::table('order_status_details')->insert([
                'user_id'           => $order->user_id,
                'order_id'          => $order->order_id,
                'order_status'      => $new ?? 'active',
                'payment_mode'      => strtolower($order->payment_method ?? '') === 'cod' ? 'cod' : 'online',
                'payment_status'    => $order->payment_status,
                'order_remarks'     => $logRemarks,
                'status_updated_by' => Auth::id(),
                'status_updated_at' => Carbon::now(),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('[updateOrderState] audit insert failed: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Order status updated to "' . $label . '".');
    }

    /* ══════════════════════════════════════════════════════════
     |  ORDER DETAILS — paid
     ══════════════════════════════════════════════════════════ */
    public function showOrderDetails($id)
    {
        $order = OrderDetail::findOrFail($id);

        // Refresh live shipment status so it shows on page load (no Track click).
        $this->syncTrackingStatus($order);

        [$invoiceItems, $subtotal, $totalSaved, $productNames, $quantities, $prices]
            = $this->buildInvoiceItems($order);

        $statusHistory = $this->orderStatusHistory($order->order_id);

        return view('backend.order-details-view', compact(
            'order', 'invoiceItems', 'subtotal', 'totalSaved',
            'productNames', 'quantities', 'prices', 'statusHistory'
        ));
    }

    /** Status-change history (with date/time) for an order, newest first. */
    private function orderStatusHistory($orderId)
    {
        return DB::table('order_status_details')
            ->where('order_id', $orderId)
            ->orderByDesc('status_updated_at')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Pull the latest shipment status from Shiprocket and persist it, so the
     * detail page shows live status on refresh WITHOUT clicking "Track".
     * Silent + resilient: any failure just leaves the stored status intact.
     */
    private function syncTrackingStatus(OrderDetail $order): void
    {
        if (empty($order->shipment_id)) {
            return;
        }

        try {
            $token = $this->getShiprocketToken();
            if (!$token) {
                return;
            }

            $response = Http::withToken($token)
                ->timeout(12)
                ->get("{$this->srBaseUrl}/courier/track/shipment/{$order->shipment_id}");

            if ($response->failed()) {
                return;
            }

            $tracking = $response->json()['tracking_data'] ?? null;
            if (!$tracking) {
                return;
            }

            $shipmentTrack = $tracking['shipment_track'][0] ?? [];

            $order->update([
                'awb_code'        => $shipmentTrack['awb_code']       ?? $order->awb_code,
                'courier_name'    => $shipmentTrack['courier_name']   ?? $order->courier_name,
                'courier_status'  => $shipmentTrack['current_status'] ?? $order->courier_status,
                'delivery_status' => $tracking['shipment_status']     ?? $order->delivery_status,
                'updated_at'      => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('[syncTrackingStatus] ' . $e->getMessage());
        }
    }

    /* ══════════════════════════════════════════════════════════
     |  ORDER DETAILS — failed
     ══════════════════════════════════════════════════════════ */
    public function OrderFailedDetails($id)
    {
        $order = OrderDetail::findOrFail($id);

        [$invoiceItems, $subtotal, $totalSaved, $productNames, $quantities, $prices]
            = $this->buildInvoiceItems($order);

        return view('backend.shiprocket.order-failed-details-view', compact(
            'order', 'invoiceItems', 'subtotal', 'totalSaved',
            'productNames', 'quantities', 'prices'
        ));
    }

    /* ══════════════════════════════════════════════════════════
     |  ORDER DETAILS — COD
     ══════════════════════════════════════════════════════════ */
    public function OrderCodDetails($id)
    {
        $order = OrderDetail::findOrFail($id);

        // Refresh live shipment status so it shows on page load (no Track click).
        $this->syncTrackingStatus($order);

        [$invoiceItems, $subtotal, $totalSaved, $productNames, $quantities, $prices]
            = $this->buildInvoiceItems($order);

        $statusHistory = $this->orderStatusHistory($order->order_id);

        return view('backend.shiprocket.order-cod-details-view', compact(
            'order', 'invoiceItems', 'subtotal', 'totalSaved',
            'productNames', 'quantities', 'prices', 'statusHistory'
        ));
    }

    /* ══════════════════════════════════════════════════════════
     |  SHARED invoice-item builder (replaces 3 copy-pasted blocks)
     ══════════════════════════════════════════════════════════ */
    private function buildInvoiceItems(OrderDetail $order): array
    {
        $productIDs   = $this->decodeText($order->product_ids);
        $productNames = $this->decodeText($order->product_names);
        $quantities   = $this->decodeText($order->quantities);
        $prices       = $this->decodeText($order->prices);
        $subtotals    = $this->decodeText($order->subtotals);
        $orderImages  = $this->decodeText($order->images);
        $offerIds     = $this->decodeText($order->offer_ids);   // may be []
        $offerDataArr = $this->decodeText($order->offer_data);  // may be []

        /* Pre-load normal products only (skip pid = 0 bundle placeholders) */
        $normalPids = array_values(array_filter($productIDs, fn($pid) => (int)$pid > 0));
        $products   = DB::table('products_details')
            ->whereIn('id', array_unique($normalPids))
            ->get()->keyBy('id');

        $invoiceItems = [];
        $subtotal     = 0;
        $totalSaved   = 0;

        foreach ($productIDs as $i => $pid) {
            $oid      = (int)($offerIds[$i] ?? 0);
            $quantity = (int)($quantities[$i] ?? 1);

            if ($oid > 0) {
                /* ── Bundle / offer row ── */
                $offerData = $this->decodeOfferElement($offerDataArr[$i] ?? null);

                $name     = $offerData['offer_name']  ?? ($productNames[$i] ?? 'Bundle Offer');
                $paidPerU = (float)($prices[$i]       ?? $offerData['final_price'] ?? 0);
                $mrpPerU  = (float)($offerData['mrp_total'] ?? $paidPerU);

                $imageRaw = $offerData['offer_image'] ?? ($orderImages[$i] ?? null);
                $imageUrl = $this->buildOfferImageUrl($imageRaw);

                $selected  = $offerData['selected'] ?? [];
                $lineMRP   = $mrpPerU  * $quantity;
                $linePaid  = $paidPerU * $quantity;
                $lineSaved = max(0, $lineMRP - $linePaid);
                $subtotal   += $linePaid;
                $totalSaved += $lineSaved;

                $invoiceItems[] = [
                    'name'          => $name,
                    'image'         => $imageUrl,
                    'quantity'      => $quantity,
                    'mrp_per_unit'  => $mrpPerU,
                    'paid_per_unit' => $paidPerU,
                    'line_mrp'      => $lineMRP,
                    'line_paid'     => $linePaid,
                    'line_saved'    => $lineSaved,
                    'has_offer'     => ($mrpPerU > $paidPerU),
                    'is_bundle'     => true,
                    'selected'      => $selected,
                ];

            } else {
                /* ── Normal product row ── */
                $product  = $products[$pid] ?? null;
                $paidPerU = (float)($prices[$i] ?? ($product->offer_price ?? $product->price ?? 0));
                $mrpPerU  = (float)($product->price ?? $paidPerU);
                $name     = $productNames[$i] ?? ($product->product_name ?? 'Product');

                $imageRaw = $orderImages[$i] ?? null;
                if (empty($imageRaw) && $product) {
                    $pImages  = json_decode($product->images ?? '[]', true);
                    $imageRaw = $pImages[0] ?? null;
                }
                $imageUrl = $this->buildImageUrl($imageRaw);

                $lineMRP   = $mrpPerU  * $quantity;
                $linePaid  = $paidPerU * $quantity;
                $lineSaved = max(0, $lineMRP - $linePaid);
                $subtotal   += $linePaid;
                $totalSaved += $lineSaved;

                $invoiceItems[] = [
                    'name'          => $name,
                    'image'         => $imageUrl,
                    'quantity'      => $quantity,
                    'mrp_per_unit'  => $mrpPerU,
                    'paid_per_unit' => $paidPerU,
                    'line_mrp'      => $lineMRP,
                    'line_paid'     => $linePaid,
                    'line_saved'    => $lineSaved,
                    'has_offer'     => ($mrpPerU > $paidPerU),
                    'is_bundle'     => false,
                    'selected'      => [],
                ];
            }
        }

        return [$invoiceItems, $subtotal, $totalSaved, $productNames, $quantities, $prices];
    }

    /* ══════════════════════════════════════════════════════════
     |  SHIP ORDER — universal flow for COD + Prepaid
     ══════════════════════════════════════════════════════════ */
    public function shipOrder($orderId)
    {
        try {
            $order = OrderDetail::where('order_id', $orderId)->first();

            if (!$order) {
                return redirect()->back()
                    ->with('error', 'Order not found.');
            }

            /* ── Guard: already shipped with a real shipment_id ── */
            if ($order->is_shipped && !empty($order->shipment_id)) {
                return redirect()->back()
                    ->with('error', "Order already shipped. Shipment ID: {$order->shipment_id}");
            }

            /* ── Detect payment type ── */
            $paymentType = $this->detectPaymentType($order);

            if ($paymentType === 'unknown') {
                return redirect()->back()
                    ->with('error', 'Cannot determine payment method (COD or Online). Please check order data.');
            }

            /* ── Block bad statuses ── */
            $ps = strtolower($order->payment_status ?? '');
            if (in_array($ps, ['failed', 'cancelled', 'expired', 'refunded'])) {
                return redirect()->back()
                    ->with('error', 'Cannot ship order with status: ' . strtoupper($ps));
            }

            /* ── For prepaid, must be paid ── */
            if ($paymentType === 'prepaid' && $ps !== 'paid') {
                return redirect()->back()
                    ->with('error', 'Prepaid order is not paid yet (status: ' . strtoupper($ps) . '). Cannot ship.');
            }

            /* ── COD: allow shipping even when payment_status = 'cod' ── */
            if ($paymentType === 'cod' && !in_array($ps, ['cod', 'paid'])) {
                return redirect()->back()
                    ->with('error', 'COD order has unexpected status: ' . strtoupper($ps) . '. Cannot ship.');
            }

            /* ── Authenticate ── */
            $token = $this->getShiprocketToken();
            if (!$token) {
                return redirect()->back()
                    ->with('error', 'Shiprocket authentication failed. Check credentials.');
            }

            /* ── Push to Shiprocket ── */
            $result = $this->createShiprocketOrder($order, $token, $paymentType);

            if (isset($result['error']) || empty($result['shipment_id'])) {
                $errorMsg = $result['error'] ?? 'Shiprocket did not return a shipment_id.';
                Log::error('[Shiprocket Ship] Failed', [
                    'order_id'     => $orderId,
                    'payment_type' => $paymentType,
                    'error'        => $errorMsg,
                    'result'       => $result,
                ]);
                return redirect()->back()->with('error', $errorMsg);
            }

            /* ── Persist Shiprocket data ── */
            DB::table('order_details')->where('order_id', $orderId)->update([
                'is_shipped'         => 1,
                'shipment_id'        => $result['shipment_id'],
                'channel_order_id'   => $result['channel_order_id']   ?? null,
                'awb_code'           => $result['awb_code']           ?? null,
                'courier_company_id' => $result['courier_company_id'] ?? null,
                'courier_name'       => $result['courier_name']       ?? null,
                'courier_status'     => $result['status']             ?? 'NEW',
                'updated_at'         => now(),
            ]);

            Log::info('[Shiprocket Ship] Success', [
                'order_id'     => $orderId,
                'payment_type' => strtoupper($paymentType),
                'shipment_id'  => $result['shipment_id'],
                'awb_code'     => $result['awb_code'] ?? null,
            ]);

            $msg = 'Order pushed to Shiprocket as '
                 . ($paymentType === 'cod' ? 'COD' : 'Prepaid')
                 . '. Shipment ID: ' . $result['shipment_id'];
            if (!empty($result['awb_code'])) {
                $msg .= ', AWB: ' . $result['awb_code'];
            }

            /* ── Redirect back to the correct list ── */
            $redirectRoute = ($paymentType === 'cod')
                ? 'cod-order-details.data'
                : 'shiprocket-details.index';

            return redirect()->route($redirectRoute)->with('success', $msg);

        } catch (Exception $e) {
            Log::error('[Shiprocket Ship] Exception', [
                'order_id' => $orderId,
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);
            return redirect()->back()
                ->with('error', 'Shiprocket error: ' . $e->getMessage());
        }
    }

    /* ══════════════════════════════════════════════════════════
     |  COD PENDING PAGE
     ══════════════════════════════════════════════════════════ */
    public function codPending()
    {
        $orders = OrderDetail::where('payment_method', 'cod')
            ->whereIn('payment_status', ['cod'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total_cod_orders'      => OrderDetail::where('payment_method', 'cod')->count(),
            'cod_pending'           => OrderDetail::where('payment_method', 'cod')->where('payment_status', 'cod')->count(),
            'cod_paid'              => OrderDetail::where('payment_method', 'cod')->where('payment_status', 'paid')->count(),
            'cod_delivered_unpaid'  => OrderDetail::where('payment_method', 'cod')
                                        ->where('payment_status', 'cod')
                                        ->where(function ($q) {
                                            $q->where('delivery_status', 'DELIVERED')
                                              ->orWhere('courier_status', 'DELIVERED');
                                        })->count(),
            'total_pending_amount'  => OrderDetail::where('payment_method', 'cod')->where('payment_status', 'cod')->sum('total_price'),
            'total_received_amount' => OrderDetail::where('payment_method', 'cod')->where('payment_status', 'paid')->sum('total_price'),
        ];

        return view('backend.cod-pending', compact('orders', 'stats'));
    }

    /* ══════════════════════════════════════════════════════════
     |  MANUAL — mark COD as paid
     ══════════════════════════════════════════════════════════ */
    public function markCodAsPaid(Request $request, $orderId)
    {
        $order = OrderDetail::where('order_id', $orderId)->first();
        if (!$order) return redirect()->back()->with('error', 'Order not found.');

        if ($order->payment_method !== 'cod') {
            return redirect()->back()->with('error', 'This is not a COD order.');
        }

        if ($order->payment_status === 'paid') {
            return redirect()->back()->with('error', 'Order is already paid.');
        }

        DB::table('order_details')->where('order_id', $orderId)->update([
            'payment_status' => 'paid',
            'payment_id'     => 'COD-MANUAL-' . now()->format('YmdHis'),
            'description'    => trim(($order->description ?? '') . "\n[COD PAID MANUALLY on " . now()->format('d M Y H:i') . ']'),
            'updated_at'     => now(),
        ]);

        Log::info('[COD] Marked paid manually', ['order_id' => $orderId, 'marked_by' => Auth::id()]);

        return redirect()->back()->with('success', "Order {$orderId} marked as PAID.");
    }

    /* ══════════════════════════════════════════════════════════
     |  UNDO — revert COD to unpaid
     ══════════════════════════════════════════════════════════ */
    public function markCodAsUnpaid(Request $request, $orderId)
    {
        $order = OrderDetail::where('order_id', $orderId)->first();
        if (!$order) return redirect()->back()->with('error', 'Order not found.');

        if ($order->payment_method !== 'cod') {
            return redirect()->back()->with('error', 'Not a COD order.');
        }

        DB::table('order_details')->where('order_id', $orderId)->update([
            'payment_status' => 'cod',
            'payment_id'     => null,
            'description'    => trim(($order->description ?? '') . "\n[COD UNPAID REVERTED on " . now()->format('d M Y H:i') . ']'),
            'updated_at'     => now(),
        ]);

        return redirect()->back()->with('success', "Order {$orderId} reverted to UNPAID.");
    }

    /* ══════════════════════════════════════════════════════════
     |  LIVE COD REMITTANCE CHECK (AJAX)
     ══════════════════════════════════════════════════════════ */
    public function checkCodRemittance($orderId)
    {
        $order = OrderDetail::where('order_id', $orderId)->first();
        if (!$order) return response()->json(['success' => false, 'message' => 'Order not found.']);

        if (empty($order->awb_code)) {
            return response()->json(['success' => false, 'message' => 'Order has no AWB code. Not shipped yet.']);
        }

        $token = $this->getShiprocketToken();
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Auth failed.']);
        }

        $response = Http::withToken($token)->get("{$this->srBaseUrl}/account/details/cod-remittance");

        if ($response->failed()) {
            return response()->json(['success' => false, 'message' => 'Remittance API failed.']);
        }

        $remitData = $response->json()['data'] ?? [];
        $found = null;

        foreach ($remitData as $remit) {
            $awbList = $remit['awb_list'] ?? $remit['awbs'] ?? [];
            if (is_string($awbList)) $awbList = array_map('trim', explode(',', $awbList));
            if (in_array($order->awb_code, (array)$awbList)) {
                $found = $remit;
                break;
            }
        }

        if (!$found) {
            return response()->json([
                'success' => true,
                'found'   => false,
                'message' => 'Not in any remittance batch yet. Shiprocket normally remits 8 days after delivery.',
            ]);
        }

        $status = strtolower($found['status'] ?? '');

        if (in_array($status, ['paid', 'success'])) {
            DB::table('order_details')->where('order_id', $orderId)->update([
                'payment_status' => 'paid',
                'payment_id'     => 'COD-REMIT-' . ($found['id'] ?? 'AUTO'),
                'updated_at'     => now(),
            ]);
            return response()->json([
                'success' => true,
                'found'   => true,
                'paid'    => true,
                'message' => 'Remittance confirmed. Marked as PAID.',
            ]);
        }

        return response()->json([
            'success' => true,
            'found'   => true,
            'paid'    => false,
            'message' => 'Remittance status: ' . strtoupper($status),
        ]);
    }

    /* ══════════════════════════════════════════════════════════
     |  TRACK ORDER (AJAX — used by COD detail view)
     ══════════════════════════════════════════════════════════ */
    public function trackOrder($orderId)
    {
        $order = OrderDetail::where('order_id', $orderId)
            ->orWhere('id', $orderId)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.']);
        }

        if (empty($order->shipment_id)) {
            return response()->json(['success' => false, 'message' => 'Order not yet shipped. No shipment ID found.']);
        }

        $token = $this->getShiprocketToken();
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Shiprocket auth failed.']);
        }

        $response = Http::withToken($token)
            ->get("{$this->srBaseUrl}/courier/track/shipment/{$order->shipment_id}");

        if ($response->failed()) {
            return response()->json(['success' => false, 'message' => 'Tracking API call failed.']);
        }

        $data     = $response->json();
        $tracking = $data['tracking_data'] ?? null;

        if (!$tracking) {
            return response()->json(['success' => false, 'message' => 'No tracking data returned.']);
        }

        $shipmentTrack = $tracking['shipment_track'][0] ?? [];

        /* Persist latest status */
        DB::table('order_details')->where('order_id', $order->order_id)->update([
            'awb_code'       => $shipmentTrack['awb_code']        ?? $order->awb_code,
            'courier_name'   => $shipmentTrack['courier_name']    ?? $order->courier_name,
            'courier_status' => $shipmentTrack['current_status']  ?? $order->courier_status,
            'delivery_status'=> $tracking['shipment_status']      ?? $order->delivery_status,
            'updated_at'     => now(),
        ]);

        return response()->json([
            'success'        => true,
            'current_status' => $shipmentTrack['current_status'] ?? 'Unknown',
            'courier_name'   => $shipmentTrack['courier_name']   ?? 'N/A',
            'awb_code'       => $shipmentTrack['awb_code']       ?? $order->awb_code,
            'etd'            => $shipmentTrack['etd']            ?? null,
            'tracking_url'   => $tracking['track_url']           ?? null,
            'activities'     => $tracking['shipment_track_activities'] ?? [],
        ]);
    }

    /* ══════════════════════════════════════════════════════════
     |  PRIVATE HELPERS
     ══════════════════════════════════════════════════════════ */

    /**
     * Detect COD vs Prepaid.
     * Priority: payment_method field → payment_status fallback.
     */
    private function detectPaymentType(OrderDetail $order): string
    {
        $pm = strtolower(trim($order->payment_method ?? ''));
        $ps = strtolower(trim($order->payment_status ?? ''));

        if ($pm === 'cod')    return 'cod';
        if ($pm === 'online') return 'prepaid';

        /* Fallback from payment_status when payment_method is empty */
        if ($ps === 'cod')  return 'cod';
        if ($ps === 'paid') return 'prepaid';

        return 'unknown';
    }

    /**
     * Get (or refresh) a cached Shiprocket token.
     */
    private function getShiprocketToken(): ?string
    {
        $cachedToken = Session::get('shiprocket_token');
        $expiry      = Session::get('shiprocket_token_expiry');

        if ($cachedToken && $expiry && now()->lt(Carbon::parse($expiry))) {
            return $cachedToken;
        }

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("{$this->srBaseUrl}/auth/login", [
                'email'    => $this->srEmail,
                'password' => $this->srPassword,
            ]);

        if ($response->failed() || empty($response->json()['token'])) {
            Log::error('[Shiprocket Auth] Failed', ['response' => $response->body()]);
            return null;
        }

        $data  = $response->json();
        $token = $data['token'];
        Session::put('shiprocket_token', $token);
        Session::put('shiprocket_token_expiry', now()->addSeconds(($data['expires_in'] ?? 3600) - 60));

        return $token;
    }

    /**
     * Build and push the order payload to Shiprocket.
     * Works for both COD and Prepaid.
     */
    private function createShiprocketOrder(OrderDetail $order, string $token, string $paymentType): array
    {
        try {
            $shiprocketPaymentMethod = ($paymentType === 'cod') ? 'COD' : 'Prepaid';

            $ba  = substr(preg_replace('/[^\p{L}\p{N}\/#,\-\.\(\) ]/u', '', $order->billing_address ?? ''), 0, 100);
            $ba2 = substr(preg_replace('/[^\p{L}\p{N}\/#,\-\.\(\) ]/u', '', $order->street ?? ''), 0, 50);
            $bc  = DB::table('main_cities')->where('id', $order->city)->value('name')       ?? 'Mumbai';
            $bs  = DB::table('main_states')->where('id', $order->state)->value('name')      ?? 'Maharashtra';
            $bcn = DB::table('main_countries')->where('id', $order->country)->value('name') ?? 'India';
            $bp  = $order->postal_code ?? '400001';

            $parts = explode(' ', $order->customer_name ?? 'Customer', 2);
            $fn    = $parts[0] ?? 'Customer';
            $ln    = $parts[1] ?? 'NA';

            $names    = $this->decodeText($order->product_names);
            $qtys     = $this->decodeText($order->quantities);
            $prcs     = $this->decodeText($order->prices);
            $offerIds = $this->decodeText($order->offer_ids);   // may be [] for old COD orders
            $offerDat = $this->decodeText($order->offer_data);  // may be []

            if (count($names) === 0) {
                return ['error' => 'No order items found.'];
            }

            $items = [];
            foreach ($names as $i => $n) {
                $oid = (int)($offerIds[$i] ?? 0);

                if ($oid > 0) {
                    /* Bundle: expand into child products */
                    $offerData = $this->decodeOfferElement($offerDat[$i] ?? null);
                    $selected  = $offerData['selected'] ?? [];

                    if (!empty($selected)) {
                        foreach ($selected as $idx => $sel) {
                            $items[] = [
                                'name'          => substr(trim($sel['name'] ?? 'Bundle Item'), 0, 100),
                                'sku'           => 'BUNDLE-' . $oid . '-' . ($i + 1) . '-' . ($idx + 1),
                                'units'         => (int)($sel['qty'] ?? $sel['quantity'] ?? 1),
                                'selling_price' => (float)($sel['price'] ?? 0),
                            ];
                        }
                    } else {
                        /* Fallback: use the offer row itself */
                        $items[] = [
                            'name'          => substr(trim($n ?: 'Bundle Offer'), 0, 100),
                            'sku'           => 'BUNDLE-' . $oid . '-' . ($i + 1),
                            'units'         => (int)($qtys[$i] ?? 1),
                            'selling_price' => (float)($prcs[$i] ?? 0),
                        ];
                    }
                } else {
                    /* Normal product */
                    $items[] = [
                        'name'          => substr(trim($n ?: 'Product'), 0, 100),
                        'sku'           => 'SKU-' . $order->id . '-' . ($i + 1),
                        'units'         => (int)($qtys[$i] ?? 1),
                        'selling_price' => (float)($prcs[$i] ?? 0),
                    ];
                }
            }

            if (count($items) === 0) {
                return ['error' => 'No shippable items could be built from this order.'];
            }

            $totalQty = array_sum(array_map('intval', $qtys ?: [1]));
            $weight   = max(0.5, $totalQty * 0.15);

            $payload = [
                'order_id'               => $order->order_id,
                'order_date'             => Carbon::parse($order->created_at)->format('Y-m-d'),
                'pickup_location'        => 'warehouse',
                'channel_id'             => '0',
                'billing_customer_name'  => $fn,
                'billing_last_name'      => $ln,
                'billing_address'        => $ba,
                'billing_address_2'      => $ba2,
                'billing_city'           => $bc,
                'billing_pincode'        => $bp,
                'billing_state'          => $bs,
                'billing_country'        => $bcn,
                'billing_email'          => $order->customer_email ?? '',
                'billing_phone'          => $order->customer_phone ?? '',
                'shipping_is_billing'    => true,
                'shipping_customer_name' => $fn,
                'shipping_last_name'     => $ln,
                'shipping_address'       => $ba,
                'shipping_address_2'     => $ba2,
                'shipping_city'          => $bc,
                'shipping_pincode'       => $bp,
                'shipping_state'         => $bs,
                'shipping_country'       => $bcn,
                'shipping_email'         => $order->customer_email ?? '',
                'shipping_phone'         => $order->customer_phone ?? '',
                'order_items'            => $items,
                'payment_method'         => $shiprocketPaymentMethod,
                'sub_total'              => (float)$order->total_price,
                'length'                 => 10,
                'breadth'                => 10,
                'height'                 => 10,
                'weight'                 => $weight,
            ];

            Log::info('[Shiprocket] Creating order', [
                'order_id'       => $order->order_id,
                'payment_method' => $shiprocketPaymentMethod,
                'items'          => count($items),
            ]);

            $response   = Http::withToken($token)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->srBaseUrl}/orders/create/adhoc", $payload);

            $statusCode = $response->status();
            $body       = $response->json() ?? [];

            Log::info('[Shiprocket] API response', [
                'order_id'    => $order->order_id,
                'status_code' => $statusCode,
                'body'        => $body,
            ]);

            if (!$response->successful()) {
                $msg = $body['message'] ?? 'Shiprocket rejected the order.';
                if (!empty($body['errors']) && is_array($body['errors'])) {
                    $errs = [];
                    foreach ($body['errors'] as $field => $fieldErrors) {
                        $errs[] = $field . ': ' . (is_array($fieldErrors)
                            ? implode(', ', $fieldErrors)
                            : $fieldErrors);
                    }
                    $msg = implode(' | ', $errs);
                }
                return ['error' => "Shiprocket ({$statusCode}): {$msg}"];
            }

            if (empty($body['shipment_id'])) {
                return ['error' => 'Shiprocket response missing shipment_id. Body: ' . json_encode($body)];
            }

            if (isset($body['status_code']) && $body['status_code'] != 1) {
                return ['error' => 'Shiprocket status: ' . ($body['status'] ?? 'Unknown')];
            }

            return $body;

        } catch (Exception $e) {
            return ['error' => 'Exception: ' . $e->getMessage()];
        }
    }

    /* ══════════════════════════════════════════════════════════
     |  DECODE HELPERS
     ══════════════════════════════════════════════════════════ */

    /**
     * Decode a JSON-encoded column value into an array.
     * Handles double-encoded strings and comma-separated fallback.
     * Returns [] safely when value is null/empty.
     */
    private function decodeText($value): array
    {
        if ($value === null || $value === '' || $value === 'NULL') {
            return [];
        }

        // Strip surrounding quotes added by some MySQL dumps
        $value = trim($value);
        if (str_starts_with($value, '"') && str_ends_with($value, '"')) {
            $value = json_decode($value, true) ?? $value;
        }

        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode($value, true);

        // Handle double-encoded: json_decode gives a string, decode again
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        if (is_array($decoded)) {
            return $decoded;
        }

        // Last resort: comma-separated
        return array_map('trim', explode(',', (string)$value));
    }

    private function decodeOfferElement($element): array
    {
        if (empty($element))    return [];
        if (is_array($element)) return $element;
        if (is_string($element)) {
            $d = json_decode($element, true);
            return is_array($d) ? $d : [];
        }
        return [];
    }

    /* Offer images stored under /public/offerimage/ */
    private function buildOfferImageUrl($raw): string
    {
        if (empty($raw)) return asset('signage/home/productimage/default.png');
        if (is_array($raw)) $raw = $raw[0] ?? null;
        if (empty($raw))    return asset('signage/home/productimage/default.png');

        $raw = trim((string)$raw);
        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) return $raw;
        return asset('offerimage/' . basename($raw));
    }

    /* Normal product images under /public/signage/home/productimage/ */
    private function buildImageUrl($raw): string
    {
        if (empty($raw)) return asset('signage/home/productimage/default.png');

        if (is_string($raw) && str_starts_with(trim($raw), '[')) {
            $d = json_decode($raw, true);
            if (is_array($d) && !empty($d)) $raw = $d[0];
        }
        if (is_array($raw)) $raw = $raw[0] ?? null;
        if (empty($raw))    return asset('signage/home/productimage/default.png');

        $raw = trim((string)$raw);
        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) return $raw;
        return asset('signage/home/productimage/' . basename($raw));
    }
}