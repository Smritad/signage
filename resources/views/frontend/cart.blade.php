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
                    <h1 class="title-page">Shopping Cart</h1>
                    <ul class="breadcrumbs-page">
                        <li><a href="{{ route('frontend.index') }}" class="h6 link">Home</a></li>
                        <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                        <li>
                            <h6 class="current-page fw-normal">Shopping Cart</h6>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
        <!-- /Page Title -->
        <!-- View Cart -->
        <div class="flat-spacing each-list-prd">
            <div class="container">
                <div class="row">
                    <div class="col-xxl-9 col-xl-8">
                        <div class="tf-cart-sold">
                          
                        </div>
                        <form>
                                <table class="tf-table-page-cart">
                                    <thead>
                                        <tr>
                                            <th class="h6">Product</th>
                                            <th class="h6">Price</th>
                                            <th class="h6">Quantity</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($carts as $cart)
                                            @php
                                $category = \App\Models\CategoryDetails::find($cart->category_id);
                                $subcategory = \App\Models\SabCategoryDetails::find($cart->sub_category_id);

                                // Use fallback strings if null to avoid missing parameter error
                                $catSlug = $category->slug ?? 'category';
                                $subcatSlug = $subcategory->slug ?? 'subcategory';

                                $productUrl = route('product.details', [
                                    'cat' => $catSlug,
                                    'sabcat' => $subcatSlug,
                                    'slug' => $cart->slug
                                ]);
                            @endphp


                            <tr class="tf-cart_item each-prd" 
                                data-cart-id="{{ $cart->id }}" 
                                data-price="{{ $cart->offer_price ?? $cart->price }}">

                                                <td>
                                                    <div class="cart_product">
                                                        <a href="{{ $productUrl }}" class="img-prd">
                            @php
                                $images = json_decode($cart->images, true); // decode JSON to array
                                $firstImage = $images[0] ?? 'placeholder.jpg'; // first image or fallback
                            @endphp

                            <img class="lazyload" src="{{ asset('signage/home/productimage/' . $firstImage) }}" alt="{{ $cart->product_name }}">
                            </a>



                           <div class="infor-prd">
    <h6 class="prd_name">
        <a href="{{ $productUrl }}" class="link">
            {{ $cart->product_name }}
        </a>
        <!-- Hidden input to store product ID -->
        <input type="text" name="product_ids[]" value="{{ $cart->product_id }}">
    </h6>
</div>

                        </div>
                    </td>
                    
                    @php
                            $unitPrice = $cart->offer_price ?? $cart->price;
                            $totalPrice = $unitPrice * $cart->quantity;
                        @endphp
                        <td class="cart_price h6 each-price">Rs. {{ number_format($totalPrice, 2) }}</td>                   
                         <td class="cart_quantity">
                        <div class="wg-quantity">
                            <button class="btn-quantity minus-quantity" type="button" data-cart-id="{{ $cart->id }}">
                                <i class="icon-minus fs-14"></i>
                            </button>
                            <input class="quantity-product" type="text" name="quantity" value="{{ $cart->quantity }}" data-cart-id="{{ $cart->id }}">
                            <button class="btn-quantity plus-quantity" type="button" data-cart-id="{{ $cart->id }}">
                                <i class="icon-plus fs-14"></i>
                            </button>
                        </div>
                    </td>
                    <td class="cart_remove remove link" data-cart-id="{{ $cart->id }}">
                        <i class="icon icon-close"></i>
                    </td>
                    
                </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Your cart is empty.</td>
                        </tr>
                    @endforelse
                </tbody>
                        </table>

                    </form>

                    </div>
                    <div class="col-xxl-3 col-xl-4">
                        <div class="fl-sidebar-cart bg-white-smoke sticky-top">
                            <div class="box-order-summary">
                                <h4 class="title fw-semibold">Order Summary</h4>
                                <div class="subtotal h6 text-button d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold">Subtotal</h6>
                                    <span id="cart-subtotal" class="total">Rs. 0</span>
                                </div>
                                <div class="discount text-button d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold">Discounts</h6>
                                    <span id="cart-discount" class="total h6">Rs. 0</span>
                                </div>
                                <div class="ship">
                                    <h6 class="fw-bold">Shipping</h6>
                                    <div class="flex-grow-1">
                                        <fieldset class="ship-item">
                                            <input type="radio" name="ship-check" class="tf-check-rounded" id="free" checked>
                                            <label class="h6" for="free">
                                                <span>Free Shipping</span>
                                                <span id="cart-shipping" class="price">Rs. 0</span>
                                            </label>
                                        </fieldset>
                                    </div>
                                </div>
                                <h5 class="total-order d-flex justify-content-between align-items-center">
                                    <span>Total</span>
                                    <span id="cart-total" class="total">Rs. 0</span>
                                </h5>
                                <div class="list-ver">
                                   <a href="#" id="proceed-to-checkout" class="tf-btn w-100 animate-btn">
                                        Process to checkout
                                        <i class="icon icon-arrow-right"></i>
                                    </a>

                                    <a href="{{ route('frontend.index') }}" class="tf-btn btn-white animate-btn animate-dark w-100">
                                        Continue shopping
                                        <i class="icon icon-arrow-right"></i>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function(){

    function updateRowPrice(row){
        let quantity = parseInt(row.find('.quantity-product').val()) || 1;
        let unitPrice = parseFloat(row.data('price')) || 0;
        let totalPrice = unitPrice * quantity;
        row.find('.each-price').text('Rs. ' + totalPrice.toFixed(2));
        updateSummary();
    }

    function updateSummary(){
        let subtotal = 0;
        $(".tf-cart_item").each(function(){
            let quantity = parseInt($(this).find('.quantity-product').val()) || 1;
            let unitPrice = parseFloat($(this).data('price')) || 0;
            subtotal += (unitPrice * quantity);
        });

        let discount = 0; // 👉 you can calculate based on offers
        let shipping = 0; // 👉 free shipping for now
        let total = subtotal - discount + shipping;

        $("#cart-subtotal").text("Rs. " + subtotal.toFixed(2));
        $("#cart-discount").text("Rs. " + discount.toFixed(2));
        $("#cart-shipping").text("Rs. " + shipping.toFixed(2));
        $("#cart-total").text("Rs. " + total.toFixed(2));
    }

    // Plus
    $(document).on('click', '.plus-quantity', function(){
        let row = $(this).closest('.tf-cart_item');
        let input = row.find('.quantity-product');
        let quantity = parseInt(input.val()) + 1;
        input.val(quantity);
        updateRowPrice(row);
    });

    // Minus
    $(document).on('click', '.minus-quantity', function(){
        let row = $(this).closest('.tf-cart_item');
        let input = row.find('.quantity-product');
        let quantity = Math.max(parseInt(input.val()) - 1, 1);
        input.val(quantity);
        updateRowPrice(row);
    });

    // Manual change
    $(document).on('change', '.quantity-product', function(){
        let row = $(this).closest('.tf-cart_item');
        let quantity = Math.max(parseInt($(this).val()) || 1, 1);
        $(this).val(quantity);
        updateRowPrice(row);
    });

    // Remove cart row
   // Remove cart row
$(document).on('click', '.cart_remove', function(){
    let row = $(this).closest('.tf-cart_item');
    let cartId = row.data('cart-id');

    // Remove row from UI
    row.remove();
    updateSummary();

    // Backend sync
    fetch("{{ route('cart.remove') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "X-Requested-With": "XMLHttpRequest"
        },
        body: JSON.stringify({ cart_id: cartId })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            $('.cart-count').text(data.cart_count || 0);

            // reload if empty cart
            if(data.cart_count == 0){
                location.reload();
            }

            // ✅ show Notyf message instead of Toastr
            notyf.open({
                type: 'custom-success',
                message: data.message || 'Product removed from cart!'
            });
        } else {
            notyf.error(data.message || 'Failed to remove item.');
        }
    })
    .catch(() => {
        notyf.error('Something went wrong. Try again.');
    });
});

    // Initial calculation on page load
    updateSummary();
});
</script>
<script>
$(document).ready(function(){

    // Initialize Notyf
    const notyf = new Notyf({
        duration: 3000,
        position: { x: 'right', y: 'top' },
        dismissible: true
    });

    $('#proceed-to-checkout').on('click', function(e){
        e.preventDefault();

        const cartData = [];

        $('.tf-cart_item').each(function(){
            const row = $(this);
            cartData.push({
                id: row.find('input[name="product_ids[]"]').val(), // get product ID from hidden input
                product_name: row.find('.prd_name a').text().trim(),
                quantity: parseInt(row.find('.quantity-product').val()) || 1,
                price: parseFloat(row.data('price')) || 0,
                image: row.find('img').attr('src')
            });
        });

        fetch('{{ route("cart.storeCheckoutData") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ cart: cartData })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                // Show success notification
                notyf.success('Products added to cart successfully!');

                // Redirect to checkout after short delay
                setTimeout(() => {
                    window.location.href = "{{ route('show.checkout') }}";
                }, 1000);

            } else {
                notyf.error('Failed to process checkout.');
            }
        })
        .catch(err => {
            console.error(err);
            notyf.error('Something went wrong.');
        });
    });

});
</script>



        <!-- /View Cart -->
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