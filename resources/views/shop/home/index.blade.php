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

        .product-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
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
    </style>
@endsection
@section('content')
    <section class="bg-light py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold fw-bold mb-3">{{ __('Welcome to our B2B Store') }}</h1>
                    <p class="lead text-muted mb-4">
                        {{ __('messages.shop_welcome_message') }}
                    </p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('shop.products.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-shopping-bag me-2"></i> {{ __('Browse Products') }}
                        </a>
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i> {{ __('Log in') }}
                            </a>
                        @endguest
                    </div>
                </div>
                <div class="col-lg-4">
                    <img src="https://dummyimage.com/500x400/0066cc/ffffff.png&text=B2B+Store"
                         alt="B2B Store" class="img-fluid rounded shadow">
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
                                <div class="product-overlay">
                                    <a href="{{ route('shop.products.show', $product->slug ?? $product->id) }}"
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i> View Details
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title product-title">
                                    <a href="{{ route('shop.products.show', $product->slug ?? $product->id) }}"
                                       class="text-decoration-none text-dark">
                                        {{ $product->StockItemName }}
                                    </a>
                                </h5>
                                <p class="text-muted small">{{ Str::limit($product->MarketingComments, 60) }}</p>

                                @if(\App\Helpers\Features::publicPricesEnabled() || auth()->check())
                                    <h5 class="text-primary mb-3">
                                        @if(auth()->check())
                                            @php
                                                $customer = auth()->user()->customer;
                                                $priceLevel = $customer->price_level ?? 1;
                                                $price = $priceLevel == 1 ? $product->SellingPrice : $product->{"SellingPrice{$priceLevel}"};
                                            @endphp
                                            {{ config('app.currency', 'R') }} {{ number_format($price, 2) }}
                                        @else
                                            {{ config('app.currency', 'R') }} {{ number_format($product->SellingPrice, 2) }}
                                        @endif
                                    </h5>
                                @else
                                    <p class="text-muted">
                                        <a href="{{ route('login') }}" class="text-primary">Login</a> to view prices
                                    </p>
                                @endif
                            </div>
                            <div class="card-footer bg-transparent">
                                @auth
                                    <button class="btn btn-outline-primary btn-sm w-100 add-to-cart"
                                            data-product-id="{{ $product->id }}">
                                        <i class="fas fa-cart-plus me-1"></i> Add to Cart
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="fas fa-sign-in-alt me-1"></i> Login to Order
                                    </a>
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
                        <h5>Bulk Pricing</h5>
                        <p class="text-muted">Competitive prices for wholesale orders</p>
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

@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/nouislider/nouislider.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/wnumb/wNumb.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add to cart functionality
            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.dataset.productId;

                    // Add AJAX call to add to cart
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
                                // Update cart count
                                document.querySelector('#cart-count').textContent = data.cartCount;

                                // Show success message TODO: use a toast library here
                                alert('Product added to cart!');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error adding to cart');
                        });
                });
            });
        });
    </script>
@endsection
