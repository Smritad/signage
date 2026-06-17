<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
    @include('components.frontend.head')
</head>

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
                <h1 class="title-page">{{ $fragrance->title }}</h1>
                <ul class="breadcrumbs-page">
                    <li><a href="{{ url('/') }}" class="h6 link">Home</a></li>
                    <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                    <li><h6 class="current-page fw-normal">{{ $fragrance->title }}</h6></li>
                </ul>
            </div>
        </div>
    </section>

    {{-- Hidden: pass PHP values to JS --}}
    <div id="page-meta"
         data-fragrance-id="{{ $fragrance->id }}"
         data-price-min="{{ $minPrice }}"
         data-price-max="{{ $maxPrice }}"
         data-filter-url="{{ route('frgrance.filter') }}"
         data-csrf="{{ csrf_token() }}"
         style="display:none;"></div>

    @php
        use Illuminate\Support\Facades\Auth;
        $userId    = Auth::guard('custom')->check() ? Auth::guard('custom')->id() : null;
        $sessionId = $userId ? null : session()->getId();
    @endphp

    <div class="flat-spacing">
        <div class="container">
            <div class="row">

                {{-- ═══ SIDEBAR ═══ --}}
                <div class="col-xl-3">
                    <div class="canvas-sidebar sidebar-filter canvas-filter left">
                        <div class="canvas-wrapper">
                            <div class="canvas-header d-xl-none">
                                <span class="title h3 fw-medium">Filter</span>
                                <span class="icon-close link icon-close-popup fs-24 close-filter"></span>
                            </div>

                            <div class="canvas-body">

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
                                                <input type="checkbox" class="tf-check availability-check" id="inStock" value="in">
                                                <label for="inStock" class="label">
                                                    <span>In Stock</span>
                                                    <span class="count">{{ $inStockCount }}</span>
                                                </label>
                                            </li>
                                            <li class="list-item {{ $outStockCount == 0 ? 'disabled' : '' }}">
                                                <input type="checkbox" class="tf-check availability-check" id="outStock" value="out">
                                                <label for="outStock" class="label">
                                                    <span>Out of Stock</span>
                                                    <span class="count">{{ $outStockCount }}</span>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                {{-- Perfume Notes --}}
                                <div class="widget-facet">
                                    <div class="facet-title" data-bs-target="#fragrance-filter" role="button"
                                         data-bs-toggle="collapse" aria-expanded="true" aria-controls="fragrance-filter">
                                        <span class="h4 fw-semibold">Perfume Notes</span>
                                        <span class="icon icon-caret-down fs-20"></span>
                                    </div>
                                    <div id="fragrance-filter" class="collapse show">
                                        <ul class="collapse-body filter-group-check current-scrollbar">
                                            @foreach($fragranceTypes as $ft)
                                                <li class="list-item {{ ($fragranceCounts[$ft->id] ?? 0) == 0 ? 'disabled' : '' }}">
                                                    <input type="checkbox" class="tf-check fragrance-check"
                                                           id="fragrance_{{ $ft->id }}" value="{{ $ft->id }}"
                                                           {{ $ft->id == $fragrance->id ? 'checked' : '' }}>
                                                    <label for="fragrance_{{ $ft->id }}" class="label">
                                                        <span>{{ $ft->title }}</span>
                                                        <span class="count">{{ $fragranceCounts[$ft->id] ?? 0 }}</span>
                                                    </label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                {{-- Size --}}
                                @if(!empty($units))
                                <div class="widget-facet">
                                    <div class="facet-title" data-bs-target="#size-filter" role="button"
                                         data-bs-toggle="collapse" aria-expanded="true" aria-controls="size-filter">
                                        <span class="h4 fw-semibold">Size</span>
                                        <span class="icon icon-caret-down fs-20"></span>
                                    </div>
                                    <div id="size-filter" class="collapse show">
                                        <ul class="collapse-body filter-group-check current-scrollbar">
                                            @foreach($units as $unit)
                                                <li class="list-item">
                                                    <input type="checkbox" class="tf-check unit-check"
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

                               {{-- Price --}}
                                <div class="widget-facet">
                                    <div class="facet-title" data-bs-target="#price-filter" role="button"
                                         data-bs-toggle="collapse" aria-expanded="true" aria-controls="price-filter">
                                        <span class="h4 fw-semibold">Price</span>
                                        <span class="icon icon-caret-down fs-20"></span>
                                    </div>
                                    <div id="price-filter" class="collapse show">
                                        <div class="collapse-body widget-price filter-price">
                                            <div id="price-slider"
                                                 data-min="{{ $minPrice }}"
                                                 data-max="{{ $maxPrice }}"></div>
                                            <div class="box-value-price mt-2">
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

                                {{-- Desktop reset button --}}
                                <div class="mt-3 d-none d-xl-block">
                                    <button id="reset-filter-desktop" class="tf-btn btn-reset w-100">Reset Filters</button>
                                </div>

                            </div>{{-- /canvas-body --}}

                            <div class="canvas-bottom d-xl-none">
                                <button id="reset-filter" class="tf-btn btn-reset">Reset Filters</button>
                            </div>

                            

                           
                        </div>
                    </div>
                </div>

                {{-- ═══ PRODUCTS ═══ --}}
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
                                    <!--<div class="select-item" data-sort-value="a-z"><span class="text-value-item">Alphabetically, A-Z</span></div>-->
                                    <!--<div class="select-item" data-sort-value="z-a"><span class="text-value-item">Alphabetically, Z-A</span></div>-->
                                    <div class="select-item" data-sort-value="price-low-high"><span class="text-value-item">Price, low to high</span></div>
                                    <div class="select-item" data-sort-value="price-high-low"><span class="text-value-item">Price, high to low</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="wrapper-shop tf-grid-layout tf-col-3" id="gridLayout">
                        {{-- filled by AJAX on load --}}
                    </div>

                    <div class="wg-pagination-wrap mt-3"></div>
                </div>

            </div>
        </div>
    </div>

    @include('components.frontend.footer')
</div>

<div class="offcanvas offcanvas-start canvas-mb" id="mobileMenu">
    <span class="icon-close-popup" data-bs-dismiss="offcanvas"><i class="icon-close"></i></span>
    <div class="canvas-header">
        <p class="text-logo-mb"><img src="images/logo/logo.webp"></p>
    </div>
</div>

@include('components.frontend.main-js')

<script>
(function () {
    'use strict';

    /* ── read page-level config ── */
    var meta       = document.getElementById('page-meta');
    var FILTER_URL = meta.dataset.filterUrl;
    var CSRF       = meta.dataset.csrf;
    var PRICE_MIN  = parseInt(meta.dataset.priceMin) || 0;
    var PRICE_MAX  = parseInt(meta.dataset.priceMax) || 0;

    var currentPage    = 1;
    var priceMinVal    = PRICE_MIN;
    var priceMaxVal    = PRICE_MAX;
    var sliderReady    = false;

    /* ══════════════════════════════════════
       PRICE SLIDER
    ══════════════════════════════════════ */
    var sliderEl = document.getElementById('price-slider');
    if (sliderEl && PRICE_MIN < PRICE_MAX) {
        noUiSlider.create(sliderEl, {
            start   : [PRICE_MIN, PRICE_MAX],
            connect : true,
            range   : { min: PRICE_MIN, max: PRICE_MAX },
            format  : { to: function(v){ return Math.round(v); }, from: function(v){ return parseInt(v); } }
        });

        sliderEl.noUiSlider.on('update', function (vals) {
            document.getElementById('price-min-value').textContent = vals[0];
            document.getElementById('price-max-value').textContent = vals[1];
            priceMinVal = parseInt(vals[0]);
            priceMaxVal = parseInt(vals[1]);
        });

        sliderEl.noUiSlider.on('change', function () {
            loadProducts(1);
        });

        sliderReady = true;
    }

    /* ══════════════════════════════════════
       HELPERS
    ══════════════════════════════════════ */
    function getAvailability() {
        var checked = document.querySelectorAll('.availability-check:checked');
        return checked.length ? checked[0].value : '';   // 'in', 'out', or ''
    }

    function getCheckedValues(selector) {
        var vals = [];
        document.querySelectorAll(selector + ':checked').forEach(function (el) { vals.push(el.value); });
        return vals;
    }

    function getSort() {
        var active = document.querySelector('.tf-dropdown-sort .select-item.active');
        return active ? (active.dataset.sortValue || 'best-selling') : 'best-selling';
    }

    /* ══════════════════════════════════════
       LOAD PRODUCTS
    ══════════════════════════════════════ */
    function loadProducts(page) {
        currentPage = parseInt(page) || 1;

        var grid = document.getElementById('gridLayout');
        grid.classList.add('loading');

        var fragIds = getCheckedValues('.fragrance-check');
        var units   = getCheckedValues('.unit-check');
        var avail   = getAvailability();
        var sort    = getSort();

        // Build POST data manually so arrays serialize correctly for jQuery
        $.ajax({
            url  : FILTER_URL,
            type : 'POST',
            data : {
                _token       : CSRF,
                availability : avail,
                fragrance_ids: fragIds,
                units        : units,
                min_price    : priceMinVal,
                max_price    : priceMaxVal,
                sort         : sort,
                page         : currentPage
            },
            success: function (res) {
                grid.classList.remove('loading');
                grid.innerHTML = res.html || '';
                document.querySelector('.wg-pagination-wrap').innerHTML = res.pagination || '';
                $('html, body').animate({ scrollTop: $(grid).offset().top - 100 }, 400);
            },
            error: function (xhr) {
                grid.classList.remove('loading');
                console.error('Filter AJAX error', xhr.status, xhr.responseText);
                grid.innerHTML = '<div class="text-center py-5 w-100"><h5 class="text-danger">Error loading products. Please try again.</h5></div>';
            }
        });
    }

    /* ══════════════════════════════════════
       EVENT LISTENERS
    ══════════════════════════════════════ */

    // Availability: mutual-exclusion checkboxes
    $(document).on('change', '.availability-check', function () {
        var self = this;
        document.querySelectorAll('.availability-check').forEach(function (el) {
            if (el !== self) el.checked = false;
        });
        loadProducts(1);
    });

    // Fragrance & size checkboxes
    $(document).on('change', '.fragrance-check, .unit-check', function () {
        loadProducts(1);
    });

    // Sort dropdown
    $(document).on('click', '.select-item', function () {
        $('.select-item').removeClass('active');
        $(this).addClass('active');
        $('.text-sort-value').text($(this).find('.text-value-item').text());
        loadProducts(1);
    });

    // Pagination (delegated – HTML replaced by AJAX)
    $(document).on('click', '.wg-pagination-wrap a', function (e) {
        e.preventDefault();
        var href = $(this).attr('href') || '';
        var match = href.match(/page=(\d+)/);
        loadProducts(match ? parseInt(match[1]) : 1);
    });

    // Layout switch
    $(document).on('click', '.tf-view-layout-switch', function () {
        $('.tf-view-layout-switch').removeClass('active');
        $(this).addClass('active');
        $('.wrapper-shop').removeClass('tf-col-2 tf-col-3').addClass($(this).data('value-layout'));
    });

    // Reset
   // Reset (covers both mobile + desktop buttons)
    $(document).on('click', '#reset-filter, #reset-filter-desktop', function () {
        document.querySelectorAll('.availability-check, .fragrance-check, .unit-check').forEach(function (el) {
            el.checked = false;
        });

        // Re-check the current page's fragrance by default
        // ↓↓↓ DELETE these 3 lines if you want a FULL clear (no fragrance checked) ↓↓↓
        var defaultFrag = meta.dataset.fragranceId;
        var defaultEl   = document.getElementById('fragrance_' + defaultFrag);
        if (defaultEl) defaultEl.checked = true;
        // ↑↑↑

        if (sliderReady) {
            sliderEl.noUiSlider.reset();
            priceMinVal = PRICE_MIN;
            priceMaxVal = PRICE_MAX;
        } else {
            priceMinVal = PRICE_MIN;
            priceMaxVal = PRICE_MAX;
        }

        $('.select-item').removeClass('active');
        $('.select-item[data-sort-value="best-selling"]').addClass('active');
        $('.text-sort-value').text('Best Selling');

        loadProducts(1);
    });

    /* ── initial load ── */
    loadProducts(1);

}());
</script>

{{-- Cart AJAX --}}
<script>
$(document).on('submit', '.add-to-cart-form', function (e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr('action'), method: 'POST', data: $(this).serialize(),
        success: function (res) {
            if (res.success) {
                notyf.open({ type: 'custom-success', message: res.message });
                if (res.cart_count !== undefined) $('#cart-count').text(res.cart_count);
                setTimeout(function () { location.reload(); }, 800);
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
    var form    = $(this).closest('form');
    var icon    = form.find('.wishlist-icon');
    var tooltip = form.find('.tooltip');
    $.ajax({
        url: "{{ route('wishlist.add') }}", method: 'POST',
        data: { _token: form.find('[name="_token"]').val(), product_id: form.data('product') },
        success: function (r) {
            if (r.status === 'added') {
                notyf.open({ type: 'custom-success', message: r.message || 'Added to wishlist' });
                icon.removeClass('icon-heart').addClass('icon-trash');
                tooltip.text('Remove from Wishlist');
            } else {
                notyf.error(r.message || 'Removed from wishlist');
                icon.removeClass('icon-trash').addClass('icon-heart');
                tooltip.text('Add to Wishlist');
            }
            if (r.count !== undefined) $('.wishlist-count').text(r.count);
        }
    });
});
</script>

</body>
</html>