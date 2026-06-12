<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.frontend.head')
    <style>
        .oc-banner { text-align: center; margin-bottom: 2.5rem; }
        .oc-banner-icon { font-size: 3rem; color: #4CAF50; margin-bottom: .75rem; }
        .oc-banner-icon.cod { color: #0c5460; }
        .oc-banner h2 { font-weight: 700; margin-bottom: .35rem; }
        .oc-banner h2.online { color: #155724; }
        .oc-banner h2.cod-title { color: #0c5460; }
        .oc-banner p { color: #6c757d; }

        .oc-card { padding: 1.5rem; border: 1px solid #e0e0e0; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.06); background: #fff; height: 100%; }

        .oc-meta-table { width: 100%; font-size: 17px; border-collapse: collapse; }
        .oc-meta-table td { padding: 6px 0; vertical-align: top; }
        .oc-meta-label { color: #6c757d; font-weight: 600; width: 45%; }

        .oc-badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 17px; font-weight: 600; }
        .oc-badge-cod     { background: #d1ecf1; color: #0c5460; }
        .oc-badge-online  { background: #d4edda; color: #155724; }
        .oc-badge-paid    { background: #d4edda; color: #155724; }
        .oc-badge-pending { background: #fff3cd; color: #856404; }
        .oc-badge-failed  { background: #f8d7da; color: #721c24; }
        .oc-badge-cancelled { background: #f8d7da; color: #721c24; }
        .oc-badge-refunded  { background: #e2e3e5; color: #383d41; }

        .oc-shipping-name { font-weight: 700; margin-bottom: 4px; }
        .oc-shipping-line { font-size: 17px; color: #6c757d; margin-bottom: 3px; }

        .oc-section-label { font-weight: 700; font-size: 17px; margin-bottom: .75rem; padding-bottom: 6px; border-bottom: 2px solid #e0e0e0; color: #333; }
        .oc-section-label.offer { border-color: #ffc107; color: #856404; }

        .oc-item-row { display: flex; align-items: flex-start; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px dashed #eee; }
        .oc-item-row:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .oc-item-img { width: 70px; height: 70px; object-fit: cover; border-radius: 6px; border: 1px solid #eee; flex-shrink: 0; }
        .oc-item-info { flex: 1; margin-left: 1rem; }
        .oc-item-name { font-weight: 700; color: #333; margin-bottom: 4px; font-size: 17px; }
        .oc-item-badge { display: inline-block; background: #fff3cd; color: #856404; font-size: 17px; padding: 2px 8px; border-radius: 3px; margin-bottom: 6px; }
        .oc-item-meta { font-size: 17px; color: #6c757d; }
        .oc-item-selected { margin: 4px 0 6px 0; padding-left: 16px; font-size: 17px; color: #666; }
        .oc-item-price { text-align: right; min-width: 90px; margin-left: .75rem; }
        .oc-price-final { font-weight: 700; color: #155724; font-size: 17px; }
        .oc-price-original { text-decoration: line-through; color: #6c757d; font-size: 17px; }
        .oc-saved { color: #dc3545; font-size: 17px; }

        .oc-totals { background: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 6px; padding: 1rem; margin-top: 1rem; }
        .oc-totals-row { display: flex; justify-content: space-between; margin-bottom: 4px; }
        .oc-totals-divider { border: none; border-top: 1px solid #ddd; margin: .5rem 0; }
        .oc-grand-label { font-weight: 700; font-size: 17px; }
        .oc-grand-value { font-weight: 700; font-size: 17px; color: #155724; }
        .oc-cod-note { font-size: 17px; color: #6c757d; margin-top: .5rem; margin-bottom: 0; }

        .oc-actions { display: flex; gap: 1rem; margin-top: 1.25rem; }
        .oc-actions .tf-btn { flex: 1; text-align: center; }
    </style>
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