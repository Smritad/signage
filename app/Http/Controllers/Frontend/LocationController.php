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


class LocationController extends Controller
{
    
public function getStates($country_id)
    {
        $states = DB::table('main_states')->where('country_id', $country_id)->get();
        return response()->json($states);
    }
 
    public function getCities($state_id)
    {
        $cities = DB::table('main_cities')->where('state_id', $state_id)->get();
        return response()->json($cities);
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










}
