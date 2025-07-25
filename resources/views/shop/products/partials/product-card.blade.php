<div class="amazon-product-card" onclick="window.location.href='{{ route('shop.products.show', $product->slug ?? $product->id) }}'">
    <div class="position-relative">
        <img src="{{ $product->photo ? $product->photo->thumbnail : 'https://dummyimage.com/300x300/cccccc/000000.png&text=No+Image' }}" class="amazon-product-image">
        @if($product->is_new)
            <span class="position-absolute top-0 start-0 badge bg-success">{{ __('New') }}</span>
        @endif

        @if(isset($product->pricing) && $product->pricing['discount_percentage'] > 0)
            <span class="position-absolute top-0 end-0 badge bg-danger">
                {{ $product->pricing['discount_percentage'] }}% OFF
            </span>
        @endif

        @if(\App\Helpers\PricingHelper::hasWholesalePricing())
            <span class="position-absolute bottom-0 start-0 badge bg-primary">
                {{ \App\Helpers\PricingHelper::getPriceTierName() }}
            </span>
        @endif
    </div>

    <a href="{{ route('shop.products.show', $product->slug ?? $product->id) }}" class="amazon-product-title text-decoration-none">
        {{ $product->StockItemName }}
    </a>

    @if(isset($product->pricing))
        @if($product->pricing['show_prices'])
            <div class="amazon-price mt-2">
                @php
                    $price = $product->pricing['price'];
                    $basePrice = $product->pricing['base_price'];
                    $whole = floor($price);
                    $fraction = sprintf('%02d', ($price - $whole) * 100);
                @endphp

                @if($product->pricing['discount_percentage'] > 0)
                    {{-- Customer gets a discount --}}
                    <span class="amazon-price-whole">{{ config('app.currency', 'R') }}{{ number_format($whole, 0) }}</span>
                    <span class="amazon-price-fraction">{{ $fraction }}</span>
                    <br>
                    <span class="amazon-old-price">{{ \App\Helpers\PricingHelper::formatPrice($basePrice) }}</span>
                    <small class="text-success ms-1">Save {{ $product->pricing['discount_percentage'] }}%</small>
                @else
                    {{-- Regular pricing --}}
                    <span class="amazon-price-whole">{{ config('app.currency', 'R') }}{{ number_format($whole, 0) }}</span>
                    <span class="amazon-price-fraction">{{ $fraction }}</span>
                    @if(auth()->guest())
                        <br><small class="text-info">Login for wholesale pricing</small>
                    @endif
                @endif
            </div>
        @else
            <div class="text-muted mt-2">
                <small>
                    <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                        Login to see pricing
                    </a>
                </small>
            </div>
        @endif
    @else
        {{-- Fallback if pricing isn't loaded --}}
        <div class="amazon-price mt-2">
            <span class="amazon-price-whole">{{ config('app.currency', 'R') }}{{ number_format(floor($product->SellingPrice), 0) }}</span>
            <span class="amazon-price-fraction">{{ sprintf('%02d', ($product->SellingPrice - floor($product->SellingPrice)) * 100) }}</span>
        </div>
    @endif

    <div class="mt-2">
        @if(\App\Helpers\Features::showStock())
            @if($product->stockHolding && $product->stockHolding->QuantityOnHand > 10)
                <small class="text-success">{{ __('In Stock') }}</small>
            @elseif($product->stockHolding && $product->stockHolding->QuantityOnHand > 0)
                <small class="text-warning">{{ __('Low Stock') }} ({{ $product->stockHolding->QuantityOnHand }})</small>
            @elseif(\App\Helpers\Features::backordersEnabled())
                <small class="text-info">{{ __('Available for Backorder') }}</small>
            @else
                <small class="text-danger">{{ __('Out of Stock') }}</small>
            @endif
        @endif
    </div>

    <div class="mt-3">
        @auth
            @if($product->stockHolding->QuantityOnHand > 0 || \App\Helpers\Features::backordersEnabled())
                <button class="btn btn-amazon-primary btn-sm add-to-cart-btn w-100"
                        data-product-id="{{ $product->id }}"
                        onclick="event.stopPropagation();">
                    <i class="bi bi-cart-plus me-1"></i>{{ __('Add to Cart') }}
                </button>
            @else
                <button class="btn btn-secondary btn-sm w-100" disabled>
                    {{ __('Out of Stock') }}
                </button>
            @endif
        @else
            <button class="btn btn-outline-secondary btn-sm w-100"
                    data-bs-toggle="modal"
                    data-bs-target="#loginModal"
                    onclick="event.stopPropagation();">
                <i class="bi bi-person me-1"></i>{{ __('Login to Order') }}
            </button>
        @endauth
    </div>
</div>
