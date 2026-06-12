<header class="tf-header header-fix header-abs-1">
    <div class="container">
        <div class="row align-items-center">

            {{-- ── Mobile hamburger ── --}}
            <div class="col-md-4 col-3 d-xl-none menu-part-one">
                <a href="#mobileMenu" data-bs-toggle="offcanvas" class="btn-mobile-menu">
                    <span></span>
                </a>
            </div>

            {{-- ── Logo ── --}}
            <div class="col-xl-3 col-md-4 col-6 d-flex justify-content-center justify-content-xl-start">
                <a href="{{ url('/') }}" class="logo-site">
                    <img src="{{ asset('frontend/assets/images/logo/logo.webp') }}" alt="Logo">
                </a>
            </div>
            
            

            {{-- ── Desktop navigation ── --}}
            @php
                $masterCategories = \App\Models\CategoryDetails::all();

                /*
                |--------------------------------------------------------------
                | Helper: does this sub-category have any products?
                |--------------------------------------------------------------
                | sub_category_id in products_details may be stored as:
                |   (a) a plain integer        → e.g. 5
                |   (b) a JSON array of ints   → e.g. [1, 2]
                |   (c) a JSON array of strings→ e.g. ["1", "2"]
                | This helper checks ALL of them safely.
                */
                if (!function_exists('subHasProductsCheck')) {
                    function subHasProductsCheck($subId) {
                        return \App\Models\ProductsDetails::where(function ($q) use ($subId) {
                            $q->where('sub_category_id', $subId)                              // plain int (old rows)
                              ->orWhere('sub_category_id', (string) $subId)                   // plain string
                              ->orWhereJsonContains('sub_category_id', (int) $subId)          // JSON array of int
                              ->orWhereJsonContains('sub_category_id', (string) $subId);     // JSON array of string
                        })->exists();
                    }
                }
            @endphp

            <div class="col-xl-6 d-none d-xl-block">
                <nav class="box-navigation">
                    <ul class="box-nav-menu">
                        <li class="menu-item position-relative">
                            <a href="{{ route('crazy.index') }}" class="item-link">
                                Today's Deal
                            </a>
                        </li>
                        <li class="menu-item position-relative">
                            <a href="{{ route('product.all') }}" class="item-link">
                                Shop All
                            </a>
                        </li>

                        @foreach($masterCategories as $master)
                            @php
                                // GUARD: skip categories missing a slug
                                if (empty($master->slug)) continue;

                                $masterHasProducts = \App\Models\ProductsDetails::where('category_id', $master->id)->exists();

                                $subCategories = \App\Models\SabCategoryDetails::where('category_id', $master->id)
                                    ->whereNotNull('slug')
                                    ->where('slug', '!=', '')
                                    ->get();
                            @endphp

         
                            <li class="menu-item position-relative">
                                <a href="{{ $masterHasProducts ? route('product.category', $master->slug) : route('coming.soon') }}"
                                   class="item-link">
                                    {{ $master->category_name }}
                            
                                    {{-- show arrow only if dropdown exists --}}
                                    @if($masterHasProducts || $subCategories->count() > 0)
                                        <i class="icon icon-caret-down"></i>
                                    @endif
                                </a>
                            
                                @if($masterHasProducts || $subCategories->count() > 0)
                                    <div class="sub-menu">
                                        <ul class="sub-menu_list">
                            
                                            {{-- All Category --}}
                                            <li>
                                                <a href="{{ $masterHasProducts ? route('product.category', $master->slug) : route('coming.soon') }}"
                                                   class="sub-menu_link">
                                                    All {{ $master->category_name }}
                                                </a>
                                            </li>
                            
                                            @foreach($subCategories as $sub)
                                                @php
                                                    $subHasProducts = subHasProductsCheck($sub->id);
                                                @endphp
                                                <li>
                                                    <a href="{{ $subHasProducts
                                                            ? route('product.subcategory', ['category' => $master->slug, 'sabcat' => $sub->slug])
                                                            : route('coming.soon') }}"
                                                       class="sub-menu_link">
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
            
            

            {{-- ── Icon bar (user / search / wishlist / cart) ── --}}
            <div class="col-xl-3 col-md-4 col-3 menu-part-four">
                <ul class="nav-icon-list">

                    {{-- User dropdown --}}
                    <li class="nav-item dropdown d-none d-lg-flex">
                        <a class="nav-icon-item icon link dropdown-toggle" href="#" id="userDropdown">
                            <i class="icon icon-user"></i>
                            <i class="icon icon-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu login-sec" aria-labelledby="userDropdown" id="header-login-sec">
                            @auth('custom')
                                <li>
                                    <span class="dropdown-item">
                                    <strong>
                                        Welcome, 
                                        {{
                                            !empty(Auth::guard('custom')->user()->name)
                                                ? ucwords(Auth::guard('custom')->user()->name)
                                                : ucwords(str_replace('.', ' ', explode('@', Auth::guard('custom')->user()->email)[0]))
                                        }}
                                    </strong>                                   
                                    </span>
                                </li>
                                <li><a class="dropdown-item" href="{{ route('frontend.account') }}">My Account</a></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                </li>
                                <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display:none;">
                                    @csrf
                                </form>
                            @else
                                <li><a class="dropdown-item" href="{{ route('user.login') }}">Login</a></li>
                                <li><a class="dropdown-item" href="{{ route('user.registration') }}">Register</a></li>
                            @endauth
                        </ul>
                    </li>

                    {{-- Search --}}
                    <li class="d-md-flex">
                        <a class="nav-icon-item link" href="#"
                           data-bs-toggle="modal" data-bs-target="#searchModal" aria-label="Search">
                            <i class="icon icon-magnifying-glass"></i>
                        </a>
                    </li>

                    {{-- Wishlist --}}
                    <li class="d-sm-flex position-relative">
                        <a class="nav-icon-item link" href="{{ route('wishlist.index') }}">
                            <i class="icon icon-heart"></i>
                            <span class="wishlist-count">
                                @if(Auth::guard('custom')->check())
                                    {{ \App\Models\Wishlist::where('user_id', Auth::guard('custom')->id())->count() }}
                                @else
                                    {{ \App\Models\Wishlist::where('session_id', session()->getId())->count() }}
                                @endif
                            </span>
                        </a>
                    </li>

                    {{-- Cart --}}
                    @php
                        $cartCount = Auth::guard('custom')->check()
                            ? \App\Models\Cart::where('user_id', Auth::guard('custom')->id())->count()
                            : \App\Models\Cart::where('session_id', session()->getId())->count();
                    @endphp
                    <li class="shop-cart">
                        <a class="nav-icon-item link" href="{{ route('cart.index') }}">
                            <i class="icon icon-shopping-cart-simple"></i>
                            <span class="count cart-count">{{ $cartCount }}</span>
                        </a>
                    </li>

                </ul>
            </div>

        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- Search Modal                                                    --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg rounded-4 border-0">
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title">
                        <i class="icon icon-magnifying-glass me-2"></i>Enter Search
                    </h5>
                    <button type="button" class="btn-close btn-close-white"
                            data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-light">
                            <i class="icon icon-magnifying-glass"></i>
                        </span>
                        <input id="globalSearchInput" class="form-control rounded-3" type="search"
                               placeholder="Search products, categories..." autocomplete="off">
                    </div>
                    <div id="searchDropdown"
                         class="list-group rounded-3 shadow-sm overflow-auto"
                         style="max-height:360px;"></div>
                </div>
            </div>
        </div>
    </div>

    <style>
    #searchDropdown .list-group-item { transition: all 0.2s; cursor: pointer; }
    #searchDropdown .list-group-item:hover { background-color: #f1f5f9; transform: translateX(5px); }
    #searchDropdown small { color: #6c757d; }
    .box-nav-menu { display: flex; align-items: center; justify-content: center; gap: 32px; }

    @media (max-width: 991px) {
        .box-nav-menu { display: block; padding: 0; margin: 0; }
        .box-nav-menu .menu-item { display: block; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .sub-menu { display: none; padding-left: 15px; margin-top: 5px; }
        .menu-item.active .sub-menu { display: block; }
        .caret-toggle { cursor: pointer; font-size: 16px; transition: transform 0.3s ease; }
        .menu-item.active .caret-toggle { transform: rotate(180deg); }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const input         = document.getElementById('globalSearchInput');
        const dropdown      = document.getElementById('searchDropdown');
        const searchModalEl = document.getElementById('searchModal');
        const searchModal   = new bootstrap.Modal(searchModalEl);

        input?.addEventListener('input', debounce(async (e) => {
            const query = e.target.value.trim();
            if (query.length < 2) { dropdown.innerHTML = ''; return; }

            dropdown.innerHTML = `<div class="list-group-item text-center text-muted">Searching...</div>`;

            try {
                const res = await fetch(`{{ route('global.search') }}?q=${encodeURIComponent(query)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) throw new Error('Network error');
                renderResults(await res.json());
            } catch {
                dropdown.innerHTML = `<div class="list-group-item text-danger">Search failed</div>`;
            }
        }, 250));

        function renderResults(items) {
            if (!items || items.length === 0) {
                dropdown.innerHTML = `<div class="list-group-item text-muted">No results</div>`;
                return;
            }
            dropdown.innerHTML = items.map(i => {
                return `<a href="${i.url}" class="list-group-item list-group-item-action search-result-item">
                            <div>${i.title}</div>
                            <small>${i.type}</small>
                        </a>`;
            }).join('');

            dropdown.querySelectorAll('.search-result-item').forEach(a => {
                a.onclick = (e) => {
                    e.preventDefault();
                    searchModal.hide();
                    searchModalEl.addEventListener('hidden.bs.modal', function redirect() {
                        searchModalEl.removeEventListener('hidden.bs.modal', redirect);
                        window.location.href = a.href;
                    });
                };
            });
        }

        function debounce(fn, delay) {
            let timer;
            return (...args) => { clearTimeout(timer); timer = setTimeout(() => fn(...args), delay); };
        }

        searchModalEl.addEventListener('shown.bs.modal',  () => input?.focus());
        searchModalEl.addEventListener('hidden.bs.modal', () => { dropdown.innerHTML = ''; input.value = ''; });

        document.querySelectorAll('.caret-toggle').forEach(icon => {
            icon.addEventListener('click', function (e) {
                e.preventDefault();
                this.closest('.menu-item')?.classList.toggle('active');
            });
        });
    });
    </script>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- Offcanvas Mobile Menu                                           --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="offcanvas offcanvas-start canvas-mb" id="mobileMenu">
        <span class="icon-close-popup" data-bs-dismiss="offcanvas">
            <i class="icon-close"></i>
        </span>

        <div class="canvas-header text-center">
            <p class="text-logo-mb mb-2">
                <img src="{{ asset('frontend/assets/images/logo/logo.webp') }}" alt="Logo" class="lazyload">
            </p>
            <span class="br-line d-block mx-auto"></span>
        </div>

        <div class="canvas-body" style="color:black;">
            <div class="mb-content-top">
                @php
                    $masterCategories = \App\Models\CategoryDetails::all();
                @endphp

                <ul class="nav-ul-mb list-unstyled" id="wrapper-menu-navigation">
                    <li class="menu-item mb-1 border-bottom">
                            <a href="{{ route('crazy.index') }}" class="item-link">
                                Today's Deal
                            </a>
                        </li>
                        <li class="menu-item mb-1 border-bottom">
                            <a href="{{ route('product.all') }}" class="item-link">
                                Shop All
                            </a>
                        </li>
                    @foreach($masterCategories as $index => $master)
                        @php
                            // GUARD: skip categories missing a slug
                            if (empty($master->slug)) continue;

                            $masterHasProducts = \App\Models\ProductsDetails::where('category_id', $master->id)->exists();

                            $subCategories = \App\Models\SabCategoryDetails::where('category_id', $master->id)
                                ->whereNotNull('slug')
                                ->where('slug', '!=', '')
                                ->get();
                        @endphp

                        <li class="menu-item mb-1 border-bottom">
                            @if($subCategories->count() > 0)
                                <div class="d-flex justify-content-between align-items-center py-2">
                                    <a href="{{ $masterHasProducts ? route('product.category', $master->slug) : route('coming.soon') }}"
                                       class="item-link text-dark fw-semibold">
                                        {{ $master->category_name }}
                                    </a>

                                    <button class="btn-toggle-caret bg-transparent border-0 p-0"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#submenu-{{ $index }}"
                                            aria-expanded="false"
                                            aria-controls="submenu-{{ $index }}">
                                        <i class="icon icon-caret-down text-dark"></i>
                                    </button>
                                </div>

                                <div id="submenu-{{ $index }}" class="collapse"
                                     data-bs-parent="#wrapper-menu-navigation">
                                    <ul class="sub-menu_list ps-3">
                                        @foreach($subCategories as $sub)
                                            @php
                                                $subHasProducts = subHasProductsCheck($sub->id);
                                            @endphp
                                            <li class="py-1">
                                                <a href="{{ $subHasProducts
                                                        ? route('product.subcategory', ['category' => $master->slug, 'sabcat' => $sub->slug])
                                                        : route('coming.soon') }}"
                                                   class="sub-menu_link text-dark">
                                                    {{ $sub->sab_category_name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <a href="{{ $masterHasProducts ? route('product.category', $master->slug) : route('coming.soon') }}"
                                   class="item-link text-dark fw-semibold d-block py-2">
                                    {{ $master->category_name }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Mobile Account Buttons --}}
            <div class="mobile-login-menu d-flex flex-column gap-2 mt-3">
                @auth('custom')
                    <span class="mb-2 text-dark fw-bold">
                            Welcome, 
                            {{
                                !empty(Auth::guard('custom')->user()->name)
                                    ? ucwords(Auth::guard('custom')->user()->name)
                                    : explode('@', Auth::guard('custom')->user()->email)[0]
                            }}
                        </span>
                    <a href="{{ route('frontend.account') }}"
                       class="tf-btn type-small style-2 w-100 text-center py-2">My Account</a>
                    <a href="{{ route('user.logout') }}"
                       class="tf-btn type-small style-2 w-100 text-center py-2"
                       onclick="event.preventDefault(); document.getElementById('mobile-logout-form').submit();">
                        Logout
                    </a>
                    <form id="mobile-logout-form" action="{{ route('user.logout') }}" method="POST" style="display:none;">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('user.login') }}"
                       class="tf-btn type-small style-2 w-100 text-center py-2 mb-1">
                        Login <i class="icon icon-user ms-1"></i>
                    </a>
                    <a href="{{ route('user.registration') }}"
                       class="tf-btn type-small style-2 w-100 text-center py-2">
                        Register <i class="icon icon-user ms-1"></i>
                    </a>
                @endauth
            </div>

            {{-- Social Icons --}}
            <div class="flow-us-wrap mt-3">
                <h5 class="title text-dark">Follow us on</h5>
                <ul class="tf-social-icon d-flex gap-3">
                    <li><a href="https://www.facebook.com/"  target="_blank" class="text-dark"><i class="icon-fb"></i></a></li>
                    <li><a href="https://www.instagram.com/" target="_blank" class="text-dark"><i class="icon-instagram-logo"></i></a></li>
                    <li><a href="https://x.com/"            target="_blank" class="text-dark"><i class="icon-x"></i></a></li>
                </ul>
            </div>
        </div>
    </div>

</header>