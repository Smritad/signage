<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')
</head>

<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    <div class="page-body">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="page-title">
                <div class="row">
                    <div class="col-6">
                        <h4>Add About Hayagreevas Details</h4>
                    </div>
                    <div class="col-6 text-end">
                        <ol class="breadcrumb justify-content-end">
                            <li class="breadcrumb-item"><a href="{{ route('about-hayagreevas.index') }}">Home</a></li>
                            <li class="breadcrumb-item active">Add About Hayagreevas Details</li>
                        </ol>
                    </div>
                </div>
            </div>

        

    <!-- Form -->
<form action="{{ route('manage-privacy-policy.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="card mt-3">
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label>Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
            </div>

            <div class="col-md-6">
                <label>Banner Image</label>
                <input type="file" name="banner_image" class="form-control" accept="image/*" onchange="previewBanner(event)">
                <img id="bannerPreview" style="max-height:100px; display:none;" class="mt-2">
            </div>

            <div class="col-md-12">
                <label>Description <span class="text-danger">*</span></label>
                <textarea name="description" id="editor" class="form-control">{{ old('description') }}</textarea>
            </div>
        </div>
    </div>

    <div class="text-end mt-3">
        <a href="{{ route('manage-privacy-policy.index') }}" class="btn btn-danger">Cancel</a>
        <button type="submit" class="btn btn-primary">Create</button>
    </div>
</form>

<script>
function previewBanner(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('bannerPreview');
        output.src = reader.result;
        output.style.display = 'block';
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</div>




        </div>
    </div>

    @include('components.backend.footer')
    @include('components.backend.main-js')

   
</body>
</html>
