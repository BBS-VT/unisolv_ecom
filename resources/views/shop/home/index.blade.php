@extends('shop.layouts.app')

@section('title', __('Shop Home'))

@section('styles')
    <link href="{{ URL::asset('build/libs/nouislider/nouislider.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .category-card {
            transition: all 0.3s ease;
            border: 1px solid #e3e3e3;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: #0066cc;
        }

        .product-card {
            transition: all 0.3s ease;
            border: 1px solid #e3e3e3;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .product-title {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 48px;
        }

        .product-image-wrapper {
            overflow: hidden;
            position: relative;
        }

        .product-card:hover .product-overlay {
            opacity: 1;
        }

        .feature-box {
            padding: 2rem;
        }

        .feature-icon {
            transition: transform 0.3s ease;
        }

        .feature-box:hover .feature-icon {
            transform: scale(1.1);
        }

        .category-icon i {
            transition: color 0.3s ease;
        }

        .category-card:hover .category-icon i {
            color: #0056b3 !important;
        }

        .dropdown-menu-scrollable {
            max-height: 400px;
            overflow-y: auto;
        }

        .category-scroll-container {
            max-height: 320px;
            overflow-y: auto;
        }

        /* Custom scrollbar for the category dropdown */
        .category-scroll-container::-webkit-scrollbar {
            width: 6px;
        }

        .category-scroll-container::-webkit-scrollbar-track {
            background: #f8f9fa;
        }

        .category-scroll-container::-webkit-scrollbar-thumb {
            background: #dee2e6;
            border-radius: 3px;
        }

        .category-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #ced4da;
        }

        /* For Firefox */
        .category-scroll-container {
            scrollbar-width: thin;
            scrollbar-color: #dee2e6 #f8f9fa;
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
        }

        .dropdown-header {
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
        }

        /* Price tier badge styles */
        .price-tier-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }

        .tier-retail { background-color: #e3f2fd; color: #1976d2; }
        .tier-wholesale { background-color: #f3e5f5; color: #7b1fa2; }
        .tier-special { background-color: #fff3e0; color: #f57c00; }
        .tier-premium { background-color: #e8f5e8; color: #388e3c; }

        .hero-section {
            position: relative;
            overflow: hidden;
            background-color: #6c757d;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .hero-section.has-background-image::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 0;
        }

        .hero-section .container {
            position: relative;
            z-index: 1;
        }

    </style>
@endsection

@section('content')
<main>
    @php
        $heroBackgroundImagePath = 'shop/images/hero-banner.jpg';
        $hasHeroBackgroundImage = file_exists(public_path($heroBackgroundImagePath));
        $heroStyle = $hasHeroBackgroundImage
            ? "background-image: url('" . asset($heroBackgroundImagePath) . "');"
            : '';
        $heroClass = $hasHeroBackgroundImage ? 'hero-section has-background-image' : 'hero-section bg-secondary';
    @endphp

    <section class="{{ $heroClass }} text-white" style="{{ $heroStyle }}">
        <div class="container">
            <div class="row justify-content-between align-items-center">
                <div class="col-12 col-md-7 col-lg-6 text-center text-md-left">
                    <h1 class="display-2 mb-4">{{ __('Welcome to our ') }} <br class="d-none d-md-inline">{{ __('Store') }}</h1>
                    <p class="lead mb-4 text-muted">{{ __('Browse our products and discover competitive wholesale pricing. Login to access your negotiated rates.') }}</p>
                    <a href="{{ route('shop.products.index') }}" class="btn btn-tertiary me-3 animate-up-2">
                        <i class="fas fa-shopping-bag me-2"></i> {{ __('Shop Now') }}
                    </a>
                </div>

                <div class="col-12 col-md-5 d-none d-md-block text-center">
                    <img src="{{ asset('shop/images/shop_home.png') }}" alt="">
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">{{ __('Shop by Category') }}</h2>
            <div class="row">
                @foreach($categories as $category)
                    <div class="col-md-4 col-lg-3 mb-4">
                        <a href="{{ route('shop.categories.show', $category->slug ?? $category->id) }}"
                           class="text-decoration-none">
                            <div class="card h-100 category-card">
                                <div class="card-body text-center">
                                    <div class="category-icon mb-3">
                                        <i class="fas fa-box fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="card-title">{{ $category->StockGroupName }}</h5>
                                    <p class="text-muted small">{{ $category->products_count }} Products</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('shop.products.index') }}" class="btn btn-outline-primary">
                    {{ __('View All Categories') }} <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Featured Products</h2>

            <div class="row">
                @foreach($featuredProducts as $product)
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card h-100 product-card">
                            <div class="product-image-wrapper position-relative" style="height: 200px;">
                                <img src="{{ $product->photo ? $product->photo->thumbnail : 'https://dummyimage.com/300x300/cccccc/000000.png&text=Product' }}"
                                     class="card-img-top h-100 object-fit-cover"
                                     alt="{{ $product->StockItemName }}">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title product-title">
                                    <a href="{{ route('shop.products.show', $product->slug ?? $product->id) }}"
                                       class="text-decoration-none text-dark">
                                        {{ $product->StockItemName }}
                                    </a>
                                </h5>
                                <p class="text-muted small">{{ Str::limit($product->MarketingComments, 60) }}</p>

                                @if($product->pricing['show_prices'])
                                    <div class="pricing mb-3">
                                        <h5 class="text-primary mb-1">
                                            {{ config('app.currency', 'R') }} {{ number_format($product->pricing['price'], 2) }}
                                        </h5>
                                        @if($product->pricing['discount_percentage'] > 0)
                                            <small class="text-muted">
                                                <s>{{ config('app.currency', 'R') }} {{ number_format($product->pricing['base_price'], 2) }}</s>
                                                <span class="text-success ms-1">
                                                    Save {{ $product->pricing['discount_percentage'] }}%
                                                </span>
                                            </small>
                                        @elseif(auth()->guest())
                                            <small class="text-info">
                                                <i class="fas fa-info-circle me-1"></i>Login for wholesale pricing
                                            </small>
                                        @endif
                                    </div>
                                @else
                                    <div class="pricing mb-3">
                                        <p class="text-muted">
                                            <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a> to view prices
                                        </p>
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer bg-transparent">
                                @auth
                                    <button class="btn btn-outline-primary btn-sm w-100 add-to-cart"
                                            data-product-id="{{ $product->id }}">
                                        <i class="fas fa-cart-plus me-1"></i> Add to Cart
                                    </button>
                                @else
                                    <button type="button" class="btn btn-outline-secondary btn-sm w-100"
                                            data-bs-toggle="modal" data-bs-target="#loginModal">
                                        <i class="fas fa-sign-in-alt me-1"></i> Login to Order
                                    </button>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-4">
                    <div class="feature-box">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-truck fa-3x text-primary"></i>
                        </div>
                        <h5>Fast Delivery</h5>
                        <p class="text-muted">Quick and reliable delivery to your business</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="feature-box">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-tags fa-3x text-primary"></i>
                        </div>
                        <h5>Wholesale Pricing</h5>
                        <p class="text-muted">Competitive prices based on your customer tier</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="feature-box">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-shield-alt fa-3x text-primary"></i>
                        </div>
                        <h5>Secure Ordering</h5>
                        <p class="text-muted">Safe and secure B2B transactions</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="feature-box">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-headset fa-3x text-primary"></i>
                        </div>
                        <h5>Customer Support</h5>
                        <p class="text-muted">Dedicated support for business customers</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="loginModalLabel">
                        <i class="fas fa-user-circle me-2 text-primary"></i>Customer Login
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <div class="text-center mb-4">
                        <p class="text-muted">Login to access your wholesale pricing and place orders</p>
                    </div>

                    <form id="loginForm" method="POST" action="{{ route('login') }}">
                        @csrf
                        <input type="hidden" name="redirect_to_shop" value="1">

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="{{ old('email') }}" required autofocus>
                            </div>
                            <div class="invalid-feedback" id="email-error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="password-error"></div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="loginBtn">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Need an account? <a href="#" class="text-primary">Contact us for business registration</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
    <script src="{{ URL::asset('build/libs/nouislider/nouislider.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/wnumb/wNumb.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password toggle functionality
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');

            togglePassword.addEventListener('click', function (e) {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });

            // Enhanced login form handling
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');

            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Clear previous errors
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

                // Show loading state
                loginBtn.disabled = true;
                loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging in...';

                // Submit form via fetch for better error handling
                fetch(loginForm.action, {
                    method: 'POST',
                    body: new FormData(loginForm),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (response.ok) {
                            // Success - reload the page to show logged in state
                            window.location.reload();
                        } else {
                            return response.json();
                        }
                    })
                    .then(data => {
                        if (data && data.errors) {
                            // Show validation errors
                            Object.keys(data.errors).forEach(field => {
                                const input = document.getElementById(field);
                                const errorDiv = document.getElementById(field + '-error');
                                if (input && errorDiv) {
                                    input.classList.add('is-invalid');
                                    errorDiv.textContent = data.errors[field][0];
                                }
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Login error:', error);
                        alert('An error occurred during login. Please try again.');
                    })
                    .finally(() => {
                        // Reset button state
                        loginBtn.disabled = false;
                        loginBtn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>Login';
                    });
            });

            // Add to cart functionality
            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    const originalText = this.innerHTML;

                    // Show loading state
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Adding...';

                    fetch('{{ route("shop.cart.add") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: 1
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update cart count if element exists
                                const cartCount = document.querySelector('#cart-count');
                                if (cartCount) {
                                    cartCount.textContent = data.cartCount;
                                }

                                // Show success state briefly
                                this.innerHTML = '<i class="fas fa-check me-1"></i> Added!';
                                this.classList.remove('btn-outline-primary');
                                this.classList.add('btn-success');

                                setTimeout(() => {
                                    this.innerHTML = originalText;
                                    this.classList.remove('btn-success');
                                    this.classList.add('btn-outline-primary');
                                    this.disabled = false;
                                }, 2000);
                            } else {
                                throw new Error(data.message || 'Failed to add to cart');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error adding to cart: ' + error.message);
                            this.innerHTML = originalText;
                            this.disabled = false;
                        });
                });
            });
        });
    </script>
@endpush
