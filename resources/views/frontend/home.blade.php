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

        {{-- ════════════════════════════════════════════════════════════ --}}
        {{-- HELPER: build product URL safely (used in both sliders)       --}}
        {{-- ════════════════════════════════════════════════════════════ --}}
        @php
            /**
             * Build a product frontend URL safely.
             * Handles: null category, null subcategory, int OR JSON array sub_category_id.
             */
            $buildProductUrl = function ($product) {
                $category = $product->category_id
                    ? \App\Models\CategoryDetails::find($product->category_id)
                    : null;

                /* Extract first subcategory id — supports int AND JSON array */
                $subCatId = null;
                $rawSub   = $product->getRawOriginal('sub_category_id');
                if (!empty($rawSub)) {
                    $decoded = json_decode($rawSub, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $subCatId = isset($decoded[0]) ? (int) $decoded[0] : null;
                    } elseif (is_numeric($rawSub)) {
                        $subCatId = (int) $rawSub;
                    }
                }

                $subcategory = $subCatId ? \App\Models\SabCategoryDetails::find($subCatId) : null;

                if ($category && $subcategory && $product->slug) {
                    return route('product.details', [
                        'cat'    => $category->slug,
                        'sabcat' => $subcategory->slug,
                        'slug'   => $product->slug,
                    ]);
                }
                return '#';
            };
        @endphp

        {{-- ════════════════════════════════════════════════════════════ --}}
        {{-- Banner Slider                                                --}}
        {{-- ════════════════════════════════════════════════════════════ --}}
        <div class="tf-slideshow type-abs tf-btn-swiper-main hover-sw-nav">
            <div dir="ltr" class="swiper tf-swiper sw-slide-show slider_effect_fade"
                 data-auto="true" data-loop="true" data-effect="fade" data-delay="3000">
                <div class="swiper-wrapper">
                    @foreach($banners as $banner)
                        <div class="swiper-slide">
                            <div class="slider-wrap">
                                <div class="sld_image">
                                    <img src="{{ asset('home/banner/' . basename($banner->banner_images)) }}"
                                         alt="{{ $banner->banner_heading }}" class="lazyload desktop-banner">

                                    @if(!empty($banner->mobile_banner))
                                        <img src="{{ asset('home/banner/' . basename($banner->mobile_banner)) }}"
                                             alt="{{ $banner->banner_heading }}"
                                             class="lazyload mobile-banner"
                                             style="width:100%!important;height:350px!important;object-fit:cover!important;display:block!important;">
                                    @else
                                        <img src="{{ asset('home/banner/' . basename($banner->banner_images)) }}"
                                             alt="{{ $banner->banner_heading }}" class="lazyload mobile-banner">
                                    @endif
                                </div>

                                <div class="sld_content">
                                    <div class="container">
                                        <div class="content-sld_wrap">
                                            <h1 class="title_sld text-display fade-item fade-item-1">
                                                {!! str_replace('.', '.<br>', e($banner->banner_heading)) !!}
                                            </h1>

                                            @php
                                                $masterCategory = \App\Models\CategoryDetails::whereNotNull('slug')
                                                                    ->where('slug', '!=', '')->first();
                                            @endphp

                                            @if($masterCategory)
                                                <div class="fade-item fade-item-3 mt-5">
                                                    <a href="{{ route('product.category', $masterCategory->slug) }}"
                                                       class="tf-btn animate-btn fw-semibold">
                                                        Shop {{ $masterCategory->category_name }}
                                                        <i class="icon icon-arrow-right"></i>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="sw-dot-default tf-sw-pagination"></div>
            </div>

            <div class="tf-sw-nav nav-prev-swiper"><i class="icon icon-caret-left"></i></div>
            <div class="tf-sw-nav nav-next-swiper"><i class="icon icon-caret-right"></i></div>
        </div>

        {{-- ════════════════════════════════════════════════════════════ --}}
        {{-- All Products Swiper — FIXED                                  --}}
        {{-- ════════════════════════════════════════════════════════════ --}}
        <div class="flat-spacing s-category home-main-slider">
            <div class="sect-title text-center wow fadeInUp">
                <p class="s-title h1 fw-medium mb-8">All Products</p>
            </div>

            @if($products->count() > 0)
                <div dir="ltr" class="swiper tf-swiper wow fadeInUp"
                     data-preview="5" data-tablet="3" data-mobile-sm="2" data-mobile="2"
                     data-space-lg="24" data-space-md="12" data-space="12"
                     data-pagination="1" data-pagination-sm="1" data-pagination-md="1" data-pagination-lg="1"
                     data-auto="true" data-delay="3000" data-loop="true">

                    <div class="swiper-wrapper">
                        @foreach($products as $product)
                            @php
                                $images     = is_array($product->images) ? $product->images : json_decode($product->images, true);
                                $firstImage = !empty($images[0]) ? $images[0] : null;
                                $productUrl = $buildProductUrl($product);
                            @endphp

                            <div class="swiper-slide">
                                <div class="box-image_category hover-img">
                                    <a href="{{ $productUrl }}" class="box-image_image img-style">
                                        <img class="lazyload"
                                             src="{{ $firstImage
                                                    ? asset('signage/home/productimage/' . $firstImage)
                                                    : asset('frontend/assets/images/no-image.jpg') }}"
                                             alt="{{ $product->product_name }}">
                                    </a>

                                    <div class="box-image_content">
                                        <a href="{{ $productUrl }}" class="tf-btn btn-white animate-btn animate-dark">
                                            <span class="h5 fw-medium">{{ $product->product_name }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="sw-dot-default tf-sw-pagination"></div>
                </div>
            @else
                <p class="text-center text-muted py-4">No products available right now.</p>
            @endif
        </div>

       
        {{-- ════════════════════════════════════════════════════════════ --}}
        {{-- Product Category                                            --}}
        {{-- ════════════════════════════════════════════════════════════ --}}
        <div class="flat-spacing pt-0">
            <div class="container">
                <div class="sect-title text-center wow fadeInUp">
                    <h1 class="s-title mb-8">Product Category</h1>
                </div>

                @php
                    $categories = \App\Models\CategoryDetails::all();
                @endphp

                <div dir="ltr" class="swiper tf-swiper"
                     data-preview="3" data-tablet="2" data-mobile-sm="1" data-mobile="1"
                     data-space-lg="48" data-space-md="32" data-space="12"
                     data-pagination="1" data-pagination-sm="1" data-pagination-md="2" data-pagination-lg="3"
                     data-auto="true" data-delay="3000" data-loop="true">
                    <div class="swiper-wrapper">
                        @foreach($categories as $category)
                            @php
                                $hasProducts = \App\Models\ProductsDetails::where('category_id', $category->id)->exists();

                                $imagePath = $category->image && file_exists(public_path('signage/home/productimage/' . $category->image))
                                            ? 'signage/home/productimage/' . $category->image
                                            : 'frontend/assets/images/home-category/Air-Freshner-Sachet.webp';

                                $categoryUrl = $hasProducts ? route('product.category', $category->slug) : route('coming.soon');
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
                                            <a href="{{ $categoryUrl }}" class="link">{{ $category->category_name }}</a>
                                        </h4>
                                        <a href="{{ $categoryUrl }}" class="tf-btn-line fw-bold letter-space-0">Shop now</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="sw-dot-default tf-sw-pagination"></div>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════ --}}
        {{-- Shop by Fragrance                                           --}}
        {{-- ════════════════════════════════════════════════════════════ --}}
        <section class="themesFlat fragrance-wrap">
            <div class="container">
                <div class="sect-title text-center wow fadeInUp">
                    <p class="s-title h1 fw-medium mb-8">Shop by Fragrance</p>
                </div>

                <div dir="ltr" class="swiper tf-swiper wow fadeInUp"
                     data-preview="5" data-tablet="3" data-mobile-sm="2" data-mobile="2"
                     data-space-lg="24" data-space-md="12" data-space="12"
                     data-pagination="1" data-pagination-sm="1" data-pagination-md="1" data-pagination-lg="5"
                     data-auto="true" data-delay="3000" data-loop="true">
                    <div class="swiper-wrapper">
                        @foreach($fragranceTypes as $ft)
                            <div class="swiper-slide">
                                <a href="{{ route('product.fragrance', ['slug' => $ft->slug]) }}" class="widget-collection style-circle hover-img">
                                    <div class="collection_image img-style">
                                        <img class="lazyload"
                                             src="{{ asset('signage/home/productimage/' . $ft->image) }}"
                                             data-src="{{ asset('signage/home/productimage/' . $ft->image) }}"
                                             alt="{{ $ft->title }}">
                                    </div>
                                    <div class="collection_content">
                                        <p class="collection_name h4 link">{{ $ft->title }}</p>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="sw-dot-default tf-sw-pagination"></div>
                </div>
            </div>
        </section>

        {{-- ════════════════════════════════════════════════════════════ --}}
        {{-- Advertisements                                              --}}
        {{-- ════════════════════════════════════════════════════════════ --}}
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
                        @php $category = \App\Models\CategoryDetails::where('slug', 'perfume')->first(); @endphp
                        @if($category)
                            <div class="group-btn">
                                <a href="{{ route('product.category', $category->slug) }}" class="tf-btn animate-btn type-small-3">
                                    Shop Now
                                    <i class="icon icon-arrow-right"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
        @endforeach

        {{-- ════════════════════════════════════════════════════════════ --}}
        {{-- Why Wellness                                                --}}
        {{-- ════════════════════════════════════════════════════════════ --}}
        <div class="flat-spacing why-wellness">
            <div class="container">
                <div class="sect-title text-center wow fadeInUp">
                    <h1 class="s-title mb-8">{{ $signageHeading }}</h1>
                </div>

                @if(!empty($signageItems))
                    <div dir="ltr" class="swiper tf-swiper"
                         data-preview="4" data-tablet="3" data-mobile-sm="2" data-mobile="2"
                         data-space-lg="97" data-space-md="33" data-space="10"
                         data-pagination="1" data-pagination-sm="2" data-pagination-md="3" data-pagination-lg="4">
                        <div class="swiper-wrapper">
                            @foreach($signageItems as $index => $item)
                                <div class="swiper-slide">
                                    <div class="box-icon_V01 wow fadeInLeft" data-wow-delay="{{ $index * 0.1 }}s">
                                        <span class="icon">
                                            <img src="{{ asset('' . $item['image']) }}" alt="{{ $item['title'] }}">
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

        {{-- ════════════════════════════════════════════════════════════ --}}
        {{-- Testimonials                                                --}}
        {{-- ════════════════════════════════════════════════════════════ --}}
        <section class="flat-spacing bg-white-smoke">
            <div class="container">
                <div class="sect-title text-center wow fadeInUp">
                    <h1 class="s-title mb-8 text-white">{{ $customerReviewHeading }}</h1>
                </div>
                <div class="tf-btn-swiper-main pst-2">
                    <div dir="ltr" class="swiper tf-swiper"
                         data-preview="3" data-tablet="2" data-mobile-sm="1" data-mobile="1"
                         data-space-lg="48" data-space-md="32" data-space="12"
                         data-pagination="1" data-pagination-sm="1" data-pagination-md="2" data-pagination-lg="3"
                         data-auto="true" data-delay="3000" data-loop="true">
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
                    <div class="tf-sw-nav nav-prev-swiper"><i class="icon icon-caret-left"></i></div>
                    <div class="tf-sw-nav nav-next-swiper"><i class="icon icon-caret-right"></i></div>
                </div>
            </div>
        </section>

        {{-- ════════════════════════════════════════════════════════════ --}}
        {{-- Instagram                                                   --}}
        {{-- ════════════════════════════════════════════════════════════ --}}
        <section class="flat-spacing pb-xl-0 instagram-sec">
            <div class="container">
                <div class="sect-title text-center wow fadeInUp">
                    <div class="h1 title mb-16">Follow Us On Instagram</div>
                </div>
            </div>
            <div dir="ltr" class="swiper tf-swiper wow fadeInUp"
                 data-preview="5" data-tablet="4" data-mobile-sm="3" data-mobile="1"
                 data-space="0" data-pagination="2" data-pagination-sm="3" data-pagination-md="4" data-pagination-lg="6"
                 data-auto="true" data-delay="3000" data-loop="true">
                <div class="swiper-wrapper">
                    @for($i = 1; $i <= 5; $i++)
                        <div class="swiper-slide">
                            <div class="gallery-item hover-img hover-overlay">
                                <div class="image img-style">
                                    <img class="lazyload"
                                         src="{{ asset('frontend/assets/images/instagram/' . $i . '.webp') }}"
                                         data-src="{{ asset('frontend/assets/images/instagram/' . $i . '.webp') }}"
                                         alt="Image">
                                </div>
                                <a href="#" class="box-icon hover-tooltip">
                                    <span class="icon icon-instagram-logo"></span>
                                    <span class="tooltip">View product</span>
                                </a>
                            </div>
                        </div>
                    @endfor
                </div>
                <div class="sw-dot-default tf-sw-pagination"></div>
            </div>
        </section>

        @include('components.frontend.footer')
    </div>

    @include('components.frontend.main-js')
</body>
</html>