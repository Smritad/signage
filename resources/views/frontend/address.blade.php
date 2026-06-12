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
        <div class="preload-logo"><div class="spinner"></div></div>
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
                                    <img id="profileImgDash"
                                         class="lazyload imgDash rounded-circle"
                                         src="{{ $user->avatar ? asset('signage/home/productimage/'.$user->avatar) : asset('signage/home/productimage/ad.jpg') }}"
                                         alt="{{ $user->name ?? 'User' }}"
                                         style="width:120px; height:120px; object-fit:cover;">

                                    <div class="btn-change_img box-icon" id="changeImgDash" style="cursor:pointer;">
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
                                    <a href="{{ route('frontend.address') }}" class="my-account-nav_item h5 active">
                                        <i class="icon icon-box-arrow-down"></i> Personal Details
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('frontend.accountsetting') }}" class="my-account-nav_item h5">
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
                            <h2 class="account-title type-semibold">Personal Details</h2>
                            <div class="account-my_address">

                                {{-- Billing Address --}}
                                <div class="account-address-item file-delete">
                                    <div class="address-item_content">
                                        <h4 class="address-title">Billing Address</h4>
                                        @if($billingAddress)
                                            <div class="address-info">
                                                <h5 class="fw-semibold">Address</h5>
                                                <p class="h6">{{ $billingAddress }}</p>
                                            </div>
                                        @else
                                            <div class="address-info text-muted">
                                                <p>No billing address added.</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="address-item_action">
                                        <a href="javascript:void(0)" class="tf-btn animate-btn editAddressBtn"
                                           data-type="billing" data-title="Billing Address"
                                           data-address="{{ $billingAddress ?? '' }}">
                                            Edit
                                        </a>
                                    </div>
                                </div>

                                {{-- Shipping Address --}}
                                <div class="account-address-item file-delete">
                                    <div class="address-item_content">
                                        <h4 class="address-title">Shipping Address</h4>
                                        @if($shippingAddress)
                                            <div class="address-info">
                                                <h5 class="fw-semibold">Address</h5>
                                                <p class="h6">{{ $shippingAddress }}</p>
                                            </div>
                                        @else
                                            <div class="address-info text-muted">
                                                <p>No shipping address added.</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="address-item_action">
                                        <a href="javascript:void(0)" class="tf-btn animate-btn editAddressBtn"
                                           data-type="shipping" data-title="Shipping Address"
                                           data-address="{{ $shippingAddress ?? '' }}">
                                            Edit
                                        </a>
                                    </div>
                                </div>

                                {{-- Street --}}
                                <div class="account-address-item file-delete">
                                    <div class="address-item_content">
                                        <h4 class="address-title">Street</h4>
                                        @if($user->street)
                                            <div class="address-info">
                                                <p class="h6">{{ $user->street }}</p>
                                            </div>
                                        @else
                                            <div class="address-info text-muted">
                                                <p>No street added.</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="address-item_action">
                                        <a href="javascript:void(0)" class="tf-btn animate-btn editAddressBtn"
                                           data-type="street" data-title="Street"
                                           data-address="{{ $user->street ?? '' }}">
                                            Edit
                                        </a>
                                    </div>
                                </div>

                                {{-- Postal Code --}}
                                <div class="account-address-item file-delete">
                                    <div class="address-item_content">
                                        <h4 class="address-title">Postal Code</h4>
                                        @if($user->postal_code)
                                            <div class="address-info">
                                                <p class="h6">{{ $user->postal_code }}</p>
                                            </div>
                                        @else
                                            <div class="address-info text-muted">
                                                <p>No postal code added.</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="address-item_action">
                                        <a href="javascript:void(0)" class="tf-btn animate-btn editAddressBtn"
                                           data-type="postal_code" data-title="Postal Code"
                                           data-address="{{ $user->postal_code ?? '' }}">
                                            Edit
                                        </a>
                                    </div>
                                </div>

                                {{-- Mobile Number --}}
                                <div class="account-address-item file-delete">
                                    <div class="address-item_content">
                                        <h4 class="address-title">Mobile Number</h4>
                                        @if($user->mobile_no)
                                            <div class="address-info">
                                                <p class="h6">{{ $user->mobile_no }}</p>
                                            </div>
                                        @else
                                            <div class="address-info text-muted">
                                                <p>No mobile number added.</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="address-item_action">
                                        <a href="javascript:void(0)" class="tf-btn animate-btn editAddressBtn"
                                           data-type="mobile_no" data-title="Mobile Number"
                                           data-address="{{ $user->mobile_no ?? '' }}">
                                            Edit
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- Edit Address Modal --}}
        <div class="modal modalCentered fade modal-edit_address" id="editAddress">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-heading">
                        <h2 class="fw-normal" id="editAddressTitle">Edit</h2>
                        <span class="icon-close icon-close-popup" data-bs-dismiss="modal"></span>
                    </div>
                    <form class="form-edit_address" method="POST" id="editAddressForm">
                        @csrf
                        <div class="form_content">
                            <fieldset>
                                <input type="text" name="address" id="addressField" placeholder="Enter value *" required>
                            </fieldset>
                            <button type="submit" class="tf-btn animate-btn w-100 mt-3">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @include('components.frontend.footer')
    </div>

    {{-- Mobile Menu --}}
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

    {{-- Address modal JS only --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.editAddressBtn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var type    = this.dataset.type;
                var title   = this.dataset.title;
                var address = this.dataset.address || '';

                document.getElementById('addressField').value    = address;
                document.getElementById('editAddressTitle').textContent = 'Edit ' + title;

                var form = document.getElementById('editAddressForm');
                form.action = '/signage/user/update-address/' + type;

                var modal = new bootstrap.Modal(document.getElementById('editAddress'));
                modal.show();
            });
        });
    });
    </script>

</body>
</html>