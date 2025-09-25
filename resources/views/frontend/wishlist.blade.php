
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
                    <h1 class="title-page">Wishlist</h1>
                    <ul class="breadcrumbs-page">
                        <li><a href="index.html" class="h6 link">Home</a></li>
                        <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                        <li>
                            <h6 class="current-page fw-normal">Wishlist</h6>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
        <!-- /Page Title -->
        <!-- Wishlist -->
      


<!-- Wishlist -->
<!-- Wishlist -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="flat-spacing">
    <div class="container">
        <div class="tf-grid-layout tf-col-2 md-col-3 xl-col-4 wrapper-wishlist">

            @forelse($wishlist as $item)
                @php
                    $images = json_decode($item->images, true);
                    if (!is_array($images)) {
                        $images = explode(',', $item->images ?? '');
                    }
                    $firstImage = $images[0] ?? 'default.png';

                    $subCategory = \App\Models\SabCategoryDetails::find($item->sub_category_id);
                    $category = \App\Models\CategoryDetails::find($item->category_id);

                    $productUrl = route('product.details', [
                        'cat' => $category?->slug ?? '',
                        'sabcat' => $subCategory?->slug ?? '',
                        'slug' => $item->slug
                    ]);
                @endphp

                <div class="card-product grid style-2 has-size" id="wishlist-item-{{ $item->id }}">
                    <div class="card-product_wrapper">
                        <!-- Product Image -->
                        <a href="{{ $productUrl }}" class="product-img">
                            <img class="lazyload img-product"
                                 src="{{ asset('signage/home/productimage/' . $firstImage) }}"
                                 data-src="{{ asset('signage/home/productimage/' . $firstImage) }}"
                                 alt="{{ $item->product_name }}">
                            <img class="lazyload img-hover"
                                 src="{{ asset('signage/home/productimage/' . $firstImage) }}"
                                 data-src="{{ asset('signage/home/productimage/' . $firstImage) }}"
                                 alt="{{ $item->product_name }}">
                        </a>

                        <!-- Remove Button -->
                        <span class="remove box-icon remove-wishlist" data-id="{{ $item->id }}">
                            <i class="icon icon-trash"></i>
                        </span>

                        <!-- Product Actions -->
                        <ul class="product-action_list">
                            <li>
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                    <button type="submit" class="hover-tooltip box-icon">
                                        <span class="icon icon-shopping-cart-simple"></span>
                                        <span class="tooltip">Add to cart</span>
                                    </button>
                                </form>
                            </li>
                            <li class="compare">
                                <a href="#compare" data-bs-toggle="offcanvas" class="hover-tooltip box-icon ">
                                    <span class="icon icon-compare"></span>
                                    <span class="tooltip">Compare</span>
                                </a>
                            </li>
                            <li>
                                <a href="#quickView" data-bs-toggle="modal" class="hover-tooltip box-icon">
                                    <span class="icon icon-view"></span>
                                    <span class="tooltip">Quick view</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-product_info">
                        <!-- Product Name -->
                        <a href="{{ $productUrl }}" class="name-product h4 link">
                            {{ $item->product_name }}
                        </a>

                        <!-- Price Section -->
                        <div class="price-wrap">
                            @if($item->offer_price && $item->offer_price < $item->price)
                                <span class="price-old h6 fw-normal">Rs.{{ number_format($item->price, 2) }}</span>
                                <span class="price-new h6">Rs.{{ number_format($item->offer_price, 2) }}</span>
                            @else
                                <span class="price-new h6">Rs.{{ number_format($item->price, 2) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center">Your wishlist is empty.</p>
            @endforelse

        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".remove-wishlist").forEach(function (btn) {
        btn.addEventListener("click", function () {
            let id = this.dataset.id;

            fetch("/wishlist/remove/" + id, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Accept": "application/json",
                },
            })
            .then(res => res.json())
            .then(data => {
                // ✅ Reload page after deletion
                location.reload();
            })
            .catch(err => console.error(err));
        });
    });
});
</script>




<!-- /Wishlist -->

<!-- /Wishlist -->


        <!-- /Wishlist -->
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

          @include('components.frontend.main-js')


</body>

</html>