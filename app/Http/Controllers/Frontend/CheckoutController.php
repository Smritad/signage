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


class CheckoutController extends Controller
{
    // Show checkout page
     public function showCheckout()
    {
        $user = Auth::guard('custom')->user();
        $cart = session()->get('checkout_cart', []);
//dd($cart);
        // Load everything (quick fix)
        $fetch_all_countries = DB::table('main_countries')->orderBy('name')->get();
        $fetch_all_states    = DB::table('main_states')->orderBy('name')->get();
        $fetch_all_cities    = DB::table('main_cities')->orderBy('name')->get();

        return view('frontend.checkout', compact(
            'cart',
            'user',
            'fetch_all_countries',
            'fetch_all_states',
            'fetch_all_cities'
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
    $request->validate(['email'=>'required|email']);
    $email = $request->email;
    $otp = rand(100000,999999);

    DB::table('otps')->updateOrInsert(
        ['email'=>$email],
        ['otp'=>$otp, 'created_at'=>now()]
    );

    try {
        Mail::raw("Your OTP for verification is $otp", function($message) use ($email){
            $message->to($email)
                    ->subject('OTP Verification - Signage Website');
        });
        Log::info('[OTP] Mail sent to: '.$email);
        return response()->json(['success'=>true,'message'=>'OTP sent to your email!']);
    } catch (\Exception $e) {
        Log::error('[OTP] Mail send failed: '.$e->getMessage());
        return response()->json(['success'=>false,'message'=>'Failed to send OTP. Check logs']);
    }
}


public function verifyOtp(Request $request)
{
    // Ensure validation errors return JSON
    $validator = \Validator::make($request->all(), [
        'email' => 'required|email',
        'otp' => 'required|digits:6',
        'password' => 'required|min:6|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors()->first()
        ]);
    }

    // Find OTP record
    $record = DB::table('otps')->where('email', $request->email)->first();
    if (!$record || $record->otp != $request->otp) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP'
        ]);
    }

    // Check if user exists, create if not
    $user = \App\Models\CustomUser::firstOrCreate(
        ['email' => $request->email],
        ['password' => bcrypt($request->password)]
    );

    // Log in the user
    Auth::guard('custom')->login($user);

    // Success response
    return response()->json([
        'success' => true,
        'message' => 'OTP verified successfully. You are now logged in.'
    ]);
}


     public function order_confirmation(Request $request)
    {
        $orderId = $request->query('order_id'); 
    
        $order = OrderDetail::where('order_id', $orderId)->first(); 
        // dd($order);
    
        if (!$order) {
            return redirect()->route('frontend.index')->with('error', 'Order not found.');
        }
    
        return view('frontend.order-confirmation', compact('order'));
    }
    
    

}
