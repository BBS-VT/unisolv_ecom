{{-- Updated amazon-product-card with promotion integration --}}
<div class="amazon-product-card" data-product-url='{{ route('shop.products.show', $product->slug ?? $product->id) }}'>
    <div class="position-relative">
        <img src="{{ $product->photo ? $product->photo->thumbnail : 'https://dummyimage.com/300x300/cccccc/000000.png&text=No+Image' }}" class="amazon-product-image">
        @if($product->is_new)
            <span class="position-absolute top-0 start-0 badge bg-success">{{ __('New') }}</span>
        @endif

        {{-- UPDATED PROMOTION BADGES --}}
        @php
            use App\Helpers\PricingHelper;
            $priceData = PricingHelper::getProductPrice($product, 1);
        @endphp

        @if($priceData['has_promotion'])
            <span class="position-absolute top-0 end-0 badge bg-danger">
                @if($priceData['promotion']->type === 'bogo')
                    BOGO
                @elseif($priceData['savings'] > 0)
                    @php
                        $savingsPercent = $priceData['original_price'] > 0 ? round(($priceData['savings'] / $priceData['original_price']) * 100) : 0;
                    @endphp
                    {{ $savingsPercent }}% OFF
                @else
                    SPECIAL
                @endif
            </span>
        @elseif(isset($product->pricing) && $product->pricing['discount_percentage'] > 0)
            <span class="position-absolute top-0 end-0 badge bg-danger">
                {{ $product->pricing['discount_percentage'] }}% OFF
            </span>
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

    {{-- UPDATED PRICING WITH PROMOTIONS --}}
    @if(isset($product->pricing))
        @if($product->pricing['show_prices'])
            <div class="amazon-price mt-2">
                @if($priceData['has_promotion'] && $priceData['savings'] > 0)
                    {{-- Promotion pricing --}}
                    <div class="promotion-pricing">
                        @php
                            $discountedPrice = $priceData['discounted_price'] / 100;
                            $originalPrice = $priceData['original_price'] / 100;
                            $whole = floor($discountedPrice);
                            $fraction = sprintf('%02d', ($discountedPrice - $whole) * 100);
                        @endphp

                        <span class="amazon-price-whole text-danger">{{ config('app.currency', 'R') }}{{ number_format($whole, 0) }}</span>
                        <span class="amazon-price-fraction text-danger">{{ $fraction }}</span>
                        <br>
                        <span class="amazon-old-price text-muted text-decoration-line-through">
                            {{ config('app.currency', 'R') }}{{ number_format($originalPrice, 2) }}
                        </span>
                        <small class="text-success ms-1">
                            Save {{ $priceData['formatted']['savings'] }}
                            @if($priceData['promotion']->type === 'bogo')
                                (BOGO)
                            @endif
                        </small>
                    </div>
                @elseif(isset($product->pricing) && $product->pricing['discount_percentage'] > 0)
                    {{-- Legacy pricing discount --}}
                    @php
                        $price = $product->pricing['price'];
                        $basePrice = $product->pricing['base_price'];
                        $whole = floor($price);
                        $fraction = sprintf('%02d', ($price - $whole) * 100);
                    @endphp

                    <span class="amazon-price-whole">{{ config('app.currency', 'R') }}{{ number_format($whole, 0) }}</span>
                    <span class="amazon-price-fraction">{{ $fraction }}</span>
                    <br>
                    <span class="amazon-old-price">{{ \App\Helpers\PricingHelper::formatPrice($basePrice) }}</span>
                    <small class="text-success ms-1">Save {{ $product->pricing['discount_percentage'] }}%</small>
                @else
                    {{-- Regular pricing --}}
                    @php
                        $price = $product->pricing['price'];
                        $whole = floor($price);
                        $fraction = sprintf('%02d', ($price - $whole) * 100);
                    @endphp
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
            @if($priceData['has_promotion'])
                @php
                    $discountedPrice = $priceData['discounted_price'] / 100;
                    $originalPrice = $priceData['original_price'] / 100;
                    $whole = floor($discountedPrice);
                    $fraction = sprintf('%02d', ($discountedPrice - $whole) * 100);
                @endphp
                <span class="amazon-price-whole text-danger">{{ config('app.currency', 'R') }}{{ number_format($whole, 0) }}</span>
                <span class="amazon-price-fraction text-danger">{{ $fraction }}</span>
                <br>
                <span class="amazon-old-price text-muted text-decoration-line-through">
                    {{ config('app.currency', 'R') }}{{ number_format($originalPrice, 2) }}
                </span>
            @else
                <span class="amazon-price-whole">{{ config('app.currency', 'R') }}{{ number_format(floor($product->SellingPrice), 0) }}</span>
                <span class="amazon-price-fraction">{{ sprintf('%02d', ($product->SellingPrice - floor($product->SellingPrice)) * 100) }}</span>
            @endif
        </div>
    @endif

    {{-- PROMOTION MESSAGE --}}
    @if($priceData['has_promotion'])
        <div class="promotion-message mt-1">
            <small class="text-success">
                <i class="fas fa-tag me-1"></i>
                @if($priceData['promotion']->type === 'bogo')
                    Buy {{ $priceData['promotion']->buy_quantity ?: 1 }}, Get {{ $priceData['promotion']->get_quantity ?: 1 }} Free!
                @elseif($priceData['promotion']->type === 'quantity_break')
                    Buy {{ $priceData['promotion']->min_quantity }}+ for discount
                @else
                    {{ $priceData['promotion']->name }}
                @endif
            </small>
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
                        data-product-id="{{ $product->id }}">
                    <i class="bi bi-cart-plus me-1 "></i>{{ __('Add to Cart') }}
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
