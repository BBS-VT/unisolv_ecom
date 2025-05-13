@php
    use App\Helpers\Features;
@endphp

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('shop.home') }}">
            {{ config('app.name') }}
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('shop.home') ? 'active' : '' }}"
                        href="{{ route('shop.home') }}">
                        <i class="fas fa-home me-1"></i>{{ __('Home') }}
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('shop.products.*') ? 'active' : '' }}"
                        href="#" id="productsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-box me-1"></i>{{ __('Products') }}
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="productsDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('shop.products.index') }}">
                                <i class="fas fa-list me-1"></i>{{ __('All Products') }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        @foreach(\App\Models\ProductCategory::withCount('products')->having('products_count', '>', 0)->get() as $category)
                            <li>
                                <a class="dropdown-item" href="{{ route('shop.categories.show', $category->slug ?? $category->id) }}">
                                    {{ $category->StockGroupName }}
                                    <span class="badge bg-secondary ms-1">{{ $category->products_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
                @auth
                    @if(Features::orderApprovalRequired())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('shop.drafts.*') ? 'active' : '' }}"
                               href="{{ route('shop.drafts.index') }}">
                                <i class="fas fa-file-alt me-1"></i> Draft Orders
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <form class="d-flex me-2" action="{{ route('shop.products.index') }}" method="GET">
                        <div class="input-group">
                            <input class="form-control form-control-sm" type="search" name="search"
                                   placeholder="Search products..." aria-label="Search"
                                   value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary btn-sm" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </li>


                <li class="nav-item">
                    <a class="nav-link position-relative {{ request()->routeIs('shop.cart.*') ? 'active' : '' }}"
                       href="{{ route('shop.cart.index') }}">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                              id="cart-count">
                            {{ session('cart') ? count(session('cart')) : 0 }}
                        </span>
                    </a>
                </li>

                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i>
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('shop.account.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('shop.account.orders.index') }}">
                                    <i class="fas fa-shopping-bag me-2"></i> My Orders
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('shop.account.profile') }}">
                                    <i class="fas fa-user-edit me-2"></i> Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('shop.account.addresses') }}">
                                    <i class="fas fa-map-marker-alt me-2"></i> Addresses
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="fas fa-cogs me-2"></i> Admin Panel
                                </a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                    </li>
                    @if(Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i> Register
                            </a>
                        </li>
                    @endif
                @endguest
            </ul>
        </div>
    </div>
</nav>

@if(!request()->routeIs('shop.home'))
    <nav aria-label="breadcrumb" class="bg-light py-2">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('shop.home') }}">Home</a></li>
                @yield('breadcrumbs')
            </ol>
        </div>
    </nav>
@endif

@if($announcement = \App\Models\CompanySetting::getSetting('shop_announcement', auth()->user()?->currentCompany()?->id))
    <div class="alert alert-info text-center mb-0 rounded-0" role="alert">
        <i class="fas fa-info-circle me-1"></i> {{ $announcement }}
        <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


<style>
    .navbar {
        box-shadow: 0 2px 4px rgba(0,0,0,.04);
    }

    .navbar-brand {
        font-size: 1.5rem;
        color: #333;
    }

    .nav-link {
        font-weight: 500;
        color: #555;
        padding: .5rem 1rem;
    }

    .nav-link:hover {
        color: #333;
    }

    .nav-link.active {
        color: #0066cc;
        border-bottom: 2px solid #0066cc;
    }

    .dropdown-item {
        padding: .5rem 1.5rem;
    }

    .badge {
        font-size: .75rem;
        min-width: 18px;
        height: 18px;
        padding: 3px 6px;
    }

    #cart-count {
        font-size: .65rem;
    }

    .form-control-sm {
        min-width: 200px;
    }

    @media (max-width: 768px) {
        .form-control-sm {
            min-width: 150px;
        }

        .nav-link.active {
            border-bottom: none;
            border-left: 3px solid #0066cc;
        }
    }
</style>


<script>

    function updateCartCount(count) {
        document.getElementById('cart-count').textContent = count;
    }


    document.addEventListener('cart:updated', function(event) {
        updateCartCount(event.detail.count);
    });
</script>
