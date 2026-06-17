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

        <section class="s-page-title">
            <div class="container">
                <div class="content">
                    <h1 class="title-page">Shopping Cart</h1>
                    <ul class="breadcrumbs-page">
                        <li><a href="{{ route('frontend.index') }}" class="h6 link">Home</a></li>
                        <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                        <li><h6 class="current-page fw-normal">Shopping Cart</h6></li>
                    </ul>
                </div>
            </div>
        </section>

        <div class="flat-spacing each-list-prd">
            <div class="container">
                <div class="row">

                    {{-- LEFT: cart table --}}
                    <div class="col-xxl-7 col-xl-8 custom-cart-box">
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

                                @php
                                    $offerRows  = $carts->where('combo', 'offer');
                                    $normalRows = $carts->where('combo', '!=', 'offer');

                                    function resolveProductImage(string $raw): string
                                    {
                                        $raw = trim($raw);
                                        if (empty($raw)) return asset('images/no-image.png');
                                        $decoded = json_decode($raw, true);
                                        if (is_string($decoded)) $decoded = json_decode($decoded, true);
                                        $filename = null;
                                        if (is_array($decoded) && !empty($decoded[0])) {
                                            $filename = $decoded[0];
                                        } elseif (is_string($decoded) && !empty($decoded)) {
                                            $filename = $decoded;
                                        } else {
                                            $filename = $raw;
                                        }
                                        $filename = trim($filename, '"\'');
                                        if (empty($filename)) return asset('images/no-image.png');
                                        return asset('signage/home/productimage/' . $filename);
                                    }

                                    function resolveOfferImage(string $raw): string
                                    {
                                        $raw = trim($raw);
                                        if (empty($raw)) return asset('images/no-image.png');
                                        $decoded = json_decode($raw, true);
                                        if (is_string($decoded)) {
                                            $filename = $decoded;
                                        } elseif (is_array($decoded) && !empty($decoded[0])) {
                                            $filename = $decoded[0];
                                        } else {
                                            $filename = $raw;
                                        }
                                        $filename = trim($filename, '"\'');
                                        if (empty($filename)) return asset('images/no-image.png');
                                        return asset('offerimage/' . $filename);
                                    }
                                @endphp

                                {{-- OFFER / BUNDLE ROWS --}}
                                @if($offerRows->count() > 0)

                                    <tr>
                                        <td colspan="4" class="cart-section-label">Offer Bundles</td>
                                    </tr>

                                    @foreach($offerRows as $cart)
                                        @php
                                            $offerImageUrl = resolveOfferImage((string)($cart->images ?? ''));
                                            $mrpTotal      = (float) $cart->price;
                                            $finalPrice    = (float) ($cart->offer_price ?? $cart->price);
                                            $savings       = max(0, $mrpTotal - $finalPrice);
                                            $realOfferId   = (int) ($cart->offer_id ?? 0);

                                            // ✅ Fetch slug from offers table using offer_id
                                            $offerSlug = null;
                                            if ($realOfferId > 0) {
                                                $offerSlug = \App\Models\Offer::where('id', $realOfferId)->value('slug');
                                            }
                                            // Fallback: use cart slug if offer slug not found
                                            if (empty($offerSlug)) {
                                                $offerSlug = $cart->slug ?? null;
                                            }

                                            $includedItems = [];
                                            if (!empty($cart->combo_text)) {
                                                $decoded = json_decode($cart->combo_text, true);
                                                if (is_array($decoded)) $includedItems = $decoded;
                                            }
                                        @endphp

                                        <tr class="tf-cart_item"
                                            data-cart-id="{{ $cart->id }}"
                                            data-price="{{ $finalPrice }}"
                                            data-mrp="{{ $mrpTotal }}"
                                            data-offer-id="{{ $realOfferId }}"
                                            data-offer-slug="{{ $offerSlug }}"
                                            data-offer-name="{{ e($cart->product_name) }}"
                                            data-offer-image="{{ $offerImageUrl }}"
                                            data-combo="offer">

                                            <td>
                                                <div class="cart_product">
                                                    @if(!empty($offerSlug))
                                                        <a href="{{ route('crazy.show', $offerSlug) }}" class="img-prd">
                                                    @else
                                                        <span class="img-prd">
                                                    @endif
                                                        <img src="{{ $offerImageUrl }}"
                                                             alt="{{ e($cart->product_name) }}"
                                                             onerror="this.src='{{ asset('images/no-image.png') }}'">
                                                    @if(!empty($offerSlug))
                                                        </a>
                                                    @else
                                                        </span>
                                                    @endif

                                                    <div class="infor-prd">
                                                        <h6 class="prd_name">
                                                            @if(!empty($offerSlug))
                                                                <a href="{{ route('crazy.show', $offerSlug) }}">{{ $cart->product_name }}</a>
                                                            @else
                                                                {{ $cart->product_name }}
                                                            @endif

                                                            @if(!empty($includedItems))
                                                                <small class="text-muted d-block mt-1">Bundle includes:</small>
                                                                <ul class="cart-bundle-list">
                                                                    @foreach($includedItems as $it)
                                                                        <li>
                                                                            {{ $it['name'] ?? '' }}
                                                                            @if(!empty($it['unit'])) ({{ $it['unit'] }}) @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="cart_price h6 each-price">
                                                @if($savings > 0)
                                                    <span class="price-original">₹ {{ number_format($mrpTotal, 0) }}</span>
                                                @endif
                                                <span class="price-final">₹ {{ number_format($finalPrice, 0) }}</span>
                                            </td>

                                            <td class="cart_quantity">
                                                <span class="bundle-qty-val">1</span>
                                                <span class="bundle-qty-label">Bundle</span>
                                            </td>

                                            <td class="cart_remove remove link">
                                                <i class="icon icon-close"></i>
                                            </td>
                                        </tr>
                                    @endforeach

                                @endif

                                {{-- NORMAL PRODUCT ROWS --}}
                                @if($normalRows->count() > 0)

                                    @if($offerRows->count() > 0)
                                        <tr>
                                            <td colspan="4" class="cart-section-label normal">Products</td>
                                        </tr>
                                    @endif

                                    @foreach($normalRows as $cart)
                                        @php
                                            $category    = \App\Models\CategoryDetails::find($cart->category_id);
                                            $subcategory = \App\Models\SabCategoryDetails::find($cart->sub_category_id);
                                            $catSlug     = $category->slug    ?? 'category';
                                            $subcatSlug  = $subcategory->slug ?? 'subcategory';

                                            $productUrl = route('product.details', [
                                                'cat'    => $catSlug,
                                                'sabcat' => $subcatSlug,
                                                'slug'   => $cart->slug,
                                            ]);

                                            // ── Offer-aware pricing ──
                                            $mrp             = (float) $cart->price;
                                            $hasOffer        = !empty($cart->offer_price) && (float) $cart->offer_price < $mrp;
                                            $unitPrice       = $hasOffer ? (float) $cart->offer_price : $mrp;
                                            $discountPercent = ($hasOffer && $mrp > 0)
                                                                ? (int) round((($mrp - $unitPrice) / $mrp) * 100)
                                                                : 0;

                                            // Line totals for the current saved quantity
                                            $qtyNow    = (int) $cart->quantity;
                                            $lineFinal = $unitPrice * $qtyNow;
                                            $lineMrp   = $mrp * $qtyNow;

                                            $productImageUrl = resolveProductImage((string)($cart->images ?? ''));
                                            $productQuantity = \App\Models\ProductsDetails::where('id', $cart->product_id)->value('quantity') ?? 0;
                                        @endphp

                                        <tr class="tf-cart_item"
                                            data-cart-id="{{ $cart->id }}"
                                            data-price="{{ $unitPrice }}"
                                            data-mrp="{{ $mrp }}"
                                            data-product-id="{{ $cart->product_id }}"
                                            data-product-image="{{ $productImageUrl }}"
                                            data-combo="no">

                                            <td>
                                                <div class="cart_product">
                                                    <a href="{{ $productUrl }}" class="img-prd">
                                                        <img src="{{ $productImageUrl }}"
                                                             alt="{{ e($cart->product_name) }}"
                                                             onerror="this.src='{{ asset('images/no-image.png') }}'">
                                                    </a>
                                                    <div class="infor-prd">
                                                        <h6 class="prd_name">
                                                            <a href="{{ $productUrl }}">{{ $cart->product_name }}</a>
                                                            <small class="d-block text-muted">
                                                                @if($hasOffer)
                                                                    <span class="price-original">₹ {{ number_format($mrp, 0) }}</span>
                                                                    <span class="price-final">₹ {{ number_format($unitPrice, 0) }}</span>
                                                                    <span class="text-danger">({{ $discountPercent }}% OFF)</span>
                                                                @else
                                                                    ₹ {{ number_format($unitPrice, 0) }}
                                                                @endif
                                                            </small>
                                                            <input type="hidden" name="product_ids[]" value="{{ $cart->product_id }}">
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- each-price shows the LINE total; price-original is struck MRP --}}
                                            <td class="cart_price h6 each-price">
                                                @if($hasOffer)
                                                    <span class="price-original">₹ {{ number_format($lineMrp, 0) }}</span>
                                                @endif
                                                <span class="price-final">₹ {{ number_format($lineFinal, 0) }}</span>
                                            </td>

                                            <td class="cart_quantity">
                                                <div class="wg-quantity">
                                                    <button class="btn-quantity minus-quantity" type="button">
                                                        <i class="icon-minus fs-14"></i>
                                                    </button>
                                                    <input class="quantity-product"
                                                           type="text"
                                                           value="{{ $cart->quantity }}"
                                                           data-max="{{ $productQuantity }}">
                                                    <button class="btn-quantity plus-quantity" type="button">
                                                        <i class="icon-plus fs-14"></i>
                                                    </button>
                                                </div>
                                            </td>

                                            <td class="cart_remove remove link">
                                                <i class="icon icon-close"></i>
                                            </td>
                                        </tr>
                                    @endforeach

                                @endif

                                @if($carts->count() == 0)
                                    <tr>
                                        <td colspan="4" class="text-center py-4">Your cart is empty.</td>
                                    </tr>
                                @endif

                                </tbody>
                            </table>
                        </form>
                    </div>

                    {{-- RIGHT: Order summary --}}
                    <div class="col-xxl-5 col-xl-4">
                        <div class="fl-sidebar-cart bg-white-smoke sticky-top">
                            <div class="box-order-summary p-3 rounded shadow-sm">
                                <h4 class="title fw-semibold mb-3">Order Summary</h4>

                                <div class="offer-summary mb-3"></div>

                                <div id="offer-total-box" class="border rounded p-2 mb-3 bg-light" style="display:none;">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-muted small">Bundle MRP</span>
                                        <span id="offer-mrp" class="text-muted small price-original">₹ 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-danger fw-semibold small">You Save</span>
                                        <span id="offer-savings" class="text-danger small">- ₹ 0</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="fw-bold mb-0">Bundle Subtotal</h6>
                                        <span id="offer-subtotal" class="fw-bold text-success">₹ 0</span>
                                    </div>
                                </div>

                                <div class="normal-summary mb-2"></div>

                                <div id="normal-total-box" class="border rounded p-2 mb-3 bg-light" style="display:none;">
                                    <div class="d-flex justify-content-between align-items-center mb-1" id="normal-mrp-row" style="display:none !important;">
                                        <span class="text-muted small">Total MRP</span>
                                        <span id="normal-mrp" class="text-muted small price-original">₹ 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-1" id="normal-savings-row" style="display:none !important;">
                                        <span class="text-danger fw-semibold small">You Save</span>
                                        <span id="normal-savings" class="text-danger small">- ₹ 0</span>
                                    </div>
                                    <hr class="my-2" id="normal-divider" style="display:none;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="fw-bold mb-0">Subtotal</h6>
                                        <span id="normal-subtotal" class="fw-bold text-success">₹ 0</span>
                                    </div>
                                </div>

                                <div class="border rounded p-3 bg-white shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold mb-0">Shipping</h6>
                                        <span id="cart-shipping" class="fw-bold">Free</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="fw-bold mb-0">Grand Total</h6>
                                        <span id="cart-total" class="fw-bold text-success fs-5">₹ 0</span>
                                    </div>
                                </div>

                                <div class="list-ver d-grid gap-2 mt-4">
                                    <a href="#" id="proceed-to-checkout" class="tf-btn w-100 animate-btn">
                                        Proceed to checkout <i class="icon icon-arrow-right"></i>
                                    </a>
                                    <a href="{{ route('frontend.index') }}" class="tf-btn btn-white animate-btn animate-dark w-100">
                                        Continue shopping <i class="icon icon-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
            $(document).ready(function () {

                const notyf = new Notyf({
                    duration: 2500,
                    position: { x: 'right', y: 'top' }
                });

                // ✅ Base URL for offer slug links
                const dealsBaseUrl = '{{ url("/deals") }}';

                // ✅ Indian-format helper — rounds and drops decimals
                function money(n) {
                    return Math.round(n).toLocaleString('en-IN');
                }

                function updateSummary() {
                    let shipping    = 0;
                    let offerMrp    = 0;
                    let offerFinal  = 0;
                    let normalTotal = 0;
                    let normalMrp   = 0;

                    const $offerSummary  = $('.offer-summary').empty();
                    const $normalSummary = $('.normal-summary').empty();

                    $('.tf-cart_item').each(function () {
                        const $row       = $(this);
                        const isOffer    = $row.data('combo') === 'offer';
                        const qty        = isOffer ? 1 : (parseInt($row.find('.quantity-product').val()) || 1);
                        const price      = parseFloat($row.data('price')) || 0;
                        const mrp        = parseFloat($row.data('mrp'))   || price;

                        // ✅ Use slug for URL (not numeric id)
                        const offerSlug  = $row.data('offer-slug') || '';

                        const imgSrc = isOffer
                            ? ($row.data('offer-image')   || '{{ asset('images/no-image.png') }}')
                            : ($row.data('product-image') || '{{ asset('images/no-image.png') }}');

                        // ✅ Build link using slug
                        const linkHref = isOffer && offerSlug
                            ? dealsBaseUrl + '/' + offerSlug
                            : ($row.find('.prd_name a').first().attr('href') || '#');

                        const name = isOffer
                            ? ($row.data('offer-name') || '')
                            : ($row.find('.prd_name a').first().text().trim() || '');

                        const lineTotal = isOffer ? price : price * qty;
                        const lineMrp   = isOffer ? mrp   : mrp * qty;
                        const lineHasOffer = lineMrp > lineTotal;

                        const itemHTML = `
                            <div class="summary-item">
                                <a href="${linkHref}">
                                    <img src="${imgSrc}" class="summary-thumb"
                                         onerror="this.src='{{ asset('images/no-image.png') }}'">
                                </a>
                                <div class="summary-item-info">
                                    <a href="${linkHref}">${name}</a><br>
                                    ${lineHasOffer ? `<small class="text-muted text-decoration-line-through">₹ ${money(lineMrp)}</small> ` : ''}
                                    <small>₹ ${money(lineTotal)}</small>
                                    <small>${isOffer ? ' — Bundle' : ' × ' + qty}</small>
                                </div>
                            </div>`;

                        if (isOffer) {
                            offerMrp   += mrp;
                            offerFinal += price;
                            $offerSummary.append(itemHTML);
                        } else {
                            normalTotal += price * qty;
                            normalMrp   += mrp * qty;
                            $normalSummary.append(itemHTML);
                        }
                    });

                    if (offerFinal > 0) {
                        const savings = Math.max(0, offerMrp - offerFinal);
                        $('#offer-total-box').show();
                        $('#offer-mrp').text('₹ ' + money(offerMrp));
                        $('#offer-savings').text('- ₹ ' + money(savings));
                        $('#offer-subtotal').text('₹ ' + money(offerFinal));
                    } else {
                        $('#offer-total-box').hide();
                    }

                    if (normalTotal > 0) {
                        const nSavings = Math.max(0, normalMrp - normalTotal);
                        $('#normal-total-box').show();
                        $('#normal-subtotal').text('₹ ' + money(normalTotal));

                        const showSavings = nSavings > 0;
                        if (showSavings) {
                            $('#normal-mrp').text('₹ ' + money(normalMrp));
                            $('#normal-savings').text('- ₹ ' + money(nSavings));
                        }
                        // NOTE: these rows use Bootstrap's `d-flex` (display:flex !important),
                        // so jQuery .hide()/.show() can't toggle them — set display with
                        // !important via setProperty so removing a discount actually hides them.
                        $('#normal-mrp-row, #normal-savings-row').each(function () {
                            this.style.setProperty('display', showSavings ? 'flex' : 'none', 'important');
                        });
                        $('#normal-divider').toggle(showSavings);
                    } else {
                        $('#normal-total-box').hide();
                    }

                    const grand = offerFinal + normalTotal + shipping;
                    $('#cart-shipping').text('₹ ' + money(shipping));
                    $('#cart-total').text('₹ ' + money(grand));
                }

                function updateRowPrice($row) {
                    if ($row.data('combo') !== 'offer') {
                        const price    = parseFloat($row.data('price')) || 0;
                        const mrp      = parseFloat($row.data('mrp'))   || price;
                        const qty      = parseInt($row.find('.quantity-product').val()) || 1;
                        const hasOffer = mrp > price;

                        // Rebuild the price cell so the struck MRP stays visible on qty change
                        let cellHtml = '';
                        if (hasOffer) {
                            cellHtml += '<span class="price-original">₹ ' + money(mrp * qty) + '</span>';
                        }
                        cellHtml += '<span class="price-final">₹ ' + money(price * qty) + '</span>';
                        $row.find('.each-price').html(cellHtml);

                        // ✅ PERSIST the quantity to the database
                        const cartId = $row.data('cart-id');
                        fetch('{{ route('cart.updateQuantity') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type'    : 'application/json',
                                'X-CSRF-TOKEN'    : '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ cart_id: cartId, quantity: qty })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                $('.cart-count').text(data.cart_count || 0);
                            }
                        })
                        .catch(() => console.error('Quantity update failed'));
                    }
                    updateSummary();
                }

                $(document).on('click', '.plus-quantity', function () {
                    const $row = $(this).closest('.tf-cart_item');
                    const $inp = $row.find('.quantity-product');
                    const max  = parseInt($inp.data('max')) || 0;
                    const cur  = parseInt($inp.val()) || 1;
                    if (cur < max) {
                        $inp.val(cur + 1);
                        updateRowPrice($row);
                    } else {
                        notyf.error('Only ' + max + ' units available in stock.');
                    }
                });

                $(document).on('click', '.minus-quantity', function () {
                    const $row = $(this).closest('.tf-cart_item');
                    const $inp = $row.find('.quantity-product');
                    const cur  = parseInt($inp.val()) || 1;
                    if (cur > 1) { $inp.val(cur - 1); updateRowPrice($row); }
                });

                $(document).on('change', '.quantity-product', function () {
                    const $row = $(this).closest('.tf-cart_item');
                    $(this).val(Math.max(parseInt($(this).val()) || 1, 1));
                    updateRowPrice($row);
                });

                $(document).on('click', '.cart_remove', function () {
                    const $row   = $(this).closest('.tf-cart_item');
                    const cartId = $row.data('cart-id');
                    const isOffer = $row.data('combo') === 'offer';
                
                    $row.remove();
                
                    // ✅ Remove section label if no items remain in that section
                    const remainingOffers  = $('.tf-cart_item[data-combo="offer"]').length;
                    const remainingNormals = $('.tf-cart_item[data-combo="no"]').length;
                
                    if (remainingOffers === 0) {
                        // Remove "Offer Bundles" label row
                        $('td.cart-section-label:not(.normal)').closest('tr').remove();
                    }
                    if (remainingNormals === 0) {
                        // Remove "Products" label row
                        $('td.cart-section-label.normal').closest('tr').remove();
                    }
                
                    updateSummary();
                    checkEmptyCart();
                
                    fetch('{{ route('cart.remove') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type'    : 'application/json',
                            'X-CSRF-TOKEN'    : '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ cart_id: cartId })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            $('.cart-count').text(data.cart_count || 0);
                            if (data.cart_count == 0) location.reload();
                        }
                    })
                    .catch(() => console.error('Remove request failed'));
                });

                $('#proceed-to-checkout').on('click', function (e) {
                    e.preventDefault();
                    const cartData = [];

                    $('.tf-cart_item').each(function () {
                        const $row      = $(this);
                        const isOffer   = $row.data('combo') === 'offer';
                        const offerId   = parseInt($row.data('offer-id')) || 0;
                        const offerSlug = $row.data('offer-slug') || '';   // ✅ pass slug too
                        const price     = parseFloat($row.data('price')) || 0;
                        const mrp       = parseFloat($row.data('mrp'))   || price;
                        const image     = isOffer
                            ? ($row.data('offer-image')   || '')
                            : ($row.data('product-image') || '');

                        if (isOffer) {
                            cartData.push({
                                cart_id      : $row.data('cart-id'),
                                is_offer     : true,
                                offer_id     : offerId,
                                offer_slug   : offerSlug,   // ✅ included for checkout page use
                                product_id   : 0,
                                product_name : $row.data('offer-name') || '',
                                quantity     : 1,
                                price        : price,
                                mrp          : mrp,
                                subtotal     : price,
                                image        : image,
                                size         : '',
                                print        : '',
                            });
                        } else {
                            const qty = parseInt($row.find('.quantity-product').val()) || 1;
                            cartData.push({
                                cart_id      : $row.data('cart-id'),
                                is_offer     : false,
                                offer_id     : 0,
                                offer_slug   : '',
                                product_id   : $row.data('product-id') || $row.find('input[name="product_ids[]"]').val() || 0,
                                product_name : $row.find('.prd_name a').first().text().trim(),
                                quantity     : qty,
                                price        : price,
                                mrp          : mrp,   // ✅ FIX: send real MRP (was hardcoded to price)
                                subtotal     : price * qty,
                                image        : image,
                                size         : '',
                                print        : '',
                            });
                        }
                    });

                    if (cartData.length === 0) { notyf.error('Your cart is empty.'); return; }

                    fetch('{{ route('cart.storeCheckoutData') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type'    : 'application/json',
                            'X-CSRF-TOKEN'    : '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ cart: cartData })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = '{{ route('show.checkout') }}';
                        } else {
                            notyf.error('Failed to process checkout. Please try again.');
                        }
                    })
                    .catch(() => notyf.error('Something went wrong. Please try again.'));
                });

                updateSummary();
            });

            function checkEmptyCart() {
                if ($('.tf-cart_item').length === 0) {
                    $('.custom-cart-box, .fl-sidebar-cart').hide();
                    if ($('#emptyCartMessage').length === 0) {
                        $('.each-list-prd .container .row').append(`
                            <div id="emptyCartMessage" class="col-12 text-center py-5">
                                <h4 class="fw-semibold mb-3">No products found in your cart.</h4>
                                <a href="{{ route('frontend.index') }}" class="tf-btn animate-btn">
                                    Continue Shopping <i class="icon icon-arrow-right"></i>
                                </a>
                            </div>
                        `);
                    }
                } else {
                    $('.custom-cart-box, .fl-sidebar-cart').show();
                    $('#emptyCartMessage').remove();
                }
            }

            $(document).ready(checkEmptyCart);
            </script>

        </div>

        @include('components.frontend.footer')
    </div>

    <div class="offcanvas offcanvas-start canvas-mb" id="mobileMenu">
        <span class="icon-close-popup" data-bs-dismiss="offcanvas">
            <i class="icon-close"></i>
        </span>
        <div class="canvas-header">
            <p class="text-logo-mb">
                <img src="images/logo/logo.webp" data-src="images/logo/logo.webp" alt="Logo">
            </p>
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
                    <a href="#" data-bs-toggle="modal" class="tf-btn type-small style-2">
                        Search <i class="icon icon-magnifying-glass"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @include('components.frontend.main-js')
</body>
</html>