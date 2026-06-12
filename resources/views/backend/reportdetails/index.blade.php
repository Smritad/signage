<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')
</head>
<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="index.html">
                                    <svg class="stroke-icon">
                                        <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                                    </svg>
                                </a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('report-details.index') }}">Home</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">Report</li>
                                    </ol>
                                </nav>
                            </div>

                            <form method="GET" action="{{ route('report-details.index') }}" class="row mb-4">
                                <div class="col-md-3">
                                    <label for="report_type">Select Report Type</label>
                                    <select name="report_type" id="report_type" class="form-control" onchange="this.form.submit()">
                                        <option value="customer"  {{ request('report_type', 'customer') == 'customer'  ? 'selected' : '' }}>Customer Sales Report</option>
                                        <option value="inventory" {{ request('report_type') == 'inventory' ? 'selected' : '' }}>Inventory Report</option>
                                        <option value="product"   {{ request('report_type') == 'product'   ? 'selected' : '' }}>Product Report</option>
                                        <option value="category"  {{ request('report_type') == 'category'  ? 'selected' : '' }}>Category Report</option>
                                        <option value="offer"     {{ request('report_type') == 'offer'     ? 'selected' : '' }}>Offer Report</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label for="from_date">From Date</label>
                                    <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
                                </div>

                                <div class="col-md-2">
                                    <label for="to_date">To Date</label>
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
                                </div>

                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('report-details.index') }}" class="btn btn-secondary">Reset</a>
                                        <a href="{{ route('report-details.export', request()->query()) }}" class="btn btn-success">Export</a>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive custom-scrollbar">

                                {{-- ═══ INVENTORY ═══ --}}
                                @if ($reportType === 'inventory')
                                    <table class="table table-bordered" id="basic-1">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Product Name</th>
                                                <th>Total Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($products as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $item->product_name }}</td>
                                                    <td>{{ $item->total_stock }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-secondary fw-bold">
                                                <td colspan="2">Total</td>
                                                <td>{{ $totals['total_stock'] }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                {{-- ═══ PRODUCT ═══ --}}
                                @elseif ($reportType === 'product')
                                    <p class="text-muted small mb-2">
                                        <i class="fa fa-info-circle"></i> Showing only <strong>paid</strong> orders.
                                    </p>
                                    <table class="table table-bordered" id="basic-1">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Product Name</th>
                                                <th>Sub Category</th>
                                                <th>Stock Left</th>
                                                <th>Total Sale Products</th>
                                                <th>Total Revenue (₹)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($products as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $item->product_name }}</td>
                                                    <td>{{ $item->sab_category_name }}</td>
                                                    <td>{{ $item->stock_left }}</td>
                                                    <td>{{ $item->total_sales }}</td>
                                                    <td>₹{{ $item->total_revenue }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-secondary fw-bold">
                                                <td colspan="3">Total</td>
                                                <td>{{ $totals['stock_left'] }}</td>
                                                <td>{{ $totals['total_sales'] }}</td>
                                                <td>₹{{ $totals['total_revenue'] }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                {{-- ═══ CATEGORY ═══ --}}
                                @elseif ($reportType === 'category')
                                    <p class="text-muted small mb-2">
                                        <i class="fa fa-info-circle"></i>
                                        Counts products matched via <code>products_details.category_id</code>. Only <strong>paid</strong> orders included.
                                    </p>
                                    <table class="table table-bordered" id="basic-1">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Category Name</th>
                                                <th>Total Order Lines</th>
                                                <th>Total Qty Sold</th>
                                                <th>Total Amount (₹)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($categoryResults as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $item->category_name }}</td>
                                                    <td>{{ $item->total_orders }}</td>
                                                    <td>{{ $item->total_qty }}</td>
                                                    <td>₹{{ $item->total_amount }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No data found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-secondary fw-bold">
                                                <td colspan="2">Total</td>
                                                <td>{{ $totals['total_orders'] }}</td>
                                                <td>{{ $totals['total_qty'] }}</td>
                                                <td>₹{{ $totals['total_amount'] }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                {{-- ═══ OFFER ═══ --}}
                                @elseif ($reportType === 'offer')
                                    <p class="text-muted small mb-2">
                                        <i class="fa fa-info-circle"></i> Showing <strong>paid + COD</strong> orders.
                                    </p>
                                    <table class="table table-bordered" id="basic-1">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Offer Name</th>
                                                <th>Offer Price (₹)</th>
                                                <th>Total Orders</th>
                                                <th>Total Qty Sold</th>
                                                <th>Total Amount (₹)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($offerResults as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        <!--@if ($item->offer_image)-->
                                                        <!--    <img src="{{ asset('offerimage/' . $item->offer_image) }}"-->
                                                        <!--         alt="" width="36" height="36"-->
                                                        <!--         style="border-radius:4px;object-fit:cover;margin-right:8px;vertical-align:middle;">-->
                                                        <!--@endif-->
                                                        {{ $item->offer_name }}
                                                    </td>
                                                    <td>₹{{ $item->offer_price }}</td>
                                                    <td>{{ $item->total_orders }}</td>
                                                    <td>{{ $item->total_qty }}</td>
                                                    <td>₹{{ $item->total_amount }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">No offer orders found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-secondary fw-bold">
                                                <td colspan="3">Total</td>
                                                <td>{{ $totals['total_orders'] }}</td>
                                                <td>{{ $totals['total_qty'] }}</td>
                                                <td>₹{{ $totals['total_amount'] }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                {{-- ═══ CUSTOMER (default) ═══ --}}
                                @else
                                    <table class="table table-bordered" id="basic-1">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Customer Name</th>
                                                <th>Email</th>
                                                <th>Total Amount (₹)</th>
                                                <th>Total Orders</th>
                                                <th>Created Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($customers as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $item->customer_name }}</td>
                                                    <td>{{ $item->customer_email }}</td>
                                                    <td>₹{{ number_format($item->total_spent, 2) }}</td>
                                                    <td>{{ $item->total_orders }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($item->created_date)->format('Y-m-d') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-secondary fw-bold">
                                                <td colspan="3">Total</td>
                                                <td>₹{{ $totals['total_spent'] }}</td>
                                                <td>{{ $totals['total_orders'] }}</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                @endif

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
                $('#basic-1').DataTable({
                    pageLength: 25,
                    order: [],
                    // Keep tfoot outside DataTable scrolling so totals always show
                    bInfo: true,
                });
            }
        });
    </script>
</body>
</html>