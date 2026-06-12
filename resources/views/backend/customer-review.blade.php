<!doctype html>
<html lang="en">

<head>
    @include('components.backend.head')
    <style>
        /* ═══ Stat boxes ═══ */
        .stat-card {
            background: #fff; border-radius: 10px; padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px;
            border-left: 4px solid #006666;
        }
        .stat-card.approved { border-left-color: #28a745; }
        .stat-card.pending  { border-left-color: #ffc107; }
        .stat-card.avg      { border-left-color: #f5a623; }
        .stat-card .stat-label { color: #666; font-size: 13px; font-weight: 600; text-transform: uppercase; margin-bottom: 6px; }
        .stat-card .stat-value { font-size: 28px; font-weight: 700; color: #222; }

        /* ═══ Rating stars ═══ */
        .rating-display { color: #f5a623; font-size: 15px; letter-spacing: 2px; }
        .rating-display .empty { color: #ddd; }

        /* ═══ Table ═══ */
        #basic-1 tbody td { vertical-align: middle; }
        .product-thumb {
            width: 55px; height: 55px; object-fit: cover;
            border-radius: 6px; border: 1px solid #ddd;
        }
        .product-name-cell {
            max-width: 220px; font-weight: 600; color: #006666;
        }
        .review-content-cell {
            max-width: 300px; color: #555; font-size: 13px;
            display: -webkit-box; -webkit-line-clamp: 2;
            -webkit-box-orient: vertical; overflow: hidden;
        }

        /* ═══ Status badge ═══ */
        .status-badge {
            padding: 4px 10px; border-radius: 12px; font-size: 11px;
            font-weight: 700; text-transform: uppercase; display: inline-block;
        }
        .status-badge.approved    { background: #d4edda; color: #155724; }
        .status-badge.disapproved { background: #f8d7da; color: #721c24; }

        /* ═══ Toggle switch ═══ */
        .toggle-switch {
            position: relative; display: inline-block; width: 44px; height: 24px;
        }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider {
            position: absolute; cursor: pointer; inset: 0;
            background: #ccc; transition: .3s; border-radius: 24px;
        }
        .toggle-slider:before {
            position: absolute; content: ""; height: 18px; width: 18px;
            left: 3px; bottom: 3px; background: white;
            transition: .3s; border-radius: 50%;
        }
        .toggle-switch input:checked + .toggle-slider { background: #28a745; }
        .toggle-switch input:checked + .toggle-slider:before { transform: translateX(20px); }

        /* ═══ Action buttons ═══ */
        .btn-action {
            width: 32px; height: 32px; display: inline-flex;
            align-items: center; justify-content: center; border-radius: 4px;
            color: #fff; border: none; margin: 0 2px; cursor: pointer;
            text-decoration: none; transition: opacity 0.2s;
        }
        .btn-action:hover { opacity: 0.85; color: #fff; }
        .btn-view   { background: #17a2b8; }
        .btn-delete { background: #dc3545; }

        /* ═══ Filter bar ═══ */
        .filter-bar {
            background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;
        }
        .filter-bar label { font-weight: 600; font-size: 13px; margin-bottom: 4px; color: #333; }

        /* ═══ Modal ═══ */
        .review-modal-star { color: #f5a623; font-size: 22px; }
        .review-modal-star.empty { color: #ddd; }
        .review-media-item {
            display: inline-block; width: 100px; height: 100px;
            margin: 5px; border-radius: 6px; object-fit: cover;
            border: 1px solid #ddd; cursor: pointer;
        }

        /* Status change box inside modal */
        .status-change-box {
            background: #fff8e1;
            border: 1px solid #ffe082;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .status-change-box .status-label {
            font-size: 12px; color: #666; font-weight: 700; text-transform: uppercase;
            display: block; margin-bottom: 8px;
        }
        .btn-change-status {
            padding: 8px 20px; border: none; border-radius: 6px;
            color: #fff; font-weight: 600; font-size: 13px;
            cursor: pointer; transition: all 0.2s;
        }
        .btn-change-status.to-disapprove { background: #dc3545; }
        .btn-change-status.to-approve    { background: #28a745; }
        .btn-change-status:hover         { opacity: 0.85; }
        .btn-change-status:disabled      { opacity: 0.6; cursor: not-allowed; }
    </style>
</head>

<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h3>Customer Ratings & Reviews</h3></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ url('/admin') }}">
                                    <svg class="stroke-icon"><use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg>
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Reviews</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">

            {{-- ═══ STAT BOXES ═══ --}}
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-label">Total Reviews</div>
                        <div class="stat-value">{{ $stats['total'] }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card approved">
                        <div class="stat-label">Approved</div>
                        <div class="stat-value">{{ $stats['approved'] }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card pending">
                        <div class="stat-label">Disapproved</div>
                        <div class="stat-value">{{ $stats['pending'] }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card avg">
                        <div class="stat-label">Average Rating</div>
                        <div class="stat-value">
                            <span style="color:#f5a623;">★</span> {{ $stats['avg'] }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ FILTERS ═══ --}}
            <form method="GET" action="{{ route('admin.customer-rating-review') }}" class="filter-bar">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label>Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Product, customer, title...">
                    </div>
                    <div class="col-md-2">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Approved</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Disapproved</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Rating</label>
                        <select name="rating" class="form-select">
                            <option value="">All Ratings</option>
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i == 1 ? '' : 's' }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('admin.customer-rating-review') }}" class="btn btn-secondary">
                            <i class="fa fa-refresh"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            {{-- ═══ REVIEWS TABLE ═══ --}}
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive custom-scrollbar">
                                <table class="display" id="basic-1" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Image</th>
                                            <th>Product</th>
                                            <th>Customer</th>
                                            <th>Rating</th>
                                            <th>Review</th>
                                            <th>Approved</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reviews as $index => $review)
                                            @php
                                                $productImage = null;
                                                if (!empty($review->media)) {
                                                    $imgs = json_decode($review->media, true);
                                                    if (is_array($imgs) && !empty($imgs)) {
                                                        $raw = $imgs[0];
                                                        $productImage = str_starts_with($raw, 'http')
                                                            ? $raw
                                                            : asset('signage/home/reviews/' . basename($raw));
                                                    }
                                                }
                                            @endphp

                                            <tr>
                                                <td>{{ $index + 1 }}</td>

                                                <td>
                                                    <small>{{ \Carbon\Carbon::parse($review->created_at)->format('d M Y') }}</small><br>
                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($review->created_at)->format('h:i A') }}</small>
                                                </td>

                                                <td>
                                                    @if($productImage)
                                                        <img src="{{ $productImage }}" class="product-thumb" alt="">
                                                    @else
                                                        <div class="product-thumb d-flex align-items-center justify-content-center" style="background:#f0f0f0;">
                                                            <i class="fa fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                </td>

                                                <td>
                                                    <div class="product-name-cell">
                                                        {{ $review->product_name ?? 'Deleted Product' }}
                                                    </div>
                                                </td>

                                                <td>
                                                    <strong>{{ $review->reviewer_name }}</strong><br>
                                                    <small class="text-muted">{{ $review->reviewer_email }}</small>
                                                </td>

                                                <td>
                                                    <div class="rating-display">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <span class="{{ $i <= $review->rating ? '' : 'empty' }}">★</span>
                                                        @endfor
                                                    </div>
                                                    <small class="text-muted">{{ $review->rating }}/5</small>
                                                </td>

                                                <td>
                                                    @if(!empty($review->title))
                                                        <strong class="d-block">{{ $review->title }}</strong>
                                                    @endif
                                                    <div class="review-content-cell">{{ $review->content }}</div>
                                                </td>

                                                <td>
                                                    <label class="toggle-switch">
                                                        <input type="checkbox"
                                                               class="review-toggle"
                                                               data-id="{{ $review->id }}"
                                                               data-toggle-url="{{ route('admin.review.toggle', $review->id) }}"
                                                               {{ $review->is_approved ? 'checked' : '' }}>
                                                        <span class="toggle-slider"></span>
                                                    </label>
                                                </td>

                                                <td>
                                                    <span class="status-badge {{ $review->is_approved ? 'approved' : 'disapproved' }}" id="status-badge-{{ $review->id }}">
                                                        {{ $review->is_approved ? 'Approved' : 'Disapproved' }}
                                                    </span>
                                                </td>

                                                <td>
                                                    <button class="btn-action btn-view"
                                                            data-view-url="{{ route('admin.review.view', $review->id) }}"
                                                            data-toggle-url="{{ route('admin.review.toggle', $review->id) }}"
                                                            data-id="{{ $review->id }}"
                                                            onclick="viewReview(this)"
                                                            title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                    <button class="btn-action btn-delete"
                                                            data-delete-url="{{ route('admin.review.delete', $review->id) }}"
                                                            onclick="deleteReview(this)"
                                                            title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center py-4 text-muted">
                                                    <i class="fa fa-comment-o" style="font-size:32px;"></i><br>
                                                    No reviews found.
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

    {{-- ═══ VIEW REVIEW MODAL ═══ --}}
    <div class="modal fade" id="viewReviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background:#006666; color:#fff;">
                    <h5 class="modal-title"><i class="fa fa-comment"></i> Review Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="reviewModalBody">
                        <div class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @include('components.backend.footer')
    @include('components.backend.main-js')

    <script>
    /* Track currently open review in modal */
    let currentModalReviewId  = null;
    let currentToggleUrl      = null;
    let currentApprovedState  = null;

    /* ══════════════════════════════════════════════════════════
     |  Table toggle switch
     ══════════════════════════════════════════════════════════ */
    document.querySelectorAll('.review-toggle').forEach(toggle => {
        toggle.addEventListener('change', function () {
            const id       = this.dataset.id;
            const url      = this.dataset.toggleUrl;
            const checkbox = this;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    updateRowStatus(id, data.is_approved);
                    showToast(data.message, 'success');
                } else {
                    checkbox.checked = !checkbox.checked;
                    showToast('Failed to update status', 'error');
                }
            })
            .catch(err => {
                checkbox.checked = !checkbox.checked;
                console.error(err);
                showToast('Network error', 'error');
            });
        });
    });

    /* ══════════════════════════════════════════════════════════
     |  Update row status badge + toggle switch after a change
     ══════════════════════════════════════════════════════════ */
    function updateRowStatus(id, isApproved) {
        const badge  = document.getElementById('status-badge-' + id);
        const toggle = document.querySelector(`.review-toggle[data-id="${id}"]`);

        if (badge) {
            badge.classList.remove('approved', 'disapproved');
            badge.classList.add(isApproved ? 'approved' : 'disapproved');
            badge.textContent = isApproved ? 'Approved' : 'Disapproved';
        }
        if (toggle) toggle.checked = !!isApproved;
    }

    /* ══════════════════════════════════════════════════════════
     |  View review — with Change Status button in modal
     ══════════════════════════════════════════════════════════ */
    function viewReview(button) {
        const url     = button.dataset.viewUrl;
        const modalEl = document.getElementById('viewReviewModal');
        const modal   = new bootstrap.Modal(modalEl);
        const body    = document.getElementById('reviewModalBody');

        currentModalReviewId = button.dataset.id;
        currentToggleUrl     = button.dataset.toggleUrl;

        body.innerHTML = '<div class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x"></i></div>';
        modal.show();

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    body.innerHTML = '<div class="alert alert-danger">Failed to load review.</div>';
                    return;
                }
                const r = data.review;
                currentApprovedState = !!r.is_approved;

                /* Stars */
                let stars = '';
                for (let i = 1; i <= 5; i++) {
                    stars += `<span class="review-modal-star${i <= r.rating ? '' : ' empty'}">★</span>`;
                }

                /* Media */
                let mediaHtml = '';
                if (data.media.length > 0) {
                    mediaHtml = '<div class="mb-3"><strong>Attached Media:</strong><br>';
                    data.media.forEach(m => {
                        const mediaUrl = data.mediaBaseUrl + m;
                        const isVideo  = /\.(mp4|mov|webm|avi)$/i.test(m);
                        if (isVideo) {
                            mediaHtml += `<video src="${mediaUrl}" class="review-media-item" controls></video>`;
                        } else {
                            mediaHtml += `<a href="${mediaUrl}" target="_blank"><img src="${mediaUrl}" class="review-media-item"></a>`;
                        }
                    });
                    mediaHtml += '</div>';
                }

                const productBlock = data.productImage
                    ? `<img src="${data.productImage}" style="width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #ddd;margin-right:10px;">`
                    : '';

                body.innerHTML = `
                    <div class="mb-3 p-3 bg-light rounded d-flex align-items-center">
                        ${productBlock}
                        <div>
                            <small class="text-muted">PRODUCT</small>
                            <h6 class="mb-0 fw-bold" style="color:#006666;">${r.product_name || 'Deleted Product'}</h6>
                        </div>
                    </div>

                    <!-- ═══ CHANGE STATUS BOX ═══ -->
                    <div class="status-change-box">
                        <span class="status-label">Current Status</span>
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span class="status-badge ${currentApprovedState ? 'approved' : 'disapproved'}" id="modalStatusBadge">
                                ${currentApprovedState ? 'Approved' : 'Disapproved'}
                            </span>
                            <button class="btn-change-status ${currentApprovedState ? 'to-disapprove' : 'to-approve'}" id="btnChangeStatus" onclick="changeStatusFromModal()">
                                <i class="fa ${currentApprovedState ? 'fa-times-circle' : 'fa-check-circle'}"></i>
                                Change to ${currentApprovedState ? 'Disapproved' : 'Approved'}
                            </button>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted">REVIEWER</small>
                            <p class="mb-0"><strong>${r.reviewer_name}</strong></p>
                            <small>${r.reviewer_email}</small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">RATING</small>
                            <p class="mb-0">${stars} <strong class="ms-2">${r.rating}/5</strong></p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">DATE</small>
                        <p class="mb-0">${new Date(r.created_at).toLocaleString()}</p>
                    </div>

                    ${r.title ? `<div class="mb-3"><small class="text-muted">TITLE</small><h5>${r.title}</h5></div>` : ''}

                    <div class="mb-3">
                        <small class="text-muted">REVIEW CONTENT</small>
                        <div class="p-3" style="white-space: pre-wrap;font-weight: bold;">${r.content || '-'}</div>
                    </div>

                    ${mediaHtml}
                `;
            })
            .catch(err => {
                console.error(err);
                body.innerHTML = '<div class="alert alert-danger">Network error.</div>';
            });
    }

    /* ══════════════════════════════════════════════════════════
     |  Change status from inside the modal
     ══════════════════════════════════════════════════════════ */
    function changeStatusFromModal() {
        if (!currentToggleUrl || !currentModalReviewId) return;

        const btn = document.getElementById('btnChangeStatus');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Updating...';

        fetch(currentToggleUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                currentApprovedState = !!data.is_approved;

                /* Update badge inside modal */
                const modalBadge = document.getElementById('modalStatusBadge');
                if (modalBadge) {
                    modalBadge.classList.remove('approved', 'disapproved');
                    modalBadge.classList.add(currentApprovedState ? 'approved' : 'disapproved');
                    modalBadge.textContent = currentApprovedState ? 'Approved' : 'Disapproved';
                }

                /* Update button label */
                btn.disabled = false;
                btn.classList.remove('to-approve', 'to-disapprove');
                btn.classList.add(currentApprovedState ? 'to-disapprove' : 'to-approve');
                btn.innerHTML = `
                    <i class="fa ${currentApprovedState ? 'fa-times-circle' : 'fa-check-circle'}"></i>
                    Change to ${currentApprovedState ? 'Disapproved' : 'Approved'}
                `;

                /* Update the table row behind the modal too */
                updateRowStatus(currentModalReviewId, currentApprovedState ? 1 : 0);

                showToast(data.message, 'success');
            } else {
                btn.disabled = false;
                showToast('Failed to update status', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            showToast('Network error', 'error');
        });
    }

    /* ══════════════════════════════════════════════════════════
     |  Delete review
     ══════════════════════════════════════════════════════════ */
    function deleteReview(button) {
        if (!confirm('Are you sure you want to delete this review? This cannot be undone.')) return;

        const url = button.dataset.deleteUrl;

        fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 600);
            } else {
                showToast('Failed to delete', 'error');
            }
        });
    }

    /* Simple toast */
    function showToast(message, type = 'success') {
        const bg = type === 'success' ? '#28a745' : '#dc3545';
        const toast = document.createElement('div');
        toast.style.cssText = `
            position:fixed; top:20px; right:20px;
            background:${bg}; color:#fff; padding:12px 20px;
            border-radius:6px; z-index:9999; box-shadow:0 4px 12px rgba(0,0,0,0.2);
            font-weight:600; font-size:14px;
        `;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.style.opacity = '0', 2500);
        setTimeout(() => toast.remove(), 3000);
    }

    /* Init DataTable */
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof $ !== 'undefined' && typeof $.fn.DataTable !== 'undefined') {
            try {
                $('#basic-1').DataTable({
                    "pageLength": 25,
                    "order": [],
                    "columnDefs": [{ "orderable": false, "targets": [2, 7, 9] }]
                });
            } catch (e) { /* already initialized */ }
        }
    });
    </script>

</body>
</html>