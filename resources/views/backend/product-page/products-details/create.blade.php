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
                  <h4>Add Products Details Form</h4>
                </div>
                <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                    <a href="{{ route('products-details.index') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Add Products Details</li>
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
                        <h4> Products Details Form</h4>
                        <p class="f-m-light mt-1">Fill up your true details and submit the form.</p>
                    </div>
                    <div class="card-body">
                        <div class="vertical-main-wizard">
                        <div class="row g-3">    
                            <!-- Removed empty col div -->
                            <div class="col-12">
                            <div class="tab-content" id="wizard-tabContent">
                                <div class="tab-pane fade show active" id="wizard-contact" role="tabpanel" aria-labelledby="wizard-contact-tab">
                                 <form action="{{ route('products-details.store') }}" method="POST">
        @csrf
create feild thiss

category display in drop from the table category_details table take 
        product name
        price 
                product sku

        discount 
        quntity
        estimate delivery
        return policy
        product sku
image and title as (user can add multiple this add btn click doing create in tables and store in jscon endcode)
description
key benefits
how to use
faqs  in table take question and answer and user can add multiple and store in jscon endcode
        <div class="mb-3">
            <label for="title" class="form-label">Perfume Note Title</label>
            <input type="text" name="title" id="title" class="form-control" placeholder="Enter perfume note" required>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('products-details.index') }}" class="btn btn-secondary">Cancel</a>
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