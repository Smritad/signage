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
                    <h1 class="title-page">Register</h1>
                    <ul class="breadcrumbs-page">
                        <li><a href="{{ route('frontend.index') }}" class="h6 link">Home</a></li>
                        <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                        <li>
                            <h6 class="current-page fw-normal">Register</h6>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="flat-spacing">
            <div class="container">
                <div class="s-log">
                    <div class="col-left">
                        <h1 class="heading">Register</h1>

                        @if($errors->any())
                            @php
                                $filteredErrors = array_filter($errors->all(), fn($e) => !str_contains($e, 'already registered'));
                            @endphp
                            @if(count($filteredErrors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach($filteredErrors as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @endif

                        <form class="form-login" id="registerForm" action="{{ route('registration.store') }}" method="POST" novalidate>
                            @csrf
                            <div class="list-ver">

                                {{-- Email --}}
                                <fieldset>
                                    <input type="email" id="email" name="email"
                                        placeholder="Enter your email address *"
                                        value="{{ old('email') }}"
                                        required>
                                </fieldset>
                                <span class="js-error-msg" id="email-error"></span>

                                {{-- Password --}}
                                <fieldset class="password-wrapper">
                                    <input class="password-field" type="password" id="password" name="password"
                                        placeholder="Password *" required>
                                    <span class="toggle-pass icon-show-password"></span>
                                </fieldset>
                                <span class="js-error-msg" id="password-error"></span>

                                {{-- Confirm Password --}}
                                <fieldset class="password-wrapper">
                                    <input class="password-field" type="password" id="password_confirmation" name="password_confirmation"
                                        placeholder="Confirm password *" required>
                                    <span class="toggle-pass icon-show-password"></span>
                                </fieldset>
                                <span class="js-error-msg" id="confirm-error"></span>

                            </div>

                            <button type="submit" class="tf-btn animate-btn w-100">Register</button>
                        </form>
                    </div>

                    <div class="col-right">
                        <h1 class="heading">Have An Account</h1>
                        <p class="h6 text-sub">
                            Welcome back, log in to your account to enhance your shopping experience, receive coupons, and the best discount codes.
                        </p>
                        <a href="{{ route('user.login') }}" class="btn_log tf-btn animate-btn">
                            Login
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

    {{-- Toaster: already registered --}}
    @if($errors->has('email') && str_contains($errors->first('email'), 'already registered'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notyf = new Notyf({
                duration: 4000,
                position: { x: 'right', y: 'top' },
                types: [
                    {
                        type: 'custom',
                        background: '#ab924a',
                        icon: false,
                    }
                ]
            });
            notyf.open({ type: 'custom', message: 'This email is already registered.' });
        });
    </script>
    @endif

    <script>
    (function () {
        const form     = document.getElementById('registerForm');
        const email    = document.getElementById('email');
        const password = document.getElementById('password');
        const confirm  = document.getElementById('password_confirmation');

        // Global password format:
        // Min 8 chars, at least 1 uppercase, 1 lowercase, 1 number, 1 special char (@#$%&*!)
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%&*!])[A-Za-z\d@#$%&*!]{8,}$/;

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
            if (!passwordRegex.test(val)) {
                showError('password-error', 'Password must be at least 8 characters and include uppercase, lowercase, a number, and a special character (@ # $ % & * !).'); return false;
            }
            clearError('password-error'); return true;
        }

        function validateConfirm() {
            const val = confirm.value;
            if (!val) {
                showError('confirm-error', 'Please confirm your password.'); return false;
            }
            if (val !== password.value) {
                showError('confirm-error', 'Password confirmation does not match.'); return false;
            }
            clearError('confirm-error'); return true;
        }

        // Live validation on blur
        email.addEventListener('blur', validateEmail);
        password.addEventListener('blur', validatePassword);
        confirm.addEventListener('blur', validateConfirm);

        // Clear error as user types
        email.addEventListener('input', function () { clearError('email-error'); });
        password.addEventListener('input', function () { clearError('password-error'); });
        confirm.addEventListener('input', function () { clearError('confirm-error'); });

        // Block submit if JS validation fails + disable button after valid submit
        form.addEventListener('submit', function (e) {
            const e1 = validateEmail();
            const e2 = validatePassword();
            const e3 = validateConfirm();
            if (!e1 || !e2 || !e3) {
                e.preventDefault();
            } else {
                const btn = form.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.textContent = 'Please wait...';
            }
        });
    })();
    </script>

</body>
</html>