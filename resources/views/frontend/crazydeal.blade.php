<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.frontend.head')
</head>
<body>
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
                <div class="offer-grid">
                    @foreach($offers as $offer)
                        @php
                            $image = $offer->offer_image
                                ? asset('offerimage/' . $offer->offer_image)
                                : asset('images/no-image.png');

                            $badgeClass = $offer->offer_price_type === 'percent' ? 'percent' : 'fixed';
                            $badgeText  = $offer->offer_price_type === 'percent'
                                ? number_format($offer->offer_price, 0) . '% OFF'
                                : '₹' . number_format($offer->offer_price, 0);
                        @endphp
                        <div class="offer-card">
                            <div class="offer-image">
                                <img src="{{ $image }}" alt="{{ $offer->offer_name }}">
                            </div>
                            <div class="offer-content">
                                <span class="offer-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                                <h3 class="offer-title">{{ $offer->offer_name }}</h3>
                                <p class="offer-description">
                                    {{ isset($offer->description) && $offer->description
                                        ? Str::limit(strip_tags($offer->description), 100)
                                        : 'Bundle this deal and save big. Click below to build your box.' }}
                                </p>
                                <a href="{{ route('crazy.show', ['slug' => $offer->slug]) }}" class="offer-btn">
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
