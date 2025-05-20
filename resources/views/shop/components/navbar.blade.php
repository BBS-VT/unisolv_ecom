@php
    use App\Helpers\Features;
@endphp
<header>
    <div class="container-fluid">
        <div class="row py-3 border-bottom">

            <div class="col-sm-4 col-lg-3 text-center text-sm-start">
                <div class="main-logo">
                    <a href="{{ route('shop.home') }}">

                       <img src="#" alt="" class="img-fluid">
                    </a>
                </div>
            </div>

            <div class="col-sm-6 offset-sm-2 offset-md-0 col-lg-5 d-none d-lg-block">
                <div class="search-bar row bg-light p-2 my-2 rounded-4">
                    <div class="col-md-4 d-none d-md-block">

                        <select class="form-select border-0 bg-transparent">
                            @foreach(\App\Models\ProductCategory::withCount('products')->having('products_count', '>', 0)->orderBy('StockGroupName')->get() as $category)
                                <option>
                                    <a class="dropdown-item d-flex justify-content-between align-items-center"
                                       href="{{ route('shop.categories.show', $category->slug ?? $category->id) }}">
                                        <span>{{ $category->StockGroupName }}</span>
                                        <span class="badge bg-secondary rounded-pill ms-2">{{ $category->products_count }}</span>
                                    </a>
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-11 col-md-7">
                        <form id="search-form" class="text-center" action="index.html" method="post">
                            <input type="text" class="form-control border-0 bg-transparent" placeholder="Search for products" />
                        </form>
                    </div>
                    <div class="col-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M21.71 20.29L18 16.61A9 9 0 1 0 16.61 18l3.68 3.68a1 1 0 0 0 1.42 0a1 1 0 0 0 0-1.39ZM11 18a7 7 0 1 1 7-7a7 7 0 0 1-7 7Z"/></svg>
                    </div>
                </div>
            </div>

            <div class="col-sm-8 col-lg-4 d-flex justify-content-end gap-5 align-items-center mt-4 mt-sm-0 justify-content-center justify-content-sm-end">
                <div class="support-box text-end d-none d-xl-block">
                    <span class="fs-6 text-muted">Support Nr</span>
                    <h5 class="mb-0"></h5>
                </div>

                <ul class="d-flex justify-content-end list-unstyled m-0">
                    @auth
                    <li>
                        <a href="#" class="rounded-circle bg-light p-2 mx-1">
                            <svg width="24" height="24" viewBox="0 0 24 24">
                                <i class="fas fa-user-circle me-1"></i>
                            </svg>
                        </a>
                    </li>
                    @endauth
                    <li>
                        <a href="#" class="rounded-circle bg-light p-2 mx-1">
                            <svg width="24" height="24" viewBox="0 0 24 24"><use xlink:href="#heart"></use></svg>
                        </a>
                    </li>
                    <li class="d-lg-none">
                        <a href="#" class="rounded-circle bg-light p-2 mx-1" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCart" aria-controls="offcanvasCart">
                            <svg width="24" height="24" viewBox="0 0 24 24"><use xlink:href="#cart"></use></svg>
                        </a>
                    </li>
                    <li class="d-lg-none">
                        <a href="#" class="rounded-circle bg-light p-2 mx-1" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSearch" aria-controls="offcanvasSearch">
                            <svg width="24" height="24" viewBox="0 0 24 24"><use xlink:href="#search"></use></svg>
                        </a>
                    </li>
                </ul>

                <div class="cart text-end d-none d-lg-block dropdown">
                    <button class="border-0 bg-transparent d-flex flex-column gap-2 lh-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCart" aria-controls="offcanvasCart">
                        <span class="fs-6 text-muted dropdown-toggle">Your Cart</span>
                        <span class="cart-total fs-5 fw-bold">{{ session('cart') ? count(session('cart')) : 0 }}</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</header>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand fw-bold" href="">
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
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('shop.products.*') || request()->routeIs('shop.categories.*') ? 'active' : '' }}"
                       href="#" id="productsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-box me-1"></i> Products
                    </a>
                    <ul class="dropdown-menu dropdown-menu-scrollable" aria-labelledby="productsDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('shop.products.index') }}">
                                <i class="fas fa-th me-2"></i> All Products
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li class="dropdown-header text-muted">Categories</li>
                        <div class="category-scroll-container">
                            @foreach(\App\Models\ProductCategory::withCount('products')->having('products_count', '>', 0)->orderBy('StockGroupName')->get() as $category)
                                <li>
                                    <a class="dropdown-item d-flex justify-content-between align-items-center"
                                       href="{{ route('shop.categories.show', $category->slug ?? $category->id) }}">
                                        <span>{{ $category->StockGroupName }}</span>
                                        <span class="badge bg-secondary rounded-pill ms-2">{{ $category->products_count }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </div>
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
                       href="{{ route('shop.cart.show') }}">
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
                                <a class="dropdown-item" href="{{ route('shop.account.index') }}">
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

                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('home') }}">
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
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#shopLoginModal">
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
                @endauth
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


{{--<style>
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
</style>--}}


<script>

    function updateCartCount(count) {
        document.getElementById('cart-count').textContent = count;
    }


    document.addEventListener('cart:updated', function(event) {
        updateCartCount(event.detail.count);
    });
</script>
