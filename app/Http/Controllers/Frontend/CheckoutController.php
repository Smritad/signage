<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\State;
use App\Models\City;
use App\Models\Country;
use App\Models\OrderDetail;
use App\Models\CustomUser;
use App\Traits\MergesGuestCart;   // ★ add


class CheckoutController extends Controller
{    
    
    use MergesGuestCart;   // ★ add

    // Show checkout page
     public function showCheckout()
{
    $user = Auth::guard('custom')->user();

    if ($user) {
        // ★ Logged in: always build cart fresh from DB (includes merged guest items).
        $rows = DB::table('carts')->where('user_id', $user->id)->get();

        $cart = $rows->map(function ($r) {
            return [
                'cart_id'      => $r->id,
                'is_offer'     => $r->combo === 'offer',
                'offer_id'     => (int) ($r->offer_id ?? 0),
                'product_id'   => (int) ($r->product_id ?? 0),
                'product_name' => $r->product_name,
                'quantity'     => (int) $r->quantity,
                'price'        => (float) ($r->offer_price ?? $r->price),
                'mrp'          => (float) $r->price,
                'image'        => $r->images,
                'size'         => '',
                'print'        => '',
            ];
        })->values()->toArray();
    } else {
        // Guest: fall back to the session array set by the cart page.
        $cart = session()->get('checkout_cart', []);
    }

    $fetch_all_countries = DB::table('main_countries')->orderBy('name')->get();
    $fetch_all_states    = DB::table('main_states')->orderBy('name')->get();
    $fetch_all_cities    = DB::table('main_cities')->orderBy('name')->get();

    return view('frontend.checkout', compact(
        'cart', 'user', 'fetch_all_countries', 'fetch_all_states', 'fetch_all_cities'
    ));
}




    // Store cart data from AJAX
    public function storeCheckoutData(Request $request)
    {
        $cartData = $request->input('cart', []);

        if(!empty($cartData)){
            session(['checkout_cart' => $cartData]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Cart is empty']);
    }








public function sendOtp(Request $request)
{
    $request->validate(['email' => 'required|email']);
    $email = $request->email;

    // ✅ Step 1: Check if user already exists
    $userExists = \App\Models\CustomUser::where('email', $email)->exists();
    if ($userExists) {
        return response()->json([
            'success' => false,
            'message' => 'Email already registered. Please log in instead.'
        ]);
    }

    // ✅ Step 2: Generate and store OTP
    $otp = rand(100000, 999999);

    DB::table('otps')->updateOrInsert(
        ['email' => $email],
        ['otp' => $otp, 'created_at' => now()]
    );

    // ✅ Step 3: Send email
    try {
        $htmlContent = '
        <body style="margin:0; padding:0; font-family: Arial, sans-serif; background-color:#f4f4f4;">
            <div style="max-width:600px; margin:0 auto; background-color:#ffffff; border-radius:8px; overflow:hidden;">
                
                <div style="background-color:#000; text-align:center; padding:20px;">
                    <img src="https://anvayafoundation.com/signage/frontend/assets/images/logo/logo.webp" 
                         alt="Signage Logo" style="height:60px; display:block; margin:0 auto;">
                </div>

                <div style="padding:30px; text-align:center;">
                    <h2 style="color:#333;">OTP Verification</h2>
                    <p style="color:#555; font-size:16px;">
                        Your OTP for verification is:
                    </p>
                    <p style="font-size:28px; font-weight:bold; letter-spacing:4px; color:#000; margin:20px 0;">
                        '.$otp.'
                    </p>
                    <p style="color:#555; font-size:14px;">
                        This OTP is valid for 10 minutes. Do not share it with anyone.
                    </p>
                </div>

                <div style="background-color:#f1f1f1; text-align:center; padding:15px; font-size:12px; color:#777;">
                    &copy; '.date('Y').' Signage. All rights reserved.<br>
                    If you did not request this, please ignore this email.
                </div>
            </div>
        </body>';

        Mail::html($htmlContent, function($message) use ($email) {
            $message->to($email)
                    ->subject('OTP Verification - Signage Website');
        });

        Log::info('[OTP] Mail sent to: '.$email);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your email!'
        ]);

    } catch (\Exception $e) {
        Log::error('[OTP] Mail send failed: '.$e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to send OTP. Please try again later.'
        ]);
    }
}


public function verifyOtp(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email'    => 'required|email',
            'otp'      => 'required|digits:6',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $record = DB::table('otps')->where('email', $request->email)->first();
        if (!$record || $record->otp != $request->otp) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP']);
        }

        // ★ Capture the guest session id BEFORE login (login may regenerate the session).
        $guestSessionId = session()->getId();

        $user = \App\Models\CustomUser::firstOrCreate(
            ['email' => $request->email],
            ['password' => bcrypt($request->password)]
        );

        Auth::guard('custom')->login($user);

        // ★ Merge guest cart into this user's cart.
        $this->mergeGuestCart($user->id, $guestSessionId);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully. You are now logged in.'
        ]);
    }

    //  public function order_confirmation(Request $request)
    // {
    //     dd($order);
    //     $orderId = $request->query('order_id'); 
    
    //     $order = OrderDetail::where('order_id', $orderId)->first(); 
        
    
    //     if (!$order) {
    //         return redirect()->route('frontend.index')->with('error', 'Order not found.');
    //     }
    
    //     return view('frontend.order-confirmation', compact('order'));
    // }
    
    

}
