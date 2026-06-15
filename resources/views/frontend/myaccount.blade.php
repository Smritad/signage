<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.frontend.head')
    <style>
        .tb-order_code a { word-break: break-all; }
    </style>
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
                                         src="{{ $user->avatar ? (str_starts_with($user->avatar, 'http') ? $user->avatar : asset('signage/home/productimage/'.$user->avatar)) : asset('signage/home/productimage/ad.jpg') }}"
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
                                    <a href="{{ route('frontend.account') }}" class="my-account-nav_item h5 active">
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

                            <div class="acount-order_stats">
                                <div class="swiper tf-swiper" data-preview="3" data-space-lg="48" data-space-md="16" data-space="12" data-pagination="1">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="order-box">
                                                <div class="order_icon"><i class="icon icon-package-thin"></i></div>
                                                <div class="order_info">
                                                    <p class="info_label h6">Pending/Failed</p>
                                                    <h2 class="info_count type-semibold">{{ $pendingOrders }}</h2>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="order-box">
                                                <div class="order_icon"><i class="icon icon-check-fat"></i></div>
                                                <div class="order_info">
                                                    <p class="info_label h6">Successful Orders</p>
                                                    <h2 class="info_count type-semibold">{{ $successfulOrders }}</h2>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="order-box">
                                                <div class="order_icon"><i class="icon icon-box-arrow-up"></i></div>
                                                <div class="order_info">
                                                    <p class="info_label h6">Total Orders</p>
                                                    <h2 class="info_count type-semibold">{{ $totalOrders }}</h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sw-dot-default tf-sw-pagination"></div>
                                </div>
                            </div>

                            <div class="account-my_order mt-5" id="orders">
                                <h2 class="account-title type-semibold">Recent Orders</h2>
                                <div class="overflow-auto">
                                    <table class="table-my_order order_recent">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Products</th>
                                                <th>Date</th>
                                                <th>Total</th>
                                                <th>Payment</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentOrders as $order)
                                                @php
                                                    $count = is_array($order->product_names) ? count($order->product_names) : 0;
                                                    $paymentStatus = strtolower(trim($order->payment_status ?? 'pending'));
                                                    $statusMap = [
                                                        'paid'      => ['label' => 'Paid',       'class' => 'stt-paid'],
                                                        'cod'       => ['label' => 'Order Placed (COD)', 'class' => 'stt-cod'],
                                                        'pending'   => ['label' => 'Pending',    'class' => 'stt-pending'],
                                                        'failed'    => ['label' => 'Failed',     'class' => 'stt-failed'],
                                                        'cancelled' => ['label' => 'Cancelled',  'class' => 'stt-cancelled'],
                                                        'expired'   => ['label' => 'Expired',    'class' => 'stt-failed'],
                                                        'refunded'  => ['label' => 'Refunded',   'class' => 'stt-refunded'],
                                                    ];
                                                    $st = $statusMap[$paymentStatus] ?? ['label' => 'Pending', 'class' => 'stt-pending'];
                                                    $paymentMethod = strtolower($order->payment_method ?? 'online');
                                                @endphp
                                                <tr class="tb-order-item">
                                                    <td class="tb-order_code">
                                                        <a href="{{ route('frontend.ordersdetails', encrypt($order->order_id)) }}">
                                                            #{{ $order->order_id }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $count }} {{ Str::plural('Product', $count) }}</td>
                                                    <td>{{ $order->created_at ? $order->created_at->format('d M Y') : '-' }}</td>
                                                    <td class="tb-order_price">₹{{ number_format($order->total_price, 0) }}</td>
                                                    <td>{{ $paymentMethod === 'cod' ? 'COD' : 'Online' }}</td>
                                                    <td><div class="tb-order_status {{ $st['class'] }}">{{ $st['label'] }}</div></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-3">No orders found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>

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

</body>
</html>