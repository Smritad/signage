<!doctype html>
<html lang="en">
    
<head>
    @include('components.backend.head')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.3.0/apexcharts.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.3.0/apexcharts.min.js"></script>
</head>
	   
		@include('components.backend.header')

	    <!--start sidebar wrapper-->	
	    @include('components.backend.sidebar')
	   <!--end sidebar wrapper-->



       <div class="page-body"> 
          <div class="container-fluid">            
            <div class="page-title"> 
              <div class="row">
                
                
              </div>
            </div>
          </div>


        <!-- Container-fluid starts -->
          <div class="container-fluid">
            <div class="row"> 
              <div class="col-xl-6 box-col-7"> 
                <div class="card">
                  <div class="card-header sales-chart card-no-border pb-0">
                    <h4>Sales Chart </h4>
                    <div class="sales-chart-dropdown">
                      <div class="sales-chart-dropdown-select">
                        <div class="card-header-right-icon online-store">
                          <div class="dropdown">
                            <button class="btn dropdown-toggle dropdown-toggle-store" id="dropdownMenuButtonToggle" data-bs-toggle="dropdown" aria-expanded="false">Online Store</button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButtonToggle" role="menu"><span class="dropdown-item">All </span><span class="dropdown-item">Employee</span><span class="dropdown-item">Client    </span></div>
                          </div>
                        </div>
                        <div class="card-header-right-icon"> 
                          <div class="dropdown"> 
                            <button class="btn dropdown-toggle" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">Last Year  </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1" role="menu"><span class="dropdown-item">Last Month</span><span class="dropdown-item">Last Week </span><span class="dropdown-item">Today  </span></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="card-body p-2 pt-0">
                    <div class="sales-wrapper">
                      <div id="saleschart"> </div>
                    </div>
                  </div>
                </div>
              </div>


                <div class="col-xl-6 col-md-12 box-col-5 total-revenue-total-order">
                  <div class="row">
                  
                  </div>
                </div>

               

             

            </div>
          </div>
          <!-- Container-fluid Ends -->
        </div>
        <!-- footer start-->
        @include('components.backend.footer')
      </div>
    </div>

        
    
    @include('components.backend.main-js')

    
   








        
</body>

</html>