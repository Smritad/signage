<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ProductsDetails;
use App\Models\CategoryDetails;
use App\Models\SabCategoryDetails;
use App\Models\PerfumeNotesDetails;
use App\Models\PerfumeNotesLevelDetails;
use App\Models\FragranceTypeDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AllProductsDetailsController extends Controller
{
   public function productDetail($cat, $sabcat, $slug)
{
    $product = ProductsDetails::where('slug', $slug)->firstOrFail();

    // Get fragrance type name
    $fragranceType = null;
    if ($product->fragrance_type_id) {
        $fragranceType = FragranceTypeDetails::find($product->fragrance_type_id);
    }

    $subcategory = SabCategoryDetails::where('slug', $sabcat)->firstOrFail();
    $category = CategoryDetails::where('slug', $cat)->firstOrFail();

    // Handle images field
    if (!empty($product->images)) {
        $decoded = json_decode($product->images, true);
        $product->images = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : explode(',', $product->images);
    } else {
        $product->images = [];
    }

    // Fetch related products (same subcategory, exclude current product)
    $relatedProducts = ProductsDetails::where('sub_category_id', $product->sub_category_id)
        ->where('id', '!=', $product->id)
        ->take(8) // limit to 8 products
        ->get();

    return view('frontend.all-productsdetails', compact('product', 'subcategory', 'category', 'fragranceType', 'relatedProducts'));
}





   


}
