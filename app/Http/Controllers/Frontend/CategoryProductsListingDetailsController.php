<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoryDetails;
use App\Models\SabCategoryDetails;
use App\Models\ProductsDetails;
use App\Models\ProductReview;

class CategoryProductsListingDetailsController extends Controller
{
    private function parseFirstSubCategoryId($raw): ?int
    {
        if (is_null($raw)) return null;

        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return isset($decoded[0]) ? (int) $decoded[0] : null;
        }
        return is_numeric($raw) ? (int) $raw : null;
    }

    public function index($slug)
    {
        $category      = CategoryDetails::where('slug', $slug)->firstOrFail();
        $allCategories = CategoryDetails::all();

        $categoryCounts = ProductsDetails::whereNull('deleted_by')
            ->selectRaw('category_id, COUNT(DISTINCT id) as count')
            ->groupBy('category_id')
            ->pluck('count', 'category_id')
            ->toArray();

        $products = ProductsDetails::where('category_id', $category->id)
            ->whereNull('deleted_by')
            ->paginate(6);

        $inStockCount  = ProductsDetails::where('category_id', $category->id)->whereNull('deleted_by')->where('quantity', '>', 0)->count();
        $outStockCount = ProductsDetails::where('category_id', $category->id)->whereNull('deleted_by')->where('quantity', '<=', 0)->count();

        $fragranceTypes  = \App\Models\FragranceTypeDetails::orderBy('title', 'asc')->get();
        $allProducts     = ProductsDetails::where('category_id', $category->id)->whereNull('deleted_by')->get();
        $fragranceCounts = [];

        foreach ($allProducts as $product) {
            $raw     = $product->getRawOriginal('fragrance_type_id');
            $decoded = json_decode($raw, true);

            $fragranceIds = (json_last_error() === JSON_ERROR_NONE && is_array($decoded))
                ? $decoded
                : (is_numeric($raw) ? [$raw] : []);

            foreach ($fragranceIds as $fid) {
                $fid = (int) $fid;
                $fragranceCounts[$fid] = ($fragranceCounts[$fid] ?? 0) + 1;
            }
        }

        $units = ProductsDetails::where('category_id', $category->id)
            ->whereNull('deleted_by')
            ->whereNotNull('measurement_unit')
            ->where('measurement_unit', '!=', '')
            ->select('measurement_unit')
            ->distinct()
            ->orderByRaw('CAST(REPLACE(REPLACE(LOWER(measurement_unit), "ml", ""), " ", "") AS UNSIGNED) ASC')
            ->pluck('measurement_unit')
            ->toArray();

        $unitCounts = ProductsDetails::where('category_id', $category->id)
            ->whereNull('deleted_by')
            ->whereNotNull('measurement_unit')
            ->selectRaw('measurement_unit, COUNT(*) as count')
            ->groupBy('measurement_unit')
            ->pluck('count', 'measurement_unit')
            ->toArray();

        $comboCount = ProductsDetails::where('category_id', $category->id)
            ->whereNull('deleted_by')
            ->where(function ($q) {
                $q->where('sub_category_id', 5)
                  ->orWhereJsonContains('sub_category_id', '5')
                  ->orWhereJsonContains('sub_category_id', 5);
            })->count();

        $singleCount = ProductsDetails::where('category_id', $category->id)->whereNull('deleted_by')->count() - $comboCount;

        $minPrice = (int) (ProductsDetails::where('category_id', $category->id)->whereNull('deleted_by')->min('price') ?? 0);
        $maxPrice = (int) (ProductsDetails::where('category_id', $category->id)->whereNull('deleted_by')->max('price') ?? 0);

        return view('frontend.all-category', compact(
            'category', 'allCategories', 'categoryCounts', 'products',
            'inStockCount', 'outStockCount', 'fragranceTypes', 'fragranceCounts',
            'minPrice', 'maxPrice', 'units', 'unitCounts', 'comboCount', 'singleCount'
        ));
    }

    public function filter(Request $request)
    {
        $query = ProductsDetails::query()->whereNull('deleted_by');

        // ── Category ──────────────────────────────────────────────────────────────
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // ── Availability ──────────────────────────────────────────────────────────
        if ($request->availability === 'in') {
            $query->where('quantity', '>', 0);
        } elseif ($request->availability === 'out') {
            $query->where('quantity', '<=', 0);
        }

        // ── Fragrance ─────────────────────────────────────────────────────────────
        if ($request->filled('fragrance_ids') && is_array($request->fragrance_ids)) {
            $query->where(function ($q) use ($request) {
                foreach ($request->fragrance_ids as $fid) {
                    $fid = (int) $fid;
                    $q->orWhere('fragrance_type_id', $fid)
                      ->orWhereRaw("JSON_CONTAINS(fragrance_type_id, '\"$fid\"')")
                      ->orWhereRaw("JSON_CONTAINS(fragrance_type_id, '$fid')");
                }
            });
        }

        // ── Units / Size ──────────────────────────────────────────────────────────
        if ($request->filled('units') && is_array($request->units)) {
            $query->whereIn('measurement_unit', $request->units);
        }

        // ── Product Type ──────────────────────────────────────────────────────────
        if ($request->filled('product_types') && is_array($request->product_types)) {
            $types = $request->product_types;
            $query->where(function ($q) use ($types) {
                if (in_array('combo', $types)) {
                    $q->orWhere(function ($q2) {
                        $q2->where('sub_category_id', 5)
                           ->orWhereJsonContains('sub_category_id', '5')
                           ->orWhereJsonContains('sub_category_id', 5);
                    });
                }
                if (in_array('single', $types)) {
                    $q->orWhere(function ($q2) {
                        $q2->where('sub_category_id', '!=', 5)
                           ->where(function ($q3) {
                               $q3->whereNull('sub_category_id')
                                  ->orWhereRaw("JSON_CONTAINS(sub_category_id, '\"5\"') = 0")
                                  ->orWhereRaw("sub_category_id NOT LIKE '%5%'");
                           });
                    });
                }
            });
        }

        // ── Price ─────────────────────────────────────────────────────────────────
        // FIX: use has() + isset check instead of filled()
        // filled() returns false when value is "0" (treats zero as empty)
        // has() just checks the key exists in the request payload
        if ($request->has('min_price') && $request->has('max_price')
            && $request->input('min_price') !== null
            && $request->input('max_price') !== null
        ) {
            $minPrice = (int) $request->input('min_price');
            $maxPrice = (int) $request->input('max_price');

            // Only apply if max > 0 (guard against both being 0 on bad payload)
            if ($maxPrice > 0) {
                $query->whereBetween('price', [$minPrice, $maxPrice]);
            }
        }

        // ── Sort ──────────────────────────────────────────────────────────────────
        switch ($request->sort) {
            case 'a-z':            $query->orderBy('product_name', 'asc');  break;
            case 'z-a':            $query->orderBy('product_name', 'desc'); break;
            case 'price-low-high': $query->orderBy('price', 'asc');         break;
            case 'price-high-low': $query->orderBy('price', 'desc');        break;
            default:               $query->orderBy('priority', 'asc')->orderBy('created_at', 'desc'); break;
        }

        $products = $query->paginate(6, ['*'], 'page', $request->page ?? 1);

        if ($products->isEmpty()) {
            return response()->json([
                'html'       => '<div class="text-center py-5"><h5 class="text-muted">No products found matching your filters.</h5></div>',
                'pagination' => '',
            ]);
        }

        // ── Batch-load rating stats ───────────────────────────────────────────────
        // RATING HIDDEN FOR NOW
        // $productIds  = $products->pluck('id')->toArray();
        // $ratingStats = ProductReview::whereIn('product_id', $productIds)
        //     ->where('is_approved', 1)
        //     ->selectRaw('product_id, AVG(rating) as avg_rating, COUNT(*) as review_count')
        //     ->groupBy('product_id')
        //     ->get()
        //     ->keyBy('product_id');

        $html = '';

        foreach ($products as $product) {
            $images     = is_array($product->images) ? $product->images : json_decode($product->images, true);
            $firstImage = $images[0] ?? 'default.png';

            $subCatId    = $this->parseFirstSubCategoryId($product->getRawOriginal('sub_category_id'));
            $sabcategory = $subCatId ? SabCategoryDetails::find($subCatId) : null;
            $categoryObj = CategoryDetails::find($product->category_id);

            $productUrl = ($sabcategory && $sabcategory->slug && $categoryObj && $categoryObj->slug && $product->slug)
                ? route('product.details', [
                    'cat'    => $categoryObj->slug,
                    'sabcat' => $sabcategory->slug,
                    'slug'   => $product->slug,
                  ])
                : '#';

            $isInWishlist = \App\Models\Wishlist::where('user_id', auth()->id() ?? 0)
                ->where('product_id', $product->id)->exists();

            $cartBtn = $product->quantity > 0
                ? '<form class="add-to-cart-form d-inline" method="POST" action="' . route('cart.add') . '">'
                    . csrf_field()
                    . '<input type="hidden" name="product_id" value="' . $product->id . '">'
                    . '<button type="submit" class="hover-tooltip tooltip-left box-icon">'
                        . '<span class="icon icon-shopping-cart-simple"></span>'
                        . '<span class="tooltip">Add to cart</span>'
                    . '</button></form>'
                : '<button type="button" class="hover-tooltip tooltip-left box-icon disabled" disabled>'
                    . '<span class="icon icon-x-circle"></span>'
                    . '<span class="tooltip">Out of Stock</span>'
                  . '</button>';

            $hasOffer = !empty($product->offer_price);

            $priceHtml = $hasOffer
                ? '<span class="price-old h6 fw-normal">Rs.' . number_format($product->price) . '</span>'
                  . '<span class="price-new h6">Rs.' . number_format($product->offer_price) . '</span>'
                : '<span class="price-new h6">Rs.' . number_format($product->price) . '</span>';

            $discountPercent = 0;
            if (!empty($product->discount) && $product->discount > 0) {
                $discountPercent = (int) round($product->discount);
            } elseif ($hasOffer && $product->offer_price < $product->price) {
                $discountPercent = (int) round((($product->price - $product->offer_price) / $product->price) * 100);
            }
            $discountBadge = $discountPercent > 0
                ? '<span class="product-discount-badge">' . $discountPercent . '% OFF</span>'
                : '';

            // ── Rating (HIDDEN FOR NOW) ───────────────────────────────────────────
            // $stat        = $ratingStats[$product->id] ?? null;
            // $avgRating   = $stat ? round($stat->avg_rating, 1) : 0;
            // $reviewCount = $stat ? (int) $stat->review_count : 0;

            // $ratingHtml = $reviewCount > 0
            //     ? '<div class="card-rating-row">'
            //         . '<i class="icon-star star-icon"></i>'
            //         . '<strong>' . number_format($avgRating, 1) . '</strong>'
            //         . '<span class="divider">|</span>'
            //         . '<span class="verified-check">✓</span>'
            //         . '<span class="review-count">(' . $reviewCount . ' Review' . ($reviewCount == 1 ? '' : 's') . ')</span>'
            //       . '</div>'
            //     : '<div class="card-rating-row no-reviews"><i class="icon-star" style="color:#ddd;"></i> No reviews yet</div>';

            $badges = '';
            if ($product->is_bestseller)  $badges .= '<li class="product-badge_item h6 bestseller">Bestseller</li>';
            if ($product->is_new_arrival) $badges .= '<li class="product-badge_item h6 new-arrival">New Arrival</li>';

            $html .= '
            <div class="card-product grid">
                <div class="card-product_wrapper">
                    ' . $discountBadge . '
                    <a href="' . $productUrl . '" class="product-img">
                        <img class="lazyload img-product" src="' . asset('signage/home/productimage/' . $firstImage) . '" alt="' . e($product->product_name) . '">
                        <img class="lazyload img-hover"    src="' . asset('signage/home/productimage/' . $firstImage) . '" alt="' . e($product->product_name) . '">
                    </a>
                    <ul class="product-action_list">
                        <li>' . $cartBtn . '</li>
                        <li class="wishlist">
                            <form class="add-to-wishlist-form" data-product="' . $product->id . '">' . csrf_field() . '
                                <button type="button" class="hover-tooltip tooltip-left box-icon wishlist-btn">
                                    <span class="icon wishlist-icon ' . ($isInWishlist ? 'icon-trash' : 'icon-heart') . '"></span>
                                    <span class="tooltip">' . ($isInWishlist ? 'Remove from Wishlist' : 'Add to Wishlist') . '</span>
                                </button>
                            </form>
                        </li>
                        <li>
                            <a href="' . $productUrl . '" class="hover-tooltip tooltip-left box-icon">
                                <span class="icon icon-view"></span>
                                <span class="tooltip">Quick view</span>
                            </a>
                        </li>
                    </ul>
                    <ul class="product-badge_list">' . $badges . '</ul>
                </div>
                <div class="card-product_info">
                    <a href="' . $productUrl . '" class="name-product h4 link" style="font-size:20px;">' . e($product->product_name) . '</a>
                    <div class="price-wrap">' . $priceHtml . '</div>
                </div>
            </div>';
        }

        // ── Pagination ────────────────────────────────────────────────────────────
        $pagination  = '<div class="wd-full wg-pagination m-0 justify-content-center d-flex">';
        $pagination .= $products->onFirstPage()
            ? '<span class="pagination-item h6 direct disabled"><i class="icon icon-caret-left"></i></span>'
            : '<a href="' . $products->previousPageUrl() . '" class="pagination-item h6 direct"><i class="icon icon-caret-left"></i></a>';

        foreach ($products->getUrlRange(1, $products->lastPage()) as $pageNum => $url) {
            $pagination .= $pageNum == $products->currentPage()
                ? '<span class="pagination-item h6 active">' . $pageNum . '</span>'
                : '<a href="' . $url . '" class="pagination-item h6">' . $pageNum . '</a>';
        }

        $pagination .= $products->hasMorePages()
            ? '<a href="' . $products->nextPageUrl() . '" class="pagination-item h6 direct"><i class="icon icon-caret-right"></i></a>'
            : '<span class="pagination-item h6 direct disabled"><i class="icon icon-caret-right"></i></span>';
        $pagination .= '</div>';

        return response()->json(['html' => $html, 'pagination' => $pagination]);
    }
}