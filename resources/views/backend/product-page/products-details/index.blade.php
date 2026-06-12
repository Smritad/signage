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
											<a href="{{ route('products-details.index') }}">Home</a>
										</li>
										<li class="breadcrumb-item active" aria-current="page">Products Details </li>
									</ol>
								</nav>

								<a href="{{ route('products-details.create') }}" class="btn btn-primary px-5 radius-30">+ Add Products Details</a>
							</div>


<div class="table-responsive custom-scrollbar">
<table class="table table-striped" id="basic-1">
    <thead>
        <tr>
            <th>#</th>
            <th>Products Name</th>
            <th>Priority</th>
            <th>Bestseller</th> <!-- New column -->
            <th>New Arrival</th> <!-- New column -->
            <th width="200">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $key => $product)
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $product->product_name }}</td>
                <td>
                    <form action="{{ route('products-details.updatePriority', $product->id) }}" method="POST" class="d-flex">
                        @csrf
                        @method('PATCH')
                        <input type="number" name="priority" value="{{ $product->priority }}" class="form-control form-control-sm me-1" style="width:80px;">
                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                    </form>
                </td>

                <!-- Bestseller toggle -->
                <td>
                    <form action="{{ route('products-details.toggleBestseller', $product->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="checkbox" name="is_bestseller" onchange="this.form.submit()" {{ $product->is_bestseller ? 'checked' : '' }}>
                    </form>
                </td>

                <!-- New Arrival toggle -->
                <td>
                    <form action="{{ route('products-details.toggleNewArrival', $product->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="checkbox" name="is_new_arrival" onchange="this.form.submit()" {{ $product->is_new_arrival ? 'checked' : '' }}>
                    </form>
                </td>

                <td>
                    <a href="{{ route('products-details.edit', $product->id) }}" class="btn btn-primary">Edit</a>
                    <form action="{{ route('products-details.destroy', $product->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</button>
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