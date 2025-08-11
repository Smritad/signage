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
											<a href="{{ route('customer-review-details.index') }}">Home</a>
										</li>
										<li class="breadcrumb-item active" aria-current="page">Customer Review Details</li>
									</ol>
								</nav>

								<a href="{{ route('customer-review-details.create') }}" class="btn btn-primary px-5 radius-30">+ Add Customer Review Details</a>
							</div>


                   <div class="table-responsive custom-scrollbar">
                    <table class="display" id="basic-1">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Heading</th>
                                <th>Reviews</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $index => $record)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $record->heading }}</td>
                                <td>
                                    @foreach(json_decode($record->items, true) as $item)
                                        <strong>{{ $item['title'] }}</strong><br>
                                        {{ $item['description'] }}<br>
                                        <em>{{ $item['name'] }} (Rating: {{ $item['rating'] }})</em>
                                        <hr>
                                    @endforeach
                                </td>
                                <td>
                                    <a href="{{ route('customer-review-details.edit', $record->id) }}" class="btn btn-primary">Edit</a>
                                    <br>
                                    <br>
                                    <form action="{{ route('customer-review-details.destroy', $record->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure to delete this?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            @if($records->isEmpty())
                            <tr><td colspan="4" class="text-center">No records found.</td></tr>
                            @endif
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