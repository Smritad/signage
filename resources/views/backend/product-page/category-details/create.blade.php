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
                  <h4>Add Sab Category Details Form</h4>
                </div>
                <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                    <a href="{{ route('category-details.index') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Add Sab Category Details</li>
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
                        <h4> Category Details Form</h4>
                        <p class="f-m-light mt-1">Fill up your true details and submit the form.</p>
                    </div>
                    <div class="card-body">
                        <div class="vertical-main-wizard">
                        <div class="row g-3">    
                            <!-- Removed empty col div -->
                            <div class="col-12">
                            <div class="tab-content" id="wizard-tabContent">
                                <div class="tab-pane fade show active" id="wizard-contact" role="tabpanel" aria-labelledby="wizard-contact-tab">
                                    <form action="{{ route('category-details.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                    
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="category_name" class="form-label">
                                                    Category Name <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="category_name" id="category_name" class="form-control" required>
                                    
                                                @error('category_name')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                    
                                            <div class="col-md-6">
                                                <label for="category_image" class="form-label">Category Image</label>
                                                <input type="file" name="category_image" id="category_image" class="form-control" accept="image/*">
                                    
                                                <small class="text-secondary"><b>Note: Each file should be less than 2MB.</b></small><br>
                                                <small class="text-secondary"><b>Allowed: jpg, jpeg, png, webp, svg</b></small>
                                    
                                                @error('category_image')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    
                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-primary">Save</button>
                                            <a href="{{ route('category-details.index') }}" class="btn btn-secondary">Cancel</a>
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

</body>

</html>