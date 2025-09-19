<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoryDetails;
use App\Models\SabCategoryDetails;
use App\Models\ProductsDetails;

class ProductsListingDetailsController extends Controller
{
   public function subcategory($category, $sabcat)
{
    // Get parent category by slug
    $category = CategoryDetails::where('slug', $category)->firstOrFail();

    // Get subcategory by slug, making sure it belongs to the parent category
    $sabcategory = SabCategoryDetails::where('slug', $sabcat)
        ->where('category_id', $category->id)
        ->firstOrFail();

    // All subcategories (for sidebar filter)
    $allsabCategories = SabCategoryDetails::all();

    // Category-wise product counts
    $categoryCounts = ProductsDetails::selectRaw('sub_category_id, COUNT(*) as count')
        ->groupBy('sub_category_id')
        ->pluck('count', 'sub_category_id');

    // Products of current subcategory
    $products = ProductsDetails::where('sub_category_id', $sabcategory->id)->get();

    // Availability counts
    $inStockCount = ProductsDetails::where('sub_category_id', $sabcategory->id)
        ->where('quantity', '>', 0)
        ->count();

    $outStockCount = ProductsDetails::where('sub_category_id', $sabcategory->id)
        ->where('quantity', '<=', 0)
        ->count();

    // Perfume Notes
    $fragranceTypes = \App\Models\FragranceTypeDetails::all();

    // Perfume Notes product counts
    $fragranceCounts = ProductsDetails::selectRaw('fragrance_type_id, COUNT(*) as count')
        ->groupBy('fragrance_type_id')
        ->pluck('count', 'fragrance_type_id');

    // Price range for this subcategory
    $minPrice = ProductsDetails::where('sub_category_id', $sabcategory->id)->min('price');
    $maxPrice = ProductsDetails::where('sub_category_id', $sabcategory->id)->max('price');
    if ($minPrice == $maxPrice) {
        $minPrice = 0;
    }

    return view('frontend.all-sabcategory', compact(
        'category',        // parent category
        'sabcategory',
        'allsabCategories',
        'categoryCounts',
        'products',
        'inStockCount',
        'outStockCount',
        'fragranceTypes',
        'fragranceCounts',
        'minPrice',
        'maxPrice'
    ));
}

}


