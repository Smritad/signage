<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.frontend.head')
</head>

<style>
.product-discount-badge {
    position: absolute; bottom: 15px; right: 15px;
    background: #dc3545; color: #fff; font-size: 13px; font-weight: 700;
    padding: 5px 10px; border-radius: 4px; z-index: 10;
    letter-spacing: 0.5px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}
.card-product_wrapper { position: relative; }
.product-badge_list {
    position: absolute; top: 10px; left: 10px;
    display: flex; gap: 6px; padding: 0; margin: 0;
    list-style: none; z-index: 10;
}
.product-badge_item {
    font-size: 11px; font-weight: 600; text-transform: uppercase;
    padding: 3px 8px; border-radius: 12px; color: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2); white-space: nowrap;
}
.product-badge_item.bestseller  { background: linear-gradient(45deg, #EE7F00, #EE7F00); }
.product-badge_item.new-arrival { background: linear-gradient(45deg, #499F30, #499F30); }
.card-rating-row {
    display: flex; align-items: center; gap: 6px;
    margin: 6px 0 4px 0; font-size: 13px; color: #333;
}
.card-rating-row .star-icon    { color: #f5a623; font-size: 14px; }
.card-rating-row .verified-check {
    display: inline-flex; align-items: center; justify-content: center;
    width: 14px; height: 14px; border-radius: 50%;
    background: #1e88e5; color: #fff; font-size: 9px; font-weight: 700;
}
.card-rating-row .review-count { color: #666; font-size: 12px; }
.card-rating-row .divider      { color: #ccc; margin: 0 2px; }
.card-rating-row.no-reviews    { color: #999; font-size: 12px; }
.category-filter-link { cursor: pointer; }
.category-filter-link.active-cat { color: var(--color-main, #000); font-weight: 700; }
#gridLayout { transition: opacity .25s; }
#gridLayout.loading { opacity: .4; pointer-events: none; }
</style>

<body>
<button id="goTop"><span class="border-progress"></span><span class="icon icon-caret-up"></span></button>
<div class="preload preload-container" id="preload">
    <div class="preload-logo"><div class="spinner"></div></div>
</div>

<div id="wrapper">
    @include('components.frontend.header')

    <section class="s-page-title">
        <div class="container">
            <div class="content">
                <h1 class="title-page">Shop All</h1>
                <ul class="breadcrumbs-page">
                    <li><a href="{{ route('frontend.index') }}" class="h6 link">Home</a></li>
                    <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                    <li><h6 class="current-page fw-normal">Shop All</h6></li>
                </ul>
            </div>
        </div>
    </section>

    {{-- Hidden config passed from PHP to JS --}}
    <div id="page-meta"
         data-filter-url="{{ route('product.all.filter') }}"
         data-wishlist-url="{{ route('wishlist.add') }}"
         data-csrf="{{ csrf_token() }}"
         data-price-min="{{ $minPrice }}"
         data-price-max="{{ $maxPrice }}"
         style="display:none;"></div>

    <div class="flat-spacing">
        <div class="container">
            <div class="row">

                {{-- ══ Sidebar ══ --}}
                <div class="col-xl-3">
                    <div class="canvas-sidebar sidebar-filter canvas-filter left">
                        <div class="canvas-wrapper">
                            <div class="canvas-header d-xl-none">
                                <span class="title h3 fw-medium">Filter</span>
                                <span class="icon-close link icon-close-popup fs-24 close-filter"></span>
                            </div>

                            <div class="canvas-body">

                                {{-- Category --}}
                                <div class="widget-facet">
                                    <div class="facet-title" data-bs-target="#category" role="button"
                                         data-bs-toggle="collapse" aria-expanded="true" aria-controls="category">
                                        <span class="h4 fw-semibold">Category</span>
                                        <span class="icon icon-caret-down fs-20"></span>
                                    </div>
                                    <div id="category" class="collapse show">
                                        <ul class="collapse-body filter-group-check group-category">
                                            <li class="list-item">
                                                <a href="#" class="link h6 category-filter-link active-cat"
                                                   data-category-id="">All</a>
                                            </li>
                                            @foreach($allCategories as $cat)
                                                <li class="list-item">
                                                    <a href="#" class="link h6 category-filter-link"
                                                       data-category-id="{{ $cat->id }}">
                                                        {{ $cat->category_name }}
                                                        <span class="count">{{ $categoryCounts[$cat->id] ?? 0 }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                {{-- Availability --}}
                                <div class="widget-facet">
                                    <div class="facet-title" data-bs-target="#availability" role="button"
                                         data-bs-toggle="collapse" aria-expanded="true" aria-controls="availability">
                                        <span class="h4 fw-semibold">Availability</span>
                                        <span class="icon icon-caret-down fs-20"></span>
                                    </div>
                                    <div id="availability" class="collapse show">
                                        <ul class="collapse-body filter-group-check current-scrollbar">
                                            <li class="list-item">
                                                <input type="checkbox" name="availability" class="tf-check" id="inStock" value="in">
                                                <label for="inStock" class="label">
                                                    <span>In Stock</span>
                                                    <span class="count">{{ $inStockCount }}</span>
                                                </label>
                                            </li>
                                            <li class="list-item {{ $outStockCount == 0 ? 'disabled' : '' }}">
                                                <input type="checkbox" name="availability" class="tf-check" id="outStock" value="out">
                                                <label for="outStock" class="label">
                                                    <span>Out of Stock</span>
                                                    <span class="count">{{ $outStockCount }}</span>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                {{-- Product Type --}}
                                @if(($singleCount ?? 0) > 0 || ($comboCount ?? 0) > 0)
                                <div class="widget-facet">
                                    <div class="facet-title" data-bs-target="#productType" role="button"
                                         data-bs-toggle="collapse" aria-expanded="true" aria-controls="productType">
                                        <span class="h4 fw-semibold">Product Type</span>
                                        <span class="icon icon-caret-down fs-20"></span>
                                    </div>
                                    <div id="productType" class="collapse show">
                                        <ul class="collapse-body filter-group-check current-scrollbar">
                                            <li class="list-item">
                                                <input type="checkbox" class="tf-check" name="product_types[]" id="type_single" value="single">
                                                <label for="type_single" class="label">
                                                    <span>Single Product</span>
                                                    <span class="count">{{ $singleCount ?? 0 }}</span>
                                                </label>
                                            </li>
                                            <li class="list-item">
                                                <input type="checkbox" class="tf-check" name="product_types[]" id="type_combo" value="combo">
                                                <label for="type_combo" class="label">
                                                    <span>Combo Product</span>
                                                    <span class="count">{{ $comboCount ?? 0 }}</span>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                @endif

                                {{-- Size --}}
                                @if(!empty($units))
                                <div class="widget-facet">
                                    <div class="facet-title" data-bs-target="#size" role="button"
                                         data-bs-toggle="collapse" aria-expanded="true" aria-controls="size">
                                        <span class="h4 fw-semibold">Size</span>
                                        <span class="icon icon-caret-down fs-20"></span>
                                    </div>
                                    <div id="size" class="collapse show">
                                        <ul class="collapse-body filter-group-check current-scrollbar">
                                            @foreach($units as $unit)
                                                <li class="list-item">
                                                    <input type="checkbox" class="tf-check" name="units[]"
                                                           id="unit_{{ $loop->index }}" value="{{ $unit }}">
                                                    <label for="unit_{{ $loop->index }}" class="label">
                                                        <span>{{ $unit }}</span>
                                                        <span class="count">{{ $unitCounts[$unit] ?? 0 }}</span>
                                                    </label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                @endif

                                {{-- Perfume Notes --}}
                                <div class="widget-facet">
                                    <div class="facet-title" data-bs-target="#fragrance" role="button"
                                         data-bs-toggle="collapse" aria-expanded="true" aria-controls="fragrance">
                                        <span class="h4 fw-semibold">Perfume Notes</span>
                                        <span class="icon icon-caret-down fs-20"></span>
                                    </div>
                                    <div id="fragrance" class="collapse show">
                                        <ul class="collapse-body filter-group-check current-scrollbar">
                                            @foreach($fragranceTypes as $ft)
                                                <li class="list-item">
                                                    <input type="checkbox" class="tf-check" name="fragrance[]"
                                                           id="fragrance_{{ $ft->id }}" value="{{ $ft->id }}">
                                                    <label for="fragrance_{{ $ft->id }}" class="label">
                                                        <span>{{ $ft->title }}</span>
                                                        <span class="count">{{ $fragranceCounts[$ft->id] ?? 0 }}</span>
                                                    </label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                {{-- Price --}}
                                <div class="widget-facet">
                                    <div class="facet-title" data-bs-target="#price" role="button"
                                         data-bs-toggle="collapse" aria-expanded="true" aria-controls="price">
                                        <span class="h4 fw-semibold">Price</span>
                                        <span class="icon icon-caret-down fs-20"></span>
                                    </div>
                                    <div id="price" class="collapse show">
                                        <div class="collapse-body widget-price filter-price">
                                            <div class="price-val-range" id="price-value-range"
                                                 data-min="{{ $minPrice }}"
                                                 data-max="{{ $maxPrice }}"></div>
                                            <div class="box-value-price">
                                                <span class="h6 text-main">Price:</span>
                                                <div class="price-box">
                                                    <div class="price-val" id="price-min-value">{{ $minPrice }}</div>
                                                    <span>-</span>
                                                    <div class="price-val" id="price-max-value">{{ $maxPrice }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 d-none d-xl-block">
                                    <button id="reset-filter" class="tf-btn btn-reset w-100">Reset Filters</button>
                                </div>

                            </div>{{-- /canvas-body --}}

                            <div class="canvas-bottom d-xl-none">
                                <button id="reset-filter-mobile" class="tf-btn btn-reset">Reset Filters</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ Products ══ --}}
                <div class="col-xl-9">
                    <div class="tf-shop-control">
                        <div class="tf-control-filter d-xl-none">
                            <button type="button" id="filterShop" class="tf-btn-filter">
                                <span class="icon icon-filter"></span><span class="text">Filter</span>
                            </button>
                        </div>
                        <ul class="tf-control-layout">
                            <li class="tf-view-layout-switch sw-layout-2" data-value-layout="tf-col-2"><i class="icon-grid-2"></i></li>
                            <li class="tf-view-layout-switch sw-layout-3 active d-none d-md-flex" data-value-layout="tf-col-3"><i class="icon-grid-3"></i></li>
                        </ul>
                        <div class="tf-control-sorting">
                            <p class="h6 d-none d-lg-block">Sort by:</p>
                            <div class="tf-dropdown-sort" data-bs-toggle="dropdown">
                                <div class="btn-select">
                                    <span class="text-sort-value">Best Selling</span>
                                    <span class="icon icon-caret-down"></span>
                                </div>
                                <div class="dropdown-menu">
                                    <div class="select-item active" data-sort-value="best-selling"><span class="text-value-item">Best Selling</span></div>
                                    <div class="select-item" data-sort-value="price-low-high"><span class="text-value-item">Price, low to high</span></div>
                                    <div class="select-item" data-sort-value="price-high-low"><span class="text-value-item">Price, high to low</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Server-rendered initial grid --}}
                    <div class="wrapper-shop tf-grid-layout tf-col-3" id="gridLayout">
                        @forelse($products as $product)
                            @php
                                $images     = is_array($product->images) ? $product->images : json_decode($product->images, true);
                                $firstImage = $images[0] ?? 'default.png';

                                $sabcategory = $product->firstSubcategory ?? null;
                                if (!$sabcategory) {
                                    $subRaw     = $product->getRawOriginal('sub_category_id');
                                    $subDecoded = json_decode($subRaw, true);
                                    $subCatId   = (json_last_error() === JSON_ERROR_NONE && is_array($subDecoded))
                                        ? (isset($subDecoded[0]) ? (int)$subDecoded[0] : null)
                                        : (is_numeric($subRaw) ? (int)$subRaw : null);
                                    $sabcategory = $subCatId ? \App\Models\SabCategoryDetails::find($subCatId) : null;
                                }

                                $productUrl = ($sabcategory && $sabcategory->slug && $product->slug)
                                    ? route('product.details', [
                                        'cat'    => $product->category->slug ?? '',
                                        'sabcat' => $sabcategory->slug,
                                        'slug'   => $product->slug,
                                      ])
                                    : '#';

                                $isInWishlist = \App\Models\Wishlist::where('user_id', auth()->id() ?? 0)
                                    ->where('product_id', $product->id)->exists();

                                $hasOffer        = !empty($product->offer_price);
                                $discountPercent = 0;
                                if (!empty($product->discount) && $product->discount > 0) {
                                    $discountPercent = (int) round($product->discount);
                                } elseif ($hasOffer && $product->offer_price < $product->price) {
                                    $discountPercent = (int) round((($product->price - $product->offer_price) / $product->price) * 100);
                                }

                                $avgRating   = $product->avg_rating   ?? 0;
                                $reviewCount = $product->review_count ?? 0;
                            @endphp

                            <div class="card-product grid">
                                <div class="card-product_wrapper">
                                    @if($discountPercent > 0)
                                        <span class="product-discount-badge">{{ $discountPercent }}% OFF</span>
                                    @endif
                                    <a href="{{ $productUrl }}" class="product-img">
                                        <img class="lazyload img-product"
                                             src="{{ asset('signage/home/productimage/' . $firstImage) }}"
                                             alt="{{ $product->product_name }}">
                                        <img class="lazyload img-hover"
                                             src="{{ asset('signage/home/productimage/' . $firstImage) }}"
                                             alt="{{ $product->product_name }}">
                                    </a>
                                    <ul class="product-action_list">
                                        <li>
                                            @if($product->quantity > 0)
                                                <form class="add-to-cart-form d-inline" method="POST" action="{{ route('cart.add') }}">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                    <button type="submit" class="hover-tooltip tooltip-left box-icon">
                                                        <span class="icon icon-shopping-cart-simple"></span>
                                                        <span class="tooltip">Add to cart</span>
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="hover-tooltip tooltip-left box-icon disabled" disabled>
                                                    <span class="icon icon-x-circle"></span>
                                                    <span class="tooltip">Out of Stock</span>
                                                </button>
                                            @endif
                                        </li>
                                        <li class="wishlist">
                                            <form class="add-to-wishlist-form" data-product="{{ $product->id }}">
                                                @csrf
                                                <button type="button" class="hover-tooltip tooltip-left box-icon wishlist-btn">
                                                    <span class="icon wishlist-icon {{ $isInWishlist ? 'icon-trash' : 'icon-heart' }}"></span>
                                                    <span class="tooltip">{{ $isInWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}</span>
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <a href="{{ $productUrl }}" class="hover-tooltip tooltip-left box-icon">
                                                <span class="icon icon-view"></span>
                                                <span class="tooltip">Quick view</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <ul class="product-badge_list">
                                        @if($product->is_bestseller)<li class="product-badge_item h6 bestseller">Bestseller</li>@endif
                                        @if($product->is_new_arrival)<li class="product-badge_item h6 new-arrival">New Arrival</li>@endif
                                    </ul>
                                </div>
                                <div class="card-product_info">
                                    <a href="{{ $productUrl }}" class="name-product h4 link">{{ $product->product_name }}</a>

                                    {{-- ═══ Rating / review row — HIDDEN for now (do not display) ═══
                                         Un-comment this block to bring the rating row back.
                                    @if($reviewCount > 0)
                                        <div class="card-rating-row">
                                            <i class="icon-star star-icon"></i>
                                            <strong>{{ number_format($avgRating, 1) }}</strong>
                                            <span class="divider">|</span>
                                            <span class="verified-check">✓</span>
                                            <span class="review-count">({{ $reviewCount }} Review{{ $reviewCount == 1 ? '' : 's' }})</span>
                                        </div>
                                    @else
                                        <div class="card-rating-row no-reviews">
                                            <i class="icon-star" style="color:#ddd;"></i> No reviews yet
                                        </div>
                                    @endif
                                    ═══ end rating row ═══ --}}

                                    <div class="price-wrap">
                                        @if($hasOffer)
                                            <span class="price-old h6 fw-normal">Rs.{{ number_format($product->price) }}</span>
                                            <span class="price-new h6">Rs.{{ number_format($product->offer_price) }}</span>
                                        @else
                                            <span class="price-new h6">Rs.{{ number_format($product->price) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 w-100">
                                <h5 class="text-muted">No products found.</h5>
                            </div>
                        @endforelse
                    </div>{{-- /gridLayout --}}

                    {{-- Pagination --}}
                    <div class="wg-pagination-wrap">
                        @if ($products->lastPage() > 1)
                            <div class="wd-full wg-pagination m-0 justify-content-center d-flex">
                                @if ($products->onFirstPage())
                                    <span class="pagination-item h6 direct disabled"><i class="icon icon-caret-left"></i></span>
                                @else
                                    <a href="{{ $products->previousPageUrl() }}" class="pagination-item h6 direct"><i class="icon icon-caret-left"></i></a>
                                @endif
                                @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                                    @if ($page == $products->currentPage())
                                        <span class="pagination-item h6 active">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="pagination-item h6">{{ $page }}</a>
                                    @endif
                                @endforeach
                                @if ($products->hasMorePages())
                                    <a href="{{ $products->nextPageUrl() }}" class="pagination-item h6 direct"><i class="icon icon-caret-right"></i></a>
                                @else
                                    <span class="pagination-item h6 direct disabled"><i class="icon icon-caret-right"></i></span>
                                @endif
                            </div>
                        @endif
                    </div>

                </div>{{-- /col products --}}
            </div>
        </div>
    </div>

    @include('components.frontend.footer')
</div>{{-- /wrapper --}}

<div class="offcanvas offcanvas-start canvas-mb" id="mobileMenu">
    <span class="icon-close-popup" data-bs-dismiss="offcanvas"><i class="icon-close"></i></span>
    <div class="canvas-header">
        <p class="text-logo-mb"><img src="{{ asset('images/logo/logo.webp') }}"></p>
    </div>
</div>

@include('components.frontend.main-js')

<script>
$(document).ready(function () {

    var meta = document.getElementById('page-meta');
    if (!meta) { console.error('page-meta missing'); return; }

    var FILTER_URL   = meta.dataset.filterUrl;
    var WISHLIST_URL = meta.dataset.wishlistUrl;
    var CSRF         = meta.dataset.csrf;

    var activeCategoryId = '';
    var globalMin        = parseInt(meta.dataset.priceMin) || 0;
    var globalMax        = parseInt(meta.dataset.priceMax) || 99999;
    var currentMinPrice  = globalMin;
    var currentMaxPrice  = globalMax;
    var priceSliderReady = false;

    console.log('ShopAll filter URL:', FILTER_URL);

    /* ══ Price slider — FIXED: destroy any existing instance + try/catch ══
       The theme's main-js auto-inits this slider. Calling create() again
       throws "Slider was already initialized" and kills ALL handlers below.
       This block prevents that crash. */
    var sliderEl = document.getElementById('price-value-range');
    try {
        if (sliderEl && typeof noUiSlider !== 'undefined' && globalMin < globalMax) {

            // Destroy any instance the theme already created
            if (sliderEl.noUiSlider && typeof sliderEl.noUiSlider.destroy === 'function') {
                sliderEl.noUiSlider.destroy();
            }

            noUiSlider.create(sliderEl, {
                start   : [globalMin, globalMax],
                connect : true,
                range   : { 'min': globalMin, 'max': globalMax },
                format  : { to: function(v){ return parseInt(v); }, from: function(v){ return parseInt(v); } }
            });

            sliderEl.noUiSlider.on('update', function (values) {
                currentMinPrice = parseInt(values[0]);
                currentMaxPrice = parseInt(values[1]);
                $("#price-min-value").text(currentMinPrice);
                $("#price-max-value").text(currentMaxPrice);
            });

            sliderEl.noUiSlider.on('change', function () { loadProducts(1); });

            priceSliderReady = true;
        }
    } catch (err) {
        // Even if the slider fails, filters MUST keep working
        console.error('Price slider init failed (filters still work):', err);
        priceSliderReady = false;
    }

    /* ── Category ── */
    $(document).on('click', '.category-filter-link', function (e) {
        e.preventDefault();
        activeCategoryId = $(this).data('category-id') !== undefined ? String($(this).data('category-id')) : '';
        $('.category-filter-link').removeClass('active-cat');
        $(this).addClass('active-cat');
        loadProducts(1);
    });

    /* ── Availability (mutual exclusion) ── */
    $(document).on('change', "input[name='availability']", function () {
        if ($(this).is(':checked')) $("input[name='availability']").not(this).prop('checked', false);
        loadProducts(1);
    });

    /* ── Other checkboxes ── */
    $(document).on('change', "input[name='fragrance[]'], input[name='units[]'], input[name='product_types[]']",
        function () { loadProducts(1); });

    /* ── Sort ── */
    $(document).on('click', '.tf-dropdown-sort .select-item', function () {
        $('.tf-dropdown-sort .select-item').removeClass('active');
        $(this).addClass('active');
        $(".text-sort-value").text($(this).find('.text-value-item').text());
        loadProducts(1);
    });

    /* ── Pagination ── */
    $(document).on('click', '.wg-pagination a', function (e) {
        e.preventDefault();
        var match = ($(this).attr('href') || '').match(/page=(\d+)/);
        loadProducts(match ? parseInt(match[1]) : 1);
    });

    /* ── Layout switch ── */
    $(document).on('click', '.tf-view-layout-switch', function () {
        $('.tf-view-layout-switch').removeClass('active');
        $(this).addClass('active');
        $('#gridLayout').removeClass('tf-col-2 tf-col-3').addClass($(this).data('value-layout'));
    });

    /* ── Reset (clears EVERY filter then reloads) ── */
    function doReset() {
        activeCategoryId = '';
        $('.category-filter-link').removeClass('active-cat');
        $('.category-filter-link[data-category-id=""]').addClass('active-cat');
        $("input[name='availability'], input[name='fragrance[]'], input[name='units[]'], input[name='product_types[]']").prop('checked', false);

        if (priceSliderReady) {
            sliderEl.noUiSlider.reset();
            currentMinPrice = globalMin;
            currentMaxPrice = globalMax;
        }

        $('.tf-dropdown-sort .select-item').removeClass('active');
        $('.tf-dropdown-sort .select-item[data-sort-value="best-selling"]').addClass('active');
        $(".text-sort-value").text("Best Selling");

        loadProducts(1);
    }
    $(document).on('click', '#reset-filter, #reset-filter-mobile', doReset);

    /* ── Core loader ── */
    function loadProducts(page) {
        page = parseInt(page) || 1;
        var grid = $('#gridLayout');
        grid.addClass('loading');

        var payload = {
            _token        : CSRF,
            category_id   : activeCategoryId,
            availability  : $("input[name='availability']:checked").val() || '',
            fragrance_ids : $("input[name='fragrance[]']:checked").map(function(){ return $(this).val(); }).get(),
            units         : $("input[name='units[]']:checked").map(function(){ return $(this).val(); }).get(),
            product_types : $("input[name='product_types[]']:checked").map(function(){ return $(this).val(); }).get(),
            min_price     : currentMinPrice,
            max_price     : currentMaxPrice,
            sort          : $('.tf-dropdown-sort .select-item.active').data('sort-value') || 'best-selling',
            page          : page
        };

        $.ajax({
            url     : FILTER_URL,
            type    : 'POST',
            headers : { 'X-CSRF-TOKEN': CSRF },
            data    : payload,
            success : function (res) {
                grid.removeClass('loading').html(res.html || '');
                $('.wg-pagination-wrap').html(res.pagination || '');
                $('html, body').animate({ scrollTop: grid.offset().top - 100 }, 300);
            },
            error   : function (xhr) {
                grid.removeClass('loading');
                console.error('ShopAll filter FAILED:', xhr.status, xhr.responseText);
                grid.html('<div class="text-center py-5 w-100"><h5 class="text-danger">Filter error ('+xhr.status+'). Check console.</h5></div>');
            }
        });
    }
}); // end ready
</script>

{{-- Cart AJAX --}}
<script>
$(document).on('submit', '.add-to-cart-form', function (e) {
    e.preventDefault();
    var form = $(this);
    $.ajax({
        url: form.attr('action'), method: 'POST', data: form.serialize(),
        success: function (res) {
            if (res.success) {
                notyf.open({ type: 'custom-success', message: res.message });
                if (res.cart_count !== undefined) $('.cart-count').text(res.cart_count);
            } else {
                notyf.error(res.message || 'Something went wrong!');
            }
        }
    });
});
</script>

{{-- Wishlist AJAX --}}
<script>
$(document).on('click', '.wishlist-btn', function (e) {
    e.preventDefault();
    var form  = $(this).closest('form');
    var meta  = document.getElementById('page-meta');
    var icon  = form.find('.wishlist-icon');
    var tip   = form.find('.tooltip');
    $.ajax({
        url: meta.dataset.wishlistUrl, method: 'POST',
        data: { _token: meta.dataset.csrf, product_id: form.data('product') },
        success: function (r) {
            if (r.status === 'added') {
                notyf.open({ type: 'custom-success', message: r.message || 'Added to wishlist' });
                icon.removeClass('icon-heart').addClass('icon-trash');
                tip.text('Remove from Wishlist');
            } else {
                notyf.error(r.message || 'Removed from wishlist');
                icon.removeClass('icon-trash').addClass('icon-heart');
                tip.text('Add to Wishlist');
            }
            if (r.count !== undefined) $('.wishlist-count').text(r.count);
        }
    });
});
</script>

</body>
</html>