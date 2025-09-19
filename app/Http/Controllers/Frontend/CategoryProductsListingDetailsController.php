<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoryDetails;
use App\Models\SabCategoryDetails;
use App\Models\ProductsDetails;

class CategoryProductsListingDetailsController extends Controller
{
    public function index($slug)
{
    // Current category
    $category = CategoryDetails::where('slug', $slug)->firstOrFail();
    // All categories for sidebar filter
    $allCategories = CategoryDetails::all();

    // Category-wise product counts
    $categoryCounts = ProductsDetails::selectRaw('category_id, COUNT(*) as count')
        ->groupBy('category_id')
        ->pluck('count', 'category_id');

    // Products of current category
    $products = ProductsDetails::where('category_id', $category->id)->get();

    // Availability counts
    $inStockCount = ProductsDetails::where('category_id', $category->id)
        ->where('quantity', '>', 0)
        ->count();

    $outStockCount = ProductsDetails::where('category_id', $category->id)
        ->where('quantity', '<=', 0)
        ->count();

    // Perfume Notes
    $fragranceTypes = \App\Models\FragranceTypeDetails::all();

    // Perfume Notes product counts
    $fragranceCounts = ProductsDetails::selectRaw('fragrance_type_id, COUNT(*) as count')
        ->groupBy('fragrance_type_id')
        ->pluck('count', 'fragrance_type_id');

    // Price range
    $minPrice = ProductsDetails::where('category_id', $category->id)->min('price');
    $maxPrice = ProductsDetails::where('category_id', $category->id)->max('price');


    return view('frontend.all-category', compact(
        'category',
        'allCategories',
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








