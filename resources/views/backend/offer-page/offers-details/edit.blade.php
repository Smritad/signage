<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')

    <style>
        :root {
            --brand:        #064f4f;
            --brand-mid:    #0a6e6e;
            --brand-soft:   #e6f1f1;
            --brand-softer: #f2f8f8;
            --brand-glow:   rgba(10,110,110,.12);
            --bg:           #f5f7fa;
            --surface:      #ffffff;
            --border:       #e3e8ee;
            --border-soft:  #eef1f5;
            --text:         #1f2937;
            --text-mid:     #4b5563;
            --muted:        #6b7280;
            --muted-soft:   #9ca3af;
            --danger:       #dc2626;
            --radius-sm:    8px;
            --radius:       10px;
            --radius-lg:    14px;
        }
        body { font-family:'Plus Jakarta Sans',sans-serif; background:var(--bg); color:var(--text); }
        .page-body { background:var(--bg)!important; }
        .page-heading { display:flex; justify-content:space-between; align-items:center; padding:4px 2px 18px; }
        .page-heading h4 { margin:0; font-weight:700; font-size:1.35rem; color:var(--text); }
        .page-heading .subtitle { font-size:12.5px; color:var(--muted); margin-top:2px; }
        .breadcrumb { margin:0; background:transparent; padding:0; font-size:12.5px; }
        .breadcrumb-item a { color:var(--brand-mid)!important; text-decoration:none; }
        .breadcrumb-item.active { color:var(--muted); }
        .card-clean { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); box-shadow:0 1px 3px rgba(15,23,42,.04); overflow:hidden; }
        .card-clean__header { padding:18px 24px; border-bottom:1px solid var(--border-soft); }
        .card-clean__header h5 { margin:0; font-size:1rem; font-weight:700; color:var(--text); display:flex; align-items:center; gap:8px; }
        .card-clean__header h5::before { content:''; width:4px; height:18px; background:var(--brand); border-radius:3px; }
        .card-clean__header p { margin:4px 0 0 12px; font-size:12.5px; color:var(--muted); }
        .card-clean__body { padding:24px; }
        .section-block+.section-block { margin-top:28px; padding-top:24px; border-top:1px dashed var(--border-soft); }
        .section-title { font-size:12px; font-weight:700; color:var(--text); text-transform:uppercase; letter-spacing:.6px; margin:0 0 4px; }
        .section-hint  { font-size:12px; color:var(--muted); margin:0 0 16px; }
        .form-label { font-size:12.5px; font-weight:600; color:var(--text-mid); margin-bottom:6px; letter-spacing:.1px; }
        .form-control,.form-select { border:1px solid var(--border)!important; border-radius:var(--radius)!important; font-size:13.5px!important; padding:10px 13px!important; color:var(--text)!important; background:var(--surface)!important; box-shadow:none!important; transition:border-color .15s,box-shadow .15s; }
        .form-control::placeholder { color:var(--muted-soft); }
        .form-control:focus,.form-select:focus { border-color:var(--brand-mid)!important; box-shadow:0 0 0 3px var(--brand-glow)!important; outline:none!important; }
        .seg-toggle { display:inline-flex; width:100%; background:var(--brand-softer); border:1px solid var(--border); border-radius:var(--radius); padding:3px; margin-bottom:10px; }
        .seg-btn { flex:1; padding:7px 6px; border-radius:7px; font-size:11.5px; font-weight:600; border:none; background:transparent; color:var(--muted); cursor:pointer; transition:all .15s; display:inline-flex; align-items:center; justify-content:center; gap:5px; white-space:nowrap; }
        .seg-btn:hover:not(.active) { color:var(--brand-mid); }
        .seg-btn.active { background:var(--surface); color:var(--brand); box-shadow:0 1px 3px rgba(15,23,42,.08),0 0 0 1px var(--border-soft); }
        .input-group-prefix,.input-group-suffix { display:inline-flex; align-items:center; justify-content:center; min-width:42px; padding:0 12px; background:var(--brand-softer); border:1px solid var(--border); color:var(--text-mid); font-weight:600; font-size:13.5px; }
        .input-group-prefix { border-right:none; border-radius:var(--radius) 0 0 var(--radius); }
        .input-group-suffix { border-left:none;  border-radius:0 var(--radius) var(--radius) 0; }
        .ig-input-right { border-radius:0 var(--radius) var(--radius) 0!important; border-left:none!important; }
        .ig-input-left  { border-radius:var(--radius) 0 0 var(--radius)!important; border-right:none!important; }
        .hint { font-size:11.5px; color:var(--muted); margin-top:6px; display:flex; align-items:center; gap:5px; }
        .img-upload { border:1.5px dashed var(--border); border-radius:var(--radius); background:var(--brand-softer); padding:22px 16px; text-align:center; position:relative; cursor:pointer; transition:border-color .15s,background .15s; }
        .img-upload:hover { border-color:var(--brand-mid); background:var(--brand-soft); }
        .img-upload input[type="file"] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
        .img-upload__icon { width:36px; height:36px; border-radius:50%; background:var(--surface); border:1px solid var(--border); display:inline-flex; align-items:center; justify-content:center; margin-bottom:8px; color:var(--brand-mid); }
        .img-upload__title { font-size:13px; font-weight:600; color:var(--text); display:block; }
        .img-upload__hint  { font-size:11.5px; color:var(--muted); margin-top:2px; display:block; }
        .img-spec { display:inline-block; margin-top:8px; font-size:10.5px; font-weight:600; letter-spacing:.3px; color:var(--brand-mid); background:var(--surface); border:1px solid var(--border); border-radius:6px; padding:2px 8px; }
        .img-preview { margin-top:10px; display:none; border-radius:var(--radius-sm); overflow:hidden; border:1px solid var(--border); }
        .img-preview.show { display:block; }
        .img-preview img { width:100%; display:block; object-fit:cover; }
        .img-filename { font-size:11px; color:var(--text-mid); font-weight:500; margin-top:6px; word-break:break-all; }
        .existing-img-card { position:relative; border-radius:var(--radius); border:1px solid var(--border); background:var(--surface); overflow:hidden; margin-bottom:10px; }
        .existing-img-card img { width:100%; display:block; object-fit:cover; max-height:110px; }
        .existing-img-card .meta { display:flex; justify-content:space-between; align-items:center; padding:6px 10px; background:var(--brand-softer); font-size:10.5px; font-weight:600; color:var(--brand); letter-spacing:.3px; border-top:1px solid var(--border); }
        .replace-hint { font-size:11.5px; color:var(--muted); margin:6px 0 8px; display:flex; align-items:center; gap:5px; }
        .products-wrapper { display:flex; flex-direction:column; gap:14px; }
        .product-row { background:var(--surface); border:1px solid var(--border); border-left:3px solid var(--brand); border-radius:var(--radius); padding:20px 18px 16px; position:relative; animation:rowIn .2s ease; }
        @keyframes rowIn { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
        .row-num { position:absolute; top:-1px; left:-1px; background:var(--brand); color:#fff; font-size:10.5px; font-weight:700; padding:3px 10px; border-radius:var(--radius) 0 var(--radius-sm) 0; letter-spacing:.4px; }
        .row-remove { position:absolute; top:10px; right:10px; width:26px; height:26px; border-radius:50%; border:1px solid var(--border); background:var(--surface); color:var(--muted); font-size:16px; line-height:1; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all .15s; }
        .row-remove:hover { background:#fef2f2; border-color:#fca5a5; color:var(--danger); }
        .variant-chips { display:flex; flex-wrap:wrap; gap:6px; margin-top:10px; }
        .chip { display:inline-flex; align-items:center; gap:4px; font-size:11.5px; font-weight:600; padding:3px 9px; border-radius:6px; border:1px solid; }
        .chip-cat  { background:var(--brand-softer); border-color:#bfdcdc; color:var(--brand); }
        .chip-unit { background:#fff7ed; border-color:#fed7aa; color:#9a3412; }
        .chip-prod { background:#ecfdf5; border-color:#a7f3d0; color:#065f46; }
        .chip-pin  { background:#faf5ff; border-color:#d8b4fe; color:#6b21a8; }
        .select2-container--default .select2-selection--multiple,
        .select2-container--default .select2-selection--single { border:1px solid var(--border)!important; border-radius:var(--radius)!important; min-height:42px!important; background:var(--surface)!important; }
        .select2-container--default .select2-selection--single { height:42px!important; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height:40px!important; font-size:13.5px!important; color:var(--text)!important; padding-left:13px!important; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height:40px!important; }
        .select2-container--default.select2-container--focus .select2-selection,
        .select2-container--default.select2-container--open  .select2-selection { border-color:var(--brand-mid)!important; box-shadow:0 0 0 3px var(--brand-glow)!important; }
        .select2-container--default .select2-selection--multiple { padding:3px 6px!important; }
        .select2-container--default .select2-selection--multiple .select2-selection__choice { background:var(--brand)!important; border:none!important; color:#fff!important; border-radius:5px!important; font-size:11.5px!important; font-weight:600!important; padding:2px 8px!important; }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove { color:rgba(255,255,255,.75)!important; margin-right:5px!important; }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover { color:#fff!important; }
        .select2-container--default .select2-selection--multiple .select2-selection__placeholder { color:var(--muted-soft)!important; font-size:13px!important; }
        .select2-dropdown { border:1px solid var(--border)!important; border-radius:var(--radius)!important; box-shadow:0 10px 28px rgba(15,23,42,.10)!important; font-size:13px!important; }
        .select2-results__option--highlighted { background:var(--brand-mid)!important; }
        .select2-container { width:100%!important; }
        #addProductBtn { margin-top:14px; width:100%; border:1.5px dashed var(--border); background:transparent; color:var(--brand-mid); border-radius:var(--radius); padding:11px; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:7px; transition:all .15s; }
        #addProductBtn:hover { background:var(--brand-softer); border-color:var(--brand-mid); color:var(--brand); }
        .summary-card { position:sticky; top:20px; }
        .summary-stat { background:linear-gradient(180deg,var(--brand-softer),var(--surface)); border:1px solid var(--border); border-radius:var(--radius); padding:16px 18px; }
        .stat-label { font-size:10.5px; font-weight:700; letter-spacing:.6px; text-transform:uppercase; color:var(--muted); margin-bottom:4px; }
        .stat-value { font-size:1.7rem; font-weight:800; color:var(--brand); line-height:1.15; letter-spacing:-.5px; }
        .stat-sub { font-size:11.5px; color:var(--muted); margin-top:3px; }
        .stat-badge { display:inline-flex; align-items:center; gap:4px; background:var(--surface); color:var(--brand); border:1px solid var(--border); font-size:10.5px; font-weight:700; padding:2px 8px; border-radius:999px; letter-spacing:.3px; margin-bottom:6px; }
        .summary-block { margin-top:18px; }
        .product-pill { background:var(--brand-softer); color:var(--brand); border:1px solid #bfdcdc; font-size:11.5px; font-weight:600; padding:3px 10px; border-radius:999px; display:inline-block; margin:2px 2px 0 0; }
        .empty-text { font-size:12px; color:var(--muted-soft); font-style:italic; }
        .summary-thumb-grid { display:flex; gap:10px; }
        .summary-thumb-cell { flex:1; }
        .summary-thumb-cell .label { font-size:10px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px; }
        .summary-thumb-cell .frame { width:100%; height:56px; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--brand-softer); display:flex; align-items:center; justify-content:center; overflow:hidden; }
        .summary-thumb-cell .frame img { width:100%; height:100%; object-fit:cover; }
        .summary-thumb-cell .none { font-size:10.5px; color:var(--muted-soft); font-style:italic; }
        .btn-save { background:var(--brand)!important; color:#fff!important; border:none!important; border-radius:var(--radius)!important; font-weight:600!important; padding:10px 22px!important; font-size:13.5px!important; transition:background .15s,transform .15s; }
        .btn-save:hover { background:var(--brand-mid)!important; transform:translateY(-1px); }
        .btn-cancel { background:var(--surface)!important; color:var(--text-mid)!important; border:1px solid var(--border)!important; border-radius:var(--radius)!important; font-weight:600!important; padding:10px 18px!important; font-size:13.5px!important; }
        .btn-cancel:hover { background:var(--brand-softer)!important; color:var(--brand)!important; }
        .field-error { font-size:11.5px; color:var(--danger); margin-top:4px; }
        .slot-section { background:var(--brand-softer); border:1px solid var(--border-soft); border-radius:var(--radius-sm); padding:14px 14px 10px; margin-top:12px; }
        .slot-section-label { font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.5px; margin-bottom:10px; }
        .pinned-note { font-size:11.5px; color:#6b21a8; background:#faf5ff; border:1px solid #d8b4fe; border-radius:6px; padding:6px 10px; margin-top:8px; display:flex; align-items:flex-start; gap:6px; }
    </style>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
</head>
<body>

@include('components.backend.header')
@include('components.backend.sidebar')

<div class="page-body">
<div class="container-fluid">

    <div class="page-heading">
        <div>
            <h4>Edit Offer</h4>
            <div class="subtitle">Update this bundle's details, pricing, images, or product slots.</div>
        </div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('offer-details.index') }}">Offers</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </div>

    @if($errors->any())
    <div class="alert alert-danger rounded-3 mb-3">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form action="{{ route('offer-details.update', $offer->id) }}" method="POST" id="offerForm" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="products"         id="productsJson">
    <input type="hidden" name="offer_price_type" id="offerPriceType"
           value="{{ old('offer_price_type', $offer->offer_price_type ?? 'fixed') }}">

    <div class="row g-4">

        {{-- ── LEFT ──────────────────────────────────────────── --}}
        <div class="col-lg-8">
            <div class="card-clean">
                <div class="card-clean__header">
                    <h5>Edit Offer Details</h5>
                    <p>Update the name, price, images or product slots of this bundle.</p>
                </div>
                <div class="card-clean__body">

                    {{-- Basic Info --}}
                    <div class="section-block">
                        <div class="section-title">Basic Info</div>
                        <div class="section-hint">Identifies the offer and how its price is calculated.</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Offer Name <span class="text-danger">*</span></label>
                                <input type="text" name="offer_name" class="form-control"
                                       value="{{ old('offer_name', $offer->offer_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pricing <span class="text-danger">*</span></label>
                                <div class="seg-toggle" id="pricingToggle">
                                    <button type="button" class="seg-btn active" data-type="fixed">Fixed Price</button>
                                    <button type="button" class="seg-btn"        data-type="percent">Discount %</button>
                                </div>
                                <div id="fixedPriceWrap">
                                    <div class="d-flex">
                                        <span class="input-group-prefix">₹</span>
                                        <input type="number" name="offer_price" id="offerPriceInput"
                                               class="form-control ig-input-right"
                                               placeholder="0" min="0" step="0.01"
                                               value="{{ old('offer_price', $offer->offer_price_type === 'fixed' ? $offer->offer_price : '') }}">
                                    </div>
                                    <div class="hint">Fixed selling price for the entire bundle.</div>
                                </div>
                                <div id="percentPriceWrap" style="display:none;">
                                    <div class="d-flex">
                                        <input type="number" name="offer_discount_percent" id="offerDiscountInput"
                                               class="form-control ig-input-left"
                                               placeholder="0" min="0.01" max="100" step="0.01"
                                               value="{{ old('offer_discount_percent', $offer->offer_price_type === 'percent' ? $offer->offer_price : '') }}">
                                        <span class="input-group-suffix">%</span>
                                    </div>
                                    <div class="hint">Flat discount % applied on the cart total.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Images --}}
                    <div class="section-block">
                        <div class="section-title">Offer Images</div>
                        <div class="section-hint">Existing images shown below. Upload to replace.</div>
                        <div class="row g-3">

                            {{-- Banner --}}
                            <div class="col-md-6">
                                <label class="form-label">Banner Image</label>
                                @if($offer->banner_image)
                                    <div class="existing-img-card" id="existingBannerBox">
                                        <img src="{{ asset('offerimage/' . $offer->banner_image) }}" alt="Current Banner">
                                        <div class="meta"><span>Current Banner</span><span>1200 × 400 px</span></div>
                                    </div>
                                    <div class="replace-hint">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        Upload a new file below to replace.
                                    </div>
                                @endif
                                <div class="img-upload">
                                    <input type="file" name="banner_image" accept="image/jpeg,image/png,image/webp"
                                           onchange="previewImage(this,'bannerPreview','bannerFilename','banner','existingBannerBox')">
                                    <span class="img-upload__icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/></svg>
                                    </span>
                                    <span class="img-upload__title">{{ $offer->banner_image ? 'Replace banner image' : 'Click to upload banner' }}</span>
                                    <span class="img-upload__hint">JPG, PNG or WEBP</span>
                                    <span class="img-spec">Recommended: 1200 × 400 px</span>
                                </div>
                                <div class="img-preview" id="bannerPreview"><img src="" alt="" style="max-height:110px;"></div>
                                <div class="img-filename" id="bannerFilename"></div>
                                @error('banner_image')<div class="field-error">{{ $message }}</div>@enderror
                            </div>

                            {{-- Offer image --}}
                            <div class="col-md-6">
                                <label class="form-label">Offer Image</label>
                                @if($offer->offer_image)
                                    <div class="existing-img-card" id="existingOfferBox">
                                        <img src="{{ asset('offerimage/' . $offer->offer_image) }}" alt="Current Offer Image">
                                        <div class="meta"><span>Current Offer Image</span><span>800 × 800 px</span></div>
                                    </div>
                                    <div class="replace-hint">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        Upload a new file below to replace.
                                    </div>
                                @endif
                                <div class="img-upload">
                                    <input type="file" name="offer_image" accept="image/jpeg,image/png,image/webp"
                                           onchange="previewImage(this,'offerPreview','offerFilename','offer','existingOfferBox')">
                                    <span class="img-upload__icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 12v9H4v-9"/><rect x="2" y="7" width="20" height="5"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
                                    </span>
                                    <span class="img-upload__title">{{ $offer->offer_image ? 'Replace offer image' : 'Click to upload offer image' }}</span>
                                    <span class="img-upload__hint">JPG, PNG or WEBP</span>
                                    <span class="img-spec">Recommended: 800 × 800 px</span>
                                </div>
                                <div class="img-preview" id="offerPreview"><img src="" alt="" style="max-height:110px;"></div>
                                <div class="img-filename" id="offerFilename"></div>
                                @error('offer_image')<div class="field-error">{{ $message }}</div>@enderror
                            </div>

                        </div>
                    </div>

                    {{-- Product Slots --}}
                    <div class="section-block">
                        <div class="section-title">Product Slots</div>
                        <div class="section-hint">
                            Each slot = one customer step.<br>
                            <strong>By Category</strong> – customer picks any product from that category.<br>
                            <strong>Specific Products</strong> – you fix the exact list.<br>
                            <strong>Category + Pinned</strong> – pick a category then pin exact products the customer chooses from (e.g. "Nebula, Altair, Aquila, Kepler").
                        </div>
                        <div id="productsWrapper" class="products-wrapper"></div>
                        <button type="button" id="addProductBtn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Add Another Slot
                        </button>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── RIGHT ─────────────────────────────────────────── --}}
        <div class="col-lg-4">
            <div class="card-clean summary-card">
                <div class="card-clean__header">
                    <h5>Live Summary</h5>
                    <p>Auto-updates as you edit.</p>
                </div>
                <div class="card-clean__body">

                    <div class="summary-stat">
                        <div id="summaryTypeBadge" class="stat-badge">Fixed Price</div>
                        <div class="stat-label">Offer Value</div>
                        <div class="stat-value" id="summaryPrice">—</div>
                        <div class="stat-sub"   id="summaryPriceSub"></div>
                    </div>

                    <div class="summary-block">
                        <div class="stat-label">Slots</div>
                        <div id="summaryProducts"><span class="empty-text">Loading…</span></div>
                    </div>

                    <div class="summary-block">
                        <div class="stat-label">Images</div>
                        <div class="summary-thumb-grid">
                            <div class="summary-thumb-cell">
                                <div class="label">Banner</div>
                                <div class="frame">
                                    @if($offer->banner_image)
                                        <img id="summaryBannerThumb" src="{{ asset('offerimage/' . $offer->banner_image) }}" alt="">
                                    @else
                                        <img id="summaryBannerThumb" src="" alt="" style="display:none;">
                                        <span id="summaryBannerNone" class="none">None</span>
                                    @endif
                                </div>
                            </div>
                            <div class="summary-thumb-cell">
                                <div class="label">Offer</div>
                                <div class="frame">
                                    @if($offer->offer_image)
                                        <img id="summaryOfferThumb" src="{{ asset('offerimage/' . $offer->offer_image) }}" alt="">
                                    @else
                                        <img id="summaryOfferThumb" src="" alt="" style="display:none;">
                                        <span id="summaryOfferNone" class="none">None</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="summary-block">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ $offer->is_active ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$offer->is_active ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-save flex-grow-1">Update Offer</button>
                        <a href="{{ route('offer-details.index') }}" class="btn btn-cancel">Cancel</a>
                    </div>

                </div>
            </div>
        </div>

    </div>
    </form>
</div>
</div>

@include('components.backend.footer')
@include('components.backend.main-js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
/* ── PHP → JS data ── */
const ALL_PRODUCTS            = @json($products ?? []);
const ALL_CATEGORIES          = @json($categories ?? []);
const EXISTING_SLOTS          = @json($offer->products_decoded ?? []);
const OFFER_PRICE_TYPE_SAVED  = '{{ old('offer_price_type', $offer->offer_price_type ?? 'fixed') }}';

let rowIdx = 0;

/* ══════════════════════════════════════════════════════
   IMAGE PREVIEW
══════════════════════════════════════════════════════ */
function previewImage(input, previewId, filenameId, thumbType, existingBoxId) {
    const file = input.files[0];
    if (!file) return;
    if (existingBoxId) $('#' + existingBoxId).hide();
    const reader = new FileReader();
    reader.onload = function(e) {
        $('#' + previewId).find('img').attr('src', e.target.result);
        $('#' + previewId).addClass('show');
        $('#' + filenameId).text(file.name + ' · ' + (file.size / 1024).toFixed(1) + ' KB');
        if (thumbType === 'banner') {
            $('#summaryBannerThumb').attr('src', e.target.result).show();
            $('#summaryBannerNone').hide();
        } else {
            $('#summaryOfferThumb').attr('src', e.target.result).show();
            $('#summaryOfferNone').hide();
        }
    };
    reader.readAsDataURL(file);
}

/* ══════════════════════════════════════════════════════
   PRICE TYPE TOGGLE
══════════════════════════════════════════════════════ */
$(document).on('click', '#pricingToggle .seg-btn', function () {
    const type = $(this).data('type');
    $('#pricingToggle .seg-btn').removeClass('active');
    $(this).addClass('active');
    $('#offerPriceType').val(type);
    if (type === 'fixed') {
        $('#fixedPriceWrap').show(); $('#percentPriceWrap').hide();
        $('#offerPriceInput').attr('required', true);
        $('#offerDiscountInput').removeAttr('required');
    } else {
        $('#fixedPriceWrap').hide(); $('#percentPriceWrap').show();
        $('#offerDiscountInput').attr('required', true);
        $('#offerPriceInput').removeAttr('required');
    }
    updateSummary();
});

/* ══════════════════════════════════════════════════════
   HELPERS
══════════════════════════════════════════════════════ */
function escHtml(str) {
    return String(str || '').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function getUnitsForCategory(catId) {
    const units = new Set();
    ALL_PRODUCTS.forEach(p => {
        if (String(p.category_id) !== String(catId)) return;
        let variants = p.price_variants;
        if (typeof variants === 'string') { try { variants = JSON.parse(variants); } catch(e) { variants = []; } }
        if (!Array.isArray(variants)) variants = [];
        variants.forEach(v => {
            const u = v.measurement_unit || v.unit || v.ml || v.size;
            if (u) units.add(u);
        });
    });
    return [...units];
}

function getProductsForCategory(catId) {
    return ALL_PRODUCTS.filter(p => String(p.category_id) === String(catId));
}

function buildCategoryOptions(selectedId) {
    let html = '<option value="">— Select Category —</option>';
    ALL_CATEGORIES.forEach(c => {
        html += `<option value="${c.id}" ${String(c.id) === String(selectedId) ? 'selected' : ''}>${escHtml(c.category_name)}</option>`;
    });
    return html;
}

function buildAllProductOptions() {
    let html = '';
    ALL_PRODUCTS.forEach(p => {
        html += `<option value="${p.id}">${escHtml(p.product_name)}</option>`;
    });
    return html;
}

/* ══════════════════════════════════════════════════════
   ADD SLOT ROW
══════════════════════════════════════════════════════ */
function addSlotRow(prefill) {
    rowIdx++;
    const i     = rowIdx;
    prefill     = prefill || {};
    const stype = prefill.slot_type || 'category';

    const html = `
    <div class="product-row" id="prow${i}">
        <div class="row-num">Slot ${i}</div>
        <button type="button" class="row-remove" onclick="removeRow(${i})">&times;</button>

        <div class="row g-3 mt-1">

            <div class="col-md-5">
                <label class="form-label">Step Label</label>
                <input type="text" class="form-control slot-label" id="slotLabel${i}"
                       placeholder="e.g. Any 1 Sandalwood Perfume 100ml"
                       value="${escHtml(prefill.slot_label || '')}">
                <div class="hint">Shown as the step title on the storefront.</div>
            </div>

            <div class="col-md-2">
                <label class="form-label">Qty</label>
                <input type="number" class="form-control slot-qty" id="slotQty${i}"
                       min="1" value="${prefill.qty || 1}">
            </div>

            <div class="col-md-5">
                <label class="form-label">Selection Type</label>
                <div class="seg-toggle slot-type-toggle" id="slotToggle${i}">
                    <button type="button" class="seg-btn ${stype==='category'       ?'active':''}"
                            data-slot="${i}" data-slottype="category">By Category</button>
                    <button type="button" class="seg-btn ${stype==='specific'       ?'active':''}"
                            data-slot="${i}" data-slottype="specific">Specific</button>
                    <button type="button" class="seg-btn ${stype==='category_pinned'?'active':''}"
                            data-slot="${i}" data-slottype="category_pinned">Category + Product</button>
                </div>
            </div>

            {{-- CATEGORY WRAP --}}
            <div class="col-12 slot-category-wrap" id="catWrap${i}"
                 style="${stype!=='category'?'display:none':''}">
                <div class="slot-section">
                    <div class="slot-section-label">Category options</div>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Category</label>
                            <select class="form-select slot-cat" id="slotCat${i}" data-idx="${i}">
                                ${buildCategoryOptions(prefill.category_id)}
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">Filter by Unit(s) <span style="font-weight:400;color:var(--muted);">(blank = all)</span></label>
                            <select class="unit-sel" id="unitSel${i}" data-idx="${i}" multiple></select>
                            <div class="mt-2">
                                <small class="text-muted fw-semibold">Matching Products</small>
                                <div id="matchingProducts${i}" class="mt-1 d-flex flex-wrap gap-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SPECIFIC WRAP --}}
            <div class="col-12 slot-specific-wrap" id="specificWrap${i}"
                 style="${stype!=='specific'?'display:none':''}">
                <div class="slot-section">
                    <div class="slot-section-label">Pick exact products</div>
                    <label class="form-label">Products</label>
                    <select class="specific-sel" id="specificSel${i}" data-idx="${i}" multiple>
                        ${buildAllProductOptions()}
                    </select>
                </div>
            </div>

            {{-- CATEGORY + PINNED WRAP --}}
            <div class="col-12 slot-pinned-wrap" id="pinnedWrap${i}"
                 style="${stype!=='category_pinned'?'display:none':''}">
                <div class="slot-section">
                    <div class="slot-section-label">Category + Pinned products</div>
                    <div class="pinned-note">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Choose a category first, then pin the specific products the customer will pick from.
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-5">
                            <label class="form-label">Category</label>
                            <select class="form-select slot-pinned-cat" id="slotPinnedCat${i}" data-idx="${i}">
                                ${buildCategoryOptions(prefill.category_id)}
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">Filter by Unit(s) <span style="font-weight:400;color:var(--muted);">(optional)</span></label>
                            <select class="pinned-unit-sel" id="pinnedUnitSel${i}" data-idx="${i}" multiple></select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Pinned Products <span class="text-danger">*</span></label>
                            <select class="pinned-prod-sel" id="pinnedProdSel${i}" data-idx="${i}" multiple></select>
                            <div class="hint">Only these products will be shown to the customer for this slot.</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="variant-chips" id="slotChips${i}"></div>
    </div>`;

    $('#productsWrapper').append(html);

    /* Init Select2 – unit selector (category type) */
    $(`#unitSel${i}`).select2({ placeholder:'— All units —', allowClear:true })
        .on('change', function () { renderMatchingProducts(i); refreshSlotChips(i); updateSummary(); });

    /* Init Select2 – specific products */
    $(`#specificSel${i}`).select2({ placeholder:'— Search products —', allowClear:true })
        .on('change', function () { refreshSlotChips(i); updateSummary(); });

    /* Init Select2 – pinned unit selector */
    $(`#pinnedUnitSel${i}`).select2({ placeholder:'— All units —', allowClear:true })
        .on('change', function () { repopulatePinnedProducts(i); refreshSlotChips(i); updateSummary(); });

    /* Init Select2 – pinned products */
    $(`#pinnedProdSel${i}`).select2({ placeholder:'— Search & select products —', allowClear:true })
        .on('change', function () { refreshSlotChips(i); updateSummary(); });

    /* Category change – category type */
    $(`#slotCat${i}`).on('change', function () {
        populateUnits(i, $(this).val(), []);
        renderMatchingProducts(i);
        refreshSlotChips(i);
        updateSummary();
    });

    /* Category change – category_pinned type */
    $(`#slotPinnedCat${i}`).on('change', function () {
        populatePinnedUnits(i, $(this).val(), []);
        repopulatePinnedProducts(i);
        refreshSlotChips(i);
        updateSummary();
    });

    /* Prefill: category type */
    if (stype === 'category' && prefill.category_id) {
        populateUnits(i, prefill.category_id, prefill.units || []);
        renderMatchingProducts(i);
    }

    /* Prefill: specific type */
    if (stype === 'specific' && prefill.specific_product_ids && prefill.specific_product_ids.length) {
        setTimeout(() => {
            $(`#specificSel${i}`).val(prefill.specific_product_ids.map(String)).trigger('change');
        }, 120);
    }

    /* Prefill: category_pinned type */
    if (stype === 'category_pinned' && prefill.category_id) {
        populatePinnedUnits(i, prefill.category_id, prefill.units || [], function () {
            repopulatePinnedProducts(i, function () {
                if (prefill.pinned_product_ids && prefill.pinned_product_ids.length) {
                    setTimeout(() => {
                        $(`#pinnedProdSel${i}`).val(prefill.pinned_product_ids.map(String)).trigger('change');
                    }, 150);
                }
            });
        });
    }

    $(`#slotLabel${i}, #slotQty${i}`).on('input', updateSummary);
    refreshSlotChips(i);
    updateSummary();
}

/* ── Populate units for category type ── */
function populateUnits(i, catId, preselect) {
    const $sel  = $(`#unitSel${i}`);
    const units = getUnitsForCategory(catId);
    if ($sel.hasClass('select2-hidden-accessible')) $sel.select2('destroy');
    $sel.empty();
    units.forEach(u => $sel.append(new Option(u, u)));
    if (preselect && preselect.length) $sel.val(preselect);
    $sel.select2({ placeholder:'— All units —', allowClear:true })
        .on('change', function () { renderMatchingProducts(i); refreshSlotChips(i); updateSummary(); });
}

/* ── Populate units for category_pinned type ── */
function populatePinnedUnits(i, catId, preselect, callback) {
    const $sel  = $(`#pinnedUnitSel${i}`);
    const units = getUnitsForCategory(catId);
    if ($sel.hasClass('select2-hidden-accessible')) $sel.select2('destroy');
    $sel.empty();
    units.forEach(u => $sel.append(new Option(u, u)));
    if (preselect && preselect.length) $sel.val(preselect);
    $sel.select2({ placeholder:'— All units —', allowClear:true })
        .on('change', function () { repopulatePinnedProducts(i); refreshSlotChips(i); updateSummary(); });
    if (callback) callback();
}

/* ── Repopulate pinned product dropdown based on category + units ── */
function repopulatePinnedProducts(i, callback) {
    const catId        = $(`#slotPinnedCat${i}`).val();
    const selectedUnits = $(`#pinnedUnitSel${i}`).val() || [];
    const $sel         = $(`#pinnedProdSel${i}`);
    const prevSelected = $sel.val() || [];

    if ($sel.hasClass('select2-hidden-accessible')) $sel.select2('destroy');
    $sel.empty();

    let products = catId ? getProductsForCategory(catId) : [];

    if (selectedUnits.length) {
        products = products.filter(p => {
            let variants = p.price_variants;
            if (typeof variants === 'string') { try { variants = JSON.parse(variants); } catch(e) { variants = []; } }
            if (!Array.isArray(variants)) variants = [];
            return variants.some(v => {
                const u = v.measurement_unit || v.unit || v.ml || v.size;
                return selectedUnits.includes(String(u));
            });
        });
    }

    products.forEach(p => $sel.append(new Option(p.product_name, p.id)));

    const validIds  = products.map(p => String(p.id));
    const stillValid = prevSelected.filter(id => validIds.includes(String(id)));
    if (stillValid.length) $sel.val(stillValid);

    $sel.select2({ placeholder:'— Search & select products —', allowClear:true })
        .on('change', function () { refreshSlotChips(i); updateSummary(); });

    if (callback) callback();
}

/* ── Render matching products preview (category type) ── */
function renderMatchingProducts(i) {
    const catId = $(`#slotCat${i}`).val();
    const units = $(`#unitSel${i}`).val() || [];
    const wrap  = $(`#matchingProducts${i}`);
    wrap.html('');
    if (!catId) return;
    let products = getProductsForCategory(catId);
    if (units.length) {
        products = products.filter(p => {
            let variants = p.price_variants;
            if (typeof variants === 'string') { try { variants = JSON.parse(variants); } catch(e) { variants = []; } }
            if (!Array.isArray(variants)) variants = [];
            return variants.some(v => {
                const u = v.measurement_unit || v.unit || v.ml || v.size;
                return units.includes(String(u));
            });
        });
    }
    if (!products.length) { wrap.html('<span class="text-danger small">No matching products</span>'); return; }
    wrap.html(products.map(p => `<span class="chip chip-prod">${escHtml(p.product_name)}</span>`).join(''));
}

/* ══════════════════════════════════════════════════════
   SLOT TYPE TOGGLE
══════════════════════════════════════════════════════ */
$(document).on('click', '.slot-type-toggle .seg-btn', function () {
    const i    = $(this).data('slot');
    const type = $(this).data('slottype');
    $(`#slotToggle${i} .seg-btn`).removeClass('active');
    $(this).addClass('active');
    $(`#catWrap${i}`).toggle(type === 'category');
    $(`#specificWrap${i}`).toggle(type === 'specific');
    $(`#pinnedWrap${i}`).toggle(type === 'category_pinned');
    refreshSlotChips(i);
    updateSummary();
});

/* ══════════════════════════════════════════════════════
   REFRESH CHIPS
══════════════════════════════════════════════════════ */
function refreshSlotChips(i) {
    const type = $(`#slotToggle${i} .seg-btn.active`).data('slottype');
    let html   = '';
    if (type === 'category') {
        const catName = $(`#slotCat${i} option:selected`).text();
        const units   = $(`#unitSel${i}`).val() || [];
        if (catName && catName !== '— Select Category —') html += `<span class="chip chip-cat">${escHtml(catName)}</span>`;
        units.forEach(u => { html += `<span class="chip chip-unit">${escHtml(u)}</span>`; });
    } else if (type === 'specific') {
        $(`#specificSel${i} option:selected`).each(function () {
            html += `<span class="chip chip-prod">${escHtml($(this).text())}</span>`;
        });
    } else if (type === 'category_pinned') {
        const catName = $(`#slotPinnedCat${i} option:selected`).text();
        const units   = $(`#pinnedUnitSel${i}`).val() || [];
        if (catName && catName !== '— Select Category —') html += `<span class="chip chip-cat">${escHtml(catName)}</span>`;
        units.forEach(u => { html += `<span class="chip chip-unit">${escHtml(u)}</span>`; });
        $(`#pinnedProdSel${i} option:selected`).each(function () {
            html += `<span class="chip chip-pin">${escHtml($(this).text())}</span>`;
        });
    }
    $(`#slotChips${i}`).html(html);
}

/* ══════════════════════════════════════════════════════
   REMOVE ROW
══════════════════════════════════════════════════════ */
function removeRow(i) {
    $(`#prow${i}`).remove();
    updateSummary();
}

/* ══════════════════════════════════════════════════════
   UPDATE SUMMARY + SERIALISE JSON
══════════════════════════════════════════════════════ */
function updateSummary() {
    const type      = $('#offerPriceType').val();
    const isPercent = type === 'percent';
    const val       = isPercent
        ? parseFloat($('#offerDiscountInput').val()) || 0
        : parseFloat($('#offerPriceInput').val())    || 0;

    $('#summaryTypeBadge').text(isPercent ? 'Discount %' : 'Fixed Price');
    if (val > 0) {
        $('#summaryPrice').text(isPercent ? val + '%' : '₹' + val.toLocaleString('en-IN'));
        $('#summaryPriceSub').text(isPercent ? 'Flat discount on cart total' : 'Fixed bundle price');
    } else {
        $('#summaryPrice').text('—'); $('#summaryPriceSub').text('');
    }

    const slots   = [];
    let pillsHtml = '';

    $('.product-row').each(function () {
        const i     = $(this).attr('id').replace('prow', '');
        const stype = $(`#slotToggle${i} .seg-btn.active`).data('slottype') || 'category';
        const label = $(`#slotLabel${i}`).val().trim();
        const qty   = parseInt($(`#slotQty${i}`).val()) || 1;
        const slot  = { slot_label: label, slot_type: stype, qty };

        if (stype === 'category') {
            slot.category_id          = $(`#slotCat${i}`).val()       || null;
            slot.units                = $(`#unitSel${i}`).val()        || [];
            slot.specific_product_ids = [];
            slot.pinned_product_ids   = [];
            const catName = $(`#slotCat${i} option:selected`).text();
            if (catName && catName !== '— Select Category —') {
                const unitStr = slot.units.length ? ` (${slot.units.join(', ')})` : '';
                pillsHtml += `<span class="product-pill">${qty > 1 ? qty + '× ' : ''}${escHtml(label || catName)}${escHtml(unitStr)}</span>`;
            }
        } else if (stype === 'specific') {
            slot.category_id          = null;
            slot.units                = [];
            slot.pinned_product_ids   = [];
            slot.specific_product_ids = ($(`#specificSel${i}`).val() || []).map(Number);
            const names = [];
            $(`#specificSel${i} option:selected`).each(function () { names.push($(this).text()); });
            if (names.length) {
                pillsHtml += `<span class="product-pill">${qty > 1 ? qty + '× ' : ''}${escHtml(label || names.join(', '))}</span>`;
            }
        } else if (stype === 'category_pinned') {
            slot.category_id          = $(`#slotPinnedCat${i}`).val() || null;
            slot.units                = $(`#pinnedUnitSel${i}`).val()  || [];
            slot.specific_product_ids = [];
            slot.pinned_product_ids   = ($(`#pinnedProdSel${i}`).val() || []).map(Number);
            const catName  = $(`#slotPinnedCat${i} option:selected`).text();
            const pinNames = [];
            $(`#pinnedProdSel${i} option:selected`).each(function () { pinNames.push($(this).text()); });
            if (catName && catName !== '— Select Category —') {
                const sub = pinNames.length ? ': ' + pinNames.join(', ') : '';
                pillsHtml += `<span class="product-pill">${qty > 1 ? qty + '× ' : ''}${escHtml(label || catName)}${escHtml(sub)}</span>`;
            }
        }

        slots.push(slot);
    });

    $('#productsJson').val(JSON.stringify(slots));
    $('#summaryProducts').html(pillsHtml || '<span class="empty-text">No slots yet</span>');
}

/* ══════════════════════════════════════════════════════
   INIT
══════════════════════════════════════════════════════ */
$(document).ready(function () {
    if (OFFER_PRICE_TYPE_SAVED === 'percent') {
        $('[data-type="percent"]').trigger('click');
    }

    if (EXISTING_SLOTS && EXISTING_SLOTS.length) {
        EXISTING_SLOTS.forEach(slot => addSlotRow(slot));
    } else {
        addSlotRow();
    }

    setTimeout(updateSummary, 300);

    $('#addProductBtn').on('click', function () { addSlotRow(); });
    $('#offerPriceInput, #offerDiscountInput').on('input', updateSummary);

    $('#offerForm').on('submit', function (e) {
        let slots = [];
        try { slots = JSON.parse($('#productsJson').val() || '[]'); } catch (ex) {}
        if (!slots.length) {
            e.preventDefault();
            alert('Please add at least one product slot.');
        }
    });
});
</script>
</body>
</html>