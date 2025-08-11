<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\BannerDetails;
use App\Models\HomeContactAdverstimentDetails;
use App\Models\SignageWellnessDetails;
use App\Models\CustomerReviewDetails;


class HomeController extends Controller
{

   public function home(Request $request)
{
    $banners = BannerDetails::all();
    $advertisements = HomeContactAdverstimentDetails::all();

    // Fetch the single SignageWellnessDetails row
    $signage = SignageWellnessDetails::first();

    $signageHeading = $signage?->heading ?? ''; 
    $signageItems = [];

    if (!empty($signage?->items)) {
        $decodedItems = json_decode($signage->items, true) ?? [];

        // Clean image paths
        foreach ($decodedItems as &$item) {
            if (!empty($item['image'])) {
                // Replace triple backslashes or double slashes with single forward slash
                $item['image'] = str_replace(['\\\\\\', '\\\\', '\\', '//'], '/', $item['image']);
            }
        }

        $signageItems = $decodedItems;
    }

    // Customer Review Section
    $customerReview = CustomerReviewDetails::first();
    $customerReviewHeading = $customerReview?->heading ?? '';
    $customerReviews = [];
    if (!empty($customerReview?->items)) {
        $customerReviews = json_decode($customerReview->items, true) ?? [];
    }

    return view('frontend.home', compact(
        'banners',
        'advertisements',
        'signageHeading',
        'signageItems',
        'customerReviewHeading',
        'customerReviews'
    ));
}






public function footer(Request $request)

{
    return view('components.frontend.footer');
}

}