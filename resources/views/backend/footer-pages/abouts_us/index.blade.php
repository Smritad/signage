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
											<a href="{{ route('aboutus-details.index') }}">Home</a>
										</li>
										<li class="breadcrumb-item active" aria-current="page">About us Details</li>
									</ol>
								</nav>

								<a href="{{ route('aboutus-details.create') }}" class="btn btn-primary px-5 radius-30">+ Add About us Details</a>
							</div>


                    <div class="table-responsive custom-scrollbar">
                    <table class="table table-bordered" id="aboutus-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Title</th>
            <th>Image</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($abouts as $index => $about)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $about->title }}</td>
            <td>
                @if($about->image)
                    <img src="{{ asset('signage/home/productimage/'.$about->image) }}" 
                         alt="{{ $about->title }}" style="height: 50px;">
                @else
                    N/A
                @endif
            </td>
            <td>
                <a href="{{ route('aboutus-details.edit', $about->id) }}" class="btn btn-sm btn-primary">Edit</a>
                <form action="{{ route('aboutus-details.destroy', $about->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger"
                        onclick="return confirm('Are you sure to delete?')">Delete</button>
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