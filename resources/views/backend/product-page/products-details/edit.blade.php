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
    </style>
</head>
<body>
@include('components.backend.header')
@include('components.backend.sidebar')

<div class="page-body">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Edit Product Details</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('products-details.index') }}">Home</a></li>
                        <li class="breadcrumb-item active">Edit Product</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5>Update Product</h5>
                        <p class="text-muted">Modify product details below.</p>
                    </div>
                    <div class="card-body">
                    <form action="{{ route('products-details.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Category & Subcategory -->
                            <div class="card mb-4 p-3">
                                <h5 class="mb-3">Category & Subcategory</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Category</label>
                                        <select name="category_id" class="form-control" required>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                                    {{ $cat->category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Sub Category</label>
                                        <select name="sub_category_id" class="form-control" required>
                                            @foreach($subCategories as $sub)
                                                <option value="{{ $sub->id }}" {{ $product->sub_category_id == $sub->id ? 'selected' : '' }}>
                                                    {{ $sub->sab_category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Info -->
                            <div class="card mb-4 p-3">
                                <h5 class="mb-3">Product Info</h5>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Product Name</label>
                                        <input type="text" name="product_name" value="{{ $product->product_name }}" class="form-control" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Price</label>
                                        <input type="number" step="0.01" name="price" value="{{ $product->price }}" class="form-control" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Offer Price</label>
                                        <input type="number" step="0.01" name="offer_price" value="{{ $product->offer_price ?? '' }}" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Product SKU</label>
                                        <input type="text" name="product_sku" value="{{ $product->product_sku }}" class="form-control" required>
                                    </div>
                                </div>

                                <div class="row g-3 mt-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Discount (%)</label>
                                        <input type="number" step="0.01" name="discount" value="{{ $product->discount }}" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" name="quantity" value="{{ $product->quantity }}" class="form-control" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Estimate Delivery</label>
                                        <input type="text" name="estimate_delivery" value="{{ $product->estimate_delivery }}" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Return Policy</label>
                                        <input type="text" name="return_policy" value="{{ $product->return_policy }}" class="form-control">
                                    </div>
                                </div>
                            </div>


                            <!-- Product Images -->
                            <div class="card mb-4 p-3">
                                <h5 class="mb-3">Product Images</h5>
                              <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary btn-sm" id="add-image">
                                        Add Images
                                    </button>
                                </div>    
                                <br>
                                <table class="table table-bordered" id="images-table">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Preview</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $images = $product->images ? json_decode($product->images, true) : [];
                                        @endphp
                                        @foreach($images as $i => $img)
                                        <tr>
                                            <td>
                                                <input type="file" name="images[{{ $i }}]" class="form-control image-input" accept=".jpg,.jpeg,.png,.webp">
                                                <input type="hidden" name="old_images[{{ $i }}]" value="{{ $img }}">
                                            </td>
                                            <td>
                                                <img src="{{ asset('signage/home/productimage/' . $img) }}" class="img-thumbnail" style="max-width: 80px;">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger remove-row">Remove</button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                         <!-- Perfume Notes & Levels -->
                        <div class="card mb-4 p-3">
                            <h5 class="mb-3">Perfume Notes</h5>

                            <table class="table table-bordered" id="perfume-notes-table">
                                <thead>
                                    <tr>
                                        <th>Perfume Notes</th>
                                        <th>Perfume Note Level</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $existingNotes = $product->perfume_notes ? json_decode($product->perfume_notes, true) : [];
                                    @endphp

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
                                                    <option value="">Select Note Level</option>
                                                    @foreach($perfumeNotesLevel as $level)
                                                        <option value="{{ $level->id }}"
                                                            {{ isset($detail['level_id']) && $detail['level_id'] == $level->id ? 'selected' : '' }}>
                                                            {{ $level->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-note">Remove</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <button type="button" class="btn btn-sm btn-primary" id="add-perfume-note">Add Note</button>
                        </div>




                            <!-- Additional Product Features -->
                            <div class="card mb-4 p-3">
                                <h5 class="mb-3">Additional Product Features</h5>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary btn-sm" id="add-perfume-detail">
                                        Add More
                                    </button>
                                </div>    
                                <br>
                                <table class="table table-bordered" id="perfume-details-table">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Icon</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $perfumeDetails = $product->perfume_details ? json_decode($product->perfume_details, true) : [];
                                        @endphp
                                        @foreach($perfumeDetails as $i => $detail)
                                            <tr>
                                                <td>
                                                    <input type="text" name="perfume_details[{{ $i }}][title]" class="form-control" value="{{ $detail['title'] ?? '' }}" required>
                                                </td>
                                                <td>
                                                    @if(!empty($detail['icon']))
                                                        <img src="{{ asset('signage/home/productimage/'.$detail['icon']) }}" class="img-thumbnail mb-2" style="max-width:80px;">
                                                        <input type="hidden" name="perfume_details[{{ $loop->index }}][old_icon]" value="{{ $detail['icon'] }}">
                                                    @endif
                                                    <input type="file" name="perfume_details[{{ $loop->index }}][icon]" class="form-control icon-input" accept=".jpg,.jpeg,.png,.webp">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger remove-row">Remove</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                           <!-- Fragrance Type & Measurement Unit -->
                                        <div class="card mb-4 p-3">
                                            <h5 class="mb-3">Fragrance Type</h5>
                                            <div class="row g-3">
                                                <!-- Fragrance Type Select -->
                                                <div class="col-md-6">
                                                    <label for="fragrance_type_id" class="form-label">Fragrance Type <span class="text-danger">*</span></label>
                                                    <select name="fragrance_type_id" id="fragrance_type_id" class="form-control" required>
                                                        <option value="">Select Fragrance Type</option>
                                                        @foreach($fragranceTypes as $ft)
                                                            <option value="{{ $ft->id }}" 
                                                                {{ isset($product) && $product->fragrance_type_id == $ft->id ? 'selected' : '' }}>
                                                                {{ $ft->title }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <!-- Measurement Unit Input -->
                                                <div class="col-md-6">
                                                    <label for="measurement_unit" class="form-label">Measurement Unit <span class="text-danger">*</span></label>
                                                    <input type="text" name="measurement_unit" id="measurement_unit"
                                                        class="form-control" placeholder="e.g., 100 ml"
                                                        value="{{ $product->measurement_unit ?? old('measurement_unit') }}" required>
                                                </div>
                                            </div>
                                        </div>


                            <!-- Description, Key Benefits & How to Use -->
                            <div class="card mb-4 p-3">
                                <h5 class="mb-3">Product Description</h5>
                                   
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control" rows="3">{{ $product->description }}</textarea>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <label class="form-label">Key Benefits</label>
                                        <textarea name="key_benefits" class="form-control" rows="3">{{ $product->key_benefits }}</textarea>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <label class="form-label">How to Use</label>
                                        <textarea name="how_to_use" class="form-control" rows="3">{{ $product->how_to_use }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- FAQs -->
                            <div class="card mb-4 p-3">
                                <h5 class="mb-3">FAQs</h5>
                                 <button type="button" class="btn btn-primary btn-sm" id="add-faq">
                                        Add FAQs
                                    </button>
                                </div>    
                                <br>
                                <table class="table table-bordered" id="faqs-table">
                                    <thead>
                                        <tr>
                                            <th>Question</th>
                                            <th>Answer</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $faqs = $product->faqs ? json_decode($product->faqs, true) : [];
                                        @endphp
                                        @foreach($faqs as $i => $faq)
                                            <tr>
                                                <td><input type="text" name="faqs[{{ $i }}][question]" class="form-control" value="{{ $faq['question'] ?? '' }}" required></td>
                                                <td><input type="text" name="faqs[{{ $i }}][answer]" class="form-control" value="{{ $faq['answer'] ?? '' }}" required></td>
                                                <td><button type="button" class="btn btn-danger remove-row">Remove</button></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Submit -->
                            <div class="d-flex justify-content-end mb-5">
                                <button type="submit" class="btn btn-success me-2">Update</button>
                                <a href="{{ route('products-details.index') }}" class="btn btn-secondary">Cancel</a>
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

<script>
$(document).ready(function () {
    function initSelect2(row) {
        row.find('.perfume-notes-select').select2({
            placeholder: "-- Select Perfume Notes --",
            allowClear: true,
            width: '100%'
        });
        row.find('.perfume-level-select').select2({
            placeholder: "-- Select Note Level --",
            allowClear: true,
            width: '100%'
        });
    }

    // Initialize existing rows
    $('#perfume-notes-table tbody tr').each(function() {
        initSelect2($(this));
    });

    let noteIndex = $('#perfume-notes-table tbody tr').length;

    // Pre-generate options for JS
    let notesOptions = @json($perfumeNotes->map(fn($n) => ['id'=>$n->id, 'title'=>$n->title]));
    let levelsOptions = @json($perfumeNotesLevel->map(fn($l) => ['id'=>$l->id, 'title'=>$l->title]));

    function buildOptions(options, selected=[]) {
        return options.map(o => `<option value="${o.id}" ${selected.includes(o.id.toString()) ? 'selected' : ''}>${o.title}</option>`).join('');
    }

    // Add new row
    $('#add-perfume-note').click(function () {
        let row = $(`
            <tr>
                <td>
                    <select name="perfume_notes_details[${noteIndex}][note_ids][]" class="form-control perfume-notes-select" multiple required>
                        ${buildOptions(notesOptions)}
                    </select>
                </td>
                <td>
                    <select name="perfume_notes_details[${noteIndex}][level_id]" class="form-control perfume-level-select" required>
                        <option value="">Select Note Level</option>
                        ${buildOptions(levelsOptions)}
                    </select>
                </td>
                <td><button type="button" class="btn btn-danger btn-sm remove-note">Remove</button></td>
            </tr>
        `);
        $('#perfume-notes-table tbody').append(row);
        initSelect2(row);
        noteIndex++;
    });

    // Remove row
    $(document).on('click', '.remove-note', function () {
        $(this).closest('tr').remove();
    });
});
</script>




<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
let faqIndex = {{ isset($faqs) ? count($faqs) : 1 }};
let perfumeDetailIndex = {{ isset($perfumeDetails) ? count($perfumeDetails) : 1 }};

// Add FAQ row
document.getElementById('add-faq').addEventListener('click', function(){
    const tbody = document.querySelector('#faqs-table tbody');
    tbody.insertAdjacentHTML('beforeend', `<tr>
        <td><input type="text" name="faqs[${faqIndex}][question]" class="form-control" placeholder="Enter question" required></td>
        <td><input type="text" name="faqs[${faqIndex}][answer]" class="form-control" placeholder="Enter answer" required></td>
        <td><button type="button" class="btn btn-danger remove-row">Remove</button></td>
    </tr>`);
    faqIndex++;
});

// Add Image row

document.getElementById('add-image').addEventListener('click', function(){
    const tbody = document.querySelector('#images-table tbody');
    tbody.insertAdjacentHTML('beforeend', `<tr>
        <td>
            <input type="file" name="images[]" class="form-control image-input" accept=".jpg,.jpeg,.png,.webp">
        </td>
        <td><img class="image-preview"></td>
        <td><button type="button" class="btn btn-danger remove-row">Remove</button></td>
    </tr>`);
});

// Add Perfume Detail row
document.getElementById('add-perfume-detail').addEventListener('click', function(){
    const tbody = document.querySelector('#perfume-details-table tbody');
    tbody.insertAdjacentHTML('beforeend', `<tr>
        <td><input type="file" name="perfume_details[${perfumeDetailIndex}][icon]" class="form-control icon-input" accept=".jpg,.jpeg,.png,.webp"></td>
        <td><input type="text" name="perfume_details[${perfumeDetailIndex}][title]" class="form-control" placeholder="Enter title" required></td>
        <td><img class="image-preview"></td>
        <td><button type="button" class="btn btn-danger remove-row">Remove</button></td>
    </tr>`);
    perfumeDetailIndex++;
});



document.addEventListener('click', function(e){
    if(e.target && e.target.classList.contains('remove-row')){
        let row = e.target.closest('tr');
        let oldInput = row.querySelector('input[name="old_images[]"]');
        if(oldInput){
            // mark as deleted
            oldInput.name = "deleted_images[]";
        }
        row.remove();
    }
});


// Preview uploaded images/icons
document.addEventListener('change', function(e){
    if(e.target.classList.contains('image-input') || e.target.classList.contains('icon-input')){
        const file = e.target.files[0];
        const preview = e.target.closest('tr').querySelector('.image-preview');
        if(file){
            const reader = new FileReader();
            reader.onload = function(ev){ preview.src = ev.target.result; }
            reader.readAsDataURL(file);
        } else {
            preview.src = '';
        }
    }
});
</script>

<script>
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('icon-input')) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                e.target.closest('tr').querySelector('.image-preview').src = ev.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
});
</script>

</body>
</html>
