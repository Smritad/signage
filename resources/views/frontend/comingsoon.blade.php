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

    <!-- Page Title -->
    <section class="s-page-title">
        <div class="container">
            <div class="content text-center">
                <h1 class="title-page">Coming Soon</h1>
                <!-- <ul class="breadcrumbs-page"> -->
                    <!-- <li><a href="{{ url('/') }}" class="h6 link">Home</a></li>
                    <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                    <li>
                        <h6 class="current-page fw-normal">Coming Soon</h6>
                    </li> -->
                <!-- </ul> -->
            </div>
        </div>
    </section>

    <!-- Coming Soon Section -->
    <section class="coming-soon-section" style="text-align:center; padding:100px 20px; background:#f2f2f2;">
        <div class="container">
            <h1 class="coming-soon-title" style="font-size:3rem; font-weight:700; margin-bottom:20px;">
                Coming Soon
            </h1>
            <p class="coming-soon-text" style="font-size:1.2rem; margin-bottom:30px; color:#555;">
                We are working hard to launch our exciting products. Stay tuned!
            </p>
            <a href="{{ url('/') }}" class="btn-back-home" 
               style="display:inline-block; padding:12px 30px; background:#ab924a; color:#fff; border-radius:5px; font-size: 17px; text-decoration:none;">
                Back to Home
            </a>
        </div>
    </section>


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