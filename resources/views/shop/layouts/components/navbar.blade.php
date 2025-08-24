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
    <div class="amazon-nav-secondary">
        <div class="container-fluid px-3">
            <div class="d-flex align-items-center">
                <a href="#" class="me-3">
                    <i class="bi bi-list me-1"></i>All
                </a>
                <a href="{{ route('shop.products.index') }}" class="me-3">Today's Deals</a>

                <a href="#" class="me-3">Customer Service</a>
            </div>
        </div>
    </div>
</header>
