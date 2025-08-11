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
                <div class="col-6">
                    <h4>Add Customer Review  Details Form</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('customer-review-details.index') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Add Customer Review  Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Customer Review  Details Form</h4>
                        <p class="f-m-light mt-1">Fill in the correct details and submit the form.</p>
                    </div>
                    <div class="card-body">
                        <div class="vertical-main-wizard">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="tab-content" id="wizard-tabContent">
                                        <div class="tab-pane fade show active" id="wizard-contact" role="tabpanel" aria-labelledby="wizard-contact-tab">
                                            <form class="row g-3 needs-validation custom-input" novalidate action="{{ route('customer-review-details.store') }}" method="POST" enctype="multipart/form-data">
                                                @csrf

                                               <!-- Heading -->
                                                <div class="col-12">
                                                    <label class="form-label" for="heading">Heading <span class="txt-danger">*</span></label>
                                                    <input class="form-control" id="heading" type="text" name="heading" placeholder="Enter Heading" required>
                                                    <div class="invalid-feedback">Please enter a Heading.</div>
                                                </div>

                                               
                                                <!-- Dynamic Table -->
                                                <div class="col-12">
                                                    <label class="form-label">Customer Review  Details <span class="txt-danger">*</span></label>

                                                    <!-- Add More Button aligned right -->
                                                    <div class="mb-2 text-end">
                                                        <button type="button" class="btn btn-success btn-sm" onclick="addRow()">+ Add More</button>
                                                    </div>

                                                    <table class="table table-bordered" id="signageTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Title</th>
                                                                <th>Description</th>
                                                                <th>Name</th>
                                                                <th>Rating</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="signageBody">
                                                            <tr>
                                                                <td><input type="text" class="form-control" name="items[0][title]" required></td>
                                                                <td><textarea class="form-control" name="items[0][description]" required></textarea></td>
                                                                <td><input type="text" class="form-control" name="items[0][name]" required placeholder="Customer Name"></td>
                                                                <td><input type="number" class="form-control" name="items[0][rating]" required placeholder="Rating (1-5)" min="1" max="5"></td>
                                                                <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Remove</button></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>

                                                </div>



                                                <!-- Submit -->
                                                <div class="col-12 text-end">
                                                    <a href="{{ route('customer-review-details.index') }}" class="btn btn-danger px-4">Cancel</a>
                                                    <button class="btn btn-primary" type="submit">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
            </div>
        </div>
    </div>
</div>

@include('components.backend.footer')
@include('components.backend.main-js')



<script>
let rowIndex = 1;

function addRow() {
    const tableBody = document.getElementById('signageBody');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td><input type="text" class="form-control" name="items[${rowIndex}][title]" required></td>
        <td><textarea class="form-control" name="items[${rowIndex}][description]" required></textarea></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][name]" required placeholder="Customer Name"></td>
        <td><input type="number" class="form-control" name="items[${rowIndex}][rating]" required placeholder="Rating (1-5)" min="1" max="5"></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Remove</button></td>
    `;
    tableBody.appendChild(newRow);
    rowIndex++;
}
function removeRow(button) {
    const row = button.closest('tr');
    row.remove();
}


function previewImage(event, index) {
    const input = event.target;
    const file = input.files[0];
    const preview = document.getElementById(`preview_${index}`);

    if (!file) return;

    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/svg+xml'];

    if (validTypes.includes(file.type)) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        preview.src = '';
        alert('Please upload a valid image file (jpg, jpeg, png, webp, svg).');
    }
}
</script>
</body>
</html>
