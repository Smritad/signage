
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
            <h1 class="title-page">Contact Us</h1>
            <ul class="breadcrumbs-page">
              <li><a href="{{ route('frontend.index') }}" class="h6 link">Home</a></li>
              <li class="d-flex"><i class="icon icon-caret-right"></i></li>
              <li>
                <h6 class="current-page fw-normal">Contact Us</h6>
              </li>
            </ul>
          </div>
        </div>
      </section>
      <!-- /Page Title -->
      <!-- Section Product -->
      <div class="contact-us-page flat-spacing">
        <div class="google-map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3766.361717123811!2d73.04057407503242!3d19.26662998197762!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7bd4efdc79fbd%3A0x707dfb44144cbf95!2sLogassadivine%20Pvt%20ltd!5e0!3m2!1sen!2sin!4v1760082735723!5m2!1sen!2sin" width="100%" height="500"  style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <div class="container">
          <div class="row form-contact">
            <div class="col-lg-8">
              <div class="form-message">
                <h2 class="title">Get In Touch With Us!</h2>
                    <form action="{{ route('contact.send') }}" method="POST" class="form-change_pass">
                        @csrf
                        <div class="form_content">
                            <div class="cols tf-grid-layout sm-col-2">
                                <fieldset>
                                    <input type="text" name="name" placeholder="Your Name*" required>
                                </fieldset>
                                <fieldset>
                                    <input type="email" name="email" placeholder="Email Address*" required>
                                </fieldset>
                            </div>
                            <div class="cols tf-grid-layout sm-col-2">
                                <fieldset>
                                    <input type="text" name="contact" placeholder="Contact Us*" required>
                                </fieldset>
                                <fieldset>
                                    <input type="text" name="company" placeholder="Company*">
                                </fieldset>
                            </div>
                            <fieldset>
                                <textarea name="message" placeholder="Message" required></textarea>
                            </fieldset>
                            <button type="submit" class="btn-submit_form tf-btn animate-btn w-100 fw-bold">
                                Submit
                            </button>
                        </div>
                    </form>
              </div>
            </div>
            <style>.text-main-2 {
                color: #ffffff !important;
            }
            
            .email-link {
                text-transform: none !important;
            }
            </style>
            <div class="col-lg-4">
              <div class="form-contact-information">
                <form class="stelina-contact-info">
                    <h2 class="title">
                        {{ $footer->footer_heading ?? 'Contact Information' }}
                    </h2>
                
                    <div class="info">
                        <div class="item address">
                    <i class="icon icon-map-pin"></i>
                    <span class="text" >
                        @if($footer->address_line1 || $footer->address_line2)
                            <a href="https://www.google.com/maps/search/{{ urlencode($footer->address_line1 . ' ' . $footer->address_line2) }}" 
                               target="_blank" class="text-decoration-none text-main-2">
                               {{ $footer->address_line1 ?? '' }} {{ $footer->address_line2 ?? '' }}
                            </a>
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                
                
                       <div class="item phone">
                    <i class="icon icon-phone"></i>
                    <span class="text">
                        @if($footer->phone)
                            <a href="tel:{{ preg_replace('/\s+/', '', $footer->phone) }}" class="text-decoration-none text-main-2">
                                {{ $footer->phone }}
                            </a>
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                
                <div class="item email">
                    <i class="icon icon-envelope-simple"></i>
                    <span class="text">
                        @if($footer->email)
                            <a href="mailto:{{ $footer->email }}" class="email-link text-decoration-none text-main-2">
                                {{ $footer->email }}
                            </a>
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                
                
                
                <div class="item timing">
                    <i class="icon icon-alarm"></i>
                    <span class="text">
                        Customer care time – 10am to 8pm
                    </span>
                </div>
                
                
                </div>
                
                    <div class="socials">
                        @if($footer->facebook_link)
                            <a href="{{ $footer->facebook_link }}" class="social-item" target="_blank">
                                <i class="icon-fb"></i>
                            </a>
                        @endif
                
                        @if($footer->instagram_link)
                            <a href="{{ $footer->instagram_link }}" class="social-item" target="_blank">
                                <i class="icon-instagram-logo"></i>
                            </a>
                        @endif
                
                        @if($footer->twitter_link)
                            <a href="{{ $footer->twitter_link }}" class="social-item" target="_blank">
                                <i class="icon-x"></i>
                            </a>
                        @endif
                    </div>
                </form>
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