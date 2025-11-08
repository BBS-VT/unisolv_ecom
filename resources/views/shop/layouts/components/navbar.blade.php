<header class="amazon-header">
    <div class="container-fluid px-3">
        <div class="d-flex align-items-center justify-content-between py-2">
            <!-- Logo -->
            <a href="{{ route('shop.home') }}" class="navbar-brand">
                <strong>B2B Shop</strong>
            </a>

            <!-- Search Bar -->
            <div class="amazon-search-bar flex-grow-1 mx-4">
                <div class="input-group">
                    <select class="form-select" style="max-width: 120px; border-radius: 4px 0 0 4px; border-right: none;">
                        <option>All</option>
                        <option>Electronics</option>
                        <option>Office</option>
                        <option>Industrial</option>
                    </select>
                    <input type="text" class="form-control" placeholder="Search products..." id="globalSearch">
                    <button class="amazon-search-btn" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <!-- Right Nav Items -->
            <div class="d-flex align-items-center">
                @include('shop.components.partials.account_nav')
                {{--@guest
                    <a href="#" class="amazon-nav-item me-3" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <span class="nav-label">Hello, sign in</span>
                        <span class="nav-title">Account & Lists</span>
                    </a>
                @else
                    <div class="dropdown me-3">
                        <a href="#" class="amazon-nav-item dropdown-toggle" data-bs-toggle="dropdown">
                            <span class="nav-label">Hello, {{ Auth::user()->PreferredName }}</span>
                            <span class="nav-title">Account & Lists</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('shop.account.index') }}">Your Account</a></li>
                            <li><a class="dropdown-item" href="{{ route('shop.account.orders.index') }}">Your Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                                <i data-feather="power" class="align-self-center icon-xs icon-dual mr-1"></i> {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
--}}{{--                            <li><a class="dropdown-item" href="{{ route('logout') }}">Sign Out</a></li>--}}{{--
                        </ul>
                    </div>
                @endguest--}}

                <a href="{{ route('shop.account.orders.index') }}" class="amazon-nav-item me-3">
                    <span class="nav-label">Returns</span>
                    <span class="nav-title">& Orders</span>
                </a>

                <a href="{{ route('shop.cart.show') }}" class="amazon-nav-item position-relative">
                    <i class="bi bi-cart3" style="font-size: 2rem;"></i>
                    <span class="cart-badge">{{ $cartCount ?? 0 }}</span>
                    <span class="nav-title mt-1">Cart</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Secondary Navigation -->
    @php
        $shopLocations = \App\Models\Location::shopLocations()->get();
        $currentLocation = request('location');
        $cartLocation = session('cart_location');
    @endphp
    <div class="amazon-nav-secondary">
        <div class="container-fluid px-3">
            <div class="d-flex align-items-center">
                {{-- All Departments (disabled if cart is locked to specific location) --}}
                <a href="{{ $cartLocation ? '#' : route('shop.products.index') }}"
                   class="me-3 {{ !$currentLocation ? 'fw-bold text-decoration-underline' : '' }} {{ $cartLocation ? 'text-muted disabled' : '' }}"
                   @if($cartLocation)
                       data-bs-toggle="tooltip"
                   title="Complete your current order to browse other locations"
                   onclick="event.preventDefault();"
                    @endif>
                    <i class="bi bi-list me-1"></i>All
                </a>

                {{-- Individual Location Links --}}
                @foreach($shopLocations as $location)
                    @php
                        $isLocked = $cartLocation && $cartLocation !== $location->LocationCode;
                        $isActive = $currentLocation == $location->LocationCode || $cartLocation == $location->LocationCode;
                    @endphp

                    <a href="{{ $isLocked ? '#' : route('shop.products.index', ['location' => $location->LocationCode]) }}"
                       class="me-3 {{ $isActive ? 'fw-bold text-decoration-underline' : '' }} {{ $isLocked ? 'text-muted' : '' }}"
                       @if($isLocked)
                           data-bs-toggle="tooltip"
                       title="Complete your current order to browse this location"
                       onclick="event.preventDefault();"
                       style="cursor: not-allowed; opacity: 0.5;"
                        @endif>
                        {{ $location->LocationName }}
                        @if($cartLocation == $location->LocationCode)
                            <i class="bi bi-lock-fill ms-1" data-bs-toggle="tooltip" title="Cart locked to this location"></i>
                        @endif
                    </a>
                @endforeach

                <a href="{{ route('shop.products.index', ['featured' => 1]) }}" class="me-3">Today's Deals</a>
                <a href="#" class="me-3">Customer Service</a>
            </div>
        </div>
    </div>

    @if($cartLocation)
        <div class="container-fluid px-3 mt-2">
            <div class="alert alert-info alert-dismissible fade show mb-0" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Shopping from {{ \App\Models\Location::where('LocationCode', $cartLocation)->first()?->LocationName }}</strong>
                - Complete your order or <a href="{{ route('cart.clear') }}" class="alert-link">clear your cart</a> to shop from other locations.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
</header>
