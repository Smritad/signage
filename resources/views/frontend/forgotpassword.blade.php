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

        <section class="s-page-title">
            <div class="container">
                <div class="content">
                    <h1 class="title-page">Forget your password</h1>
                    <ul class="breadcrumbs-page">
                        <li><a href="{{ route('frontend.index') }}" class="h6 link">Home</a></li>
                        <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                        <li>
                            <h6 class="current-page fw-normal">Forget your password</h6>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="flat-spacing">
            <div class="container">
                <div class="s-log">
                    <div class="col-left">
                        <h1 class="heading">Forgot your password?</h1>

                        <form action="{{ route('user.sendResetLink') }}" method="POST" class="form-login" id="forgotForm" novalidate>
                            @csrf
                            <div class="list-ver">
                                <fieldset>
                                    <input type="email" id="forgot_email" name="email"
                                        placeholder="Enter your email address *"
                                        value="{{ old('email') }}"
                                        required>
                                </fieldset>
                                <span id="email-error" style="color:red; font-size:13px; display:block; min-height:18px; margin-bottom:6px;"></span>
                            </div>
                            <button type="submit" id="forgotBtn" class="tf-btn animate-btn w-100">
                                Send Password Reset Link
                            </button>
                        </form>
                    </div>

                    <div class="col-right">
                        <h1 class="heading">New Customer</h1>
                        <p class="h6 text-sub">
                            For new customers, register now and get discounts!
                        </p>
                        <a href="{{ route('user.registration') }}" class="tf-btn animate-btn w-100 fw-bold">
                            Register
                        </a>
                    </div>
                </div>
            </div>
        </section>

        @include('components.frontend.footer')
    </div>

    <div class="offcanvas offcanvas-start canvas-mb" id="mobileMenu">
        <span class="icon-close-popup" data-bs-dismiss="offcanvas">
            <i class="icon-close"></i>
        </span>
        <div class="canvas-header">
            <p class="text-logo-mb"><img src="images/logo/logo.webp" data-src="images/logo/logo.webp"></p>
            <a href="#" class="tf-btn type-small style-2">Login <i class="icon icon-user"></i></a>
            <span class="br-line"></span>
        </div>
        <div class="canvas-body">
            <div class="mb-content-top">
                <ul class="nav-ul-mb" id="wrapper-menu-navigation"></ul>
            </div>
            <div class="group-btn">
                <a href="#" class="tf-btn type-small style-2">Wishlist <i class="icon icon-heart"></i></a>
                <div data-bs-dismiss="offcanvas">
                    <a href="#" data-bs-toggle="modal" class="tf-btn type-small style-2">Search <i class="icon icon-magnifying-glass"></i></a>
                </div>
            </div>
            <div class="flow-us-wrap">
                <h5 class="title">Follow us on</h5>
                <ul class="tf-social-icon">
                    <li><a href="https://www.facebook.com/" target="_blank" class="social-facebook"><span class="icon"><i class="icon-fb"></i></span></a></li>
                    <li><a href="https://www.instagram.com/" target="_blank" class="social-instagram"><span class="icon"><i class="icon-instagram-logo"></i></span></a></li>
                    <li><a href="https://x.com/" target="_blank" class="social-x"><span class="icon"><i class="icon-x"></i></span></a></li>
                </ul>
            </div>
        </div>
    </div>

    @include('components.frontend.main-js')

   
    <script>
    (function () {
        const form      = document.getElementById('forgotForm');
        const emailInp  = document.getElementById('forgot_email');
        const btn       = document.getElementById('forgotBtn');

        function showError(msg) {
            document.getElementById('email-error').textContent = msg;
        }
        function clearError() {
            document.getElementById('email-error').textContent = '';
        }

        function validateEmail() {
            const val = emailInp.value.trim();
            if (!val) { showError('Email address is required.'); return false; }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) { showError('Please enter a valid email address.'); return false; }
            clearError(); return true;
        }

        emailInp.addEventListener('blur', validateEmail);
        emailInp.addEventListener('input', clearError);

        form.addEventListener('submit', function (e) {
            if (!validateEmail()) { e.preventDefault(); return; }
            // ✅ Disable button to prevent multiple clicks
            btn.disabled    = true;
            btn.textContent = 'Please wait...';
        });
    })();
    </script>

</body>
</html>