<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ProductsDetails;
use App\Models\CategoryDetails;
use App\Models\SabCategoryDetails;
use App\Models\FragranceTypeDetails;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class AllProductsDetailsController extends Controller
{
    public function productDetail($cat, $sabcat, $slug)
    {
        $product = ProductsDetails::where('slug', $slug)
            ->whereNull('deleted_at')
            ->firstOrFail();

        /* Fragrance types (multi-select) */
        $fragranceTypes = collect();
        $rawFragrance   = $product->getRawOriginal('fragrance_type_id');
        if (!empty($rawFragrance)) {
            $decoded = json_decode($rawFragrance, true);
            $ids     = (json_last_error() === JSON_ERROR_NONE && is_array($decoded))
                        ? $decoded
                        : (is_numeric($rawFragrance) ? [$rawFragrance] : []);
            if (!empty($ids)) $fragranceTypes = FragranceTypeDetails::whereIn('id', $ids)->get();
        }

        /* Images */
        if (!empty($product->images)) {
            $decoded = json_decode($product->images, true);
            $product->images = (json_last_error() === JSON_ERROR_NONE && is_array($decoded))
                                ? $decoded : explode(',', $product->images);
        } else {
            $product->images = [];
        }

        /* Perfume details */
        $perfumeDetails = [];
        if (!empty($product->perfume_details)) {
            $decoded = json_decode($product->perfume_details, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) $perfumeDetails = $decoded;
        }

        $subcategory = SabCategoryDetails::where('slug', $sabcat)->firstOrFail();
        $category    = CategoryDetails::where('slug', $cat)->firstOrFail();

        /* Related products */
        $relatedProducts = ProductsDetails::where('id', '!=', $product->id)
            ->whereNull('deleted_at')
            ->where(function ($q) use ($subcategory) {
                $q->where('sub_category_id', $subcategory->id)
                  ->orWhereJsonContains('sub_category_id', (string) $subcategory->id)
                  ->orWhereJsonContains('sub_category_id', $subcategory->id);
            })
            ->take(8)
            ->get();

        /* ═══════════════ REVIEWS & RATING STATS ═══════════════ */
        $reviewsQuery = ProductReview::where('product_id', $product->id)->where('is_approved', 1);

        $reviewStats = [
            'total'       => (clone $reviewsQuery)->count(),
            'average'     => round((clone $reviewsQuery)->avg('rating') ?? 0, 2),
            'distribution'=> [
                5 => (clone $reviewsQuery)->where('rating', 5)->count(),
                4 => (clone $reviewsQuery)->where('rating', 4)->count(),
                3 => (clone $reviewsQuery)->where('rating', 3)->count(),
                2 => (clone $reviewsQuery)->where('rating', 2)->count(),
                1 => (clone $reviewsQuery)->where('rating', 1)->count(),
            ],
        ];

        /* First 5 reviews — rest loaded via AJAX */
        $reviews = (clone $reviewsQuery)->orderBy('created_at', 'desc')->paginate(5);

        /* Customer photos gallery (from all reviews that have media) */
        $photoReviews = (clone $reviewsQuery)->whereNotNull('media')->where('media', '!=', '[]')->get();
        $allPhotos    = [];
        foreach ($photoReviews as $pr) {
            $m = is_array($pr->media) ? $pr->media : (json_decode($pr->media, true) ?? []);
            foreach ($m as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) $allPhotos[] = $file;
            }
        }
        $allPhotos = array_slice(array_values(array_unique($allPhotos)), 0, 8);

        return view('frontend.all-productsdetails', compact(
            'product',
            'subcategory',
            'category',
            'fragranceTypes',
            'relatedProducts',
            'perfumeDetails',
            'reviewStats',
            'reviews',
            'allPhotos'
        ));
    }
    
    
}