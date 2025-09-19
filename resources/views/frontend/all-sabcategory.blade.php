
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
        @include('components.frontend.head')
</head>

<body>
    <button id="goTop">
        <span class="border-progress"></span>
        <span class="icon icon-caret-up"></span>
    </button>
    <div class="preload preload-container" id="preload">
        <div class="preload-logo">
            <div class="spinner"></div>
        </div>
    </div>
    <div id="wrapper">
       @include('components.frontend.header')
        <!-- /Header -->
        <!-- Page Title -->
       {{-- Page Title & Breadcrumb --}}
            <section class="s-page-title">
                <div class="container">
                    <div class="content">
                        <h1 class="title-page">{{ $sabcategory->sab_category_name }}</h1>
                        <ul class="breadcrumbs-page">
                            <li><a href="{{ url('/') }}" class="h6 link">Home</a></li>
                            <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                            <li>
                                <h6 class="current-page fw-normal">{{ $sabcategory->sab_category_name }}</h6>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

        <!-- /Page Title -->
        <!-- Section Product -->
        <div class="flat-spacing">
            <div class="container">
                <div class="row">
                    <div class="col-xl-3">
                        <div class="canvas-sidebar sidebar-filter canvas-filter left">
                           <div class="canvas-wrapper">
                        <div class="canvas-header d-xl-none">
                            <span class="title h3 fw-medium">Filter</span>
                            <span class="icon-close link icon-close-popup fs-24 close-filter"></span>
                        </div>

                        <div class="canvas-body">

                            {{-- Category Filter --}}
                            <div class="widget-facet">
                                <div class="facet-title" data-bs-target="#category" role="button"
                                    data-bs-toggle="collapse" aria-expanded="true" aria-controls="category">
                                    <span class="h4 fw-semibold">Sab Category</span>
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
                                            <input type="radio" name="availability" class="tf-check" id="inStock">
                                            <label for="inStock" class="label">
                                                <span>In Stock</span>
                                                <span class="count">{{ $inStockCount }}</span>
                                            </label>
                                        </li>
                                        <li class="list-item {{ $outStockCount == 0 ? 'disabled' : '' }}">
                                            <input type="radio" name="availability" class="tf-check" id="outStock">
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
                                <div class="facet-title" data-bs-target="#fragrance" role="button"
                                    data-bs-toggle="collapse" aria-expanded="true" aria-controls="fragrance">
                                    <span class="h4 fw-semibold">Perfume Notes</span>
                                    <span class="icon icon-caret-down fs-20"></span>
                                </div>
                                <div id="fragrance" class="collapse show">
                                    <ul class="collapse-body filter-group-check current-scrollbar">
                                        @foreach($fragranceTypes as $ft)
                                            <li class="list-item {{ ($fragranceCounts[$ft->id] ?? 0) == 0 ? 'disabled' : '' }}">
                                                <input type="radio" class="tf-check" id="fragrance_{{ $ft->id }}">
                                                <label for="fragrance_{{ $ft->id }}" class="label">
                                                    <span>{{ $ft->title }}</span>
                                                    <span class="count">{{ $fragranceCounts[$ft->id] ?? 0 }}</span>
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                        {{-- Price Range --}}
                        <div class="widget-facet">
                            <div class="facet-title" data-bs-target="#price" role="button"
                                data-bs-toggle="collapse" aria-expanded="true" aria-controls="price">
                                <span class="h4 fw-semibold">Price</span>
                                <span class="icon icon-caret-down fs-20"></span>
                            </div>
                            <div id="price" class="collapse show">
                                <div class="collapse-body widget-price filter-price">
                                    <div class="price-val-range"
                                        id="price-value-range"
                                        data-min="{{ $minPrice ?? 0 }}"
                                        data-max="{{ $maxPrice ?? 0 }}">
                                    </div>
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


                            </div>

                            <div class="canvas-bottom d-xl-none">
                                <button id="reset-filter" class="tf-btn btn-reset">Reset Filters</button>
                            </div>
                        </div>

                        </div>
                    </div>
                    <div class="col-xl-9">
                        <div class="tf-shop-control">
                            <div class="shop-sale-text d-none d-xl-flex">
                                <input type="checkbox" name="sale" class="tf-check" id="sale">
                                <label for="sale" class="label">Show only products on sale</label>
                            </div>
                            <div class="tf-control-filter d-xl-none">
                                <button type="button" id="filterShop" class="tf-btn-filter">
                                    <span class="icon icon-filter"></span><span class="text">Filter</span>
                                </button>
                            </div>
                            <ul class="tf-control-layout">
                                <li class="tf-view-layout-switch sw-layout-2" data-value-layout="tf-col-2">
                                    <i class="icon-grid-2"></i>
                                </li>
                                <li class="tf-view-layout-switch sw-layout-3 active d-none d-md-flex"
                                    data-value-layout="tf-col-3">
                                    <i class="icon-grid-3"></i>
                                </li>

                            </ul>
                            <div class="tf-control-sorting">
                                <p class="h6 d-none d-lg-block">Sort by:</p>
                                <div class="tf-dropdown-sort" data-bs-toggle="dropdown">
                                    <div class="btn-select">
                                        <span class="text-sort-value">Best Selling</span>
                                        <span class="icon icon-caret-down"></span>
                                    </div>
                                    <div class="dropdown-menu">
                                        <div class="select-item active remove-all-filters"
                                            data-sort-value="best-selling">
                                            <span class="text-value-item">Best Selling</span>
                                        </div>
                                        <div class="select-item" data-sort-value="a-z">
                                            <span class="text-value-item">Alphabetically, A-Z</span>
                                        </div>
                                        <div class="select-item" data-sort-value="z-a">
                                            <span class="text-value-item">Alphabetically, Z-A</span>
                                        </div>
                                        <div class="select-item" data-sort-value="price-low-high">
                                            <span class="text-value-item">Price, low to high</span>
                                        </div>
                                        <div class="select-item" data-sort-value="price-high-low">
                                            <span class="text-value-item">Price, high to low</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="wrapper-control-shop gridLayout-wrapper">
                            <div class="meta-filter-shop">
                                <div id="product-count-grid" class="count-text"></div>
                                <div id="product-count-list" class="count-text"></div>
                                <div id="applied-filters"></div>
                                <button id="remove-all" class="remove-all-filters" style="display: none;">
                                    <i class="icon icon-close"></i>
                                    Clear all</button>
                            </div>
                            <div class="tf-list-layout wrapper-shop" id="listLayout" style="display: none;">
                                <!-- Product 1 -->
                                <div class="card-product product-style_list" data-brand="automet">
                                    <div class="card-product_wrapper">
                                        <a href="product-detail.html" class="product-img">
                                            <img class="lazyload img-product" src="images/products/product-21.jpg"
                                                data-src="images/products/product-21.jpg" alt="Product">
                                            <img class=" lazyload img-hover" src="images/products/product-22.jpg"
                                                data-src="images/products/product-22.jpg" alt="Product">
                                        </a>
                                        <ul class="product-action_list">
                                            <li class="">
                                                <a href="#compare" data-bs-toggle="offcanvas"
                                                    class="hover-tooltip tooltip-left box-icon ">
                                                    <span class="icon icon-compare"></span>
                                                    <span class="tooltip">Compare</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#quickView" data-bs-toggle="modal"
                                                    class="hover-tooltip tooltip-left box-icon">
                                                    <span class="icon icon-view"></span>
                                                    <span class="tooltip">Quick view</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="product-countdown">
                                            <div class="js-countdown cd-has-zero" data-timer="25472"
                                                data-labels="d : ,h : ,m : ,s"></div>
                                        </div>
                                        <ul class="product-badge_list">
                                            <li class="product-badge_item h6 hot">Hot</li>
                                        </ul>
                                    </div>
                                    <div class="card-product_info">
                                        <div class="product-info_list">
                                            <a href="product-detail.html" class="name-product h3 link">SANDAL WOOD - EAU
                                                DE Parfum</a>
                                            <div class="price-wrap">
                                                <span class="price-old h6 fw-normal">Rs.1099</span>
                                                <span class="price-new h6">Rs.1799</span>
                                            </div>
                                            <ul class="product-color_list">
                                                <li
                                                    class="product-color-item color-swatch hover-tooltip tooltip-bot active">
                                                    <span class="tooltip color-filter">Dark</span>
                                                    <span class="swatch-value bg-dark-charcoal"></span>
                                                    <img src="images/products/product-21.jpg"
                                                        data-src="images/products/product-21.jpg" alt="Product">

                                                </li>
                                            </ul>
                                            <div class="product-desc_list d-none d-sm-grid">
                                                <p class="product-desc">
                                                    <span class="headline fw-bold">Contents:</span> Super soft and comfy
                                                    fabric, skin-friendly and
                                                    breathable.
                                                    Womens
                                                    tops dressy casual,
                                                    round neck
                                                    cute lightweight tops，loose fit basic tees
                                                </p>
                                                <p class="product-desc d-none d-md-block">
                                                    <span class="headline fw-bold">Details:</span> Warm up or cool down
                                                    with this essential 3/4 sleeve
                                                    t
                                                    shirts,
                                                    featured
                                                    in an loose fit
                                                    and Pleated sleeve design with sew seaming front for a lived-in
                                                    look.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="product-action_list">
                                            <span class="h6">To buy, select <span
                                                    class="fw-bold text-black">size</span></span>
                                            <div class="group-btn">
                                                <a href="#shoppingCart" data-bs-toggle="offcanvas"
                                                    class="tf-btn animate-btn">
                                                    Add to Cart
                                                    <i class="icon icon-shopping-cart-simple"></i>
                                                </a>
                                                <a href="#" class="tf-btn style-line btn-add-wishlist2">
                                                    <span class="text">Add to List</span>
                                                    <i class="icon icon-heart"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pagination -->
                                <div class="wd-full wg-pagination">
                                    <a href="#" class="pagination-item h6 direct"><i
                                            class="icon icon-caret-left"></i></a>
                                    <a href="#" class="pagination-item h6">1</a>
                                    <span class="pagination-item h6 active">2</span>
                                    <a href="#" class="pagination-item h6">3</a>
                                    <a href="#" class="pagination-item h6">4</a>
                                    <a href="#" class="pagination-item h6">5</a>
                                    <a href="#" class="pagination-item h6 direct"><i
                                            class="icon icon-caret-right"></i></a>
                                </div>
                            </div>
                            <div class="wrapper-shop tf-grid-layout tf-col-3" id="gridLayout">
                               {{-- Subcategory Products Loop --}}
@foreach($products as $product)
    @php
        $images = json_decode($product->images, true);
        $firstImage = !empty($images) ? $images[0] : 'default.png';

        // Generate URL with category + subcategory slug
        $productUrl = route('product.details', [
            'cat' => $category->slug,
            'sabcat' => $sabcategory->slug,
            'slug' => $product->slug
        ]);
    @endphp

    <div class="card-product grid">
        <div class="card-product_wrapper">
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
                <form class="add-to-cart-form d-inline" method="POST" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button type="submit" class="hover-tooltip tooltip-left box-icon">
                        <span class="icon icon-shopping-cart-simple"></span>
                        <span class="tooltip">Add to cart</span>
                    </button>
                </form>
                </li>
                <li class="wishlist">
    @php
        $isInWishlist = \App\Models\Wishlist::where('user_id', auth()->id() ?? 0)
                                             ->where('product_id', $product->id)
                                             ->exists();
    @endphp

    <form class="add-to-wishlist-form" data-product="{{ $product->id }}">
        @csrf
        <button type="button" class="hover-tooltip tooltip-left box-icon wishlist-btn">
            <span class="icon {{ $isInWishlist ? 'icon-heart-filled' : 'icon-heart' }}"></span>
            <span class="tooltip">
                {{ $isInWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}
            </span>
        </button>
    </form>
</li>
                <li>
                    <a href="#quickView" data-bs-toggle="modal"
                        class="hover-tooltip tooltip-left box-icon">
                        <span class="icon icon-view"></span>
                        <span class="tooltip">Quick view</span>
                    </a>
                </li>
            </ul>

            @if($product->is_new ?? false)
                <ul class="product-badge_list">
                    <li class="product-badge_item h6 new">New arrival</li>
                </ul>
            @endif
        </div>

        <div class="card-product_info">
            <a href="{{ $productUrl }}" class="name-product h4 link">{{ $product->product_name }}</a>
            <div class="price-wrap">
                @if($product->offer_price)
                    <span class="price-old h6 fw-normal">Rs.{{ number_format($product->price, 2) }}</span>
                    <span class="price-new h6">Rs.{{ number_format($product->offer_price, 2) }}</span>
                @else
                    <span class="price-new h6">Rs.{{ number_format($product->price, 2) }}</span>
                @endif
            </div>
        </div>
    </div>
@endforeach



                                <!-- Pagination -->
                                <div class="wd-full wg-pagination m-0 justify-content-center">
                                    <a href="#" class="pagination-item h6 direct"><i
                                            class="icon icon-caret-left"></i></a>
                                    <a href="#" class="pagination-item h6">1</a>
                                    <span class="pagination-item h6 active">2</span>
                                    <a href="#" class="pagination-item h6">3</a>
                                    <a href="#" class="pagination-item h6">4</a>
                                    <a href="#" class="pagination-item h6">5</a>
                                    <a href="#" class="pagination-item h6 direct"><i
                                            class="icon icon-caret-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Section Product -->
        <!-- Footer -->
        <footer class="tf-footer style-color-white style-4 bg-black">
            <div class="footer-body">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-3 col-sm-6 mb_30 mb-xl-0">
                            <div class="footer-col-block">
                                <p class="footer-heading footer-heading-mobile">Contact us</p>
                                <div class="tf-collapse-content">
                                    <ul class="footer-contact">
                                        <li>
                                            <i class="icon icon-map-pin"></i>
                                            <span class="br-line"></span>
                                            <a href="https://www.google.com/maps?q=8500+Lorem+Street+Chicago,+IL+55030+Dolor+sit+amet"
                                                target="_blank" class="h6 link">
                                                8500 Lorem Street Chicago, IL 55030 <br class="d-none d-lg-block"> Dolor
                                                sit amet
                                            </a>
                                        </li>
                                        <li>
                                            <i class="icon icon-phone"></i>
                                            <span class="br-line"></span>
                                            <a href="tel:+88001234567" class="h6 link">+8(800) 123 4567</a>
                                        </li>
                                        <li>
                                            <i class="icon icon-envelope-simple"></i>
                                            <span class="br-line"></span>
                                            <a href="mailto:info@domainname.com"
                                                class="h6 link">info@domainname.com</a>
                                        </li>
                                    </ul>
                                    <div class="social-wrap">
                                        <ul class="tf-social-icon style-2">
                                            <li>
                                                <a href="https://www.facebook.com/" target="_blank"
                                                    class="social-facebook">
                                                    <span class="icon"><i class="icon-fb"></i></span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://www.instagram.com/" target="_blank"
                                                    class="social-instagram">
                                                    <span class="icon"><i class="icon-instagram-logo"></i></span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://x.com/" target="_blank" class="social-x">
                                                    <span class="icon"><i class="icon-x"></i></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-sm-6 mb_30 mb-xl-0">
                            <div class="footer-col-block footer-wrap-1 ms-xl-auto">
                                <p class="footer-heading footer-heading-mobile">Shopping</p>
                                <div class="tf-collapse-content">
                                    <ul class="footer-menu-list">
                                        <li><a href="#" class="link h6">Shipping</a></li>
                                        <li><a href="#" class="link h6">Shop by Brand</a></li>
                                        <li><a href="#" class="link h6">Track order</a></li>
                                        <li><a href="#" class="link h6">Terms & Conditions</a></li>
                                        <li><a href="#" class="link h6">My Wishlist</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 mb_30 mb-sm-0">
                            <div class="footer-col-block footer-wrap-2 mx-xl-auto">
                                <p class="footer-heading footer-heading-mobile">Information</p>
                                <div class="tf-collapse-content">
                                    <ul class="footer-menu-list">
                                        <li><a href="#" class="link h6">About Us</a></li>
                                        <li><a href="#" class="link h6">Term & Policy</a></li>
                                        <li><a href="#" class="link h6">Help Center</a></li>
                                        <li><a href="#" class="link h6">Refunds</a></li>
                                        <li><a href="#" class="link h6">Careers</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-sm-6">
                            <div class="footer-col-block">
                                <p class="footer-heading footer-heading-mobile">Let’s keep in touch</p>
                                <div class="tf-collapse-content">
                                    <div class="footer-newsletter">
                                        <p class="h6 caption text-main-5">
                                            Enter your email below to be the first to know about new collections and
                                            product launches.
                                        </p>
                                        <form class="form_sub has_check" id="subscribe-form">
                                            <div class="f-content" id="subscribe-content">
                                                <fieldset class="col">
                                                    <input class="style-stroke-2" id="subscribe-email" type="email"
                                                        name="email-form" placeholder="Enter your email" required>
                                                </fieldset>
                                                <button id="subscribe-button" type="button"
                                                    class="tf-btn btn-white animate-btn animate-dark type-small-2">
                                                    Subscribe
                                                    <i class="icon icon-arrow-right"></i>
                                                </button>
                                            </div>
                                            <div class="checkbox-wrap">
                                                <input id="remember" type="checkbox"
                                                    class="tf-check style-3 style-white">
                                                <label for="remember" class="h6 text-main-5">
                                                    By clicking subcribe, you agree to the  
                                                    <a href="#" class="text-decoration-underline link text-main-5">Terms
                                                        of Service</a> and <a href="#"
                                                        class="text-decoration-underline link text-main-5">
                                                        Privacy Policy</a>.
                                                </label>
                                            </div>
                                            <div id="subscribe-msg"></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="container">
                    <div class="inner-bottom">

                        <p class="h6">Copyright © 2025 Signage Wellness. All rights reserved. Designed By <a
                                class="link" href="https://www.matrixbricks.com/" target="_blank">Matrix Bricks</a></p>

                        <div class="list-hor flex-wrap">
                            <span class="h6">Payment:</span>
                            <ul class="payment-method-list">
                                <li><img src="images/payment/visa-2.svg" alt="Payment"></li>
                                <li><img src="images/payment/master-card-2.svg" alt="Payment"></li>
                                <li><img src="images/payment/amex-2.svg" alt="Payment"></li>
                                <li><img src="images/payment/discover-2.svg" alt="Payment"></li>
                                <li><img src="images/payment/paypal-2.svg" alt="Payment"></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- /Footer -->
    </div>

    <!-- Mobile Menu -->
    <div class="offcanvas offcanvas-start canvas-mb" id="mobileMenu">
        <span class="icon-close-popup" data-bs-dismiss="offcanvas">
            <i class="icon-close"></i>
        </span>
        <div class="canvas-header">
            <p class="text-logo-mb"><img src="images/logo/logo.webp" data-src="images/logo/logo.webp"></p>
            <a href="#" class="tf-btn type-small style-2">
                Login
                <i class="icon icon-user"></i>
            </a>
            <span class="br-line"></span>
        </div>
        <div class="canvas-body">
            <div class="mb-content-top">
                <ul class="nav-ul-mb" id="wrapper-menu-navigation"></ul>
            </div>
            <div class="group-btn">
                <a href="#" class="tf-btn type-small style-2">
                    Wishlist
                    <i class="icon icon-heart"></i>
                </a>
                <div data-bs-dismiss="offcanvas">
                    <a href="#" data-bs-toggle="modal" class="tf-btn type-small style-2">
                        Search
                        <i class="icon icon-magnifying-glass"></i>
                    </a>
                </div>
            </div>
            <div class="flow-us-wrap">
                <h5 class="title">Follow us on</h5>
                <ul class="tf-social-icon">
                    <li>
                        <a href="https://www.facebook.com/" target="_blank" class="social-facebook">
                            <span class="icon"><i class="icon-fb"></i></span>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.instagram.com/" target="_blank" class="social-instagram">
                            <span class="icon"><i class="icon-instagram-logo"></i></span>
                        </a>
                    </li>
                    <li>
                        <a href="https://x.com/" target="_blank" class="social-x">
                            <span class="icon"><i class="icon-x"></i></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Mobile Menu -->

    <!-- Javascript -->
       @include('components.frontend.main-js')
<script>
    $('.add-to-cart-form').on('submit', function(e){
    e.preventDefault();
    let form = $(this);
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        success: function(res){
            if(res.success){
                alert(res.message);
                // update cart counter if needed
            }
        }
    });
});

</script>
<script>
document.querySelectorAll('.add-to-wishlist-form').forEach(form => {
    form.querySelector('.wishlist-btn').addEventListener('click', function() {
        const productId = form.dataset.product;
        const token = form.querySelector('input[name="_token"]').value;
        const icon = this.querySelector('span.icon');
        const tooltip = this.querySelector('span.tooltip');

        fetch("{{ route('wishlist.add') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);

            if(data.status === 'added'){
                icon.classList.remove('icon-heart');
                icon.classList.add('icon-heart-filled');
                tooltip.textContent = 'Remove from Wishlist';
            } else {
                icon.classList.remove('icon-heart-filled');
                icon.classList.add('icon-heart');
                tooltip.textContent = 'Add to Wishlist';
            }
        })
        .catch(err => alert('Error processing wishlist!'));
    });
});
</script>

</body>

</html>