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
                  <h4>Add Footer  Details Form</h4>
                </div>
                <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                    <a href="{{ route('footer-details.index') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Add Footer  Details</li>
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
                        <h4>Footer  Details Form</h4>
                        <p class="f-m-light mt-1">Fill up your true details and submit the form.</p>
                    </div>
                    <div class="card-body">
                        <div class="vertical-main-wizard">
                        <div class="row g-3">    
                            <!-- Removed empty col div -->
                            <div class="col-12">
                            <div class="tab-content" id="wizard-tabContent">
                                <div class="tab-pane fade show active" id="wizard-contact" role="tabpanel" aria-labelledby="wizard-contact-tab">
                                <form class="row g-3 needs-validation custom-input" novalidate action="{{ route('footer-details.store') }}" method="POST">
                                    @csrf

                                    <div class="col-md-6">
                                        <label class="form-label">Footer Heading <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="footer_heading" required placeholder="Enter Footer Heading">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="address_line1" required placeholder="Enter address line 1">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Address Line 2</label>
                                        <input type="text" class="form-control" name="address_line2" placeholder="Enter address line 2 (optional)">
                                    </div>
<div class="col-md-6">
                                        <label class="form-label">iframe address</label>
                                        <input type="text" class="form-control" name="iframeaddress" placeholder="Enter address url">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="phone" required placeholder="Enter contact number">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="email" required placeholder="Enter email address">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Newsletter Heading <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="newsletter_heading" required placeholder="Enter newsletter heading">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Facebook Link <span class="text-danger">*</span></label>
                                        <input type="url" class="form-control" name="facebook_link" required placeholder="Enter Facebook profile/page URL">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Instagram Link <span class="text-danger">*</span></label>
                                        <input type="url" class="form-control" name="instagram_link" required placeholder="Enter Instagram profile URL">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Twitter Link <span class="text-danger">*</span></label>
                                        <input type="url" class="form-control" name="twitter_link" required placeholder="Enter Twitter/X profile URL">
                                    </div> 
                                    
                                    <div class="col-md-12">
                                        <label class="form-label">Newsletter Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="newsletter_description" rows="4" required placeholder="Write a short description encouraging users to subscribe..."></textarea>
                                    </div>

                                    

                                    <div class="col-12 text-end">
                                        <a href="{{ route('footer-details.index') }}" class="btn btn-danger px-4">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Submit</button>
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