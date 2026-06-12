<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->invoice_id ?? '-' }}</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url("{{ storage_path('fonts/DejaVuSans.ttf') }}") format('truetype');
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        h2, h3 { margin-bottom: 5px !important; }
        p { margin: 0 0 8px 0; }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .table th, .table td {
            border: 1px solid #333;
            padding: 7px;
            text-align: left;
            vertical-align: middle;
        }
        .table th {
            background: #f4f4f4;
            font-weight: bold;
            font-size: 12px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }

        /* Company block under the logo */
        .company-name {
            margin: 0 0 4px 0;
            font-size: 15px;
            font-weight: bold;
            color: #004385;
        }
        .company-meta {
            margin: 0;
            font-size: 11px;
            color: #555;
            line-height: 1.5;
        }

        /* ── FIXED: footer no longer uses position:fixed ── */
        /* position:fixed in DomPDF renders at bottom of EVERY page,  */
        /* causing it to appear mid-document when content spans pages. */
        .footer {
            background: #000;
            padding: 8px;
            text-align: center;
            font-size: 12px;
            color: #fff;
            margin-top: 20px;
        }

        .product-thumb {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>

@php
    /* ── Status helpers ── */
    $statusMap = [
        'paid'      => ['bg' => '#d4edda', 'text' => '#155724', 'label' => 'PAID'],
        'failed'    => ['bg' => '#f8d7da', 'text' => '#721c24', 'label' => 'FAILED'],
        'expired'   => ['bg' => '#e2e3e5', 'text' => '#383d41', 'label' => 'EXPIRED'],
        'cancelled' => ['bg' => '#f8d7da', 'text' => '#721c24', 'label' => 'CANCELLED'],
        'refunded'  => ['bg' => '#e2e3e5', 'text' => '#383d41', 'label' => 'REFUNDED'],
    ];
    $status     = strtolower(trim($order->payment_status ?? ''));
    $statusInfo = $statusMap[$status] ?? null;
    $isCod      = strtolower($order->payment_method ?? '') === 'cod';
    $isPaid     = ($status === 'paid');

    $orderDate     = $order->created_at ?? now();
    $deliveryStart = \Carbon\Carbon::parse($orderDate)->addDays(8);
    $deliveryEnd   = \Carbon\Carbon::parse($orderDate)->addDays(9);

    $billingCity    = \DB::table('main_cities')->where('id', $order->city)->value('name');
    $billingState   = \DB::table('main_states')->where('id', $order->state)->value('name');
    $billingCountry = \DB::table('main_countries')->where('id', $order->country)->value('name');

    /* ══════════════════════════════════════════════════════
     | IMAGE HELPER — returns base64 data-URI for DomPDF
     ══════════════════════════════════════════════════════ */
    $getPdfImage = function (string $filename, string $folder = 'product'): ?string {
        if (empty($filename)) return null;

        $filename = basename(trim($filename));
        if (str_contains($filename, '?')) $filename = strtok($filename, '?');

        $path = $folder === 'offer'
            ? public_path('offerimage/' . $filename)
            : public_path('signage/home/productimage/' . $filename);

        if (!file_exists($path)) return null;

        try {
            $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $mime = in_array($ext, ['jpg', 'jpeg']) ? 'jpeg' : $ext;
            return 'data:image/' . $mime . ';base64,' . base64_encode(file_get_contents($path));
        } catch (\Throwable $e) {
            return null;
        }
    };

    /* ══════════════════════════════════════════════════════
     | Decode order arrays
     ══════════════════════════════════════════════════════ */
    $productIds   = json_decode($order->product_ids   ?? '[]', true) ?? [];
    $productNames = json_decode($order->product_names ?? '[]', true) ?? [];
    $quantities   = json_decode($order->quantities    ?? '[]', true) ?? [];
    $prices       = json_decode($order->prices        ?? '[]', true) ?? [];
    $orderImages  = json_decode($order->images        ?? '[]', true) ?? [];
    $offerIds     = json_decode($order->offer_ids     ?? '[]', true) ?? [];
    $offerDataArr = json_decode($order->offer_data    ?? '[]', true) ?? [];

    /* Pre-fetch normal products */
    $normalPids  = array_unique(array_filter($productIds, fn($p) => (int) $p > 0));
    $productsRaw = !empty($normalPids)
        ? \DB::table('products_details')->whereIn('id', $normalPids)->get()->keyBy('id')
        : collect();

    /* ══════════════════════════════════════════════════════
     | Build per-row base64 image
     ══════════════════════════════════════════════════════ */
    $itemImages = [];

    foreach ($productIds as $i => $pid) {
        $oid = (int) ($offerIds[$i] ?? 0);

        if ($oid > 0) {
            /* ── OFFER ROW ── */
            $od       = $offerDataArr[$i] ?? [];
            $offerImg = $od['offer_image'] ?? ($orderImages[$i] ?? '');

            /* Normalise: strip to filename if full URL */
            if (str_starts_with((string) $offerImg, 'http')) {
                $offerImg = basename(parse_url($offerImg, PHP_URL_PATH));
            }

            $itemImages[$i] = !empty($offerImg)
                ? $getPdfImage($offerImg, 'offer')
                : null;

        } else {
            /* ── NORMAL ROW ── */
            $raw = $orderImages[$i] ?? null;

            if (empty($raw) && isset($productsRaw[(int) $pid])) {
                $pImages = json_decode($productsRaw[(int) $pid]->images ?? '[]', true);
                $raw     = $pImages[0] ?? null;
            }

            /* Handle JSON-encoded image array stored as string */
            if (is_string($raw) && str_starts_with(trim($raw), '[')) {
                $dec = json_decode($raw, true);
                $raw = is_array($dec) ? ($dec[0] ?? null) : $raw;
            }

            $itemImages[$i] = !empty($raw)
                ? $getPdfImage(basename((string) $raw), 'product')
                : null;
        }
    }
@endphp

{{-- ══════════════════════════════════════════════
     HEADER — Logo + Company details + Invoice meta
══════════════════════════════════════════════ --}}
<table width="100%">
    <tr>
        <td width="60%" style="padding: 10px; vertical-align: top;">
           

            {{-- Company name + registered details --}}
            <div style="margin-top: 10px;">
                <p class="company-name">LOGASSADIVINE PRIVATE LIMITED</p>
                <p class="company-meta">
                    L-36, Phase 2, APMC Market 1,<br>
                    Vashi, Raigad, Maharashtra - 400705<br>
                    <strong>GSTIN:</strong> 27AAEC2607J9C1ZH
                </p>
            </div>
            <br>
             <img src="data:image/webp;base64,{{ base64_encode(file_get_contents(public_path('frontend/assets/images/logo/logo.webp'))) }}"
                 alt="Logo"
                 style="width: 180px; background: #001e3b; padding: 8px; border-radius: 4px;">
        </td>
        <td width="40%" style="text-align: right; padding: 10px; vertical-align: top;">
            <h2 style="margin: 0;">INVOICE</h2>
            <p style="margin: 4px 0;"><strong>#{{ $order->invoice_id ?? '-' }}</strong></p>
            <p style="margin: 4px 0;"><strong>Date:</strong> {{ date('d M Y', strtotime($orderDate)) }}</p>
            <!--<p style="margin: 4px 0;">-->
            <!--    <strong>Expected:</strong>-->
            <!--    {{ $deliveryStart->format('d M') }} – {{ $deliveryEnd->format('d M Y') }}-->
            <!--</p>-->

            @if($isCod)
                <p style="margin: 4px 0;">
                    <span class="status-badge" style="background:#d1ecf1; color:#0c5460;">
                        ORDER PLACED (COD)
                    </span>
                </p>
            @elseif($statusInfo)
                <p style="margin: 4px 0;">
                    <span class="status-badge"
                          style="background:{{ $statusInfo['bg'] }}; color:{{ $statusInfo['text'] }};">
                        {{ $statusInfo['label'] }}
                    </span>
                </p>
            @endif
        </td>
    </tr>
</table>

{{-- ══════════════════════════════════════════════
     ADDRESSES
══════════════════════════════════════════════ --}}
<table width="100%" style="margin-bottom: 15px;">
    <tr>
        <td width="50%" style="vertical-align: top; padding: 10px; background: #f5f5f5;">
            <h3 style="font-size: 14px; margin-bottom: 6px;">Billing Address</h3>
            <p style="margin: 0;">
                <strong>{{ $order->customer_name }}</strong><br>
                {{ $order->billing_address ?? '-' }}<br>
                <strong>Street:</strong> {{ $order->street ?? '-' }}<br>
                <strong>City:</strong> {{ $billingCity ?? '-' }}<br>
                <strong>State:</strong> {{ $billingState ?? '-' }}<br>
                <strong>Country:</strong> {{ $billingCountry ?? '-' }}<br>
                <strong>Pincode:</strong> {{ $order->postal_code ?? '-' }}
            </p>
        </td>
        <td width="50%" style="vertical-align: top; padding: 10px; background: #f5f5f5;">
            <h3 style="font-size: 14px; margin-bottom: 6px;">Shipping Address</h3>
            <p style="margin: 0;">
                <strong>{{ $order->customer_name }}</strong><br>
                {{ $order->shipping_address ?? '-' }}<br>
                <strong>Street:</strong> {{ $order->street ?? '-' }}<br>
                <strong>City:</strong> {{ $billingCity ?? '-' }}<br>
                <strong>State:</strong> {{ $billingState ?? '-' }}<br>
                <strong>Country:</strong> {{ $billingCountry ?? '-' }}<br>
                <strong>Pincode:</strong> {{ $order->postal_code ?? '-' }}
            </p>
        </td>
    </tr>
</table>

{{-- ══════════════════════════════════════════════
     CUSTOMER + PAYMENT DETAILS
══════════════════════════════════════════════ --}}
<table width="100%" style="margin-bottom: 15px;">
    <tr>
        <td width="50%" style="padding: 10px;">
            <h3 style="font-size: 14px; margin-bottom: 6px;">Customer Details</h3>
            <p style="margin: 0;">
                <strong>Name:</strong> {{ $order->customer_name ?? '-' }}<br>
                <strong>Email:</strong> {{ $order->customer_email ?? '-' }}<br>
                <strong>Phone:</strong> +91 {{ $order->customer_phone ?? '-' }}
            </p>
        </td>
        <td width="50%" style="padding: 10px;">
            <h3 style="font-size: 14px; margin-bottom: 6px;">Payment Details</h3>
            <p style="margin: 0;">
                <strong>Method:</strong> {{ $isCod ? 'CASH ON DELIVERY' : 'ONLINE' }}<br>
                <strong>Order Status:</strong>
                @if($isCod) ORDER PLACED
                @elseif($statusInfo) {{ $statusInfo['label'] }}
                @endif
                <!--<strong>Expected Delivery:</strong>-->
                <!--{{ $deliveryStart->format('d M') }} – {{ $deliveryEnd->format('d M Y') }} (8–9 days)<br>-->
                @if(!empty($order->payment_id))
                    <strong>Payment ID:</strong> {{ $order->payment_id }}
                @endif
            </p>
        </td>
    </tr>
</table>

{{-- ══════════════════════════════════════════════
     LINE ITEMS TABLE
══════════════════════════════════════════════ --}}
<table class="table">
    <thead>
        <tr>
            <th style="width:30px;">#</th>
            <th style="width:55px;">Image</th>
            <th>Product / Bundle</th>
            <th style="width:35px;">Qty</th>
            <th style="width:80px;">MRP</th>
            <th style="width:90px;">Discount</th>
            <th style="width:85px;">Amount</th>
        </tr>
    </thead>
    <tbody>
        @php $totalAmount = 0; $totalDiscount = 0; @endphp

        @foreach($invoiceItems as $index => $item)
            <tr>
                {{-- # --}}
                <td>{{ $index + 1 }}</td>

                {{-- Image --}}
                <td>
                    @if(!empty($itemImages[$index]))
                        <img src="{{ $itemImages[$index] }}"
                             class="product-thumb"
                             alt="{{ $item['name'] }}">
                    @else
                        <span style="font-size:10px; color:#999;">No img</span>
                    @endif
                </td>

                {{-- Product / Bundle name --}}
                <td>
                    {{ $item['name'] }}

                    @if(!empty($item['isCombo']))
                        <br>
                        <small style="color:#856404;">(Bundle / Offer)</small>

                        @php
                            $od               = $offerDataArr[$index] ?? [];
                            $selectedInBundle = $od['selected'] ?? [];
                        @endphp

                        @if(!empty($selectedInBundle))
                            <ul style="margin:3px 0 0 0; padding-left:14px;
                                       font-size:11px; color:#555;">
                                @foreach($selectedInBundle as $sel)
                                    <li>
                                        {{ $sel['name'] ?? '' }}
                                        @if(!empty($sel['unit'])) ({{ $sel['unit'] }}) @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    @endif
                </td>

                {{-- Qty --}}
                <td>{{ $item['quantity'] }}</td>

                {{-- MRP --}}
                <td>₹{{ number_format($item['rate'], 0) }}</td>

                {{-- Discount --}}
                <td>
                    @if($item['discount'] > 0)
                        ₹{{ number_format($item['discount'], 0) }} OFF
                    @else
                        —
                    @endif
                </td>

                {{-- Amount --}}
                <td>₹{{ number_format($item['amount'], 0) }}</td>
            </tr>

            @php
                $totalAmount   += $item['amount'];
                $totalDiscount += $item['discount'];
            @endphp
        @endforeach
    </tbody>

    <tfoot>
        @if($totalDiscount > 0)
            <tr>
                <td colspan="6" style="text-align: right;">
                    <strong>Total Discount:</strong>
                </td>
                <td style="color: #dc3545;">
                    ₹{{ number_format($totalDiscount, 0) }}
                </td>
            </tr>
        @endif

        <tr>
            <td colspan="6" style="text-align: right;">
                <strong>{{ $isCod ? 'Amount Due (COD)' : 'Grand Total' }}:</strong>
            </td>
            <td>
                <strong>₹{{ number_format($order->total_price, 0) }}</strong>
            </td>
        </tr>
    </tfoot>
</table>

{{-- ══════════════════════════════════════════════
     STATUS NOTE
══════════════════════════════════════════════ --}}
@if($isCod)
    <div style="padding:10px; background:#d1ecf1; color:#0c5460;
                border-radius:4px; margin-bottom:20px;">
        <strong>Cash on Delivery</strong> — Your order was placed on
        {{ date('d M Y', strtotime($orderDate)) }}.
        <!--Expected delivery:-->
        <!--<strong>{{ $deliveryStart->format('d M') }} – {{ $deliveryEnd->format('d M Y') }}</strong>-->
        <!--(approximately 8–9 days).-->
        <!--Please keep ₹{{ number_format($order->total_price, 0) }} ready at delivery.-->
    </div>

@elseif($isPaid)
    <div style="padding:10px; background:#d4edda; color:#155724;
                border-radius:4px; margin-bottom:20px;">
        <strong>Payment Received</strong> — Confirmed on
        {{ date('d M Y', strtotime($orderDate)) }}.
        <!--Expected delivery:-->
        <!--<strong>{{ $deliveryStart->format('d M') }} – {{ $deliveryEnd->format('d M Y') }}</strong>-->
        <!--(approximately 8–9 days).-->
    </div>

@elseif($statusInfo)
    <div style="padding:10px;
                background:{{ $statusInfo['bg'] }};
                color:{{ $statusInfo['text'] }};
                border-radius:4px; margin-bottom:20px;">
        <strong>Status: {{ $statusInfo['label'] }}</strong>
        @if($status === 'failed')    — please retry payment.
        @elseif($status === 'cancelled') — this order is cancelled.
        @elseif($status === 'expired')   — session expired, please reorder.
        @elseif($status === 'refunded')  — amount has been refunded.
        @endif
    </div>
@endif

{{-- ══════════════════════════════════════════════
     FOOTER — flows naturally, NOT position:fixed
     position:fixed in DomPDF prints on every page
     which caused it to appear mid-document.
══════════════════════════════════════════════ --}}
<!--<div class="footer">-->
<!--    <b>&copy; {{ date('Y') }} Signage. All rights reserved.</b>-->
<!--</div>-->

</body>
</html>