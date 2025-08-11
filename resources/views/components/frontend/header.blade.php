 <header class="tf-header header-fix header-abs-1">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-4 col-3 d-xl-none">
                        <a href="#mobileMenu" data-bs-toggle="offcanvas" class="btn-mobile-menu">
                            <span></span>
                        </a>
                    </div>
                    <div class="col-xl-3 col-md-4 col-6 d-flex justify-content-center justify-content-xl-start">
                        <a href="{{ url('/') }}" class="logo-site">
                            <img src="{{ asset('frontend/assets/images/logo/logo.webp')}}" alt="Logo">
                        </a>
                    </div>
                    <div class="col-xl-6 d-none d-xl-block">
                        <nav class="box-navigation">
                            <ul class="box-nav-menu">
                                <li class="menu-item position-relative">
                                    <a href="javascript:void(0)" class="item-link">Perfumes <i
                                            class="icon icon-caret-down"></i></a>
                                    <div class="sub-menu">
                                        <ul class="sub-menu_list">
                                            <li><a href="#" class="sub-menu_link">Men</a></li>
                                            <li><a href="#" class="sub-menu_link">Women</a></li>
                                            <li><a href="#" class="sub-menu_link">Unisex</a></li>
                                            <li><a href="#" class="sub-menu_link">Combos</a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="menu-item">
                                    <a href="javascript:void(0)" class="item-link">Reed Diffusers</a>
                                </li>
                                <li class="menu-item">
                                    <a href="javascript:void(0)" class="item-link">Air Freshner Sachet</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div class="col-xl-3 col-md-4 col-3">
                        <ul class="nav-icon-list">
                            <li class="d-none d-lg-flex">
                                <a class="nav-icon-item link" href="#"><i class="icon icon-user"></i></a>
                            </li>
                            <li class="d-none d-md-flex">
                                <a class="nav-icon-item link" href="#" data-bs-toggle="modal">
                                    <i class="icon icon-magnifying-glass"></i>
                                </a>
                            </li>
                            <li class="d-none d-sm-flex">
                                <a class="nav-icon-item link" href="#"><i class="icon icon-heart"></i></a>
                            </li>
                            <li class="shop-cart" data-bs-toggle="offcanvas" data-bs-target="#shoppingCart">
                                <a class="nav-icon-item link" href="#" data-bs-toggle="offcanvas">
                                    <i class="icon icon-shopping-cart-simple"></i>
                                </a>
                                <span class="count">24</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>