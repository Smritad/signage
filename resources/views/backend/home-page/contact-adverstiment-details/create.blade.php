<!doctype html>
<html lang="en">
    
<head>
    @include('components.backend.head')
</head>
	   
		@include('components.backend.header')

	    <!--start sidebar wrapper-->	
	    @include('components.backend.sidebar')
	   <!--end sidebar wrapper-->


        <div class="page-body">
          <div class="container-fluid">
            <div class="page-title">
              <div class="row">
                <div class="col-6">
                  <h4>Add Adverstiment Details Form</h4>
                </div>
                <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                    <a href="{{ route('contact-adverstiment-details.index') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Add Adverstiment Details</li>
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
                        <h4>contact-adverstiment-details Details Form</h4>
                        <p class="f-m-light mt-1">Fill up your true details and submit the form.</p>
                    </div>
                    <div class="card-body">
                        <div class="vertical-main-wizard">
                        <div class="row g-3">    
                            <!-- Removed empty col div -->
                            <div class="col-12">
                            <div class="tab-content" id="wizard-tabContent">
                                <div class="tab-pane fade show active" id="wizard-contact" role="tabpanel" aria-labelledby="wizard-contact-tab">
                                <form class="row g-3 needs-validation custom-input" novalidate action="{{ route('contact-adverstiment-details.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <!-- Title -->
                                    <div class="col-xxl-4 col-sm-6">
                                        <label class="form-label" for="title">Title <span class="txt-danger">*</span></label>
                                        <input class="form-control" id="title" type="text" name="title" placeholder="Enter Title" required>
                                        <div class="invalid-feedback">Please enter a Title.</div>
                                    </div>

                                    <!--  Heading -->
                                    <div class="col-xxl-4 col-sm-6">
                                        <label class="form-label" for="advertisement_heading">Advertisement Heading <span class="txt-danger">*</span></label>
                                        <input class="form-control" id="advertisement_heading" type="text" name="advertisement_heading" placeholder="Enter Banner Heading" required>
                                        <div class="invalid-feedback">Please enter a Advertisement Heading.</div>
                                    </div>

                                    <!-- Advertisement Image -->
                                    <div class="col-xxl-4 col-sm-12">
                                        <label class="form-label" for="advertisement_image">Advertisement Image <span class="txt-danger">*</span></label>
                                        <input class="form-control" id="advertisement_image" type="file" name="advertisement_image" accept=".jpg, .jpeg, .png, .webp" required onchange="previewAdvertisementImage()">
                                        <div class="invalid-feedback">Please upload an Advertisement Image.</div>
                                        <small class="text-secondary"><b>Note: The file size should be less than 2MB.</b></small>
                                        <br>
                                        <small class="text-secondary"><b>Note: Only files in .jpg, .jpeg, .png, .webp format can be uploaded.</b></small>
                                    </div>

                                    <!-- Preview Section -->
                                    <div class="col-xxl-4 col-sm-12" id="advertisementImagePreviewContainer" style="display: none;">
                                        <img id="advertisement_image_preview" src="" alt="Preview" class="img-fluid" style="max-height: 200px; border: 1px solid #ddd; padding: 5px;">
                                    </div>

                                    <!-- Form Actions -->
                                    <div class="col-12 text-end">
                                        <a href="{{ route('banner-details.index') }}" class="btn btn-danger px-4">Cancel</a>
                                        <button class="btn btn-primary" type="submit">Submit</button>
                                    </div>
                                </form>


                                </div>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>

          </div>
        </div>
        <!-- footer start-->
        @include('components.backend.footer')
        </div>
        </div>


       
       @include('components.backend.main-js')

<script>
    function previewAdvertisementImage() {
        const file = document.getElementById('advertisement_image').files[0];
        const previewContainer = document.getElementById('advertisementImagePreviewContainer');
        const previewImage = document.getElementById('advertisement_image_preview');

        // Clear previous preview
        previewImage.src = '';

        if (file) {
            const validImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

            if (validImageTypes.includes(file.type)) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                };

                reader.readAsDataURL(file);
            } else {
                alert('Please upload a valid image file (jpg, jpeg, png, webp).');
            }
        }
    }
</script>

</body>

</html>