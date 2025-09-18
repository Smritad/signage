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
                                            @php
                        use App\Models\CategoryDetails;
                        use App\Models\SabCategoryDetails;
                        use App\Models\ProductsDetails;

                        // Fetch all master categories
                        $masterCategories = CategoryDetails::all();
                        @endphp

                        <div class="col-xl-6 d-none d-xl-block">
                            <nav class="box-navigation">
                                <ul class="box-nav-menu">
                                    @foreach($masterCategories as $master)
                                        @php
                                            // Check if master category has products
                                            $masterHasProducts = ProductsDetails::where('category_id', $master->id)->exists();

                                            // Get subcategories for this master
                                            $subCategories = SabCategoryDetails::where('category_id', $master->id)->get();
                                        @endphp

                                        <li class="menu-item position-relative">
                                            <a href="{{ $masterHasProducts ? route('product.category', $master->slug) : route('coming.soon') }}" class="item-link">
                                                {{ $master->category_name }}
                                                @if($subCategories->count() > 0)
                                                    <i class="icon icon-caret-down"></i>
                                                @endif
                                            </a>

                                            @if($subCategories->count() > 0)
                                                <div class="sub-menu">
                                                    <ul class="sub-menu_list">
                                                        @foreach($subCategories as $sub)
                                                            @php
                                                                // Check if subcategory has products
                                                                $subHasProducts = ProductsDetails::where('sub_category_id', $sub->id)->exists();
                                                            @endphp
                                                            <li>
                                                                <a href="{{ $subHasProducts ? route('product.details', $sub->slug) : route('coming.soon') }}" class="sub-menu_link">
                                                                    {{ $sub->sab_category_name }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </li>
                                    @endforeach

                                   
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