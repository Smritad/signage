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
                               <form class="row g-3 needs-validation custom-input" novalidate action="{{ route('contact-adverstiment-details.update', $advertisement->id) }}"  method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <!-- Title -->
                                    <div class="col-xxl-4 col-sm-6">
                                        <label class="form-label" for="title">Title <span class="txt-danger">*</span></label>
                                        <input class="form-control" id="title" type="text" name="title" value="{{ old('title', $advertisement->title) }}" required>
                                    </div>

                                    <!-- Heading -->
                                    <div class="col-xxl-4 col-sm-6">
                                        <label class="form-label" for="advertisement_heading">Advertisement Heading <span class="txt-danger">*</span></label>
                                        <input class="form-control" id="advertisement_heading" type="text" name="advertisement_heading" value="{{ old('advertisement_heading', $advertisement->advertisement_heading) }}" required>
                                    </div>

                                    <!-- Image Upload -->
                                    <div class="col-xxl-4 col-sm-12">
                                        <label class="form-label" for="advertisement_image">Advertisement Image</label>
                                        <input class="form-control" id="advertisement_image" type="file" name="advertisement_image" accept=".jpg, .jpeg, .png, .webp">
                                        <small class="text-muted">Leave empty to keep the current image.</small>
                                        @if($advertisement->advertisement_image)
                                            <br><img src="{{ asset('home/advertisement/' . $advertisement->advertisement_image) }}" width="150" class="mt-2">
                                        @endif
                                    </div>

                                    <!-- Buttons -->
                                    <div class="col-12 text-end">
                                        <a href="{{ route('contact-adverstiment-details.index') }}" class="btn btn-secondary">Back</a>
                                        <button class="btn btn-success">Update</button>
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