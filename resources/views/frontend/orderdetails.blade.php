<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.frontend.head')
    <style>
        .tb-order_status { padding:4px 12px; border-radius:12px; font-size:12px; font-weight:600; display:inline-block; }
        .stt-paid      { background:#d4edda; color:#155724; }
        .stt-cod       { background:#d1ecf1; color:#0c5460; }
        .stt-pending   { background:#fff3cd; color:#856404; }
        .stt-failed    { background:#f8d7da; color:#721c24; }
        .stt-cancelled { background:#f8d7da; color:#721c24; }
        .stt-refunded  { background:#e2e3e5; color:#383d41; }
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
                                    <a href="{{ route('frontend.account') }}" class="my-account-nav_item h5">
                                        <i class="icon icon-circle-four"></i> Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('frontend.ordersdetails') }}" class="my-account-nav_item h5 active">
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
                            <h2 class="account-title type-semibold">My Orders</h2>

                            @if(session('success'))
                                <div class="alert alert-success" style="background:#d4edda;color:#155724;padding:10px 14px;border-radius:6px;margin-bottom:12px;">{{ session('success') }}</div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger" style="background:#f8d7da;color:#721c24;padding:10px 14px;border-radius:6px;margin-bottom:12px;">{{ session('error') }}</div>
                            @endif

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
                                            <th>Courier Status</th>
                                            <th>Action</th>
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
                                                // status = 3 marks a cancelled order (payment_status stays paid/cod).
                                                if ((int)($order->status ?? 0) === 3) {
                                                    $st = ['label' => 'Cancelled', 'class' => 'stt-cancelled'];
                                                }
                                                $paymentMethod = strtolower($order->payment_method ?? 'online');
                                            @endphp
                                            <tr class="tb-order-item">
                                                <td class="tb-order_code">#{{ $order->order_id }}</td>
                                                <td>
                                                    <p class="mb-0 fw-semibold">{{ $count }} {{ Str::plural('Product', $count) }}</p>
                                                </td>
                                                <td><p class="small text-secondary mb-0">{{ $order->created_at ? $order->created_at->format('d M Y') : '-' }}</p></td>
                                                <td class="tb-order_price">₹{{ number_format($order->total_price, 0) }}</td>
                                                <td>{{ $paymentMethod === 'cod' ? 'COD' : 'Online' }}</td>
                                                <td><div class="tb-order_status {{ $st['class'] }}">{{ $st['label'] }}</div></td>
                                                <td>
                                                    @if($order->courier_status)
                                                        @php
                                                            $status = strtoupper($order->courier_status);
                                                            $badgeClass = $status === 'DELIVERED' ? 'bg-success' :
                                                                         ($status === 'SHIPPED'   ? 'bg-primary' :
                                                                         ($status === 'CANCELLED' ? 'bg-danger'  :
                                                                         ($status === 'IN TRANSIT'? 'bg-info text-dark' : 'bg-secondary')));
                                                        @endphp
                                                        <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">NEW</span>
                                                    @endif
                                                </td>
                                                <td class="tb-order_action">
                                                    <a href="{{ route('frontend.ordersdetailsview', $order->id) }}"
                                                       style="background:#004281; color:#fff; padding:6px 14px; border-radius:6px; display:inline-block;">
                                                       View
                                                    </a>
                                                    @if($order->isCancellable())
                                                        <form action="{{ route('frontend.order.cancel', $order->id) }}" method="POST"
                                                              style="display:inline-block; margin-top:6px;"
                                                              onsubmit="return confirm('Are you sure you want to cancel this order? This cannot be undone.');">
                                                            @csrf
                                                            <button type="submit"
                                                                    style="background:#c0392b; color:#fff; padding:6px 14px; border:none; border-radius:6px; cursor:pointer;">
                                                                Cancel
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">No orders found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination --}}
                            @if($recentOrders->lastPage() > 1)
                                <div class="wd-full wg-pagination mt-3">
                                    @if($recentOrders->onFirstPage())
                                        <span class="pagination-item h6 direct disabled"><i class="icon icon-caret-left"></i></span>
                                    @else
                                        <a href="{{ $recentOrders->previousPageUrl() }}" class="pagination-item h6 direct"><i class="icon icon-caret-left"></i></a>
                                    @endif

                                    @for($i = 1; $i <= $recentOrders->lastPage(); $i++)
                                        @if($i == $recentOrders->currentPage())
                                            <span class="pagination-item h6 active">{{ $i }}</span>
                                        @else
                                            <a href="{{ $recentOrders->url($i) }}" class="pagination-item h6">{{ $i }}</a>
                                        @endif
                                    @endfor

                                    @if($recentOrders->hasMorePages())
                                        <a href="{{ $recentOrders->nextPageUrl() }}" class="pagination-item h6 direct"><i class="icon icon-caret-right"></i></a>
                                    @else
                                        <span class="pagination-item h6 direct disabled"><i class="icon icon-caret-right"></i></span>
                                    @endif
                                </div>
                            @endif

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