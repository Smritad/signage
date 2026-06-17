<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->invoice_id ?? 'Order' }}</title>
</head>
<body style="margin:0; padding:0; font-family: Arial, Helvetica, sans-serif; background-color:#f4f4f4;">

    @php
    $statusMap = [
        'paid'      => ['bg'=>'#d4edda', 'text'=>'#155724', 'label'=>'PAID'],
        'failed'    => ['bg'=>'#f8d7da', 'text'=>'#721c24', 'label'=>'FAILED'],
        'expired'   => ['bg'=>'#e2e3e5', 'text'=>'#383d41', 'label'=>'EXPIRED'],
        'cancelled' => ['bg'=>'#f8d7da', 'text'=>'#721c24', 'label'=>'CANCELLED'],
        'refunded'  => ['bg'=>'#e2e3e5', 'text'=>'#383d41', 'label'=>'REFUNDED'],
    ];
    $status     = strtolower(trim($order->payment_status ?? ''));
    $statusInfo = $statusMap[$status] ?? null;
    $isPaid     = ($status === 'paid');
    $isCod      = strtolower($order->payment_method ?? '') === 'cod';

    $orderDate     = $order->created_at ?? now();
    $deliveryStart = \Carbon\Carbon::parse($orderDate)->addDays(8);
    $deliveryEnd   = \Carbon\Carbon::parse($orderDate)->addDays(9);

    $buildImageUrl = function ($raw, string $folder = 'product') : string {
        $default = asset('/home/productimage/default.png');
        if (empty($raw)) return $default;
        if (str_starts_with((string)$raw, 'http://') || str_starts_with((string)$raw, 'https://')) {
            return (string)$raw;
        }
        if (is_string($raw) && str_starts_with(trim($raw), '[')) {
            $d = json_decode($raw, true);
            if (is_array($d) && !empty($d)) $raw = $d[0];
        }
        if (is_array($raw)) $raw = $raw[0] ?? null;
        if (empty($raw)) return $default;
        $filename = basename(trim((string) $raw));
        if (str_contains($filename, '?')) $filename = strtok($filename, '?');
        return $folder === 'offer'
            ? asset('offerimage/' . $filename)
            : asset('signage/home/productimage/' . $filename);
    };

    $productIds   = json_decode($order->product_ids   ?? '[]', true) ?? [];
    $productNames = json_decode($order->product_names ?? '[]', true) ?? [];
    $prices       = json_decode($order->prices        ?? '[]', true) ?? [];
    $quantities   = json_decode($order->quantities    ?? '[]', true) ?? [];
    $orderImages  = json_decode($order->images        ?? '[]', true) ?? [];
    $offerIds     = json_decode($order->offer_ids     ?? '[]', true) ?? [];
    $offerDataArr = json_decode($order->offer_data    ?? '[]', true) ?? [];

    $normalPids  = array_unique(array_filter($productIds, fn($p) => (int)$p > 0));
    $productsRaw = !empty($normalPids)
                   ? \DB::table('products_details')->whereIn('id', $normalPids)->get()->keyBy('id')
                   : collect();

    $offerRows = $normalRows = [];
    $subtotalMRP = $subtotalPaid = $offerMRPTotal = $offerFinalTotal = 0;

    foreach ($productIds as $i => $pid) {
        $oid = (int) ($offerIds[$i] ?? 0);

        if ($oid > 0) {
            $od         = $offerDataArr[$i] ?? [];
            $finalPrice = (float) ($od['final_price'] ?? $prices[$i] ?? 0);
            $mrpTotal   = (float) ($od['mrp_total']   ?? $finalPrice);
            $offerMRPTotal   += $mrpTotal;
            $offerFinalTotal += $finalPrice;
            $offerImgRaw = $od['offer_image'] ?? ($orderImages[$i] ?? '');

            $offerRows[] = [
                'name'     => $od['offer_name'] ?? ($productNames[$i] ?? 'Bundle Offer'),
                'qty'      => 1,
                'mrp'      => $mrpTotal,
                'final'    => $finalPrice,
                'discount' => max(0, $mrpTotal - $finalPrice),
                'imageUrl' => $buildImageUrl($offerImgRaw, 'offer'),
                'selected' => $od['selected'] ?? [],
            ];
        } else {
            $product  = isset($productsRaw[(int)$pid]) ? $productsRaw[(int)$pid] : null;
            $qty      = (int)   ($quantities[$i] ?? 1);
            $paidPerU = (float) ($prices[$i]     ?? 0);
            $mrpPerU  = $product ? (float) $product->price : $paidPerU;
            $name     = $productNames[$i] ?? ($product->product_name ?? 'Product');

            $imgRaw = $orderImages[$i] ?? null;
            if (empty($imgRaw) && $product) {
                $pImages = json_decode($product->images ?? '[]', true);
                $imgRaw  = $pImages[0] ?? null;
            }

            $lineMRP  = $mrpPerU  * $qty;
            $linePaid = $paidPerU * $qty;
            $subtotalMRP  += $lineMRP;
            $subtotalPaid += $linePaid;

            $normalRows[] = [
                'name'     => $name,
                'qty'      => $qty,
                'mrpPerU'  => $mrpPerU,
                'paidPerU' => $paidPerU,
                'lineMRP'  => $lineMRP,
                'linePaid' => $linePaid,
                'hasOffer' => $paidPerU < $mrpPerU,
                'imageUrl' => $buildImageUrl($imgRaw, 'product'),
            ];
        }
    }

    $offerSavings  = max(0, $offerMRPTotal - $offerFinalTotal);
    $normalSavings = max(0, $subtotalMRP   - $subtotalPaid);
    $totalSavings  = $offerSavings + $normalSavings;
    $totalPrice    = (float) ($order->total_price ?? 0);
    @endphp

    <div style="max-width:640px; margin:30px auto; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 0 15px rgba(0,0,0,0.08);">

        {{-- HEADER --}}
        <div style="background:#000; text-align:center; padding:25px 0;">
            <img src="https://anvayafoundation.com/signage_live/frontend/assets/images/logo/logo.webp"
                 alt="Signage"
                 style="height:70px; display:block; margin:0 auto;">
        </div>

        {{-- GREETING --}}
        <div style="padding:30px 30px 10px 30px; text-align:center;">
            <h2 style="color:#333; margin:0 0 10px 0; font-size:22px; font-weight:700;">
                @if($isCod)      Your Order Has Been Placed
                @elseif($isPaid) Thank You for Your Order
                @else            Order Update
                @endif
            </h2>
            <p style="color:#555; font-size:15px; line-height:1.6; margin:0;">
                Dear <strong>{{ $order->customer_name }}</strong>,<br>
                @if($isCod)
                    Your order has been placed successfully on
                    <strong>{{ \Carbon\Carbon::parse($orderDate)->format('d M Y') }}</strong>.
                @elseif($isPaid)
                    Your order has been confirmed on
                    <strong>{{ \Carbon\Carbon::parse($orderDate)->format('d M Y') }}</strong>.
                @endif
            </p>
        </div>

        {{-- ORDER META --}}
        <div style="padding:20px 30px;">
            <table width="100%" cellpadding="0" cellspacing="0"
                   style="background:#f9f9f9; border:1px solid #e0e0e0; border-radius:8px;">
                <tr>
                    <td style="padding:20px;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="width:60%; vertical-align:top;">
                                    <p style="margin:0 0 4px 0; color:#888; font-size:11px; letter-spacing:.5px;">INVOICE NUMBER</p>
                                    <p style="margin:0 0 14px 0; color:#222; font-size:16px; font-weight:700;">#{{ $order->invoice_id ?? 'N/A' }}</p>

                                    <p style="margin:0 0 4px 0; color:#888; font-size:11px; letter-spacing:.5px;">ORDER ID</p>
                                    <p style="margin:0 0 14px 0; color:#222; font-size:14px;">#{{ $order->order_id }}</p>

                                    <p style="margin:0 0 4px 0; color:#888; font-size:11px; letter-spacing:.5px;">ORDER DATE</p>
                                    <p style="margin:0 0 14px 0; color:#222; font-size:14px;">{{ date('d M Y, h:i A', strtotime($orderDate)) }}</p>

                                    <!--<p style="margin:0 0 4px 0; color:#888; font-size:11px; letter-spacing:.5px;">EXPECTED DELIVERY</p>-->
                                    <!--<p style="margin:0; color:#222; font-size:14px; font-weight:600;">-->
                                    <!--    {{ $deliveryStart->format('d M') }} – {{ $deliveryEnd->format('d M Y') }}-->
                                    <!--</p>-->
                                </td>
                                <td style="width:40%; vertical-align:top; text-align:right;">
                                    @if($isCod)
                                        <p style="margin:0 0 4px 0; color:#888; font-size:11px; letter-spacing:.5px;">ORDER STATUS</p>
                                        <span style="display:inline-block; background:#d1ecf1; color:#0c5460;
                                                     padding:6px 14px; border-radius:4px;
                                                     font-size:12px; font-weight:700; letter-spacing:.5px;">
                                            ORDER PLACED
                                        </span>
                                    @elseif($statusInfo)
                                        <p style="margin:0 0 4px 0; color:#888; font-size:11px; letter-spacing:.5px;">PAYMENT STATUS</p>
                                        <span style="display:inline-block;
                                                     background:{{ $statusInfo['bg'] }};
                                                     color:{{ $statusInfo['text'] }};
                                                     padding:6px 14px; border-radius:4px;
                                                     font-size:12px; font-weight:700; letter-spacing:.5px;">
                                            {{ $statusInfo['label'] }}
                                        </span>
                                    @endif

                                    <p style="margin:14px 0 4px 0; color:#888; font-size:11px; letter-spacing:.5px;">PAYMENT METHOD</p>
                                    <p style="margin:0; color:#222; font-size:13px; font-weight:600;">
                                        {{ $isCod ? 'CASH ON DELIVERY' : 'ONLINE' }}
                                    </p>

                                    @if(!empty($order->payment_id))
                                        <p style="margin:12px 0 4px 0; color:#888; font-size:11px; letter-spacing:.5px;">PAYMENT ID</p>
                                        <p style="margin:0; color:#222; font-size:12px;">{{ $order->payment_id }}</p>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        {{-- OFFER / BUNDLE ROWS --}}
        @if(count($offerRows) > 0)
        <div style="padding:0 30px 10px 30px;">
            <h3 style="color:#333; font-size:15px; font-weight:700; margin:0 0 12px 0;
                       border-bottom:2px solid #e0a800; padding-bottom:6px; letter-spacing:.5px;">
                OFFER BUNDLES
            </h3>
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; font-size:14px;">
                <thead>
                    <tr style="background:#fff8e1;">
                        <th style="padding:10px; text-align:left; font-size:11px; color:#666; letter-spacing:.5px;">BUNDLE</th>
                        <th style="padding:10px; text-align:center; font-size:11px; color:#666; letter-spacing:.5px;">QTY</th>
                        <th style="padding:10px; text-align:right; font-size:11px; color:#666; letter-spacing:.5px;">MRP</th>
                        <th style="padding:10px; text-align:right; font-size:11px; color:#666; letter-spacing:.5px;">YOU PAY</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($offerRows as $row)
                    <tr>
                        <td style="padding:12px 10px; border-bottom:1px solid #eee;">
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding-right:10px; vertical-align:top;">
                                        <img src="{{ $row['imageUrl'] }}"
                                             alt="{{ $row['name'] }}"
                                             width="56" height="56"
                                             style="width:56px;height:56px;object-fit:cover;border-radius:6px;border:1px solid #eee;">
                                    </td>
                                    <td style="vertical-align:top;">
                                        <strong style="color:#222; font-size:13px; display:block;">{{ $row['name'] }}</strong>
                                        <span style="font-size:11px; color:#856404; background:#fff3cd;
                                                     padding:2px 6px; border-radius:3px;
                                                     display:inline-block; margin-top:4px;">
                                            Bundle Offer
                                        </span>
                                        @if(!empty($row['selected']))
                                            <ul style="margin:6px 0 0 0; padding-left:14px;
                                                       font-size:11px; color:#555; line-height:1.7;">
                                                @foreach($row['selected'] as $sel)
                                                    <li>
                                                        {{ $sel['name'] ?? '' }}
                                                        @if(!empty($sel['unit'])) ({{ $sel['unit'] }}) @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="padding:12px 10px; border-bottom:1px solid #eee; text-align:center; color:#555;">
                            {{ $row['qty'] }}
                        </td>
                        <td style="padding:12px 10px; border-bottom:1px solid #eee; text-align:right;">
                            @if($row['discount'] > 0)
                                <span style="color:#999; text-decoration:line-through; font-size:11px; display:block;">
                                    Rs. {{ number_format($row['mrp'], 0) }}
                                </span>
                            @else
                                <span style="color:#222;">Rs. {{ number_format($row['mrp'], 0) }}</span>
                            @endif
                        </td>
                        <td style="padding:12px 10px; border-bottom:1px solid #eee;
                                   text-align:right; font-weight:700; color:#155724;">
                            Rs. {{ number_format($row['final'], 0) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($offerSavings > 0)
            <div style="margin-top:8px; padding:12px; background:#fff8e1; border-radius:6px; font-size:13px;">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="color:#856404; padding:3px 0;">Bundle MRP</td>
                        <td style="text-align:right; color:#856404; padding:3px 0;">Rs. {{ number_format($offerMRPTotal, 0) }}</td>
                    </tr>
                    <tr>
                        <td style="color:#dc3545; padding:3px 0;">You Save</td>
                        <td style="text-align:right; color:#dc3545; padding:3px 0;">- Rs. {{ number_format($offerSavings, 0) }}</td>
                    </tr>
                    <tr>
                        <td style="color:#155724; font-weight:700; padding:3px 0;">Bundle Total</td>
                        <td style="text-align:right; color:#155724; font-weight:700; padding:3px 0;">Rs. {{ number_format($offerFinalTotal, 0) }}</td>
                    </tr>
                </table>
            </div>
            @endif
        </div>
        @endif

        {{-- NORMAL PRODUCT ROWS --}}
        @if(count($normalRows) > 0)
        <div style="padding:0 30px 10px 30px;">
            <h3 style="color:#333; font-size:15px; font-weight:700; letter-spacing:.5px;
                       margin:{{ count($offerRows) ? '20px' : '0' }} 0 12px 0;
                       border-bottom:2px solid #333; padding-bottom:6px;">
                PRODUCTS
            </h3>
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; font-size:14px;">
                <thead>
                    <tr style="background:#f5f5f5;">
                        <th style="padding:10px; text-align:left; font-size:11px; color:#666; letter-spacing:.5px;">ITEM</th>
                        <th style="padding:10px; text-align:center; font-size:11px; color:#666; letter-spacing:.5px;">QTY</th>
                        <th style="padding:10px; text-align:right; font-size:11px; color:#666; letter-spacing:.5px;">UNIT PRICE</th>
                        <th style="padding:10px; text-align:right; font-size:11px; color:#666; letter-spacing:.5px;">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($normalRows as $row)
                    <tr>
                        <td style="padding:12px 10px; border-bottom:1px solid #eee;">
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding-right:10px; vertical-align:top;">
                                        <img src="{{ $row['imageUrl'] }}"
                                             alt="{{ $row['name'] }}"
                                             width="50" height="50"
                                             style="width:50px;height:50px;object-fit:cover;border-radius:6px;border:1px solid #eee;">
                                    </td>
                                    <td style="vertical-align:top; color:#222; font-size:13px; font-weight:600;">
                                        {{ $row['name'] }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="padding:12px 10px; border-bottom:1px solid #eee; text-align:center; color:#555;">
                            {{ $row['qty'] }}
                        </td>
                        <td style="padding:12px 10px; border-bottom:1px solid #eee; text-align:right;">
                            @if($row['hasOffer'])
                                <span style="color:#999; text-decoration:line-through; font-size:11px; display:block;">
                                    Rs. {{ number_format($row['mrpPerU'], 0) }}
                                </span>
                                <span style="color:#dc3545; font-size:13px; font-weight:600;">
                                    Rs. {{ number_format($row['paidPerU'], 0) }}
                                </span>
                            @else
                                <span style="color:#222; font-size:13px;">
                                    Rs. {{ number_format($row['paidPerU'], 0) }}
                                </span>
                            @endif
                        </td>
                        <td style="padding:12px 10px; border-bottom:1px solid #eee;
                                   text-align:right; color:#222; font-weight:700;">
                            Rs. {{ number_format($row['linePaid'], 0) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- ORDER SUMMARY --}}
        <div style="padding:10px 30px 20px 30px;">
            <table width="100%" cellpadding="0" cellspacing="0"
                   style="background:#f9f9f9; border:1px solid #e0e0e0; border-radius:8px;">
                <tr>
                    <td style="padding:20px;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;">

                            @if(($subtotalMRP + $offerMRPTotal) > 0 && $totalSavings > 0)
                            <tr>
                                <td style="padding:5px 0; color:#666;">Total MRP</td>
                                <td style="padding:5px 0; text-align:right; color:#222;">
                                    Rs. {{ number_format($subtotalMRP + $offerMRPTotal, 0) }}
                                </td>
                            </tr>
                            @endif

                            @if($totalSavings > 0)
                            <tr>
                                <td style="padding:5px 0; color:#28a745;">Total Discount</td>
                                <td style="padding:5px 0; text-align:right; color:#28a745;">
                                    - Rs. {{ number_format($totalSavings, 0) }}
                                </td>
                            </tr>
                            @endif

                            <tr>
                                <td style="padding:5px 0; color:#666;">Shipping</td>
                                <td style="padding:5px 0; text-align:right; color:#28a745; font-weight:600;">FREE</td>
                            </tr>

                            <tr>
                                <td colspan="2" style="padding-top:10px;">
                                    <hr style="border:0; border-top:1px solid #ddd; margin:0;">
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:12px 0 0 0; color:#222; font-size:17px; font-weight:700;">
                                    @if($isCod)      Amount Due (COD)
                                    @elseif($isPaid) Total Paid
                                    @else            Total
                                    @endif
                                </td>
                                <td style="padding:12px 0 0 0; text-align:right; color:#000; font-size:20px; font-weight:700;">
                                    Rs. {{ number_format($totalPrice, 0) }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        {{-- SHIPPING ADDRESS --}}
        @if(!empty($order->shipping_address))
        <div style="padding:0 30px 20px 30px;">
            <h3 style="color:#333; font-size:15px; font-weight:700; letter-spacing:.5px; margin:0 0 10px 0;">
                SHIPPING ADDRESS
            </h3>
            <div style="background:#f9f9f9; border:1px solid #e0e0e0; border-radius:8px;
                        padding:15px; color:#555; font-size:14px; line-height:1.7;">
                <strong style="color:#222;">{{ $order->customer_name }}</strong><br>
                {{ $order->shipping_address }}<br>
                {{ $order->street ?? '' }}
                @if(!empty($order->postal_code)) – {{ $order->postal_code }}@endif<br>
                Phone: {{ $order->customer_phone }}<br>
                Email: {{ $order->customer_email }}
            </div>
        </div>
        @endif

        {{-- STATUS NOTE --}}
        <div style="padding:0 30px 20px 30px;">
            @if($isCod)
                <!--<div style="background:#d1ecf1; border-left:4px solid #0c5460;-->
                <!--            padding:15px; border-radius:4px; color:#0c5460; font-size:14px; line-height:1.6;">-->
                <!--    <strong>Cash on Delivery</strong> — Please keep-->
                <!--    <strong>Rs. {{ number_format($totalPrice, 0) }}</strong> ready for the delivery person.-->
                <!--    Your order will arrive in approximately <strong>8–9 days</strong>-->
                <!--    ({{ $deliveryStart->format('d M') }} – {{ $deliveryEnd->format('d M Y') }}).-->
                <!--</div>-->
            @elseif($isPaid)
                <!--<div style="background:#d4edda; border-left:4px solid #28a745;-->
                <!--            padding:15px; border-radius:4px; color:#155724; font-size:14px; line-height:1.6;">-->
                <!--    Payment received successfully. Your order will arrive in approximately-->
                <!--    <strong>8–9 days</strong>-->
                <!--    ({{ $deliveryStart->format('d M') }} – {{ $deliveryEnd->format('d M Y') }}).-->
                <!--</div>-->
            @elseif($status === 'failed')
                <div style="background:#f8d7da; border-left:4px solid #dc3545;
                            padding:15px; border-radius:4px; color:#721c24; font-size:14px; line-height:1.6;">
                    Payment failed. Please retry from your orders page or contact support.
                </div>
            @elseif($status === 'expired')
                <div style="background:#e2e3e5; border-left:4px solid #6c757d;
                            padding:15px; border-radius:4px; color:#383d41; font-size:14px; line-height:1.6;">
                    Payment session expired. Please place the order again.
                </div>
            @elseif($status === 'cancelled')
                <div style="background:#f8d7da; border-left:4px solid #dc3545;
                            padding:15px; border-radius:4px; color:#721c24; font-size:14px; line-height:1.6;">
                    This order has been cancelled.
                </div>
            @elseif($status === 'refunded')
                <div style="background:#e2e3e5; border-left:4px solid #6c757d;
                            padding:15px; border-radius:4px; color:#383d41; font-size:14px; line-height:1.6;">
                    Refund processed. It will reflect in your account within 5–7 business days.
                </div>
            @endif
        </div>

        {{-- FOOTER --}}
        <div style="text-align:center; padding:0 30px 30px 30px;">
            <p style="color:#555; font-size:15px; line-height:1.6; margin:0;">
                Thank you for choosing <strong>Signage</strong>.
                Questions? Reply to this email anytime.
            </p>
        </div>

        <div style="background:#f1f1f1; text-align:center; padding:20px;
                    font-size:13px; color:#777; border-top:1px solid #e0e0e0;">
            <p style="margin:0 0 5px 0;">Signage Team</p>
            <strong>&copy; {{ date('Y') }} Signage Wellness. All rights reserved.</strong>
        </div>

    </div>
</body>
</html>