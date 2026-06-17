<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')
    <style>
        .image-preview {
            max-width: 100px;
            max-height: 100px;
            margin-top: 5px;
        }
        .copy-from-card {
            background: #e8f4fd;
            border: 1px dashed #006666;
        }
        .section-card {
            border-left: 4px solid #006666;
            background: #fdfdfd;
        }
        .section-title {
            color: #006666;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .section-subtitle {
            color: #6c757d;
            font-size: 13px;
            margin-bottom: 15px;
        }
        .required-star {
            color: #dc3545;
            font-weight: 700;
        }
        .field-hint {
            color: #6c757d;
            font-size: 12px;
            margin-top: 3px;
            display: block;
        }
        .form-label {
            font-weight: 500;
        }
        .required-legend {
            font-size: 13px;
            color: #6c757d;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
@include('components.backend.header')
@include('components.backend.sidebar')

@php
    $isClone             = isset($cloneProduct) && $cloneProduct;
    $cloneSubCats        = $isClone ? (json_decode($cloneProduct->sub_category_id, true) ?? []) : [];
    $cloneImages         = $isClone ? (json_decode($cloneProduct->images, true) ?? []) : [];
    $clonePerfumeNotes   = $isClone ? (json_decode($cloneProduct->perfume_notes, true) ?? []) : [];
    $clonePerfumeDetails = $isClone ? (json_decode($cloneProduct->perfume_details, true) ?? []) : [];
    $cloneFaqs           = $isClone ? (json_decode($cloneProduct->faqs, true) ?? []) : [];

    // FIX: fragrance_type_id can be JSON array OR plain int — handle both
    $cloneFragranceIds = [];
    if ($isClone) {
        $raw     = $cloneProduct->fragrance_type_id;
        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $cloneFragranceIds = $decoded;
        } elseif (!empty($raw)) {
            $cloneFragranceIds = [$raw];
        }
    }
    // Normalize all IDs to strings for comparison
    $cloneFragranceIds = array_map('strval', $cloneFragranceIds);
@endphp

<div class="page-body">
    <div class="container-fluid">

        {{-- ===================== PAGE TITLE ===================== --}}
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-6">
                    <h4>
                        <i class="fa fa-box-open text-primary"></i>
                        {{ $isClone ? 'Add Product (Copy Mode)' : 'Add New Product' }}
                    </h4>
                    <p class="text-muted mb-0">
                        Fill in the product details below. Fields marked with
                        <span class="required-star">*</span> are required.
                    </p>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb float-end">
                        <li class="breadcrumb-item"><a href="{{ route('products-details.index') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('products-details.index') }}">Products</a></li>
                        <li class="breadcrumb-item active">Add Product</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Product Information</h5>
                            <small class="text-muted">Complete all required fields to create a new product.</small>
                        </div>
                        <div class="required-legend">
                            <span class="required-star">*</span> Required field
                        </div>
                    </div>
                    <div class="card-body">

                        {{-- ===================== COPY FROM EXISTING PRODUCT ===================== --}}
                        <div class="card mb-4 p-3 copy-from-card">
                            <h5 class="mb-2">
                                Quick Fill — Copy from Existing Product
                                <small class="text-muted">(Optional shortcut)</small>
                            </h5>
                            <small class="text-muted d-block mb-3">
                                Save time by copying all details from an existing product.
                                After copying, update only the fields you want to change
                                (such as price, offer price, images, or ml size) and save as a new product.
                            </small>

                            <div class="row g-3 align-items-end">
                                <div class="col-md-9">
                                    <label class="form-label">Choose a product to copy from</label>
                                    <select id="copy-from-product" class="form-control select2-copy-from">
                                        <option value="">-- Start blank / don't copy --</option>
                                        @foreach($allProducts as $p)
                                            <option value="{{ $p->id }}"
                                                {{ $isClone && $cloneProduct->id == $p->id ? 'selected' : '' }}>
                                                {{ $p->product_name }} ({{ $p->product_sku }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="field-hint">Search by product name or SKU to quickly find the product.</small>
                                </div>
                                <div class="col-md-3" style="padding: 28px;">
                                    @if($isClone)
                                        <a href="{{ route('products-details.create') }}" class="btn btn-secondary w-100">
                                            <i class="fa fa-eraser"></i> Clear & Start Blank
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-primary w-100" id="btn-apply-copy" disabled>
                                            <i class="fa fa-magic"></i> Apply Copy
                                        </button>
                                    @endif
                                </div>
                            </div>

                            @if($isClone)
                                <div class="alert alert-info mt-3 mb-0">
                                    <i class="fa fa-check-circle"></i>
                                    <strong>Copied from:</strong> {{ $cloneProduct->product_name }}
                                    ({{ $cloneProduct->product_sku }}) — change any fields you want, then click
                                    <strong>Save as New Product</strong> at the bottom.
                                </div>
                            @endif
                        </div>

                        <form action="{{ route('products-details.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- ===================== 1. CATEGORY & SUBCATEGORY ===================== --}}
                        <div class="card section-card mb-4 p-3">
                            <h5 class="section-title">
                                <i class="fa fa-sitemap"></i> 1. Category & Subcategory
                            </h5>
                            <p class="section-subtitle">
                                Choose which category and subcategories this product belongs to.
                            </p>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Category <span class="required-star">*</span>
                                    </label>
                                    <select name="category_id" class="form-control select2-category" required>
                                        <option value="">-- Select Category --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}"
                                                {{ old('category_id', $cloneProduct->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="field-hint">Primary category this product belongs to.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Sub Category <span class="required-star">*</span>
                                    </label>
                                    <select name="sub_category_id[]" class="form-control select2-subcategory" multiple required>
                                        @foreach($subCategories as $sub)
                                            <option value="{{ $sub->id }}"
                                                {{ in_array($sub->id, old('sub_category_id', $cloneSubCats)) ? 'selected' : '' }}>
                                                {{ $sub->sab_category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="field-hint">You can select one or more subcategories.</small>
                                </div>
                            </div>
                        </div>

                        {{-- ===================== 2. BASIC PRODUCT INFO ===================== --}}
                        <div class="card section-card mb-4 p-3">
                            <h5 class="section-title">
                                <i class="fa fa-tag"></i> 2. Basic Product Information
                            </h5>
                            <p class="section-subtitle">
                                Enter the product name, pricing, and stock details.
                            </p>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">
                                        Product Name <span class="required-star">*</span>
                                    </label>
                                    <input type="text" name="product_name" class="form-control"
                                           placeholder="e.g., Rose Musk Eau De Parfum"
                                           value="{{ old('product_name', $cloneProduct->product_name ?? '') }}" required>
                                    <small class="field-hint">This name will be shown to customers.</small>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">
                                        Price (₹) <span class="required-star">*</span>
                                    </label>
                                    <input type="number" step="0.01" min="0" name="price" class="form-control"
                                           placeholder="e.g., 1999"
                                           value="{{ old('price', $cloneProduct->price ?? '') }}" required>
                                    <small class="field-hint">Original MRP.</small>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Offer Price (₹)</label>
                                    <input type="number" step="0.01" min="0" name="offer_price" class="form-control"
                                           placeholder="e.g., 1499"
                                           value="{{ old('offer_price', $cloneProduct->offer_price ?? '') }}">
                                    <small class="field-hint">Selling price (if on offer).</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">
                                        Product SKU <span class="required-star">*</span>
                                    </label>
                                    <input type="text" name="product_sku" class="form-control"
                                           placeholder="e.g., RMS-100ML-001"
                                           value="{{ old('product_sku', $cloneProduct->product_sku ?? '') }}" required>
                                    <small class="field-hint">
                                        Unique code for this product.
                                        @if($isClone)
                                            <span class="text-warning">Will be auto-suffixed if duplicated.</span>
                                        @endif
                                    </small>
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-3">
                                    <label class="form-label">Discount %</label>
                                    <input type="number" step="0.01" min="0" max="100" name="discount" class="form-control"
                                           placeholder="e.g., 25"
                                           value="{{ old('discount', $cloneProduct->discount ?? '') }}">
                                    <small class="field-hint">Optional. Enter a number between 0 and 100.</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">
                                        Stock Quantity <span class="required-star">*</span>
                                    </label>
                                    <input type="number" min="0" name="quantity" class="form-control"
                                           placeholder="e.g., 50"
                                           value="{{ old('quantity', $cloneProduct->quantity ?? '') }}" required>
                                    <small class="field-hint">Available units in stock.</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Estimated Delivery</label>
                                    <input type="text" name="estimate_delivery" class="form-control"
                                           placeholder="e.g., 5-7 business days"
                                           value="{{ old('estimate_delivery', $cloneProduct->estimate_delivery ?? '') }}">
                                    <small class="field-hint">Shown on product page.</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Return Policy</label>
                                    <input type="text" name="return_policy" class="form-control"
                                           placeholder="e.g., 7 days easy return"
                                           value="{{ old('return_policy', $cloneProduct->return_policy ?? '') }}">
                                    <small class="field-hint">Shown on product page.</small>
                                </div>
                            </div>
                        </div>

                        {{-- ===================== 3. PRODUCT IMAGES ===================== --}}
                        <div class="card section-card mb-4 p-3">
                            <h5 class="section-title">
                                <i class="fa fa-image"></i> 3. Product Images
                                <span class="required-star">*</span>
                            </h5>
                            <p class="section-subtitle">
                                Upload at least one product image. Only <strong>.webp</strong> format is allowed.
                                Maximum size: <strong>2 MB per image</strong>. First image is used as the main thumbnail.
                            </p>

                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-primary btn-sm" id="add-image">
                                    <i class="fa fa-plus"></i> Add Another Image
                                </button>
                            </div>

                            <table class="table table-bordered" id="images-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:55%">Upload Image <span class="required-star">*</span></th>
                                        <th style="width:25%">Preview</th>
                                        <th style="width:20%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($isClone && count($cloneImages))
                                        @foreach($cloneImages as $idx => $img)
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="cloned_images[{{ $idx }}]" value="{{ $img }}">
                                                    <input type="file" name="images[{{ $idx }}]" class="form-control image-input"
                                                           accept=".jpg,.jpeg,.png,.webp">
                                                    <small class="field-hint text-success">
                                                        <i class="fa fa-check-circle"></i>
                                                        Copied from original — upload only if you want to replace it.
                                                    </small>
                                                </td>
                                                <td>
                                                    <img src="{{ asset('signage/home/productimage/' . $img) }}"
                                                         class="image-preview img-thumbnail" style="max-width:80px;">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm remove-row">
                                                        <i class="fa fa-trash"></i> Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>
                                                <input type="file" name="images[]" class="form-control image-input"
                                                       accept=".jpg,.jpeg,.png,.webp" required>
                                                <small class="field-hint">Accepted formats: .webp (recommended), .jpg, .png</small>
                                            </td>
                                            <td><img class="image-preview img-thumbnail" style="max-width:80px;"></td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-row">
                                                    <i class="fa fa-trash"></i> Remove
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        {{-- ===================== 4. PERFUME NOTES ===================== --}}
                        <div class="card section-card mb-4 p-3">
                            <h5 class="section-title">
                                <i class="fa fa-leaf"></i> 4. Perfume Notes
                                <small class="text-muted">(Optional)</small>
                            </h5>
                            <p class="section-subtitle">
                                Add top, middle, and base notes that make up the fragrance profile.
                            </p>

                            <table class="table table-bordered" id="perfume-notes-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:50%">Perfume Notes</th>
                                        <th style="width:35%">Note Level</th>
                                        <th style="width:15%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($isClone && count($clonePerfumeNotes))
                                        @foreach($clonePerfumeNotes as $i => $detail)
                                            <tr>
                                                <td>
                                                    <select name="perfume_notes_details[{{ $i }}][note_ids][]"
                                                            class="form-control perfume-notes-select" multiple required>
                                                        @foreach($perfumeNotes as $note)
                                                            <option value="{{ $note->id }}"
                                                                {{ !empty($detail['note_ids']) && in_array($note->id, $detail['note_ids']) ? 'selected' : '' }}>
                                                                {{ $note->title }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="perfume_notes_details[{{ $i }}][level_id]"
                                                            class="form-control perfume-level-select" required>
                                                        <option value="">-- Select Note Level --</option>
                                                        @foreach($perfumeNotesLevel as $level)
                                                            <option value="{{ $level->id }}"
                                                                {{ isset($detail['level_id']) && $detail['level_id'] == $level->id ? 'selected' : '' }}>
                                                                {{ $level->title }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm remove-note">
                                                        <i class="fa fa-trash"></i> Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-primary" id="add-perfume-note">
                                    <i class="fa fa-plus"></i> Add Note
                                </button>
                            </div>
                        </div>

                        {{-- ===================== 5. ADDITIONAL FEATURES ===================== --}}
                        <div class="card section-card mb-4 p-3">
                            <h5 class="section-title">
                                <i class="fa fa-star"></i> 5. Additional Product Features
                                <small class="text-muted">(Optional)</small>
                            </h5>
                            <p class="section-subtitle">
                                Highlight key product features with an icon and short title
                                (e.g., "Long-lasting", "Cruelty-free", "Vegan").
                            </p>

                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-primary btn-sm" id="add-perfume-detail">
                                    <i class="fa fa-plus"></i> Add Feature
                                </button>
                            </div>

                            <table class="table table-bordered" id="perfume-details-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:45%">Feature Title</th>
                                        <th style="width:40%">Icon</th>
                                        <th style="width:15%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($isClone && count($clonePerfumeDetails))
                                        @foreach($clonePerfumeDetails as $i => $detail)
                                            <tr>
                                                <td>
                                                    <input type="text" name="perfume_details[{{ $i }}][title]" class="form-control"
                                                           placeholder="e.g., Long-lasting"
                                                           value="{{ $detail['title'] ?? '' }}" required>
                                                </td>
                                                <td>
                                                    @if(!empty($detail['icon']))
                                                        <img src="{{ asset('signage/home/productimage/' . $detail['icon']) }}"
                                                             class="img-thumbnail mb-2" style="max-width:80px;">
                                                        <input type="hidden" name="perfume_details[{{ $i }}][cloned_icon]" value="{{ $detail['icon'] }}">
                                                    @endif
                                                    <input type="file" name="perfume_details[{{ $i }}][icon]" class="form-control icon-input"
                                                           accept=".jpg,.jpeg,.png,.webp,.svg">
                                                    <small class="field-hint text-success">
                                                        <i class="fa fa-check-circle"></i> Copied — upload to replace.
                                                    </small>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm remove-row">
                                                        <i class="fa fa-trash"></i> Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        {{-- ===================== 6. FRAGRANCE TYPE & MEASUREMENT ===================== --}}
                        <div class="card section-card mb-4 p-3">
                            <h5 class="section-title">
                                <i class="fa fa-flask"></i> 6. Fragrance Type & Size
                            </h5>
                            <p class="section-subtitle">
                                Choose the fragrance family and product volume.
                            </p>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Fragrance Type <span class="required-star">*</span>
                                    </label>
                                    {{-- FIX: multi-select to match edit page storage format --}}
                                    <select name="fragrance_type_id[]" class="form-control select2-fragrance" multiple required>
                                        @foreach($fragranceTypes as $ft)
                                            <option value="{{ $ft->id }}"
                                                {{ in_array((string)$ft->id, $cloneFragranceIds) ? 'selected' : '' }}>
                                                {{ $ft->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="field-hint">You can select one or more fragrance types (e.g., Woody, Floral).</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Measurement Unit / Size</label>
                                    <input type="text" name="measurement_unit" class="form-control"
                                           placeholder="e.g., 100 ml"
                                           value="{{ old('measurement_unit', $cloneProduct->measurement_unit ?? '') }}">
                                    <small class="field-hint">Optional. Product volume (e.g., 30ml, 50ml, 100ml).</small>
                                </div>
                            </div>
                        </div>

                        {{-- ===================== 7. DESCRIPTION / BENEFITS / HOW TO USE ===================== --}}
                        <div class="card section-card mb-4 p-3">
                            <h5 class="section-title">
                                <i class="fa fa-file-alt"></i> 7. Product Description
                            </h5>
                            <p class="section-subtitle">
                                Give customers a clear understanding of the product, its benefits, and how to use it.
                            </p>

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3"
                                              id="description" placeholder="Write a short, engaging description of the product...">{{ old('description', $cloneProduct->description ?? '') }}</textarea>
                                    <small class="field-hint">Main product description shown on the product page.</small>
                                </div>
                                <div class="col-md-12 mt-5">
                                    <label class="form-label">Key Benefits</label>
                                    <textarea name="key_benefits" id="editor" class="form-control" rows="3"
                                              placeholder="List the key benefits, one per line...">{{ old('key_benefits', $cloneProduct->key_benefits ?? '') }}</textarea>
                                    <small class="field-hint">Highlight what makes this product special.</small>
                                </div>
                                <div class="col-md-12 mt-5">
                                    <label class="form-label">How to Use</label>
                                    <textarea name="how_to_use" id="how_to_use" class="form-control" rows="3"
                                              placeholder="Explain how customers should use the product...">{{ old('how_to_use', $cloneProduct->how_to_use ?? '') }}</textarea>
                                    <small class="field-hint">Step-by-step usage instructions.</small>
                                </div>
                            </div>
                        </div>

                        {{-- ===================== 8. FAQs ===================== --}}
                        <div class="card section-card mb-4 p-3">
                            <h5 class="section-title">
                                <i class="fa fa-question-circle"></i> 8. Frequently Asked Questions
                            </h5>
                            <p class="section-subtitle">
                                Add common questions and answers to help customers.
                            </p>

                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-primary btn-sm" id="add-faq">
                                    <i class="fa fa-plus"></i> Add FAQ
                                </button>
                            </div>

                            <table class="table table-bordered" id="faqs-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:40%">Question <span class="required-star">*</span></th>
                                        <th style="width:45%">Answer <span class="required-star">*</span></th>
                                        <th style="width:15%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($isClone && count($cloneFaqs))
                                        @foreach($cloneFaqs as $i => $faq)
                                            <tr>
                                                <td>
                                                    <input type="text" name="faqs[{{ $i }}][question]" class="form-control"
                                                           placeholder="e.g., Is this product long-lasting?"
                                                           value="{{ $faq['question'] ?? '' }}" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="faqs[{{ $i }}][answer]" class="form-control"
                                                           placeholder="Write a clear, concise answer..."
                                                           value="{{ $faq['answer'] ?? '' }}" required>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm remove-row">
                                                        <i class="fa fa-trash"></i> Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>
                                                <input type="text" name="faqs[0][question]" class="form-control"
                                                       placeholder="e.g., Is this product long-lasting?" required>
                                            </td>
                                            <td>
                                                <input type="text" name="faqs[0][answer]" class="form-control"
                                                       placeholder="Write a clear, concise answer..." required>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-row">
                                                    <i class="fa fa-trash"></i> Remove
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        {{-- ===================== 9. Other Information ===================== --}}
                        <div class="card section-card mb-4 p-3">
                            <h5 class="section-title">
                                <i class="fa fa-info-circle"></i> 9. Other Information
                            </h5>
                            <p class="section-subtitle">
                                Marketed By / Manufactured By / Country of Origin, etc. Shown after the FAQs on the product page.
                            </p>
                            <textarea name="other_information" id="other_information" class="form-control" rows="4"
                                      placeholder="e.g. Marketed By: ...&#10;Manufactured By: ...&#10;Country of Origin: India">{{ old('other_information', $cloneProduct->other_information ?? '') }}</textarea>
                        </div>

                        {{-- ===================== SUBMIT ===================== --}}
                        <div class="d-flex justify-content-end align-items-center mb-5 p-3 bg-light rounded">
                            <small class="text-muted me-3">
                                <span class="required-star">*</span> Please ensure all required fields are filled before saving.
                            </small>
                            <a href="{{ route('products-details.index') }}" class="btn btn-secondary me-2">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                {{ $isClone ? 'Save as New Product' : 'Save Product' }}
                            </button>
                        </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@include('components.backend.footer')
@include('components.backend.main-js')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script>
    ClassicEditor.create(document.querySelector('#description'), {
        toolbar: [
            'heading',
            '|',
            'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript',
            'link', 'blockQuote', 'codeBlock',
            'bulletedList', 'numberedList', 'todoList',
            '|',
            'alignment', 'outdent', 'indent',
            '|',
            'fontColor', 'fontBackgroundColor', 'fontSize', 'fontFamily',
            '|',
            'insertTable', 'imageUpload', 'mediaEmbed', 'horizontalLine', 'pageBreak',
            '|',
            'undo', 'redo', 'removeFormat', 'highlight', 'specialCharacters'
        ],
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
            ]
        },
        fontFamily: {
            options: [
                'default', 'Arial, Helvetica, sans-serif', 'Courier New, Courier, monospace',
                'Georgia, serif', 'Lucida Sans Unicode, Lucida Grande, sans-serif',
                'Tahoma, Geneva, sans-serif', 'Times New Roman, Times, serif',
                'Trebuchet MS, Helvetica, sans-serif', 'Verdana, Geneva, sans-serif'
            ]
        },
        fontSize: {
            options: [ 'tiny', 'small', 'default', 'big', 'huge' ]
        },
        alignment: {
            options: [ 'left', 'center', 'right', 'justify' ]
        }
    })
    .catch(error => { console.error(error); });
    
    
    ClassicEditor.create(document.querySelector('#how_to_use'), {
        toolbar: [
            'heading',
            '|',
            'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript',
            'link', 'blockQuote', 'codeBlock',
            'bulletedList', 'numberedList', 'todoList',
            '|',
            'alignment', 'outdent', 'indent',
            '|',
            'fontColor', 'fontBackgroundColor', 'fontSize', 'fontFamily',
            '|',
            'insertTable', 'imageUpload', 'mediaEmbed', 'horizontalLine', 'pageBreak',
            '|',
            'undo', 'redo', 'removeFormat', 'highlight', 'specialCharacters'
        ],
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
            ]
        },
        fontFamily: {
            options: [
                'default', 'Arial, Helvetica, sans-serif', 'Courier New, Courier, monospace',
                'Georgia, serif', 'Lucida Sans Unicode, Lucida Grande, sans-serif',
                'Tahoma, Geneva, sans-serif', 'Times New Roman, Times, serif',
                'Trebuchet MS, Helvetica, sans-serif', 'Verdana, Geneva, sans-serif'
            ]
        },
        fontSize: {
            options: [ 'tiny', 'small', 'default', 'big', 'huge' ]
        },
        alignment: {
            options: [ 'left', 'center', 'right', 'justify' ]
        }
    })
    .catch(error => { console.error(error); });

    ClassicEditor.create(document.querySelector('#other_information'), {
        toolbar: [
            'heading', '|', 'bold', 'italic', 'underline', 'link',
            'bulletedList', 'numberedList', '|', 'undo', 'redo', 'removeFormat'
        ]
    })
    .catch(error => { console.error(error); });

</script>

<script>
let faqIndex           = {{ $isClone ? count($cloneFaqs) : 1 }};
let perfumeDetailIndex = {{ $isClone ? count($clonePerfumeDetails) : 0 }};

// Add FAQ row
document.getElementById('add-faq').addEventListener('click', function () {
    const tbody = document.querySelector('#faqs-table tbody');
    tbody.insertAdjacentHTML('beforeend', `
        <tr>
            <td>
                <input type="text" name="faqs[${faqIndex}][question]" class="form-control"
                       placeholder="e.g., Is this product long-lasting?" required>
            </td>
            <td>
                <input type="text" name="faqs[${faqIndex}][answer]" class="form-control"
                       placeholder="Write a clear, concise answer..." required>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">
                    <i class="fa fa-trash"></i> Remove
                </button>
            </td>
        </tr>`);
    faqIndex++;
});

// Add Image row
document.getElementById('add-image').addEventListener('click', function () {
    const tbody = document.querySelector('#images-table tbody');
    tbody.insertAdjacentHTML('beforeend', `
        <tr>
            <td>
                <input type="file" name="images[]" class="form-control image-input"
                       accept=".jpg,.jpeg,.png,.webp" required>
                <small class="field-hint">Accepted: .webp (recommended), .jpg, .png — max 2 MB.</small>
            </td>
            <td><img class="image-preview img-thumbnail" style="max-width:80px;"></td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">
                    <i class="fa fa-trash"></i> Remove
                </button>
            </td>
        </tr>`);
});

// Add Perfume Detail row
document.getElementById('add-perfume-detail').addEventListener('click', function () {
    const tbody = document.querySelector('#perfume-details-table tbody');
    tbody.insertAdjacentHTML('beforeend', `
        <tr>
            <td>
                <input type="text" name="perfume_details[${perfumeDetailIndex}][title]" class="form-control"
                       placeholder="e.g., Long-lasting" required>
            </td>
            <td>
                <input type="file" name="perfume_details[${perfumeDetailIndex}][icon]" class="form-control icon-input"
                       accept=".jpg,.jpeg,.png,.webp,.svg">
                <img class="image-preview img-thumbnail mt-2" style="max-width:80px;display:none;">
                <small class="field-hint">Upload an icon (SVG recommended).</small>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">
                    <i class="fa fa-trash"></i> Remove
                </button>
            </td>
        </tr>`);
    perfumeDetailIndex++;
});

// Remove any row
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.remove-row');
    if (btn) {
        btn.closest('tr').remove();
    }
});

// Image preview on change
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('image-input') || e.target.classList.contains('icon-input')) {
        const file    = e.target.files[0];
        const row     = e.target.closest('tr');
        const preview = row.querySelector('.image-preview');
        if (file && preview) {
            const reader = new FileReader();
            reader.onload = ev => {
                preview.src = ev.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }
});
</script>

<script>
$(document).ready(function () {
    $('.select2-copy-from').select2({
        placeholder: '-- Search & select a product --',
        allowClear:  true,
        width:       '100%'
    });

    $('.select2-category').select2({    placeholder: '-- Select Category --',       allowClear: true, width: '100%' });
    $('.select2-subcategory').select2({ placeholder: '-- Select Sub Category --',   allowClear: true, width: '100%' });
    $('.select2-fragrance').select2({   placeholder: '-- Select Fragrance Type --', allowClear: true, width: '100%' });

    // Init Select2 on pre-filled perfume-note rows
    $('#perfume-notes-table tbody tr').each(function () {
        $(this).find('.perfume-notes-select').select2({ placeholder: '-- Select Perfume Notes --', allowClear: true, width: '100%' });
        $(this).find('.perfume-level-select').select2({ placeholder: '-- Select Note Level --',    allowClear: true, width: '100%' });
    });

    let noteIndex = $('#perfume-notes-table tbody tr').length;

    $('#add-perfume-note').on('click', function () {
        const tableBody = $('#perfume-notes-table tbody');
        const row = `
            <tr>
                <td>
                    <select name="perfume_notes_details[${noteIndex}][note_ids][]" class="form-control perfume-notes-select" multiple required>
                        @foreach($perfumeNotes as $note)
                            <option value="{{ $note->id }}">{{ $note->title }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="perfume_notes_details[${noteIndex}][level_id]" class="form-control perfume-level-select" required>
                        <option value="">-- Select Note Level --</option>
                        @foreach($perfumeNotesLevel as $level)
                            <option value="{{ $level->id }}">{{ $level->title }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-note">
                        <i class="fa fa-trash"></i> Remove
                    </button>
                </td>
            </tr>`;
        tableBody.append(row);
        tableBody.find('.perfume-notes-select').last().select2({ placeholder: '-- Select Perfume Notes --', allowClear: true, width: '100%' });
        tableBody.find('.perfume-level-select').last().select2({ placeholder: '-- Select Note Level --',    allowClear: true, width: '100%' });
        noteIndex++;
    });

    $(document).on('click', '.remove-note', function () {
        $(this).closest('tr').remove();
    });

    /* COPY-FROM-EXISTING-PRODUCT → reload page with ?clone=ID */
    const baseCreateUrl = "{{ route('products-details.create') }}";
    const $copySelect   = $('#copy-from-product');
    const $applyBtn     = $('#btn-apply-copy');

    $copySelect.on('change', function () {
        if ($applyBtn.length) {
            $applyBtn.prop('disabled', !$(this).val());
        }
    });

    $applyBtn.on('click', function () {
        const productId = $copySelect.val();
        if (!productId) return;

        const hasInput = $('input[name="product_name"]').val().trim() !== '';
        if (hasInput && !confirm('This will discard any data you have entered and load the selected product. Continue?')) {
            return;
        }

        window.location.href = baseCreateUrl + '?clone=' + productId;
    });
});
</script>

</body>
</html>