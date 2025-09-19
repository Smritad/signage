
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
       <section class="s-page-title">
    <div class="container">
        <div class="content">
            <h1 class="title-page">{{ $product->product_name }}</h1>
            <ul class="breadcrumbs-page">
                <li>
                    <a href="{{ url('/') }}" class="h6 link">Home</a>
                </li>
                <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                <li>
                    <a href="{{ route('product.subcategory', ['category' => $category->slug, 'sabcat' => $subcategory->slug]) }}" class="h6 link">
                        {{ $subcategory->sab_category_name }}
                    </a>
                </li>
                <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                <li>
                    <h6 class="current-page fw-normal">{{ $product->product_name }}</h6>
                </li>
            </ul>
        </div>
    </div>
</section>

        <!-- /Page Title -->
        <!-- Product Main -->
        <section class="flat-single-product flat-spacing-3">
            <div class="tf-main-product section-image-zoom">
                <div class="container">
                    <div class="row">
                        <!-- Product Images -->
                      <div class="col-md-6">
                            <div class="tf-product-media-wrap sticky-top">
                                <div class="product-thumbs-slider">
                                    <!-- Thumbnails -->
                                    <div dir="ltr" class="swiper tf-product-media-thumbs other-image-zoom"
                                        data-direction="vertical" data-preview="4.7">
                                        <div class="swiper-wrapper stagger-wrap">
                                            @foreach($product->images as $img)
                                                <div class="swiper-slide stagger-item">
                                                    <div class="item">
                                                        <img class="lazyload"
                                                            data-src="{{ asset('signage/home/productimage/'.$img) }}"
                                                            src="{{ asset('signage/home/productimage/'.$img) }}"
                                                            alt="{{ $product->product_name }}">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Main Gallery -->
                                    <div class="flat-wrap-media-product">
                                        <div dir="ltr" class="swiper tf-product-media-main" id="gallery-swiper-started">
                                            <div class="swiper-wrapper">
                                                @foreach($product->images as $img)
                                                    <div class="swiper-slide">
                                                        <a href="{{ asset('signage/home/productimage/'.$img) }}" target="_blank" class="item"
                                                        data-pswp-width="860px" data-pswp-height="1146px">
                                                            <img class="tf-image-zoom lazyload"
                                                                data-zoom="{{ asset('signage/home/productimage/'.$img) }}"
                                                                data-src="{{ asset('signage/home/productimage/'.$img) }}"
                                                                src="{{ asset('signage/home/productimage/'.$img) }}"
                                                                alt="{{ $product->product_name }}">
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- /Product Images -->
                        
                        <!-- Product Info -->
                        <div class="col-md-6">
                            <div class="tf-product-info-wrap position-relative">
                                <div class="tf-zoom-main sticky-top"></div>
                                <div class="tf-product-info-list other-image-zoom">
                                <h2 class="product-info-name">{{ $product->product_name }}</h2>
                                    <div class="product-info-meta">
                                        <div class="rating">
                                            <div class="d-flex gap-4">
                                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M14 5.4091L8.913 5.07466L6.99721 0.261719L5.08143 5.07466L0 5.4091L3.89741 8.7184L2.61849 13.7384L6.99721 10.9707L11.376 13.7384L10.097 8.7184L14 5.4091Z"
                                                        fill="#EF9122" />
                                                </svg>
                                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M14 5.4091L8.913 5.07466L6.99721 0.261719L5.08143 5.07466L0 5.4091L3.89741 8.7184L2.61849 13.7384L6.99721 10.9707L11.376 13.7384L10.097 8.7184L14 5.4091Z"
                                                        fill="#EF9122" />
                                                </svg>
                                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M14 5.4091L8.913 5.07466L6.99721 0.261719L5.08143 5.07466L0 5.4091L3.89741 8.7184L2.61849 13.7384L6.99721 10.9707L11.376 13.7384L10.097 8.7184L14 5.4091Z"
                                                        fill="#EF9122" />
                                                </svg>
                                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M14 5.4091L8.913 5.07466L6.99721 0.261719L5.08143 5.07466L0 5.4091L3.89741 8.7184L2.61849 13.7384L6.99721 10.9707L11.376 13.7384L10.097 8.7184L14 5.4091Z"
                                                        fill="#EF9122" />
                                                </svg>
                                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M14 5.4091L8.913 5.07466L6.99721 0.261719L5.08143 5.07466L0 5.4091L3.89741 8.7184L2.61849 13.7384L6.99721 10.9707L11.376 13.7384L10.097 8.7184L14 5.4091Z"
                                                        fill="#EF9122" />
                                                </svg>
                                            </div>
                                            <div class="reviews text-main">(3.671 review)</div>
                                        </div>
                                    </div>

                                    <div class="tf-product-heading">

                                       <div class="product-info-price price-wrap">
                                            @php
                                                $originalPrice = $product->price; // Original price
                                                $offerPrice = $product->offer_price ?? $product->price; // Use offer_price if exists, otherwise price
                                                $discount = 0;

                                                if($offerPrice < $originalPrice){
                                                    $discount = round((($originalPrice - $offerPrice) / $originalPrice) * 100);
                                                }
                                            @endphp

                                            <span class="price-new price-on-sale h2 fw-4">Rs. {{ number_format($offerPrice, 2) }}</span>

                                            @if($discount > 0)
                                                <span class="price-old h6">Rs. {{ number_format($originalPrice, 2) }}</span>
                                                <p class="badges-on-sale h6 fw-semibold">
                                                    <span class="number-sale">
                                                        -{{ $discount }} %
                                                    </span>
                                                </p>
                                            @endif
                                        </div>

                                    </div>
                                    <ul class="tf-product-cate-sku-1">
                                        <li class="item-cate-sku h6">
                                            <span class="label fw-6 text-black">Fragrance Type:</span>
                                            @if($fragranceType)
                                                <a href="#" class="value link text-main-2">{{ $fragranceType->title }}</a>
                                            @else
                                                <span class="value text-main-2">N/A</span>
                                            @endif
                                        </li>
                                    </ul>

                                    <div class="tf-product-total-quantity">
                                        <div class="group-btn">
                                            <div class="wg-quantity">
                                                <button class="btn-quantity btn-decrease">
                                                    <i class="icon icon-minus"></i>
                                                </button>
                                                <input class="quantity-product" type="text" name="number" value="1">
                                                <button class="btn-quantity btn-increase">
                                                    <i class="icon icon-plus"></i>
                                                </button>
                                            </div>
                                            <a href="#shoppingCart" data-bs-toggle="offcanvas"
                                                class="tf-btn animate-btn btn-add-to-cart">
                                                ADD TO CART
                                                <i class="icon icon-shopping-cart-simple"></i>
                                            </a>
                                            <button type="button" class="hover-tooltip box-icon btn-add-wishlist">
                                                <span class="icon icon-heart"></span>
                                                <span class="tooltip">Add to Wishlist</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="tf-product-delivery-return">
                                        {{-- Estimate Delivery --}}
                                        <div class="product-delivery">
                                            <div class="icon icon-clock-cd"></div>
                                            <p class="h6">
                                                <span class="fw-7 text-black">
                                                    {{ $product->estimate_delivery ?? '7-20 days' }}
                                                </span>
                                            </p>
                                        </div>

                                        {{-- Return Policy --}}
                                        <div class="product-delivery return">
                                            <div class="icon icon-compare"></div>
                                            <p class="h6">
                                                <span class="fw-7 text-black">
                                                    {{ $product->return_policy ?? '30 days' }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="tf-product-trust-seal">
                                        <p class="h6 text-seal">Guarantee Safe Checkout:</p>
                                        <ul class="list-card">
                                           <li class="card-item">
                                                <img src="{{ asset('frontend/assets/images/checkout-icon/visa.png') }}" alt="Visa">
                                            </li>
                                            <li class="card-item">
                                                <img src="{{ asset('frontend/assets/images/checkout-icon/mastercard.png') }}" alt="Master Card">
                                            </li>
                                            <li class="card-item">
                                                <img src="{{ asset('frontend/assets/images/checkout-icon/amex.png') }}" alt="Amex">
                                            </li>
                                            <li class="card-item">
                                                <img src="{{ asset('frontend/assets/images/checkout-icon/discover.png') }}" alt="Discover">
                                            </li>
                                            <li class="card-item">
                                                <img src="{{ asset('frontend/assets/images/checkout-icon/paypal.png') }}" alt="PayPal">
                                            </li>

                                        </ul>
                                    </div>
                                    <ul class="tf-product-cate-sku">
                                        {{-- SKU --}}
                                        <li class="item-cate-sku h6">
                                            <span class="label fw-6 text-black">SKU:</span>
                                            <a href="#" class="value link text-main-2">{{ $product->product_sku }}</a>
                                        </li>

                                        {{-- Categories --}}
                                        <li class="item-cate-sku h6">
                                            <span class="label fw-6 text-black">Categories:</span>
                                            <span class="value text-main-2">
                                                {{ $category->category_name}}
                                            </span>
                                        </li>
                                    </ul>

                                    <div class="tf-product-icon-box">
                                        <div class="item">
                                            <div class="icon">
                                                
<svg id="Image" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg"><g fill="none" stroke="#464747" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="m13 9a29.5 29.5 0 0 0 -11 23 30 30 0 1 0 30-30"/><path d="m13 17v-8h-8"/><path d="m32 15v17l-12 10"/><path d="m32 6v3"/><path d="m32 6v1"/><path d="m9.483 45 .867-.5"/><path d="m19 54.517.5-.867"/><path d="m45 54.517-.5-.867"/><path d="m54.517 45-.867-.5"/><path d="m54.517 19-.867.5"/><path d="m45 9.483-.5.867"/><path d="m6 32h3"/><path d="m32 58v-3"/><path d="m58 32h-3"/></g></svg>
                                            </div>
                                            <div class="text-small text-black">Long Lasting</div>
                                        </div>
                                        <div class="item">
                                            <div class="icon">
<svg xmlns="http://www.w3.org/2000/svg" id="Icons" viewBox="0 0 60 60" width="512" height="512"><path d="M49.484,1.32A2.988,2.988,0,0,0,47,0H37.331a2.965,2.965,0,0,0-2.777,1.874L30,13.092,25.45,1.883A2.969,2.969,0,0,0,22.669,0H13a3,3,0,0,0-2.777,4.126l8.714,21.459a19,19,0,1,0,22.13,0l8.714-21.46A3,3,0,0,0,49.484,1.32ZM12.074,3.375a.992.992,0,0,1,.1-.937A.979.979,0,0,1,13,2h9.671a1,1,0,0,1,.924.626l7.894,19.449C31,22.037,30.5,22,30,22a18.866,18.866,0,0,0-9.354,2.482ZM47,41A17,17,0,1,1,30,24,17.019,17.019,0,0,1,47,41Zm.926-37.626L39.354,24.482a18.834,18.834,0,0,0-5.586-2.1l-2.689-6.627L36.411,2.617A.992.992,0,0,1,37.331,2H47a.979.979,0,0,1,.824.438A.99.99,0,0,1,47.926,3.374Z"/><path d="M27.862,30.326l-2.415,4.882-5.4.783a2.379,2.379,0,0,0-1.321,4.06l3.909,3.8-.922,5.363a2.383,2.383,0,0,0,3.458,2.511L30,49.189l4.834,2.536a2.373,2.373,0,0,0,2.513-.185,2.364,2.364,0,0,0,.944-2.326l-.922-5.363,3.909-3.8a2.379,2.379,0,0,0-1.321-4.06l-5.4-.783-2.415-4.882a2.387,2.387,0,0,0-4.276,0Zm2.483.887,2.648,5.352a1,1,0,0,0,.753.546l5.924.859a.368.368,0,0,1,.31.26.364.364,0,0,1-.1.387L35.6,42.784a1,1,0,0,0-.288.887l1.011,5.883a.383.383,0,0,1-.557.4l-5.3-2.778a1.005,1.005,0,0,0-.93,0l-5.3,2.778a.372.372,0,0,1-.407-.03.364.364,0,0,1-.151-.369l1.011-5.883a1,1,0,0,0-.288-.887l-4.287-4.167a.364.364,0,0,1-.1-.387.368.368,0,0,1,.31-.26l5.924-.859a1,1,0,0,0,.753-.546l2.648-5.352a.386.386,0,0,1,.69,0Z"/></svg>
                                            </div>
                                            <div class="text-small text-black">IFRA - Certified</div>
                                        </div>
                                        <div class="item">
                                            <div class="icon">
<svg id="Layer_1" height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1"><path d="m139.46 336.8a6 6 0 0 0 6-6v-5.83a6 6 0 0 0 -12 0v5.83a6 6 0 0 0 6 6z"/><path d="m139.46 444.24a6 6 0 0 0 6-6v-88.53a6 6 0 0 0 -12 0v88.53a6 6 0 0 0 6 6z"/><path d="m355.3 44.527a6 6 0 0 0 -10.354 0l-36.67 62.6a48.51 48.51 0 0 0 76.15 58.817 48.548 48.548 0 0 0 7.553-58.814zm20.641 112.931a36.5 36.5 0 0 1 -57.311-44.265l31.491-53.763 31.5 53.761a36.543 36.543 0 0 1 -5.683 44.267z"/><path d="m374.512 125.14a6 6 0 0 0 -7.073 4.688 17.639 17.639 0 0 1 -4.839 9 17.365 17.365 0 0 1 -6.71 4.2 6 6 0 1 0 3.9 11.349 29.711 29.711 0 0 0 19.41-22.163 6 6 0 0 0 -4.688-7.074z"/><path d="m350.12 10.87a100.331 100.331 0 0 0 -81.52 41.75h-183.92a23.28 23.28 0 0 0 0 46.56h6.15v154.42h-6.15a23.275 23.275 0 1 0 0 46.55h6.15v154.42h-6.15a23.28 23.28 0 0 0 0 46.56h282.12a23.28 23.28 0 0 0 0-46.56h-6.16v-154.42h6.16a23.275 23.275 0 1 0 0-46.55h-6.16v-42.324a100.479 100.479 0 0 0 -10.52-200.406zm-265.44 76.31a11.28 11.28 0 1 1 0-22.56h176.474a98.918 98.918 0 0 0 -8.572 22.56zm282.12 379.39a11.28 11.28 0 1 1 0 22.56h-282.12a11.282 11.282 0 0 1 -7.971-19.264 11.172 11.172 0 0 1 7.971-3.3h25.653a6 6 0 1 0 0-12h-7.5v-154.416h245.807v154.42h-213.973a6 6 0 1 0 0 12zm0-200.97a11.275 11.275 0 1 1 0 22.55h-282.12a11.293 11.293 0 0 1 -11.28-11.28 11.275 11.275 0 0 1 11.28-11.27h198.987a6 6 0 0 0 0-12h-180.837v-154.42h147.552a100.533 100.533 0 0 0 98.258 112.631v41.789h-36.973a6 6 0 0 0 0 12zm-12.461-65.873c-1.483.069-2.863.1-4.219.1a88.515 88.515 0 0 1 -86.838-105.427 87.411 87.411 0 0 1 13.2-32.105 88.481 88.481 0 1 1 77.858 137.436z"/></svg>
                                            </div>
                                            <div class="text-small text-black">Imported Oils</div>
                                        </div>
                                        <div class="item">
                                            <div class="icon">

<svg id="Layer_1" enable-background="new 0 0 3000 3000" height="512" viewBox="0 0 3000 3000" width="512" xmlns="http://www.w3.org/2000/svg"><g transform="translate(-1686)"><g transform="matrix(1.061 0 0 1.061 -123.189 -17.905)"><g id="Icons"><path clip-rule="evenodd" d="m2146.013 1269.107c-116.298-21.179-98.868-16.708-188.981 39.35-88.377 68.11-14.801 74.495 23.863 121.566-70.898 90.538 27.108 129.286 96.691 174.566 132.573 71.445 131.605 4.769 198.387-37.943 41.209 141.927-27.474 275.442 24.836 421.809 29.704 88.626 98.25 160.884 110.308 256.594 20.698 151.373 53.544 151.95 98.774 226.365 41.474 77.894 19.852 251.106 125.313 297.864 100.198 39.131 123.19-67.423 163.56-97.162 31.161-26.385 74.694-49.049 108.456-81.834 68.593-66.324 20.332-96.389 27.131-154.646 31.649-111.266 54.656-106.039 23.161-203.7-7.357-26.464-16.061-49.093-16.761-73.238 39.901-66.338 124.151-63.235 176.112-115.475 85.757-86.989 162.336-181.493 244.639-272.012 21.057-23.057 40.76-47.525 68.359-63.078 137.464-93.776-26.684-90.177 177.136-160.106 24.157-11.718 44.864-27.361 59.275-51.916 56.97-100.675-113.516-297.917-41.956-358.082 6.46-5.9 10.216-5.999 18.057-.547 76.883 85.344 86.68 76.379 178.132 83.97-27.494 50.217-48.885 126.679 3.034 180.045 28.016 19.061 67.921 2.325 99.212.265 82.49 196.611 138.601 94.727 130.62 46.881-1.027-41.669-77.814-120.153-23.729-142.144 23.323-6.202 56.63-5.818 73.705-25.088 22.124-29.036 51.293-101.421 38.638-129.777-31.98-70.283-24.818-60.824 17.168-115.588 87.749-97.714 136.361-80.805 153.14-106.91 56.425-111.566-202.003-203.468-287.833-165.988-31.136 11.323-61.131 40.289-90.848 72.647-38.256 47.535-75.502 76.8-72.091 136.872-28.828 5.848-134.531 18.627-164.942 20.654-15.529-27.322-19.496-67.786-45.166-90.518-29.821-32.759-116.021-21.797-125.531 18.517-14.575 77.946 53.086 151.46-72.475 131.444-49.559-15.148-97.961-49.969-143.991-66.243-146.968-38.039-159.007 53.621-280.282-85.328-17.256-17.495-39.072-26.345-71.772-44.77 54.442-78.71 78.553-120.229-14.482-167.442-118.111-62.695-135.212-81.479-90.691-113.252 119.953-73.782 26.467-116.385 31.943-176.386 10.167-30.63 33.943-51.986 50.536-79.437 21.171-31.938 27.311-64.584 14.18-90.979-82.914-132.002-165.828 33.998-225.868-13.962-49.137-25.631-116.511-83.756-152.924-105.533-170.824-76.832-315.853 23.294-179.577 153.919 11.714 18.639 8.111 31.011 7.092 57.094-3.439 56.573-6.948 115.145 13.687 166.213 30.06 54.907 100.138 79.602 64.376 116.527-22.519 26.94-58.474 54.366-78.392 77.892-52.545 63.441-60.574 171.59-126.853 227.513-43.494 41.549-102.959 2.589-155.816 7.2-87.631.525-105.172 106.58-92.179 130.963 8.393 23.266 35.798 39.633 47.801 57.063 27.371 40.322 72.008 116.658 75.818 165.291zm723.164-765.826c-5.685 12.242-19.452 20.704-31.531 28.749-165.577 113.153 104.253 189.825 121.157 220.736 10.433 24.498-38.48 71.568-42.698 88.646-32.115 49.471 48.182 61.93 72.972 83.336 27.918 23.016 53.395 57.737 85.879 78.468 76.187 50.14 167.651-5.155 235.553 30.632 79.662 39.179 191.616 114.783 257.434 26.437 20.922-39.43-3.286-84.858-1.033-123.375 41.387-24.221 63.434-1.805 72.613 32.56 12.107 37.211 25.807 73.238 61.73 67.8 38.513-2.895 163.466-18.939 180.105-24.095 13.751-4.333 22.915-18.038 24.313-35.133 1.009-42.35 1.004-35.849 16.43-57.592 33.413-45.656 80.18-103.31 122.136-117.395 76.349-19.21 223.69 36.167 232.947 98.641-73.485 29.532-96.005 47.636-150.266 105.075-118.63 133.675 18.654 115.111-44.926 223.294-10.68 23.46-9.957 24.707-33.427 27.984-171.218 23.394-18.896 190.578-39.389 207.6-52.313-66.55-40.792-98.859-70.088-106.165-25.877-7.651-57.496 8.041-86.672 7.027-39.791-49.176.656-108.999 17.469-147.459 10.038-18.715 1.403-35.972-14.73-40.73-166.973-6.552-109.017-13.718-192.75-79.993-35.75-24.37-77.227 4.144-93.351 42.977-13.715 37.772-4.02 73.038 5.678 108.608 69.285 205.219 105.541 230.744-47.958 282.382-28.856 11.986-61.613 28.666-72.155 59.801-8.42 28.67-.694 46.416-26.481 63.582-57.223 36.273-75.358 58.717-98.798 85.303-74.969 82.769-145.027 167.521-221.277 247.979-55.242 72.518-177.463 69.801-209.332 158.202-2.763 12.156-1.654 27.973 1.606 43.396 48.239 158.44 24.137 112.885-8.174 231.049-14.039 72.554 36.574 94.803-19.866 141.232-34.572 30.639-80.84 55.713-109.058 81.727-42.415 39.627-59.036 117.41-121.219 78.34-60.483-42.573-60.735-194.683-80.41-242.889-46.896-104.156-89.868-100.136-102.936-218.305-27.065-194.478-139.3-222.032-134.24-417.157-1.416-109.542 63.649-471.489-143.003-255.28-47.183 13.261-130.472-55.244-150.1-66.992-64.204-42.78-11.516-46.669-3.685-90.508-1.772-33.406-44.205-57.711-62.045-73.716 124.166-95.92 98.865-40.969 193.441-45.776 85.361-12.188-28.989-201.08-50.953-231.289-42.551-49.675-66.388-52.397-22.384-111.081 46.399-29.101 112.004 14.61 164.656-.81 127.427-41.269 126.847-219.168 196.489-276.363 27.278-25.803 65.538-57.174 78.712-87.894 9.621-19.645 4.456-44.911-11.089-66.108-18.746-26.808-45.535-43.124-62.238-68.553-37.322-98.188 20.476-192.376-37.345-246.905-99.21-95.023 39.174-111.823 114.63-83.743 44.431 17.143 102.976 75.073 161.203 107.639 95.913 66.053 149.833-47.328 197.379-21.419 77.724 50.077-71.537 126.513-47.39 196.248 5.195 21.523 25.359 59.094 28.465 81.275z" fill-rule="evenodd"/></g></g></g></svg>
                                            </div>
                                            <div class="text-small text-black">Made In India</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Product Info -->
                    </div>
                </div>
            </div>
        </section>
        <!-- /Product Main -->
        <!-- Product Description -->
    <section class="flat-spacing-3">
       <div class="container">
        <div class="flat-animate-tab tab-style-1">
            <ul class="menu-tab menu-tab-1" role="tablist">
                <li class="nav-tab-item" role="presentation">
                    <a href="#descriptions" class="tab-link active" data-bs-toggle="tab">Description</a>
                </li>
                <li class="nav-tab-item" role="presentation">
                    <a href="#policy" class="tab-link" data-bs-toggle="tab">Key Benefits</a>
                </li>
                <li class="nav-tab-item" role="presentation">
                    <a href="#reviews" class="tab-link" data-bs-toggle="tab">How to Use</a>
                </li>
                <li class="nav-tab-item" role="presentation">
                    <a href="#notes" class="tab-link" data-bs-toggle="tab">Perfume Notes</a>
                </li>
                <li class="nav-tab-item" role="presentation">
                    <a href="#faqs" class="tab-link" data-bs-toggle="tab">FAQs</a>
                </li>
            </ul>

            <div class="tab-content">
                {{-- Description --}}
                <div class="tab-pane wd-product-descriptions active show" id="descriptions" role="tabpanel">
                    <div class="tab-descriptions">
                        <p class="h6 desc">{{ $product->description }}</p>
                    </div>
                </div>

                <div class="tab-pane wd-product-descriptions" id="policy" role="tabpanel">
                <div class="tab-policy">
                    <p class="h6">{{ $product->key_benefits }}</p>
                </div>
            </div>

                {{-- How to Use --}}
                <div class="tab-pane wd-product-descriptions" id="reviews" role="tabpanel">
                    <div class="tab-descriptions">
                        <p class="h6 desc">{{ $product->how_to_use }}</p>
                    </div>
                </div>

                           {{-- Perfume Notes --}}
                <div class="tab-pane wd-product-descriptions" id="notes" role="tabpanel">
                    <div class="tab-descriptions">
                        <div class="list-infor">
                            <div class="infor-item">
                                <ul>
                                    @php
                                        $perfumeNotes = json_decode($product->perfume_notes, true);

                                        // Get all note details as [id => title]
                                        $noteTitles = \DB::table('perfume_notes_details')->pluck('title', 'id')->toArray();

                                        // Get all level details as [id => title]
                                        $levelTitles = \DB::table('perfume_notes_level_details')->pluck('title', 'id')->toArray();
                                    @endphp

                                    @if(!empty($perfumeNotes))
                                        @foreach($perfumeNotes as $note)
                                            <li>
                                                <h6 class="fw-6 text-black title">
                                                    {{ $levelTitles[$note['level_id']] ?? 'Unknown' }}
                                                </h6>
                                                <div class="h6">
                                                    @if(!empty($note['note_ids']))
                                                        @foreach($note['note_ids'] as $nid)
                                                            {{ $noteTitles[$nid] ?? 'Unknown Note' }}{{ !$loop->last ? '/' : '' }}
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>



                            {{-- FAQs --}}
                <div class="tab-pane wd-product-descriptions" id="faqs" role="tabpanel">
                    <ul class="faq-list">
                        @php
                            $faqs = json_decode($product->faqs, true); // decode JSON to associative array
                        @endphp

                        @if(!empty($faqs))
                            @foreach($faqs as $index => $faq)
                                <li class="faq-item" id="faq-{{ $index }}">
                                    <div class="faq_wrap" id="faq-wrap-{{ $index }}">
                                        <div class="accordion-faq accor-mn-pl">
                                            <div class="accordion-title {{ $index === 0 ? '' : 'collapsed' }}"
                                                data-bs-target="#faq-{{ $index }}-collapse"
                                                role="button" data-bs-toggle="collapse"
                                                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                                aria-controls="faq-{{ $index }}-collapse">
                                                <span class="text h5">{{ $index + 1 }}. {{ $faq['question'] }}</span>
                                                <span class="icon"><span class="ic-accordion-custom"></span></span>
                                            </div>
                                            <div id="faq-{{ $index }}-collapse"
                                                class="collapse {{ $index === 0 ? 'show' : '' }}"
                                                data-bs-parent="#faq-wrap-{{ $index }}">
                                                <div class="accordion-body">
                                                    <p class="h6">{{ $faq['answer'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>


            </div>
        </div>
    </div>
</section>

        <!-- /Product Description -->
        <!-- Related -->
        <section class="flat-spacing-3 pt-0">
    <div class="container">
        <h1 class="sect-title text-center">Related Products</h1>
        <div dir="ltr" class="swiper tf-swiper wrap-sw-over" 
             data-preview="4" data-tablet="3" data-mobile-sm="2" data-mobile="2"
             data-space-lg="48" data-space-md="30" data-space="12" 
             data-pagination="2" data-pagination-sm="2" data-pagination-md="3" data-pagination-lg="4">
            <div class="swiper-wrapper">
                @foreach($relatedProducts as $related)
                    <div class="swiper-slide">
                        <div class="card-product">
                            <div class="card-product_wrapper">
                                <a href="{{ route('product.details', [$category->slug, $subcategory->slug, $related->slug]) }}" class="product-img">
                                  @php
    $productImages = json_decode($related->images, true);
@endphp

<img class="lazyload img-product" 
     src="{{ asset('signage/home/productimage/' . ($productImages[0] ?? 'default.png')) }}" 
     alt="{{ $related->name }}">

<img class="lazyload img-hover" 
     src="{{ asset('signage/home/productimage/' . ($productImages[1] ?? $productImages[0] ?? 'default.png')) }}" 
     alt="{{ $related->name }}">


                                </a>
                                <ul class="product-action_list">
                                    <li>
                                        <a href="#shoppingCart" data-bs-toggle="offcanvas" class="hover-tooltip tooltip-left box-icon">
                                            <span class="icon icon-shopping-cart-simple"></span>
                                            <span class="tooltip">Add to cart</span>
                                        </a>
                                    </li>
                                    <li class="wishlist">
                                        <a href="javascript:void(0);" class="hover-tooltip tooltip-left box-icon">
                                            <span class="icon icon-heart"></span>
                                            <span class="tooltip">Add to Wishlist</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#quickView" data-bs-toggle="modal" class="hover-tooltip tooltip-left box-icon">
                                            <span class="icon icon-view"></span>
                                            <span class="tooltip">Quick view</span>
                                        </a>
                                    </li>
                                </ul>
                                @if($related->is_hot ?? false)
                                    <ul class="product-badge_list">
                                        <li class="product-badge_item h6 hot">Hot</li>
                                    </ul>
                                @endif
                            </div>
                            <div class="card-product_info">
                                <a href="{{ route('product.details', [$category->slug, $subcategory->slug, $related->slug]) }}" class="name-product h4 link">{{ $related->name }}</a>
                                <div class="price-wrap">
                                    @if($related->offer_price)
                                        <span class="price-old h6 fw-normal">Rs.{{ $related->price }}</span>
                                        <span class="price-new h6">Rs.{{ $related->offer_price }}</span>
                                    @else
                                        <span class="price-new h6">Rs.{{ $related->price }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="sw-dot-default tf-sw-pagination"></div>
        </div>
    </div>
</section>

        <!-- /Related -->
        <!-- Footer -->
        <!-- Footer -->
       @include('components.frontend.footer')    
        <!-- /Footer -->
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
    <!-- Toolbar -->
    <div class="tf-toolbar-bottom">
        <div class="toolbar-item">
            <a href="shop-default.html">
                <span class="toolbar-icon">
                    <i class="icon icon-storefront"></i>
                </span>
                <span class="toolbar-label">Shop</span>
            </a>
        </div>
        <div class="toolbar-item">
            <a href="#search" data-bs-toggle="modal">
                <span class="toolbar-icon">
                    <i class="icon icon-magnifying-glass"></i>
                </span>
                <span class="toolbar-label">Search</span>
            </a>
        </div>
        <div class="toolbar-item">
            <a href="account-page.html">
                <span class="toolbar-icon">
                    <i class="icon icon-user"></i>
                </span>
                <span class="toolbar-label">Account</span>
            </a>
        </div>
        <div class="toolbar-item">
            <a href="wishlist.html">
                <span class="toolbar-icon">
                    <i class="icon icon-heart"></i>
                    <span class="toolbar-count">7</span>
                </span>
                <span class="toolbar-label">Wishlist</span>
            </a>
        </div>
        <div class="toolbar-item">
            <a href="view-cart.html">
                <span class="toolbar-icon">
                    <i class="icon icon-shopping-cart-simple"></i>
                    <span class="toolbar-count">24</span>
                </span>
                <span class="toolbar-label">Cart</span>
            </a>
        </div>
    </div>
    <!-- /Toolbar -->
    <!-- Javascript -->
            @include('components.frontend.main-js')

    
</body>
</html>