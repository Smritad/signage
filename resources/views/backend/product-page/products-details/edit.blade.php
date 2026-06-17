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

    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
@include('components.backend.header')
@include('components.backend.sidebar')

@php
    // Decode JSON fields safely
    $decoded            = json_decode($product->sub_category_id, true);
    $selectedSubCats    = is_array($decoded) ? $decoded : (!empty($product->sub_category_id) ? [$product->sub_category_id] : []);
    $images             = $product->images ? json_decode($product->images, true) : [];
    $existingNotes      = $product->perfume_notes ? json_decode($product->perfume_notes, true) : [];
    $perfumeDetails     = $product->perfume_details ? json_decode($product->perfume_details, true) : [];
    $faqs               = $product->faqs ? json_decode($product->faqs, true) : [];
    $selectedFragrances = (array) json_decode($product->fragrance_type_id, true);
@endphp

<div class="page-body">
    <div class="container-fluid">

        {{-- ===================== PAGE TITLE ===================== --}}
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-6">
                    <h4>
                        <i class="text-primary"></i>
                        Edit Product — {{ $product->product_name }}
                    </h4>
                    <p class="text-muted mb-0">
                        Update the product details below. Fields marked with
                        <span class="required-star">*</span> are required.
                    </p>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb float-end">
                        <li class="breadcrumb-item"><a href="{{ route('products-details.index') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('products-details.index') }}">Products</a></li>
                        <li class="breadcrumb-item active">Edit Product</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1"><i class="fa fa-info-circle text-primary"></i> Update Product</h5>
                            <small class="text-muted">Modify product details below and click Update to save changes.</small>
                        </div>
                        <div class="required-legend">
                            <span class="required-star">*</span> Required field
                        </div>
                    </div>
                    <div class="card-body">

                        <form action="{{ route('products-details.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

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
                                                    {{ $product->category_id == $cat->id ? 'selected' : '' }}>
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
                                        <select name="sub_category_id[]" class="form-control sub_category" multiple required>
                                            @foreach($subCategories as $sub)
                                                <option value="{{ $sub->id }}"
                                                    {{ in_array($sub->id, $selectedSubCats) ? 'selected' : '' }}>
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
                                    Edit the product name, pricing, and stock details.
                                </p>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">
                                            Product Name <span class="required-star">*</span>
                                        </label>
                                        <input type="text" name="product_name" class="form-control"
                                               placeholder="e.g., Rose Musk Eau De Parfum"
                                               value="{{ $product->product_name }}" required>
                                        <small class="field-hint">This name will be shown to customers.</small>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">
                                            Price (₹) <span class="required-star">*</span>
                                        </label>
                                        <input type="number" step="0.01" min="0" name="price" id="price" class="form-control"
                                               placeholder="e.g., 1999"
                                               value="{{ $product->price }}" required>
                                        <small class="field-hint">Original MRP.</small>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Offer Price (₹)</label>
                                        <input type="number" step="0.01" min="0" name="offer_price" id="offer_price" class="form-control"
                                               placeholder="e.g., 1499"
                                               value="{{ $product->offer_price ?? '' }}">
                                        <small class="field-hint">Selling price (if on offer).</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">
                                            Product SKU <span class="required-star">*</span>
                                        </label>
                                        <input type="text" name="product_sku" class="form-control"
                                               placeholder="e.g., RMS-100ML-001"
                                               value="{{ $product->product_sku }}" required>
                                        <small class="field-hint">Unique code for this product.</small>
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <div class="col-md-3">
                                        <label class="form-label">Discount %</label>
                                        <input type="number" step="0.01" min="0" max="100" name="discount" id="discount" class="form-control"
                                               placeholder="e.g., 25"
                                               value="{{ $product->discount }}">
                                        <small class="field-hint">Optional. Between 0 and 100.</small>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">
                                            Stock Quantity <span class="required-star">*</span>
                                        </label>
                                        <input type="number" min="0" name="quantity" class="form-control"
                                               placeholder="e.g., 50"
                                               value="{{ $product->quantity }}" required>
                                        <small class="field-hint">Available units in stock.</small>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Estimated Delivery</label>
                                        <input type="text" name="estimate_delivery" class="form-control"
                                               placeholder="e.g., 5-7 business days"
                                               value="{{ $product->estimate_delivery }}">
                                        <small class="field-hint">Shown on product page.</small>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Return Policy</label>
                                        <input type="text" name="return_policy" class="form-control"
                                               placeholder="e.g., 7 days easy return"
                                               value="{{ $product->return_policy }}">
                                        <small class="field-hint">Shown on product page.</small>
                                    </div>
                                </div>
                            </div>

                            {{-- ===================== 3. PRODUCT IMAGES ===================== --}}
                            <div class="card section-card mb-4 p-3">
                                <h5 class="section-title">
                                    <i class="fa fa-image"></i> 3. Product Images
                                </h5>
                                <p class="section-subtitle">
                                    Upload new images or replace existing ones. Only <strong>.webp</strong> format is allowed.
                                    Maximum size: <strong>2 MB per image</strong>. First image is the main thumbnail.
                                </p>

                                <div class="d-flex justify-content-end mb-2">
                                    <button type="button" class="btn btn-primary btn-sm" id="add-image">
                                        <i class="fa fa-plus"></i> Add More Images
                                    </button>
                                </div>

                                <table class="table table-bordered" id="images-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:55%">Image</th>
                                            <th style="width:25%">Preview</th>
                                            <th style="width:20%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($images as $i => $img)
                                            <tr>
                                                <td>
                                                    <input type="file" name="images[{{ $i }}]" class="form-control image-input" accept=".webp">
                                                    <input type="hidden" name="old_images[{{ $i }}]" value="{{ $img }}">
                                                    <small class="field-hint">
                                                        Leave empty to keep the existing image. Upload a new file to replace it.
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
                                    Edit top, middle, and base notes that make up the fragrance profile.
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
                                        @foreach($existingNotes as $i => $detail)
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
                                    </tbody>
                                </table>

                                <button type="button" class="btn btn-sm btn-primary" id="add-perfume-note">
                                    <i class="fa fa-plus"></i> Add Note
                                </button>
                            </div>

                            {{-- ===================== 5. ADDITIONAL FEATURES ===================== --}}
                            <div class="card section-card mb-4 p-3">
                                <h5 class="section-title">
                                    <i class="fa fa-star"></i> 5. Additional Product Features
                                    <small class="text-muted">(Optional)</small>
                                </h5>
                                <p class="section-subtitle">
                                    Highlight key features with an icon and short title
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
                                        @foreach($perfumeDetails as $i => $detail)
                                            <tr>
                                                <td>
                                                    <input type="text" name="perfume_details[{{ $i }}][title]" class="form-control"
                                                           placeholder="e.g., Long-lasting"
                                                           value="{{ $detail['title'] ?? '' }}" required>
                                                </td>
                                                <td>
                                                    @if(!empty($detail['icon']))
                                                        <img src="{{ asset('signage/home/productimage/'.$detail['icon']) }}"
                                                             class="img-thumbnail mb-2" style="max-width:80px;">
                                                        <input type="hidden" name="perfume_details[{{ $i }}][old_icon]" value="{{ $detail['icon'] }}">
                                                    @endif
                                                    <input type="file" name="perfume_details[{{ $i }}][icon]" class="form-control icon-input"
                                                           accept=".jpg,.jpeg,.png,.webp,.svg">
                                                    <small class="field-hint">Upload a new icon to replace. SVG recommended.</small>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm remove-row">
                                                        <i class="fa fa-trash"></i> Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
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
                                        <select name="fragrance_type_id[]" class="form-control fragrance-select" multiple required>
                                            @foreach($fragranceTypes as $ft)
                                                <option value="{{ $ft->id }}"
                                                    @if(in_array($ft->id, $selectedFragrances)) selected @endif>
                                                    {{ $ft->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="field-hint">e.g., Woody, Floral, Oriental.</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Measurement Unit / Size</label>
                                        <input type="text" name="measurement_unit" class="form-control"
                                               placeholder="e.g., 100 ml"
                                               value="{{ $product->measurement_unit ?? old('measurement_unit') }}">
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
                                    Update the description, benefits, and usage instructions shown to customers.
                                </p>

                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control" rows="3"
                                                  id="description" placeholder="Write a short, engaging description of the product...">{{ $product->description }}</textarea>
                                        <small class="field-hint">Main product description shown on the product page.</small>
                                    </div>
                                    <div class="col-md-12 mt-5">
                                        <label class="form-label">Key Benefits</label>
                                        <textarea name="key_benefits" id="editor" class="form-control" rows="3"
                                                  placeholder="List the key benefits, one per line...">{{ $product->key_benefits }}</textarea>
                                        <small class="field-hint">Highlight what makes this product special.</small>
                                    </div>
                                    <div class="col-md-12 mt-5">
                                        <label class="form-label">How to Use</label>
                                        <textarea name="how_to_use" id="how_to_use" class="form-control" rows="3"
                                                  placeholder="Explain how customers should use the product...">{{ $product->how_to_use }}</textarea>
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
                                    Edit common questions and answers to help customers.
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
                                        @foreach($faqs as $i => $faq)
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
                                          placeholder="e.g. Marketed By: ...&#10;Manufactured By: ...&#10;Country of Origin: India">{{ old('other_information', $product->other_information) }}</textarea>
                            </div>

                            {{-- ===================== SUBMIT ===================== --}}
                            <div class="d-flex justify-content-end align-items-center mb-5 p-3 bg-light rounded">
                                <small class="text-muted me-3">
                                    <span class="required-star">*</span> Please ensure all required fields are filled before updating.
                                </small>
                                <a href="{{ route('products-details.index') }}" class="btn btn-secondary me-2">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Update Product
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


{{-- ============================================================
     Perfume Notes — Select2 init + Add/Remove rows
============================================================ --}}
<script>
    $(document).ready(function () {
    function initSelect2(row) {
        row.find('.perfume-notes-select').select2({
            placeholder: "-- Select Perfume Notes --",
            allowClear:  true,
            width:       '100%'
        });
        row.find('.perfume-level-select').select2({
            placeholder: "-- Select Note Level --",
            allowClear:  true,
            width:       '100%'
        });
    }

    $('#perfume-notes-table tbody tr').each(function () {
        initSelect2($(this));
    });

    let noteIndex = $('#perfume-notes-table tbody tr').length;

    const notesOptions  = @json($perfumeNotes->map(fn($n) => ['id' => $n->id, 'title' => $n->title]));
    const levelsOptions = @json($perfumeNotesLevel->map(fn($l) => ['id' => $l->id, 'title' => $l->title]));

    function buildOptions(options, selected = []) {
        return options.map(o =>
            `<option value="${o.id}" ${selected.includes(o.id.toString()) ? 'selected' : ''}>${o.title}</option>`
        ).join('');
    }

    $('#add-perfume-note').on('click', function () {
        const row = $(`
            <tr>
                <td>
                    <select name="perfume_notes_details[${noteIndex}][note_ids][]" class="form-control perfume-notes-select" multiple required>
                        ${buildOptions(notesOptions)}
                    </select>
                </td>
                <td>
                    <select name="perfume_notes_details[${noteIndex}][level_id]" class="form-control perfume-level-select" required>
                        <option value="">-- Select Note Level --</option>
                        ${buildOptions(levelsOptions)}
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-note">
                        <i class="fa fa-trash"></i> Remove
                    </button>
                </td>
            </tr>
        `);
        $('#perfume-notes-table tbody').append(row);
        initSelect2(row);
        noteIndex++;
    });

    $(document).on('click', '.remove-note', function () {
        $(this).closest('tr').remove();
    });
});
</script>

{{-- ============================================================
     Select2 — Fragrance, Sub-category, Category
============================================================ --}}
<script>
$(document).ready(function () {
    $('.fragrance-select').select2({
        placeholder: "-- Select Fragrance Type --",
        allowClear:  true,
        width:       '100%'
    });

    $('.sub_category').select2({
        placeholder: "-- Select Sub Category --",
        allowClear:  true,
        width:       '100%'
    });

    $('.select2-category').select2({
        placeholder: "-- Select Category --",
        allowClear:  true,
        width:       '100%'
    });
});
</script>

{{-- ============================================================
     FAQ / Image / Feature — dynamic row handlers
============================================================ --}}
<script>
let faqIndex           = {{ count($faqs) > 0 ? count($faqs) : 1 }};
let perfumeDetailIndex = {{ count($perfumeDetails) > 0 ? count($perfumeDetails) : 0 }};

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
                <input type="file" name="images[]" class="form-control image-input" accept=".jpg,.jpeg,.png,.webp,.svg">
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
                <div class="preview-wrapper mt-2" style="max-width:80px;">
                    <img class="image-preview img-thumbnail" style="display:none;max-width:80px;">
                    <div class="svg-preview"></div>
                </div>
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

// Remove a row (marks existing image as deleted if present)
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.remove-row');
    if (!btn) return;

    const row = btn.closest('tr');

    // Mark existing image for deletion so controller can unlink it
    const oldInput = row.querySelector('input[name^="old_images"]');
    if (oldInput) {
        const match = oldInput.name.match(/old_images\[(\d+)\]/);
        if (match) {
            oldInput.name = `deleted_images[${match[1]}]`;
        } else {
            oldInput.name = 'deleted_images[]';
        }
    }

    row.remove();
});

// Image / icon preview (with SVG support)
document.addEventListener('change', function (e) {
    if (!(e.target.classList.contains('image-input') || e.target.classList.contains('icon-input'))) return;

    const file       = e.target.files[0];
    const row        = e.target.closest('tr');
    const imgPreview = row.querySelector('.image-preview');
    const svgPreview = row.querySelector('.svg-preview');

    if (!file) {
        if (imgPreview) { imgPreview.src = ''; imgPreview.style.display = 'none'; }
        if (svgPreview) svgPreview.innerHTML = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function (ev) {
        if (imgPreview) { imgPreview.style.display = 'none'; imgPreview.src = ''; }
        if (svgPreview) svgPreview.innerHTML = '';

        if (file.type === 'image/svg+xml' && svgPreview) {
            svgPreview.innerHTML = ev.target.result;
        } else if (imgPreview) {
            imgPreview.src = ev.target.result;
            imgPreview.style.display = 'block';
        }
    };
    reader.readAsDataURL(file);
});
</script>

<script>
/* Auto-calculate Discount % from Price and Offer Price */
(function () {
    var priceEl = document.getElementById('price');
    var offerEl = document.getElementById('offer_price');
    var discEl  = document.getElementById('discount');
    if (!priceEl || !offerEl || !discEl) return;

    function recalcDiscount() {
        var price = parseFloat(priceEl.value);
        var offer = parseFloat(offerEl.value);

        if (offerEl.value === '' || isNaN(offer)) {
            return; // no offer price -> leave discount as typed
        }
        if (!isNaN(price) && price > 0 && offer > 0 && offer < price) {
            discEl.value = Math.round((price - offer) / price * 100);
        } else {
            discEl.value = 0; // offer >= price (or invalid) -> no discount
        }
    }

    priceEl.addEventListener('input', recalcDiscount);
    offerEl.addEventListener('input', recalcDiscount);
})();
</script>

</body>
</html>