<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.frontend.head')
</head>
<body>
<div id="wrapper">
    @include('components.frontend.header')

    <section class="s-page-title">
        <div class="container">
            <div class="content">
                <h1 class="title-page">Order Confirmed</h1>
                <ul class="breadcrumbs-page">
                    <li><a href="{{ route('frontend.index') }}" class="h6 link">Home</a></li>
                    <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                    <li><h6 class="current-page fw-normal">Order Confirmation</h6></li>
                </ul>
            </div>
        </div>
    </section>

    <div class="flat-spacing">
        <div class="container">

            {{-- ── Banner ── --}}
            <div class="oc-banner">
                @if($order->payment_method === 'cod')
                    <div class="oc-banner-icon cod">
                        <i class="icon icon-box"></i>
                    </div>
                    <h2 class="cod-title">Order Placed Successfully</h2>
                    <p>Your Cash-on-Delivery order has been received.</p>
                @else
                    <div class="oc-banner-icon">
                        <i class="icon icon-check-circle"></i>
                    </div>
                    <h2 class="online">Payment Successful</h2>
                    <p>Thank you for your purchase. Your order is confirmed.</p>
                @endif
            </div>

            <div class="row">

                {{-- ── LEFT: order meta ── --}}
                <div class="col-lg-5 mb-4">
                    <div class="oc-card">
                        <h4 class="fw-bold mb-3">Order Details</h4>

                        <table class="oc-meta-table">
                            <tr>
                                <td class="oc-meta-label">Order ID</td>
                                <td>#{{ $order->order_id }}</td>
                            </tr>
                            <tr>
                                <td class="oc-meta-label">Invoice </td>
                                <td>{{ $order->invoice_id ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="oc-meta-label">Order Date</td>
                                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, h:i A') }}</td>
                            </tr>
                            <tr>
                                <td class="oc-meta-label">Payment Method</td>
                                <td>
                                    @if($order->payment_method === 'cod')
                                        <span class="oc-badge oc-badge-cod">Cash on Delivery</span>
                                    @else
                                        <span class="oc-badge oc-badge-online">Online Payment</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="oc-meta-label">Payment Status</td>
                                <td>
                                    @php
                                        $ps = strtolower($order->payment_status ?? '');
                                    @endphp
                                    <span class="oc-badge oc-badge-{{ $ps }}">
                                        {{ strtoupper($order->payment_status) }}
                                    </span>
                                </td>
                            </tr>
                            @if(!empty($order->payment_id))
                            <tr>
                                <td class="oc-meta-label">Payment ID</td>
                                <td style="font-size:17px;">{{ $order->payment_id }}</td>
                            </tr>
                            @endif
                            <!--<tr>-->
                            <!--    <td class="oc-meta-label">Est. Delivery</td>-->
                            <!--    <td class="fw-bold" style="color:#155724;">-->
                            <!--        {{ \Carbon\Carbon::parse($order->created_at)->addDays(8)->format('d M') }}-->
                            <!--        – {{ \Carbon\Carbon::parse($order->created_at)->addDays(9)->format('d M Y') }}-->
                            <!--    </td>-->
                            <!--</tr>-->
                        </table>

                        <hr>

                        <h5 class="fw-bold mb-2">Shipping To</h5>
                        <p class="oc-shipping-name">{{ $order->customer_name }}</p>
                        <p class="oc-shipping-line">{{ $order->shipping_address }}</p>
                        <p class="oc-shipping-line">
                            {{ $order->street ?? '' }}
                            @if(!empty($order->postal_code)) – {{ $order->postal_code }}@endif
                        </p>
                        <p class="oc-shipping-line">
                            {{ $order->city_name ?? '' }}
                            @if(!empty($order->state_name)), {{ $order->state_name }}@endif
                            @if(!empty($order->country_name)), {{ $order->country_name }}@endif
                        </p>
                        <p class="oc-shipping-line">Phone: {{ $order->customer_phone }}</p>
                        <p class="oc-shipping-line">Email: {{ $order->customer_email }}</p>
                    </div>
                </div>

                {{-- ── RIGHT: items ── --}}
                <div class="col-lg-7 mb-4">
                    <div class="oc-card">
                        <h4 class="fw-bold mb-4">Items Ordered</h4>

                        @php
                            $offerProducts  = array_filter($orderProducts, fn($p) => $p['isOffer']);
                            $normalProducts = array_filter($orderProducts, fn($p) => !$p['isOffer']);
                        @endphp

                        {{-- Offer bundles --}}
                        @if(count($offerProducts) > 0)
                            <div class="mb-4">
                                <p class="oc-section-label offer">Offer Bundles</p>

                                @foreach($offerProducts as $prod)
                                    <div class="oc-item-row">
                                        <img src="{{ $prod['image'] }}"
                                             alt="{{ $prod['name'] }}"
                                             class="oc-item-img"
                                             onerror="this.src='{{ asset('images/no-image.png') }}'">

                                        <div class="oc-item-info">
                                            <p class="oc-item-name">{{ $prod['name'] }}</p>
                                            <span class="oc-item-badge">Bundle Offer</span>

                                            @if(!empty($prod['selectedProducts']))
                                                <ul class="oc-item-selected">
                                                    @foreach($prod['selectedProducts'] as $sel)
                                                        <li>
                                                            {{ $sel['name'] ?? '' }}
                                                            @if(!empty($sel['unit'])) ({{ $sel['unit'] }})@endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif

                                            <div class="oc-item-meta">
                                                Qty: 1
                                                @if($prod['hasOffer'])
                                                    &nbsp;
                                                    <span class="oc-price-original">
                                                        Rs. {{ number_format($prod['price'], 0) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="oc-item-price">
                                            <div class="oc-price-final">Rs. {{ number_format($prod['finalPrice'], 0) }}</div>
                                            @if($prod['discount'] > 0)
                                                <div class="oc-saved">Saved Rs. {{ number_format($prod['discount'], 0) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Normal products --}}
                        @if(count($normalProducts) > 0)
                            <div class="mb-2">
                                @if(count($offerProducts) > 0)
                                    <p class="oc-section-label">Products</p>
                                @endif

                                @foreach($normalProducts as $prod)
                                    @php
                                        $imgUrl = $prod['imageIsFullUrl']
                                            ? $prod['image']
                                            : asset('signage/home/productimage/' . $prod['image']);
                                    @endphp
                                    <div class="oc-item-row">
                                        <img src="{{ $imgUrl }}"
                                             alt="{{ $prod['name'] }}"
                                             class="oc-item-img"
                                             onerror="this.src='{{ asset('images/no-image.png') }}'">

                                        <div class="oc-item-info">
                                            <p class="oc-item-name">{{ $prod['name'] }}</p>
                                            @if($prod['fragrance'] !== 'NA')
                                                <p class="oc-item-meta mb-1">Variant: {{ $prod['fragrance'] }}</p>
                                            @endif
                                            <div class="oc-item-meta">
                                                Qty: {{ $prod['quantity'] }}
                                                @if($prod['hasOffer'])
                                                    &nbsp;
                                                    <span class="oc-price-original">
                                                        Rs. {{ number_format($prod['price'], 0) }}
                                                    </span>
                                                @endif
                                                &nbsp; x Rs. {{ number_format($prod['offerPrice'], 0) }} each
                                            </div>
                                        </div>

                                        <div class="oc-item-price">
                                            <div class="oc-price-final">Rs. {{ number_format($prod['finalPrice'], 0) }}</div>
                                            @if($prod['discount'] > 0)
                                                <div class="oc-saved">Saved Rs. {{ number_format($prod['discount'], 0) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Totals --}}
                        <div class="oc-totals">
                            <div class="oc-totals-row">
                                <span class="text-muted">Shipping</span>
                                <span class="fw-semibold" style="color:#155724;">FREE</span>
                            </div>
                            <hr class="oc-totals-divider">
                            <div class="oc-totals-row">
                                <span class="oc-grand-label">
                                    @if($order->payment_method === 'cod') Amount Due (COD) @else Total Paid @endif
                                </span>
                                <span class="oc-grand-value">Rs. {{ number_format($order->total_price, 0) }}</span>
                            </div>
                            @if($order->payment_method === 'cod')
                                <p class="oc-cod-note">
                                    Please keep <strong>Rs. {{ number_format($order->total_price, 0) }}</strong> ready for the delivery person.
                                </p>
                            @endif
                        </div>

                        {{-- CTA --}}
                        <div class="oc-actions">
                            <a href="{{ route('frontend.index') }}"
                               class="tf-btn btn-grey animate-btn animate-dark">
                                Continue Shopping
                            </a>
                            @if(!empty($order->invoice_id))
                                <a href="{{ asset('signage/invoices/invoice_' . $order->invoice_id . '.pdf') }}"
                                   target="_blank"
                                   class="tf-btn animate-btn">
                                    Download Invoice
                                </a>
                            @endif
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('components.frontend.footer')
</div>
@include('components.frontend.main-js')
</body>
</html>