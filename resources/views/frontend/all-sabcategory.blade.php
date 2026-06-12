<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
    @include('components.frontend.head')


</head>

<body>
    <button id="goTop"><span class="border-progress"></span><span class="icon icon-caret-up"></span></button>
    <div class="preload preload-container" id="preload"><div class="preload-logo"><div class="spinner"></div></div></div>

    <div id="wrapper">
        @include('components.frontend.header')

        <section class="s-page-title">
            <div class="container">
                <div class="content">
                    <h1 class="title-page">{{ $sabcategory->sab_category_name }}</h1>
                    <ul class="breadcrumbs-page">
                        <li><a href="{{ url('/') }}" class="h6 link">Home</a></li>
                        <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                        <li><h6 class="current-page fw-normal">{{ $sabcategory->sab_category_name }}</h6></li>
                    </ul>
                </div>
            </div>
        </section>

        <div class="flat-spacing combo-wrap">
            <div class="container">
                <div class="row">

                    @php
                        use Illuminate\Support\Facades\Auth;
                        $userId    = Auth::guard('custom')->check() ? Auth::guard('custom')->id() : null;
                        $sessionId = $userId ? null : session()->getId();
                        $currentComboCount = \App\Models\Cart::where('combo', 'yes')
                            ->where('sub_category_id', '!=', 5)
                            ->when($userId,  fn($q) => $q->where('user_id',    $userId))
                            ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
                            ->count();
                    @endphp

                    @if(!$hasCombo)
                    <div class="col-xl-3">
                        <div class="canvas-sidebar sidebar-filter canvas-filter left">
                            <div class="canvas-wrapper">
                                <div class="canvas-header d-xl-none">
                                    <span class="title h3 fw-medium">Filter</span>
                                    <span class="icon-close link icon-close-popup fs-24 close-filter"></span>
                                </div>

                                <div class="canvas-body">
                                    <div class="widget-facet">
                                        <div class="facet-title" data-bs-target="#category" role="button" data-bs-toggle="collapse" aria-expanded="true" aria-controls="category">
                                            <span class="h4 fw-semibold">Sub Category</span>
                                            <span class="icon icon-caret-down fs-20"></span>
                                        </div>
                                        <div id="category" class="collapse show">
                                            <ul class="collapse-body filter-group-check group-category">
                                                @foreach($allsabCategories as $cat)
                                                    <li class="list-item">
                                                        <a href="{{ route('product.subcategory', ['category' => $category->slug, 'sabcat' => $cat->slug]) }}" class="link h6">
                                                            {{ $cat->sab_category_name }}
                                                            <span class="count">{{ $categoryCounts[$cat->id] ?? 0 }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="widget-facet">
                                        <div class="facet-title" data-bs-target="#availability" role="button" data-bs-toggle="collapse" aria-expanded="true" aria-controls="availability">
                                            <span class="h4 fw-semibold">Availability</span>
                                            <span class="icon icon-caret-down fs-20"></span>
                                        </div>
                                        <div id="availability" class="collapse show">
                                            <ul class="collapse-body filter-group-check current-scrollbar">
                                                <li class="list-item">
                                                    <input type="checkbox" name="availability" class="tf-check" id="inStock" value="in">
                                                    <label for="inStock" class="label"><span>In Stock</span><span class="count">{{ $inStockCount }}</span></label>
                                                </li>
                                                <li class="list-item {{ $outStockCount == 0 ? 'disabled' : '' }}">
                                                    <input type="checkbox" name="availability" class="tf-check" id="outStock" value="out">
                                                    <label for="outStock" class="label"><span>Out of Stock</span><span class="count">{{ $outStockCount }}</span></label>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    @if(($singleCount ?? 0) > 0 || ($comboCount ?? 0) > 0)
                                    <div class="widget-facet">
                                        <div class="facet-title" data-bs-target="#productType" role="button" data-bs-toggle="collapse" aria-expanded="true" aria-controls="productType">
                                            <span class="h4 fw-semibold">Product Type</span>
                                            <span class="icon icon-caret-down fs-20"></span>
                                        </div>
                                        <div id="productType" class="collapse show">
                                            <ul class="collapse-body filter-group-check current-scrollbar">
                                                <li class="list-item">
                                                    <input type="checkbox" class="tf-check" name="product_types[]" id="type_single" value="single">
                                                    <label for="type_single" class="label"><span>Single Product</span><span class="count">{{ $singleCount ?? 0 }}</span></label>
                                                </li>
                                                <li class="list-item">
                                                    <input type="checkbox" class="tf-check" name="product_types[]" id="type_combo" value="combo">
                                                    <label for="type_combo" class="label"><span>Combo Product</span><span class="count">{{ $comboCount ?? 0 }}</span></label>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    @endif

                                    @if(!empty($units))
                                    <div class="widget-facet">
                                        <div class="facet-title" data-bs-target="#size" role="button" data-bs-toggle="collapse" aria-expanded="true" aria-controls="size">
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

                                    <div class="widget-facet">
                                        <div class="facet-title" data-bs-target="#fragrance" role="button" data-bs-toggle="collapse" aria-expanded="true" aria-controls="fragrance">
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

                                    <div class="widget-facet">
                                        <div class="facet-title" data-bs-target="#price" role="button" data-bs-toggle="collapse" aria-expanded="true" aria-controls="price">
                                            <span class="h4 fw-semibold">Price</span>
                                            <span class="icon icon-caret-down fs-20"></span>
                                        </div>
                                        <div id="price" class="collapse show">
                                            <div class="collapse-body widget-price filter-price">
                                                <div class="price-val-range" id="price-value-range" data-min="{{ $minPrice ?? 0 }}" data-max="{{ $maxPrice ?? 0 }}"></div>
                                                <div class="box-value-price">
                                                    <span class="h6 text-main">Price:</span>
                                                    <div class="price-box">
                                                        <div class="price-val" id="price-min-value" data-currency="₹">{{ $minPrice ?? 0 }}</div>
                                                        <span>-</span>
                                                        <div class="price-val" id="price-max-value" data-currency="₹">{{ $maxPrice ?? 0 }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Desktop reset button --}}
                                    <div class="mt-3 d-none d-xl-block">
                                        <button id="reset-filter-desktop" class="tf-btn btn-reset w-100">Reset Filters</button>
                                    </div>
                                </div>

                                <div class="canvas-bottom d-xl-none">
                                    <button id="reset-filter" class="tf-btn btn-reset">Reset Filters</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="{{ $hasCombo ? 'col-xl-12' : 'col-xl-9' }}">
                        @if(!$hasCombo)
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
                                <div class="tf-dropdown-sort" data-bs-toggle="dropdown">
                                    <div class="btn-select">
                                        <span class="text-sort-value">Sort by</span>
                                        <span class="icon icon-caret-down"></span>
                                    </div>
                                    <div class="dropdown-menu">
                                        <div class="select-item active remove-all-filters" data-sort-value="best-selling"><span class="text-value-item">Best Selling</span></div>
                                        <div class="select-item" data-sort-value="price-low-high"><span class="text-value-item">Price, low to high</span></div>
                                        <div class="select-item" data-sort-value="price-high-low"><span class="text-value-item">Price, high to low</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($hasCombo)
                            <h2 class="text-dark fw-bold mb-2 text-center">Make Your Own Combo</h2>
                        @endif

                        {{-- ═══════════════ NORMAL PRODUCTS ═══════════════ --}}
                        <div class="wrapper-shop tf-grid-layout tf-col-3 center-custom" id="gridLayout">
                            @foreach($products as $product)
                                @php
                                    $images     = json_decode($product->images, true);
                                    $firstImage = !empty($images) ? $images[0] : 'default.png';

                                    $productUrl = route('product.details', [
                                        'cat'    => $category->slug,
                                        'sabcat' => $sabcategory->slug,
                                        'slug'   => $product->slug
                                    ]);

                                    $hasOffer = !empty($product->offer_price);

                                    $discountPercent = 0;
                                    if (!empty($product->discount) && $product->discount > 0) {
                                        $discountPercent = (int) round($product->discount);
                                    } elseif ($hasOffer && $product->offer_price < $product->price) {
                                        $discountPercent = (int) round((($product->price - $product->offer_price) / $product->price) * 100);
                                    }

                                    $avgRating   = $product->avg_rating   ?? 0;
                                    $reviewCount = $product->review_count ?? 0;

                                    // Wishlist state (matches the AJAX-rendered cards so hover icon is consistent)
                                    $isInWishlist = \App\Models\Wishlist::where('user_id', auth()->id() ?? 0)
                                        ->where('product_id', $product->id)->exists();
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

                                        {{-- Hover action icons: add-to-cart / wishlist / quick view --}}
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

                                        {{-- Bestseller / New Arrival tags (now shown on initial load too) --}}
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
                            @endforeach
                        </div>

                        {{-- ═══════════════ COMBO PRODUCTS ═══════════════ --}}
                        @if(isset($sabcategory) && $sabcategory->slug == 'make-your-own-combo')
                            <div class="wrapper-shop tf-grid-layout tf-col-3 center-custom" id="comboLayout">
                                @foreach($comboProducts as $product)
                                    @php
                                        $images     = json_decode($product->images, true);
                                        $firstImage = !empty($images) ? $images[0] : 'default.png';
                                        $categorySlug = $category->slug ?? 'unknown-category';
                                        $subCategory  = \App\Models\SabCategoryDetails::find($product->sub_category_id);

                                        if (!$subCategory) {
                                            $productName = strtolower($product->product_name ?? '');
                                            if (str_contains($productName, 'men'))        $subCategory = \App\Models\SabCategoryDetails::where('slug', 'men')->first();
                                            elseif (str_contains($productName, 'women')) $subCategory = \App\Models\SabCategoryDetails::where('slug', 'women')->first();
                                            elseif (str_contains($productName, 'unisex')) $subCategory = \App\Models\SabCategoryDetails::where('slug', 'unisex')->first();
                                        }

                                        $subCategorySlug = $subCategory->slug ?? 'make-your-own-combo';
                                        $productUrl = route('product.details', [
                                            'cat' => $categorySlug, 'sabcat' => $subCategorySlug, 'slug' => $product->slug
                                        ]);

                                        $hasOffer = !empty($product->offer_price);
                                        $discountPercent = 0;
                                        if (!empty($product->discount) && $product->discount > 0) {
                                            $discountPercent = (int) round($product->discount);
                                        } elseif ($hasOffer && $product->offer_price < $product->price) {
                                            $discountPercent = (int) round((($product->price - $product->offer_price) / $product->price) * 100);
                                        }

                                        $avgRating   = $product->avg_rating   ?? 0;
                                        $reviewCount = $product->review_count ?? 0;

                                        // Wishlist state for the combo cards' hover icon
                                        $isInWishlist = \App\Models\Wishlist::where('user_id', auth()->id() ?? 0)
                                            ->where('product_id', $product->id)->exists();
                                    @endphp

                                    <div class="card-product grid">
                                        <div class="card-product_wrapper">
                                            @if($discountPercent > 0)<span class="product-discount-badge">{{ $discountPercent }}% OFF</span>@endif

                                            <a href="{{ $productUrl }}" class="product-img">
                                                <img class="lazyload img-product" src="{{ asset('signage/home/productimage/' . $firstImage) }}" alt="{{ $product->product_name }}">
                                                <img class="lazyload img-hover"   src="{{ asset('signage/home/productimage/' . $firstImage) }}" alt="{{ $product->product_name }}">
                                            </a>

                                            {{-- Hover action icons: wishlist / quick view
                                                 (no add-to-cart here — combo items are added via the "Add" combo button below) --}}
                                            <ul class="product-action_list">
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

                                            {{-- Bestseller / New Arrival tags --}}
                                            <ul class="product-badge_list">
                                                @if($product->is_bestseller)<li class="product-badge_item h6 bestseller">Bestseller</li>@endif
                                                @if($product->is_new_arrival)<li class="product-badge_item h6 new-arrival">New Arrival</li>@endif
                                            </ul>

                                            <div class="combo-add-btn mt-2">
                                                <form class="combo-form d-inline" method="POST" action="{{ route('cart.add_combo') }}">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                    <input type="hidden" name="combo_text" value="100ml">
                                                    <button type="submit" class="tf-btn w-100 animate-btn combo-btn" data-product="{{ $product->id }}">
                                                        <span class="btn-text">Add</span>
                                                    </button>
                                                </form>
                                            </div>
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
                                @endforeach
                            </div>
                        @endif

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
                </div>
            </div>
        </div>

        {{-- Combo Modal --}}
        <div class="modal fade" id="comboModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-3 shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title w-100 text-center">🎉 Combo Offer Applied</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="combo-products-list" class="row"></div>
                        <div class="text-center mt-4">
                            <h5>Total: ₹<span id="combo-total"></span></h5>
                            <h4 class="text-success">Final Price (55% OFF): ₹<span id="combo-final"></span></h4>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('cart.index') }}" class="btn btn-success w-100">Proceed to Cart</a>
                    </div>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const imageBasePath = "{{ asset('signage/home/productimage') }}/";
            let currentComboCount = {{ $currentComboCount }};

            function updateComboButtons(count) {
                document.querySelectorAll('.combo-btn').forEach(btn => {
                    if (btn.getAttribute("data-action") === "added") {
                        btn.disabled = true;
                        btn.classList.add("btn-success");
                    } else if (count >= 2) {
                        btn.disabled = false;
                        btn.classList.add("fade-disabled");
                    } else {
                        btn.disabled = false;
                        btn.classList.remove("fade-disabled");
                    }
                });
            }

            @php
                $userIdJs = Auth::guard('custom')->check() ? Auth::guard('custom')->id() : null;
                $comboIds = \App\Models\Cart::where('combo', 'yes')
                            ->where('sub_category_id', '!=', 5)
                            ->when($userIdJs,  fn($q) => $q->where('user_id', $userIdJs))
                            ->when(!$userIdJs, fn($q) => $q->where('session_id', session()->getId()))
                            ->pluck('product_id')
                            ->toArray();
            @endphp

            const comboProducts = @json($comboIds);

            $('.combo-btn').each(function() {
                let btn = $(this);
                if (comboProducts.includes(btn.data('product'))) {
                    btn.removeClass("btn-primary").addClass("btn-success");
                    btn.find(".btn-text").text("Remove");
                    btn.attr("data-action", "remove");
                }
            });

            updateComboButtons(currentComboCount);

            let firstComboAdded = true;

            $(document).on('submit', '.combo-form', function (e) {
                e.preventDefault();
                let form = $(this);
                $.ajax({
                    url: form.attr('action'), method: 'POST', data: form.serialize(),
                    success: function (res) {
                        if (res.success) {
                            notyf.open({ type: 'success', message: res.message });
                            if (res.combo_products) {
                                res.combo_products.forEach(p => {
                                    let btn = $(`.combo-btn[data-product="${p.product_id}"]`);
                                    if (btn.length) {
                                        btn.removeClass("btn-primary").addClass("btn-success");
                                        btn.find(".btn-text").text("Added");
                                        btn.attr("data-action", "added");
                                        btn.prop("disabled", true);
                                    }
                                });
                                currentComboCount = res.combo_products.length;
                                updateComboButtons(currentComboCount);
                            }
                            if (res.show_modal && res.combo_products?.length) {
                                renderComboModal(res.combo_products);
                                $('#comboModal').modal('show');
                            }
                            if (firstComboAdded) {
                                firstComboAdded = false;
                                setTimeout(() => location.reload(), 60000);
                            }
                        } else {
                            notyf.error(res.message || 'Something went wrong!');
                        }
                    }
                });
            });

            $(document).on('click', '.combo-btn', function (e) {
                let btn = $(this);
                if (btn.attr('data-action') === 'remove') {
                    e.preventDefault();
                    $.ajax({
                        url: "{{ route('cart.remove_combo') }}",
                        method: 'POST',
                        data: { _token: "{{ csrf_token() }}", product_id: btn.data('product') },
                        success: function (res) {
                            if (res.success) {
                                notyf.open({ type: 'success', message: res.message });
                                currentComboCount = res.combo_count;
                                btn.removeClass("btn-success").addClass("btn-primary");
                                btn.find(".btn-text").text("Add");
                                btn.removeAttr("data-action");
                                updateComboButtons(currentComboCount);
                            }
                        }
                    });
                }
            });

            function renderComboModal(products) {
                let list = $('#combo-products-list');
                list.empty();
                let total = 0;
                products.forEach(p => {
                    let price = parseFloat(p.price ?? 0);
                    total += price;
                    let imagesArr = [];
                    try { imagesArr = JSON.parse(p.images); } catch (e) { imagesArr = []; }
                    let firstImage = imagesArr.length > 0 ? imagesArr[0] : null;
                    let imageUrl = firstImage ? (imageBasePath + '/' + firstImage) : "{{ asset('signage/no-image.png') }}";
                    list.append(`
                        <div class="col-md-6 text-center mb-3">
                            <img src="${imageUrl}" class="img-fluid rounded mb-2" style="max-height:120px;">
                            <p>${p.product_name}</p>
                            <p>₹${price.toFixed(2)}</p>
                        </div>
                    `);
                });
                let finalPrice = (total * 0.55);
                let finalcomboPrice = (total - finalPrice);
                $('#combo-total').text(total.toFixed(2));
                $('#combo-final').text(finalcomboPrice.toFixed(2));
                var comboModal = new bootstrap.Modal(document.getElementById('comboModal'));
                comboModal.show();
                document.getElementById('comboModal').addEventListener('hidden.bs.modal', function () {
                    location.reload();
                });
            }
        });
        </script>

        <script>
        $(document).ready(function () {
            function loadProducts(page = 1) {
                let sub_category_id = "{{ $sabcategory->id }}";
                let sub_category_slug = "{{ $sabcategory->slug }}";

                if (sub_category_slug === "unisex")      sub_category_id = [1, 2, 3];
                else if (sub_category_slug === "men")    sub_category_id = [1, 3];
                else if (sub_category_slug === "women")  sub_category_id = [2, 3];

                let availability = $("input[name='availability']:checked").attr("id") === 'inStock' ? 'in' :
                                   $("input[name='availability']:checked").attr("id") === 'outStock' ? 'out' : '';

                const fragrance_ids = $("input[name='fragrance[]']:checked").map(function() { return $(this).val(); }).get();
                const units         = $("input[name='units[]']:checked").map(function() { return $(this).val(); }).get();
                const product_types = $("input[name='product_types[]']:checked").map(function() { return $(this).val(); }).get();

                let min_price = parseInt($("#price-min-value").text()) || 0;
                let max_price = parseInt($("#price-max-value").text()) || 0;
                let sort = $(".tf-dropdown-sort .select-item.active").data("sort-value") || 'best-selling';

                $.ajax({
                    url: "{{ route('subcategory.filter') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        sub_category_id, sub_category_slug,
                        availability, fragrance_ids,
                        units, product_types,
                        min_price, max_price, sort,
                        page: parseInt(page) || 1
                    },
                    success: function (response) {
                        $("#gridLayout").html(response.html);
                        $(".wg-pagination").html(response.pagination);
                        $('html, body').animate({scrollTop: $("#gridLayout").offset().top - 100}, 500);
                    }
                });
            }

            $(document).on("change", "input[name='availability'], input[name='fragrance[]'], input[name='units[]'], input[name='product_types[]']", function(){
                loadProducts(1);
            });

            $(".select-item").on("click", function () {
                $(".select-item").removeClass("active");
                $(this).addClass("active");
                loadProducts(1);
            });

            $(document).on("click", ".wg-pagination a", function(e){
                e.preventDefault();
                let page = $(this).attr("href").split("page=")[1];
                loadProducts(page);
            });

            // Reset — covers BOTH desktop + mobile buttons, also resets slider position
            $(document).on("click", "#reset-filter, #reset-filter-desktop", function(){
                $("input[name='availability'], input[name='fragrance[]'], input[name='units[]'], input[name='product_types[]']").prop("checked", false);

                var sliderEl = document.getElementById('price-value-range');
                if (sliderEl && sliderEl.noUiSlider) {
                    sliderEl.noUiSlider.set([
                        parseInt($("#price-value-range").data("min")),
                        parseInt($("#price-value-range").data("max"))
                    ]);
                } else {
                    $("#price-min-value").text($("#price-value-range").data("min"));
                    $("#price-max-value").text($("#price-value-range").data("max"));
                }

                $(".select-item").removeClass("active");
                $(".select-item[data-sort-value='best-selling']").addClass("active");
                loadProducts(1);
            });

            if ($("#price-value-range").length) {
                let min = parseInt($("#price-value-range").data("min"));
                let max = parseInt($("#price-value-range").data("max"));
                var priceSlider = document.getElementById('price-value-range');

                // Guard: destroy any instance the theme's main-js already created
                if (priceSlider.noUiSlider) { priceSlider.noUiSlider.destroy(); }

                noUiSlider.create(priceSlider, {
                    start: [min, max], connect: true,
                    range: { 'min': min, 'max': max },
                    format: { to: v => parseInt(v), from: v => parseInt(v) }
                });
                priceSlider.noUiSlider.on('update', function (values) {
                    $("#price-min-value").text(values[0]);
                    $("#price-max-value").text(values[1]);
                });
                priceSlider.noUiSlider.on('change', function () { loadProducts(1); });
            }

            $(".tf-view-layout-switch").on("click", function(){
                $(".tf-view-layout-switch").removeClass("active");
                $(this).addClass("active");
                let layout = $(this).data("value-layout");
                $(".wrapper-shop").removeClass("tf-col-2 tf-col-3").addClass(layout);
            });

            loadProducts(1);
        });
        </script>

        @include('components.frontend.footer')
    </div>

    @include('components.frontend.main-js')

    <script>
    $(document).on('click', '.wishlist-btn', function(e) {
        e.preventDefault();
        let form = $(this).closest('form');
        let productId = form.data('product');
        let token = form.find('input[name="_token"]').val();
        let icon = form.find('.wishlist-icon');
        let tooltip = form.find('.tooltip');
        $.ajax({
            url: "{{ route('wishlist.add') }}", method: "POST",
            data: { _token: token, product_id: productId },
            success: function(response) {
                if (response.status === 'added') {
                    notyf.open({ type: 'custom-success', message: response.message || "Added to wishlist" });
                    icon.removeClass('icon-heart').addClass('icon-trash');
                    tooltip.text('Remove from Wishlist');
                } else if (response.status === 'removed') {
                    notyf.error(response.message || "Removed from wishlist");
                    icon.removeClass('icon-trash').addClass('icon-heart');
                    tooltip.text('Add to Wishlist');
                }
                if (response.count !== undefined) $(".wishlist-count").text(response.count);
            }
        });
    });
    </script>

    <script>
    $(document).ready(function() {
        $(document).on('submit', '.add-to-cart-form', function(e) {
            e.preventDefault();
            let form = $(this);
            $.ajax({
                url: form.attr('action'), method: 'POST', data: form.serialize(),
                success: function(res) {
                    if (res.success) {
                        notyf.open({ type: 'custom-success', message: res.message });
                        $('#cart-count').text(res.cart_count);
                        setTimeout(() => location.reload(), 500);
                    } else {
                        notyf.error(res.message || 'Something went wrong!');
                    }
                }
            });
        });
    });
    </script>

</body>
</html>