@php
use Illuminate\Support\Facades\DB;

$footerData = DB::table('footer_details')->first(); // replace with your actual table name
@endphp

<footer class="tf-footer style-color-white style-4 bg-black">
    <div class="footer-body">
        <div class="container">
            <div class="row">
                <!-- Contact Us -->
                <div class="col-xl-3 col-sm-6 mb_30 mb-xl-0">
                    <div class="footer-col-block">
                        <p class="footer-heading footer-heading-mobile">{{ $footerData->footer_heading ?? 'Contact Us' }}</p>
                        <div class="tf-collapse-content">
                            <ul class="footer-contact">
                                <li>
                                    <i class="icon icon-map-pin"></i>
                                    <span class="br-line"></span>
                                    <a href="https://www.google.com/maps?q={{ urlencode($footerData->address_line1.' '.$footerData->address_line2) }}"
                                        target="_blank" class="h6 link">
                                        {{ $footerData->address_line1 }} <br class="d-none d-lg-block">
                                        {{ $footerData->address_line2 }}
                                    </a>
                                </li>
                                <li>
                                    <i class="icon icon-phone"></i>
                                    <span class="br-line"></span>
                                    <a href="tel:{{ $footerData->phone }}" class="h6 link">{{ $footerData->phone }}</a>
                                </li>
                                <li>
                                    <i class="icon icon-envelope-simple"></i>
                                    <span class="br-line"></span>
                                    <a href="mailto:{{ $footerData->email }}" class="h6 link">{{ $footerData->email }}</a>
                                </li>
                            </ul>
                            <div class="social-wrap">
                                <ul class="tf-social-icon style-2">
                                    <li>
                                        <a href="{{ $footerData->facebook_link }}" target="_blank" class="social-facebook">
                                            <span class="icon"><i class="icon-fb"></i></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ $footerData->instagram_link }}" target="_blank" class="social-instagram">
                                            <span class="icon"><i class="icon-instagram-logo"></i></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ $footerData->twitter_link }}" target="_blank" class="social-x">
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
                <!-- Newsletter -->
                <div class="col-xl-4 col-sm-6">
                    <div class="footer-col-block">
                        <p class="footer-heading footer-heading-mobile">{{ $footerData->newsletter_heading ?? 'Let’s keep in touch' }}</p>
                        <div class="tf-collapse-content">
                            <div class="footer-newsletter">
                                <p class="h6 caption text-main-5">
                                    {{ $footerData->newsletter_description }}
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
                                        <input id="remember" type="checkbox" class="tf-check style-3 style-white">
                                        <label for="remember" class="h6 text-main-5">
                                            By clicking subscribe, you agree to the  
                                            <a href="#" class="text-decoration-underline link text-main-5">Terms of Service</a>
                                            and 
                                            <a href="#" class="text-decoration-underline link text-main-5">Privacy Policy</a>.
                                        </label>
                                    </div>
                                    <div id="subscribe-msg"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
<br><br>
           <div class="footer-bottom">
                <div class="container">
                    <div class="inner-bottom">

                        <p class="h6">Copyright © 2025 Signage Wellness. All rights reserved. Designed By <a
                                class="link" href="https://www.matrixbricks.com/" target="_blank">Matrix Bricks</a></p>

                        <div class="list-hor flex-wrap">
                            <span class="h6">Payment:</span>
                            <ul class="payment-method-list">
                                <li><img src="{{ asset('frontend/assets/images/payment/visa-2.svg')}}" alt="Payment"></li>
                                <li><img src="{{ asset('frontend/assets/images/payment/master-card-2.svg')}}" alt="Payment"></li>
                                <li><img src="{{ asset('frontend/assets/images/payment/amex-2.svg')}}" alt="Payment"></li>
                                <li><img src="{{ asset('frontend/assets/images/payment/discover-2.svg')}}" alt="Payment"></li>
                                <li><img src="{{ asset('frontend/assets/images/payment/paypal-2.svg')}}" alt="Payment"></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
           
</footer>
