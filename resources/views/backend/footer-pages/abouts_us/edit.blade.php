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
                  <h4>Edit Banner Details Form</h4>
                </div>
                <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                    <a href="{{ route('banner-details.index') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Edit Banner Details</li>
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
                        <h4>Banner Details Form</h4>
                        <p class="f-m-light mt-1">Fill up your true details and submit the form.</p>
                    </div>
                    <div class="card-body">
                        <div class="vertical-main-wizard">
                        <div class="row g-3">    
                            <!-- Removed empty col div -->
                            <div class="col-12">
                            <div class="tab-content" id="wizard-tabContent">
                                <div class="tab-pane fade show active" id="wizard-contact" role="tabpanel" aria-labelledby="wizard-contact-tab">
                                    <form class="row g-3 needs-validation custom-input" novalidate 
                                          action="{{ route('aboutus-details.update', $about->id) }}" 
                                          method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                    
                                        <!-- Title -->
                                        <div class="col-xxl-4 col-sm-12">
                                            <label class="form-label" for="title">Title <span class="txt-danger">*</span></label>
                                            <input class="form-control" id="title" type="text" name="title" 
                                                   value="{{ old('title', $about->title) }}" placeholder="Enter Title" required>
                                            <div class="invalid-feedback">Please enter a Title.</div>
                                        </div>
                                    
                                        <!-- Description -->
                                        <div class="col-xxl-8 col-sm-12">
                                            <label class="form-label" for="description">Description <span class="txt-danger">*</span></label>
                                            <textarea class="form-control" id="editor" name="description" rows="5" 
                                                  placeholder="Enter Description" required>{!! old('description', $about->description) !!}</textarea>
                                        @error('description')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror    <div class="invalid-feedback">Please enter a Description.</div>
                                        </div>
                                    
                                        <!-- Image -->
                                        <div class="col-xxl-4 col-sm-12">
                                            <label class="form-label" for="image">Image <span class="txt-danger">*</span></label>
                                            <input class="form-control" id="image" type="file" name="image" 
                                                   accept=".jpg,.jpeg,.png,.webp" onchange="previewImage()">
                                            <div class="invalid-feedback">Please upload an Image.</div>
                                            <small class="text-secondary"><b>Note: Max file size 2MB. Only jpg, jpeg, png, webp allowed.</b></small>
                                        </div>
                                    
                                        <!-- Preview -->
                                        <div class="col-xxl-4 col-sm-12" id="imagePreviewContainer" 
                                             style="display: {{ $about->image ? 'block' : 'none' }};">
                                            <img id="image_preview" src="{{ $about->image ? asset('signage/home/productimage/' . $about->image) : '' }}" 
                                                 alt="Preview" class="img-fluid" style="max-height: 200px; border:1px solid #ddd; padding:5px;">
                                        </div>
                                    
                                        <!-- Form Actions -->
                                        <div class="col-12 text-end">
                                            <a href="{{ route('aboutus-details.index') }}" class="btn btn-danger px-4">Cancel</a>
                                            <button class="btn btn-primary" type="submit">Update</button>
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
       
       
       <!-- JS Preview -->
        <script>
            function previewImage() {
            const input = document.getElementById('image');
            const preview = document.getElementById('image_preview');
            const container = document.getElementById('imagePreviewContainer');
        
            if(input.files && input.files[0]){
                const reader = new FileReader();
                reader.onload = function(e){
                    preview.src = e.target.result;
                    container.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        </script>

</body>

</html>