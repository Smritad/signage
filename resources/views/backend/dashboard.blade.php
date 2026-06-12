<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')
</head>

@include('components.backend.header')
@include('components.backend.sidebar')

<style>
    .dash-wrap { padding: 24px; background: #f0f2f8; min-height: 100vh; }

    .dash-topbar { display: flex; align-items: center; gap: 10px; margin-bottom: 24px; flex-wrap: wrap; }
    .dash-topbar h4 { margin: 0; font-size: 20px; font-weight: 700; color: #1a1a2e; flex: 1; }
    .f-pill { padding: 6px 20px; border-radius: 20px; border: 2px solid #d0d5e8;
        background: #fff; font-size: 13px; font-weight: 500; color: #555;
        cursor: pointer; text-decoration: none; transition: all 0.2s; }
    .f-pill:hover { border-color: #4361ee; color: #4361ee; text-decoration: none; }
    .f-pill.active { background: #4361ee; border-color: #4361ee; color: #fff; }

    .stat-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 14px; margin-bottom: 22px; }
    .stat-card { border-radius: 14px; padding: 18px 16px; color: #fff;
        position: relative; overflow: hidden; box-shadow: 0 4px 18px rgba(0,0,0,0.13); }
    .stat-card::before { content: ''; position: absolute; right: -20px; top: -20px;
        width: 90px; height: 90px; border-radius: 50%; background: rgba(255,255,255,0.13); }
    .stat-label { font-size: 11px; font-weight: 700; letter-spacing: 0.6px; text-transform: uppercase; opacity: 0.85; }
    .stat-val { font-size: 24px; font-weight: 800; margin: 6px 0 2px; line-height: 1; }
    .stat-sub { font-size: 11px; opacity: 0.75; }
    .c-indigo { background: linear-gradient(135deg,#4361ee,#3a0ca3); }
    .c-green  { background: linear-gradient(135deg,#06d6a0,#028a6a); }
    .c-teal   { background: linear-gradient(135deg,#0096c7,#023e8a); }
    .c-orange { background: linear-gradient(135deg,#fb8500,#e85d04); }
    .c-red    { background: linear-gradient(135deg,#ef233c,#8d0801); }
    .c-purple { background: linear-gradient(135deg,#7b2d8b,#4a1060); }

    .chart-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px; }
    .chart-full { margin-bottom: 18px; }
    .c-card { background: #fff; border-radius: 14px; padding: 20px 20px 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.07); }
    .c-card-title { font-size: 14px; font-weight: 700; color: #1a1a2e; margin-bottom: 6px; }
    .c-card-sub { font-size: 22px; font-weight: 800; color: #333; margin-bottom: 10px; }
    .c-card-note { font-size: 11px; color: #888; font-weight: 400; margin-left: 8px; }

    .t-card { background: #fff; border-radius: 14px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); }
    .t-card-title { font-size: 14px; font-weight: 700; color: #1a1a2e; margin-bottom: 14px; }
    .o-table { width: 100%; border-collapse: collapse; }
    .o-table th { font-size: 11px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.5px; color: #888; border-bottom: 2px solid #f0f0f0;
        padding: 8px 12px; text-align: left; }
    .o-table td { padding: 10px 12px; font-size: 13px; border-bottom: 1px solid #f8f9fa;
        color: #333; vertical-align: middle; }
    .o-table tr:last-child td { border-bottom: none; }
    .o-table tr:hover td { background: #fafbff; }
    .spill { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
    .sp-paid    { background: #d1fae5; color: #065f46; }
    .sp-failed  { background: #fee2e2; color: #991b1b; }
    .sp-cod     { background: #dbeafe; color: #1e40af; }
    .sp-pending { background: #fef3c7; color: #92400e; }
    .mpill { display: inline-block; padding: 2px 8px; border-radius: 10px;
        font-size: 10px; font-weight: 600; background: #f0f0f0; color: #555; }

    @media (max-width: 1200px) { .stat-grid { grid-template-columns: repeat(3,1fr); } }
    @media (max-width: 768px)  { .stat-grid { grid-template-columns: repeat(2,1fr); }
                                  .chart-row { grid-template-columns: 1fr; } }
</style>

<div class="page-body">
<div class="container-fluid">
<div class="dash-wrap">

    {{-- Top Bar --}}
    <div class="dash-topbar">
        <h4>Dashboard</h4>
        @foreach(['last_year' => 'Last Year', 'last_month' => 'Last Month', 'last_week' => 'Last Week', 'today' => 'Today'] as $val => $label)
            <a href="{{ route('admin.dashboard', ['filter' => $val]) }}"
               class="f-pill {{ $filter === $val ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
    </div>

    {{-- Stat Cards --}}
    <div class="stat-grid">
        <div class="stat-card c-indigo">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-val">Rs. {{ number_format($totalRevenue, 0) }}</div>
            <div class="stat-sub">Online paid + COD</div>
        </div>
        <div class="stat-card c-green">
            <div class="stat-label">Total Orders</div>
            <div class="stat-val">{{ $totalOrders }}</div>
            <div class="stat-sub">All statuses</div>
        </div>
        <div class="stat-card c-teal">
            <div class="stat-label">Online Paid</div>
            <div class="stat-val">{{ $paidOrders }}</div>
            <div class="stat-sub">Payment gateway</div>
        </div>
        <div class="stat-card c-orange">
            <div class="stat-label">COD Orders</div>
            <div class="stat-val">{{ $codOrders }}</div>
            <div class="stat-sub">Cash on delivery</div>
        </div>
        <div class="stat-card c-red">
            <div class="stat-label">Failed Orders</div>
            <div class="stat-val">{{ $failedOrders }}</div>
            <div class="stat-sub">Payment failed</div>
        </div>
        <div class="stat-card c-purple">
            <div class="stat-label">Active Offers</div>
            <div class="stat-val">{{ $activeOffers }}</div>
            <div class="stat-sub">Live bundles</div>
        </div>
    </div>

    {{-- Revenue + Orders Charts --}}
    <div class="chart-row">
        <div class="c-card">
            <div class="c-card-title">
                Monthly Revenue
                <span class="c-card-note">Online paid + COD</span>
            </div>
            <div class="c-card-sub">Rs. {{ number_format($salesData->sum(), 0) }}</div>
            <div id="revenueChart"></div>
        </div>
        <div class="c-card">
            <div class="c-card-title">
                Monthly Orders
                <span class="c-card-note">All statuses</span>
            </div>
            <div class="c-card-sub">{{ $orderCounts->sum() }} orders</div>
            <div id="ordersChart"></div>
        </div>
    </div>

    {{-- Offer Revenue Chart --}}
    @if(count($offerNames) > 0)
    <div class="chart-full">
        <div class="c-card">
            <div class="c-card-title">
                Offer Revenue Breakdown
                <span class="c-card-note">Bundle offers revenue — online paid + COD combined</span>
            </div>
            <div id="offerChart"></div>
        </div>
    </div>
    @endif

    {{-- Recent Orders --}}
    <div class="t-card">
        <div class="t-card-title">Recent Orders</div>
        <table class="o-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Mode</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                @php
                    $status = $order->payment_status ?? 'pending';
                    $pc = match($status) {
                        'paid'    => 'sp-paid',
                        'failed'  => 'sp-failed',
                        'cod'     => 'sp-cod',
                        default   => 'sp-pending',
                    };
                @endphp
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->customer_name ?? '-' }}</td>
                    <td>Rs. {{ number_format($order->total_price, 0) }}</td>
                    <td><span class="mpill">{{ strtoupper($order->payment_method ?? '-') }}</span></td>
                    <td><span class="spill {{ $pc }}">{{ ucfirst($status) }}</span></td>
                    <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;color:#aaa;padding:28px;">No orders found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
</div>
</div>

@include('components.backend.footer')
@include('components.backend.main-js')

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
(function () {
    const months  = @json($salesData->keys());
    const revenue = @json($salesData->values());
    const orders  = @json($orderCounts->values());

    new ApexCharts(document.querySelector('#revenueChart'), {
        chart: { type: 'area', height: 220, toolbar: { show: false } },
        series: [{ name: 'Revenue (Rs.)', data: revenue }],
        xaxis: { categories: months },
        colors: ['#4361ee'],
        fill: { type: 'gradient', gradient: { opacityFrom: 0.45, opacityTo: 0.05 } },
        stroke: { curve: 'smooth', width: 3 },
        dataLabels: { enabled: false },
        grid: { borderColor: '#f0f0f0' },
        tooltip: { y: { formatter: v => 'Rs. ' + Number(v).toLocaleString() } },
        yaxis: { labels: { formatter: v => 'Rs. ' + Number(v).toLocaleString() } }
    }).render();

    new ApexCharts(document.querySelector('#ordersChart'), {
        chart: { type: 'bar', height: 220, toolbar: { show: false } },
        series: [{ name: 'Orders', data: orders }],
        xaxis: { categories: months },
        colors: ['#06d6a0'],
        plotOptions: { bar: { borderRadius: 6, columnWidth: '48%' } },
        dataLabels: { enabled: false },
        grid: { borderColor: '#f0f0f0' },
        tooltip: { y: { formatter: v => v + ' orders' } }
    }).render();

    @if(count($offerNames) > 0)
    new ApexCharts(document.querySelector('#offerChart'), {
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        series: [{ name: 'Revenue (Rs.)', data: @json($offerAmounts) }],
        xaxis: {
            categories: @json($offerNames),
            labels: { style: { fontSize: '12px' }, trim: true, maxHeight: 80, rotate: -30 }
        },
        colors: ['#7b2d8b'],
        plotOptions: { bar: { borderRadius: 6, columnWidth: '42%' } },
        dataLabels: { enabled: false },
        grid: { borderColor: '#f0f0f0' },
        tooltip: { y: { formatter: v => 'Rs. ' + Number(v).toLocaleString() } },
        yaxis: { labels: { formatter: v => 'Rs. ' + Number(v).toLocaleString() } }
    }).render();
    @endif
})();
</script>

</body>
</html>
