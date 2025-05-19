
<div class="card h-100 product-card">
    <div class="product-image-wrapper position-relative" style="height: 250px;">
        <img src="{{ $product->photo ? $product->photo->thumbnail : 'https://dummyimage.com/300x300/cccccc/000000.png&text=No+Image' }}"
             class="card-img-top h-100 object-fit-cover"
             alt="{{ $product->StockItemName }}">

        {{-- Stock indicator --}}
        @if(\App\Helpers\Features::showStock())
            @if($product->stockHolding && $product->stockHolding->QuantityOnHand > 0)
                <span class="position-absolute top-0 end-0 m-2 badge bg-success">
                    In Stock ({{ $product->stockHolding->QuantityOnHand }})
                </span>
            @else
                <span class="position-absolute top-0 end-0 m-2 badge bg-danger">
                    Out of Stock
                </span>
            @endif
        @endif

        {{-- Quick view overlay --}}
        <div class="product-overlay">
            <a href="{{ route('shop.products.show', $product->slug ?? $product->id) }}"
               class="btn btn-light btn-sm">
                <i class="fas fa-eye me-1"></i> View Details
            </a>
        </div>
    </div>

    <div class="card-body d-flex flex-column">
        <h5 class="card-title product-title">
            <a href="{{ route('shop.products.show', $product->slug ?? $product->id) }}"
               class="text-decoration-none text-dark">
                {{ $product->StockItemName }}
            </a>
        </h5>

        <p class="text-muted small mb-2">
            SKU: {{ $product->StockCode }}
        </p>

        @if($product->MarketingComments)
            <p class="text-muted small flex-grow-1">
                {{ Str::limit($product->MarketingComments, 60) }}
            </p>
        @endif

        @if($product->pricing['show_prices'])
            <div class="pricing-section mt-auto">
                <h5 class="text-primary mb-0">
                    {{ config('app.currency', 'R') }} {{ number_format($product->pricing['price'], 2) }}
                    @if($product->pricing['tax_rate'] > 0)
                        <small class="text-muted">incl. VAT</small>
                    @endif
                </h5>
                @if($product->pricing['discount_percentage'] > 0)
                    <small class="text-muted text-decoration-line-through">
                        {{ config('app.currency', 'R') }} {{ number_format($product->pricing['base_price'], 2) }}
                    </small>
                    <span class="badge bg-danger ms-1">
                        {{ $product->pricing['discount_percentage'] }}% OFF
                    </span>
                @endif
            </div>
        @else
            <p class="text-muted mb-0 mt-auto">
                <a href="#" data-bs-toggle="modal" data-bs-target="#shopLoginModal" class="text-primary">Login</a> to view prices
            </p>
        @endif
    </div>

    <div class="card-footer bg-transparent">
        @auth
            <form class="add-to-cart-form" data-product-id="{{ $product->id }}">
                @csrf
                <div class="input-group">
                    <input type="number" class="form-control form-control-sm"
                           name="quantity" value="1" min="1"
                           max="{{ $product->stockHolding ? $product->stockHolding->QuantityOnHand : 999 }}">
                    <button type="submit" class="btn btn-primary btn-sm"
                        {{ (!\App\Helpers\Features::backordersEnabled() && (!$product->stockHolding || $product->stockHolding->QuantityOnHand <= 0)) ? 'disabled' : '' }}>
                        <i class="fas fa-cart-plus me-1"></i> Add to Cart
                    </button>
                </div>
            </form>
        @else
            <a href="#" data-bs-toggle="modal" data-bs-target="#shopLoginModal" class="btn btn-outline-secondary btn-sm w-100">
                <i class="fas fa-sign-in-alt me-1"></i> Login to Order
            </a>
        @endauth
    </div>
</div>

<style>
    .product-card {
        transition: all 0.3s ease;
        border: 1px solid #e3e3e3;
        height: 100%;
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
        font-size: 1rem;
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

    .object-fit-cover {
        object-fit: cover;
    }

    .pricing-section {
        border-top: 1px solid #e3e3e3;
        padding-top: 0.75rem;
    }
</style>
