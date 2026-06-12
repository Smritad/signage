
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
            <h1 class="title-page">Return & Refund Policy</h1>
            <ul class="breadcrumbs-page">
              <li><a href="{{ route('frontend.index') }}" class="h6 link">Home</a></li>
              <li class="d-flex"><i class="icon icon-caret-right"></i></li>
              <li>
                <h6 class="current-page fw-normal">Return & Refund Policy</h6>
              </li>
            </ul>
          </div>
        </div>
      </section>
      <!-- /Page Title -->
      <!-- Section Product -->
<div class="about-us-page flat-spacing">
  <div class="container">
    <div class="row align-items-center">
      
     

      
          <!-- Description -->
          <div>
            {!! $policy->description !!}
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
      <!-- /Section Product -->
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
    <!-- Javascript -->
                    @include('components.frontend.main-js')

  </body>
</html>