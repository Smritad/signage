<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FragranceTypeDetails;
use App\Models\ProductsDetails;
use App\Models\CategoryDetails;
use App\Models\SabCategoryDetails;
use App\Models\Wishlist;
use App\Models\ProductReview;

class FragranceProductsListingController extends Controller
{
    // ─────────────────────────────────────────────────────────────────
    //  SHARED: attach rating stats to a product collection
    //  (Left active — harmless; ready for when the rating row is re-enabled.)
    // ─────────────────────────────────────────────────────────────────
    private function attachRatings($products)
    {
        $ids   = $products->pluck('id')->toArray();
        $stats = ProductReview::whereIn('product_id', $ids)
            ->where('is_approved', 1)
            ->selectRaw('product_id, AVG(rating) as avg_rating, COUNT(*) as review_count')
            ->groupBy('product_id')
            ->get()->keyBy('product_id');

        foreach ($products as $product) {
            $s = $stats[$product->id] ?? null;
            $product->avg_rating   = $s ? round($s->avg_rating, 1) : 0;
            $product->review_count = $s ? (int) $s->review_count   : 0;
        }
    }

    // ─────────────────────────────────────────────────────────────────
    //  SHARED: build one product card HTML string
    // ─────────────────────────────────────────────────────────────────
    private function buildCardHtml($product): string
    {
        // ── images ──
        $images     = is_array($product->images) ? $product->images : json_decode($product->images, true);
        $firstImage = $images[0] ?? 'default.png';

        // ── URL: resolve sub-category ──
        $raw     = $product->getRawOriginal('sub_category_id');
        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $subCatId = isset($decoded[0]) ? (int) $decoded[0] : null;
        } else {
            $subCatId = is_numeric($raw) ? (int) $raw : null;
        }

        $sabcategory = $subCatId ? SabCategoryDetails::find($subCatId) : null;
        $categoryObj = CategoryDetails::find($product->category_id);

        $productUrl = ($sabcategory && $sabcategory->slug && $categoryObj && $categoryObj->slug && $product->slug)
            ? route('product.details', [
                'cat'    => $categoryObj->slug,
                'sabcat' => $sabcategory->slug,
                'slug'   => $product->slug,
              ])
            : '#';

        // ── wishlist ──
        $isInWishlist = Wishlist::where('user_id', auth()->id() ?? 0)
            ->where('product_id', $product->id)->exists();

        // ── discount badge ──
        $hasOffer = !empty($product->offer_price);
        $disc     = 0;
        if (!empty($product->discount) && $product->discount > 0) {
            $disc = (int) round($product->discount);
        } elseif ($hasOffer && $product->offer_price < $product->price) {
            $disc = (int) round((($product->price - $product->offer_price) / $product->price) * 100);
        }
        $discBadge = $disc > 0 ? '<span class="product-discount-badge">'.$disc.'% OFF</span>' : '';

        // ── cart button ──
        if ($product->quantity > 0) {
            $cartBtn = '<form class="add-to-cart-form d-inline" method="POST" action="'.route('cart.add').'">'
                . csrf_field()
                . '<input type="hidden" name="product_id" value="'.$product->id.'">'
                . '<button type="submit" class="hover-tooltip tooltip-left box-icon">'
                . '<span class="icon icon-shopping-cart-simple"></span>'
                . '<span class="tooltip">Add to cart</span>'
                . '</button></form>';
        } else {
            $cartBtn = '<button type="button" class="hover-tooltip tooltip-left box-icon disabled" disabled>'
                . '<span class="icon icon-x-circle"></span>'
                . '<span class="tooltip">Out of Stock</span>'
                . '</button>';
        }

        // ── price ──
        $priceHtml = $hasOffer
            ? '<span class="price-old h6 fw-normal">Rs.'.number_format($product->price).'</span>'
              .'<span class="price-new h6">Rs.'.number_format($product->offer_price).'</span>'
            : '<span class="price-new h6">Rs.'.number_format($product->price).'</span>';

        /* ── rating — TEMPORARILY HIDDEN: do not display for now ──
           Logic preserved below but commented out.
           To re-enable: un-comment this block and delete the `$ratingHtml = '';` line.

        $avg  = $product->avg_rating   ?? 0;
        $rcnt = $product->review_count ?? 0;

        if ($rcnt > 0) {
            $ratingHtml = '<div class="card-rating-row">'
                . '<i class="icon-star star-icon"></i>'
                . '<strong>'.number_format($avg, 1).'</strong>'
                . '<span class="divider">|</span>'
                . '<span class="verified-check">&#10003;</span>'
                . '<span class="review-count">('.$rcnt.' Review'.($rcnt == 1 ? '' : 's').')</span>'
                . '</div>';
        } else {
            $ratingHtml = '<div class="card-rating-row no-reviews">'
                . '<i class="icon-star" style="color:#ddd;"></i> No reviews yet'
                . '</div>';
        }
        ── end hidden rating block ── */

        $ratingHtml = ''; // rating row disabled — renders nothing in the card for now

        // ── badges ──
        $badges = '';
        if (!empty($product->is_bestseller))  $badges .= '<li class="product-badge_item h6 bestseller">Bestseller</li>';
        if (!empty($product->is_new_arrival)) $badges .= '<li class="product-badge_item h6 new-arrival">New Arrival</li>';

        $wishIcon    = $isInWishlist ? 'icon-trash' : 'icon-heart';
        $wishTooltip = $isInWishlist ? 'Remove from Wishlist' : 'Add to Wishlist';

        return '
        <div class="card-product grid">
            <div class="card-product_wrapper">
                '.$discBadge.'
                <a href="'.$productUrl.'" class="product-img">
                    <img class="lazyload img-product" src="'.asset('signage/home/productimage/'.$firstImage).'" alt="'.e($product->product_name).'">
                    <img class="lazyload img-hover"    src="'.asset('signage/home/productimage/'.$firstImage).'" alt="'.e($product->product_name).'">
                </a>
                <ul class="product-action_list">
                    <li>'.$cartBtn.'</li>
                    <li class="wishlist">
                        <form class="add-to-wishlist-form" data-product="'.$product->id.'">'.csrf_field().'
                            <button type="button" class="hover-tooltip tooltip-left box-icon wishlist-btn">
                                <span class="icon wishlist-icon '.$wishIcon.'"></span>
                                <span class="tooltip">'.$wishTooltip.'</span>
                            </button>
                        </form>
                    </li>
                    <li>
                        <a href="'.$productUrl.'" class="hover-tooltip tooltip-left box-icon">
                            <span class="icon icon-view"></span>
                            <span class="tooltip">Quick view</span>
                        </a>
                    </li>
                </ul>
                <ul class="product-badge_list">'.$badges.'</ul>
            </div>
            <div class="card-product_info">
                <a href="'.$productUrl.'" class="name-product h4 link">'.e($product->product_name).'</a>
                '.$ratingHtml.'
                <div class="price-wrap">'.$priceHtml.'</div>
            </div>
        </div>';
    }

    // ─────────────────────────────────────────────────────────────────
    //  SHARED: build pagination HTML from a paginator
    // ─────────────────────────────────────────────────────────────────
    private function buildPaginationHtml($products): string
    {
        if ($products->lastPage() <= 1) return '';

        $html  = '<div class="wd-full wg-pagination m-0 justify-content-center d-flex">';
        $html .= $products->onFirstPage()
            ? '<span class="pagination-item h6 direct disabled"><i class="icon icon-caret-left"></i></span>'
            : '<a href="'.$products->previousPageUrl().'" class="pagination-item h6 direct"><i class="icon icon-caret-left"></i></a>';

        foreach ($products->getUrlRange(1, $products->lastPage()) as $p => $url) {
            $html .= $p == $products->currentPage()
                ? '<span class="pagination-item h6 active">'.$p.'</span>'
                : '<a href="'.$url.'" class="pagination-item h6">'.$p.'</a>';
        }

        $html .= $products->hasMorePages()
            ? '<a href="'.$products->nextPageUrl().'" class="pagination-item h6 direct"><i class="icon icon-caret-right"></i></a>'
            : '<span class="pagination-item h6 direct disabled"><i class="icon icon-caret-right"></i></span>';

        return $html . '</div>';
    }

    // ─────────────────────────────────────────────────────────────────
    //  SHARED: apply common filters to a query builder
    // ─────────────────────────────────────────────────────────────────
    private function applyFilters($query, Request $request)
    {
        // ── 1. Availability ──────────────────────────────────────────
        $avail = trim($request->input('availability', ''));
        if ($avail === 'in') {
            $query->where('quantity', '>', 0);
        } elseif ($avail === 'out') {
            $query->where('quantity', '<=', 0);
        }

        // ── 2. Fragrance IDs ─────────────────────────────────────────
        $fragIds = $request->input('fragrance_ids', []);
        if (!empty($fragIds) && is_array($fragIds)) {
            $query->where(function ($q) use ($fragIds) {
                foreach ($fragIds as $fid) {
                    $fid = (int) $fid;
                    $q->orWhereRaw("JSON_VALID(fragrance_type_id) AND JSON_CONTAINS(fragrance_type_id, '\"$fid\"')")
                      ->orWhereRaw("JSON_VALID(fragrance_type_id) AND JSON_CONTAINS(fragrance_type_id, '$fid')")
                      ->orWhereRaw("(NOT JSON_VALID(fragrance_type_id) AND fragrance_type_id = '$fid')");
                }
            });
        }

        // ── 3. Sizes ─────────────────────────────────────────────────
        $units = $request->input('units', []);
        if (!empty($units) && is_array($units)) {
            $query->whereIn('measurement_unit', $units);
        }

        // ── 4. Product types (JSON-safe) ─────────────────────────────
        $types = $request->input('product_types', []);
        if (!empty($types) && is_array($types)) {
            $combo  = in_array('combo', $types);
            $single = in_array('single', $types);

            // If both checked → no filtering (everything is one or the other)
            if ($combo && !$single) {
                $query->where(function ($q) {
                    $q->where('sub_category_id', '5')
                      ->orWhereRaw("JSON_VALID(sub_category_id) AND JSON_CONTAINS(sub_category_id, '\"5\"')")
                      ->orWhereRaw("JSON_VALID(sub_category_id) AND JSON_CONTAINS(sub_category_id, '5')");
                });
            } elseif ($single && !$combo) {
                $query->where(function ($q) {
                    $q->whereNull('sub_category_id')
                      ->orWhereRaw("(NOT JSON_VALID(sub_category_id) AND sub_category_id <> '5')")
                      ->orWhereRaw("(JSON_VALID(sub_category_id) AND JSON_CONTAINS(sub_category_id, '\"5\"') = 0 AND JSON_CONTAINS(sub_category_id, '5') = 0)");
                });
            }
        }

        // ── 5. Price range ───────────────────────────────────────────
        $minPrice = $request->has('min_price') ? (int) $request->input('min_price') : null;
        $maxPrice = $request->has('max_price') ? (int) $request->input('max_price') : null;

        if ($minPrice !== null && $maxPrice !== null && $maxPrice > 0 && $maxPrice >= $minPrice) {
            $query->whereBetween('price', [$minPrice, $maxPrice]);
        }

        // ── 6. Sorting ───────────────────────────────────────────────
        switch ($request->input('sort', 'best-selling')) {
            case 'a-z':            $query->orderBy('product_name', 'asc');  break;
            case 'z-a':            $query->orderBy('product_name', 'desc'); break;
            case 'price-low-high': $query->orderBy('price', 'asc');         break;
            case 'price-high-low': $query->orderBy('price', 'desc');        break;
            default:               $query->orderBy('priority', 'asc')->orderBy('created_at', 'desc'); break;
        }

        return $query;
    }

    // ═════════════════════════════════════════════════════════════════
    //  FRAGRANCE PAGE  GET /fragrance/{slug}
    // ═════════════════════════════════════════════════════════════════
    public function fragrance($slug)
    {
        $fragrance = FragranceTypeDetails::where('slug', $slug)->firstOrFail();

        $baseQuery = ProductsDetails::whereRaw("JSON_CONTAINS(fragrance_type_id, '\"$fragrance->id\"')");

        $products = (clone $baseQuery)
            ->where('quantity', '>', 0)
            ->orderBy('priority', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        $this->attachRatings($products);

        $fragranceTypes  = FragranceTypeDetails::orderBy('title', 'asc')->get();
        $fragranceCounts = [];
        foreach ($fragranceTypes as $ft) {
            $fragranceCounts[$ft->id] = ProductsDetails::whereRaw("JSON_CONTAINS(fragrance_type_id, '\"$ft->id\"')")->count();
        }

        $inStockCount  = (clone $baseQuery)->where('quantity', '>', 0)->count();
        $outStockCount = (clone $baseQuery)->where('quantity', '<=', 0)->count();

        $units = (clone $baseQuery)
            ->whereNotNull('measurement_unit')->where('measurement_unit', '!=', '')
            ->select('measurement_unit')->distinct()
            ->orderByRaw('CAST(REPLACE(REPLACE(LOWER(measurement_unit),"ml","")," ","") AS UNSIGNED) ASC')
            ->pluck('measurement_unit')->toArray();

        $unitCounts = (clone $baseQuery)
            ->whereNotNull('measurement_unit')
            ->selectRaw('measurement_unit, COUNT(*) as count')
            ->groupBy('measurement_unit')
            ->pluck('count', 'measurement_unit')->toArray();

        $minPrice = (int) ((clone $baseQuery)->min('price') ?? 0);
        $maxPrice = (int) ((clone $baseQuery)->max('price') ?? 0);

        return view('frontend.fragrance-products', compact(
            'fragrance', 'products',
            'fragranceTypes', 'fragranceCounts',
            'inStockCount', 'outStockCount',
            'units', 'unitCounts',
            'minPrice', 'maxPrice'
        ));
    }

    // ═════════════════════════════════════════════════════════════════
    //  FRAGRANCE FILTER  POST /fragrance/filter  (name: frgrance.filter)
    // ═════════════════════════════════════════════════════════════════
    public function filter(Request $request)
    {
        $query = ProductsDetails::query();
        $this->applyFilters($query, $request);

        $page     = max(1, (int) $request->input('page', 1));
        $products = $query->paginate(6, ['*'], 'page', $page);

        if ($products->isEmpty()) {
            return response()->json([
                'html'       => '<div class="text-center py-5 w-100"><h5 class="text-muted">No products found matching your filters.</h5></div>',
                'pagination' => '',
            ]);
        }

        $this->attachRatings($products);

        $html = '';
        foreach ($products as $product) {
            $html .= $this->buildCardHtml($product);
        }

        return response()->json([
            'html'       => $html,
            'pagination' => $this->buildPaginationHtml($products),
        ]);
    }

    // ═════════════════════════════════════════════════════════════════
    //  SHOP ALL PAGE  GET /shop-all
    // ═════════════════════════════════════════════════════════════════
    public function all()
    {
        $products = ProductsDetails::whereNotNull('category_id')
            ->orderBy('priority', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        $this->attachRatings($products);

        $allCategories  = CategoryDetails::all();
        $categoryCounts = ProductsDetails::selectRaw('category_id, COUNT(*) as count')
            ->groupBy('category_id')->pluck('count', 'category_id');

        $inStockCount  = ProductsDetails::where('quantity', '>', 0)->count();
        $outStockCount = ProductsDetails::where('quantity', '<=', 0)->count();

        $comboSubCategoryId = 5;
        $comboCount = ProductsDetails::whereNull('deleted_by')
            ->where(function ($q) use ($comboSubCategoryId) {
                $q->where('sub_category_id', $comboSubCategoryId)
                  ->orWhereRaw("JSON_VALID(sub_category_id) AND JSON_CONTAINS(sub_category_id, '\"".$comboSubCategoryId."\"')")
                  ->orWhereRaw("JSON_VALID(sub_category_id) AND JSON_CONTAINS(sub_category_id, '".$comboSubCategoryId."')");
            })->count();
        $singleCount = ProductsDetails::whereNull('deleted_by')->count() - $comboCount;

        $units = ProductsDetails::whereNull('deleted_by')
            ->whereNotNull('measurement_unit')->where('measurement_unit', '!=', '')
            ->select('measurement_unit')->distinct()
            ->orderByRaw('CAST(REPLACE(REPLACE(LOWER(measurement_unit),"ml","")," ","") AS UNSIGNED) ASC')
            ->pluck('measurement_unit')->toArray();

        $unitCounts = ProductsDetails::whereNull('deleted_by')
            ->whereNotNull('measurement_unit')
            ->selectRaw('measurement_unit, COUNT(*) as count')
            ->groupBy('measurement_unit')
            ->pluck('count', 'measurement_unit')->toArray();

        $fragranceTypes  = FragranceTypeDetails::orderBy('title', 'asc')->get();
        $fragranceCounts = [];
        foreach (ProductsDetails::whereNull('deleted_by')->get() as $product) {
            $raw     = $product->getRawOriginal('fragrance_type_id');
            $decoded = json_decode($raw, true);
            $fids    = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : (is_numeric($raw) ? [$raw] : []);
            foreach ($fids as $fid) {
                $fid = (int) $fid;
                $fragranceCounts[$fid] = ($fragranceCounts[$fid] ?? 0) + 1;
            }
        }

        $minPrice = (int) (ProductsDetails::min('price') ?? 0);
        $maxPrice = (int) (ProductsDetails::max('price') ?? 0);

        return view('frontend.shop-all', compact(
            'products', 'allCategories', 'categoryCounts',
            'inStockCount', 'outStockCount',
            'singleCount', 'comboCount',
            'units', 'unitCounts',
            'fragranceTypes', 'fragranceCounts',
            'minPrice', 'maxPrice'
        ));
    }

    // ═════════════════════════════════════════════════════════════════
    //  SHOP ALL FILTER  POST /shop-all/filter  (name: product.all.filter)
    // ═════════════════════════════════════════════════════════════════
    public function filterAll(Request $request)
    {
        $query = ProductsDetails::query();

        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->input('category_id'));
        }

        $this->applyFilters($query, $request);

        $page     = max(1, (int) $request->input('page', 1));
        $products = $query->paginate(6, ['*'], 'page', $page);

        if ($products->isEmpty()) {
            return response()->json([
                'html'       => '<div class="text-center py-5 w-100"><h5 class="text-muted">No products found matching your filters.</h5></div>',
                'pagination' => '',
            ]);
        }

        $this->attachRatings($products);

        $html = '';
        foreach ($products as $product) {
            $html .= $this->buildCardHtml($product);
        }

        return response()->json([
            'html'       => $html,
            'pagination' => $this->buildPaginationHtml($products),
        ]);
    }
}