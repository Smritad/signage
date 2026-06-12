
<table class="table table-bordered" id="orders-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Order ID</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $index => $order)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $order->order_id }}</td>
            <td>{{ $order->status }}</td>
            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</td>
            <td>
                <a href="{{ route('admin.order.invoice', $order->order_id) }}" class="btn btn-sm btn-primary">View</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
    $(document).ready(function() {
        $('#orders-table').DataTable();
    });
</script>
