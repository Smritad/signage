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
											<a href="{{ route('fragrance-type-details.index') }}">Home</a>
										</li>
										<li class="breadcrumb-item active" aria-current="page">Perfume Notes Details</li>
									</ol>
								</nav>

								<a href="{{ route('fragrance-type-details.create') }}" class="btn btn-primary px-5 radius-30">+ Add Perfume Notes Details</a>
							</div>


                    <div class="table-responsive custom-scrollbar">
                    <table class="display" id="basic-1">
                       
                      <thead>
                          <tr>
                              <th>#</th>
                              <th>Title</th>
                            
                              <th width="200">Action</th>
                          </tr>
                      </thead>
                      <tbody>
                          @forelse($notes as $note)
                              <tr>
                                  <td>{{ $loop->iteration }}</td>
                                  <td>{{ $note->title }}</td>
                                
                                  <td>
                                      <a href="{{ route('fragrance-type-details.edit', $note->id) }}" class="btn btn-primary">Edit</a>
                                      <form action="{{ route('fragrance-type-details.destroy', $note->id) }}" method="POST" style="display:inline;">
                                          @csrf @method('DELETE')
                                          <button type="submit" class="btn btn-danger btn-sm"
                                              onclick="return confirm('Are you sure?')">Delete</button>
                                      </form>
                                  </td>
                              </tr>
                          @empty
                              <tr><td colspan="9">No perfume notes found</td></tr>
                          @endforelse
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