<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\BannerDetails;
use App\Models\HomeContactAdverstimentDetails;
use App\Models\SignageWellnessDetails;
use App\Models\CustomerReviewDetails;
use App\Models\ProductsDetails;
use App\Models\FragranceTypeDetails;

class HomeController extends Controller
{

 public function home(Request $request)
{
   $banners = BannerDetails::whereNull('deleted_by')->get();

$advertisements = HomeContactAdverstimentDetails::whereNull('deleted_by')->get();

    // Signage section
    $signage = SignageWellnessDetails::first();
    $signageHeading = $signage?->heading ?? ''; 
    $signageItems = [];

    if (!empty($signage?->items)) {
        $decodedItems = json_decode($signage->items, true) ?? [];

        foreach ($decodedItems as &$item) {
            if (!empty($item['image'])) {
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

    // ✅ Fetch latest products dynamically
$products = ProductsDetails::orderBy('priority', 'asc')
    ->take(10)
    ->get();

    foreach ($products as $product) {
        if (!empty($product->images)) {
            $decoded = json_decode($product->images, true);
            $product->images = (json_last_error() === JSON_ERROR_NONE && is_array($decoded))
                ? $decoded
                : [$product->images]; // fallback if not json
        } else {
            $product->images = [];
        }
    }

  // Fetch all fragrance types in descending order of ID
$fragranceTypes = FragranceTypeDetails::orderBy('title', 'asc')->get();


    // Get product counts per fragrance
    $fragranceCounts = ProductsDetails::selectRaw('fragrance_type_id, COUNT(*) as count')
        ->groupBy('fragrance_type_id')
        ->pluck('count', 'fragrance_type_id');

    // Return only **one view** with all required data
    return view('frontend.home', compact(
        'banners',
        'advertisements',
        'signageHeading',
        'signageItems',
        'customerReviewHeading',
        'customerReviews',
        'products',
        'fragranceTypes',
        'fragranceCounts'
    ));
}







public function footer(Request $request)

{
    return view('components.frontend.footer');
}

}