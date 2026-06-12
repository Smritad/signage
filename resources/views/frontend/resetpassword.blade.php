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
                    <h1 class="title-page">Reset your password</h1>
                    <ul class="breadcrumbs-page">
                        <li><a href="{{ route('frontend.index') }}" class="h6 link">Home</a></li>
                        <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                        <li>
                            <h6 class="current-page fw-normal">Reset your password</h6>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="flat-spacing">
            <div class="container">
                <div class="s-log">
                    <div class="col-left">
                        <h1 class="heading">Reset your password</h1>

                        <form action="{{ route('user.updatePassword') }}" method="POST" class="form-login" id="resetForm" novalidate>
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="list-ver">

                                {{-- Email --}}
                                <fieldset>
                                    <input type="email" id="reset_email" name="email"
                                        placeholder="Your Email *"
                                        value="{{ old('email') }}"
                                        required>
                                </fieldset>
                                <span class="js-error-msg" id="email-error"></span>

                                {{-- New Password --}}
                                <fieldset class="password-wrapper">
                                    <input class="password-field" type="password" id="password" name="password"
                                        placeholder="New Password *" required>
                                    <span class="toggle-pass icon-show-password"></span>
                                </fieldset>
                                <span class="js-error-msg" id="password-error"></span>

                                {{-- Confirm Password --}}
                                <fieldset class="password-wrapper">
                                    <input class="password-field" type="password" id="password_confirmation" name="password_confirmation"
                                        placeholder="Confirm Password *" required>
                                    <span class="toggle-pass icon-show-password"></span>
                                </fieldset>
                                <span class="js-error-msg" id="confirm-error"></span>

                            </div>

                            <button type="submit" id="resetBtn" class="tf-btn animate-btn w-100">
                                Reset Password
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

    {{-- ✅ Toaster for server-side errors and messages --}}
    @if(session('error') || session('message') || $errors->has('email') || $errors->has('password'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notyf = new Notyf({
                duration: 5000,
                position: { x: 'right', y: 'top' },
                dismissible: true,
                types: [
                    {
                        type: 'custom-warning',
                        background: '#ab924a',
                        icon: false,
                    }
                ]
            });

            @if(session('error'))
                notyf.open({ type: 'custom-warning', message: @json(session('error')) });
            @endif

            @if($errors->has('email'))
                notyf.open({ type: 'custom-warning', message: @json($errors->first('email')) });
            @endif

            @if($errors->has('password'))
                notyf.open({ type: 'custom-warning', message: @json($errors->first('password')) });
            @endif

            @if(session('message'))
                notyf.success(@json(session('message')));
            @endif
        });
    </script>
    @endif

    {{-- ✅ Inline field errors from server --}}
    @if($errors->has('email') || session('error_type') === 'email')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const el = document.getElementById('email-error');
            if (el) {
                el.textContent = @json($errors->first('email') ?: session('error'));
                el.style.display = 'block';
            }
        });
    </script>
    @endif

    <script>
    (function () {
        const form     = document.getElementById('resetForm');
        const emailInp = document.getElementById('reset_email');
        const password = document.getElementById('password');
        const confirm  = document.getElementById('password_confirmation');
        const resetBtn = document.getElementById('resetBtn');

        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%&*!])[A-Za-z\d@#$%&*!]{8,}$/;

        function showError(id, msg) {
            const el = document.getElementById(id);
            el.textContent = msg;
            el.style.display = 'block';
        }
        function clearError(id) {
            document.getElementById(id).textContent = '';
        }

        function validateEmail() {
            const val = emailInp.value.trim();
            if (!val) { showError('email-error', 'Email address is required.'); return false; }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) { showError('email-error', 'Please enter a valid email address.'); return false; }
            clearError('email-error'); return true;
        }

        function validatePassword() {
            const val = password.value;
            if (!val) { showError('password-error', 'Password is required.'); return false; }
            if (!passwordRegex.test(val)) {
                showError('password-error', 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@ # $ % & * !).');
                return false;
            }
            clearError('password-error'); return true;
        }

        function validateConfirm() {
            const val = confirm.value;
            if (!val) { showError('confirm-error', 'Please confirm your password.'); return false; }
            if (val !== password.value) { showError('confirm-error', 'Password confirmation does not match.'); return false; }
            clearError('confirm-error'); return true;
        }

        emailInp.addEventListener('blur', validateEmail);
        password.addEventListener('blur', validatePassword);
        confirm.addEventListener('blur', validateConfirm);

        emailInp.addEventListener('input', () => clearError('email-error'));
        password.addEventListener('input', () => clearError('password-error'));
        confirm.addEventListener('input', () => clearError('confirm-error'));

        form.addEventListener('submit', function (e) {
            const e1 = validateEmail();
            const e2 = validatePassword();
            const e3 = validateConfirm();
            if (!e1 || !e2 || !e3) {
                e.preventDefault();
            } else {
                resetBtn.disabled    = true;
                resetBtn.textContent = 'Please wait...';
            }
        });
    })();
    </script>



</body>
</html>