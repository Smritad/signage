<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.frontend.head')
</head>
<body>
<div id="wrapper">

    @include('components.frontend.header')

 @if(!empty($offer->banner_image))
    <section class="s-page-title">
        <div class="container">
            <div class="content">
                <h1 class="title-page">{{ $offer->offer_name }}</h1>
                <ul class="breadcrumbs-page">
                    <li>
                        <a href="https://anvayafoundation.com/signage" class="h6 link">Home</a>
                    </li>
                    <li class="d-flex">
                        <i class="icon icon-caret-right"></i>
                    </li>
                    <li>
                        <h6 class="current-page fw-normal">{{ $offer->offer_name }}</h6>
                    </li>
                </ul>
            </div>
        </div>
    </section>
@endif

    <div class="flat-spacing">
        <div class="container">


            {{-- ═══════════════════════════════════════
                 CIRCULAR STEP NAV
            ═══════════════════════════════════════ --}}
            <div class="cd-steps-nav" id="cdStepsNav">
                @foreach($stepProducts as $step)
                    @if(!$loop->first)
                        <div class="cd-connector" id="conn{{ $step['step_no'] - 1 }}"></div>
                    @endif
                    <button class="cd-step-btn {{ $loop->first ? 'active' : '' }}"
                            data-step="{{ $step['step_no'] }}"
                            id="stepBtn{{ $step['step_no'] }}">
                        <span class="cd-step-lbl-top">Step {{ $step['step_no'] }}</span>
                        <span class="cd-circle">{{ $step['step_no'] }}</span>
                        <span class="cd-step-lbl-bot">{{ Str::limit($step['title'], 20) }}</span>
                    </button>
                @endforeach
            </div>

            {{-- Progress strip --}}
            <!--<div class="cd-progress-strip">-->
            <!--    <div class="cd-progress-fill" id="cdProgressFill" style="width:0%"></div>-->
            <!--</div>-->

            {{-- ═══════════════════════════════════════
                 STEP PANELS
            ═══════════════════════════════════════ --}}
            @foreach($stepProducts as $step)
                @php
                    $qtyNeeded    = (int)$step['qty'];
                    $isPercent    = $offer->offer_price_type === 'percent';
                    $pctOff       = $isPercent ? (int)$offer->offer_price : 0;
                    $isFixed      = $offer->offer_price_type === 'fixed';
                @endphp

                <div class="cd-tab-panel {{ $loop->first ? 'active' : '' }}"
                     id="cdPanel{{ $step['step_no'] }}"
                     data-step="{{ $step['step_no'] }}"
                     data-qty-needed="{{ $qtyNeeded }}">

                    {{-- Hint bar --}}
                    <div class="cd-select-hint">
                        <span class="hint-text">
                            {{ $step['title'] }}
                            @if($qtyNeeded > 1)
                                &nbsp;<span style="color:#888;font-weight:500;">— pick {{ $qtyNeeded }}</span>
                            @endif
                        </span>
                        <span class="hint-count" id="hintCount{{ $step['step_no'] }}">
                            Selected: <strong>0</strong> / {{ $qtyNeeded }}
                        </span>
                    </div>

                    {{-- Product grid --}}
                    <div class="wrapper-control-shop gridLayout-wrapper">
                        <div class="wrapper-shop tf-grid-layout crazy-deals-details tf-col-4"
                             id="productGrid{{ $step['step_no'] }}">

                            @forelse($step['products'] as $product)
                                @php
                                    $image = asset('images/no-image.png');
                                    if (!empty($product->images)) {
                                        $dec = json_decode($product->images, true);
                                        if (is_string($dec)) { $dec = json_decode($dec, true); }
                                        if (is_array($dec) && !empty($dec[0])) {
                                            $image = asset('signage/home/productimage/' . trim($dec[0]));
                                        }
                                    }
                                    $hasOffer     = !empty($product->offer_price)
                                                    && (float)$product->offer_price < (float)$product->price;
                                    $displayPrice = $hasOffer
                                                    ? (float)$product->offer_price
                                                    : (float)$product->price;
                                    $afterPct     = ($isPercent && $pctOff > 0)
                                                    ? round($displayPrice * (1 - $pctOff / 100))
                                                    : null;
                                @endphp

                                <div class="card-product grid selectProductCard"
                                     data-step="{{ $step['step_no'] }}"
                                     data-qty-needed="{{ $qtyNeeded }}"
                                     data-id="{{ $product->id }}"
                                     data-name="{{ $product->product_name }}"
                                     data-unit="{{ $product->measurement_unit }}"
                                     data-price="{{ $displayPrice }}"
                                     data-image="{{ $image }}">

                                    <div class="card-product_wrapper">

                                        {{-- % ribbon (single-select only) --}}
                                        @if($isPercent && $pctOff > 0 && $qtyNeeded <= 1)
                                            <span class="cd-pct-ribbon">{{ $pctOff }}% OFF</span>
                                        @endif

                                        {{-- Tick --}}
                                        <span class="cd-tick">✓</span>

                                        {{-- Multi-select order badge --}}
                                        @if($qtyNeeded > 1)
                                            <span class="cd-order-badge cd-order-num"></span>
                                        @endif

                                        <a href="{{ url('product/' . $product->slug) }}"
                                           class="product-img" onclick="return false;">
                                            <img class="lazyload img-product"
                                                 src="{{ $image }}"
                                                 data-src="{{ $image }}"
                                                 alt="{{ $product->product_name }}">
                                            <img class="lazyload img-hover"
                                                 src="{{ $image }}"
                                                 data-src="{{ $image }}"
                                                 alt="{{ $product->product_name }}">
                                        </a>

                                        <div class="product-action_bot">
                                            <a href="javascript:void(0);"
                                               class="tf-btn btn-white animate-btn animate-dark cdSelectBtn">
                                                Add to Box
                                            </a>
                                        </div>

                                    </div>{{-- /card_wrapper --}}

                                    <div class="card-product_info">
                                        <a href="{{ url('product/' . $product->slug) }}"
                                           class="name-product h4 link" onclick="return false;">
                                            {{ $product->product_name }}
                                        </a>
                                        <p class="mb-4" style="font-size:12px;color:#999;margin-bottom:8px!important;">
                                            {{ $product->measurement_unit }}
                                        </p>
                                        <div class="price-wrap">
                                            @if($hasOffer)
                                                <span class="price-old h6 fw-normal">
                                                    ₹{{ number_format($product->price, 0) }}
                                                </span>
                                                <span class="price-new h6">
                                                    ₹{{ number_format($product->offer_price, 0) }}
                                                </span>
                                            @elseif($afterPct)
                                                <span class="price-old h6 fw-normal">
                                                    ₹{{ number_format($displayPrice, 0) }}
                                                </span>
                                                <span class="price-new h6">
                                                    ₹{{ number_format($afterPct, 0) }}
                                                </span>
                                            @else
                                                <span class="price-new h6">
                                                    ₹{{ number_format($displayPrice, 0) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                </div>{{-- /card-product --}}

                            @empty
                                <p class="text-muted col-12">No products available for this step.</p>
                            @endforelse

                        </div>{{-- /wrapper-shop --}}
                    </div>{{-- /gridLayout-wrapper --}}

                    {{-- ── PAGINATION (rendered by JS if > 8 products) ── --}}
                    <div id="cdPaginationWrap{{ $step['step_no'] }}" style="display:none;">
                        <div class="cd-pagination-wrap">
                            <div class="cd-pager-info" id="cdPagerInfo{{ $step['step_no'] }}"></div>
                            <div class="cd-pagination" id="cdPager{{ $step['step_no'] }}"></div>
                        </div>
                    </div>

                    {{-- Prev / Next --}}
                    <div class="cd-step-actions">
                        @if(!$loop->first)
                            <button class="cd-btn-back" data-prev="{{ $step['step_no'] - 1 }}">← Back</button>
                        @endif
                        @if(!$loop->last)
                            <button class="cd-btn-next"
                                    id="nextBtn{{ $step['step_no'] }}"
                                    data-next="{{ $step['step_no'] + 1 }}"
                                    disabled>
                                Next Step →
                            </button>
                        @endif
                    </div>

                </div>{{-- /cd-tab-panel --}}
            @endforeach

        </div>
    </div>

    {{-- ═══════════════════════════════════════
         FLOATING SUMMARY BAR (fixed, above footer)
    ═══════════════════════════════════════ --}}
    <div class="cd-summary-bar">

        <div class="bar-chips" id="barChips">
            <span class="bar-empty-msg">Pick products from each step to build your bundle.</span>
        </div>

        <div class="bar-price">
            @if($offer->offer_price_type === 'fixed')
                <div class="bar-offer-tag">Bundle Offer</div>
            @elseif($offer->offer_price_type === 'percent')
                <div class="bar-offer-tag">{{ (int)$offer->offer_price }}% OFF</div>
            @endif
            <div class="bar-mrp" id="barMrp" style="display:none;"></div>
            <div class="bar-final" id="barFinal">₹0</div>
            <div class="bar-label">
                @if($offer->offer_price_type === 'fixed') Offer Price @else You Pay @endif
            </div>
        </div>

        <button class="bar-cta-btn" id="completeBoxBtn" disabled>
            Add Bundle to Cart
        </button>

    </div>

    {{-- Toast --}}
    <div class="cd-toast" id="cdToast"></div>

    @include('components.frontend.footer')
</div>

@include('components.frontend.main-js')

<script>
/* ─────────────────────────────────────────────────────
   CONFIG (injected from PHP)
───────────────────────────────────────────────────── */
const OFFER_ID         = {{ $offer->id }};
const OFFER_PRICE_TYPE = '{{ $offer->offer_price_type }}';
const OFFER_PRICE      = {{ (float)$offer->offer_price }};
const TOTAL_STEPS      = {{ count($stepProducts) }};

const STEP_CONFIG = {
    @foreach($stepProducts as $step)
    {{ $step['step_no'] }}: { qtyNeeded: {{ (int)$step['qty'] }} },
    @endforeach
};

/* ─────────────────────────────────────────────────────
   STATE
───────────────────────────────────────────────────── */
let selectedProducts = {};   // stepNo → [ { id, name, image, price, unit }, … ]

/* ─────────────────────────────────────────────────────
   PAGINATION  — 8 cards per page (4 cols × 2 rows)
───────────────────────────────────────────────────── */
const PAGE_SIZE = 8;
const stepPages = {};

function _initPagination(step) {
    stepPages[step] = 1;
    _renderPage(step);
}

function _renderPage(step) {
    const grid  = $('#productGrid' + step);
    const cards = grid.children('.selectProductCard');
    const total = cards.length;
    const page  = stepPages[step] || 1;
    const pages = Math.ceil(total / PAGE_SIZE);
    const start = (page - 1) * PAGE_SIZE;
    const end   = start + PAGE_SIZE;

    cards.each(function (i) {
        $(this).toggle(i >= start && i < end);
    });

    const wrap  = $('#cdPaginationWrap' + step);
    const pager = $('#cdPager' + step);
    const info  = $('#cdPagerInfo' + step);

    if (pages <= 1) {
        wrap.hide();
        return;
    }

    wrap.show();

    const showing_end = Math.min(end, total);
    info.html('Showing <strong>' + (start + 1) + '–' + showing_end + '</strong> of <strong>' + total + '</strong> products');

    pager.empty();

    $('<button class="cd-page-btn arrow" title="Previous page">&#8592;</button>')
        .prop('disabled', page === 1)
        .on('click', function () { stepPages[step]--; _renderPage(step); _scrollToGrid(step); })
        .appendTo(pager);

    for (let p = 1; p <= pages; p++) {
        const nearCurrent = Math.abs(p - page) <= 1;
        const isEdge      = p === 1 || p === pages;

        if (!nearCurrent && !isEdge) {
            if (p === 2 || p === pages - 1) {
                $('<span class="cd-page-ellipsis">…</span>').appendTo(pager);
            }
            continue;
        }

        (function (pg) {
            $('<button class="cd-page-btn' + (pg === page ? ' active' : '') + '">' + pg + '</button>')
                .on('click', function () { stepPages[step] = pg; _renderPage(step); _scrollToGrid(step); })
                .appendTo(pager);
        })(p);
    }

    $('<button class="cd-page-btn arrow" title="Next page">&#8594;</button>')
        .prop('disabled', page === pages)
        .on('click', function () { stepPages[step]++; _renderPage(step); _scrollToGrid(step); })
        .appendTo(pager);
}

function _scrollToGrid(step) {
    var top = $('#productGrid' + step).offset().top - 120;
    $('html,body').animate({ scrollTop: top }, 260);
}

/* ─────────────────────────────────────────────────────
   INIT — no auto-select, all steps start empty
───────────────────────────────────────────────────── */
$(document).ready(function () {

    for (var s = 1; s <= TOTAL_STEPS; s++) {
        _initPagination(s);
    }

    var first = {{ $stepProducts[0]['step_no'] ?? 1 }};
    _syncNav(first);
    _refreshAll();
});

/* ─────────────────────────────────────────────────────
   CARD CLICK
───────────────────────────────────────────────────── */
$(document).on('click', '.selectProductCard, .cdSelectBtn', function (e) {
    e.stopPropagation();
    var card      = $(this).hasClass('selectProductCard')
                    ? $(this)
                    : $(this).closest('.selectProductCard');
    var step      = parseInt(card.data('step'));
    var qtyNeeded = STEP_CONFIG[step] ? STEP_CONFIG[step].qtyNeeded : 1;
    _doSelect(card, step, qtyNeeded, false);
});

/* ─────────────────────────────────────────────────────
   CORE SELECT / DESELECT
───────────────────────────────────────────────────── */
function _doSelect(card, step, qtyNeeded, silent) {

    if (!selectedProducts[step]) selectedProducts[step] = [];

    var pid = parseInt(card.data('id'));

    if (qtyNeeded <= 1) {
        /* ── Single-select ── */
        $('.selectProductCard[data-step="' + step + '"]')
            .removeClass('cd-selected')
            .find('.cdSelectBtn').text('Add to Box');

        card.addClass('cd-selected').find('.cdSelectBtn').text('✓ Added');

        selectedProducts[step] = [{
            id    : pid,
            name  : card.data('name'),
            image : card.data('image'),
            price : parseFloat(card.data('price')),
            unit  : card.data('unit'),
        }];

    } else {
        /* ── Multi-select ── */
        var isSelected = card.hasClass('cd-selected');

        if (isSelected) {
            card.removeClass('cd-selected').find('.cdSelectBtn').text('Add to Box');
            selectedProducts[step] = selectedProducts[step].filter(function (p) { return p.id !== pid; });
        } else {
            if (selectedProducts[step].length >= qtyNeeded) {
                var evicted = selectedProducts[step].shift();
                $('.selectProductCard[data-step="' + step + '"][data-id="' + evicted.id + '"]')
                    .removeClass('cd-selected')
                    .find('.cdSelectBtn').text('Add to Box');
            }
            card.addClass('cd-selected').find('.cdSelectBtn').text('✓ Added');
            selectedProducts[step].push({
                id    : pid,
                name  : card.data('name'),
                image : card.data('image'),
                price : parseFloat(card.data('price')),
                unit  : card.data('unit'),
            });
        }

        /* rebuild order badges */
        $('.selectProductCard[data-step="' + step + '"] .cd-order-num').text('');
        selectedProducts[step].forEach(function (p, i) {
            $('.selectProductCard[data-step="' + step + '"][data-id="' + p.id + '"] .cd-order-num')
                .text(i + 1);
        });
    }

    var selCount  = (selectedProducts[step] || []).length;
    var satisfied = selCount >= qtyNeeded;

    $('#hintCount' + step + ' strong').text(selCount);
    $('#nextBtn' + step).prop('disabled', !satisfied);

    if (satisfied) {
        $('#stepBtn' + step).addClass('done');
        $('#conn' + (step - 1)).addClass('done');
    } else {
        $('#stepBtn' + step).removeClass('done');
        $('#conn' + (step - 1)).removeClass('done');
    }

    _refreshAll();

    if (satisfied && !silent) {
        var next = step + 1;
        if (next <= TOTAL_STEPS) {
            var nextCfg  = STEP_CONFIG[next];
            var nextDone = nextCfg
                           && selectedProducts[next]
                           && selectedProducts[next].length >= nextCfg.qtyNeeded;
            if (!nextDone) {
                _toast('✓ Step ' + step + ' done! Going to Step ' + next + '…');
                setTimeout(function () { _goTo(next); }, 650);
            }
        } else {
            _toast('All steps complete! Tap "Add Bundle to Cart".');
        }
    }
}

/* ─────────────────────────────────────────────────────
   NAVIGATION
───────────────────────────────────────────────────── */
$(document).on('click', '.cd-step-btn', function () { _goTo(parseInt($(this).data('step'))); });
$(document).on('click', '.cd-btn-next', function () { _goTo(parseInt($(this).data('next'))); });
$(document).on('click', '.cd-btn-back', function () { _goTo(parseInt($(this).data('prev'))); });

function _goTo(step) {
    $('.cd-tab-panel').removeClass('active');
    $('#cdPanel' + step).addClass('active');
    _syncNav(step);
    $('html,body').animate({ scrollTop: $('#cdStepsNav').offset().top - 80 }, 280);
}

function _syncNav(currentStep) {
    $('.cd-step-btn').removeClass('active');
    $('#stepBtn' + currentStep).addClass('active').removeClass('done');

    for (var s = 1; s <= TOTAL_STEPS; s++) {
        if (s === currentStep) continue;
        var cfg = STEP_CONFIG[s];
        var ok  = cfg && selectedProducts[s] && selectedProducts[s].length >= cfg.qtyNeeded;
        if (ok) {
            $('#stepBtn' + s).addClass('done').removeClass('active');
            $('#conn' + (s - 1)).addClass('done');
        }
    }
}

/* ─────────────────────────────────────────────────────
   REFRESH SUMMARY BAR + PROGRESS
───────────────────────────────────────────────────── */
function _refreshAll() {
    var chips    = '';
    var mrpTotal = 0;

    Object.keys(selectedProducts).sort(function (a, b) { return a - b; }).forEach(function (s) {
        (selectedProducts[s] || []).forEach(function (p) {
            mrpTotal += p.price;
            chips += '<div class="cd-chip">'
                   + '<img src="' + p.image + '" alt="' + p.name + '">'
                   + '<div class="chip-inner">'
                   + '<span class="chip-step">Step ' + s + '</span>'
                   + '<span class="chip-name">' + p.name + '</span>'
                   + '</div></div>';
        });
    });

    $('#barChips').html(chips || '<span class="bar-empty-msg">Pick products from each step to build your bundle.</span>');

    var allDone    = _allDone();
    var finalPrice = mrpTotal;

    if (OFFER_PRICE_TYPE === 'fixed' && allDone) {
        finalPrice = OFFER_PRICE;
        if (mrpTotal > 0 && mrpTotal !== OFFER_PRICE) {
            $('#barMrp').text('MRP ₹' + Math.round(mrpTotal).toLocaleString('en-IN')).show();
        } else {
            $('#barMrp').hide();
        }
    } else if (OFFER_PRICE_TYPE === 'percent' && mrpTotal > 0) {
        finalPrice = Math.round(mrpTotal * (1 - OFFER_PRICE / 100));
        $('#barMrp').text('MRP ₹' + Math.round(mrpTotal).toLocaleString('en-IN')).show();
    } else {
        $('#barMrp').hide();
    }

    $('#barFinal').text('₹' + Math.round(finalPrice).toLocaleString('en-IN'));

    var done = 0;
    for (var s = 1; s <= TOTAL_STEPS; s++) {
        var cfg = STEP_CONFIG[s];
        if (cfg && selectedProducts[s] && selectedProducts[s].length >= cfg.qtyNeeded) done++;
    }
    $('#cdProgressFill').css('width', Math.round((done / TOTAL_STEPS) * 100) + '%');

    $('#completeBoxBtn').prop('disabled', !allDone);
}

function _allDone() {
    return Object.keys(STEP_CONFIG).every(function (s) {
        var cfg = STEP_CONFIG[s];
        return cfg && selectedProducts[s] && selectedProducts[s].length >= cfg.qtyNeeded;
    });
}

/* ─────────────────────────────────────────────────────
   TOAST
───────────────────────────────────────────────────── */
var _toastTimer = null;
function _toast(msg) {
    clearTimeout(_toastTimer);
    $('#cdToast').text(msg).addClass('show');
    _toastTimer = setTimeout(function () { $('#cdToast').removeClass('show'); }, 2400);
}

/* ─────────────────────────────────────────────────────
   ADD BUNDLE TO CART
───────────────────────────────────────────────────── */
$('#completeBoxBtn').on('click', function () {

    if (!_allDone()) {
        _toast('⚠ Please complete all steps first.');
        return;
    }

    let btn = $(this);
    btn.prop('disabled', true).text('Adding...');

    $.ajax({
        url  : "{{ route('cart.addBundle') }}",
        type : "POST",
        data : {
            _token   : "{{ csrf_token() }}",
            offer_id : OFFER_ID,
            products : JSON.stringify(selectedProducts)
        },
        success: function (response) {
            if (response.success) {
                $('.cart-count, .count').text(response.cart_count);
                _toast('✓ Bundle added to cart');
                setTimeout(function () {
                    window.location.href = "{{ route('cart.index') }}";
                }, 500);
            } else {
                btn.prop('disabled', false).text('Add Bundle to Cart');
                alert(response.message || 'Something went wrong');
            }
        },
        error: function (xhr) {
            console.log(xhr.responseText);
            btn.prop('disabled', false).text('Add Bundle to Cart');
            alert('Failed to add bundle');
        }
    });
});
</script>
</body>
</html>