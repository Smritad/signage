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
        <div class="preload-logo">
            <div class="spinner"></div>
        </div>
    </div>
    <div id="wrapper">
        @include('components.frontend.header')
        <!-- /Header -->
        <!-- Banner Slider -->
       <div class="tf-slideshow type-abs tf-btn-swiper-main hover-sw-nav">
    <div dir="ltr" class="swiper tf-swiper sw-slide-show slider_effect_fade" 
         data-auto="true" data-loop="true" data-effect="fade" data-delay="3000">
        <div class="swiper-wrapper">
            @foreach($banners as $banner)
                <div class="swiper-slide">
                    <div class="slider-wrap">
                        <div class="sld_image">
                            <img src="{{ asset('home/banner/' . basename($banner->banner_images)) }}" 
                                alt="{{ $banner->banner_heading }}" class="lazyload"> 
                        </div>
                        <div class="sld_content">
                            <div class="container">
                                <div class="content-sld_wrap">
                                    <h1 class="title_sld text-display fade-item fade-item-1">
                                        {!! str_replace('.', '.<br>', e($banner->banner_heading)) !!}
                                    </h1>

                                    <div class="fade-item fade-item-3 mt-5">
                                        <a href="shop-default-list.html" 
                                           class="tf-btn animate-btn fw-semibold">
                                            Shop now
                                            <i class="icon icon-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="sw-dot-default tf-sw-pagination"></div>
    </div>

    <div class="tf-sw-nav nav-prev-swiper">
        <i class="icon icon-caret-left"></i>
    </div>
    <div class="tf-sw-nav nav-next-swiper">
        <i class="icon icon-caret-right"></i>
    </div>
</div>

        <!-- /Banner Slider -->
        <!-- Category -->
        <div class="flat-spacing s-category">
            <div class="sect-title text-center wow fadeInUp">
                <p class="s-title h1 fw-medium  mb-8">Perfume Category</p>
            </div>
            <div dir="ltr" class="swiper tf-swiper wow fadeInUp" data-preview="5" data-tablet="3" data-mobile-sm="2"
                data-mobile="2" data-space-lg="24" data-space-md="12" data-space="12" data-pagination="2"
                data-pagination-sm="2" data-pagination-md="3" data-pagination-lg="5">
                <div class="swiper-wrapper">
                    <!-- item 1 -->
                    <div class="swiper-slide">
                        <div class="box-image_category hover-img">
                            <a href="#" class="box-image_image img-style">
                                <img class="lazyload" src="{{ asset('frontend/assets/images/perfume/1.webp')}}" data-src="{{ asset('frontend/assets/images/perfume/1.webp')}}"
                                    alt="Image">
                            </a>
                            <div class="box-image_content">
                                <a href="#" class="tf-btn btn-white animate-btn animate-dark">
                                    <span class="h5 fw-medium">Kepler</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- item 2 -->
                    <div class="swiper-slide">
                        <div class="box-image_category hover-img">
                            <a href="#" class="box-image_image img-style">
                                <img class="lazyload" src="{{ asset('frontend/assets/images/perfume/2.webp')}}" data-src="{{ asset('frontend/assets/images/perfume/2.webp')}}"
                                    alt="Image">
                            </a>
                            <div class="box-image_content">
                                <a href="#" class="tf-btn btn-white animate-btn animate-dark">
                                    <span class="h5 fw-medium">Aquila</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- item 3 -->
                    <div class="swiper-slide">
                        <div class="box-image_category hover-img">
                            <a href="#" class="box-image_image img-style">
                                <img class="lazyload" src="{{ asset('frontend/assets/images/perfume/3.webp')}}" data-src="{{ asset('frontend/assets/images/perfume/3.webp')}}"
                                    alt="Image">
                            </a>
                            <div class="box-image_content">
                                <a href="#" class="tf-btn btn-white animate-btn animate-dark">
                                    <span class="h5 fw-medium">Altair</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- item 4 -->
                    <div class="swiper-slide">
                        <div class="box-image_category hover-img">
                            <a href="#" class="box-image_image img-style">
                                <img class="lazyload" src="{{ asset('frontend/assets/images/perfume/4.webp')}}" data-src="{{ asset('frontend/assets/images/perfume/4.webp')}}"
                                    alt="Image">
                            </a>
                            <div class="box-image_content">
                                <a href="#" class="tf-btn btn-white animate-btn animate-dark">
                                    <span class="h5 fw-medium">Sandal Wood</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- item 5 -->
                    <div class="swiper-slide">
                        <div class="box-image_category hover-img">
                            <a href="#" class="box-image_image img-style">
                                <img class="lazyload" src="{{ asset('frontend/assets/images/perfume/5.webp')}}" data-src="{{ asset('frontend/assets/images/perfume/5.webp')}}"
                                    alt="Image">
                            </a>
                            <div class="box-image_content">
                                <a href="#" class="tf-btn btn-white animate-btn animate-dark">
                                    <span class="h5 fw-medium">Oud Sensation</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- item 6 -->
                    <div class="swiper-slide">
                        <div class="box-image_category hover-img">
                            <a href="#" class="box-image_image img-style">
                                <img class="lazyload" src="{{ asset('frontend/assets/images/perfume/6.webp')}}" data-src="{{ asset('frontend/assets/images/perfume/6.webp')}}"
                                    alt="Image">
                            </a>
                            <div class="box-image_content">
                                <a href="#" class="tf-btn btn-white animate-btn animate-dark">
                                    <span class="h5 fw-medium">Nebula</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sw-dot-default tf-sw-pagination"></div>
            </div>
        </div>
        <!-- /Category -->
        <!-- Box Image -->
        <div class="flat-spacing pt-0">
            <div class="container">
                <div class="sect-title text-center wow fadeInUp">
                    <h1 class="s-title mb-8">Product Category</h1>
                </div>
              @php
use App\Models\CategoryDetails;

// Fetch all categories from database
$categories = CategoryDetails::all();
@endphp

<div dir="ltr" class="swiper tf-swiper" data-preview="3" data-tablet="2" data-mobile-sm="1"
    data-mobile="1" data-space-lg="48" data-space-md="32" data-space="12" data-pagination="1"
    data-pagination-sm="1" data-pagination-md="2" data-pagination-lg="3">
    <div class="swiper-wrapper">
        @foreach($categories as $category)
            @php
                // Use a default image for now
                $imagePath = 'frontend/assets/images/home-category/Air-Freshner-Sachet.webp';

                // Generate category URL dynamically
                $categoryUrl = route('product.category', $category->slug);
            @endphp

            <div class="swiper-slide">
                <div class="box-image_V05 type-space-2 hover-img wow fadeInLeft">
                    <a href="{{ $categoryUrl }}" class="box-image_image img-style">
                        <img src="{{ asset($imagePath) }}"
                             data-src="{{ asset($imagePath) }}"
                             alt="{{ $category->category_name }}"
                             class="lazyload">
                    </a>
                    <div class="box-image_content">
                        <h4 class="title">
                            <a href="{{ $categoryUrl }}" class="link">
                                {{ $category->category_name }}
                            </a>
                        </h4>
                        <a href="{{ $categoryUrl }}" class="tf-btn-line fw-bold letter-space-0">
                            Shop now
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="sw-dot-default tf-sw-pagination"></div>
</div>

            </div>
        </div>
        <!-- /Box Image -->
        <!-- Category -->
        <section class="themesFlat">
            <div class="container">
                <div class="sect-title text-center wow fadeInUp">
                    <h1 class="title mb-8">Shop by Fragrance</h1>
                </div>
                <div dir="ltr" class="swiper tf-swiper wow fadeInUp" data-preview="7" data-tablet="4" data-mobile-sm="3"
                    data-mobile="2" data-space-lg="48" data-space-md="32" data-space="12" data-pagination="2"
                    data-pagination-sm="3" data-pagination-md="4" data-pagination-lg="6">
                    <div class="swiper-wrapper">
                        <!-- item 1 -->
                        <div class="swiper-slide">
                            <a href="#" class="widget-collection style-circle hover-img">
                                <div class="collection_image img-style">
                                    <img class="lazyload" src="{{ asset('frontend/assets/images/flavours/Rose-flavour.webp')}}"
                                        data-src="{{ asset('frontend/assets/images/flavours/Rose-flavour.webp')}}" alt="">
                                </div>
                                <div class="collection_content">
                                    <p class="collection_name h4 link">
                                        Rose <span class="count text-main-2">(24)</span>
                                    </p>
                                </div>
                            </a>
                        </div>
                        <!-- item 2 -->
                        <div class="swiper-slide">
                            <a href="#" class="widget-collection style-circle hover-img">
                                <div class="collection_image img-style">
                                    <img class="lazyload" src="{{ asset('frontend/assets/images/flavours/citru-flavour.webp')}}"
                                        data-src="{{ asset('frontend/assets/images/flavours/citru-flavour.webp')}}" alt="">
                                </div>
                                <div class="collection_content">
                                    <p class="collection_name h4 link">
                                        Citrusy <span class="count text-main-2">(24)</span>
                                    </p>

                                </div>
                            </a>
                        </div>
                        <!-- item 3 -->
                        <div class="swiper-slide">
                            <a href="#" class="widget-collection style-circle hover-img">
                                <div class="collection_image img-style">
                                    <img class="lazyload" src="{{ asset('frontend/assets/images/flavours/white-floral-flavour.webp')}}"
                                        data-src="{{ asset('frontend/assets/images/flavours/white-floral-flavour.webp')}}" alt="">
                                </div>
                                <div class="collection_content">
                                    <p class="collection_name h4 link">
                                        White Floral <span class="count text-main-2">(24)</span>
                                    </p>

                                </div>
                            </a>
                        </div>
                        <!-- item 4 -->
                        <div class="swiper-slide">
                            <a href="#" class="widget-collection style-circle hover-img">
                                <div class="collection_image img-style">
                                    <img class="lazyload" src="{{ asset('frontend/assets/images/flavours/aquatic-flavour.webp')}}"
                                        data-src="{{ asset('frontend/assets/images/flavours/aquatic-flavour.webp')}}" alt="">
                                </div>
                                <div class="collection_content">
                                    <p class="collection_name h4 link">
                                        Aquatic <span class="count text-main-2">(24)</span>
                                    </p>
                                </div>
                            </a>
                        </div>
                        <!-- item 5 -->
                        <div class="swiper-slide">
                            <a href="#" class="widget-collection style-circle hover-img">
                                <div class="collection_image img-style">
                                    <img class="lazyload" src="{{ asset('frontend/assets/images/flavours/musk-flavour.webp')}}"
                                        data-src="{{ asset('frontend/assets/images/flavours/musk-flavour.webp')}}" alt="">
                                </div>
                                <div class="collection_content">
                                    <p class="collection_name h4 link">
                                        Musk <span class="count text-main-2">(24)</span>
                                    </p>
                                </div>
                            </a>
                        </div>
                        <!-- item 6 -->
                        <div class="swiper-slide">
                            <a href="#" class="widget-collection style-circle hover-img">
                                <div class="collection_image img-style">
                                    <img class="lazyload" src="{{ asset('frontend/assets/images/flavours/spice-flavour.webp')}}"
                                        data-src="{{ asset('frontend/assets/images/flavours/spice-flavour.webp')}}" alt="">
                                </div>
                                <div class="collection_content">
                                    <p class="collection_name h4 link">
                                        Spicy <span class="count text-main-2">(24)</span>
                                    </p>

                                </div>
                            </a>
                        </div>
                        <!-- item 7 -->
                        <div class="swiper-slide">
                            <a href="#" class="widget-collection style-circle hover-img">
                                <div class="collection_image img-style">
                                    <img class="lazyload" src="{{ asset('frontend/assets/images/flavours/woody-flavour.webp')}}"
                                        data-src="{{ asset('frontend/assets/images/flavours/woody-flavour.webp')}}" alt="">
                                </div>
                                <div class="collection_content">
                                    <p class="collection_name h4 link">
                                        Woody <span class="count text-main-2">(24)</span>
                                    </p>

                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="sw-dot-default tf-sw-pagination"></div>
                </div>
            </div>
        </section>
        <!-- /Category -->
        <!-- Voucher -->
        @foreach($advertisements as $ad)
        <section class="flat-spacing pb-0">
            <div class="container">
                <div class="banner-V02 hover-img wow fadeInUp">
                    <div class="banner_img img-style">
                        <img src="{{ asset('home/advertisement/' . $ad->advertisement_image) }}" alt="Banner" class="lazyload">

                    </div>
                    <div class="banner_content">
                        <div class="box-text">
                            <h2 class="title type-semibold">
                                <a href="#" class="text-white">{{ $ad->title }}</a>
                            </h2>
                            <h4 class="sub-title text-white">{{ $ad->advertisement_heading }}</h4>
                        </div>
                        <div class="group-btn">
                            <a href="#" class="tf-btn animate-btn type-small-3">
                                Contact Us
                                <i class="icon icon-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endforeach

        <!-- /Voucher -->
        <!-- Box Icon -->
       <div class="flat-spacing why-wellness">
    <div class="container">
        <div class="sect-title text-center wow fadeInUp">
            <h1 class="s-title mb-8">{{ $signageHeading }}</h1>
        </div>

        @if(!empty($signageItems))
            <div dir="ltr" class="swiper tf-swiper"
                data-preview="4" data-tablet="3" data-mobile-sm="2" data-mobile="1"
                data-space-lg="97" data-space-md="33" data-space="13"
                data-pagination="1" data-pagination-sm="2" data-pagination-md="3" data-pagination-lg="4">

                <div class="swiper-wrapper">
                    @foreach($signageItems as $index => $item)
                        <div class="swiper-slide">
                            <div class="box-icon_V01 wow fadeInLeft" data-wow-delay="{{ $index * 0.1 }}s">
                                <span class="icon">
                                    <img src="{{ asset($item['image']) }}" alt="{{ $item['title'] }}">
                                </span>
                                <div class="content">
                                    <h4 class="title fw-bold">{{ $item['title'] }}</h4>
                                    <p class="text">{{ $item['description'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="sw-dot-default tf-sw-pagination"></div>
            </div>
        @endif
    </div>
</div>


        <!-- /Box Icon -->

        <!-- Testimonial -->
        <section class="flat-spacing bg-white-smoke">
    <div class="container">
        <div class="sect-title text-center wow fadeInUp">
            <h1 class="s-title mb-8 text-white">{{ $customerReviewHeading }}</h1>
        </div>
        <div class="tf-btn-swiper-main pst-2">
            <div dir="ltr" class="swiper tf-swiper" 
                 data-preview="3" data-tablet="2" data-mobile-sm="1"
                 data-mobile="1" data-space-lg="48" data-space-md="32" 
                 data-space="12" data-pagination="1" data-pagination-sm="1" 
                 data-pagination-md="2" data-pagination-lg="3">

                <div class="swiper-wrapper">
                    @foreach($customerReviews as $index => $review)
                        <div class="swiper-slide">
                            <div class="testimonial-V01 border-0 wow fadeInLeft" 
                                 @if($index > 0) data-wow-delay="{{ $index * 0.1 }}s" @endif>
                                <div>
                                    <h4 class="tes_title">{{ $review['title'] ?? '' }}</h4>
                                    <p class="tes_text h4">{{ $review['description'] ?? '' }}</p>
                                    <div class="tes_author">
                                        <p class="author-name h4">{{ $review['name'] ?? '' }}</p>
                                        <i class="author-verified icon-check-circle fs-24"></i>
                                    </div>
                                    <div class="rate_wrap">
                                        @for($i = 0; $i < ($review['rating'] ?? 0); $i++)
                                            <i class="icon-star text-star"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="sw-dot-default tf-sw-pagination"></div>
            </div>
            <div class="tf-sw-nav nav-prev-swiper">
                <i class="icon icon-caret-left"></i>
            </div>
            <div class="tf-sw-nav nav-next-swiper">
                <i class="icon icon-caret-right"></i>
            </div>
        </div>
    </div>
</section>

        <!-- /Testimonial -->
        <!-- Gallery -->
        <section class="flat-spacing pb-xl-0 instagram-sec">
            <div class="container">
                <div class="sect-title text-center wow fadeInUp">
                    <div class="h1 title mb-16">Follow Us On Instagram</div>
                </div>
            </div>
            <div dir="ltr" class="swiper tf-swiper wow fadeInUp" data-preview="5" data-tablet="4" data-mobile-sm="3"
                data-mobile="2" data-space="0" data-pagination="2" data-pagination-sm="3" data-pagination-md="4"
                data-pagination-lg="6">
                <div class="swiper-wrapper">
                    <!-- item 1 -->
                    <div class="swiper-slide">
                        <div class="gallery-item hover-img hover-overlay">
                            <div class="image img-style">
                                <img class="lazyload" src="{{ asset('frontend/assets/images/instagram/1.webp')}}" data-src="{{ asset('frontend/assets/images/instagram/1.webp')}}"
                                    alt="Image">
                            </div>
                            <a href="#" class="box-icon hover-tooltip">
                                <span class="icon icon-instagram-logo"></span>
                                <span class="tooltip">View product</span>
                            </a>
                        </div>
                    </div>
                    <!-- item 2 -->
                    <div class="swiper-slide">
                        <div class="gallery-item hover-img hover-overlay">
                            <div class="image img-style">
                                <img class="lazyload" src="{{ asset('frontend/assets/images/instagram/2.webp')}}" data-src="{{ asset('frontend/assets/images/instagram/2.webp')}}"
                                    alt="Image">
                            </div>
                            <a href="#" class="box-icon hover-tooltip">
                                <span class="icon icon-instagram-logo"></span>
                                <span class="tooltip">View product</span>
                            </a>
                        </div>
                    </div>
                    <!-- item 3 -->
                    <div class="swiper-slide">
                        <div class="gallery-item hover-img hover-overlay">
                            <div class="image img-style">
                                <img class="lazyload" src="{{ asset('frontend/assets/images/instagram/3.webp')}}" data-src="{{ asset('frontend/assets/images/instagram/3.webp')}}"
                                    alt="Image">
                            </div>
                            <a href="#" class="box-icon hover-tooltip">
                                <span class="icon icon-instagram-logo"></span>
                                <span class="tooltip">View product</span>
                            </a>
                        </div>
                    </div>
                    <!-- item 4 -->
                    <div class="swiper-slide">
                        <div class="gallery-item hover-img hover-overlay">
                            <div class="image img-style">
                                <img class="lazyload" src="{{ asset('frontend/assets/images/instagram/4.webp')}}" data-src="{{ asset('frontend/assets/images/instagram/4.webp')}}"
                                    alt="Image">
                            </div>
                            <a href="#" class="box-icon hover-tooltip">
                                <span class="icon icon-instagram-logo"></span>
                                <span class="tooltip">View product</span>
                            </a>
                        </div>
                    </div>
                    <!-- item 5 -->
                    <div class="swiper-slide">
                        <div class="gallery-item hover-img hover-overlay">
                            <div class="image img-style">
                                <img class="lazyload" src="{{ asset('frontend/assets/images/instagram/5.webp')}}" data-src="{{ asset('frontend/assets/images/instagram/5.webp')}}"
                                    alt="Image">
                            </div>
                            <a href="#" class="box-icon hover-tooltip">
                                <span class="icon icon-instagram-logo"></span>
                                <span class="tooltip">View product</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="sw-dot-default tf-sw-pagination"></div>
            </div>
        </section>
        <!-- /Gallery -->
        <!-- Footer -->
          @include('components.frontend.footer')    

        <!-- /Footer -->
    </div>

    <!-- Mobile Menu -->
    <div class="offcanvas offcanvas-start canvas-mb" id="mobileMenu">
        <span class="icon-close-popup" data-bs-dismiss="offcanvas">
            <i class="icon-close"></i>
        </span>
        <div class="canvas-header">
            <p class="text-logo-mb"><img src="{{ asset('frontend/assets/images/logo/logo.webp')}}" data-src="{{ asset('frontend/assets/images/logo/logo.webp')}}"></p>
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

</body>