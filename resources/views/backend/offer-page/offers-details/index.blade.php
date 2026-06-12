<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')
</head>

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
                            <a href="{{ route('offer-details.index') }}">
                                <svg class="stroke-icon"><use href="../assets/svg/icon-sprite.svg#stroke-home"></use></svg>
                            </a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">

                        {{-- Header --}}
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('offer-details.index') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">Offer Details</li>
                                </ol>
                            </nav>
                            <a href="{{ route('offer-details.create') }}"
                               class="btn btn-primary px-4"
                               style="background:#064f4f;border-color:#064f4f;border-radius:8px;font-weight:600;">
                                + Add Offer
                            </a>
                        </div>

                        <!--{{-- Flash messages --}}-->
                        <!--@if(session('success'))-->
                        <!--<div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">-->
                        <!--    {{ session('success') }}-->
                        <!--    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>-->
                        <!--</div>-->
                        <!--@endif-->

                        {{-- Table --}}
                        <div class="table-responsive custom-scrollbar">
                            <table class="table table-striped" id="offersTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Offer Name</th>
                                        <th>Offer Value</th>
                                        <!--<th>Products</th>-->
                                        <th>Status</th>
                                        <th width="160" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($offers as $i => $offer)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td style="font-weight:600;color:#064f4f;">
                                            {{ $offer->offer_name }}
                                        </td>
                                        <td style="font-weight:700;">
                                            @if($offer->offer_price_type === 'percent')
                                                {{ number_format((float)$offer->offer_price, 2) }}% OFF
                                            @else
                                                ₹{{ number_format((float)$offer->offer_price, 2) }}
                                            @endif
                                        </td>
                                        <!--<td>-->
                                        <!--    @foreach($offer->products_decoded as $p)-->
                                        <!--        <span style="display:inline-block;background:#e6f7f7;color:#064f4f;-->
                                        <!--                     border:1px solid #99d6d6;border-radius:999px;-->
                                        <!--                     font-size:11px;font-weight:700;-->
                                        <!--                     padding:2px 9px;margin:2px;">-->
                                        <!--            {{ $p['product_name'] ?? '' }}-->
                                        <!--            @if(!empty($p['variant_label']))-->
                                        <!--                · {{ $p['variant_label'] }}-->
                                        <!--            @endif-->
                                        <!--            @if(($p['qty'] ?? 1) > 1)-->
                                        <!--                × {{ $p['qty'] }}-->
                                        <!--            @endif-->
                                        <!--        </span>-->
                                        <!--    @endforeach-->
                                        <!--</td>-->
                                        <td>
                                            @if($offer->is_active)
                                                <span class="badge"
                                                      style="background:#064f4f;color:#fff;
                                                             padding:4px 10px;border-radius:999px;
                                                             font-size:12px;font-weight:800;">
                                                    Active
                                                </span>
                                            @else
                                                <span class="badge"
                                                      style="background:#e5e7eb;color:#6b7280;
                                                             padding:4px 10px;border-radius:999px;
                                                             font-size:12px;font-weight:800;">
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{-- Edit --}}
                                            <a href="{{ route('offer-details.edit', $offer->id) }}"
                                             class="btn btn-primary"
                                               >
                                                Edit
                                            </a>
                                            <br>
                                            <br>
                                            {{-- Delete --}}
                                            <form action="{{ route('offer-details.destroy', $offer->id) }}"
                                                  method="POST" style="display:inline;"
                                                  onsubmit="return confirm('Delete this offer?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"  class="btn btn-sm btn-danger" 
                                                        >
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No offers found. <a href="{{ route('offer-details.create') }}">Add one →</a>
                                        </td>
                                    </tr>
                                    @endforelse
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function () {
    $('#offersTable').DataTable({
        pageLength: 10,
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: [3] }   // Products + Action not sortable
        ]
    });
});
</script>
</body>
</html>