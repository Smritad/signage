<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.frontend.head')
</head>
<body>

<style>
    /* Today's Deal cards reuse the product-card layout used on Shop All */
    #wrapper .card-product_info {
        display: flex;
        flex-direction: column;
    }
    #wrapper .card-product_info .offer-card-desc {
        font-size: 14px;
        line-height: 1.5;
        color: #6c757d;
        margin: 6px 0 14px;
    }
    /* push the CTA to the bottom so every card lines up */
    #wrapper .card-product_info .btn-build-box {
        margin-top: auto;
    }
</style>

<div id="wrapper">
    @include('components.frontend.header')

    <section class="s-page-title">
        <div class="container">
            <div class="content">
                <h1 class="title-page">Crazy Deals</h1>
               
            </div>
        </div>
    </section>

    <div class="flat-spacing">
        <div class="container">
            @if($offers->isEmpty())
                <div class="alert alert-warning">No deals available right now. Check back soon!</div>
            @else
                <div class="wrapper-shop tf-grid-layout tf-col-4">
                    @foreach($offers as $offer)
                        @php
                            $image = $offer->offer_image
                                ? asset('offerimage/' . $offer->offer_image)
                                : asset('images/no-image.png');

                            $badgeText  = $offer->offer_price_type === 'percent'
                                ? number_format($offer->offer_price, 0) . '% OFF'
                                : '₹' . number_format($offer->offer_price, 0);

                            $offerUrl   = route('crazy.show', ['slug' => $offer->slug]);
                        @endphp

                        <div class="card-product grid">
                            <div class="card-product_wrapper">
                                <span class="product-discount-badge">{{ $badgeText }}</span>
                                <a href="{{ $offerUrl }}" class="product-img">
                                    <img class="lazyload img-product"
                                         src="{{ $image }}"
                                         alt="{{ $offer->offer_name }}">
                                    <img class="lazyload img-hover"
                                         src="{{ $image }}"
                                         alt="{{ $offer->offer_name }}">
                                </a>
                            </div>
                            <div class="card-product_info">
                                <a href="{{ $offerUrl }}" class="name-product h4 link">{{ $offer->offer_name }}</a>
                                <p class="offer-card-desc">
                                    {{ isset($offer->description) && $offer->description
                                        ? Str::limit(strip_tags($offer->description), 90)
                                        : 'Bundle this deal and save big. Click below to build your box.' }}
                                </p>
                                <a href="{{ $offerUrl }}" class="tf-btn btn-build-box w-100 justify-content-center">
                                    Build Your Box
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($offers->lastPage() > 1)
                    <div class="wd-full wg-pagination m-0 justify-content-center d-flex mt-4">

                        {{-- Prev --}}
                        @if($offers->onFirstPage())
                            <span class="pagination-item h6 direct disabled">
                                <i class="icon icon-caret-left"></i>
                            </span>
                        @else
                            <a href="{{ $offers->previousPageUrl() }}" class="pagination-item h6 direct">
                                <i class="icon icon-caret-left"></i>
                            </a>
                        @endif

                        {{-- Page numbers --}}
                        @foreach($offers->getUrlRange(1, $offers->lastPage()) as $page => $url)
                            @if($page == $offers->currentPage())
                                <span class="pagination-item h6 active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="pagination-item h6">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next --}}
                        @if($offers->hasMorePages())
                            <a href="{{ $offers->nextPageUrl() }}" class="pagination-item h6 direct">
                                <i class="icon icon-caret-right"></i>
                            </a>
                        @else
                            <span class="pagination-item h6 direct disabled">
                                <i class="icon icon-caret-right"></i>
                            </span>
                        @endif

                    </div>
                @endif

            @endif
        </div>
    </div>

    @include('components.frontend.footer')
</div>
@include('components.frontend.main-js')
</body>
</html>
