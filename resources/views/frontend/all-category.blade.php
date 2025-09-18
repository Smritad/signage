
<!DOCTYPE html>
<html lang="en">
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
            <h1 class="title-page">{{ $category->category_name }}</h1>

            <ul class="breadcrumbs-page">
                <li><a href="{{ route('frontend.index') }}" class="h6 link">Home</a></li>
                <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                <li>
                    <h6 class="current-page fw-normal">{{ $category->category_name }}</h6>
                </li>
            </ul>
        </div>
    </div>
</section>



        <!-- /Page Title -->
        <!-- Section Product -->
        <div class="flat-spacing">
            <div class="container">
                <div class="row">
                    <div class="col-xl-3">
                        <div class="canvas-sidebar sidebar-filter canvas-filter left">
                            <div class="canvas-wrapper">
                                <div class="canvas-header d-xl-none">
                                    <span class="title h3 fw-medium">Filter</span>
                                    <span class="icon-close link icon-close-popup fs-24 close-filter"></span>
                                </div>
                                <div class="canvas-body">
                                    <div class="widget-facet">
                                        <div class="facet-title" data-bs-target="#category" role="button"
                                            data-bs-toggle="collapse" aria-expanded="true" aria-controls="category">
                                            <span class="h4 fw-semibold">Category</span>
                                            <span class="icon icon-caret-down fs-20"></span>
                                        </div>
                                        <div id="category" class="collapse show">
                                            <ul class="collapse-body filter-group-check group-category">
                                                <li class="list-item">
                                                    <a href="#" class="link h6">Perfumes <span
                                                            class="count">23</span></a>
                                                </li>
                                                <li class="list-item">
                                                    <a href="#" class="link h6">Reed Diffusers<span
                                                            class="count">44</span></a>
                                                </li>
                                                <li class="list-item">
                                                    <a href="#" class="link h6">Air Freshner Sachet<span
                                                            class="count">75</span></a>
                                                </li>

                                            </ul>
                                        </div>
                                    </div>

                                    <div class="widget-facet">
                                        <div class="facet-title" data-bs-target="#availability" role="button"
                                            data-bs-toggle="collapse" aria-expanded="true" aria-controls="availability">
                                            <span class="h4 fw-semibold">Availability</span>
                                            <span class="icon icon-caret-down fs-20"></span>
                                        </div>
                                        <div id="availability" class="collapse show">
                                            <ul class="collapse-body filter-group-check current-scrollbar">
                                                <li class="list-item">
                                                    <input type="radio" name="availability" class="tf-check"
                                                        id="inStock">
                                                    <label for="inStock" class="label">
                                                        <span>In Stock</span><span class="count">23</span>
                                                    </label>
                                                </li>
                                                <li class="list-item disabled">
                                                    <input type="radio" name="availability" class="tf-check"
                                                        id="outStock">
                                                    <label for="outStock" class="label">
                                                        <span>Out of Stock</span><span class="count">34</span>
                                                    </label>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="widget-facet">
                                        <div class="facet-title" data-bs-target="#availability" role="button"
                                            data-bs-toggle="collapse" aria-expanded="true" aria-controls="availability">
                                            <span class="h4 fw-semibold">Perfume Notes</span>
                                            <span class="icon icon-caret-down fs-20"></span>
                                        </div>
                                        <div id="availability" class="collapse show">
                                            <ul class="collapse-body filter-group-check current-scrollbar">
                                                <li class="list-item">
                                                    <input type="radio" class="tf-check" id="inStock">
                                                    <label class="label">
                                                        <span>Woody Aromatic</span><span class="count">03</span>
                                                    </label>
                                                </li>
                                                <li class="list-item disabled">
                                                    <input type="radio" class="tf-check" id="outStock">
                                                    <label class="label">
                                                        <span>Floral Fruity Gourmand</span><span class="count">06</span>
                                                    </label>
                                                </li>
                                                <li class="list-item disabled">
                                                    <input type="radio" class="tf-check" id="outStock">
                                                    <label class="label">
                                                        <span>Citrus Aromatic</span><span class="count">06</span>
                                                    </label>
                                                </li>
                                                <li class="list-item disabled">
                                                    <input type="radio" class="tf-check" id="outStock">
                                                    <label class="label">
                                                        <span>Arabic Oudh</span><span class="count">04</span>
                                                    </label>
                                                </li>
                                                <li class="list-item disabled">
                                                    <input type="radio" class="tf-check" id="outStock">
                                                    <label class="label">
                                                        <span>Aromatic Aquatic</span><span class="count">05</span>
                                                    </label>
                                                </li>
                                                <li class="list-item disabled">
                                                    <input type="radio" class="tf-check" id="outStock">
                                                    <label class="label">
                                                        <span>Powdery</span><span class="count">09</span>
                                                    </label>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="widget-facet">
                                        <div class="facet-title" data-bs-target="#price" role="button"
                                            data-bs-toggle="collapse" aria-expanded="true" aria-controls="price">
                                            <span class="h4 fw-semibold">Price</span>
                                            <span class="icon icon-caret-down fs-20"></span>
                                        </div>
                                        <div id="price" class="collapse show">
                                            <div class="collapse-body widget-price filter-price">
                                                <div class="price-val-range" id="price-value-range" data-min="0"
                                                    data-max="500"></div>
                                                <div class="box-value-price">
                                                    <span class="h6 text-main">Price:</span>
                                                    <div class="price-box">
                                                        <div class="price-val" id="price-min-value" data-currency="₹">
                                                        </div>
                                                        <span>-</span>
                                                        <div class="price-val" id="price-max-value" data-currency="₹">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="canvas-bottom d-xl-none">
                                    <button id="reset-filter" class="tf-btn btn-reset">Reset Filters</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-9">
                        <div class="tf-shop-control">
                            <div class="shop-sale-text d-none d-xl-flex">
                                <input type="checkbox" name="sale" class="tf-check" id="sale">
                                <label for="sale" class="label">Show only products on sale</label>
                            </div>
                            <div class="tf-control-filter d-xl-none">
                                <button type="button" id="filterShop" class="tf-btn-filter">
                                    <span class="icon icon-filter"></span><span class="text">Filter</span>
                                </button>
                            </div>
                            <ul class="tf-control-layout">
                                <li class="tf-view-layout-switch sw-layout-2" data-value-layout="tf-col-2">
                                    <i class="icon-grid-2"></i>
                                </li>
                                <li class="tf-view-layout-switch sw-layout-3 active d-none d-md-flex"
                                    data-value-layout="tf-col-3">
                                    <i class="icon-grid-3"></i>
                                </li>

                            </ul>
                            <div class="tf-control-sorting">
                                <p class="h6 d-none d-lg-block">Sort by:</p>
                                <div class="tf-dropdown-sort" data-bs-toggle="dropdown">
                                    <div class="btn-select">
                                        <span class="text-sort-value">Best Selling</span>
                                        <span class="icon icon-caret-down"></span>
                                    </div>
                                    <div class="dropdown-menu">
                                        <div class="select-item active remove-all-filters"
                                            data-sort-value="best-selling">
                                            <span class="text-value-item">Best Selling</span>
                                        </div>
                                        <div class="select-item" data-sort-value="a-z">
                                            <span class="text-value-item">Alphabetically, A-Z</span>
                                        </div>
                                        <div class="select-item" data-sort-value="z-a">
                                            <span class="text-value-item">Alphabetically, Z-A</span>
                                        </div>
                                        <div class="select-item" data-sort-value="price-low-high">
                                            <span class="text-value-item">Price, low to high</span>
                                        </div>
                                        <div class="select-item" data-sort-value="price-high-low">
                                            <span class="text-value-item">Price, high to low</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="wrapper-control-shop gridLayout-wrapper">
                            <div class="meta-filter-shop">
                                <div id="product-count-grid" class="count-text"></div>
                                <div id="product-count-list" class="count-text"></div>
                                <div id="applied-filters"></div>
                                <button id="remove-all" class="remove-all-filters" style="display: none;">
                                    <i class="icon icon-close"></i>
                                    Clear all</button>
                            </div>
                            <div class="tf-list-layout wrapper-shop" id="listLayout" style="display: none;">
                                <!-- Product 1 -->
                                <div class="card-product product-style_list" data-brand="automet">
                                    <div class="card-product_wrapper">
                                        <a href="product-detail.html" class="product-img">
                                            <img class="lazyload img-product" src="images/products/product-21.jpg"
                                                data-src="images/products/product-21.jpg" alt="Product">
                                            <img class=" lazyload img-hover" src="images/products/product-22.jpg"
                                                data-src="images/products/product-22.jpg" alt="Product">
                                        </a>
                                        <ul class="product-action_list">
                                            <li class="">
                                                <a href="#compare" data-bs-toggle="offcanvas"
                                                    class="hover-tooltip tooltip-left box-icon ">
                                                    <span class="icon icon-compare"></span>
                                                    <span class="tooltip">Compare</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#quickView" data-bs-toggle="modal"
                                                    class="hover-tooltip tooltip-left box-icon">
                                                    <span class="icon icon-view"></span>
                                                    <span class="tooltip">Quick view</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="product-countdown">
                                            <div class="js-countdown cd-has-zero" data-timer="25472"
                                                data-labels="d : ,h : ,m : ,s"></div>
                                        </div>
                                        <ul class="product-badge_list">
                                            <li class="product-badge_item h6 hot">Hot</li>
                                        </ul>
                                    </div>
                                    <div class="card-product_info">
                                        <div class="product-info_list">
                                            <a href="product-detail.html" class="name-product h3 link">SANDAL WOOD - EAU
                                                DE Parfum</a>
                                            <div class="price-wrap">
                                                <span class="price-old h6 fw-normal">Rs.1099</span>
                                                <span class="price-new h6">Rs.1799</span>
                                            </div>
                                            <ul class="product-color_list">
                                                <li
                                                    class="product-color-item color-swatch hover-tooltip tooltip-bot active">
                                                    <span class="tooltip color-filter">Dark</span>
                                                    <span class="swatch-value bg-dark-charcoal"></span>
                                                    <img src="images/products/product-21.jpg"
                                                        data-src="images/products/product-21.jpg" alt="Product">

                                                </li>
                                            </ul>
                                            <div class="product-desc_list d-none d-sm-grid">
                                                <p class="product-desc">
                                                    <span class="headline fw-bold">Contents:</span> Super soft and comfy
                                                    fabric, skin-friendly and
                                                    breathable.
                                                    Womens
                                                    tops dressy casual,
                                                    round neck
                                                    cute lightweight tops，loose fit basic tees
                                                </p>
                                                <p class="product-desc d-none d-md-block">
                                                    <span class="headline fw-bold">Details:</span> Warm up or cool down
                                                    with this essential 3/4 sleeve
                                                    t
                                                    shirts,
                                                    featured
                                                    in an loose fit
                                                    and Pleated sleeve design with sew seaming front for a lived-in
                                                    look.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="product-action_list">
                                            <span class="h6">To buy, select <span
                                                    class="fw-bold text-black">size</span></span>
                                            <div class="group-btn">
                                                <a href="#shoppingCart" data-bs-toggle="offcanvas"
                                                    class="tf-btn animate-btn">
                                                    Add to Cart
                                                    <i class="icon icon-shopping-cart-simple"></i>
                                                </a>
                                                <a href="#" class="tf-btn style-line btn-add-wishlist2">
                                                    <span class="text">Add to List</span>
                                                    <i class="icon icon-heart"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pagination -->
                                <div class="wd-full wg-pagination">
                                    <a href="#" class="pagination-item h6 direct"><i
                                            class="icon icon-caret-left"></i></a>
                                    <a href="#" class="pagination-item h6">1</a>
                                    <span class="pagination-item h6 active">2</span>
                                    <a href="#" class="pagination-item h6">3</a>
                                    <a href="#" class="pagination-item h6">4</a>
                                    <a href="#" class="pagination-item h6">5</a>
                                    <a href="#" class="pagination-item h6 direct"><i
                                            class="icon icon-caret-right"></i></a>
                                </div>
                            </div>
                            <div class="wrapper-shop tf-grid-layout tf-col-3" id="gridLayout">
                                <!-- Product 1 -->
                                <div class="card-product grid" data-brand="fisoew">
                                    <div class="card-product_wrapper">
                                        <a href="product-detail.html" class="product-img">
                                            <img class="lazyload img-product" src="images/perfume/4.webp"
                                                data-src="images/perfume/4.webp" alt="Product">
                                            <img class="lazyload img-hover" src="images/perfume/4.webp"
                                                data-src="images/perfume/4.webp" alt="Product">
                                        </a>
                                        <ul class="product-action_list">
                                            <li>
                                                <a href="#shoppingCart" data-bs-toggle="offcanvas"
                                                    class="hover-tooltip tooltip-left box-icon">
                                                    <span class="icon icon-shopping-cart-simple"></span>
                                                    <span class="tooltip">Add to cart</span>
                                                </a>
                                            </li>
                                            <li class="wishlist">
                                                <a href="javascript:void(0);"
                                                    class="hover-tooltip tooltip-left box-icon">
                                                    <span class="icon icon-heart"></span>
                                                    <span class="tooltip">Add to Wishlist</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#quickView" data-bs-toggle="modal"
                                                    class="hover-tooltip tooltip-left box-icon">
                                                    <span class="icon icon-view"></span>
                                                    <span class="tooltip">Quick view</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <ul class="product-badge_list">
                                            <li class="product-badge_item h6 hot">Hot</li>
                                        </ul>
                                        <!-- <div class="product-countdown">
                                            <div class="js-countdown cd-has-zero" data-timer="25472" data-labels="d : ,h : ,m : ,s"></div>
                                        </div> -->
                                    </div>
                                    <div class="card-product_info">
                                        <a href="product-detail.html" class="name-product h4 link">SANDAL WOOD - EAU DE
                                            Parfum</a>
                                        <div class="price-wrap">
                                            <span class="price-old h6 fw-normal">Rs.1799</span>
                                            <span class="price-new h6">Rs.1099</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-product grid" data-brand="fisoew">
                                    <div class="card-product_wrapper">
                                        <a href="product-detail.html" class="product-img">
                                            <img class="lazyload img-product" src="images/perfume/6.webp"
                                                data-src="images/perfume/6.webp" alt="Product">
                                            <img class="lazyload img-hover" src="images/perfume/6.webp"
                                                data-src="images/perfume/6.webp" alt="Product">
                                        </a>
                                        <ul class="product-action_list">
                                            <li>
                                                <a href="#shoppingCart" data-bs-toggle="offcanvas"
                                                    class="hover-tooltip tooltip-left box-icon">
                                                    <span class="icon icon-shopping-cart-simple"></span>
                                                    <span class="tooltip">Add to cart</span>
                                                </a>
                                            </li>
                                            <li class="wishlist">
                                                <a href="javascript:void(0);"
                                                    class="hover-tooltip tooltip-left box-icon">
                                                    <span class="icon icon-heart"></span>
                                                    <span class="tooltip">Add to Wishlist</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#quickView" data-bs-toggle="modal"
                                                    class="hover-tooltip tooltip-left box-icon">
                                                    <span class="icon icon-view"></span>
                                                    <span class="tooltip">Quick view</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <!-- <ul class="product-badge_list">
                                            <li class="product-badge_item h6 hot">Hot</li>
                                        </ul> -->
                                        <!-- <div class="product-countdown">
                                            <div class="js-countdown cd-has-zero" data-timer="25472" data-labels="d : ,h : ,m : ,s"></div>
                                        </div> -->
                                    </div>
                                    <div class="card-product_info">
                                        <a href="product-detail.html" class="name-product h4 link">NEBULA - EAU DE
                                            Parfum</a>
                                        <div class="price-wrap">
                                            <span class="price-old h6 fw-normal">Rs.999</span>
                                            <span class="price-new h6">Rs.599</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-product grid" data-brand="fisoew">
                                    <div class="card-product_wrapper">
                                        <a href="product-detail.html" class="product-img">
                                            <img class="lazyload img-product" src="images/perfume/3.webp"
                                                data-src="images/perfume/3.webp" alt="Product">
                                            <img class="lazyload img-hover" src="images/perfume/3.webp"
                                                data-src="images/perfume/3.webp" alt="Product">
                                        </a>
                                        <ul class="product-action_list">
                                            <li>
                                                <a href="#shoppingCart" data-bs-toggle="offcanvas"
                                                    class="hover-tooltip tooltip-left box-icon">
                                                    <span class="icon icon-shopping-cart-simple"></span>
                                                    <span class="tooltip">Add to cart</span>
                                                </a>
                                            </li>
                                            <li class="wishlist">
                                                <a href="javascript:void(0);"
                                                    class="hover-tooltip tooltip-left box-icon">
                                                    <span class="icon icon-heart"></span>
                                                    <span class="tooltip">Add to Wishlist</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#quickView" data-bs-toggle="modal"
                                                    class="hover-tooltip tooltip-left box-icon">
                                                    <span class="icon icon-view"></span>
                                                    <span class="tooltip">Quick view</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <!-- <ul class="product-badge_list">
                                            <li class="product-badge_item h6 hot">Hot</li>
                                        </ul> -->
                                        <!-- <div class="product-countdown">
                                            <div class="js-countdown cd-has-zero" data-timer="25472" data-labels="d : ,h : ,m : ,s"></div>
                                        </div> -->
                                    </div>
                                    <div class="card-product_info">
                                        <a href="product-detail.html" class="name-product h4 link">ALTAIR - EAU DE
                                            Parfum</a>
                                        <div class="price-wrap">
                                            <span class="price-old h6 fw-normal">Rs.999</span>
                                            <span class="price-new h6">Rs.599</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-product grid" data-brand="fisoew">
                                    <div class="card-product_wrapper">
                                        <a href="product-detail.html" class="product-img">
                                            <img class="lazyload img-product" src="images/perfume/5.webp"
                                                data-src="images/perfume/5.webp" alt="Product">
                                            <img class="lazyload img-hover" src="images/perfume/5.webp"
                                                data-src="images/perfume/5.webp" alt="Product">
                                        </a>
                                        <ul class="product-action_list">
                                            <li>
                                                <a href="#shoppingCart" data-bs-toggle="offcanvas"
                                                    class="hover-tooltip tooltip-left box-icon">
                                                    <span class="icon icon-shopping-cart-simple"></span>
                                                    <span class="tooltip">Add to cart</span>
                                                </a>
                                            </li>
                                            <li class="wishlist">
                                                <a href="javascript:void(0);"
                                                    class="hover-tooltip tooltip-left box-icon">
                                                    <span class="icon icon-heart"></span>
                                                    <span class="tooltip">Add to Wishlist</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#quickView" data-bs-toggle="modal"
                                                    class="hover-tooltip tooltip-left box-icon">
                                                    <span class="icon icon-view"></span>
                                                    <span class="tooltip">Quick view</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <!-- <ul class="product-badge_list">
                                            <li class="product-badge_item h6 hot">Hot</li>
                                        </ul> -->
                                        <!-- <div class="product-countdown">
                                            <div class="js-countdown cd-has-zero" data-timer="25472" data-labels="d : ,h : ,m : ,s"></div>
                                        </div> -->
                                    </div>
                                    <div class="card-product_info">
                                        <a href="product-detail.html" class="name-product h4 link">OUDH SENSATION - EAU
                                            DE Parfum</a>
                                        <div class="price-wrap">
                                            <span class="price-old h6 fw-normal">Rs.1799</span>
                                            <span class="price-new h6">Rs.1099</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-product grid" data-brand="fisoew">
                                    <div class="card-product_wrapper">
                                        <a href="product-detail.html" class="product-img">
                                            <img class="lazyload img-product" src="images/perfume/1.webp"
                                                data-src="images/perfume/1.webp" alt="Product">
                                            <img class="lazyload img-hover" src="images/perfume/1.webp"
                                                data-src="images/perfume/1.webp" alt="Product">
                                        </a>
                                        <ul class="product-action_list">
                                            <li>
                                                <a href="#shoppingCart" data-bs-toggle="offcanvas"
                                                    class="hover-tooltip tooltip-left box-icon">
                                                    <span class="icon icon-shopping-cart-simple"></span>
                                                    <span class="tooltip">Add to cart</span>
                                                </a>
                                            </li>
                                            <li class="wishlist">
                                                <a href="javascript:void(0);"
                                                    class="hover-tooltip tooltip-left box-icon">
                                                    <span class="icon icon-heart"></span>
                                                    <span class="tooltip">Add to Wishlist</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#quickView" data-bs-toggle="modal"
                                                    class="hover-tooltip tooltip-left box-icon">
                                                    <span class="icon icon-view"></span>
                                                    <span class="tooltip">Quick view</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <!-- <ul class="product-badge_list">
                                            <li class="product-badge_item h6 hot">Hot</li>
                                        </ul> -->
                                        <!-- <div class="product-countdown">
                                            <div class="js-countdown cd-has-zero" data-timer="25472" data-labels="d : ,h : ,m : ,s"></div>
                                        </div> -->
                                    </div>
                                    <div class="card-product_info">
                                        <a href="product-detail.html" class="name-product h4 link">KEPLER - EAU DE
                                            Parfum</a>
                                        <div class="price-wrap">
                                            <span class="price-old h6 fw-normal">Rs.999</span>
                                            <span class="price-new h6">Rs.599</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-product grid" data-brand="fisoew">
                                    <div class="card-product_wrapper">
                                        <a href="product-detail.html" class="product-img">
                                            <img class="lazyload img-product" src="images/perfume/2.webp"
                                                data-src="images/perfume/2.webp" alt="Product">
                                            <img class="lazyload img-hover" src="images/perfume/2.webp"
                                                data-src="images/perfume/2.webp" alt="Product">
                                        </a>
                                        <ul class="product-action_list">
                                            <li>
                                                <a href="#shoppingCart" data-bs-toggle="offcanvas"
                                                    class="hover-tooltip tooltip-left box-icon">
                                                    <span class="icon icon-shopping-cart-simple"></span>
                                                    <span class="tooltip">Add to cart</span>
                                                </a>
                                            </li>
                                            <li class="wishlist">
                                                <a href="javascript:void(0);"
                                                    class="hover-tooltip tooltip-left box-icon">
                                                    <span class="icon icon-heart"></span>
                                                    <span class="tooltip">Add to Wishlist</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#quickView" data-bs-toggle="modal"
                                                    class="hover-tooltip tooltip-left box-icon">
                                                    <span class="icon icon-view"></span>
                                                    <span class="tooltip">Quick view</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <ul class="product-badge_list">
                                            <li class="product-badge_item h6 new">New arrival</li>
                                        </ul>
                                        <!-- <div class="product-countdown">
                                            <div class="js-countdown cd-has-zero" data-timer="25472" data-labels="d : ,h : ,m : ,s"></div>
                                        </div> -->
                                    </div>
                                    <div class="card-product_info">
                                        <a href="product-detail.html" class="name-product h4 link">AQUILA - EAU DE
                                            Parfum</a>
                                        <div class="price-wrap">
                                            <span class="price-old h6 fw-normal">Rs.999</span>
                                            <span class="price-new h6">Rs.599</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Pagination -->
                                <div class="wd-full wg-pagination m-0 justify-content-center">
                                    <a href="#" class="pagination-item h6 direct"><i
                                            class="icon icon-caret-left"></i></a>
                                    <a href="#" class="pagination-item h6">1</a>
                                    <span class="pagination-item h6 active">2</span>
                                    <a href="#" class="pagination-item h6">3</a>
                                    <a href="#" class="pagination-item h6">4</a>
                                    <a href="#" class="pagination-item h6">5</a>
                                    <a href="#" class="pagination-item h6 direct"><i
                                            class="icon icon-caret-right"></i></a>
                                </div>
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
    <div class="offcanvas offcanvas-start canvas-mb" id="mobileMenu">
        <span class="icon-close-popup" data-bs-dismiss="offcanvas">
            <i class="icon-close"></i>
        </span>
        <div class="canvas-header">
            <p class="text-logo-mb"><img src="images/logo/logo.webp" data-src="images/logo/logo.webp"></p>
            <a href="#" class="tf-btn type-small style-2">
                Login
                <i class="icon icon-user"></i>
            </a>
            <span class="br-line"></span>
        </div>
        <div class="canvas-body">
            <div class="mb-content-top">
                <ul class="nav-ul-mb" id="wrapper-menu-navigation"></ul>
            </div>
            <div class="group-btn">
                <a href="#" class="tf-btn type-small style-2">
                    Wishlist
                    <i class="icon icon-heart"></i>
                </a>
                <div data-bs-dismiss="offcanvas">
                    <a href="#" data-bs-toggle="modal" class="tf-btn type-small style-2">
                        Search
                        <i class="icon icon-magnifying-glass"></i>
                    </a>
                </div>
            </div>
            <div class="flow-us-wrap">
                <h5 class="title">Follow us on</h5>
                <ul class="tf-social-icon">
                    <li>
                        <a href="https://www.facebook.com/" target="_blank" class="social-facebook">
                            <span class="icon"><i class="icon-fb"></i></span>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.instagram.com/" target="_blank" class="social-instagram">
                            <span class="icon"><i class="icon-instagram-logo"></i></span>
                        </a>
                    </li>
                    <li>
                        <a href="https://x.com/" target="_blank" class="social-x">
                            <span class="icon"><i class="icon-x"></i></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Mobile Menu -->

    <!-- Javascript -->
    @include('components.frontend.main-js')

</body>

</html>