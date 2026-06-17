<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
    @include('components.frontend.head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <button id="goTop"><span class="border-progress"></span><span class="icon icon-caret-up"></span></button>
    <div class="preload preload-container" id="preload"><div class="preload-logo"><div class="spinner"></div></div></div>

    <div id="wrapper">
        @include('components.frontend.header')

        <section class="s-page-title">
            <div class="container">
                <div class="content">
                    <h1 class="title-page">{{ $product->product_name }}</h1>
                    <ul class="breadcrumbs-page">
                        <li><a href="{{ url('/') }}" class="h6 link">Home</a></li>
                        <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                        <li>
                            <a href="{{ route('product.subcategory', ['category' => $category->slug, 'sabcat' => $subcategory->slug]) }}" class="h6 link">
                                {{ $subcategory->sab_category_name }}
                            </a>
                        </li>
                        <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                        <li><h6 class="current-page fw-normal">{{ $product->product_name }}</h6></li>
                    </ul>
                </div>
            </div>
        </section>

        @php
            $originalPrice = $product->price;
            $hasOffer      = !empty($product->offer_price);
            $finalPrice    = $hasOffer ? $product->offer_price : $originalPrice;

            $discountPercent = 0;
            if (!empty($product->discount) && $product->discount > 0) {
                $discountPercent = (int) round($product->discount);
            } elseif ($hasOffer && $product->offer_price < $originalPrice) {
                $discountPercent = (int) round((($originalPrice - $product->offer_price) / $originalPrice) * 100);
            }
        @endphp

        {{-- PRODUCT MAIN --}}
        <section class="flat-single-product flat-spacing-3 product-detail-wrap">
            <div class="tf-main-product section-image-zoom">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="lightSlider-card">
                                @if($discountPercent > 0)
                                    <span class="product-discount-badge">{{ $discountPercent }}% OFF</span>
                                @endif

                                {{-- Bestseller / New Arrival tags on the main product image --}}
                                @if($product->is_bestseller || $product->is_new_arrival)
                                    <ul class="product-badge_list">
                                        @if($product->is_bestseller)<li class="product-badge_item h6 bestseller">Bestseller</li>@endif
                                        @if($product->is_new_arrival)<li class="product-badge_item h6 new-arrival">New Arrival</li>@endif
                                    </ul>
                                @endif

                                <div class="demo">
                                    <ul id="lightSlider">
                                        @if(!empty($product->images))
                                            @foreach($product->images as $img)
                                                <li data-thumb="{{ asset('signage/home/productimage/' . $img) }}">
                                                    <img src="{{ asset('signage/home/productimage/' . $img) }}" alt="{{ $product->product_name }}">
                                                </li>
                                            @endforeach
                                        @else
                                            <li data-thumb="{{ asset('signage/no-image.png') }}">
                                                <img src="{{ asset('signage/no-image.png') }}" alt="No Image">
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="tf-product-info-wrap position-relative">
                                <div class="tf-zoom-main sticky-top"></div>
                                <div class="tf-product-info-list other-image-zoom">
                                    <h2 class="product-info-name">{{ $product->product_name }}</h2>

                                    <!--@if($reviewStats['total'] > 0)-->
                                    <!--    <div class="d-flex align-items-center gap-2 mb-2">-->
                                    <!--        <div class="review-stars">-->
                                    <!--            @for($s = 1; $s <= 5; $s++)-->
                                    <!--                <i class="icon-star {{ $s <= round($reviewStats['average']) ? '' : 'text-muted' }}"></i>-->
                                    <!--            @endfor-->
                                    <!--        </div>-->
                                    <!--        <span class="h6 text-muted">-->
                                    <!--            {{ $reviewStats['average'] }} ({{ $reviewStats['total'] }} reviews)-->
                                    <!--        </span>-->
                                    <!--    </div>-->
                                    <!--@endif-->

                                    <div class="tf-product-heading">
                                        <div class="product-info-price price-wrap">
                                            <span class="price-new price-on-sale h2 fw-4">Rs. {{ number_format($finalPrice) }}</span>
                                            @if($hasOffer)
                                                <span class="price-old h6">Rs. {{ number_format($originalPrice) }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <ul class="tf-product-cate-sku-1">
                                        <li class="item-cate-sku h6 d-flex gap-3">
                                            <span class="label fw-6 text-black">Fragrance Type:</span>
                                            <span class="value text-main-2">
                                                {{ $fragranceTypes && $fragranceTypes->isNotEmpty() ? $fragranceTypes->pluck('title')->implode(', ') : 'N/A' }}
                                            </span>
                                        </li>
                                    </ul>

                                    <div class="tf-product-total-quantity">
                                        <div class="group-btn">
                                            @if($product->quantity > 0)
                                                <div class="wg-quantity full-width">
                                                    <button class="btn-quantity btn-decrease" type="button"><i class="icon icon-minus"></i></button>
                                                    <input class="quantity-product" type="text" name="number" value="1" data-max="{{ $product->quantity }}">
                                                    <button class="btn-quantity btn-increase" type="button"><i class="icon icon-plus"></i></button>
                                                </div>
                                                <a href="javascript:void(0);" class="tf-btn animate-btn btn-add-to-cart eighty" data-product-id="{{ $product->id }}">
                                                    ADD TO CART <i class="icon icon-shopping-cart-simple"></i>
                                                </a>
                                            @else
                                                <button type="button" class="tf-btn animate-btn btn-out-of-stock" disabled>
                                                    OUT OF STOCK <i class="icon icon-x-circle"></i>
                                                </button>
                                            @endif

                                            @php
                                                if (Auth::guard('custom')->check()) {
                                                    $isInWishlist = \App\Models\Wishlist::where('user_id', Auth::guard('custom')->id())
                                                        ->where('product_id', $product->id)->exists();
                                                } else {
                                                    $isInWishlist = \App\Models\Wishlist::where('session_id', session()->getId())
                                                        ->where('product_id', $product->id)->exists();
                                                }
                                            @endphp

                                            <button type="button" class="hover-tooltip box-icon btn-add-wishlist twenty" data-product="{{ $product->id }}">
                                                <span class="icon {{ $isInWishlist ? 'icon-heart-filled text-danger' : 'icon-heart' }}"></span>
                                                <span class="tooltip">{{ $isInWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="tf-product-delivery-return">
                                        <div class="product-delivery">
                                            <div class="icon icon-clock-cd"></div>
                                            <p class="h6"><span class="fw-7 text-black">{{ $product->estimate_delivery ?? '7-20 days' }}</span></p>
                                        </div>
                                        <div class="product-delivery return">
                                            <div class="icon icon-compare"></div>
                                            <p class="h6"><span class="fw-7 text-black">{{ $product->return_policy ?? '30 days' }}</span></p>
                                        </div>
                                    </div>

                                    <div class="tf-product-trust-seal">
                                        <p class="h6 text-seal">Guarantee Safe Checkout:</p>
                                        <ul class="list-card">
                                            <li class="card-item"><img src="{{ asset('frontend/assets/images/checkout-icon/visa.png') }}" alt="Visa"></li>
                                            <li class="card-item"><img src="{{ asset('frontend/assets/images/checkout-icon/mastercard.png') }}" alt="Master Card"></li>
                                            <li class="card-item"><img src="{{ asset('frontend/assets/images/checkout-icon/amex.png') }}" alt="Amex"></li>
                                            <li class="card-item"><img src="{{ asset('frontend/assets/images/checkout-icon/discover.png') }}" alt="Discover"></li>
                                            <li class="card-item"><img src="{{ asset('frontend/assets/images/checkout-icon/paypal.png') }}" alt="PayPal"></li>
                                        </ul>
                                    </div>

                                    <ul class="tf-product-cate-sku">
                                        <li class="item-cate-sku h6">
                                            <span class="label fw-6 text-black">Available:</span>
                                            @if($product->quantity > 0)
                                                <a href="#" class="value link text-main-2">In Stock</a>
                                            @else
                                                <span class="value text-danger">Out of Stock</span>
                                            @endif
                                        </li>
                                        <li class="item-cate-sku h6">
                                            <span class="label fw-6 text-black">Categories:</span>
                                            <span class="value text-main-2">{{ $category->category_name }}</span>
                                        </li>
                                    </ul>

                                    <div class="tf-product-icon-box">
                                        @foreach($perfumeDetails as $detail)
                                            <div class="item">
                                                <div class="icon">
                                                    @if(!empty($detail['icon']))
                                                        <img src="{{ url('signage/home/productimage/' . $detail['icon']) }}"
                                                             alt="{{ $detail['title'] ?? '' }}"
                                                             style="width: 50px; height: 50px; object-fit: contain;">
                                                    @endif
                                                </div>
                                                <div class="text-small text-black">{{ $detail['title'] ?? '' }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- TABS --}}
        <section class="flat-spacing-3">
            <div class="container">
                <div class="flat-animate-tab tab-style-1">
                    <ul class="menu-tab menu-tab-1" role="tablist">
                        <li class="nav-tab-item"><a href="#descriptions" class="tab-link active" data-bs-toggle="tab">Description</a></li>
                        <li class="nav-tab-item"><a href="#policy" class="tab-link" data-bs-toggle="tab">Key Benefits</a></li>
                        <li class="nav-tab-item"><a href="#reviews-tab" class="tab-link" data-bs-toggle="tab">How to Use</a></li>
                        <li class="nav-tab-item"><a href="#notes" class="tab-link" data-bs-toggle="tab">Perfume Notes</a></li>
                        <li class="nav-tab-item"><a href="#faqs" class="tab-link" data-bs-toggle="tab">FAQs</a></li>
                        @if(trim(strip_tags($product->other_information ?? '')) !== '')
                        <li class="nav-tab-item"><a href="#other-info" class="tab-link" data-bs-toggle="tab">Other Information</a></li>
                        @endif
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane wd-product-descriptions active show" id="descriptions">
                            <div class="tab-descriptions"><p class="h6 desc">{!! $product->description !!}</p></div>
                        </div>
                        <div class="tab-pane wd-product-descriptions" id="policy">
                            <div class="tab-policy ck-content"><p class="h6">{!! $product->key_benefits !!}</p></div>
                        </div>
                        <div class="tab-pane wd-product-descriptions" id="reviews-tab">
                            <div class="tab-descriptions"><p class="h6 desc">{!! $product->how_to_use !!}</p></div>
                        </div>
                        <div class="tab-pane wd-product-descriptions" id="notes">
                            <div class="tab-descriptions">
                                <div class="list-infor">
                                    <div class="infor-item">
                                        <ul>
                                            @php
                                                $perfumeNotes = json_decode($product->perfume_notes, true);
                                                $noteTitles   = \DB::table('perfume_notes_details')->pluck('title', 'id')->toArray();
                                                $levelTitles  = \DB::table('perfume_notes_level_details')->pluck('title', 'id')->toArray();
                                            @endphp
                                            @if(!empty($perfumeNotes))
                                                @foreach($perfumeNotes as $note)
                                                    <li>
                                                        <h6 class="fw-6 text-black title">{{ $levelTitles[$note['level_id']] ?? 'Unknown' }}</h6>
                                                        <div class="h6">
                                                            @if(!empty($note['note_ids']))
                                                                @foreach($note['note_ids'] as $nid)
                                                                    {{ $noteTitles[$nid] ?? 'Unknown Note' }}{{ !$loop->last ? ' / ' : '' }}
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane wd-product-descriptions" id="faqs">
                            <ul class="faq-list">
                                @php $faqs = json_decode($product->faqs, true); @endphp
                                @if(!empty($faqs))
                                    <li class="faq-item" id="faq-list">
                                        <div class="faq_wrap" id="faq-wrap">
                                            @foreach($faqs as $index => $faq)
                                                <div class="accordion-faq accor-mn-pl">
                                                    <div class="accordion-title {{ $index === 0 ? '' : 'collapsed' }}"
                                                         data-bs-target="#faq-{{ $index }}" role="button" data-bs-toggle="collapse"
                                                         aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                                                        <span class="text h5">{{ $index + 1 }}. {{ $faq['question'] }}</span>
                                                        <span class="icon"><span class="ic-accordion-custom"></span></span>
                                                    </div>
                                                    <div id="faq-{{ $index }}" class="collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#faq-wrap">
                                                        <div class="accordion-body"><p class="h6">{{ $faq['answer'] }}</p></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        @if(trim(strip_tags($product->other_information ?? '')) !== '')
                        <div class="tab-pane wd-product-descriptions" id="other-info">
                            <div class="other-information">{!! $product->other_information !!}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- ═══════════════ CUSTOMER REVIEWS SECTION ═══════════════ --}}
        <section class="review-section flat-spacing-3" style="display:none;">
            <div class="container">
                <h3 class="text-center mb-4 fw-bold">Customer Reviews</h3>

                {{-- Summary box --}}
                <div class="review-summary-box">
                    <div class="rating-big-score">
                        <div class="stars">
                            @for($s = 1; $s <= 5; $s++)
                                <i class="icon-star {{ $s <= round($reviewStats['average']) ? '' : 'text-muted' }}"></i>
                            @endfor
                        </div>
                        <div class="score">{{ $reviewStats['average'] ?: '0.0' }}</div>
                        <div class="count">Based on {{ $reviewStats['total'] }} review{{ $reviewStats['total'] == 1 ? '' : 's' }}</div>
                    </div>

                    <div class="rating-distribution">
                        @for($star = 5; $star >= 1; $star--)
                            @php
                                $count = $reviewStats['distribution'][$star] ?? 0;
                                $pct   = $reviewStats['total'] > 0 ? round(($count / $reviewStats['total']) * 100, 1) : 0;
                            @endphp
                            <div class="dist-row">
                                <span class="stars-mini">
                                    @for($s = 1; $s <= $star; $s++)<i class="icon-star"></i>@endfor
                                </span>
                                <div class="bar-wrap">
                                    <div class="bar-fill" style="width: {{ $pct }}%;"></div>
                                </div>
                                <span class="dist-count">{{ $count }}</span>
                            </div>
                        @endfor
                    </div>

                    <div>
                        <button type="button" class="btn-cancel-review" id="toggleReviewForm">Write a review</button>
                    </div>
                </div>

                {{-- ═══════════ Review Form ═══════════ --}}
                <div class="review-form-wrapper" id="reviewFormWrapper" style="display:none;">
                    <h4 class="text-center mb-3">Write a review</h4>

                    <form id="reviewForm" enctype="multipart/form-data" novalidate>
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        {{-- ═══ STAR RATING — with clear error UI ═══ --}}
                        <div class="text-center mb-3">
                            <label>Rating <span style="color:#dc3545;">*</span></label>

                            <div class="rating-wrapper">
                                <div class="star-rating-input" id="starRatingInput">
                                    <input type="hidden" name="rating" id="ratingValue" value="">
                                    <span class="star" data-value="1" title="Poor">★</span>
                                    <span class="star" data-value="2" title="Fair">★</span>
                                    <span class="star" data-value="3" title="Good">★</span>
                                    <span class="star" data-value="4" title="Very Good">★</span>
                                    <span class="star" data-value="5" title="Excellent">★</span>
                                </div>
                                <span class="rating-selected-text" id="ratingSelectedText"></span>
                                <div class="rating-error-msg" id="ratingErrorMsg">
                                    Please select a star rating before submitting your review.
                                </div>
                            </div>
                        </div>

                        <label>Review Title <span style="color:#dc3545;">*</span> <span class="text-muted small">(100 chars max)</span></label>
                        <input type="text" name="title" maxlength="100" placeholder="Give your review a title" required>

                        <label>Review content <span style="color:#dc3545;">*</span></label>
                        <textarea name="content" placeholder="Start writing here..." required></textarea>

                        <label>Picture/video <span class="text-muted small">(optional)</span></label>
                        <div class="media-upload-box" onclick="document.getElementById('mediaFiles').click();">
                            <div class="icon-upload">⬆</div>
                            <p class="mb-0 text-muted small">Click to upload images or videos</p>
                        </div>
                        <input type="file" id="mediaFiles" name="media[]" multiple accept="image/*,video/*" style="display:none;">
                        <div class="media-preview" id="mediaPreview"></div>

                        <label class="mt-3">Display name <span style="color:#dc3545;">*</span> <span class="text-muted small">(displayed publicly like John S.)</span></label>
                        <input type="text" name="reviewer_name" placeholder="Display name" value="{{ Auth::guard('custom')->check() ? Auth::guard('custom')->user()->name : '' }}" required>

                        <label>Email address <span style="color:#dc3545;">*</span></label>
                        <input type="email" name="reviewer_email" placeholder="Your email address" value="{{ Auth::guard('custom')->check() ? Auth::guard('custom')->user()->email : '' }}" required>

                        <p class="text-muted small mt-2 text-center">
                            How we use your data: We'll only contact you about the review you left, and only if necessary.
                        </p>

                        <div class="review-form-buttons mt-3">
                            <button type="button" class="btn-cancel" id="cancelReviewBtn">Cancel review</button>
                            <button type="submit" class="btn-submit">Submit Review</button>
                        </div>
                    </form>
                </div>

                {{-- Customer Photos --}}
                @if(count($allPhotos))
                    <div class="mb-3">
                        <p class="fw-semibold mb-2">Customer photos & videos</p>
                        <div class="customer-photos">
                            @foreach($allPhotos as $idx => $photo)
                                @if($idx < 7)
                                    <a href="{{ asset('signage/home/reviews/' . $photo) }}" target="_blank">
                                        <img src="{{ asset('signage/home/reviews/' . $photo) }}" alt="Customer photo">
                                    </a>
                                @endif
                            @endforeach
                            @if(count($allPhotos) > 7)
                                <div class="more-count">See more</div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                    <div></div>
                    <select class="review-sort-dropdown" id="reviewSort">
                        <option value="recent">Most Recent</option>
                        <option value="highest">Highest Rated</option>
                        <option value="lowest">Lowest Rated</option>
                    </select>
                </div>

                <div id="reviewsList">
                    @forelse($reviews as $review)
                        {!! App\Http\Controllers\Frontend\ProductReviewController::renderReviewItem($review) !!}
                    @empty
                        <p class="text-center text-muted py-4">No reviews yet. Be the first to write one!</p>
                    @endforelse
                </div>

                @if($reviews->hasMorePages())
                    <div class="text-center">
                        <button id="loadMoreReviews" data-product="{{ $product->id }}" data-page="2">Load More</button>
                    </div>
                @endif
            </div>
        </section>

        {{-- RELATED --}}
        <section class="flat-spacing-3 pt-0 related-slider">
            <div class="container">
                <h1 class="sect-title text-center">Related Products</h1>
                <div dir="ltr" class="swiper tf-swiper wrap-sw-over"
                     data-preview="4" data-tablet="3" data-mobile-sm="2" data-mobile="2"
                     data-space-lg="48" data-space-md="30" data-space="12"
                     data-pagination="2" data-pagination-sm="2" data-pagination-md="3" data-pagination-lg="4">
                    <div class="swiper-wrapper">
                        @foreach($relatedProducts as $related)
                            @php
                                $productImages = json_decode($related->images, true);
                                $firstImage    = $productImages[0] ?? 'default.png';
                                $secondImage   = $productImages[1] ?? $firstImage;

                                $relHasOffer = !empty($related->offer_price);
                                $relDiscount = 0;
                                if (!empty($related->discount) && $related->discount > 0) {
                                    $relDiscount = (int) round($related->discount);
                                } elseif ($relHasOffer && $related->offer_price < $related->price) {
                                    $relDiscount = (int) round((($related->price - $related->offer_price) / $related->price) * 100);
                                }
                                $relatedUrl = route('product.details', [$category->slug, $subcategory->slug, $related->slug]);
                            @endphp
                            <div class="swiper-slide">
                                <div class="card-product">
                                    <div class="card-product_wrapper">
                                        @if($relDiscount > 0)<span class="product-discount-badge">{{ $relDiscount }}% OFF</span>@endif
                                        <a href="{{ $relatedUrl }}" class="product-img">
                                            <img class="lazyload img-product" src="{{ asset('signage/home/productimage/' . $firstImage) }}" alt="{{ $related->product_name }}">
                                            <img class="lazyload img-hover" src="{{ asset('signage/home/productimage/' . $secondImage) }}" alt="{{ $related->product_name }}">
                                        </a>
                                        <ul class="product-action_list">
                                            <li>
                                                <form class="add-to-cart-form d-inline" method="POST" action="{{ route('cart.add') }}">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $related->id }}">
                                                    <button type="submit" class="hover-tooltip tooltip-left box-icon">
                                                        <span class="icon icon-shopping-cart-simple"></span>
                                                        <span class="tooltip">Add to cart</span>
                                                    </button>
                                                </form>
                                            </li>
                                            <li class="wishlist">
                                                <form class="add-to-wishlist-form" data-product="{{ $related->id }}">@csrf
                                                    <button type="button" class="hover-tooltip tooltip-left box-icon wishlist-btn">
                                                        <span class="icon wishlist-icon icon-heart"></span>
                                                        <span class="tooltip">Add to Wishlist</span>
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <a href="{{ $relatedUrl }}" class="hover-tooltip tooltip-left box-icon">
                                                    <span class="icon icon-view"></span>
                                                    <span class="tooltip">Quick view</span>
                                                </a>
                                            </li>
                                        </ul>

                                        {{-- Bestseller / New Arrival tags --}}
                                        <ul class="product-badge_list">
                                            @if($related->is_bestseller)<li class="product-badge_item h6 bestseller">Bestseller</li>@endif
                                            @if($related->is_new_arrival)<li class="product-badge_item h6 new-arrival">New Arrival</li>@endif
                                        </ul>
                                    </div>
                                    <div class="card-product_info">
                                        <a href="{{ $relatedUrl }}" class="name-product h4 link">{{ $related->product_name }}</a>
                                        <div class="price-wrap">
                                            @if($relHasOffer)
                                                <span class="price-old h6 fw-normal">Rs.{{ number_format($related->price) }}</span>
                                                <span class="price-new h6">Rs.{{ number_format($related->offer_price) }}</span>
                                            @else
                                                <span class="price-new h6">Rs.{{ number_format($related->price) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="sw-dot-default tf-sw-pagination"></div>
                </div>
            </div>
        </section>

        @include('components.frontend.footer')
    </div>

    {{-- Lightbox container for review media (images + videos) --}}
    <div class="review-lightbox" id="reviewLightbox">
        <button type="button" class="rl-close" id="reviewLightboxClose">&times;</button>
        <div class="rl-content" id="reviewLightboxContent"></div>
    </div>

    @include('components.frontend.main-js')

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- Review form logic — uses global notyf from main-js        --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <script>
    $(document).ready(function () {

        const ratingLabels = {
            1: '★ Poor',
            2: '★★ Fair',
            3: '★★★ Good',
            4: '★★★★ Very Good',
            5: '★★★★★ Excellent'
        };

        /* Toggle form */
        $('#toggleReviewForm').on('click', function () {
            $('#reviewFormWrapper').slideToggle();
            setTimeout(function () {
                $('html, body').animate({ scrollTop: $('#reviewFormWrapper').offset().top - 100 }, 400);
            }, 350);
        });

        $('#cancelReviewBtn').on('click', function () {
            $('#reviewFormWrapper').slideUp();
            resetForm();
        });

        /* ═══ STAR RATING — click, hover, select ═══ */
        const $starInput = $('#starRatingInput');
        const $stars     = $starInput.find('.star');

        /* Click to select */
        $stars.on('click', function () {
            const val = $(this).data('value');
            $('#ratingValue').val(val);
            fillStars(val);
            $('#ratingSelectedText').text('You rated: ' + ratingLabels[val]);
            hideRatingError();
        });

        /* Hover highlight */
        $stars.on('mouseenter', function () {
            const val = $(this).data('value');
            $stars.each(function () {
                $(this).toggleClass('filled', $(this).data('value') <= val);
            });
        });

        /* Reset hover back to selected */
        $starInput.on('mouseleave', function () {
            const selected = parseInt($('#ratingValue').val()) || 0;
            fillStars(selected);
        });

        function fillStars(count) {
            $stars.each(function () {
                $(this).toggleClass('filled', $(this).data('value') <= count);
            });
        }

        function showRatingError() {
            $('#starRatingInput').addClass('has-error');
            $('#ratingErrorMsg').addClass('show');
            $('html, body').animate({ scrollTop: $('#starRatingInput').offset().top - 150 }, 400);
            setTimeout(() => $('#starRatingInput').removeClass('has-error').addClass('has-error'), 100);
        }

        function hideRatingError() {
            $('#starRatingInput').removeClass('has-error');
            $('#ratingErrorMsg').removeClass('show');
        }

        /* Media preview */
        $('#mediaFiles').on('change', function (e) {
            const files = e.target.files;
            const preview = $('#mediaPreview');
            preview.empty();
            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function (ev) {
                    const isVideo = file.type.startsWith('video/');
                    const mediaTag = isVideo
                        ? `<video src="${ev.target.result}" muted></video>`
                        : `<img src="${ev.target.result}">`;
                    preview.append(`<div class="item">${mediaTag}</div>`);
                };
                reader.readAsDataURL(file);
            });
        });

        /* Clear input error on typing */
        $('#reviewForm').on('input', 'input, textarea', function () {
            $(this).removeClass('input-error');
        });

        /* ═══ SUBMIT with field-by-field validation ═══ */
        $('#reviewForm').on('submit', function (e) {
            e.preventDefault();

            let firstErrorField = null;
            let hasError = false;

            /* 1. Rating check (highest priority) */
            const rating = parseInt($('#ratingValue').val()) || 0;
            if (rating < 1 || rating > 5) {
                showRatingError();
                notyf.error('Please select a star rating first.');
                return;
            }

            /* 2. Other required fields */
            const requiredFields = [
                { name: 'title',          label: 'Review title' },
                { name: 'content',        label: 'Review content' },
                { name: 'reviewer_name',  label: 'Display name' },
                { name: 'reviewer_email', label: 'Email address' },
            ];

            $.each(requiredFields, function (i, f) {
                const $el = $('#reviewForm [name="' + f.name + '"]');
                const val = ($el.val() || '').trim();
                if (!val) {
                    $el.addClass('input-error');
                    if (!firstErrorField) {
                        firstErrorField = { el: $el, label: f.label };
                    }
                    hasError = true;
                } else {
                    $el.removeClass('input-error');
                }
            });

            if (hasError) {
                notyf.error('Please fill in ' + firstErrorField.label + '.');
                $('html, body').animate({ scrollTop: firstErrorField.el.offset().top - 150 }, 400);
                setTimeout(() => firstErrorField.el.focus(), 450);
                return;
            }

            /* Email format */
            const email = $('[name="reviewer_email"]').val();
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                $('[name="reviewer_email"]').addClass('input-error').focus();
                notyf.error('Please enter a valid email address.');
                return;
            }

            /* Submit */
            const formData = new FormData(this);
            const $submitBtn = $('.btn-submit', this);
            $submitBtn.prop('disabled', true).text('Submitting...');

            $.ajax({
                url: "{{ route('review.store') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    if (res.success) {
                        notyf.open({ type: 'custom-success', message: res.message });
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        notyf.error(res.message || 'Something went wrong.');
                        $submitBtn.prop('disabled', false).text('Submit Review');
                    }
                },
                error: function (xhr) {
                    const errs = xhr.responseJSON?.errors;
                    if (errs) {
                        notyf.error(Object.values(errs)[0][0]);
                    } else {
                        notyf.error('Failed to submit review. Please try again.');
                    }
                    $submitBtn.prop('disabled', false).text('Submit Review');
                }
            });
        });

        function resetForm() {
            $('#reviewForm')[0].reset();
            $('#ratingValue').val('');
            $stars.removeClass('filled');
            $('#ratingSelectedText').text('');
            $('#mediaPreview').empty();
            hideRatingError();
            $('#reviewForm input, #reviewForm textarea').removeClass('input-error');
        }

        /* Sort change */
        $('#reviewSort').on('change', function () {
            loadReviews(1, $(this).val(), true);
        });

        /* Load More */
        $('#loadMoreReviews').on('click', function () {
            const page = parseInt($(this).data('page'));
            loadReviews(page, $('#reviewSort').val(), false);
        });

        function loadReviews(page, sort, replace) {
            $.ajax({
                url: "{{ route('review.loadMore') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    product_id: "{{ $product->id }}",
                    page: page,
                    sort: sort
                },
                success: function (res) {
                    if (replace) {
                        $('#reviewsList').html(res.html || '<p class="text-center text-muted py-4">No reviews match this filter.</p>');
                        $('#loadMoreReviews').data('page', 2);
                    } else {
                        $('#reviewsList').append(res.html);
                        $('#loadMoreReviews').data('page', page + 1);
                    }
                    if (!res.has_more) $('#loadMoreReviews').hide();
                    else $('#loadMoreReviews').show();
                    normalizeReviewMedia();   // re-process any newly loaded media
                }
            });
        }

        /* ═══════════════════════════════════════════════════════ */
        /* REVIEW MEDIA — normalize images & videos into clean       */
        /* clickable thumbnails + open in lightbox                   */
        /* ═══════════════════════════════════════════════════════ */
        const $lightbox        = $('#reviewLightbox');
        const $lightboxContent = $('#reviewLightboxContent');

        function openLightbox(type, src) {
            if (type === 'video') {
                $lightboxContent.html(
                    '<video src="' + src + '" controls autoplay playsinline style="max-width:90vw;max-height:90vh;"></video>'
                );
            } else {
                $lightboxContent.html('<img src="' + src + '" alt="Review media">');
            }
            $lightbox.addClass('show');
        }

        function closeLightbox() {
            $lightbox.removeClass('show');
            $lightboxContent.empty();   // stops any playing video
        }

        $('#reviewLightboxClose').on('click', closeLightbox);
        $lightbox.on('click', function (e) {
            if (e.target === this) closeLightbox();   // click backdrop to close
        });
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') closeLightbox();
        });

        // Walk every review media element, tag it, and wire clicks.
        function normalizeReviewMedia() {
            $('#reviewsList img, #reviewsList video').each(function () {
                const $el = $(this);

                // Skip avatars / icons — only touch actual review media thumbs.
                if ($el.closest('.review-avatar').length) return;
                if ($el.hasClass('rl-processed')) return;
                $el.addClass('rl-processed review-media-thumb');

                const isVideo = this.tagName.toLowerCase() === 'video';
                const src = $el.attr('src') ||
                            $el.find('source').attr('src') ||
                            $el.attr('data-src') || '';

                // Strip native controls/autoplay so it shows as a still thumb.
                if (isVideo) {
                    this.removeAttribute('controls');
                    this.removeAttribute('autoplay');
                    this.muted = true;
                    this.preload = 'metadata';
                }

                // Wrap in a clickable container with a play badge for videos.
                if (!$el.parent().hasClass('review-media-item')) {
                    $el.wrap('<span class="review-media-item' + (isVideo ? ' is-video' : '') + '"></span>');
                }

                $el.parent().off('click.rl').on('click.rl', function () {
                    openLightbox(isVideo ? 'video' : 'image', src);
                });
            });
        }

        normalizeReviewMedia();   // run once on initial load
    });
    </script>

    {{-- Wishlist --}}
    <script>
    $(document).ready(function () {
        $(document).on('click', '.btn-add-wishlist', function () {
            const btn = $(this), productId = btn.data('product');
            const icon = btn.find('.icon'), tooltip = btn.find('.tooltip');
            $.ajax({
                url: "{{ route('wishlist.add') }}", method: "POST",
                data: { _token: "{{ csrf_token() }}", product_id: productId },
                success: function (response) {
                    if (response.status === 'added') {
                        icon.removeClass('icon-heart').addClass('icon-heart-filled text-danger');
                        tooltip.text('Remove from Wishlist');
                        notyf.open({ type: 'custom-success', message: response.message });
                    } else if (response.status === 'removed') {
                        icon.removeClass('icon-heart-filled text-danger').addClass('icon-heart');
                        tooltip.text('Add to Wishlist');
                        notyf.open({ type: 'custom-success', message: response.message });
                    }
                    if (response.count !== undefined) $(".wishlist-count").text(response.count);
                }
            });
        });
    });
    </script>

    {{-- Cart + Quantity --}}
    <script>
    $(document).ready(function () {
        $(document).on('click', '.btn-increase', function () {
            const input = $(this).siblings('.quantity-product');
            const val = parseInt(input.val()) || 1, max = parseInt(input.data('max')) || 999;
            if (val < max) input.val(val + 1); else notyf.error('Only ' + max + ' units in stock.');
        });
        $(document).on('click', '.btn-decrease', function () {
            const input = $(this).siblings('.quantity-product');
            input.val(Math.max((parseInt(input.val()) || 1) - 1, 1));
        });
        $(document).on('click', '.btn-add-to-cart', function () {
            const productId = $(this).data('product-id');
            const quantity = parseInt($('.quantity-product').val()) || 1;
            fetch("{{ route('cart.add') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}", "X-Requested-With": "XMLHttpRequest" },
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    $('.cart-count').text(data.cart_count);
                    notyf.open({ type: 'custom-success', message: data.message });
                } else { notyf.error(data.message); }
            });
        });
        $(document).on('submit', '.add-to-cart-form', function (e) {
            e.preventDefault();
            const form = $(this);
            $.ajax({
                url: form.attr('action'), method: 'POST', data: form.serialize(),
                success: function (res) {
                    if (res.success) {
                        notyf.open({ type: 'custom-success', message: res.message });
                        $('#cart-count').text(res.cart_count);
                        setTimeout(() => location.reload(), 500);
                    } else { notyf.error(res.message); }
                }
            });
        });
    });
    </script>

    <script>
    $(document).ready(function () {
        $('#lightSlider').lightSlider({ item: 1, loop: true, slideMargin: 0, gallery: true, thumbItem: 5, auto: true, pause: 3000, speed: 600 });
    });
    </script>

</body>
</html>