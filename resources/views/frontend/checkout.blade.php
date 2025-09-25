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
                    <h1 class="title-page">Checkout</h1>
                    <ul class="breadcrumbs-page">
                        <li><a href="{{ route('frontend.index') }}" class="h6 link">Home</a></li>
                        <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                        <li>
                            <h6 class="current-page fw-normal">Checkout</h6>
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

            <!-- Left Column: Guest Checkout Form -->
            <div class="col-lg-7">
                <div class="tf-page-checkout mb-lg-0">
                    <div class="checkout-card p-4 shadow-sm rounded">

                        @php $user = Auth::guard('custom')->user(); @endphp

                        @if(!$user)
                        <h2 class="title type-semibold mb-3">Login / Register with Email</h2>

                        <div class="checkout-login-wrap">
                            <form id="otpForm">@csrf
                                <div class="mb-3">
                                    <input type="email" name="email" id="email" class="form-control"
                                        placeholder="Enter Email Address" required>
                                </div>

                                <button type="button" id="sendOtpBtn" class="btn btn-primary w-100 mb-3">
                                    <span id="btnText">Send OTP</span>
                                </button>

                                <!-- OTP + Password Section -->
                                <div id="otpSection" style="display:none;">
                                    <div class="mb-3">
                                        <input type="text" name="otp" id="otp" class="form-control"
                                            placeholder="Enter OTP" required>
                                    </div>
                                    <div class="mb-3">
                                        <input type="password" name="password" id="password" class="form-control"
                                            placeholder="Create Password" required>
                                    </div>
                                    <div class="mb-3">
                                        <input type="password" name="confirm_password" id="confirm_password"
                                            class="form-control" placeholder="Confirm Password" required>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">Verify & Create Account</button>
                                </div>
                                <div id="otpMessage" class="mt-2"></div>
                                <input type="hidden" id="email_hidden" name="email">
                            </form>
                        </div>
                        <hr class="my-4">
                        @endif

                        <!-- Checkout Form -->
                        <form id="checkoutForm" class="tf-checkout-cart-main">
                            <div class="box-ip-checkout estimate-shipping">
                                <h2 class="title type-semibold">Information</h2>
                                <div class="form_content">

                                    <!-- Name -->
                                    <div class="cols tf-grid-layout sm-col-2">
                                        <fieldset>
                                            <input type="text" name="first_name" placeholder="First name" required>
                                            <span class="text-danger error-msg" style="display:none;"></span>
                                        </fieldset>
                                        <fieldset>
                                            <input type="text" name="last_name" placeholder="Last name" required>
                                            <span class="text-danger error-msg" style="display:none;"></span>
                                        </fieldset>
                                    </div>

                                    <!-- Email & Phone -->
                                    <div class="cols tf-grid-layout sm-col-2">
                                        <fieldset>
                                            <input type="email" name="email" placeholder="Email address" required>
                                            <span class="text-danger error-msg" style="display:none;"></span>
                                        </fieldset>
                                        <fieldset>
                                            <input type="number" name="phone" placeholder="Phone number" required>
                                            <span class="text-danger error-msg" style="display:none;"></span>
                                        </fieldset>
                                    </div>

                                    <!-- Country / State / City -->
                                    <div class="cols tf-grid-layout sm-col-3">
                                        <fieldset>
                                            <label>Country</label>
                                            <select name="user_country" id="user_country" class="form-control" required>
                                                <option value="">--Select--</option>
                                                @foreach($fetch_all_countries as $country)
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger error-msg" style="display:none;"></span>
                                        </fieldset>
                                        <fieldset>
                                            <label>State</label>
                                            <select name="user_state" id="user_state" class="form-control" required>
                                                <option value="">--Select--</option>
                                            </select>
                                            <span class="text-danger error-msg" style="display:none;"></span>
                                        </fieldset>
                                        <fieldset>
                                            <label>City</label>
                                            <select name="user_city" id="user_city" class="form-control" required>
                                                <option value="">--Select--</option>
                                            </select>
                                            <span class="text-danger error-msg" style="display:none;"></span>
                                        </fieldset>
                                    </div>

                                    <!-- Street & Postal -->
                                    <div class="cols tf-grid-layout sm-col-2">
                                        <fieldset>
                                            <input type="text" name="street" placeholder="Street" required>
                                            <span class="text-danger error-msg" style="display:none;"></span>
                                        </fieldset>
                                        <fieldset>
                                            <input type="number" name="postal_code" placeholder="Postal code" required>
                                            <span class="text-danger error-msg" style="display:none;"></span>
                                        </fieldset>
                                    </div>

                                    <!-- Billing & Shipping -->
                                    <div class="cols tf-grid-layout sm-col-2">
                                        <fieldset>
                                            <textarea name="billing_address" placeholder="Billing Address"
                                                style="height:100px;" required></textarea>
                                            <span class="text-danger error-msg" style="display:none;"></span>
                                        </fieldset>
                                        <fieldset>
                                            <textarea name="shipping_address" placeholder="Shipping Address"
                                                style="height:100px;" required></textarea>
                                            <span class="text-danger error-msg" style="display:none;"></span>
                                        </fieldset>
                                    </div>

                                    <!-- Checkbox: Same as Billing -->
                                    <label class="billing-address-label-sec">
                                        <input type="checkbox" name="same_as_billing"
                                            class="billing-address-checkbox-cc">
                                        Same as Billing Address
                                    </label>

                                    <!-- Order Note -->
                                    <textarea name="order_note" placeholder="Note about your order"
                                        style="height: 180px;"></textarea>

                                </div>
                            </div>

                            <div class="button_submit">
                                <button type="submit" class="tf-btn animate-btn w-100">Payment</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="col-lg-5">
                <div class="fl-sidebar-cart sticky-top">
                    <div class="box-your-order p-3 border rounded shadow-sm">
                        <h2 class="title type-semibold mb-3">Your Order</h2>
                        @php $subtotal=0; $discount=0; $shipping=0; @endphp
                        <ul class="list-order-product mb-3">
                            @forelse($cart as $item)
                            @php $lineTotal=$item['price'] * $item['quantity']; $subtotal+=$lineTotal; @endphp
                            <li class="order-item d-flex align-items-center mb-2">
                                <a href="#" class="img-prd me-2">
                                    <img src="{{ $item['image'] }}" alt="{{ $item['product_name'] }}" width="60">
                                </a>
                                <div class="infor-prd flex-grow-1">
                                    <h6 class="prd_name mb-1">{{ $item['product_name'] }}</h6>
                                    <p class="price-prd mb-0">Rs.{{ number_format($lineTotal,2) }}</p>
                                    <p class="quantity-prd mb-0">Qty: {{ $item['quantity'] }}</p>
                                </div>
                            </li>
                            @empty
                            <li>Your cart is empty.</li>
                            @endforelse
                        </ul>
                        @php $total=$subtotal - $discount + $shipping; @endphp
                        <ul class="list-total mb-3">
                            <li class="total-item d-flex justify-content-between">
                                <span>Subtotal</span><span>Rs.{{ number_format($subtotal,2) }}</span>
                            </li>
                            <li class="total-item d-flex justify-content-between">
                                <span>Discount</span><span>Rs.{{ number_format($discount,2) }}</span>
                            </li>
                            <li class="total-item d-flex justify-content-between">
                                <span>Shipping</span><span>{{ $shipping==0 ? 'Free' : 'Rs.'.number_format($shipping,2) }}</span>
                            </li>
                        </ul>
                        <div class="last-total d-flex justify-content-between fw-bold">
                            <span>Total</span><span>Rs.{{ number_format($total,2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Loader Overlay -->
<div id="loading-overlay" style="
    display:none; position:fixed; top:0; left:0; width:100%; height:100%;
    background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center; color:#fff;
    font-size:20px; font-weight:bold;">
    Processing Payment...
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("checkoutForm");

    // Loader functions
    function showLoader() {
        document.getElementById("loading-overlay").style.display = "flex";
    }
    function hideLoader() {
        document.getElementById("loading-overlay").style.display = "none";
    }
    hideLoader();

    // Validation function
    function validateField(field) {
        const value = field.value.trim();
        const errorSpan = field.nextElementSibling;
        let valid = true, message = "";

        switch (field.name) {
            case "first_name":
            case "last_name":
                if (!/^[A-Za-z\s]+$/.test(value)) { valid = false; message = "Only letters allowed."; }
                break;
            case "email":
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) { valid = false; message = "Enter a valid email."; }
                break;
            case "phone":
                if (!/^\d{10,12}$/.test(value)) { valid = false; message = "Phone must be 10–12 digits."; }
                break;
            case "postal_code":
                if (!/^\d{6}$/.test(value)) { valid = false; message = "Postal Code must be 6 digits."; }
                break;
            default:
                if (value === "") { valid = false; message = "This field is required."; }
        }

        if (!valid) {
            errorSpan.textContent = message;
            errorSpan.style.display = "block";
            field.style.borderColor = "red";
        } else {
            errorSpan.textContent = "";
            errorSpan.style.display = "none";
            field.style.borderColor = "";
        }
        return valid;
    }

    // Validate on blur
    form.querySelectorAll("input, textarea, select").forEach(f => {
        f.addEventListener("blur", () => validateField(f));
    });

    // Checkbox autofill
    document.querySelector(".billing-address-checkbox-cc")
        .addEventListener("change", function () {
            const billing = document.querySelector("textarea[name='billing_address']");
            const shipping = document.querySelector("textarea[name='shipping_address']");
            shipping.value = this.checked ? billing.value : '';
        });

    // Submit handler
    form.addEventListener("submit", async function (e) {
        e.preventDefault();
        let allValid = true;
        form.querySelectorAll("input[required], select[required], textarea[required]")
            .forEach(f => { if (!validateField(f)) allValid = false; });

        if (!allValid) return; // stop if invalid

        // ✅ Start payment
        startPayment(showLoader, hideLoader);
    });
});

// Razorpay flow
async function startPayment(showLoader, hideLoader) {
    @if (!Auth::guard('custom')->check())
        alert("⚠️ Please login first using OTP before proceeding with payment!");
        return;
    @endif

    showLoader();

    let customerInfo = {
        first_name: document.querySelector("input[name='first_name']").value,
        last_name: document.querySelector("input[name='last_name']").value,
        email: document.querySelector("input[name='email']").value,
        phone: document.querySelector("input[name='phone']").value,
        street: document.querySelector("input[name='street']").value,
        city: document.querySelector("#user_city").value,
        state: document.querySelector("#user_state").value,
        postal_code: document.querySelector("input[name='postal_code']").value,
        country: document.querySelector("#user_country").value,
        billing_address: document.querySelector("textarea[name='billing_address']").value,
        shipping_address: document.querySelector("textarea[name='shipping_address']").value,
        description: document.querySelector("textarea[name='order_note']").value || ""
    };

    let cartItems = [];
    @foreach($cart as $item)
        cartItems.push({
            product_id: "{{ $item['id'] }}",
            product_name: "{{ $item['product_name'] }}",
            quantity: {{ $item['quantity'] }},
            price: {{ $item['price'] }},
            subtotal: {{ $item['price'] * $item['quantity'] }},
            image: "{{ $item['image'] }}"
        });
    @endforeach

    let subtotal = parseFloat(@json($subtotal));
    let discount = parseFloat(@json($discount));
    let shipping = parseFloat(@json($shipping));
    let totalAmount = subtotal - discount + shipping;

    let orderData = {
        customer_info: customerInfo,
        cart_items: cartItems,
        totals: { subtotal, discount, shipping, total: totalAmount }
    };

    try {
        let response = await fetch("{{ route('payment.process') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").getAttribute("content")
            },
            body: JSON.stringify({ amount: totalAmount, order_data: orderData })
        });

        let data = await response.json();
        if (!data.order_id) { alert("Order creation failed!"); hideLoader(); return; }

        let options = {
            key: data.razorpay_key,
            amount: totalAmount * 100,
            currency: "INR",
            order_id: data.order_id,
            handler: async function (res) {
                try {
                    let verifyResponse = await fetch("{{ route('payment.verify') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").getAttribute("content")
                        },
                        body: JSON.stringify({
                            razorpay_order_id: res.razorpay_order_id,
                            razorpay_payment_id: res.razorpay_payment_id,
                            razorpay_signature: res.razorpay_signature,
                            order_id: data.order_id,
                            order_data: orderData
                        })
                    });
                    let verifyData = await verifyResponse.json();
                    if (verifyData.status === 'success') {
                        window.location.href = "{{ route('order.confirm') }}?order_id=" + data.order_id;
                    } else {
                        alert("Payment verification failed!");
                    }
                } catch (err) {
                    alert("Verification error!");
                } finally {
                    hideLoader();
                }
            }
        };

        let rzp = new Razorpay(options);
        rzp.open();
        rzp.on("payment.failed", function () { hideLoader(); });
    } catch (err) {
        alert("Payment processing error.");
        console.error(err);
        hideLoader();
    }
}
</script>




<script>
$(document).ready(function() {
    // When country changes
    $('#user_country').on('change', function() {
        var countryID = $(this).val();
        if(countryID) {
            $.ajax({
                url: '/get-states/' + countryID,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#user_state').empty().append('<option value="0">--Select--</option>');
                    $('#user_city').empty().append('<option value="0">--Select--</option>'); // Clear cities
                    $.each(data, function(key, value) {
                        $('#user_state').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                    });
                }
            });
        } else {
            $('#user_state').empty().append('<option value="0">--Select--</option>');
            $('#user_city').empty().append('<option value="0">--Select--</option>');
        }
    });
 
    // When state changes
    $('#user_state').on('change', function() {
        var stateID = $(this).val();
        if(stateID) {
            $.ajax({
                url: '/get-cities/' + stateID,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#user_city').empty().append('<option value="0">--Select--</option>');
                    $.each(data, function(key, value) {
                        $('#user_city').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                    });
                }
            });
        } else {
            $('#user_city').empty().append('<option value="0">--Select--</option>');
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const emailInput = document.getElementById('email');
    const otpSection = document.getElementById('otpSection');
    const otpForm = document.getElementById('otpForm');
    const otpMessage = document.getElementById('otpMessage');
    const btnText = document.getElementById('btnText');
    const emailHidden = document.getElementById('email_hidden');
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm_password');

    // Hide OTP section and password initially
    otpSection.style.display = 'none';
    passwordField.style.display = 'none';
    confirmPasswordField.style.display = 'none';

    // --- Send OTP ---
    sendOtpBtn.addEventListener('click', function() {
        const email = emailInput.value.trim();
        if (!email || !/\S+@\S+\.\S+/.test(email)) {
            otpMessage.innerHTML = `<p class="text-danger">Enter a valid email address</p>`;
            return;
        }

        sendOtpBtn.disabled = true;
        btnText.textContent = 'Sending...';
        otpMessage.innerHTML = '';

        fetch("{{ route('send.otp') }}", {
            method: 'POST',
            headers: {
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':'{{ csrf_token() }}'
            },
            body: JSON.stringify({ email })
        })
        .then(res => res.json())
        .then(data => {
            otpMessage.innerHTML = `<p style="color:${data.success ? 'green' : 'red'};">${data.message}</p>`;
            if (data.success) {
                otpSection.style.display = 'block';
                emailInput.style.display = 'none';
                sendOtpBtn.style.display = 'none';
                passwordField.style.display = 'block';
                confirmPasswordField.style.display = 'block';
                emailHidden.value = email;
            }
            sendOtpBtn.disabled = false;
            btnText.textContent = 'Send OTP';
        })
        .catch(err => {
            console.error("Send OTP fetch error:", err);
            otpMessage.innerHTML = `<p class="text-danger">Something went wrong</p>`;
            sendOtpBtn.disabled = false;
            btnText.textContent = 'Send OTP';
        });
    });

   // --- Verify OTP ---
otpForm.addEventListener('submit', function(e) {
    e.preventDefault();

    const email = emailHidden.value;
    const otp = document.getElementById('otp').value.trim();
    const password = passwordField.value.trim();
    const confirm_password = confirmPasswordField.value.trim();

    if (!otp || !password || !confirm_password) {
        otpMessage.innerHTML = `<p class="text-danger">All fields are required</p>`;
        notyf.error("All fields are required");
        return;
    }

    const submitBtn = otpForm.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Verifying...';
    otpMessage.innerHTML = '';

    fetch("{{ route('verify.otp') }}", {
        method: 'POST',
        headers: {
            'Content-Type':'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            email,
            otp,
            password,
            password_confirmation: confirm_password
        })
    })
    .then(res => {
        if (!res.ok) {
            throw new Error("Network response was not ok");
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            // ✅ Show Notyf success notification
            notyf.open({
                type: 'success',
                message: data.message || 'Successfully logged in!'
            });

            // Reload page after short delay so user sees the notification
            setTimeout(() => window.location.reload(), 1500);
        } else {
            // ❌ Show Notyf error notification
            notyf.error(data.message || 'Verification failed');
        }

        submitBtn.disabled = false;
        submitBtn.textContent = 'Verify & Create Account';
    })
    .catch(err => {
        console.error("Verify OTP fetch error:", err);
        notyf.error("Something went wrong. Please try again.");
        otpMessage.innerHTML = `<p class="text-danger">Something went wrong</p>`;
        submitBtn.disabled = false;
        submitBtn.textContent = 'Verify & Create Account';
    });
});

});
</script>






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