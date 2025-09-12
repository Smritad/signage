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
                    <a href="{{ route('sab-category-details.index') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Edit Sab Category Details</li>
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
                        <h4>Sab Category Details Form</h4>
                        <p class="f-m-light mt-1">Fill up your true details and submit the form.</p>
                    </div>
                    <div class="card-body">
                        <div class="vertical-main-wizard">
                        <div class="row g-3">    
                            <!-- Removed empty col div -->
                            <div class="col-12">
                            <div class="tab-content" id="wizard-tabContent">
                                <div class="tab-pane fade show active" id="wizard-contact" role="tabpanel" aria-labelledby="wizard-contact-tab">
                                 <form action="{{ route('sab-category-details.update', $subcategory->id) }}" method="POST">
                                      @csrf @method('PUT')
                                      <div class="mb-3">
                                          <label>Category</label>
                                          <select name="category_id" class="form-control" required>
                                              @foreach($categories as $cat)
                                                  <option value="{{ $cat->id }}" {{ $subcategory->category_id == $cat->id ? 'selected' : '' }}>
                                                      {{ $cat->category_name }}
                                                  </option>
                                              @endforeach
                                          </select>
                                          @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                                      </div>

                                      <div class="mb-3">
                                          <label>Sab Category Name</label>
                                          <input type="text" name="sab_category_name" class="form-control" value="{{ $subcategory->sab_category_name }}" required>
                                          @error('sab_category_name') <small class="text-danger">{{ $message }}</small> @enderror
                                      </div>

                                      <button type="submit" class="btn btn-primary">Update</button>
                                      <a href="{{ route('sab-category-details.index') }}" class="btn btn-secondary">Cancel</a>
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