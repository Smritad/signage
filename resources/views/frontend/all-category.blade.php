<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.frontend.head')
</head>

<body>

    <button id="goTop">
        <span class="border-progress"></span>
        <span class="icon icon-caret-up"></span>
    </button>
    <div class="preload preload-container" id="preload">
        <div class="preload-logo"><div class="spinner"></div></div>
    </div>

    <div id="wrapper">
        @include('components.frontend.header')

        <section class="s-page-title">
            <div class="container">
                <div class="content">
                    <h1 class="title-page">{{ $category->category_name }}</h1>
                    <ul class="breadcrumbs-page">
                        <li><a href="{{ route('frontend.index') }}" class="h6 link">Home</a></li>
                        <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                        <li><h6 class="current-page fw-normal">{{ $category->category_name }}</h6></li>
                    </ul>
                </div>
            </div>
        </section>

        <div class="flat-spacing">
            <div class="container">
                <div class="row">

                    {{-- Sidebar Filter --}}
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
                                            <span class="h4 fw-semibold">Category</span>
                                            <span class="icon icon-caret-down fs-20"></span>
                                        </div>
                                        <div id="category" class="collapse show">
                                            <ul class="collapse-body filter-group-check group-category">
                                                @foreach($allCategories as $cat)
                                                    <li class="list-item">
                                                        <a href="{{ route('product.category', $cat->slug) }}" class="link h6">
                                                            {{ $cat->category_name }}
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
                                                <li class="list-item">
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
                                                <div class="price-val-range" id="price-value-range"
                                                     data-min="{{ $minPrice }}" data-max="{{ $maxPrice }}"></div>
                                                <div class="box-value-price">
                                                    <span class="h6 text-main">Price:</span>
                                                    <div class="price-box">
                                                        <div class="price-val" id="price-min-value" data-currency="₹">{{ $minPrice }}</div>
                                                        <span>-</span>
                                                        <div class="price-val" id="price-max-value" data-currency="₹">{{ $maxPrice }}</div>
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

                    {{-- Products --}}
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
                        </div>{{-- /tf-shop-control --}}

                        {{-- Product Grid --}}
                        <div class="wrapper-shop tf-grid-layout tf-col-3" id="gridLayout">
                                @foreach($products as $product)
                                    @php
                                        $images     = is_array($product->images) ? $product->images : json_decode($product->images, true);
                                        $firstImage = $images[0] ?? 'default.png';

                                        $sabcategory = $product->firstSubcategory;

                                        $productUrl = ($sabcategory && $sabcategory->slug && $product->slug)
                                            ? route('product.details', [
                                                'cat'    => $category->slug,
                                                'sabcat' => $sabcategory->slug,
                                                'slug'   => $product->slug,
                                              ])
                                            : '#';

                                        $isInWishlist = \App\Models\Wishlist::where('user_id', auth()->id() ?? 0)
                                                            ->where('product_id', $product->id)->exists();

                                        $hasOffer = !empty($product->offer_price);

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
                            </div>{{-- /gridLayout --}}

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

        @include('components.frontend.footer')
    </div>

    <div class="offcanvas offcanvas-start canvas-mb" id="mobileMenu">
        <span class="icon-close-popup" data-bs-dismiss="offcanvas"><i class="icon-close"></i></span>
        <div class="canvas-header">
            <p class="text-logo-mb"><img src="{{ asset('images/logo/logo.webp') }}"></p>
        </div>
    </div>

    @include('components.frontend.main-js')

<script>
(function () {

    var PRICE_MIN = {{ $minPrice }};
    var PRICE_MAX = {{ $maxPrice }};
    var currentMin = PRICE_MIN;
    var currentMax = PRICE_MAX;

    function loadProducts(page) {
        page = parseInt(page) || 1;

        var availability  = $("input[name='availability']:checked").first().val() || '';
        var fragrance_ids = $("input[name='fragrance[]']:checked").map(function () { return $(this).val(); }).get();
        var units         = $("input[name='units[]']:checked").map(function () { return $(this).val(); }).get();
        var product_types = $("input[name='product_types[]']:checked").map(function () { return $(this).val(); }).get();
        var sort          = $(".tf-dropdown-sort .select-item.active").data("sort-value") || 'best-selling';

        $.ajax({
            url: "{{ route('category.filter') }}",
            type: "POST",
            data: {
                _token:        "{{ csrf_token() }}",
                category_id:   "{{ $category->id }}",
                availability:  availability,
                fragrance_ids: fragrance_ids,
                units:         units,
                product_types: product_types,
                min_price:     currentMin,
                max_price:     currentMax,
                sort:          sort,
                page:          page
            },
            success: function (res) {
                $("#gridLayout").html(res.html);
                $(".wg-pagination").html(res.pagination);
                $('html, body').animate({ scrollTop: $("#gridLayout").offset().top - 100 }, 400);
            }
        });
    }

    $(window).on('load', function () {

        var sliderEl = document.getElementById('price-value-range');

        if (sliderEl && typeof noUiSlider !== 'undefined') {

            if (sliderEl.noUiSlider) { sliderEl.noUiSlider.destroy(); }

            noUiSlider.create(sliderEl, {
                start:   [PRICE_MIN, PRICE_MAX],
                connect: true,
                range:   { min: PRICE_MIN, max: PRICE_MAX },
                format:  { to: function (v) { return Math.round(v); }, from: function (v) { return Math.round(v); } }
            });

            sliderEl.noUiSlider.on('update', function (values) {
                currentMin = parseInt(values[0]);
                currentMax = parseInt(values[1]);
                $('#price-min-value').text(currentMin);
                $('#price-max-value').text(currentMax);
            });

            sliderEl.noUiSlider.on('change', function (values) {
                currentMin = parseInt(values[0]);
                currentMax = parseInt(values[1]);
                loadProducts(1);
            });

        } else if (sliderEl) {
            console.warn('noUiSlider is not loaded. Price slider will not work.');
        }

        loadProducts(1);
    });

    $(document).on('change',
        "input[name='availability'], input[name='fragrance[]'], input[name='units[]'], input[name='product_types[]']",
        function () { loadProducts(1); }
    );

    $(document).on('click', '.tf-dropdown-sort .select-item', function () {
        $('.tf-dropdown-sort .select-item').removeClass('active');
        $(this).addClass('active');
        loadProducts(1);
    });

    $(document).on('click', '.wg-pagination a', function (e) {
        e.preventDefault();
        var match = ($(this).attr('href') || '').match(/page=(\d+)/);
        loadProducts(match ? match[1] : 1);
    });

    // Reset — covers BOTH desktop + mobile buttons
    $(document).on('click', '#reset-filter, #reset-filter-desktop', function () {
        $("input[name='availability'], input[name='fragrance[]'], input[name='units[]'], input[name='product_types[]']")
            .prop('checked', false);

        currentMin = PRICE_MIN;
        currentMax = PRICE_MAX;
        $('#price-min-value').text(PRICE_MIN);
        $('#price-max-value').text(PRICE_MAX);

        var sliderEl = document.getElementById('price-value-range');
        if (sliderEl && sliderEl.noUiSlider) {
            sliderEl.noUiSlider.set([PRICE_MIN, PRICE_MAX]);
        }

        $('.tf-dropdown-sort .select-item').removeClass('active');
        $('.tf-dropdown-sort .select-item[data-sort-value="best-selling"]').addClass('active');

        loadProducts(1);
    });

    $(document).on('click', '.tf-view-layout-switch', function () {
        $('.tf-view-layout-switch').removeClass('active');
        $(this).addClass('active');
        $('.wrapper-shop').removeClass('tf-col-2 tf-col-3').addClass($(this).data('value-layout'));
    });

    $(document).on('click', '.wishlist-btn', function (e) {
        e.preventDefault();
        var form  = $(this).closest('form');
        var icon  = form.find('.wishlist-icon');
        var tip   = form.find('.tooltip');

        $.ajax({
            url: "{{ route('wishlist.add') }}", method: 'POST',
            data: { _token: form.find('[name="_token"]').val(), product_id: form.data('product') },
            success: function (res) {
                if (res.status === 'added') {
                    notyf.open({ type: 'custom-success', message: res.message || 'Added to wishlist' });
                    icon.removeClass('icon-heart').addClass('icon-trash');
                    tip.text('Remove from Wishlist');
                } else {
                    notyf.error(res.message || 'Removed from wishlist');
                    icon.removeClass('icon-trash').addClass('icon-heart');
                    tip.text('Add to Wishlist');
                }
                if (res.count !== undefined) $('.wishlist-count').text(res.count);
            }
        });
    });

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

}());
</script>
</body>
</html>