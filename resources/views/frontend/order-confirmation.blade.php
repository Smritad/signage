<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

                   @include('components.frontend.head')    
    <meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>


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
                    <h1 class="title-page">Order Confirmation</h1>
                    <ul class="breadcrumbs-page">
                        <li><a href="{{ route('frontend.index') }}" class="h6 link">Home</a></li>
                        <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                        <li>
                            <h6 class="current-page fw-normal">Order Confirmation</h6>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
        <!-- /Page Title -->
        <!-- Check Out -->
<section class="flat-spacing">
  <div class="container">
    <div class="row">
      
      

      <!-- Thank You Section -->
      <div class="thanks-wrap py-5" style="background-color: #f9f9f9;">
        <div class="container">
          <div class="row justify-content-center text-center">
            <div class="col-lg-8">
              <div class="thank-you-box bg-white p-4 p-md-5 shadow-sm rounded">

                <!-- Heading -->
                <h3 class="mb-3" style="color: #1d138bff;">
                  Thank You for Your <span style="color: inherit;">Purchase!</span>
                </h3>
                <p class="mb-4">
                  Your order has been successfully placed. A confirmation email has been sent to
                  <a href="mailto:{{ $order->customer_email }}">{{ $order->customer_email }}</a>.
                </p>

                <hr class="mb-4">

                <!-- Customer Details -->
                <h5 class="text-start mb-3 fw-bold">Customer Details</h5>
                <div class="bg-light p-3 rounded mb-4">
                  <div class="d-flex justify-content-between mb-2"><span>Name</span><strong>{{ $order->customer_name ?? 'N/A' }}</strong></div>
                  <div class="d-flex justify-content-between mb-2"><span>Email ID</span><strong>{{ $order->customer_email ?? 'N/A' }}</strong></div>
                  <div class="d-flex justify-content-between mb-2"><span>Number</span><strong>{{ $order->customer_phone ?? 'N/A' }}</strong></div>
                  <div class="d-flex justify-content-between mb-2"><span>Shipping Address</span><strong class="text-end">{{ $order->shipping_address ?? 'N/A' }}</strong></div>
                  <div class="d-flex justify-content-between"><span>Billing Address</span><strong class="text-end">{{ $order->billing_address ?? 'N/A' }}</strong></div>
                </div>

                <!-- Order Summary -->
                <h5 class="text-start mb-3 fw-bold">Order Summary</h5>

                @php
                  $productNames = json_decode($order->product_names, true) ?? [];
                  $quantities   = json_decode($order->quantities, true) ?? [];
                  $prices       = json_decode($order->prices, true) ?? [];
                  $prints       = json_decode($order->prints, true) ?? [];
                @endphp

                <div class="bg-light p-3 rounded mb-4">
                  <div class="d-flex justify-content-between mb-2"><span>Order ID</span><strong>#{{ $order->order_id }}</strong></div>
                </div>

                @foreach($productNames as $index => $productName)
                  <div class="border p-3 rounded mb-3 text-start">
                    <div class="d-flex justify-content-between mb-2"><span>Product</span><strong>{{ $productName ?? 'N/A' }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Quantity</span><strong>{{ $quantities[$index] ?? 'N/A' }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Fragrance</span><strong>{{ $prints[$index] ?? 'Front & Back Print' }}</strong></div>
                    <div class="d-flex justify-content-between">
                      <span>Price</span>
                      <strong>
                        <i class="fa fa-inr"></i>
                        {{ number_format(($prices[$index] ?? 0) * ($quantities[$index] ?? 0)) }} INR
                      </strong>
                    </div>
                  </div>
                @endforeach

                <!-- Total -->
                <div class="bg-light p-3 rounded mb-4">
                  <div class="d-flex justify-content-between fw-bold">
                    <span>Total</span>
                    <span><i class="fa fa-inr"></i> {{ number_format($order->total_price ?? 0) }} INR</span>
                  </div>
                </div>

                <!-- Button -->
                <div class="text-center">
                  <a href="{{ route('frontend.index') }}" class="btn px-4 py-2" style="background-color: rgb(157, 126, 84); color: white; border: none;">
                    Continue Shopping
                  </a>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- /Thank You Section -->

    </div>
  </div>
</section>



        <!-- /Check Out -->
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

  
  
  