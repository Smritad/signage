<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\OrderDetail;
use App\Models\OrderStatus;
use App\Models\CustomUser;

class PaymentController extends Controller
{
    // Create Razorpay order
    public function processPayment(Request $request)
    {
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        $cartItems = $request->order_data['cart_items'] ?? [];

        // Subtotal
        $totalAmount = array_sum(array_map(fn($item) => floatval($item['subtotal']), $cartItems));

        try {
            $order = $api->order->create([
                'receipt' => 'order_' . rand(1000, 9999),
                'amount' => round($totalAmount * 100), // amount in paise
                'currency' => 'INR',
                'payment_capture' => 1
            ]);

            return response()->json([
                'order_id' => $order['id'],
                'razorpay_key' => config('services.razorpay.key'),
                'amount' => $totalAmount,
                'currency' => 'INR'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Verify payment and save order
    public function verifyPayment(Request $request)
    {

       //dd($request);
        try {
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

            // Signature verification
            $expectedSignature = hash_hmac(
                'sha256',
                $request->razorpay_order_id . '|' . $request->razorpay_payment_id,
                config('services.razorpay.secret')
            );

            if ($expectedSignature !== $request->razorpay_signature) {
                return response()->json(['status' => 'Payment Verification Failed', 'error' => 'Invalid signature'], 403);
            }

            $orderData = $request->order_data;
            $cartItems = $orderData['cart_items'] ?? [];
            $totalPrice = array_sum(array_map(fn($item) => floatval($item['subtotal']), $cartItems));

            $productIds   = array_map(fn($i) => $i['product_id'], $cartItems);
            $productNames = array_map(fn($i) => $i['product_name'], $cartItems);
            $quantities   = array_map(fn($i) => $i['quantity'], $cartItems);
            $prices       = array_map(fn($i) => $i['price'], $cartItems);
            $subtotalsArr = array_map(fn($i) => $i['subtotal'], $cartItems);
            $images       = array_map(fn($i) => $i['image'], $cartItems);
            $sizes        = array_map(fn($i) => $i['size'] ?? "", $cartItems);
            $colors       = array_map(fn($i) => $i['print'] ?? "", $cartItems);

            $userId = Auth::guard('custom')->check() ? Auth::guard('custom')->id() : null;

            // Update user info if empty
            if ($userId) {
                $user = Auth::guard('custom')->user();
                $updateData = [];
                if (empty($user->phone) && !empty($orderData['customer_info']['phone'])) $updateData['phone'] = $orderData['customer_info']['phone'];
                if (empty($user->last_name) && !empty($orderData['customer_info']['last_name'])) $updateData['last_name'] = $orderData['customer_info']['last_name'];
                if (!empty($updateData)) $user->update($updateData);
            }

            // Save order
            $order = OrderDetail::create([
                'user_id' => $userId,
                'order_id' => $request->razorpay_order_id,
                'payment_id' => $request->razorpay_payment_id,
                'customer_name' => $orderData['customer_info']['first_name'],
                'customer_email' => $orderData['customer_info']['email'],
                'customer_phone' => $orderData['customer_info']['phone'],
                'street' => $orderData['customer_info']['street'] ?? '',
                'city' => $orderData['customer_info']['city'] ?? '',
                'state' => $orderData['customer_info']['state'] ?? '',
                'postal_code' => $orderData['customer_info']['postal_code'] ?? '',
                'country' => $orderData['customer_info']['country'] ?? '',
                'billing_address' => $orderData['customer_info']['billing_address'] ?? '',
                'shipping_address' => $orderData['customer_info']['shipping_address'] ?? '',
                'description' => $orderData['customer_info']['description'] ?? '',
                'total_price' => $totalPrice,
                'status' => 1,
                'product_ids' => json_encode($productIds),
                'product_names' => json_encode($productNames),
                'quantities' => json_encode($quantities),
                'prices' => json_encode($prices),
                'subtotals' => json_encode($subtotalsArr),
                'images' => json_encode($images),
                'sizes' => json_encode($sizes),
                'colors' => json_encode($colors),
                'created_at' => Carbon::now(),
            ]);

            // Save order status
            OrderStatus::create([
                'user_id' => $userId,
                'order_id' => $order->order_id,
                'order_status' => 'Order Placed',
                'status_updated_at' => Carbon::now(),
                'status_updated_by' => $userId
            ]);

           // Save logged-in user details
if ($order->customer_phone) {
    \App\Models\CustomUser::updateOrCreate(
        ['mobile_no' => $order->customer_phone], // use mobile_no instead of phone
        [
            'name'  => trim(($orderData['customer_info']['first_name'] ?? '') . ' ' . ($orderData['customer_info']['last_name'] ?? '')),
        ]
    );
}

            // Decrement stock
            foreach ($productIds as $index => $productId) {
                DB::table('products_details')->where('id', $productId)->decrement('quantity', $quantities[$index]);
            }

            // Remove items from cart
            if ($userId && !empty($productIds)) {

                //dd($productIds);
                foreach ($productIds as $productId) {
                    DB::table('carts')
                        ->where('user_id', $userId)
                        ->whereIn('product_id', $productIds)
                        ->delete();
                }
            }


            // Generate PDF invoice
            $invoiceNumber = mt_rand(10000000, 99999999);
            $invoiceFileName = 'invoice_' . $invoiceNumber . '.pdf';
            $pdfDirectory = public_path('/signage/invoices');

            if (!File::exists($pdfDirectory)) File::makeDirectory($pdfDirectory, 0777, true, true);

            $pdfPath = $pdfDirectory . '/' . $invoiceFileName;
            $order->update(['invoice_id' => $invoiceNumber]);

            $pdf = Pdf::loadView('frontend.invoice_pdf', ['order' => json_decode(json_encode($order), true)]);
            $pdf->save($pdfPath);

            // Send email with invoice
            Mail::send('frontend.invoice_mail', ['order' => $order], function ($message) use ($order, $pdfPath, $invoiceFileName) {
                $message->to($order->customer_email)
                        ->cc('smrita@matrixbricks.com')
                        ->subject('Your Invoice - ' . $order->invoice_id)
                        ->attach($pdfPath, [
                            'as' => $invoiceFileName,
                            'mime' => 'application/pdf',
                        ]);
            });

            Log::info("Invoice email sent to: " . $order->customer_email);

            return response()->json(['status' => 'success', 'order_id' => $order->order_id]);

        } catch (\Exception $e) {
            Log::error("Payment Verification Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'error' => $e->getMessage()], 500);
        }
    }
}
