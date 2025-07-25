@extends('shop.layouts.app')

@section('title', $product->StockItemName. ' - ' . config('app.name'))

@section('content')
    <div class="container-fluid">
        <nav aria-label="breadcrumb" class="amazon-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('shop.home') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shop.products.index') }}">{{ __('Products') }}</a></li>
                @if($product->category)
                    <li class="breadcrumb-item"><a href="{{ route('shop.products.category', $product->category->slug) }}">{{ $product->category->CategoryName }}</a></li>
                @endif
                <li class="breadcrumb-item active">{{ $product->StockItemName }}</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-6">
                <div class="bg-white p-4 border rounded mb-4">
                    <div class="text-center mb-3">
                        @if($product->getMedia('images')->isNotEmpty())
                            <img src="{{ $product->getFirstMediaUrl('images') }}"
                                 alt="{{ $product->StockItemName }}"
                                 class="img-fluid rounded shadow-sm" id="main-product-image">
                        @elseif($product->photo)
                            <img src="{{ $product->photo->url }}"
                                 alt="{{ $product->StockItemName }}"
                                 class="img-fluid rounded shadow-sm" id="main-product-image">
                        @else
                            <img src="https://dummyimage.com/600x600/f0f0f0/b0b0b0.png&text=No+Image"
                                 alt="{{ $product->StockItemName }}"
                                 class="img-fluid rounded shadow-sm" id="main-product-image">
                        @endif
                    </div>
                    @if($product->getMedia('images')->count() > 1)
                        <div class="d-flex justify-content-center">
                            <div class="thumbnail-container d-flex flex-wrap justify-content-center gap-2">
                                @foreach($product->getMedia('images')->take(5) as $index => $media)
                                    <div class="thumbnail-image {{ $index === 0 ? 'active' : '' }}"
                                         data-src="{{ $media->getUrl() }}"
                                         style="width: 60px; height: 60px; object-fit: contain; border: 2px solid {{ $index === 0 ? 'var(--amazon-orange)' : '#ddd' }}; border-radius: 4px; cursor: pointer;"
                                         onclick="changeMainImage('{{ $media->getUrl() }}', this)">
                                        <img src="{{ $media->getUrl('thumb') }}"
                                             alt="{{ $product->StockItemName }}"
                                             class="img-fluid rounded">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-6">
                <div class="bg-white p-4 border rounded">
                    <h1 class="h3 mb-3 fw-normal">{{ $product->StockItemName }}</h1>
                    @if($product->brand)
                        <p class="mb-2">
                            <span class="text-muted">Brand:</span>
                            <a href="{{ route('shop.products.index', ['brands' => [$product->brand->id]]) }}" class="text-decoration-none text-primary">{{ $product->brand->name }}</a>
                        </p>
                    @endif

                    <hr>

                    <!-- Pricing Section with PricingHelper -->
                    @if(isset($product->pricing))
                        @if($product->pricing['show_prices'])
                            <div class="mb-4">
                                @if($product->pricing['discount_percentage'] > 0)
                                    {{-- Customer gets wholesale discount --}}
                                    <div class="d-flex align-items-baseline mb-2">
                                        <span class="text-muted me-2">List Price:</span>
                                        <span class="text-decoration-line-through text-muted">
                                            {{ \App\Helpers\PricingHelper::formatPrice($product->pricing['base_price']) }}
                                        </span>
                                    </div>
                                    <div class="amazon-price fs-2 mb-2">
                                        @php
                                            $price = $product->pricing['price'];
                                            $whole = floor($price);
                                            $fraction = sprintf('%02d', ($price - $whole) * 100);
                                        @endphp
                                        <span class="amazon-price-whole">{{ config('app.currency', 'R') }}{{ number_format($whole, 0) }}</span>
                                        <span class="amazon-price-fraction">{{ $fraction }}</span>
                                    </div>
                                    <div class="text-success mb-2">
                                        <i class="bi bi-tag me-1"></i>
                                        You Save: {{ \App\Helpers\PricingHelper::formatPrice($product->pricing['base_price'] - $product->pricing['price']) }}
                                        ({{ $product->pricing['discount_percentage'] }}%)
                                    </div>
                                    <div class="mb-2">
                                        <span class="badge bg-primary">
                                            {{ \App\Helpers\PricingHelper::getPriceTierName($product->pricing['price_level']) }} Pricing
                                        </span>
                                    </div>
                                @else
                                    {{-- Regular pricing --}}
                                    <div class="amazon-price fs-2 mb-2">
                                        @php
                                            $price = $product->pricing['price'];
                                            $whole = floor($price);
                                            $fraction = sprintf('%02d', ($price - $whole) * 100);
                                        @endphp
                                        <span class="amazon-price-whole">{{ config('app.currency', 'R') }}{{ number_format($whole, 0) }}</span>
                                        <span class="amazon-price-fraction">{{ $fraction }}</span>
                                    </div>
                                    @if(auth()->guest())
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <a href="#" class="alert-link" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
                                            to access wholesale pricing and place orders.
                                        </div>
                                    @endif
                                @endif

                                @if($product->pricing['tax_rate'] > 0)
                                    <small class="text-muted d-block">
                                        Price excludes tax: {{ \App\Helpers\PricingHelper::formatPrice($product->pricing['price_ex_tax']) }}
                                        (Tax: {{ number_format($product->pricing['tax_rate'], 1) }}%)
                                    </small>
                                @endif
                            </div>
                        @else
                            <div class="mb-4">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <a href="#" class="alert-link" data-bs-toggle="modal" data-bs-target="#loginModal">Please log in</a>
                                    to see pricing information and place orders.
                                </div>
                            </div>
                        @endif
                    @else
                        {{-- Fallback if pricing not loaded --}}
                        <div class="mb-4">
                            <div class="amazon-price fs-2">
                                @php
                                    $price = $product->SellingPrice;
                                    $whole = floor($price);
                                    $fraction = sprintf('%02d', ($price - $whole) * 100);
                                @endphp
                                <span class="amazon-price-whole">{{ config('app.currency', 'R') }}{{ number_format($whole, 0) }}</span>
                                <span class="amazon-price-fraction">{{ $fraction }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Stock Information -->
                    <div class="mb-4">
                        <div class="row g-3">
                            <div class="col-12">
                                @if($product->stockHolding && $product->stockHolding->QuantityOnHand > 10)
                                    <div class="text-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        <strong>In Stock</strong>
                                    </div>
                                @elseif($product->stockHolding && $product->stockHolding->QuantityOnHand > 0)
                                    <div class="text-warning">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        <strong>Only {{ $product->stockHolding->QuantityOnHand }} left in stock - order soon</strong>
                                    </div>
                                @elseif(\App\Helpers\Features::backordersEnabled())
                                    <div class="text-info">
                                        <i class="bi bi-clock me-1"></i>
                                        <strong>Available for backorder</strong>
                                    </div>
                                @else
                                    <div class="text-danger">
                                        <i class="bi bi-x-circle me-1"></i>
                                        <strong>Currently unavailable</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Add to Cart Section -->
                    @auth
                        @if(($product->stockHolding && $product->stockHolding->QuantityOnHand > 0) || \App\Helpers\Features::backordersEnabled())
                            <div class="mb-4">
                                <form id="add-to-cart-form">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                                    <!-- Quantity Selector -->
                                    <div class="row g-3 align-items-end mb-3">
                                        <div class="col-md-4">
                                            <label for="product-quantity" class="form-label fw-bold">Quantity:</label>
                                            <select class="form-select" id="product-quantity" name="quantity">
                                                @for($i = 1; $i <= min(10, $product->stockHolding ? $product->stockHolding->QuantityOnHand : 10); $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                                @if($product->stockHolding && $product->stockHolding->QuantityOnHand > 10)
                                                    <option value="10+">10+</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-amazon-secondary btn-lg">
                                            <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                        </button>
                                        <button type="button" class="btn btn-amazon-primary btn-lg">
                                            <i class="bi bi-lightning me-2"></i>Buy Now
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @else
                        <div class="mb-4">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-amazon-secondary btn-lg" data-bs-toggle="modal" data-bs-target="#loginModal">
                                    <i class="bi bi-person me-2"></i>Login to Add to Cart
                                </button>
                                <button type="button" class="btn btn-amazon-primary btn-lg" data-bs-toggle="modal" data-bs-target="#loginModal">
                                    <i class="bi bi-lightning me-2"></i>Login to Buy Now
                                </button>
                            </div>
                        </div>
                    @endauth

                    <!-- Product Details -->
                    <div class="border-top pt-3">
                        <div class="row g-2 small text-muted">
                            <div class="col-12">
                                <strong>SKU:</strong> {{ $product->StockCode }}
                            </div>
                            @if($product->WeightPerUnit > 0)
                                <div class="col-12">
                                    <strong>Weight:</strong> {{ $product->WeightPerUnit }} kg
                                </div>
                            @endif
                            @if($product->dimensions)
                                <div class="col-12">
                                    <strong>Dimensions:</strong> {{ $product->dimensions }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Additional Options -->
                <div class="bg-white p-4 border rounded mt-3">
                    <h6 class="mb-3">Other Options</h6>
                    <div class="d-grid gap-2">
                        @auth
                            <button class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-heart me-2"></i>Add to Wish List
                            </button>
                            <button class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-bell me-2"></i>Add to Price Watch
                            </button>
                        @endauth
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-share me-2"></i>Share this Product
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Tabs -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="bg-white border rounded">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs border-bottom-0" id="productTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">
                                Product Description
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications" type="button" role="tab">
                                Technical Details
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content p-4" id="productTabsContent">
                        <!-- Description Tab -->
                        <div class="tab-pane fade show active" id="description" role="tabpanel">
                            <h5 class="mb-3">About this item</h5>
                            <div class="product-description">
                                {!! nl2br(e($product->MarketingComments)) !!}
                            </div>

                            @if($product->features && count($product->features) > 0)
                                <h6 class="mt-4 mb-3">Key Features</h6>
                                <ul class="list-unstyled">
                                    @foreach($product->features as $feature)
                                        <li class="mb-2">
                                            <i class="bi bi-check-circle text-success me-2"></i>
                                            {{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <!-- Specifications Tab -->
                        <div class="tab-pane fade" id="specifications" role="tabpanel">
                            <h5 class="mb-3">Technical Details</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td class="fw-bold bg-light" style="width: 30%;">Brand</td>
                                        <td>{{ $product->brand->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold bg-light">Model Number</td>
                                        <td>{{ $product->model_number ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold bg-light">SKU</td>
                                        <td>{{ $product->StockCode }}</td>
                                    </tr>
                                    @if($product->WeightPerUnit)
                                        <tr>
                                            <td class="fw-bold bg-light">Weight</td>
                                            <td>{{ $product->WeightPerUnit }} kg</td>
                                        </tr>
                                    @endif
                                    @if($product->dimensions)
                                        <tr>
                                            <td class="fw-bold bg-light">Dimensions</td>
                                            <td>{{ $product->dimensions }}</td>
                                        </tr>
                                    @endif
                                    @if($product->warranty)
                                        <tr>
                                            <td class="fw-bold bg-light">Warranty</td>
                                            <td>{{ $product->warranty }}</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if(isset($relatedProducts) && $relatedProducts && $relatedProducts->count() > 0)
            <div class="row mt-5">
                <div class="col-12">
                    <div class="bg-white p-4 border rounded">
                        <h4 class="mb-4">Products related to this item</h4>
                        <div class="row g-3">
                            @foreach($relatedProducts->take(4) as $relatedProduct)
                                <div class="col-lg-3 col-md-6">
                                    <div class="amazon-product-card h-100" onclick="window.location.href='{{ route('shop.products.show', $relatedProduct->slug ?? $relatedProduct->id) }}'">
                                        @if($relatedProduct->getMedia('images')->isNotEmpty())
                                            <img src="{{ $relatedProduct->getFirstMediaUrl('images') }}" alt="{{ $relatedProduct->StockItemName }}" class="amazon-product-image">
                                        @elseif($relatedProduct->photo)
                                            <img src="{{ $relatedProduct->photo->thumbnail }}" alt="{{ $relatedProduct->StockItemName }}" class="amazon-product-image">
                                        @else
                                            <img src="https://dummyimage.com/300x300/f0f0f0/b0b0b0.png&text=No+Image" alt="{{ $relatedProduct->StockItemName }}" class="amazon-product-image">
                                        @endif

                                        <a href="{{ route('shop.products.show', $relatedProduct->slug ?? $relatedProduct->id) }}" class="amazon-product-title">
                                            {{ $relatedProduct->StockItemName }}
                                        </a>

                                        @if(isset($relatedProduct->pricing))
                                            @if($relatedProduct->pricing['show_prices'])
                                                <div class="amazon-price mt-2">
                                                    @php
                                                        $price = $relatedProduct->pricing['price'];
                                                        $whole = floor($price);
                                                        $fraction = sprintf('%02d', ($price - $whole) * 100);
                                                    @endphp
                                                    <span class="amazon-price-whole">{{ config('app.currency', 'R') }}{{ number_format($whole, 0) }}</span>
                                                    <span class="amazon-price-fraction">{{ $fraction }}</span>
                                                </div>
                                            @endif
                                        @endif

                                        @auth
                                            <div class="mt-auto">
                                                <button class="btn btn-amazon-primary btn-sm add-to-cart-btn w-100"
                                                        data-product-id="{{ $relatedProduct->id }}"
                                                        onclick="event.stopPropagation();">
                                                    <i class="bi bi-cart-plus me-1"></i>Add to Cart
                                                </button>
                                            </div>
                                        @endauth
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Recently Viewed -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="bg-white p-4 border rounded">
                    <h4 class="mb-4">Your browsing history</h4>
                    <div class="row g-3" id="recently-viewed">
                        <!-- Recently viewed products will be loaded here via JavaScript -->
                        <div class="col-12 text-center text-muted">
                            <p>Start browsing to see your recently viewed products here.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Modal -->
    @guest
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
    @endguest
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Track product view
            trackProductView({{ $product->id }});

            // Quantity selector with custom input
            $('#product-quantity').change(function() {
                if ($(this).val() === '10+') {
                    const maxQty = {{ $product->stockHolding ? $product->stockHolding->QuantityOnHand : 999 }};
                    const customQty = prompt(`Enter quantity (max ${maxQty}):`);
                    if (customQty && !isNaN(customQty) && customQty > 0 && customQty <= maxQty) {
                        // Add custom option
                        $(this).append(`<option value="${customQty}" selected>${customQty}</option>`);
                        $(this).find('option[value="10+"]').remove();
                    } else {
                        $(this).val('10');
                    }
                }
            });

            // Add to cart form submission
            $('#add-to-cart-form').submit(function(e) {
                e.preventDefault();

                const productId = $('input[name="product_id"]').val();
                const quantity = $('#product-quantity').val();
                const button = $(this).find('button[type="submit"]');
                const originalText = button.html();

                // Show loading state
                button.prop('disabled', true);
                button.html('<i class="bi bi-hourglass-split me-2"></i>Adding to Cart...');

                $.ajax({
                    url: '{{ route("shop.cart.add") }}',
                    type: 'POST',
                    data: {
                        product_id: productId,
                        quantity: quantity,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update cart badge
                            $('.cart-badge').text(response.cart_count);

                            // Show success state
                            button.html('<i class="bi bi-check-circle me-2"></i>Added to Cart!');
                            button.removeClass('btn-amazon-secondary').addClass('btn-success');

                            // Show success message
                            showCartToast('Product added to cart successfully', 'success');

                            // Reset button after 3 seconds
                            setTimeout(function() {
                                button.html(originalText);
                                button.removeClass('btn-success').addClass('btn-amazon-secondary');
                                button.prop('disabled', false);
                            }, 3000);
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        showCartToast(response.message || 'Error adding product to cart', 'danger');

                        // Reset button
                        button.html(originalText);
                        button.prop('disabled', false);
                    }
                });
            });

            // Image gallery functionality
            function changeMainImage(src, element) {
                $('#main-product-image').attr('src', src);
                $('.thumbnail-image').removeClass('active').css('border-color', '#ddd');
                $(element).addClass('active').css('border-color', 'var(--amazon-orange)');
            }

            // Make changeMainImage globally accessible
            window.changeMainImage = changeMainImage;

            // Login modal functionality (for guests)
            @guest
            // Password toggle functionality
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');

            if (togglePassword && password) {
                togglePassword.addEventListener('click', function (e) {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                });
            }

            // Enhanced login form handling
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');

            if (loginForm && loginBtn) {
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
            }
            @endguest

            // Toast notification function
            function showCartToast(message, type = 'success') {
                // Create toast if it doesn't exist
                let toast = $('#cartToast');
                if (toast.length === 0) {
                    $('body').append(`
                        <div class="toast-container position-fixed bottom-0 end-0 p-3">
                            <div id="cartToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                                <div class="toast-header">
                                    <i class="bi bi-cart me-2"></i>
                                    <strong class="me-auto">Shopping Cart</strong>
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                                <div class="toast-body"></div>
                            </div>
                        </div>
                    `);
                    toast = $('#cartToast');
                }

                const toastBody = toast.find('.toast-body');
                const toastHeader = toast.find('.toast-header');

                toastBody.text(message);

                // Reset classes
                toastHeader.removeClass('bg-success bg-danger text-white');

                if (type === 'success') {
                    toastHeader.addClass('bg-success text-white');
                } else if (type === 'danger') {
                    toastHeader.addClass('bg-danger text-white');
                }

                const bsToast = new bootstrap.Toast(toast[0]);
                bsToast.show();
            }

            // Product view tracking function
            function trackProductView(productId) {
                // Get recently viewed from localStorage
                let recentlyViewed = JSON.parse(localStorage.getItem('recentlyViewed') || '[]');

                // Remove if already exists
                recentlyViewed = recentlyViewed.filter(id => id !== productId);

                // Add to beginning
                recentlyViewed.unshift(productId);

                // Keep only last 10
                recentlyViewed = recentlyViewed.slice(0, 10);

                // Save back to localStorage
                localStorage.setItem('recentlyViewed', JSON.stringify(recentlyViewed));

                // Load recently viewed products if container exists
                loadRecentlyViewed();
            }

            function loadRecentlyViewed() {
                const container = $('#recently-viewed');
                if (container.length === 0) return;

                const recentlyViewed = JSON.parse(localStorage.getItem('recentlyViewed') || '[]');
                const currentProductId = {{ $product->id }};

                // Filter out current product
                const otherProducts = recentlyViewed.filter(id => id !== currentProductId);

                if (otherProducts.length === 0) {
                    container.html(`
                        <div class="col-12 text-center text-muted">
                            <p>Start browsing to see your recently viewed products here.</p>
                        </div>
                    `);
                    return;
                }

                // Load product data via AJAX (you'll need to create this endpoint)
                $.ajax({
                    url: '{{ route("shop.products.recently-viewed") }}',
                    type: 'POST',
                    data: {
                        product_ids: otherProducts.slice(0, 4), // Show max 4
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.products && response.products.length > 0) {
                            let html = '';
                            response.products.forEach(product => {
                                html += `
                                    <div class="col-lg-3 col-md-6">
                                        <div class="amazon-product-card h-100" onclick="window.location.href='${product.url}'">
                                            <img src="${product.image}" alt="${product.name}" class="amazon-product-image">
                                            <a href="${product.url}" class="amazon-product-title">${product.name}</a>
                                            ${product.price_html}
                                        </div>
                                    </div>
                                `;
                            });
                            container.html(html);
                        }
                    },
                    error: function() {
                        // Silently fail - recently viewed is not critical
                    }
                });
            }

            // Make functions globally accessible
            window.showCartToast = showCartToast;
            window.trackProductView = trackProductView;
        });
    </script>
@endsection
