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
                </div>
                <div class="col-6">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">                                       
                        <svg class="stroke-icon">
                          <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                        </svg></a></li>
                  </ol>
                </div>
              </div>
            </div>
          </div>
          <!-- Container-fluid starts-->
          <div class="container-fluid">
            <div class="row">
              <!-- Zero Configuration  Starts-->
              <div class="col-sm-12">
                <div class="card">
                  <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-4">
								<nav aria-label="breadcrumb" role="navigation">
									<ol class="breadcrumb mb-0">
										<li class="breadcrumb-item">
											<a href="{{ route('footer-details.index') }}">Home</a>
										</li>
										<li class="breadcrumb-item active" aria-current="page">Footer Details</li>
									</ol>
								</nav>

								<a href="{{ route('footer-details.create') }}" class="btn btn-primary px-5 radius-30">+ Add Footer Details</a>
							</div>


                    <div class="table-responsive custom-scrollbar">
                   <table class="display" id="basic-1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Footer Heading</th>
                            <th>Address</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Newsletter</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($records as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->footer_heading }}</td>
                            <td>{{ $item->address_line1 }}<br>{{ $item->address_line2 }}</td>
                            <td>{{ $item->phone }}</td>
                            <td>{{ $item->email }}</td>
                            <td>
                                <strong>{{ $item->newsletter_heading }}</strong><br>
                                {{ $item->newsletter_description }}
                            </td>
                            <td>
                                <a href="{{ route('footer-details.edit', $item->id) }}" class="btn btn-primary">Edit</a>
                                <br>
                                <br>
                                <form action="{{ route('footer-details.destroy', $item->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                   </table>
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