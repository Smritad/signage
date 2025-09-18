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
        // Fetch the master category by slug
        $category = CategoryDetails::where('slug', $slug)->firstOrFail();

        // Fetch subcategories under this category
        $subCategories = SabCategoryDetails::where('category_id', $category->id)->get();

        // Optionally, fetch products under this category
        $products = ProductsDetails::where('category_id', $category->id)->get();

        return view('frontend.all-category', compact('category', 'subCategories', 'products'));
    }
}








