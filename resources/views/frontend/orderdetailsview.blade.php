<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.frontend.head')

    <style>
        .prd-info {
            display: flex; align-items: flex-start; gap: 15px;
            padding: 15px 0; border-bottom: 1px solid #eee;
        }
        .prd-info:last-child { border-bottom: 0; }
        .prd-info .info_image img {
            width: 80px; height: 80px; object-fit: cover;
            border-radius: 6px; border: 1px solid #ddd;
        }
        .prd-info .info_detail { flex: 1; }
        .prd-info .info-name {
            font-size: 15px; font-weight: 600; color: #000;
            display: block; margin-bottom: 6px;
        }
        .prd-info .info-price,
        .prd-info .info-qty { margin: 2px 0; color: #555; font-size: 14px; }

        .prd-order_total {
            display: flex; justify-content: space-between;
            align-items: center; padding: 15px 0;
            border-top: 2px solid #000; font-size: 17px;
            margin-top: 10px;
        }

        .bundle-badge {
            display: inline-block; padding: 2px 8px;
            background: #fff3cd; color: #856404;
            font-size: 11px; border-radius: 4px;
            margin-bottom: 5px;
        }
        .bundle-items {
            margin: 5px 0 0 0; padding-left: 16px;
            font-size: 12px; color: #666;
        }
        .bundle-items li { margin-bottom: 2px; }

        .mrp-strike {
            text-decoration: line-through;
            color: #999; font-size: 12px;
            margin-right: 5px;
        }

        .status-pill {
            display: inline-block; padding: 4px 12px;
            border-radius: 12px; font-size: 12px; font-weight: 600;
        }
        .stt-paid      { background:#d4edda; color:#155724; }
        .stt-cod       { background:#d1ecf1; color:#0c5460; }
        .stt-pending   { background:#fff3cd; color:#856404; }
        .stt-failed    { background:#f8d7da; color:#721c24; }
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
                    <h1 class="title-page">Order Details</h1>
                    <ul class="breadcrumbs-page">
                        <li><a href="{{ route('frontend.index') }}" class="h6 link">Home</a></li>
                        <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                        <li><a href="{{ route('frontend.ordersdetails') }}" class="h6 link">My Orders</a></li>
                        <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                        <li><h6 class="current-page fw-normal">#{{ $order->order_id }}</h6></li>
                    </ul>
                </div>
            </div>
        </section>

        @php
            $paymentStatus = strtolower(trim($order->payment_status ?? 'pending'));
            $paymentMethod = strtolower($order->payment_method ?? 'online');
            $isCod         = ($paymentMethod === 'cod');

            $statusMap = [
                'paid'      => ['label' => 'Paid',       'class' => 'stt-paid'],
                'cod'       => ['label' => 'COD Placed', 'class' => 'stt-cod'],
                'pending'   => ['label' => 'Pending',    'class' => 'stt-pending'],
                'failed'    => ['label' => 'Failed',     'class' => 'stt-failed'],
                'cancelled' => ['label' => 'Cancelled',  'class' => 'stt-failed'],
                'expired'   => ['label' => 'Expired',    'class' => 'stt-failed'],
                'refunded'  => ['label' => 'Refunded',   'class' => 'stt-refunded'],
            ];
            $st = $statusMap[$paymentStatus] ?? ['label' => 'Pending', 'class' => 'stt-pending'];

            $deliveryStart = \Carbon\Carbon::parse($order->created_at)->addDays(8);
            $deliveryEnd   = \Carbon\Carbon::parse($order->created_at)->addDays(9);
        @endphp

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
                        <div class="my-account-content flat-animate-tab">

                            {{-- TOP SUMMARY CARD --}}
                            <div class="card p-4 mb-4 border shadow-sm rounded">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <p class="mb-1 text-muted small">ORDER ID</p>
                                        <h5 class="mb-0">#{{ $order->order_id }}</h5>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <p class="mb-1 text-muted small">ORDER DATE</p>
                                        <p class="mb-0 fw-semibold">
                                            {{ $order->created_at->format('d M Y, h:i A') }}
                                        </p>
                                    </div>
                                    <div class="col-md-3 mb-2 text-md-end">
                                        <p class="mb-1 text-muted small">STATUS</p>
                                        <span class="status-pill {{ $st['class'] }}">{{ $st['label'] }}</span>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <p class="mb-1 text-muted small">PAYMENT METHOD</p>
                                        <p class="mb-0 fw-semibold">
                                            {{ $isCod ? 'Cash on Delivery' : 'Online' }}
                                        </p>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <p class="mb-1 text-muted small">TOTAL AMOUNT</p>
                                        <p class="mb-0 fw-bold" style="font-size:18px; color:#000;">
                                            Rs. {{ number_format($order->total_price, 0) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- TABS --}}
                            <div class="account-order_tab">
                                <ul class="tab-order_detail" role="tablist">

                                    <li class="nav-tab-item" role="presentation">
                                        <a href="#item-detail"
                                           data-bs-toggle="tab"
                                           class="tf-btn-line tf-btn-tab active">
                                            <span class="h4">Items</span>
                                        </a>
                                    </li>

                                    <li class="nav-tab-item" role="presentation">
                                        <a href="#address-info"
                                           data-bs-toggle="tab"
                                           class="tf-btn-line tf-btn-tab">
                                            <span class="h4">Address</span>
                                        </a>
                                    </li>

                                    @if(!in_array($paymentStatus, ['failed', 'cancelled', 'expired']))
                                        <li class="nav-tab-item" role="presentation">
                                            <a href="#shipment"
                                               data-bs-toggle="tab"
                                               class="tf-btn-line tf-btn-tab">
                                                <span class="h4">Shipment</span>
                                            </a>
                                        </li>
                                    @endif

                                </ul>

                                <div class="tab-content overflow-hidden">

                                    {{-- ITEMS TAB --}}
                                    <div class="tab-pane active show" id="item-detail" role="tabpanel">
                                        <div class="order-item_detail card p-3 border shadow-sm rounded">

                                            @forelse($items as $item)
                                                <div class="prd-info">
                                                    <div class="info_image">
                                                        <img src="{{ $item['image'] }}"
                                                             alt="{{ $item['name'] }}"
                                                             onerror="this.src='{{ asset('images/no-image.png') }}'">
                                                    </div>

                                                    <div class="info_detail">
                                                        <span class="info-name">{{ $item['name'] }}</span>

                                                        @if($item['isOffer'])
                                                            <span class="bundle-badge">Bundle Offer</span>

                                                            @if(!empty($item['selectedItems']))
                                                                <ul class="bundle-items">
                                                                    @foreach($item['selectedItems'] as $sel)
                                                                        <li>
                                                                            {{ $sel['name'] ?? '' }}
                                                                            @if(!empty($sel['unit']))
                                                                                ({{ $sel['unit'] }})
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        @endif

                                                        <p class="info-qty">
                                                            Qty: <strong>{{ $item['isOffer'] ? '1 (Bundle)' : $item['qty'] }}</strong>
                                                        </p>

                                                        <p class="info-price">
                                                            @if($item['isOffer'] && $item['mrp'] > $item['price'])
                                                                <span class="mrp-strike">Rs. {{ number_format($item['mrp'], 0) }}</span>
                                                            @endif
                                                            <strong>Rs. {{ number_format($item['price'], 0) }}</strong>
                                                            <span class="text-muted ms-2">
                                                                (Subtotal: Rs. {{ number_format($item['subtotal'], 0) }})
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-center text-muted py-3">No items in this order.</p>
                                            @endforelse

                                            <div class="prd-order_total">
                                                <span class="fw-bold">Order Total</span>
                                                <span class="fw-bold" style="color:#000; font-size:20px;">
                                                    Rs. {{ number_format($order->total_price, 0) }}
                                                </span>
                                            </div>

                                        </div>
                                    </div>

                                    {{-- ADDRESS TAB --}}
                                    <div class="tab-pane" id="address-info" role="tabpanel">
                                        <div class="row">

                                            <div class="col-md-6">
                                                <div class="card p-3 border shadow-sm rounded h-100">
                                                    <h5 class="fw-bold mb-3">Shipping Address</h5>
                                                    <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                                                    <p class="mb-1">{{ $order->shipping_address ?? $order->street ?? 'N/A' }}</p>
                                                    <p class="mb-1">Pincode: {{ $order->postal_code ?? 'N/A' }}</p>
                                                    <p class="mb-1">Phone: {{ $order->customer_phone ?? 'N/A' }}</p>
                                                    <p class="mb-0">Email: {{ $order->customer_email ?? 'N/A' }}</p>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-3 mt-md-0">
                                                <div class="card p-3 border shadow-sm rounded h-100">
                                                    <h5 class="fw-bold mb-3">Billing Address</h5>
                                                    <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                                                    <p class="mb-1">{{ $order->billing_address ?? 'N/A' }}</p>
                                                    <p class="mb-1">Pincode: {{ $order->postal_code ?? 'N/A' }}</p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    {{-- SHIPMENT TAB --}}
                                    @if(!in_array($paymentStatus, ['failed', 'cancelled', 'expired']))
                                        <div class="tab-pane" id="shipment" role="tabpanel">
                                            <div class="card border shadow-sm p-3">
                                                <ul class="list-group list-group-flush">

                                                    <li class="list-group-item d-flex justify-content-between">
                                                        <strong>Courier Name</strong>
                                                        <span>{{ $order->courier_name ?? 'Not shipped yet' }}</span>
                                                    </li>

                                                    <li class="list-group-item d-flex justify-content-between">
                                                        <strong>AWB Code</strong>
                                                        <span>{{ $order->awb_code ?? 'N/A' }}</span>
                                                    </li>

                                                    <li class="list-group-item d-flex justify-content-between">
                                                        <strong>Delivery Status</strong>
                                                        <span>{{ $order->courier_status ?? 'N/A' }}</span>
                                                    </li>

                                                    <li class="list-group-item d-flex justify-content-between">
                                                        <strong>Receiver Name</strong>
                                                        <span>{{ $order->customer_name ?? 'N/A' }}</span>
                                                    </li>

                                                    <li class="list-group-item d-flex justify-content-between">
                                                        <strong>Receiver Phone</strong>
                                                        <span>{{ $order->customer_phone ?? 'N/A' }}</span>
                                                    </li>

                                                </ul>
                                            </div>
                                        </div>
                                    @endif

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

    {{-- main-js contains global Notyf + profile upload + session flash --}}
    @include('components.frontend.main-js')

</body>
</html>