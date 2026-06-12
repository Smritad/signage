<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.backend.head')
    <style>
        .summary-table td {
            padding: 0.5rem 0.75rem;
        }
        .summary-table tr:last-child td {
            font-weight: 600;
            background-color: #f5f5f5;
        }
        .badge-status {
            font-size: 0.875rem;
            padding: 0.35rem 0.65rem;
        }

        /* Payment status pills */
        .pay-pill {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-block;
        }
        .pay-paid      { background: #d4edda; color: #155724; }
        .pay-cod       { background: #d1ecf1; color: #0c5460; }
        .pay-pending   { background: #fff3cd; color: #856404; }
        .pay-failed    { background: #f8d7da; color: #721c24; }
        .pay-cancelled { background: #f8d7da; color: #721c24; }
        .pay-expired   { background: #e2e3e5; color: #383d41; }
        .pay-refunded  { background: #e2e3e5; color: #383d41; }

        /* Product image */
        .product-thumb {
            width: 55px;
            height: 55px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        /* Price display */
        .price-strike {
            color: #999;
            text-decoration: line-through;
            font-size: 12px;
        }
        .price-offer {
            color: #dc3545;
            font-weight: 700;
            font-size: 14px;
        }
        .price-normal {
            font-weight: 600;
            font-size: 14px;
        }

        /* Bundle styles */
        .bundle-badge {
            display: inline-block;
            background: #6f42c1;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .bundle-name {
            font-weight: 600;
            font-size: 13px;
            display: block;
            margin-bottom: 4px;
        }
        .bundle-items-list {
            margin: 4px 0 0 0;
            padding: 0 0 0 14px;
            font-size: 12px;
            color: #555;
            list-style: disc;
        }
        .bundle-items-list li {
            margin-bottom: 2px;
        }

        /* Savings badge in table */
        .savings-badge {
            background: #d4edda;
            color: #155724;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 8px;
            display: inline-block;
        }
    </style>
</head>
<body>

    @include('components.backend.header')
    @include('components.backend.sidebar')

    @php
        $ps = strtolower(trim($order->payment_status ?? ''));
        $pm = strtolower(trim($order->payment_method ?? ''));

        if (empty($pm)) {
            if ($ps === 'cod')         $pm = 'cod';
            elseif ($ps === 'paid')    $pm = 'online';
            elseif ($ps === 'pending') $pm = 'online';
            else                       $pm = 'unknown';
        }

        $pmLabel = match($pm) {
            'cod'    => 'Cash on Delivery',
            'online' => 'Online',
            default  => 'N/A',
        };

        $psMap = [
            'paid'      => ['label' => 'Paid',       'class' => 'pay-paid'],
            'cod'       => ['label' => 'COD Placed',  'class' => 'pay-cod'],
            'pending'   => ['label' => 'Pending',     'class' => 'pay-pending'],
            'failed'    => ['label' => 'Failed',      'class' => 'pay-failed'],
            'cancelled' => ['label' => 'Cancelled',   'class' => 'pay-cancelled'],
            'expired'   => ['label' => 'Expired',     'class' => 'pay-expired'],
            'refunded'  => ['label' => 'Refunded',    'class' => 'pay-refunded'],
        ];
        $st    = $psMap[$ps] ?? ['label' => $ps ? strtoupper($ps) : 'Not Set', 'class' => 'pay-pending'];
        $isCod = ($pm === 'cod');

        $billingCity    = \DB::table('main_cities')->where('id', $order->city)->value('name');
        $billingState   = \DB::table('main_states')->where('id', $order->state)->value('name');
        $billingCountry = \DB::table('main_countries')->where('id', $order->country)->value('name');

        $subtotal   = $subtotal   ?? 0;
        $totalSaved = $totalSaved ?? 0;
    @endphp

    <div class="page-body">
        <div class="container-fluid">

            <div class="page-title mb-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4>Order Description</h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('shiprocket-details.index') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">Order Description</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">

                    {{-- Customer Info --}}
                    <div class="row border-bottom pb-3 mb-4">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $order->customer_name ?? 'N/A' }}</p>
                            <p><strong>Email:</strong> {{ $order->customer_email ?? 'N/A' }}</p>
                            <p><strong>Phone:</strong> {{ $order->customer_phone ?? 'N/A' }}</p>
                            <p><strong>Address:</strong>
                                {{ $order->street ?? '' }},
                                {{ $billingCity ?? '' }},
                                {{ $billingState ?? '' }},
                                {{ $billingCountry ?? '' }} -
                                {{ $order->postal_code ?? '' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Invoice:</strong> {{ $order->invoice_id ?? 'N/A' }}</p>
                            <p><strong>Payment Method:</strong> {{ $pmLabel }}</p>
                            <p><strong>Payment Status:</strong>
                                <span class="pay-pill {{ $st['class'] }}">{{ $st['label'] }}</span>
                            </p>
                            <p><strong>Order ID:</strong> {{ $order->order_id }}</p>
                            <p><strong>Date:</strong>
                                {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, h:i A') }}
                            </p>
                        </div>
                    </div>

                    {{-- Items Table --}}
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle w-100">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th style="width:70px;">Image</th>
                                    <th>Product</th>
                                    <th style="width:60px;">Qty</th>
                                    <th style="width:160px;">MRP</th>
                                    <th style="width:130px;">Amount Paid</th>
                                    <th style="width:130px;">Savings</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalAmount = 0; $totalSavings = 0; @endphp

                                @foreach($invoiceItems as $index => $item)
                                    @php
                                        $itemName    = $item['name']          ?? 'Product';
                                        $itemImage   = $item['image']         ?? asset('signage/home/productimage/default.png');
                                        $itemQty     = (int)($item['quantity']      ?? 1);
                                        $isBundle    = $item['is_bundle']     ?? false;
                                        $selected    = $item['selected']      ?? [];

                                        $mrpPerUnit  = (float)($item['mrp_per_unit']  ?? 0);
                                        $paidPerUnit = (float)($item['paid_per_unit'] ?? 0);
                                        $lineMRP     = (float)($item['line_mrp']  ?? 0);
                                        $linePaid    = (float)($item['line_paid'] ?? 0);
                                        $lineSaved   = (float)($item['line_saved'] ?? 0);
                                        $hasOffer    = $item['has_offer'] ?? ($paidPerUnit > 0 && $paidPerUnit < $mrpPerUnit);

                                        if ($paidPerUnit == 0 && $linePaid > 0 && $itemQty > 0) {
                                            $paidPerUnit = $linePaid / $itemQty;
                                        }
                                        if ($mrpPerUnit == 0 && $lineMRP > 0 && $itemQty > 0) {
                                            $mrpPerUnit = $lineMRP / $itemQty;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <img src="{{ $itemImage }}" class="product-thumb" alt="">
                                        </td>
                                        <td>
                                            @if($isBundle)
                                                <span class="bundle-badge">Bundle Offer</span>
                                                <span class="bundle-name">{{ $itemName }}</span>
                                                @if(!empty($selected))
                                                    <ul class="bundle-items-list">
                                                        @foreach($selected as $sel)
                                                            @php
                                                                $selName = $sel['name'] ?? $sel['product_name'] ?? 'Item';
                                                                $selUnit = $sel['unit'] ?? null;
                                                                $selQty  = $sel['qty']  ?? $sel['quantity'] ?? null;
                                                            @endphp
                                                            <li>
                                                                {{ $selName }}
                                                                @if($selUnit) ({{ $selUnit }}) @endif
                                                                @if($selQty)  &times; {{ $selQty }} @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            @else
                                                {{ $itemName }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ $itemQty }}
                                            @if($isBundle)
                                                <br><small class="text-muted">(Bundle)</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($hasOffer && $mrpPerUnit > $paidPerUnit)
                                                <span class="price-strike">
                                                    Rs. {{ number_format($lineMRP, 2) }}
                                                </span><br>
                                                <span class="price-offer">
                                                    Rs. {{ number_format($linePaid, 2) }}
                                                </span>
                                            @else
                                                <span class="price-normal">
                                                    Rs. {{ number_format($lineMRP > 0 ? $lineMRP : $linePaid, 2) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>Rs. {{ number_format($linePaid, 2) }}</strong>
                                        </td>
                                        <td>
                                            @if($lineSaved > 0)
                                                <span class="savings-badge">
                                                    Rs. {{ number_format($lineSaved, 2) }} OFF
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @php
                                        $totalAmount  += $linePaid;
                                        $totalSavings += $lineSaved;
                                    @endphp
                                @endforeach
                            </tbody>
                            <tfoot>
                                @if($totalSavings > 0)
                                    <tr>
                                        <td colspan="5" class="text-end"><strong>Total Savings:</strong></td>
                                        <td colspan="2" class="text-success">
                                            <strong>- Rs. {{ number_format($totalSavings, 2) }}</strong>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Shipping:</strong></td>
                                    <td colspan="2" class="text-success"><strong>FREE</strong></td>
                                </tr>
                                <tr class="table-light">
                                    <td colspan="5" class="text-end">
                                        <strong>{{ $isCod ? 'Amount Due (COD):' : 'Grand Total:' }}</strong>
                                    </td>
                                    <td colspan="2">
                                        <strong style="font-size:15px;">
                                            Rs. {{ number_format($order->total_price, 2) }}
                                        </strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Order Status --}}
                    <h5 class="mt-4">Shipment Status</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover summary-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Order ID</th>
                                    <th>Invoice No</th>
                                    <th>Shipment ID</th>
                                    <th>AWB Code</th>
                                    <th>Courier Name</th>
                                    <th>Shipment Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>{{ $order->order_id }}</td>
                                    <td>{{ $order->invoice_id ?? 'N/A' }}</td>
                                    <td>{{ $order->shipment_id ?? 'N/A' }}</td>
                                    <td>{{ $order->awb_code ?? 'N/A' }}</td>
                                    <td>{{ $order->courier_name ?? 'N/A' }}</td>
                                    <td>
                                        @if($order->is_shipped)
                                            <span class="badge bg-success badge-status">
                                                {{ strtoupper($order->courier_status ?? 'SHIPPED') }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark badge-status">
                                                NOT SHIPPED
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    

    @include('components.backend.footer')
    @include('components.backend.main-js')

</body>
</html>
