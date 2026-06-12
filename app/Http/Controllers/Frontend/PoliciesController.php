<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\ReturnPolicyDetail;
use App\Models\PrivacyPolicyDetail;
use App\Models\TermsConditions;

class PoliciesController extends Controller
{
   public function index(Request $request)
{
    $policy = ReturnPolicyDetail::first();
    return view('frontend.return-policy', compact('policy'));
}

 public function privacy(Request $request)
{
    $policy = PrivacyPolicyDetail::first();
    return view('frontend.privacy-policy', compact('policy'));
}


public function termsconditions(Request $request)
{
    $policy = TermsConditions::first();
    return view('frontend.terms-conditions', compact('policy'));
}


}