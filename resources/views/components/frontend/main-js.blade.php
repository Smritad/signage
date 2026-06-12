<script src="{{ asset('frontend/assets/js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/jquery.min.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/swiper-bundle.min.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/carousel.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/bootstrap-select.min.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/lazysize.min.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/wow.min.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/parallaxie.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/count-down.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/main.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/infinityslide.js')}}"></script>
   <script src="{{ asset('frontend/assets/js/nouislider.min.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/shop.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/sibforms.js')}}" defer></script>
     
    <script src="{{ asset('frontend/assets/js/photoswipe-lightbox.umd.min.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/photoswipe.umd.min.js')}}"></script>
    <!--<script src="{{ asset('frontend/assets/js/zoom.js')}}"></script>-->
    <script src="{{ asset('frontend/assets/js/lightslider.js')}}"></script>
    
        <script src="{{ asset('frontend/assets/js/drift.min.js')}}"></script>

<script src="{{ asset('frontend/assets/js/bootstrap.bundle.min.js') }}"></script>
 <script src="{{ asset('frontend/assets/js/drift.min.js') }}"></script>
  
 <!-- Include Notyf CSS & JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/notyf/3.10.0/notyf.min.css" type="text/css" media="all">
<script src="https://cdnjs.cloudflare.com/ajax/libs/notyf/3.10.0/notyf.min.js"></script>

<!-- =====================================================
     ADD ALL YOUR EXISTING JS INCLUDES HERE AS BEFORE
     e.g. <script src="...jquery..."></script> etc.
     ===================================================== -->

<script>
    /* ══════════════════════════════════════════════════════════
     |  GLOBAL NOTYF — single instance, shared across all pages
     ══════════════════════════════════════════════════════════ */
  var notyf = new Notyf({
    duration: 4000,
    ripple: true,
    position: { x: 'right', y: 'top' },
    dismissible: true,
    types: [
        {
            type: 'custom-success',
            background: '#ab924a',
            icon: {
                className: 'fa fa-check-circle',
                tagName: 'i',
                color: 'white'
            }
        },
        {
            type: 'custom-warning',
            background: '#856404',
            icon: false
        }
    ]
});

// Font Size
const style = document.createElement('style');

style.innerHTML = `
    .notyf__message {
        font-size: 20px !important;
    }
`;

document.head.appendChild(style);

    /* ── Single global helper — call showToast() from anywhere ── */
    function showToast(message, type) {
        type = type || 'success';
        if (type === 'success') {
            notyf.open({ type: 'custom-success', message: message });
        } else if (type === 'error' || type === 'warning') {
            notyf.open({ type: 'custom-warning', message: message });
        } else {
            notyf.open({ type: type, message: message });
        }
    }

    /* ── Session flash messages — fires on every page load ── */
    document.addEventListener('DOMContentLoaded', function () {

        @if(Session::has('success'))
            showToast(@json(session('success')), 'success');
        @endif

        @if(Session::has('message'))
            showToast(@json(session('message')), 'success');
        @endif

        @if(Session::has('error'))
            @if(!session('error_type'))
                showToast(@json(session('error')), 'error');
            @endif
        @endif

        @if(Session::has('warning'))
            showToast(@json(session('warning')), 'warning');
        @endif

        @if(Session::has('info'))
            notyf.open({ type: 'info', message: @json(session('info')) });
        @endif

    });

    /* ══════════════════════════════════════════════════════════
     |  GLOBAL PROFILE IMAGE UPLOAD
     |  Handles every page that has the sidebar camera button.
     |  No alert() — uses showToast() instead.
     ══════════════════════════════════════════════════════════ */
    document.addEventListener('DOMContentLoaded', function () {

        var cameraBtn  = document.getElementById('changeImgDash');
        var fileInput  = document.getElementById('fileInputDash');
        var uploadForm = document.getElementById('uploadProfileForm');

        if (!cameraBtn || !fileInput || !uploadForm) return;

        /* Use id first, fall back to class */
        var profileImg = document.getElementById('profileImgDash')
                      || document.querySelector('.imgDash');

        /* Only the camera icon opens the file picker */
        cameraBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            fileInput.click();
        });

        fileInput.addEventListener('change', function () {
            var file = fileInput.files[0];
            if (!file) return;

            /* Instant local preview */
            var reader = new FileReader();
            reader.onload = function (ev) {
                if (profileImg) profileImg.src = ev.target.result;
            };
            reader.readAsDataURL(file);

            /* AJAX upload */
            var csrfMeta = document.querySelector('meta[name="csrf-token"]');
            fetch(uploadForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfMeta ? csrfMeta.getAttribute('content') : ''
                },
                body: new FormData(uploadForm)
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) showToast(data.success, 'success');
                else showToast(data.error || 'Upload failed.', 'error');
            })
            .catch(function () {
                showToast('Upload failed. Please try again.', 'error');
            });
        });

    });
</script>
