<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
</head>

<style>
    @font-face {
        font-family: 'DejaVu Sans';
        font-style: normal;
        font-weight: normal;
        src: url("{{ storage_path('fonts/DejaVuSans.ttf') }}") format('truetype');
    }

    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 14px;
        line-height: 1.6;
        color: #333;
    }

    h2, h3 {
        margin-bottom: 5px !important;
    }

    p {
        margin-top: 0;
        margin-bottom: 10px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .table th, .table td {
        border: 1px solid #0a0a0a;
        padding: 8px;
        text-align: left;
    }

    .table th {
        background-color: #f4f4f4;
        font-weight: bold;
    }

    .totals {
        text-align: right;
        margin-top: 20px;
    }

    .footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: #000;
        padding: 10px;
        text-align: center;
        font-size: 15px;
        color: #fff;
    }
</style>
<body>
    <table width="100%">
        <tr>
            <td width="70%" style="background-image: url('{{ asset('frontend/assets/img/bg/pattern-light.png') }}'); background-repeat: no-repeat; background-size: cover; padding: 20px;">
                <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('frontend/assets/images/logo/logo.webp'))) }}" alt="Logo" style="width: 200px; background-color: #000; padding: 10px; border-radius: 4px;">
            </td>

            <td width="30%" style="text-align: right;">
                <h3>INVOICE</h3>
                <p>#{{ data_get($order, 'invoice_id', '-') }}</p>
                <p><strong>Date:</strong> {{ date('d M Y', strtotime(data_get($order, 'created_at', now()))) }}</p>
            </td>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <td width="50%">
                <h3>Billing Address:</h3>
                <p>
                    @foreach(explode(',', data_get($order, 'billing_address', '-')) as $part)
                        {{ trim($part) }}<br>
                    @endforeach
                    {{ data_get($order, 'city') }}, {{ data_get($order, 'state') }}<br>
                    {{ data_get($order, 'postal_code') }}, {{ data_get($order, 'country', 'India') }}
                </p>
            </td>
            <td width="50%" style="text-align: right;">
                <h3>Shipping Address:</h3>
                <p>
                    @foreach(explode(',', data_get($order, 'shipping_address', '-')) as $part)
                        {{ trim($part) }}<br>
                    @endforeach
                    {{ data_get($order, 'city') }}, {{ data_get($order, 'state') }}<br>
                    {{ data_get($order, 'postal_code') }}, {{ data_get($order, 'country', 'India') }}
                </p>
            </td>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <td>
                <h3>Customer Details:</h3>
                <p>
                    <strong>Name:</strong> {{ data_get($order, 'customer_name', '-') }}<br>
                    <strong>Email:</strong> {{ data_get($order, 'customer_email', '-') }}<br>
                    <strong>Phone:</strong> +91 {{ data_get($order, 'customer_phone', '-') }}<br>
                </p>
            </td>
        </tr>
    </table>

    {{-- Product Table --}}
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $items = json_decode(data_get($order, 'product_names', '[]'), true);
                $quantities = json_decode(data_get($order, 'quantities', '[]'), true);
                $prices = json_decode(data_get($order, 'prices', '[]'), true);
                $subTotal = 0;
            @endphp
            @foreach($items as $index => $item)
                @php
                    $qty = $quantities[$index] ?? 1;
                    $rate = $prices[$index] ?? 0;
                    $lineTotal = $qty * $rate;
                    $subTotal += $lineTotal;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item }}</td>
                    <td>{{ $qty }}</td>
                    <td>₹{{ number_format($rate, 2) }}</td>
                    <td>₹{{ number_format($lineTotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align:right;"><b>Sub Total</b></td>
                <td>₹{{ number_format($subTotal, 2) }}</td>
            </tr>
            @php
                $state = strtolower(data_get($order, 'state'));
                $isMaharashtra = $state === 'maharashtra';
                $cgst = $isMaharashtra ? data_get($order, 'cgst', 0) : 0;
                $sgst = $isMaharashtra ? data_get($order, 'sgst', 0) : 0;
                $igst = !$isMaharashtra ? data_get($order, 'igst', 0) : 0;
                $total = $subTotal + $cgst + $sgst + $igst;
            @endphp
            @if($isMaharashtra)
                <tr>
                    <td colspan="4" style="text-align:right;"><b>CGST</b></td>
                    <td>₹{{ number_format($cgst, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align:right;"><b>SGST</b></td>
                    <td>₹{{ number_format($sgst, 2) }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="4" style="text-align:right;"><b>IGST</b></td>
                    <td>₹{{ number_format($igst, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td colspan="4" style="text-align:right;"><b>Total</b></td>
                <td><strong>₹{{ number_format($total, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <b>&copy; {{ date('Y') }} Platina. All rights reserved.</b>
    </div>
</body>
</html>
