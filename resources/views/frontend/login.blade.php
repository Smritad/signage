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
                    <h1 class="title-page">Login</h1>
                    <ul class="breadcrumbs-page">
                        <li><a href="{{ route('frontend.index') }}" class="h6 link">Home</a></li>
                        <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                        <li>
                            <h6 class="current-page fw-normal">Login</h6>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="flat-spacing">
            <div class="container">
                <div class="s-log">
                    <div class="col-left">
                        <h1 class="heading">Login</h1>

                        <form class="form-login" id="loginForm" action="{{ route('login.store') }}" method="POST" novalidate>
                            @csrf
                            <div class="list-ver">

                                {{-- Email --}}
                                <fieldset>
                                    <input type="email" id="login_email" name="email"
                                        placeholder="Enter your email address *"
                                        value="{{ old('email') }}"
                                        required>
                                </fieldset>
                                <span class="js-error-msg" id="email-error"></span>

                                {{-- Password --}}
                                <fieldset class="password-wrapper">
                                    <input type="password" id="password" name="password"
                                        placeholder="Password *" required>
                                    <span class="toggle-pass icon-show-password"></span>
                                </fieldset>
                                <span class="js-error-msg" id="password-error"></span>

                                <input type="hidden" name="from_checkout" value="{{ request('from_checkout') }}">

                            </div>

                            <div class="check-bottom">
                                <div class="checkbox-wrap"></div>
                                <h6><a href="{{ route('user.forgotpassword') }}" class="link">Forgot your password?</a></h6>
                            </div>

                            <button type="submit" id="loginBtn" class="tf-btn animate-btn w-100">Login</button>
                        </form>
                    </div>

                    <div class="col-right">
                        <h1 class="heading">New Customer</h1>
                        <p class="h6 text-sub">
                            Register to begin your fragrance journey — where every scent tells a story of freshness and charm.
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

    @include('components.frontend.main-js')

    <script>
    (function () {
        const form     = document.getElementById('loginForm');
        const email    = document.getElementById('login_email');
        const password = document.getElementById('password');
        const loginBtn = document.getElementById('loginBtn');

        function showError(id, msg) {
            const el = document.getElementById(id);
            el.textContent = msg;
            el.style.display = 'block';
        }

        function clearError(id) {
            const el = document.getElementById(id);
            el.textContent = '';
            el.style.display = 'none';
        }

        function validateEmail() {
            const val = email.value.trim();
            if (!val) {
                showError('email-error', 'Email address is required.'); return false;
            }
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(val)) {
                showError('email-error', 'Please enter a valid email address.'); return false;
            }
            clearError('email-error'); return true;
        }

        function validatePassword() {
            const val = password.value;
            if (!val) {
                showError('password-error', 'Password is required.'); return false;
            }
            clearError('password-error'); return true;
        }

        // Live validation on blur
        email.addEventListener('blur', validateEmail);
        password.addEventListener('blur', validatePassword);

        // Clear on type
        email.addEventListener('input', function () { clearError('email-error'); });
        password.addEventListener('input', function () { clearError('password-error'); });

        // Submit handler
        form.addEventListener('submit', function (e) {
            const e1 = validateEmail();
            const e2 = validatePassword();
            if (!e1 || !e2) {
                e.preventDefault();
            } else {
                // ✅ Disable button to prevent double click
                loginBtn.disabled = true;
                loginBtn.textContent = 'Please wait...';
            }
        });

        // ✅ Highlight field inline based on server error_type
        @if(session('error_type') === 'email')
            showError('email-error', '{{ session('error') }}');
        @elseif(session('error_type') === 'password')
            showError('password-error', '{{ session('error') }}');
        @endif

    })();

    // Password toggle
    document.addEventListener('DOMContentLoaded', function () {
        const togglePass  = document.querySelector('.toggle-pass');
        const passwordInput = document.getElementById('password');
        if (togglePass && passwordInput) {
            togglePass.addEventListener('click', function () {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                togglePass.classList.toggle('icon-show-password', !isPassword);
                togglePass.classList.toggle('icon-hide-password', isPassword);
            });
        }
    });
    </script>

</body>
</html>