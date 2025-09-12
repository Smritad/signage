<!-- Page Body Start-->
 <div class="page-body-wrapper">
        <!-- Page Sidebar Start-->
        <div class="sidebar-wrapper" data-layout="stroke-svg">
          <div class="logo-wrapper"><a href="{{ route('admin.dashboard') }}"><img class="img-fluid" src="{{ asset('admin/assets/images/logo/logo.webp') }}" alt="" style="max-width: 20% !important;"></a>
		  	<a href="{{ route('admin.dashboard') }}">
				<img class="img-fluid" src="{{ asset('admin/assets/images/logo/logo.webp') }}" alt="" style="max-width: 65% !important;">
			</a>  
		  <div class="back-btn"><i class="fa fa-angle-left"> </i></div>
            <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"> </i></div>
          </div>
          <div class="logo-icon-wrapper"><a href="{{ route('admin.dashboard') }}"><img class="img-fluid" src="{{ asset('admin/assets/images/logo/logo.webp') }}" alt="" ></a></div>
          <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="sidebar-menu">
              <ul class="sidebar-links" id="simple-bar">
                <li class="back-btn"><a href="{{ route('admin.dashboard') }}"><img class="img-fluid" src="{{ asset('admin/assets/images/logo/logo-icon.png') }}" alt=""></a>
                  <div class="mobile-back text-end"> <span>Back </span><i class="fa fa-angle-right ps-2" aria-hidden="true"></i></div>
                </li>
             
                <li class="sidebar-list {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"> </i>
                  <a class="sidebar-link sidebar-title link-nav" href="{{ route('admin.dashboard') }}">
                    <svg class="stroke-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#fill-home') }}"></use>
                    </svg>
                    <span class="lan-3">Dashboard</span>
                  </a>
                </li>

               
 
                 <li class="sidebar-list {{ request()->routeIs('banner-details.index') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"> </i>
                  <a class="sidebar-link sidebar-title" href="#">
                    <svg class="stroke-icon"> 
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-icons') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-icons') }}"></use>
                    </svg>
                    <span>Home page</span>
                  </a>
                  <ul class="sidebar-submenu">
                    <li><a href="{{ route('banner-details.index') }}" class="{{ request()->routeIs('banner-details.index') ? 'active' : '' }}">Banner Details</a></li>
                    <li><a href="{{ route('contact-adverstiment-details.index') }}" class="{{ request()->routeIs('contact-adverstiment-details.index') ? 'active' : '' }}">Contact Adverstiment Details</a></li>
                    <li><a href="{{ route('signage-wellness-details.index') }}" class="{{ request()->routeIs('signage-wellness-details.index') ? 'active' : '' }}">Signage Wellness Details</a></li>
                    <li><a href="{{ route('customer-review-details.index') }}" class="{{ request()->routeIs('customer-review-details.index') ? 'active' : '' }}">Customer Review Details</a></li>
                    <li><a href="{{ route('footer-details.index') }}" class="{{ request()->routeIs('footer-details.index') ? 'active' : '' }}">Footer Details</a></li>



                  </ul>
                </li>

              <li class="sidebar-list">
                  <i class="fa fa-thumb-tack"> </i>
                  <a class="sidebar-link sidebar-title" href="#">
                    <svg class="stroke-icon"> 
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#cart') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#cart') }}"></use>
                    </svg>
                    <span>Store Management</span>
                  </a>
                  <ul class="sidebar-submenu">
                    <li><a href="{{ route('category-details.index') }}" class="{{ request()->routeIs('category-details.index') ? 'active' : '' }}">Category</a></li>
                    <li><a href="{{ route('sab-category-details.index') }}" class="{{ request()->routeIs('sab-category-details.index') ? 'active' : '' }}">Sab Category</a></li>
                    <li><a href="{{ route('perfume-notes-details.index') }}" class="{{ request()->routeIs('perfume-notes-details.index') ? 'active' : '' }}">Perfume Notes</a></li>
                    <li><a href="{{ route('fragrance-type-details.index') }}" class="{{ request()->routeIs('fragrance-type-details.index') ? 'active' : '' }}">Fragrance Type</a></li>
                    <li><a href="{{ route('products-details.index') }}" class="{{ request()->routeIs('products-details.index') ? 'active' : '' }}">Products Details</a></li>


                  </ul>
              </li>
              </ul>
              <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
            </div>
          </nav>
        </div>


        