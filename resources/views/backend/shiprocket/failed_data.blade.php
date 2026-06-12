<!doctype html>
<html lang="en">

<head>
    @include('components.backend.head')
    <style>
        .status-pill {
            padding: 4px 10px; border-radius: 12px;
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            display: inline-block;
        }
        /* Shipment status colors */
        .ship-shipped     { background:#d4edda; color:#155724; }   /* success green  */
        .ship-intransit   { background:#cce5ff; color:#004085; }   /* travel blue    */
        .ship-delivered   { background:#d1e7dd; color:#0a3622; }   /* deep green     */
        .ship-pickup      { background:#fff3cd; color:#856404; }   /* amber          */
        .ship-notshipped  { background:#f8d7da; color:#721c24; }   /* red pending    */
        .ship-cancelled   { background:#e2e3e5; color:#383d41; }   /* gray           */
        .ship-rto         { background:#f8d7da; color:#721c24; }   /* returned red   */

        .btn-shipped {
            background:#6c757d; color:#fff; cursor:not-allowed;
        }
    </style>
</head>

<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h3>Shiprocket Orders</h3></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="index.html">
                                    <svg class="stroke-icon"><use href="../assets/svg/icon-sprite.svg#stroke-home"></use></svg>
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Shiprocket</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">

            {{-- ═══ SESSION ALERTS ═══ --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-triangle"></i> <strong>Ship Failed:</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('shiprocket-details.index') }}">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Orders</li>
                                    </ol>
                                </nav>
                            </div>

                            <div class="table-responsive custom-scrollbar">
                                <table class="display" id="basic-1" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <!--<th>Email</th>-->
                                            <th>Total</th>
                                            <th>Payment Mode</th>
                                            <th>Payment Status</th>
                                            <th>Date</th>
                                            <th>Details</th>
                                            <!--<th>Shipment</th>-->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orders as $key => $order)
                                            @php
                                                $ps = strtolower(trim($order->payment_status ?? ''));
                                                $pm = strtolower(trim($order->payment_method ?? ''));
                                                if (empty($pm)) {
                                                    $pm = ($ps === 'cod') ? 'cod' : 'online';
                                                }

                                                /* ═══ Shipment status mapping ═══ */
                                                $cs = strtolower(trim($order->courier_status ?? ''));

                                                if (!$order->is_shipped) {
                                                    $shipLabel = 'Not Shipped';
                                                    $shipClass = 'ship-notshipped';
                                                } else {
                                                    /* Mapping common Shiprocket courier_status values */
                                                    if (in_array($cs, ['delivered'])) {
                                                        $shipLabel = 'Delivered';
                                                        $shipClass = 'ship-delivered';
                                                    } elseif (in_array($cs, ['in transit', 'in-transit', 'shipped', 'out for delivery'])) {
                                                        $shipLabel = ucwords($cs ?: 'In Transit');
                                                        $shipClass = 'ship-intransit';
                                                    } elseif (in_array($cs, ['pickup scheduled', 'ready to ship', 'pickup generated', 'new'])) {
                                                        $shipLabel = ucwords($cs ?: 'Pickup Pending');
                                                        $shipClass = 'ship-pickup';
                                                    } elseif (in_array($cs, ['cancelled', 'canceled'])) {
                                                        $shipLabel = 'Cancelled';
                                                        $shipClass = 'ship-cancelled';
                                                    } elseif (str_contains($cs, 'rto') || str_contains($cs, 'return')) {
                                                        $shipLabel = 'RTO / Returned';
                                                        $shipClass = 'ship-rto';
                                                    } else {
                                                        $shipLabel = $cs ? ucwords($cs) : 'Shipped';
                                                        $shipClass = 'ship-shipped';
                                                    }
                                                }

                                                $canShip = in_array($ps, ['paid', 'cod']);
                                            @endphp
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td><strong>{{ $order->order_id }}</strong></td>
                                                <td>{{ $order->customer_name }}</td>
                                                <!--<td>{{ $order->customer_email }}</td>-->
                                                <td>₹{{ number_format($order->total_price, 2) }}</td>
                                              <td>
                                                    @if($pm === 'cod')
                                                        <span style="background:#ffe5e5;color:#d10000;padding:4px 10px;border-radius:20px;font-weight:600;">
                                                            COD
                                                        </span>
                                                    @else
                                                        <span style="background:#e5f7e5;color:#008000;padding:4px 10px;border-radius:20px;font-weight:600;">
                                                            Online
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span 
                                                        style="
                                                            color:
                                                            {{ $order->payment_status == 'paid' ? 'green' : 
                                                               ($order->payment_status == 'pending' ? 'orange' : 
                                                               ($order->payment_status == 'failed' ? 'red' : 'gray')) }};
                                                            font-weight: bold;
                                                        "
                                                    >
                                                        {{ ucfirst($order->payment_status) }}
                                                    </span>
                                                </td>                                                
                                                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.Orderfaileddetails.index', $order->id) }}" class="btn btn-sm btn-primary">
                                                        View
                                                    </a>
                                                </td>
                                                <!--<td>-->
                                                <!--    @if($order->is_shipped)-->
                                                <!--        <button class="btn btn-sm btn-shipped" disabled>-->
                                                <!--           Shipped-->
                                                <!--            @if($order->awb_code)-->
                                                <!--                <br><small>AWB: {{ $order->awb_code }}</small>-->
                                                <!--            @endif-->
                                                <!--        </button>-->
                                                <!--    @elseif($canShip)-->
                                                <!--        <a href="{{ route('admin.shiprocket.ship', $order->order_id) }}"-->
                                                <!--           class="btn btn-sm btn-primary"-->
                                                <!--           onclick="return confirm('Ship this order to Shiprocket?');">-->
                                                <!--            Ship Now-->
                                                <!--        </a>-->
                                                <!--    @else-->
                                                <!--        <button class="btn btn-sm btn-secondary" disabled title="Only paid/COD orders can be shipped">-->
                                                <!--            <i class="fa fa-ban"></i> Not Shippable-->
                                                <!--        </button>-->
                                                <!--    @endif-->
                                                <!--</td>-->
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @include('components.backend.footer')
    @include('components.backend.main-js')

    <script>
    $(document).ready(function () {
        if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#basic-1')) {
            $('#basic-1').DataTable({ pageLength: 25, order: [[7, 'desc']] });
        }
    });
    </script>

</body>
</html>