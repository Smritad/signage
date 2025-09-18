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
                    <h4>Add Product Details Form</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('products-details.index') }}">Home</a></li>
                        <li class="breadcrumb-item active">Add Product Details</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5>Product Details</h5>
                        <p class="text-muted">Fill out the details below.</p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('products-details.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Category & Subcategory -->
                        <div class="card mb-4 p-3">
                            <h5 class="mb-3">Category & Subcategory</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id" class="form-control" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="sub_category_id" class="form-label">Sub Category <span class="text-danger">*</span></label>
                                    <select name="sub_category_id" id="sub_category_id" class="form-control" required>
                                        <option value="">Select Subcategory</option>
                                        @foreach($subCategories as $sub)
                                            <option value="{{ $sub->id }}">{{ $sub->sab_category_name }}</option>
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
                                    <label for="product_name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" name="product_name" id="product_name" class="form-control" placeholder="Enter product name" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="price" id="price" class="form-control" placeholder="Enter price" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="offer_price" class="form-label">Offer Price</label>
                                    <input type="number" step="0.01" name="offer_price" id="offer_price" class="form-control" placeholder="Enter offer price">
                                </div>
                                <div class="col-md-4">
                                    <label for="product_sku" class="form-label">Product SKU <span class="text-danger">*</span></label>
                                    <input type="text" name="product_sku" id="product_sku" class="form-control" placeholder="Enter SKU" required>
                                </div>
                            </div>

                            <div class="row g-3 mt-3">
                                <div class="col-md-3">
                                    <label for="discount" class="form-label">Discount (%)</label>
                                    <input type="number" step="0.01" name="discount" class="form-control" placeholder="Enter discount">
                                </div>
                                <div class="col-md-3">
                                    <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" class="form-control" placeholder="Enter quantity" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="estimate_delivery" class="form-label">Estimate Delivery</label>
                                    <input type="text" name="estimate_delivery" class="form-control" placeholder="E.g., 5-7 days">
                                </div>
                                <div class="col-md-3">
                                    <label for="return_policy" class="form-label">Return Policy</label>
                                    <input type="text" name="return_policy" class="form-control" placeholder="E.g., 7 days return">
                                </div>
                            </div>
                        </div>

                        <!-- Multiple Product Images -->
                        <div class="card mb-4 p-3">
                            <h5 class="mb-3">Product Images</h5>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary btn-sm" id="add-image">
                                        Add Image
                                    </button>
                                </div>  
                                <br>                          
                                <table class="table table-bordered" id="images-table">
                                <thead>
                                    <tr>
                                        <th>Image <span class="text-danger">*</span></th>
                                        <th>Preview</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="file" name="images[]" class="form-control image-input" accept=".jpg,.jpeg,.png,.webp" required></td>
                                        <td><img class="image-preview img-thumbnail" style="max-width:80px;"></td>
                                        <td><button type="button" class="btn btn-danger remove-row">Remove</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                       <!-- Perfume Notes -->
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
                                    <!-- Dynamic Rows Here -->
                                </tbody>
                            </table>

                            <button type="button" class="btn btn-sm btn-primary" id="add-perfume-note">
                                Add Note
                            </button>
                        </div>




                        <!-- Additional Perfume Details -->
                        <div class="card mb-4 p-3">
                            <h5 class="mb-3">Additional Product Features</h5>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary btn-sm" id="add-perfume-detail">
                                        Add Image
                                    </button>
                                </div>    
                                <br>
                            <table class="table table-bordered" id="perfume-details-table">
                                <thead>
                                    <tr>
                                        <th>Icon <span class="text-danger">*</span></th>
                                        <th>Title <span class="text-danger">*</span></th>
                                        <th>Preview</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="file" name="perfume_details[0][icon]" class="form-control icon-input" accept=".jpg,.jpeg,.png,.webp" required></td>
                                        <td><input type="text" name="perfume_details[0][title]" class="form-control" placeholder="Enter title" required></td>
                                        <td><img class="image-preview img-thumbnail" style="max-width:80px;"></td>
                                        <td><button type="button" class="btn btn-danger remove-row">Remove</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                                    <!-- Fragrance Type & Measurement -->
                                    <div class="card mb-4 p-3">
                                        <h5 class="mb-3">Fragrance Type</h5>
                                        <div class="row g-3">
                                            <!-- Fragrance Type Select -->
                                            <div class="col-md-6">
                                                <label for="fragrance_type_id" class="form-label">Fragrance Type <span class="text-danger">*</span></label>
                                                <select name="fragrance_type_id" class="form-control" required>
                                                    <option value="">Select Fragrance Type</option>
                                                    @foreach($fragranceTypes as $ft)
                                                        <option value="{{ $ft->id }}" {{ isset($product) && $product->fragrance_type_id == $ft->id ? 'selected' : '' }}>
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


                        <!-- Description / Key Benefits / How to Use -->
                        <div class="card mb-4 p-3">
                            <h5 class="mb-3">Description</h5>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control" placeholder="Enter product description" rows="3"></textarea>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label for="key_benefits" class="form-label">Key Benefits</label>
                                    <textarea name="key_benefits" class="form-control" placeholder="Enter key benefits" rows="3"></textarea>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label for="how_to_use" class="form-label">How to Use</label>
                                    <textarea name="how_to_use" class="form-control" placeholder="Instructions for use" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- FAQs -->
                        <div class="card mb-4 p-3">
                            <h5 class="mb-3">FAQs</h5>
                            <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary btn-sm" id="add-faq"">
                                        Add FAQs
                                    </button>
                                </div>    
                                <br>
                            <table class="table table-bordered" id="faqs-table">
                                <thead>
                                    <tr>
                                        <th>Question <span class="text-danger">*</span></th>
                                        <th>Answer <span class="text-danger">*</span></th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="text" name="faqs[0][question]" class="form-control" placeholder="Enter question" required></td>
                                        <td><input type="text" name="faqs[0][answer]" class="form-control" placeholder="Enter answer" required></td>
                                        <td><button type="button" class="btn btn-danger remove-row">Remove</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Submit -->
                        <div class="d-flex justify-content-end mb-5">
                            <button type="submit" class="btn btn-success me-2">Save</button>
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

<script>
let faqIndex = 1;
let perfumeDetailIndex = 1;

// Add FAQ row
document.getElementById('add-faq').addEventListener('click', function(){
    const tbody = document.querySelector('#faqs-table tbody');
    const html = `<tr>
        <td><input type="text" name="faqs[${faqIndex}][question]" class="form-control" placeholder="Enter question" required></td>
        <td><input type="text" name="faqs[${faqIndex}][answer]" class="form-control" placeholder="Enter answer" required></td>
        <td><button type="button" class="btn btn-danger remove-row">Remove</button></td>
    </tr>`;
    tbody.insertAdjacentHTML('beforeend', html);
    faqIndex++;
});

// Add Product Image row
document.getElementById('add-image').addEventListener('click', function(){
    const tbody = document.querySelector('#images-table tbody');
    const html = `<tr>
        <td><input type="file" name="images[]" class="form-control image-input" accept=".jpg,.jpeg,.png,.webp" required></td>
        <td><img class="image-preview"></td>
        <td><button type="button" class="btn btn-danger remove-row">Remove</button></td>
    </tr>`;
    tbody.insertAdjacentHTML('beforeend', html);
});

// Add Perfume Detail row
document.getElementById('add-perfume-detail').addEventListener('click', function(){
    const tbody = document.querySelector('#perfume-details-table tbody');
    const html = `<tr>
        <td><input type="file" name="perfume_details[${perfumeDetailIndex}][icon]" class="form-control icon-input" accept=".jpg,.jpeg,.png,.webp" required></td>
        <td><input type="text" name="perfume_details[${perfumeDetailIndex}][title]" class="form-control" placeholder="Enter title" required></td>
        <td><img class="image-preview"></td>
        <td><button type="button" class="btn btn-danger remove-row">Remove</button></td>
    </tr>`;
    tbody.insertAdjacentHTML('beforeend', html);
    perfumeDetailIndex++;
});

// Remove row
document.addEventListener('click', function(e){
    if(e.target && e.target.classList.contains('remove-row')){
        e.target.closest('tr').remove();
    }
});

// Image Preview
document.addEventListener('change', function(e){
    if(e.target.classList.contains('image-input') || e.target.classList.contains('icon-input')){
        const file = e.target.files[0];
        const preview = e.target.closest('tr').querySelector('.image-preview');
        if(file){
            const reader = new FileReader();
            reader.onload = function(e){
                preview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        } else {
            preview.src = '';
        }
    }
});
</script>
<script>
$(document).ready(function () {
    // Global Select2 Init (for existing fixed selects)
    $('#application_type').select2({
        placeholder: "-- Select Application Type --",
        allowClear: true,
        width: '100%'
    });

    $('#light_application_type').select2({
        placeholder: "-- Select Light Application Type --",
        allowClear: true,
        width: '100%'
    });

    $('#parent_category').select2({
        placeholder: "-- Select Sub Category --",
        allowClear: true,
        width: '100%'
    });

    // Add new row
    $('#add-perfume-note').on('click', function () {
        let tableBody = $('#perfume-notes-table tbody');

        let row = `
            <tr>
                <td>
                    <select name="perfume_notes_details[][note_ids][]" 
                            class="form-control perfume-notes-select" multiple required>
                        <option value="">Select Perfume Notes</option>
                        @foreach($perfumeNotes as $note)
                            <option value="{{ $note->id }}">{{ $note->title }}</option>
                        @endforeach
                    </select>
                    <small class="text-secondary">Hold Ctrl (Windows) or Cmd (Mac) to select multiple</small>
                </td>
                <td>
                    <select name="perfume_notes_details[][level_id]" 
                            class="form-control perfume-level-select" required>
                        <option value="">Select Note Level</option>
                        @foreach($perfumeNotesLevel as $level)
                            <option value="{{ $level->id }}">{{ $level->title }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-note">Remove</button>
                </td>
            </tr>
        `;

        tableBody.append(row);

        // ✅ Reinitialize Select2 for new selects
        tableBody.find('.perfume-notes-select').last().select2({
            placeholder: "-- Select Perfume Notes --",
            allowClear: true,
            width: '100%'
        });

        tableBody.find('.perfume-level-select').last().select2({
            placeholder: "-- Select Note Level --",
            allowClear: true,
            width: '100%'
        });
    });

    // Remove row
    $(document).on('click', '.remove-note', function () {
        $(this).closest('tr').remove();
    });
});
</script>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 
        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
 
        <!-- jQuery (required for Select2) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 
        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</body>
</html>
