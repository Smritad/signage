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
        .ship-shipped     { background:#d4edda; color:#155724; }
        .ship-intransit   { background:#cce5ff; color:#004085; }
        .ship-delivered   { background:#d1e7dd; color:#0a3622; }
        .ship-pickup      { background:#fff3cd; color:#856404; }
        .ship-notshipped  { background:#f8d7da; color:#721c24; }
        .ship-cancelled   { background:#e2e3e5; color:#383d41; }
        .ship-rto         { background:#f8d7da; color:#721c24; }

        .btn-shipped {
            background:#6c757d; color:#fff; cursor:not-allowed;
        }

        .dataTables_wrapper button {
            font-weight: 400;
            padding: 0.375rem 0.75rem;
            font-size: 14px;
            border-radius: 0.25rem;
            color: #ffffff;
            background: #e59400;
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
                                            <th>Shipment Status</th>
                                            <th>Order Status</th>
                                            <th>Date</th>
                                            <th>Details</th>
                                            <th>Shipment</th>
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

                                                /* is_shipped=1 but no shipment_id = old/corrupt data → treat as not shipped */
                                                $alreadyPushed = $order->is_shipped && !empty($order->shipment_id);

                                                if (!$alreadyPushed) {
                                                    $shipLabel = 'Not Shipped';
                                                    $shipClass = 'ship-notshipped';
                                                } else {
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

                                                /* order_state = separate admin-managed lifecycle (shown in its own
                                                   "Order Status" column). It does NOT affect the Shiprocket shipment
                                                   status / Ship-Track column, which reflects Shiprocket's record. */
                                                $orderState = $order->order_state ?? '';

                                                $canShip = in_array($ps, ['paid', 'cod']) && !$alreadyPushed;
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
                                                <td><span class="status-pill {{ $shipClass }}">{{ $shipLabel }}</span></td>
                                                <td>
                                                    <select class="form-control form-control-sm order-state-select" style="min-width:150px;"
                                                            data-action="{{ route('admin.order.state', $order->id) }}"
                                                            data-current="{{ $orderState ?: 'active' }}"
                                                            data-orderid="{{ $order->order_id }}">
                                                        <option value="active" {{ $orderState === '' ? 'selected' : '' }}>— Active —</option>
                                                        <option value="cancelled_by_user" {{ $orderState === 'cancelled_by_user' ? 'selected' : '' }}>Cancelled by User</option>
                                                        <option value="refunded" {{ $orderState === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                                        <option value="closed" {{ $orderState === 'closed' ? 'selected' : '' }}>Closed Order</option>
                                                    </select>
                                                    @if($order->order_state_at)
                                                        <small class="text-muted d-block mt-1">{{ \Carbon\Carbon::parse($order->order_state_at)->format('d M Y H:i') }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.Orderdetails.index', $order->id) }}" class="btn btn-sm btn-primary">
                                                        View
                                                    </a>
                                                </td>
                                                <td>
                                                    @if($alreadyPushed)
                                                        {{-- Already in Shiprocket — show AWB + Track button --}}
                                                        <button class="btn btn-sm btn-shipped" disabled>
                                                            Shipped
                                                            @if($order->awb_code)
                                                                <br><small>AWB: {{ $order->awb_code }}</small>
                                                            @endif
                                                        </button>
                                                        <br>
                                                        <button
                                                            class="btn btn-sm mt-1 btn-track"
                                                            data-order="{{ $order->order_id }}"
                                                            title="Refresh tracking status">
                                                            <i class="fa fa-refresh"></i> Track
                                                        </button>
                                                    @elseif($canShip)
                                                        <a href="{{ route('admin.shiprocket.ship', $order->order_id) }}"
                                                           class="btn btn-sm btn-primary"
                                                           onclick="return confirm('Ship this order to Shiprocket?');">
                                                            Ship Now
                                                        </a>
                                                    @else
                                                        <button class="btn btn-sm btn-secondary" disabled title="Only paid/COD orders can be shipped">
                                                            <i class="fa fa-ban"></i> Not Shippable
                                                        </button>
                                                    @endif
                                                </td>
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
        /* DataTable init */
        if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#basic-1')) {
            $('#basic-1').DataTable({ pageLength: 25, order: [[6, 'desc']] });
        }

        /* ── Track button AJAX ── */
        $(document).on('click', '.btn-track', function () {
            var btn     = $(this);
            var orderId = btn.data('order');

            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Tracking...');

            $.ajax({
                url: '{{ route("admin.shiprocket.track", "__ID__") }}'.replace('__ID__', orderId),
                method: 'GET',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    if (res.success) {
                        var msg = 'Status: ' + res.current_status;
                        if (res.courier_name) msg += '\nCourier: ' + res.courier_name;
                        if (res.awb_code)     msg += '\nAWB: ' + res.awb_code;
                        if (res.etd)          msg += '\nEstimated Delivery: ' + res.etd;
                        alert(msg);
                        location.reload();
                    } else {
                        alert('Track failed: ' + res.message);
                    }
                },
                error: function () {
                    alert('Tracking request failed. Please try again.');
                },
                complete: function () {
                    btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Track');
                }
            });
        });
    });
    </script>

    {{-- ── Order Status modal (write a note when changing status) ── --}}
    <div id="orderStateModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:99999;">
        <div style="background:#fff; max-width:480px; margin:7% auto; border-radius:8px; padding:22px; box-shadow:0 10px 40px rgba(0,0,0,.25);">
            <h5 style="margin-top:0;">Update Order Status</h5>
            <form id="orderStateForm" method="POST">
                @csrf
                <input type="hidden" name="order_state" id="osState">
                <p style="margin:6px 0;">Order: <strong id="osOrderId"></strong></p>
                <p style="margin:6px 0;">New status: <strong id="osLabel"></strong></p>
                <label class="form-label" style="margin-top:8px;">Message / Note <small class="text-muted">(saved to the order history)</small></label>
                <textarea name="remarks" id="osRemarks" class="form-control" rows="3"
                          placeholder="e.g. Refund of Rs.____ processed via Razorpay (ref ____) on __/__/____"></textarea>
                <div style="margin-top:16px; text-align:right;">
                    <button type="button" class="btn btn-sm btn-secondary" id="osCancel">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">Save &amp; Close</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        (function () {
            var modal  = document.getElementById('orderStateModal');
            var form   = document.getElementById('orderStateForm');
            var labels = { active: 'Active', cancelled_by_user: 'Cancelled by User', refunded: 'Refunded', closed: 'Closed Order' };

            document.querySelectorAll('.order-state-select').forEach(function (sel) {
                sel.addEventListener('change', function () {
                    var val = this.value;
                    form.action = this.getAttribute('data-action');
                    document.getElementById('osState').value   = val;
                    document.getElementById('osLabel').textContent   = labels[val] || val;
                    document.getElementById('osOrderId').textContent = this.getAttribute('data-orderid');
                    document.getElementById('osRemarks').value = '';
                    // keep the dropdown showing the real current state until saved
                    this.value = this.getAttribute('data-current');
                    modal.style.display = 'block';
                });
            });

            document.getElementById('osCancel').addEventListener('click', function () { modal.style.display = 'none'; });
            modal.addEventListener('click', function (e) { if (e.target === modal) modal.style.display = 'none'; });
        })();
    </script>

</body>
</html>