@extends('shop.layouts.app')

@section('title', $product->StockItemName)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('shop.products.index') }}">Products</a></li>
    @if($product->categories->isNotEmpty())
        <li class="breadcrumb-item">
            <a href="{{ route('shop.categories.show', $product->categories->first()->slug ?? $product->categories->first()->id) }}">
                {{ $product->categories->first()->StockGroupName }}
            </a>
        </li>
    @endif
    <li class="breadcrumb-item active" aria-current="page">{{ $product->StockItemName }}</li>
@endsection

@section('content')
    <div class="container mt-4">
        <!-- Product Details -->
        <div class="card mb-4">
            <div class="card-body p-lg-4">
                <div class="row">
                    <!-- Product Images -->
                    <div class="col-lg-5 mb-4 mb-lg-0">
                        <div class="product-images">
                            <div class="product-main-image mb-3">
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
                                <div class="product-thumbnails d-flex gap-2">
                                    @foreach($product->getMedia('images')->take(5) as $media)
                                        <div class="thumbnail-item border p-1 rounded {{ $loop->first ? 'border-primary' : '' }}"
                                             data-src="{{ $media->getUrl() }}"
                                             style="width: 80px; height: 80px; cursor: pointer;">
                                            <img src="{{ $media->getUrl('thumb') }}"
                                                 alt="{{ $product->StockItemName }}"
                                                 class="img-fluid rounded">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="col-lg-7">
                        <div class="d-flex flex-column h-100">
                            <div class="product-info mb-4">
                                <h1 class="h2 mb-2">{{ $product->StockItemName }}</h1>

                                <div class="mb-3 d-flex align-items-center">
                                    <span class="badge bg-secondary me-2">SKU: {{ $product->StockCode }}</span>

                                    @if(\App\Helpers\Features::showStock())
                                        @if($product->stockHolding && $product->stockHolding->QuantityOnHand > 0)
                                            <span class="badge bg-success me-2">In Stock ({{ $product->stockHolding->QuantityOnHand }})</span>
                                        @else
                                            <span class="badge bg-danger me-2">Out of Stock</span>
                                        @endif
                                    @endif

                                    @if($product->is_featured)
                                        <span class="badge bg-warning me-2">Featured</span>
                                    @endif
                                </div>

                                @if($product->MarketingComments)
                                    <p class="text-muted mb-3">{{ $product->MarketingComments }}</p>
                                @endif

                                @if($product->categories->isNotEmpty())
                                    <div class="mb-3">
                                        <div class="text-muted mb-1">Categories:</div>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($product->categories as $category)
                                                <a href="{{ route('shop.categories.show', $category->slug ?? $category->id) }}"
                                                   class="badge bg-light text-dark text-decoration-none">
                                                    {{ $category->StockGroupName }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Pricing Section -->
                                @if($product->pricing['show_prices'])
                                    <div class="pricing-section mb-4 p-3 bg-light rounded">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <h3 class="text-primary mb-0">
                                                    {{ config('app.currency', 'R') }} {{ number_format($product->pricing['price'], 2) }}
                                                    @if($product->pricing['tax_rate'] > 0)
                                                        <small class="text-muted">incl. VAT</small>
                                                    @endif
                                                </h3>
                                                @if($product->pricing['discount_percentage'] > 0)
                                                    <div class="mt-1">
                                                        <small class="text-muted text-decoration-line-through">
                                                            {{ config('app.currency', 'R') }} {{ number_format($product->pricing['base_price'], 2) }}
                                                        </small>
                                                        <span class="badge bg-danger ms-1">
                                                        {{ $product->pricing['discount_percentage'] }}% OFF
                                                    </span>
                                                    </div>
                                                @endif

                                                @if($product->pricing['tax_rate'] > 0)
                                                    <div class="mt-1 small text-muted">
                                                        Excl. VAT: {{ config('app.currency', 'R') }} {{ number_format($product->pricing['price_ex_tax'], 2) }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-md-6">
                                                @auth
                                                    <form id="add-to-cart-form" class="mt-3 mt-md-0" data-product-id="{{ $product->id }}">
                                                        @csrf
                                                        <div class="d-flex flex-column flex-md-row gap-2">
                                                            <div class="input-group">
                                                                <button type="button" class="btn btn-outline-secondary decrease-qty">
                                                                    <i class="fas fa-minus"></i>
                                                                </button>
                                                                <input type="number" class="form-control text-center"
                                                                       name="quantity" value="1" min="1"
                                                                       max="{{ $product->stockHolding ? $product->stockHolding->QuantityOnHand : 999 }}">
                                                                <button type="button" class="btn btn-outline-secondary increase-qty">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </div>

                                                            <button type="submit" class="btn btn-primary flex-grow-1"
                                                                {{ (!\App\Helpers\Features::backordersEnabled() && (!$product->stockHolding || $product->stockHolding->QuantityOnHand <= 0)) ? 'disabled' : '' }}>
                                                                <i class="fas fa-cart-plus me-1"></i> Add to Cart
                                                            </button>
                                                        </div>
                                                    </form>
                                                @else
                                                    <a href="{{ route('login') }}" class="btn btn-outline-primary mt-3 mt-md-0 w-100">
                                                        <i class="fas fa-sign-in-alt me-1"></i> Login to Order
                                                    </a>
                                                @endauth
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <a href="{{ route('login') }}" class="alert-link">Login</a> to view pricing and place orders.
                                    </div>
                                @endif
                            </div>

                            <!-- Product Details -->
                            <div class="product-details mt-auto">
                                <ul class="nav nav-tabs" id="productDetailTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                                                data-bs-target="#description-tab-pane" type="button" role="tab"
                                                aria-controls="description-tab-pane" aria-selected="true">
                                            Description
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="specs-tab" data-bs-toggle="tab"
                                                data-bs-target="#specs-tab-pane" type="button" role="tab"
                                                aria-controls="specs-tab-pane" aria-selected="false">
                                            Specifications
                                        </button>
                                    </li>
                                    @if(\App\Helpers\Features::showStock())
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="stock-tab" data-bs-toggle="tab"
                                                    data-bs-target="#stock-tab-pane" type="button" role="tab"
                                                    aria-controls="stock-tab-pane" aria-selected="false">
                                                Stock Info
                                            </button>
                                        </li>
                                    @endif
                                </ul>
                                <div class="tab-content p-3 border border-top-0 rounded-bottom" id="productDetailTabsContent">
                                    <div class="tab-pane fade show active" id="description-tab-pane" role="tabpanel" aria-labelledby="description-tab" tabindex="0">
                                        @if($product->InternalComments)
                                            <div class="product-description">
                                                {{ $product->InternalComments }}
                                            </div>
                                        @else
                                            <p class="text-muted">No detailed description available for this product.</p>
                                        @endif
                                    </div>
                                    <div class="tab-pane fade" id="specs-tab-pane" role="tabpanel" aria-labelledby="specs-tab" tabindex="0">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-sm">
                                                    <tr>
                                                        <td class="text-muted">Product Code</td>
                                                        <td class="fw-medium">{{ $product->StockCode }}</td>
                                                    </tr>
                                                    @if($product->Barcode)
                                                        <tr>
                                                            <td class="text-muted">Barcode</td>
                                                            <td class="fw-medium">{{ $product->Barcode }}</td>
                                                        </tr>
                                                    @endif
                                                    @if($product->Brand)
                                                        <tr>
                                                            <td class="text-muted">Brand</td>
                                                            <td class="fw-medium">{{ $product->Brand }}</td>
                                                        </tr>
                                                    @endif
                                                    @if($product->Size)
                                                        <tr>
                                                            <td class="text-muted">Size</td>
                                                            <td class="fw-medium">{{ $product->Size }}</td>
                                                        </tr>
                                                    @endif
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-sm">
                                                    @if($product->LeadTimeDays)
                                                        <tr>
                                                            <td class="text-muted">Lead Time</td>
                                                            <td class="fw-medium">{{ $product->LeadTimeDays }} days</td>
                                                        </tr>
                                                    @endif
                                                    @if($product->Weight)
                                                        <tr>
                                                            <td class="text-muted">Weight</td>
                                                            <td class="fw-medium">{{ $product->Weight }} kg</td>
                                                        </tr>
                                                    @endif
                                                    @if($product->taxType)
                                                        <tr>
                                                            <td class="text-muted">Tax Rate</td>
                                                            <td class="fw-medium">{{ $product->taxType->percent }}%</td>
                                                        </tr>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    @if(\App\Helpers\Features::showStock())
                                        <div class="tab-pane fade" id="stock-tab-pane" role="tabpanel" aria-labelledby="stock-tab" tabindex="0">
                                            @if($product->stockHolding)
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-0">Current Stock</h5>
                                                        <p class="text-muted mb-0">Available for immediate shipping</p>
                                                    </div>
                                                    <div>
                                                <span class="fs-4 fw-bold {{ $product->stockHolding->QuantityOnHand > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $product->stockHolding->QuantityOnHand }}
                                                </span>
                                                    </div>
                                                </div>
                                                @if(\App\Helpers\Features::backordersEnabled())
                                                    <div class="alert alert-info">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        Backorders are available for this product.
                                                    </div>
                                                @elseif($product->stockHolding->QuantityOnHand <= 0)
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        This product is currently out of stock. Please check back later.
                                                    </div>
                                                @endif
                                            @else
                                                <p class="text-muted">No stock information available for this product.</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->isNotEmpty())
            <section class="related-products mb-4">
                <h2 class="h4 mb-3">Related Products</h2>
                <div class="row">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="col-md-6 col-lg-3 mb-4">
                            @include('shop.products.partials.product-card', ['product' => $relatedProduct])
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Product image gallery
            const mainImage = document.getElementById('main-product-image');
            const thumbnails = document.querySelectorAll('.thumbnail-item');

            thumbnails.forEach(thumb => {
                thumb.addEventListener('click', function() {
                    // Update main image
                    mainImage.src = this.dataset.src;

                    // Update active thumbnail
                    thumbnails.forEach(t => t.classList.remove('border-primary'));
                    this.classList.add('border-primary');
                });
            });

            // Quantity buttons
            const decreaseBtn = document.querySelector('.decrease-qty');
            const increaseBtn = document.querySelector('.increase-qty');
            const quantityInput = document.querySelector('input[name="quantity"]');

            if (decreaseBtn && increaseBtn && quantityInput) {
                decreaseBtn.addEventListener('click', function() {
                    const currentValue = parseInt(quantityInput.value);
                    if (currentValue > 1) {
                        quantityInput.value = currentValue - 1;
                    }
                });

                increaseBtn.addEventListener('click', function() {
                    const currentValue = parseInt(quantityInput.value);
                    const maxValue = parseInt(quantityInput.getAttribute('max'));

                    if (!maxValue || currentValue < maxValue) {
                        quantityInput.value = currentValue + 1;
                    }
                });
            }

            // Add to cart functionality
            const addToCartForm = document.getElementById('add-to-cart-form');

            if (addToCartForm) {
                addToCartForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const productId = this.dataset.productId;
                    const quantity = this.querySelector('input[name="quantity"]').value;
                    const button = this.querySelector('button[type="submit"]');

                    // Disable button during submission
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Adding...';

                    fetch('{{ route("shop.cart.add") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: quantity
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update cart count in navbar
                                document.querySelector('#cart-count').textContent = data.cartCount;

                                // Reset button
                                button.innerHTML = '<i class="fas fa-check me-1"></i> Added to Cart!';
                                button.classList.remove('btn-primary');
                                button.classList.add('btn-success');

                                setTimeout(() => {
                                    button.disabled = false;
                                    button.innerHTML = '<i class="fas fa-cart-plus me-1"></i> Add to Cart';
                                    button.classList.remove('btn-success');
                                    button.classList.add('btn-primary');
                                }, 2000);
                            } else {
                                // Show error
                                alert(data.message || 'Error adding to cart');
                                button.disabled = false;
                                button.innerHTML = '<i class="fas fa-cart-plus me-1"></i> Add to Cart';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error adding to cart');
                            button.disabled = false;
                            button.innerHTML = '<i class="fas fa-cart-plus me-1"></i> Add to Cart';
                        });
                });
            }

            // Initialize Bootstrap tabs
            const triggerTabList = document.querySelectorAll('#productDetailTabs button');
            triggerTabList.forEach(triggerEl => {
                const tabTrigger = new bootstrap.Tab(triggerEl);
                triggerEl.addEventListener('click', event => {
                    event.preventDefault();
                    tabTrigger.show();
                });
            });
        });
    </script>
@endsection
