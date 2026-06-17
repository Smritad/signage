<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

@include('components.frontend.head')
<meta name="csrf-token" content="{{ csrf_token() }}">



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

        <section class="s-page-title">
            <div class="container">
                <div class="content">
                    <h1 class="title-page">Checkout</h1>
                    <ul class="breadcrumbs-page">
                        <li><a href="{{ route('frontend.index') }}" class="h6 link">Home</a></li>
                        <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                        <li><h6 class="current-page fw-normal">Checkout</h6></li>
                    </ul>
                </div>
            </div>
        </section>
@if(session('error'))
<script>
document.addEventListener('DOMContentLoaded', () => notyf.error(@json(session('error'))));
</script>
@endif
 
        <section class="flat-spacing">
            <div class="container">
                <div class="row">

                    {{-- LEFT: form --}}
                    <div class="col-lg-7">
                        <div class="tf-page-checkout mb-lg-0">
                            <div class="checkout-card p-4 shadow-sm rounded">

                                @php $user = Auth::guard('custom')->user(); @endphp

                               {{--
    Drop-in replacement for the @if(!$user) ... @endif block on your checkout page.
    Includes Google + Facebook buttons above your existing OTP signup form.
--}}

@if(!$user)
    
    <form id="redirectToLoginForm" action="{{ route('user.login') }}" method="GET" class="d-none">
        <input type="hidden" name="from_checkout" value="1">
    </form>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var loginLink = document.getElementById('checkoutLoginLink');
        if (loginLink) {
            loginLink.addEventListener('click', function (e) {
                e.preventDefault();
                document.getElementById('redirectToLoginForm').submit();
            });
        }
    });
    </script>

    <h2 class="checkout-login-heading">Register with Email</h2>

    <div class="checkout-login-wrap">
        <form id="otpForm">@csrf
            <div class="mb-3">
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email Address" required>
            </div>
            <button type="button" id="sendOtpBtn" class="tf-btn animate-btn w-100">
                <span id="btnText">Send OTP</span>
            </button>
            <div id="otpSection" class="d-none">
                <div class="mb-3 mt-3">
                    <input type="text" name="otp" id="otp" class="form-control" placeholder="Enter OTP" required>
                </div>
                <div class="pw-field-wrap">
                    <input type="password" id="password" class="form-control" placeholder="Create Password" required>
                    <button type="button" class="pw-toggle" onclick="togglePassword('password')">
                        <i class="icon icon-view"></i>
                    </button>
                </div>
                <div class="pw-field-wrap">
                    <input type="password" id="confirm_password" class="form-control" placeholder="Confirm Password" required>
                    <button type="button" class="pw-toggle" onclick="togglePassword('confirm_password')">
                        <i class="icon icon-view"></i>
                    </button>
                </div>
                <button type="submit" class="tf-btn animate-btn w-100">Verify &amp; Create Account</button>
            </div>
            <div id="otpMessage" class="mt-2"></div>
            <input type="hidden" id="email_hidden" name="email">
        </form>
    </div>
    <br>
    <h2 class="checkout-title type-semibold mb-3">
        Already have an account?
        <a href="#" id="checkoutLoginLink">Login here</a>
    </h2>
     <div class="social-divider"><span>or</span></div>
    {{-- Social login --}}
    <div class="social-login-wrap mb-3">
        <a href="{{ route('social.redirect', 'google') }}" class="social-btn social-google">
            <svg viewBox="0 0 48 48" aria-hidden="true">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
            </svg>
            <span>Continue with Google</span>
        </a>
        <a href="{{ route('social.redirect', 'facebook') }}" class="social-btn social-facebook">
            <svg viewBox="0 0 24 24" aria-hidden="true" fill="#fff">
                <path d="M24 12.073C24 5.404 18.627 0 12 0S0 5.404 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
            </svg>
            <span>Continue with Facebook</span>
        </a>
       
    </div>

    
    <hr class="my-4">
@endif
                               

                                @php
                                    $fullName  = $user->name ?? '';
                                    $nameParts = explode(' ', $fullName, 2);
                                    $firstName = $nameParts[0] ?? '';
                                    $lastName  = $nameParts[1] ?? '';
                                @endphp

                              

<form id="checkoutForm" class="tf-checkout-cart-main">

    <div class="box-ip-checkout estimate-shipping">

        <h2 class="title type-semibold">Information</h2>

        <div class="form_content">

            {{-- Name --}}
            <div class="cols tf-grid-layout sm-col-2">

                <fieldset>
                    <input type="text"
                           name="first_name"
                           placeholder="First name*"
                           value="{{ old('first_name', $firstName) }}"
                           required>

                    <span class="text-danger error-msg d-none"></span>
                </fieldset>

                <fieldset>
                    <input type="text"
                           name="last_name"
                           placeholder="Last name*"
                           value="{{ old('last_name', $lastName) }}"
                           required>

                    <span class="text-danger error-msg d-none"></span>
                </fieldset>

            </div>

            {{-- Email / Phone --}}
            <div class="cols tf-grid-layout sm-col-2">

                <fieldset>
                    <input type="email"
                           name="email"
                           placeholder="Email address*"
                           value="{{ old('email', $user->email ?? '') }}"
                           required>

                    <span class="text-danger error-msg d-none"></span>
                </fieldset>

                <fieldset>
                    <input type="number"
                           name="phone"
                           placeholder="Phone number*"
                           value="{{ old('phone', $user->mobile_no ?? '') }}"
                           required>

                    <span class="text-danger error-msg d-none"></span>
                </fieldset>

            </div>

            {{-- Country / State / City --}}
            <div class="cols tf-grid-layout sm-col-3">

                {{-- Country --}}
                <fieldset>

                    <label>Country*</label>

                    <select name="user_country" id="user_country" required>

                        <option value="">Select Country*</option>

                        @foreach($fetch_all_countries as $c)

                            <option value="{{ $c->id }}"
                                {{ ((string) old('user_country', $user->country ?? '') === (string) $c->id) ? 'selected' : '' }}>

                                {{ $c->name }}

                            </option>

                        @endforeach

                    </select>

                    <span class="text-danger error-msg d-none"></span>

                </fieldset>

                {{-- State --}}
                <fieldset>

                    <label>State*</label>

                    <select name="user_state" id="user_state" required>

                        <option value="">Select State*</option>

                        @foreach($fetch_all_states as $s)

                            <option value="{{ $s->id }}"
                                {{ ((string) old('user_state', $user->state ?? '') === (string) $s->id) ? 'selected' : '' }}>

                                {{ $s->name }}

                            </option>

                        @endforeach

                    </select>

                    <span class="text-danger error-msg d-none"></span>

                </fieldset>

                {{-- City --}}
                <fieldset>

                    <label>City*</label>

                    <select name="user_city" id="user_city" required>

                        <option value="">Select City*</option>

                        @foreach($fetch_all_cities as $ct)

                            <option value="{{ $ct->id }}"
                                {{ ((string) old('user_city', $user->city ?? '') === (string) $ct->id) ? 'selected' : '' }}>

                                {{ $ct->name }}

                            </option>

                        @endforeach

                    </select>

                    <span class="text-danger error-msg d-none"></span>

                </fieldset>

            </div>

            {{-- Street / Postal --}}
            <div class="cols tf-grid-layout sm-col-2">

                <fieldset>

                    <input type="text"
                           name="street"
                           placeholder="Street*"
                           value="{{ old('street', $user->street ?? '') }}"
                           required>

                    <span class="text-danger error-msg d-none"></span>

                </fieldset>

                <fieldset>

                    <input type="number"
                           name="postal_code"
                           placeholder="Postal code*"
                           value="{{ old('postal_code', $user->postal_code ?? '') }}"
                           required>

                    <span class="text-danger error-msg d-none"></span>

                </fieldset>

            </div>

            {{-- Billing / Shipping --}}
            <div class="cols tf-grid-layout sm-col-2">

                <fieldset>

                    <label>Billing Address*</label>

                    <textarea name="billing_address"
                              class="textarea-md"
                              placeholder="Room no, street, area, city, state"
                              required>{{ old('billing_address', $user->billing_address ?? '') }}</textarea>

                    <span class="text-danger error-msg d-none"></span>

                </fieldset>

                <fieldset>

                    <label>Shipping Address*</label>

                    <textarea name="shipping_address"
                              class="textarea-md"
                              placeholder="Room no, street, area, city, state"
                              required>{{ old('shipping_address', $user->shipping_address ?? '') }}</textarea>

                    <span class="text-danger error-msg d-none"></span>

                </fieldset>

            </div>

            {{-- Same as billing --}}
            <label class="billing-address-label-sec">

                <input type="checkbox"
                       name="same_as_billing"
                       class="billing-address-checkbox-cc">

                Same as Billing Address

            </label>

            {{-- Order Note --}}
            <textarea name="order_note"
                      class="textarea-lg"
                      placeholder="Note about your order">{{ old('order_note') }}</textarea>

            {{-- Payment --}}
            <h3 class="payment-heading">Payment Method</h3>

            <div class="payment-methods">

                <label>

                    <input type="radio"
                           name="payment_mode"
                           value="online"
                           checked>

                    <span class="pm-icon">
                        <i class="icon icon-credit-card"></i>
                    </span>

                    <span class="pm-content">
                        <p class="pm-title">Pay Online</p>
                        <p class="pm-sub">Card, UPI, Netbanking, Wallet</p>
                    </span>

                </label>

                <label>

                    <input type="radio"
                           name="payment_mode"
                           value="cod">

                    <span class="pm-icon">
                        <i class="icon icon-wallet"></i>
                    </span>

                    <span class="pm-content">
                        <p class="pm-title">Cash on Delivery</p>
                        <p class="pm-sub">Pay when your order arrives</p>
                    </span>

                </label>

            </div>

        </div>

    </div>

    {{-- Submit --}}
    <div class="button_submit">

        <button type="submit"
                id="payNowButton"
                class="tf-btn animate-btn w-100">

            <span id="payBtnText">Proceed to Payment</span>

        </button>

    </div>

</form>

                            </div>
                        </div>
                    </div>

                    <script>
                    document.addEventListener('change', function (e) {
                        if (e.target.name === 'payment_mode') {
                            document.getElementById('payBtnText').innerText =
                                e.target.value === 'cod' ? 'Place Order (COD)' : 'Proceed to Payment';
                        }
                    });

                    document.getElementById('payNowButton').addEventListener('click', function (e) {

                        @if(!Auth::guard('custom')->check())
                    
                            e.preventDefault();
                    
                            notyf.open({
                                type: 'custom-warning',
                                message: 'Please login first using OTP before proceeding.'
                            });
                    
                            return false;
                    
                        @endif
                    
                   
                    });
                    </script>

                    {{-- RIGHT: Order summary --}}
                    <div class="col-lg-5">
                        <div class="fl-sidebar-cart sticky-top">
                            <div class="box-your-order p-4 border rounded shadow-sm bg-white">
                                <h2 class="title type-semibold mb-4 text-center">Your Order Summary</h2>

                                @php
                                    $shipping = 0;
                                    $offers   = [];
                                    $normals  = [];

                                    if (!empty($cart)) {
                                        foreach ($cart as $item) {
                                            if (!empty($item['is_offer']) && $item['is_offer']) {
                                                $offers[] = $item;
                                            } else {
                                                $normals[] = $item;
                                            }
                                        }
                                    }

                                    $offerMrpSubtotal = 0;
                                    $offerSubtotal    = 0;
                                    foreach ($offers as $o) {
                                        $offerMrpSubtotal += ($o['mrp']   ?? $o['price']) * 1;
                                        $offerSubtotal    += ($o['price'] ?? 0) * 1;
                                    }
                                    $offerSavings = max(0, $offerMrpSubtotal - $offerSubtotal);

                                    // ── Normal products: MRP + savings (offer-aware) ──
                                    $normalMrpSubtotal = 0;
                                    $normalSubtotal    = 0;
                                    foreach ($normals as $it) {
                                        $qtyN               = $it['quantity'] ?? 1;
                                        $normalSubtotal    += ($it['price'] ?? 0) * $qtyN;
                                        $normalMrpSubtotal += ($it['mrp'] ?? $it['price'] ?? 0) * $qtyN;
                                    }
                                    $normalSavings = max(0, $normalMrpSubtotal - $normalSubtotal);

                                    $finalSubtotal = $offerSubtotal + $normalSubtotal;
                                    $totalSavings  = $offerSavings + $normalSavings;
                                    $total         = $finalSubtotal + $shipping;

                                    function resolveCheckoutImage(string $image, bool $isOffer): string
                                    {
                                        $image = trim($image);
                                        if (empty($image)) return asset('images/no-image.png');
                                        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
                                            return $image;
                                        }
                                        $image   = trim($image, '"\'');
                                        $decoded = json_decode($image, true);
                                        if (is_string($decoded)) {
                                            $image = trim($decoded, '"\'');
                                        } elseif (is_array($decoded) && !empty($decoded[0])) {
                                            $image = trim($decoded[0], '"\'');
                                        }
                                        if (empty($image)) return asset('images/no-image.png');
                                        return $isOffer
                                            ? asset('offerimage/' . $image)
                                            : asset('signage/home/productimage/' . $image);
                                    }
                                @endphp

                                {{-- Offer bundles --}}
                                @if(!empty($offers))
                                    <div class="order-section-label">
                                        <span>Offer Bundles</span>
                                        <span class="badge bg-primary">{{ count($offers) }}</span>
                                    </div>
                                    <ul class="list-order-product mb-3">
                                        @foreach($offers as $o)
                                            @php $checkoutImg = resolveCheckoutImage((string)($o['image'] ?? ''), true); @endphp
                                            <li class="order-item">
                                                <img src="{{ $checkoutImg }}"
                                                     alt="{{ $o['product_name'] ?? 'Offer' }}"
                                                     onerror="this.src='{{ asset('images/no-image.png') }}'">
                                                <div class="order-item-info">
                                                    <p class="item-name">{{ $o['product_name'] ?? 'Bundle' }}</p>
                                                    <p class="item-meta">Qty: 1 &mdash; Bundle</p>
                                                    <p class="item-price">Rs. {{ number_format($o['price'] ?? 0, 0) }}</p>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="summary-block mb-4">
                                        <div class="summary-block-row">
                                            <span class="text-muted">Bundle MRP</span>
                                            <span class="text-muted text-decoration-line-through">Rs. {{ number_format($offerMrpSubtotal, 0) }}</span>
                                        </div>
                                        <div class="summary-block-row text-danger">
                                            <span>You Save</span>
                                            <span>- Rs. {{ number_format($offerSavings, 0) }}</span>
                                        </div>
                                        <div class="summary-block-row">
                                            <span>Bundle Subtotal</span>
                                            <span class="text-success">Rs. {{ number_format($offerSubtotal, 0) }}</span>
                                        </div>
                                    </div>
                                @endif

                                {{-- Regular products --}}
                                @if(!empty($normals))
                                    <!--<div class="order-section-label">-->
                                    <!--    <span>Products</span>-->
                                    <!--    <span class="badge bg-secondary">{{ count($normals) }}</span>-->
                                    <!--</div>-->
                                    <ul class="list-order-product mb-3">
                                        @foreach($normals as $item)
                                            @php
                                                $qtyN        = $item['quantity'] ?? 1;
                                                $lineTotal   = ($item['price'] ?? 0) * $qtyN;
                                                $lineMrp     = ($item['mrp'] ?? $item['price'] ?? 0) * $qtyN;
                                                $itemHasOffer = $lineMrp > $lineTotal;
                                                $checkoutImg = resolveCheckoutImage((string)($item['image'] ?? ''), false);
                                            @endphp
                                            <li class="order-item">
                                                <img src="{{ $checkoutImg }}"
                                                     alt="{{ $item['product_name'] ?? '' }}"
                                                     onerror="this.src='{{ asset('images/no-image.png') }}'">
                                                <div class="order-item-info">
                                                    <p class="item-name">{{ $item['product_name'] }}</p>
                                                    <p class="item-meta">Qty: {{ $qtyN }}</p>
                                                    <p class="item-price">
                                                        @if($itemHasOffer)
                                                            <span class="text-muted text-decoration-line-through">Rs. {{ number_format($lineMrp, 0) }}</span>
                                                        @endif
                                                        Rs. {{ number_format($lineTotal, 0) }}
                                                    </p>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="summary-block mb-4">
                                        @if($normalSavings > 0)
                                            <div class="summary-block-row">
                                                <span class="text-muted">Total MRP</span>
                                                <span class="text-muted text-decoration-line-through">Rs. {{ number_format($normalMrpSubtotal, 0) }}</span>
                                            </div>
                                            <div class="summary-block-row text-danger">
                                                <span>You Save</span>
                                                <span>- Rs. {{ number_format($normalSavings, 0) }}</span>
                                            </div>
                                        @endif
                                        <div class="summary-block-row">
                                            <span>Subtotal</span>
                                            <span class="text-success">Rs. {{ number_format($normalSubtotal, 0) }}</span>
                                        </div>
                                    </div>
                                @endif

                                <div class="summary-block mb-3">
                                    <div class="summary-block-row">
                                        <span>Shipping</span>
                                        <span>{{ $shipping == 0 ? 'Free' : 'Rs. ' . number_format($shipping, 0) }}</span>
                                    </div>
                                </div>

                                <div class="grand-total-row">
                                    <span>Grand Total</span>
                                    <span class="text-success">Rs. {{ number_format($total, 0) }}</span>
                                </div>

                                <small class="text-muted d-block mt-2">All prices include applicable GST.</small>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <div class="loading-overlay" id="loading-overlay">Processing...</div>

        <script>
        $(document).ready(function () {
            $('#user_country').on('change', function () {
                var id = $(this).val();
                if (id) {
                    $.ajax({
                        url: '/signage/get-states/' + id, type: 'GET', dataType: 'json',
                        success: function (data) {
                            $('#user_state').empty().append('<option value="0">--Select--</option>');
                            $('#user_city').empty().append('<option value="0">--Select--</option>');
                            $.each(data, function (k, v) { $('#user_state').append('<option value="' + v.id + '">' + v.name + '</option>'); });
                        }
                    });
                }
            });
            $('#user_state').on('change', function () {
                var id = $(this).val();
                if (id) {
                    $.ajax({
                        url: '/signage/get-cities/' + id, type: 'GET', dataType: 'json',
                        success: function (data) {
                            $('#user_city').empty().append('<option value="0">--Select--</option>');
                            $.each(data, function (k, v) { $('#user_city').append('<option value="' + v.id + '">' + v.name + '</option>'); });
                        }
                    });
                }
            });
        });
        </script>

        {{-- OTP logic --}}
        <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        document.addEventListener('DOMContentLoaded', function () {
            const sendOtpBtn = document.getElementById('sendOtpBtn');
            if (!sendOtpBtn) return;

            const emailInput  = document.getElementById('email');
            const otpSection  = document.getElementById('otpSection');
            const otpForm     = document.getElementById('otpForm');
            const otpMessage  = document.getElementById('otpMessage');
            const btnText     = document.getElementById('btnText');
            const emailHidden = document.getElementById('email_hidden');

            sendOtpBtn.addEventListener('click', function () {
                const email = emailInput.value.trim();
                if (!email || !/\S+@\S+\.\S+/.test(email)) {
                    otpMessage.innerHTML = '<p class="text-danger">Enter a valid email address.</p>';
                    return;
                }
                sendOtpBtn.disabled  = true;
                btnText.textContent  = 'Sending...';

                fetch('{{ route('send.otp') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ email })
                })
                .then(res => res.json())
                .then(data => {
                    otpMessage.innerHTML = `<p style="color:${data.success ? 'green' : 'red'};">${data.message}</p>`;
                    if (data.success) {
                        otpSection.classList.remove('d-none');
                        emailInput.classList.add('d-none');
                        sendOtpBtn.classList.add('d-none');
                        emailHidden.value = email;
                    }
                    sendOtpBtn.disabled = false;
                    btnText.textContent = 'Send OTP';
                });
            });

            otpForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const email            = emailHidden.value;
                const otp              = document.getElementById('otp').value.trim();
                const password         = document.getElementById('password').value.trim();
                const confirm_password = document.getElementById('confirm_password').value.trim();

                if (!otp || !password || !confirm_password) {
                    notyf.error('All fields are required.');
                    return;
                }

                fetch('{{ route('verify.otp') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type'    : 'application/json',
                        'Accept'          : 'application/json',
                        'X-CSRF-TOKEN'    : '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ email, otp, password, password_confirmation: confirm_password })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        notyf.open({ type: 'success', message: data.message || 'Successfully logged in!' });
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        notyf.error(data.message || 'Verification failed.');
                    }
                });
            });
        });
        </script>

        @include('components.frontend.footer')
    </div>

    @include('components.frontend.main-js')
    <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('checkoutForm');

        function showLoader() { document.getElementById('loading-overlay').style.display = 'flex'; }
        function hideLoader() { document.getElementById('loading-overlay').style.display = 'none'; }

        function validateField(field) {
            const value     = field.value.trim();
            let errorSpan   = field.nextElementSibling;
            if (!errorSpan || !errorSpan.classList.contains('error-msg')) {
                errorSpan = field.parentElement.querySelector('.error-msg');
            }
            let valid = true, message = '';

            switch (field.name) {
                case 'first_name': case 'last_name':
                    if (!/^[A-Za-z\s]+$/.test(value)) { valid = false; message = 'Only letters allowed.'; }
                    break;
                case 'email':
                    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) { valid = false; message = 'Enter a valid email.'; }
                    break;
                case 'phone':
                    if (!/^\d{10,12}$/.test(value)) { valid = false; message = 'Phone must be 10–12 digits.'; }
                    break;
                case 'postal_code':
                    if (!/^\d{6}$/.test(value)) { valid = false; message = 'Postal code must be 6 digits.'; }
                    break;
                case 'user_country': case 'user_state': case 'user_city':
                    if (value === '' || value === '0') { valid = false; message = 'Please select an option.'; }
                    break;
                default:
                    if (value === '') { valid = false; message = 'This field is required.'; }
            }

            if (errorSpan) {
                if (!valid) {
                    errorSpan.textContent = message;
                    errorSpan.classList.remove('d-none');
                    field.style.borderColor = 'red';
                } else {
                    errorSpan.textContent = '';
                    errorSpan.classList.add('d-none');
                    field.style.borderColor = '';
                }
            }
            return valid;
        }

        document.querySelectorAll('#user_country, #user_state, #user_city').forEach(s => {
            s.addEventListener('change', () => validateField(s));
        });
        form.querySelectorAll('input, textarea, select').forEach(f => f.addEventListener('blur', () => validateField(f)));

        document.querySelector('.billing-address-checkbox-cc').addEventListener('change', function () {
            const b = document.querySelector("textarea[name='billing_address']");
            const s = document.querySelector("textarea[name='shipping_address']");
            s.value = this.checked ? b.value : '';
        });

        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            let allValid = true;
            form.querySelectorAll('input[required], select[required], textarea[required]')
                .forEach(f => { if (!validateField(f)) allValid = false; });
            if (!allValid) return;

            const paymentMode = document.querySelector("input[name='payment_mode']:checked").value;
            startPayment(showLoader, hideLoader, paymentMode);
        });
    });

    async function startPayment(showLoader, hideLoader, paymentMode) {
        showLoader();
        try {
            const customerInfo = {
                first_name       : document.querySelector("input[name='first_name']").value,
                last_name        : document.querySelector("input[name='last_name']").value,
                email            : document.querySelector("input[name='email']").value,
                phone            : document.querySelector("input[name='phone']").value,
                street           : document.querySelector("input[name='street']").value,
                city             : document.querySelector('#user_city').value,
                state            : document.querySelector('#user_state').value,
                postal_code      : document.querySelector("input[name='postal_code']").value,
                country          : document.querySelector('#user_country').value,
                billing_address  : document.querySelector("textarea[name='billing_address']").value,
                shipping_address : document.querySelector("textarea[name='shipping_address']").value,
                description      : document.querySelector("textarea[name='order_note']").value || ''
            };

            let cartItems = [];
            @foreach($cart as $item)
            cartItems.push({
                product_id   : '{{ $item['product_id'] ?? 0 }}',
                product_name : @json($item['product_name'] ?? ''),
                quantity     : {{ (int)($item['quantity'] ?? 1) }},
                price        : {{ (float)($item['price'] ?? 0) }},
                mrp          : {{ (float)($item['mrp'] ?? $item['price'] ?? 0) }},
                subtotal     : {{ (float)($item['price'] ?? 0) * (int)($item['quantity'] ?? 1) }},
                image        : @json($item['image'] ?? ''),
                is_offer     : {{ !empty($item['is_offer']) ? 'true' : 'false' }},
                offer_id     : {{ (int)($item['offer_id'] ?? 0) }},
                cart_id      : {{ (int)($item['cart_id']  ?? 0) }},
                size         : @json($item['size']  ?? ''),
                print        : @json($item['print'] ?? '')
            });
            @endforeach

            const orderData = {
                customer_info  : customerInfo,
                cart_items     : cartItems,
                payment_method : paymentMode,
                totals: {
                    subtotal : parseFloat(@json($finalSubtotal)) || 0,
                    discount : parseFloat(@json($totalSavings))  || 0,
                    shipping : parseFloat(@json($shipping))      || 0,
                    total    : parseFloat(@json($total))         || 0
                }
            };

            const response = await fetch('{{ route('payment.process') }}', {
                method: 'POST',
                headers: {
                    'Content-Type' : 'application/json',
                    'X-CSRF-TOKEN' : document.querySelector("meta[name='csrf-token']").getAttribute('content')
                },
                body: JSON.stringify({ order_data: orderData })
            });

            const data = await response.json();

            if (data.cod && data.cod.success) {
                window.location.href = data.cod.redirect_url;
                return;
            }
            if (data.cashfree && data.cashfree.payment_session_id) {
                const cashfree = Cashfree({ mode: 'sandbox' });
                cashfree.checkout({ paymentSessionId: data.cashfree.payment_session_id, redirectTarget: '_self' });
                return;
            }

            alert('Payment initialization failed.');
            hideLoader();
        } catch (err) {
            console.error('Payment error:', err);
            alert('Payment processing error.');
            hideLoader();
        }
    }
    </script>

</body>
</html>