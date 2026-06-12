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
        <div class="preload-logo"><div class="spinner"></div></div>
    </div>

    <div id="wrapper">
        @include('components.frontend.header')

        {{-- Page Title --}}
        <section class="s-page-title">
            <div class="container">
                <div class="content">
                    <h1 class="title-page">Wishlist</h1>
                    <ul class="breadcrumbs-page">
                        <li><a href="{{ route('frontend.index') }}" class="h6 link">Home</a></li>
                        <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                        <li><h6 class="current-page fw-normal">Wishlist</h6></li>
                    </ul>
                </div>
            </div>
        </section>

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <div class="flat-spacing">
            <div class="container">
                @if($wishlist->count() > 0)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <label class="form-check-label">
                                <input type="checkbox" id="select-all" class="form-check-input me-2">
                                Select All
                            </label>
                        </div>

                        <div class="d-flex gap-2">
                            <button id="delete-selected" class="btn btn-danger btn-sm">
                                <i class="icon icon-trash"></i> Delete Selected
                            </button>

                            <button id="move-to-cart" class="btn btn-success btn-sm">
                                <i class="icon icon-shopping-cart"></i> Move Selected to Cart
                            </button>
                        </div>
                    </div>
                @endif

                <div class="tf-grid-layout tf-col-2 md-col-3 xl-col-4 wrapper-wishlist">
                    @forelse($wishlist as $item)
                        @php
                            $product = $item->product;
                            if(!$product) continue;

                            $images     = json_decode($product->images, true) ?? ['default.png'];
                            $firstImage = $images[0];

                            $subCategory = $product->sub_category_id ? \App\Models\SabCategoryDetails::find($product->sub_category_id) : null;
                            $category    = $product->category_id ? \App\Models\CategoryDetails::find($product->category_id) : null;

                            $productUrl = route('product.details', [
                                'cat'    => $category?->slug    ?? 'default-cat',
                                'sabcat' => $subCategory?->slug ?? 'default-sabcat',
                                'slug'   => $product->slug
                            ]);
                        @endphp

                        <div class="card-product grid style-2 has-size position-relative" id="wishlist-item-{{ $item->id }}">
                            {{-- Checkbox --}}
                            <div class="form-check position-absolute" style="top:10px; left:10px; z-index:999;">
                                <input type="checkbox" class="form-check-input wishlist-checkbox" value="{{ $item->id }}">
                            </div>

                            <div class="card-product_wrapper">
                                <a href="{{ $productUrl }}" class="product-img">
                                    <img class="lazyload img-product"
                                         src="{{ asset('signage/home/productimage/' . $firstImage) }}"
                                         alt="{{ $product->product_name }}">
                                    <img class="lazyload img-hover"
                                         src="{{ asset('signage/home/productimage/' . $firstImage) }}"
                                         alt="{{ $product->product_name }}">
                                </a>

                                <span class="remove box-icon remove-wishlist" data-id="{{ $item->id }}">
                                    <i class="icon icon-trash"></i>
                                </span>

                                @if($product->is_bestseller || $product->is_new_arrival)
                                    <div class="product-badges mt-2">
                                        @if($product->is_bestseller)
                                            <span class="product-badge_item bestseller">Bestseller</span>
                                        @endif
                                        @if($product->is_new_arrival)
                                            <span class="product-badge_item new-arrival">New Arrival</span>
                                        @endif
                                    </div>
                                @endif

                                <ul class="product-action_list">
                                    <li>
                                        {{-- Cart form: NEW hidden inputs tell the controller to remove from wishlist --}}
                                        <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form d-inline" data-wishlist-item="{{ $item->id }}">
                                            @csrf
                                            <input type="hidden" name="product_id"       value="{{ $product->id }}">
                                            <input type="hidden" name="from_wishlist"    value="1">
                                            <input type="hidden" name="wishlist_item_id" value="{{ $item->id }}">
                                            <button type="submit" class="hover-tooltip box-icon">
                                                <span class="icon icon-shopping-cart-simple"></span>
                                                <span class="tooltip">Move to cart</span>
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>

                            <div class="card-product_info">
                                <a href="{{ $productUrl }}" class="name-product h4 link">{{ $product->product_name }}</a>
                                <div class="price-wrap">
                                    @if($product->offer_price && $product->offer_price < $product->price)
                                        <span class="price-old h6 fw-normal">Rs.{{ number_format($product->price, 2) }}</span>
                                        <span class="price-new h6">Rs.{{ number_format($product->offer_price, 2) }}</span>
                                    @else
                                        <span class="price-new h6">Rs.{{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <!--<p class="text-center py-5 text-muted">Your wishlist is empty.</p>-->
                    @endforelse
                </div>
            </div>
        </div>

        @include('components.frontend.footer')
    </div>

    {{-- Mobile Menu --}}
    <div class="offcanvas offcanvas-start canvas-mb" id="mobileMenu">
        <span class="icon-close-popup" data-bs-dismiss="offcanvas"><i class="icon-close"></i></span>
        <div class="canvas-header">
            <p class="text-logo-mb"><img src="images/logo/logo.webp" data-src="images/logo/logo.webp"></p>
            <a href="#" class="tf-btn type-small style-2">Login <i class="icon icon-user"></i></a>
            <span class="br-line"></span>
        </div>
        <div class="canvas-body">
            <div class="mb-content-top">
                <ul class="nav-ul-mb" id="wrapper-menu-navigation"></ul>
            </div>
            <div class="group-btn">
                <a href="#" class="tf-btn type-small style-2">Wishlist <i class="icon icon-heart"></i></a>
                <div data-bs-dismiss="offcanvas">
                    <a href="#" data-bs-toggle="modal" class="tf-btn type-small style-2">Search <i class="icon icon-magnifying-glass"></i></a>
                </div>
            </div>
            <div class="flow-us-wrap">
                <h5 class="title">Follow us on</h5>
                <ul class="tf-social-icon">
                    <li><a href="https://www.facebook.com/" target="_blank" class="social-facebook"><span class="icon"><i class="icon-fb"></i></span></a></li>
                    <li><a href="https://www.instagram.com/" target="_blank" class="social-instagram"><span class="icon"><i class="icon-instagram-logo"></i></span></a></li>
                    <li><a href="https://x.com/" target="_blank" class="social-x"><span class="icon"><i class="icon-x"></i></span></a></li>
                </ul>
            </div>
        </div>
    </div>

    @include('components.frontend.main-js')

    {{-- Select All --}}
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('select-all')?.addEventListener('change', function () {
            const checked = this.checked;
            document.querySelectorAll('.wishlist-checkbox').forEach(cb => cb.checked = checked);
        });

        document.getElementById('delete-selected')?.addEventListener('click', function () {
            const selected = Array.from(document.querySelectorAll('.wishlist-checkbox:checked'))
                                  .map(cb => cb.value);

            if (selected.length === 0) {
                alert('Please select at least one item to delete.');
                return;
            }

            if (!confirm('Are you sure you want to delete the selected items?')) return;

            fetch("{{ route('wishlist.bulkDelete') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ ids: selected }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    selected.forEach(id => document.getElementById('wishlist-item-' + id)?.remove());
                    alert('Selected items deleted successfully.');
                } else {
                    alert('Failed to delete selected items.');
                }
            })
            .catch(() => alert('Something went wrong.'));
        });
    });
    </script>

    {{-- Single "Move to Cart" icon click — removes from wishlist card immediately --}}
    <script>
    $(document).ready(function() {
        $(document).on('submit', '.add-to-cart-form', function(e) {
            e.preventDefault();
            let form           = $(this);
            let wishlistItemId = form.data('wishlist-item');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(res) {
                    if (res.success) {
                        notyf.open({ type: 'success', message: res.message });

                        $('#cart-count').text(res.cart_count);
                        if (res.wishlist_count !== undefined) {
                            $('.wishlist-count').text(res.wishlist_count);
                        }

                        // If the wishlist item was removed, fade & remove the card
                        if (res.wishlist_removed && wishlistItemId) {
                            $('#wishlist-item-' + wishlistItemId).fadeOut(300, function () {
                                $(this).remove();

                                // If no wishlist items left, reload to show empty state
                                if ($('.wrapper-wishlist .card-product').length === 0) {
                                    setTimeout(() => location.reload(), 400);
                                }
                            });
                        } else {
                            setTimeout(() => { location.reload(); }, 500);
                        }
                    } else {
                        notyf.error(res.message || 'Something went wrong!');
                    }
                },
                error: function() { notyf.error('AJAX request failed.'); }
            });
        });
    });
    </script>

    {{-- Remove-wishlist trash icon --}}
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".remove-wishlist").forEach(function (btn) {
            btn.addEventListener("click", function () {
                let id = this.dataset.id;

                fetch("/signage/wishlist/remove/" + id, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        "Accept": "application/json",
                    },
                })
                .then(res => res.json())
                .then(data => { location.reload(); })
                .catch(err => console.error(err));
            });
        });
    });
    </script>

    {{-- Move Selected to Cart (bulk) --}}
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const moveBtn = document.getElementById('move-to-cart');
        if (!moveBtn) return;

        moveBtn.addEventListener('click', function () {
            const selected = Array.from(document.querySelectorAll('.wishlist-checkbox:checked'))
                .map(cb => cb.value);

            if (selected.length === 0) {
                alert('Please select at least one item to move to cart.');
                return;
            }

            if (!confirm('Add selected items to cart?')) return;

            fetch("{{ route('wishlist.bulkAddToCart') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ ids: selected }),
            })
            .then(async (res) => {
                let data;
                try { data = await res.json(); }
                catch (err) { throw new Error("Invalid JSON response from server"); }

                if (!res.ok) throw new Error(data.message || 'Server error');

                if (data.success) {
                    notyf.open({ type: 'success', message: data.message });
                    const cartCountEl = document.getElementById('cart-count');
                    if (cartCountEl) cartCountEl.innerText = data.cart_count;
                    setTimeout(() => location.reload(), 800);
                } else {
                    notyf.error(data.message || 'Failed to add to cart.');
                }
            })
            .catch((err) => {
                console.error('Error:', err);
                notyf.error(err.message || 'Something went wrong.');
            });
        });
    });
    </script>

</body>
</html>