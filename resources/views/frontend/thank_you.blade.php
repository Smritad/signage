

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
  <meta http-equiv="content-type" content="text/html;charset=utf-8" />
  <head>
                    @include('components.frontend.head')

  </head>
  <body>
    <button id="goTop">
    <span class="border-progress"></span>
    <span class="icon icon-caret-up"></span>
    </button>
    <div class="preload preload-container" id="preload">
      <div class="preload-logo">
        <div class="spinner"></div>
      </div>
    </div>
    <div id="wrapper">
                @include('components.frontend.header')
      <!-- /Header -->
      <!-- Page Title -->
      <section class="s-page-title">
        <div class="container">
          <div class="content">
            <h1 class="title-page">Thank You</h1>
            <ul class="breadcrumbs-page">
              <li><a href="{{ route('frontend.index') }}" class="h6 link">Home</a></li>
              <li class="d-flex"><i class="icon icon-caret-right"></i></li>
              <li>
                <h6 class="current-page fw-normal">Thank You</h6>
              </li>
            </ul>
          </div>
        </div>
      </section>
      <!-- /Page Title -->
      <!-- Section Product -->
<div class="about-us-page flat-spacing">
  <div class="container">
    <div class="row align-items-center">
      
     

      
         <div style="text-align:center; padding:100px 20px; background:#f9f9f9;">
    <h1 style="color:#003d79; font-size:36px; margin-bottom:20px;">Thank You!</h1>
    <p style="font-size:18px; color:#333;">We have received your enquiry and our team will get back to you shortly.</p>
    <a href="{{ url('/') }}" style="display:inline-block; margin-top:30px; padding:12px 30px; background:#003d79; color:#fff; text-decoration:none; border-radius:5px; font-size:16px;">Go to Home</a>
</div>
        </div>
      </div>

    </div>
  </div>
</div>
      <!-- /Section Product -->
      <!-- Footer -->
                @include('components.frontend.footer')
      <!-- /Footer -->
    </div>
    <!-- Mobile Menu -->
    <!-- /Mobile Menu -->
    <!-- Javascript -->
                    @include('components.frontend.main-js')

  </body>
</html>




