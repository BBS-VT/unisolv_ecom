<div class="amazon-product-card" data-product-url='{{ route('shop.products.show', $product->slug ?? $product->id) }}'>
    <div class="position-relative">
        <img src="{{ $product->photo ? $product->photo->thumbnail : 'https://dummyimage.com/300x300/cccccc/000000.png&text=No+Image' }}" class="amazon-product-image">

        @if($product->is_new)
            <span class="position-absolute top-0 start-0 badge bg-success">{{ __('New') }}</span>
        @endif

        {{-- PROMOTION BADGES - Using pricing data consistently --}}
        @if(isset($product->pricing))
            @if($product->pricing['has_promotion'] && $product->pricing['discount_percentage'] > 0)
                <span class="position-absolute top-0 end-0 badge bg-danger">
                    {{ $product->pricing['discount_percentage'] }}% OFF
                </span>
            @elseif($product->pricing['discount_percentage'] > 0)
                <span class="position-absolute top-0 end-0 badge bg-danger">
                    {{ $product->pricing['discount_percentage'] }}% OFF
                </span>
            @endif
        @endif

        @if($product->is_featured)
            <span class="position-absolute bottom-0 end-0 badge bg-warning text-dark">
                FEATURED
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

    {{-- PRICING DISPLAY --}}
    @if(isset($product->pricing))
        @if($product->pricing['show_prices'])
            <div class="amazon-price mt-2">
                @if($product->pricing['has_promotion'] && $product->pricing['discount_percentage'] > 0)
                    {{-- Promotion pricing --}}
                    @php
                        // Prices from pricing array are in rands
                        $discountedPrice = $product->pricing['price'];
                        $originalPrice = $product->pricing['customer_price'] ?? $product->pricing['base_price'];
                        $whole = floor($discountedPrice);
                        $fraction = sprintf('%02d', ($discountedPrice - $whole) * 100);
                    @endphp

                    <span class="amazon-price-whole text-danger">
                        {{ config('app.currency', 'R') }}{{ number_format($whole, 0) }}
                    </span>
                    <span class="amazon-price-fraction text-danger">{{ $fraction }}</span>
                    <br>
                    <span class="amazon-old-price text-muted text-decoration-line-through">
                        {{ config('app.currency', 'R') }}{{ number_format($originalPrice, 2) }}
                    </span>
                    <small class="text-success ms-1">
                        Save {{ $product->pricing['discount_percentage'] }}%
                    </small>
                @else
                    {{-- Regular pricing --}}
                    @php
                        $price = $product->pricing['price'];
                        $whole = floor($price);
                        $fraction = sprintf('%02d', ($price - $whole) * 100);
                    @endphp

                    <span class="amazon-price-whole">
                        {{ config('app.currency', 'R') }}{{ number_format($whole, 0) }}
                    </span>
                    <span class="amazon-price-fraction">{{ $fraction }}</span>

                    @if($product->pricing['price_level'] > 1 && $product->pricing['discount_percentage'] > 0)
                        <br>
                        <span class="amazon-old-price text-muted text-decoration-line-through">
                            {{ config('app.currency', 'R') }}{{ number_format($product->pricing['base_price'], 2) }}
                        </span>
                        <small class="text-info ms-1">{{ \App\Helpers\PricingHelper::getPriceTierName() }} Price</small>
                    @elseif(auth()->guest())
                        <br><small class="text-info">Login for wholesale pricing</small>
                    @endif
                @endif

                {{-- Promotion message if available --}}
                @if($product->pricing['has_promotion'] && isset($product->activePromotion))
                    <div class="promotion-message mt-1">
                        <small class="text-success">
                            <i class="fas fa-tag me-1"></i>
                            @if($product->activePromotion->type === 'bogo')
                                Buy {{ $product->activePromotion->buy_quantity ?: 2 }},
                                Get {{ $product->activePromotion->get_quantity ?: 1 }} Free!
                            @elseif($product->activePromotion->type === 'quantity_break')
                                Buy {{ $product->activePromotion->min_quantity }}+ for discount
                            @elseif($product->activePromotion->type === 'date_range')
                                {{ $product->activePromotion->name }}
                                @if($product->activePromotion->ends_at)
                                    <span class="text-muted">(ends {{ $product->activePromotion->ends_at->diffForHumans() }})</span>
                                @endif
                            @else
                                {{ $product->activePromotion->name }}
                            @endif
                        </small>
                    </div>
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
            @php
                $price = $product->SellingPrice;
                $whole = floor($price);
                $fraction = sprintf('%02d', ($price - $whole) * 100);
            @endphp
            <span class="amazon-price-whole">
                {{ config('app.currency', 'R') }}{{ number_format($whole, 0) }}
            </span>
            <span class="amazon-price-fraction">{{ $fraction }}</span>
        </div>
    @endif

    {{-- STOCK STATUS --}}
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

    {{-- ADD TO CART BUTTON --}}
    <div class="mt-3">
        @auth
            @if(($product->stockHolding && $product->stockHolding->QuantityOnHand > 0) || \App\Helpers\Features::backordersEnabled())
                <button class="btn btn-amazon-primary btn-sm add-to-cart-btn w-100"
                        data-product-id="{{ $product->id }}">
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
