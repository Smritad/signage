<!DOCTYPE html>
<html lang="en">
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
                    <h1 class="title-page">My Account</h1>
                    <ul class="breadcrumbs-page">
                        <li><a href="{{ route('frontend.index') }}" class="h6 link">Home</a></li>
                        <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                        <li><h6 class="current-page fw-normal">My Account</h6></li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="flat-spacing">
            <div class="container">
                <div class="row">

                    {{-- Sidebar --}}
                    <div class="col-xl-3 d-none d-xl-block">
                        <div class="sidebar-account sidebar-content-wrap sticky-top">
                            <div class="account-author">
                                <div class="author_avatar position-relative d-inline-block">
                                    <img class="lazyload imgDash rounded-circle"
                                         src="{{ $user->avatar ? (str_starts_with($user->avatar, 'http') ? $user->avatar : asset('signage/home/productimage/'.$user->avatar)) : asset('signage/home/productimage/ad.jpg') }}"
                                         alt="{{ $user->name ?? 'User' }}"
                                         style="width:120px; height:120px; object-fit:cover;">
                                    <div class="btn-change_img box-icon" id="changeImgDash">
                                        <i class="icon icon-camera"></i>
                                    </div>
                                    <form id="uploadProfileForm" action="{{ route('user.updateProfileImage') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" name="avatar" id="fileInputDash" accept="image/*" style="display:none;">
                                    </form>
                                </div>
                                <h4 class="author_name">{{ $user->name ?? 'User' }}</h4>
                                <p class="author_email h6">{{ $user->email ?? '' }}</p>
                            </div>

                            <ul class="my-account-nav">
                                <li>
                                    <a href="{{ route('frontend.account') }}" class="my-account-nav_item h5">
                                        <i class="icon icon-circle-four"></i> Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('frontend.ordersdetails') }}" class="my-account-nav_item h5">
                                        <i class="icon icon-box-arrow-down"></i> Orders
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('frontend.address') }}" class="my-account-nav_item h5">
                                        <i class="icon icon-box-arrow-down"></i> Personal Details
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('frontend.accountsetting') }}" class="my-account-nav_item h5 active">
                                        <i class="icon icon-setting"></i> User/Password
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('user.logout') }}" class="my-account-nav_item h5"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="icon icon-sign-out"></i> Log out
                                    </a>
                                    <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display:none;">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Main Content --}}
                    <div class="col-xl-9">
                        <div class="my-account-content">
                            <form method="POST" action="{{ route('user.updateAccount') }}" id="accountForm" novalidate autocomplete="off">
                                @csrf

                                {{-- Account Info --}}
                                <h2 class="account-title type-semibold">Account Setting</h2>
                                <div class="form_content">
                                    <fieldset>
                                        <input type="text" id="acc_name" name="name"
                                            placeholder="Full name *"
                                            value="{{ old('name', $user->name) }}"
                                            required>
                                    </fieldset>
                                    <span id="name-error" style="color:red; font-size:13px; display:block; min-height:18px; margin-bottom:6px;"></span>

                                    <fieldset>
                                        <input type="email" id="acc_email" name="email"
                                            placeholder="Email *"
                                            value="{{ old('email', $user->email) }}"
                                            required>
                                    </fieldset>
                                    <span id="acc-email-error" style="color:red; font-size:13px; display:block; min-height:18px; margin-bottom:6px;"></span>
                                </div>

                                <br>

                                {{-- Change Password --}}
                                <h2 class="account-title type-semibold">Change Password</h2>
                                <p style="font-size:13px; color:#888; margin-bottom:12px;">
                                    Leave password fields empty if you don't want to change it.
                                </p>
                                <div class="form_content site-change">

                                    <fieldset class="password-wrapper">
                                        <input class="password-field" type="password" id="current_password"
                                            name="current_password" placeholder="Current password" autocomplete="off">
                                        <span class="toggle-pass icon-show-password"></span>
                                    </fieldset>
                                    <span id="current-password-error" style="color:red; font-size:13px; display:block; min-height:18px; margin-bottom:6px;"></span>

                                    <fieldset class="password-wrapper">
                                        <input class="password-field" type="password" id="new_password"
                                            name="new_password" placeholder="New password" autocomplete="new-password">
                                        <span class="toggle-pass icon-show-password"></span>
                                    </fieldset>
                                    <span id="new-password-error" style="color:red; font-size:13px; display:block; min-height:18px; margin-bottom:6px;"></span>

                                    <fieldset class="password-wrapper">
                                        <input class="password-field" type="password" id="new_password_confirmation"
                                            name="new_password_confirmation" placeholder="Confirm new password" autocomplete="new-password">
                                        <span class="toggle-pass icon-show-password"></span>
                                    </fieldset>
                                    <span id="confirm-password-error" style="color:red; font-size:13px; display:block; min-height:18px; margin-bottom:6px;"></span>

                                </div>

                                <br><br>

                                <button type="submit" id="saveBtn" class="btn-submit_form tf-btn animate-btn w-100 fw-bold">
                                    Save Changes
                                </button>
                            </form>
                        </div>
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
            <p class="text-logo-mb">
                <img src="{{ asset('frontend/assets/images/logo/logo.webp') }}"
                     data-src="{{ asset('frontend/assets/images/logo/logo.webp') }}"
                     alt="Logo" class="lazyload">
            </p>
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

    {{-- Inline error for wrong current password (session error_type) --}}
    @if(session('error_type') === 'current_password')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('current-password-error').textContent = @json(session('error'));
        });
    </script>
    @endif

    {{-- Form validation ONLY — toasts + image upload handled by main-js --}}
    <script>
    (function () {
        const form        = document.getElementById('accountForm');
        const nameInp     = document.getElementById('acc_name');
        const emailInp    = document.getElementById('acc_email');
        const currentPass = document.getElementById('current_password');
        const newPass     = document.getElementById('new_password');
        const confirmPass = document.getElementById('new_password_confirmation');
        const saveBtn     = document.getElementById('saveBtn');

        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%&*!])[A-Za-z\d@#$%&*!]{8,}$/;

        function showError(id, msg) {
            const el = document.getElementById(id);
            if (el) { el.textContent = msg; }
        }
        function clearError(id) {
            const el = document.getElementById(id);
            if (el) el.textContent = '';
        }

        function validateName() {
            if (!nameInp.value.trim()) { showError('name-error', 'Full name is required.'); return false; }
            clearError('name-error'); return true;
        }

        function validateEmail() {
            const val = emailInp.value.trim();
            if (!val) { showError('acc-email-error', 'Email address is required.'); return false; }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) { showError('acc-email-error', 'Please enter a valid email address.'); return false; }
            clearError('acc-email-error'); return true;
        }

        function validatePasswordSection() {
            const cur  = currentPass.value.trim();
            const np   = newPass.value.trim();
            const conf = confirmPass.value.trim();

            // All empty = not changing password, skip entirely
            if (!cur && !np && !conf) return true;

            let valid = true;

            if (!cur) {
                showError('current-password-error', 'Please enter your current password.'); valid = false;
            } else { clearError('current-password-error'); }

            if (!np) {
                showError('new-password-error', 'Please enter a new password.'); valid = false;
            } else if (!passwordRegex.test(np)) {
                showError('new-password-error', 'Password must include uppercase, lowercase, a number, and a special character (@ # $ % & * !).'); valid = false;
            } else { clearError('new-password-error'); }

            if (!conf) {
                showError('confirm-password-error', 'Please confirm your new password.'); valid = false;
            } else if (conf !== np) {
                showError('confirm-password-error', 'Password confirmation does not match.'); valid = false;
            } else { clearError('confirm-password-error'); }

            return valid;
        }

        // Blur validation
        nameInp.addEventListener('blur', validateName);
        emailInp.addEventListener('blur', validateEmail);
        currentPass.addEventListener('blur', validatePasswordSection);
        newPass.addEventListener('blur', validatePasswordSection);
        confirmPass.addEventListener('blur', validatePasswordSection);

        // Clear on type
        nameInp.addEventListener('input', () => clearError('name-error'));
        emailInp.addEventListener('input', () => clearError('acc-email-error'));
        currentPass.addEventListener('input', () => clearError('current-password-error'));
        newPass.addEventListener('input', () => clearError('new-password-error'));
        confirmPass.addEventListener('input', () => clearError('confirm-password-error'));

        // Submit
        form.addEventListener('submit', function (e) {
            const v1 = validateName();
            const v2 = validateEmail();
            const v3 = validatePasswordSection();
            if (!v1 || !v2 || !v3) {
                e.preventDefault();
            } else {
                saveBtn.disabled    = true;
                saveBtn.textContent = 'Please wait...';
            }
        });

        // NOTE: Profile image upload is handled GLOBALLY by main-js.
        // Do NOT add an upload handler here or it will fire twice.

    })();
    </script>

</body>
</html>