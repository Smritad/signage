<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.backend.head')
    <title>Order Details - {{ $order->invoice_id ?? 'Invoice' }}</title>
    <style>
        .summary-table td, .summary-table th {
            padding: 0.5rem 0.75rem;
        }
        .summary-table tr:last-child td {
            font-weight: 600;
            background-color: #f5f5f5;
        }
        .badge-status {
            font-size: 0.875rem;
            padding: 0.35rem 0.65rem;
            text-transform: uppercase;
        }
        /* Optional: Make the product table a bit more compact */
        table.table-bordered td, table.table-bordered th {
            vertical-align: middle;
        }
    </style>
</head>
<body>

    @include('components.backend.header')
    @include('components.backend.sidebar')

    <div class="page-body">
        <div class="container-fluid">

            <div class="page-title mb-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4>Order Description</h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Home</a></li>
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
                            <p><strong>Name:</strong> {{ $order->customer_name }}</p>
                            <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                            <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                            <!--<p><strong>Address:</strong> -->
                            <!--    {{ $order->street }}, {{ $order->city }}, {{ $order->state }}, -->
                            <!--    {{ $order->country }} - {{ $order->postal_code }}-->
                            <!--</p>-->
                            <p><strong>Address:</strong> 
                                {{ $order->street }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Invoice:</strong> {{ $order->invoice_id }}</p>
                            <p><strong>Payment Method:</strong> Online</p>
                            <p><strong>Transaction ID:</strong> {{ $order->payment_id }}</p>
                            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>

                    {{-- Products Table --}}
                    <h5>Ordered Products</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                               <tbody>
   @php
    // Decode product names and other arrays if they are stored as JSON strings
    $productNamesArr = is_string($productNames) ? json_decode($productNames, true) : $productNames;
    $pricesArr = is_string($prices) ? json_decode($prices, true) : $prices;
    $quantitiesArr = is_string($quantities) ? json_decode($quantities, true) : $quantities;
@endphp

@foreach($productNamesArr as $index => $product)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $product }}</td>
    <td>₹{{ number_format($pricesArr[$index] ?? 0, 2) }}</td>
    <td>{{ $quantitiesArr[$index] ?? 0 }}</td>
    <td>₹{{ number_format(($pricesArr[$index] ?? 0) * ($quantitiesArr[$index] ?? 0), 2) }}</td>
</tr>
@endforeach


    <tr>
        <td colspan="4" class="text-end"><strong>Shipping Charge</strong></td>
        <td>₹0.00</td>
    </tr>

    <tr>
        <td colspan="4" class="text-end"><strong>Final Amount</strong></td>
        <td>₹{{ number_format($order->total_price, 2) }}</td>
    </tr>
</tbody>


                                {{-- GST & Final --}}
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Shipping Charge</strong></td>
                                    <td>₹0.00</td>
                                </tr>

                               

                                <tr>
                                    <td colspan="4" class="text-end"><strong>Final Amount (Incl. GST)</strong></td>
                                    <td>₹{{ number_format($order->total_price, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Order Status --}}
                    <h5 class="mt-4">Order Status</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover summary-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Transaction ID</th>
                                    <th>Invoice No</th>
                                    <th>Shipment ID</th>
                                    <th>AWB Code</th>
                                    <th>Courier Company</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>{{ $order->order_id }}</td>
                                    <td>{{ $order->invoice_id }}</td>
                                    <td>{{ $order->shipment_id ?? 'N/A' }}</td>
                                    <td>{{ $order->awb_code ?? 'N/A' }}</td>
                                    <td>{{ $order->courier_company_id ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-primary badge-status">
                                            {{ strtoupper($order->courier_status ?? 'NEW') }}
                                        </span>
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
