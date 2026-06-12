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
                  <h4>Edit Fragrance Type Form</h4>
                </div>
                <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                    <a href="{{ route('fragrance-type-details.index') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Edit Fragrance Type Details</li>
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
                        <h4>Fragrance Type Details Form</h4>
                        <p class="f-m-light mt-1">Fill up your true details and submit the form.</p>
                    </div>
                    <div class="card-body">
                        <div class="vertical-main-wizard">
                        <div class="row g-3">    
                            <!-- Removed empty col div -->
                            <div class="col-12">
                            <div class="tab-content" id="wizard-tabContent">
                                <div class="tab-pane fade show active" id="wizard-contact" role="tabpanel" aria-labelledby="wizard-contact-tab">
                                    <form action="{{ route('fragrance-type-details.update', $note->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                    
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="title" class="form-label">Fragrance Type Title</label>
                                                <input type="text" name="title" id="title" value="{{ $note->title }}" class="form-control" required>
                                            </div>
                                        
                                            <div class="col-md-6">
                                                <label for="image" class="form-label">Fragrance Image</label>
                                                <input type="file" name="image" id="image" class="form-control" accept="image/*">
                                                <small class="text-secondary"><b>Note: Each file should be less than 2MB.</b></small><br>
                                                <small class="text-secondary"><b>Allowed: jpg, jpeg, png, webp, svg</b></small>

                                                @if($note->image)
                                                    <div class="mt-2">
                                                        <img src="{{ asset('signage/home/productimage/' . $note->image) }}" width="100" alt="Fragrance Image">
                                                        <input type="hidden" name="old_image" value="{{ $note->image }}">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    
                                        <button type="submit" class="btn btn-primary">Update</button>
                                        <a href="{{ route('fragrance-type-details.index') }}" class="btn btn-secondary">Cancel</a>
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