<!doctype html>
<html lang="en">

<head>
    @include('components.backend.head')
    <style>
        .stat-card {
            background: #fff; border-radius: 10px; padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px;
            border-left: 4px solid #006666;
        }
        .stat-card.pending   { border-left-color: #ffc107; }
        .stat-card.paid      { border-left-color: #28a745; }
        .stat-card.delivered { border-left-color: #17a2b8; }
        .stat-card .stat-label  { color: #666; font-size: 12px; font-weight: 700; text-transform: uppercase; }
        .stat-card .stat-value  { font-size: 24px; font-weight: 700; color: #222; margin-top: 5px; }
        .stat-card .stat-amount { font-size: 14px; color: #666; margin-top: 3px; }

        .ship-pill {
            padding: 3px 10px; border-radius: 12px;
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            display: inline-block;
        }
        .s-delivered  { background:#d4edda; color:#155724; }
        .s-intransit  { background:#cce5ff; color:#004085; }
        .s-pickup     { background:#fff3cd; color:#856404; }
        .s-notshipped { background:#f8d7da; color:#721c24; }

        .days-badge {
            display:inline-block; padding: 2px 8px; border-radius: 10px;
            font-size: 11px; font-weight: 600;
        }
        .days-early { background:#d1ecf1; color:#0c5460; }
        .days-due   { background:#fff3cd; color:#856404; }
        .days-over  { background:#f8d7da; color:#721c24; }

        .btn-mark-paid {
            background:#28a745; color:#fff; border:none;
            padding: 6px 14px; border-radius: 6px; font-weight: 600; font-size: 13px;
            cursor: pointer;
        }
        .btn-mark-paid:hover { background:#218838; color:#fff; }

        .btn-check-remit {
            background:#17a2b8; color:#fff; border:none;
            padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 12px;
            cursor: pointer; margin-right: 4px;
        }
        .btn-check-remit:hover { background:#138496; color:#fff; }
        .btn-check-remit:disabled { opacity:0.6; cursor:not-allowed; }
    </style>
</head>

<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h3>COD Payment Management</h3></div>
                    <div class="col-6 text-end">
                        <ol class="breadcrumb mb-0 d-inline-flex">
                            <li class="breadcrumb-item"><a href="{{ route('shiprocket-details.index') }}">Orders</a></li>
                            <li class="breadcrumb-item active">COD Pending</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">

            {{-- Alerts --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fa fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fa fa-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ═══ STAT CARDS ═══ --}}
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="stat-card pending">
                        <div class="stat-label">COD Pending Payment</div>
                        <div class="stat-value">{{ $stats['cod_pending'] }}</div>
                        <div class="stat-amount">₹{{ number_format($stats['total_pending_amount'], 2) }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card delivered">
                        <div class="stat-label">Delivered — Awaiting Payment</div>
                        <div class="stat-value">{{ $stats['cod_delivered_unpaid'] }}</div>
                        <div class="stat-amount">Should be remitted soon</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card paid">
                        <div class="stat-label">COD Paid (Received)</div>
                        <div class="stat-value">{{ $stats['cod_paid'] }}</div>
                        <div class="stat-amount">₹{{ number_format($stats['total_received_amount'], 2) }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-label">Total COD Orders</div>
                        <div class="stat-value">{{ $stats['total_cod_orders'] }}</div>
                        <div class="stat-amount">All time</div>
                    </div>
                </div>
            </div>

            {{-- Info banner --}}
            <div class="alert alert-info">
                <strong><i class="fa fa-info-circle"></i> How COD Payment Works:</strong>
                <ol class="mb-0 mt-2" style="font-size:14px;">
                    <li>Courier collects cash from customer at delivery</li>
                    <li>Shiprocket holds the cash for <strong>8 days</strong> (or 2 days with Early COD)</li>
                    <li>Shiprocket transfers the money to your bank account</li>
                    <li>Cron auto-marks order as PAID (runs every 6 hours)</li>
                    <li>Or you can manually click <strong>"Mark Paid"</strong> after seeing money in bank</li>
                </ol>
            </div>

            {{-- ═══ TABLE ═══ --}}
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-3">COD Orders Awaiting Payment</h4>

                    @if($orders->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fa fa-check-circle" style="font-size:48px; color:#28a745;"></i>
                            <h5 class="mt-3">No pending COD payments!</h5>
                            <p>All COD orders are either already paid or none exist yet.</p>
                        </div>
                    @else
                        <div class="table-responsive custom-scrollbar">
                            <table class="display" id="basic-1" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>AWB</th>
                                        <th>Shipment Status</th>
                                        <th>Delivered On</th>
                                        <th>Expected Remittance</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $i => $order)
                                        @php
                                            $cs = strtolower($order->courier_status ?? '');
                                            $ds = strtolower($order->delivery_status ?? '');
                                            $isDelivered = ($cs === 'delivered' || $ds === 'delivered');

                                            if ($isDelivered) {
                                                $shipLabel = 'DELIVERED';
                                                $shipClass = 's-delivered';
                                            } elseif (in_array($cs, ['in transit', 'shipped', 'out for delivery', 'in-transit'])) {
                                                $shipLabel = strtoupper($cs);
                                                $shipClass = 's-intransit';
                                            } elseif ($order->is_shipped) {
                                                $shipLabel = strtoupper($cs ?: 'PICKUP SCHEDULED');
                                                $shipClass = 's-pickup';
                                            } else {
                                                $shipLabel = 'NOT SHIPPED';
                                                $shipClass = 's-notshipped';
                                            }

                                            /* Expected remittance = delivered date + 8 days */
                                            $expectedRemit = null;
                                            $daysRemaining = null;
                                            if ($isDelivered) {
                                                $deliveredAt = \Carbon\Carbon::parse($order->updated_at);
                                                $expectedRemit = $deliveredAt->copy()->addDays(8);
                                                $daysRemaining = (int) now()->startOfDay()->diffInDays($expectedRemit->startOfDay(), false);
                                            }
                                        @endphp

                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td><strong>{{ $order->order_id }}</strong></td>
                                            <td>
                                                {{ $order->customer_name }}<br>
                                                <small class="text-muted">{{ $order->customer_email }}</small>
                                            </td>
                                            <td><strong>₹{{ number_format($order->total_price, 2) }}</strong></td>
                                            <td>
                                                @if($order->awb_code)
                                                    <code>{{ $order->awb_code }}</code>
                                                @else
                                                    <small class="text-muted">—</small>
                                                @endif
                                            </td>
                                            <td><span class="ship-pill {{ $shipClass }}">{{ $shipLabel }}</span></td>
                                            <td>
                                                @if($isDelivered)
                                                    {{ \Carbon\Carbon::parse($order->updated_at)->format('d M Y') }}
                                                @else
                                                    <small class="text-muted">—</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($expectedRemit)
                                                    @php
                                                        if ($daysRemaining > 2)       $dc = 'days-early';
                                                        elseif ($daysRemaining >= 0)  $dc = 'days-due';
                                                        else                           $dc = 'days-over';
                                                    @endphp
                                                    {{ $expectedRemit->format('d M Y') }}<br>
                                                    <span class="days-badge {{ $dc }}">
                                                        @if($daysRemaining > 0)
                                                            in {{ $daysRemaining }} days
                                                        @elseif($daysRemaining === 0)
                                                            Today
                                                        @else
                                                            {{ abs($daysRemaining) }} days overdue
                                                        @endif
                                                    </span>
                                                @else
                                                    <small class="text-muted">Awaiting delivery</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($order->awb_code)
                                                    <button type="button"
                                                            class="btn-check-remit mb-1"
                                                            data-url="{{ route('admin.cod.checkRemittance', $order->order_id) }}"
                                                            onclick="checkRemittance(this)">
                                                        <i class="fa fa-refresh"></i> Check
                                                    </button>
                                                @endif

                                                <form method="POST" action="{{ route('admin.cod.markPaid', $order->order_id) }}" class="d-inline"
                                                      onsubmit="return confirm('Confirm: money received in bank for order {{ $order->order_id }}?')">
                                                    @csrf
                                                    <button type="submit" class="btn-mark-paid">
                                                        <i class="fa fa-check"></i> Mark Paid
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    @include('components.backend.footer')
    @include('components.backend.main-js')

    <script>
    function checkRemittance(btn) {
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Checking...';

        fetch(btn.dataset.url)
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalText;

                if (!data.success) { alert('❌ ' + data.message); return; }

                if (data.paid) {
                    alert('✅ ' + data.message);
                    location.reload();
                } else if (data.found) {
                    alert('ℹ️ ' + data.message);
                } else {
                    alert('⏳ ' + data.message);
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                alert('❌ Network error: ' + err.message);
            });
    }

    $(document).ready(function () {
        if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#basic-1')) {
            $('#basic-1').DataTable({ pageLength: 25, order: [[6, 'asc']] });
        }
    });
    </script>

</body>
</html>