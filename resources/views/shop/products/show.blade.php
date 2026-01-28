@extends('shop.layouts.app')

@section('title', $product->StockItemName. ' - ' . config('app.name'))

<style>
    .pack-size-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .pack-size-option {
        position: relative;
        border: 2px solid #e9ecef;
        border-radius: 0.5rem;
        overflow: hidden;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .pack-size-option:hover {
        border-color: #007bff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .pack-size-option.selected {
        border-color: #007bff;
        background-color: #f8f9ff;
    }

    .pack-option-content {
        padding: 1rem;
        position: relative;
    }

    .pack-option-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
    }

    .pack-size-badge {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .pack-price {
        text-align: right;
    }

    .price-whole {
        font-size: 1.5rem;
        font-weight: 700;
        color: #dc3545;
    }

    .price-fraction {
        font-size: 1rem;
        font-weight: 600;
        color: #dc3545;
        vertical-align: top;
    }

    .pack-name {
        font-weight: 600;
        color: #343a40;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .unit-price {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .savings-badge {
        display: inline-block;
        background-color: #28a745;
        color: white;
        padding: 0.125rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        margin-left: 0.5rem;
    }

    .stock-info {
        font-size: 0.8rem;
    }

    .stock-good { color: #28a745; }
    .stock-low { color: #ffc107; }
    .stock-backorder { color: #17a2b8; }
    .stock-out { color: #dc3545; }

    .pack-option-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 123, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .pack-size-option:hover .pack-option-overlay {
        opacity: 1;
    }

    .pack-size-option.selected .pack-option-overlay {
        display: none;
    }

    .overlay-content {
        text-align: center;
    }

    .pack-comparison {
        border-top: 1px solid #e9ecef;
        padding-top: 1rem;
    }

    @media (max-width: 768px) {
        .pack-size-selector {
            grid-template-columns: 1fr;
        }

        .pack-option-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .pack-price {
            margin-left: 0;
            margin-top: 0.5rem;
            text-align: left;
        }
    }

    /* Update quantity selector to be pack-size aware */
    .quantity-info {
        background: #f8f9fa;
        padding: 0.75rem;
        border-radius: 0.375rem;
        margin-top: 0.5rem;
    }

    .total-units-display {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }
</style>

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
                            <img src="{{ asset('shop/images/no-image_600.png') }}"
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

                    @php
                        use App\Services\PromotionCalculationService;
                        use App\Helpers\PricingHelper;

                        $promotionService = app(PromotionCalculationService::class);
                        $priceData = PricingHelper::getProductPrice($product, 1);
                        $customerTier = auth()->user() ? (auth()->user()->price_level ?? 1) : 1;
                    @endphp

                    @if($priceData['has_promotion'])
                        <div class="promotion-alert alert alert-success border-success mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-tag text-success me-2 fs-4"></i>
                                <div>
                                    <h6 class="mb-1 text-success">Special Promotion Active!</h6>
                                    <p class="mb-0 small">
                                        <strong>{{ $priceData['promotion']->name }}</strong>
                                        @if($priceData['promotion']->ends_at)
                                            <br><span class="text-muted">Valid until {{ $priceData['promotion']->ends_at->format('M j, Y g:i A') }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <hr>

                    <!-- Pricing Section with PricingHelper -->
                    @if(isset($product->pricing))
                        @if($product->pricing['show_prices'])
                            <div class="mb-4">
                                @if($priceData['has_promotion'] && $priceData['savings'] > 0)
                                    {{-- Promotion pricing --}}
                                    <div class="promotion-pricing-section">
                                        <div class="d-flex align-items-baseline mb-2">
                                            <span class="text-muted me-2">Regular Price:</span>
                                            <span class="text-decoration-line-through text-muted">
                                                {{ $priceData['formatted']['original'] }}
                                            </span>
                                        </div>

                                        <div class="amazon-price fs-1 mb-2">
                                            @php
                                                $discountedPrice = $priceData['discounted_price'] / 100;
                                                $whole = floor($discountedPrice);
                                                $fraction = sprintf('%02d', ($discountedPrice - $whole) * 100);
                                            @endphp
                                            <span class="amazon-price-whole text-danger">{{ config('app.currency', 'R') }}{{ number_format($whole, 0) }}</span>
                                            <span class="amazon-price-fraction text-danger">{{ $fraction }}</span>
                                        </div>

                                        <div class="promotion-savings mb-3">
                                            <div class="text-success mb-2 fs-5">
                                                <i class="fas fa-star me-1"></i>
                                                You Save: {{ $priceData['formatted']['savings'] }}
                                                @php
                                                    $savingsPercent = $priceData['original_price'] > 0 ? round(($priceData['savings'] / $priceData['original_price']) * 100) : 0;
                                                @endphp
                                                ({{ $savingsPercent }}%)
                                            </div>

                                            {{-- Promotion details --}}
                                            <div class="promotion-details alert alert-light border-warning">
                                                @if($priceData['promotion']->type === 'bogo')
                                                    <i class="fas fa-gift text-warning me-1"></i>
                                                    <strong>Buy {{ $priceData['promotion']->buy_quantity ?: 1 }}, Get {{ $priceData['promotion']->get_quantity ?: 1 }} Free!</strong>
                                                @elseif($priceData['promotion']->type === 'quantity_break')
                                                    <i class="fas fa-boxes text-warning me-1"></i>
                                                    <strong>Quantity Break:</strong> Buy {{ $priceData['promotion']->min_quantity }}+ items for this discount
                                                @elseif($priceData['promotion']->type === 'date_range')
                                                    <i class="fas fa-calendar text-warning me-1"></i>
                                                    <strong>Limited Time Offer:</strong> Special pricing ends {{ $priceData['promotion']->ends_at->format('M j, Y') }}
                                                @else
                                                    <i class="fas fa-star text-warning me-1"></i>
                                                    <strong>{{ $priceData['promotion']->description ?: 'Special promotion active' }}</strong>
                                                @endif
                                            </div>
                                        </div>

                                        @if(auth()->user())
                                            <div class="mb-2">
                                                <span class="badge bg-primary">
                                                    {{ \App\Helpers\PricingHelper::getPriceTierName($customerTier) }} Pricing + Promotion
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @elseif($product->pricing['discount_percentage'] > 0)
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
                        {{-- Fallback if pricing not loaded but check for promotions --}}
                        <div class="mb-4">
                            @if($priceData['has_promotion'])
                                <div class="amazon-price fs-1 mb-2">
                                    @php
                                        $discountedPrice = $priceData['discounted_price'] / 100;
                                        $originalPrice = $priceData['original_price'] / 100;
                                        $whole = floor($discountedPrice);
                                        $fraction = sprintf('%02d', ($discountedPrice - $whole) * 100);
                                    @endphp
                                    <span class="amazon-price-whole text-danger">{{ config('app.currency', 'R') }}{{ number_format($whole, 0) }}</span>
                                    <span class="amazon-price-fraction text-danger">{{ $fraction }}</span>
                                </div>
                                <div class="text-muted text-decoration-line-through">
                                    Was: {{ config('app.currency', 'R') }}{{ number_format($originalPrice, 2) }}
                                </div>
                                <div class="text-success">
                                    You Save: {{ $priceData['formatted']['savings'] }}
                                </div>
                            @else
                                <div class="amazon-price fs-2">
                                    @php
                                        $price = $product->SellingPrice;
                                        $whole = floor($price);
                                        $fraction = sprintf('%02d', ($price - $whole) * 100);
                                    @endphp
                                    <span class="amazon-price-whole">{{ config('app.currency', 'R') }}{{ number_format($whole, 0) }}</span>
                                    <span class="amazon-price-fraction">{{ $fraction }}</span>
                                </div>
                            @endif
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

                    @if($packSizeFamily->count() > 1)
                        <div class="mb-4">
                            <h6 class="mb-3">
                                <i class="fax fa-boxes me-2"></i>{{ __('Available Pack Sizes') }}
                            </h6>
                            <div class="pack-size-selector">
                                @foreach($packSizeFamily as $packOption)
                                    <div class="pack-size-option {{ $packOption->id === $product->id ? 'selected' : '' }}"
                                         data-product-id="{{ $packOption->id }}"
                                         data-pack-size="{{ $packOption->pack_size ?? 1 }}"
                                         data-product-url="{{ route('shop.products.show', $packOption->slug ?? $packOption->id) }}">

                                        <div class="pack-option-content">
                                            <div class="pack-option-header">
                                                <div class="pack-size-badge">
                                                    {{ $packOption->pack_size ?? 1 }} {{ ($packOption->pack_size ?? 1) == 1 ? 'unit' : 'units' }}
                                                </div>

                                                @if($packOption->pricing && $packOption->pricing['show_prices'])
                                                    <div class="pack-price">
                                                        @php
                                                            $price = $packOption->pricing['price'];
                                                            $whole = floor($price);
                                                            $fraction = sprintf('%02d', ($price - $whole) * 100);
                                                        @endphp
                                                        <span class="price-whole">{{ config('app.currency', 'R') }}{{ number_format($whole, 0) }}</span>
                                                        <span class="price-fraction">{{ $fraction }}</span>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="pack-option-details">
                                                <div class="pack-name">{{ $packOption->StockItemName }}</div>

                                                @if($packOption->pricing && $packOption->pricing['show_prices'] && isset($packOption->unit_price))
                                                    <div class="unit-price">
                                                        {{ config('app.currency', 'R') }}{{ number_format($packOption->unit_price, 2) }} per unit

                                                        @if(isset($packOption->savings_per_unit) && $packOption->savings_per_unit > 0)
                                                            <span class="savings-badge">
                                        Save {{ config('app.currency', 'R') }}{{ number_format($packOption->savings_per_unit, 2) }} per unit
                                        ({{ $packOption->savings_percentage }}%)
                                    </span>
                                                        @endif
                                                    </div>
                                                @endif

                                                <div class="stock-info">
                                                    @if($packOption->stockHolding && $packOption->stockHolding->QuantityOnHand > 5)
                                                        <span class="stock-good">
                                    <i class="bi bi-check-circle me-1"></i>In Stock
                                </span>
                                                    @elseif($packOption->stockHolding && $packOption->stockHolding->QuantityOnHand > 0)
                                                        <span class="stock-low">
                                    <i class="bi bi-exclamation-triangle me-1"></i>{{ $packOption->stockHolding->QuantityOnHand }} left
                                </span>
                                                    @elseif(\App\Helpers\Features::backordersEnabled())
                                                        <span class="stock-backorder">
                                    <i class="bi bi-clock me-1"></i>Available for backorder
                                </span>
                                                    @else
                                                        <span class="stock-out">
                                    <i class="bi bi-x-circle me-1"></i>Out of stock
                                </span>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($packOption->id !== $product->id)
                                                <div class="pack-option-overlay">
                                                    <div class="overlay-content">
                                                        <button class="btn btn-sm btn-outline-primary switch-pack-btn">
                                                            <i class="fas fa-exchange-alt me-1"></i>Switch to this size
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                            </div>

                            <div class="pack-comparison mt-3">
                                <button class="btn btn-link p-0 text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#packComparisonTable" aria-expanded="false">
                                    <i class="fas fa-chart-bar me-1"></i>Compare pack sizes
                                    <i class="fas fa-chevron-down ms-1"></i>
                                </button>

                                <div class="collapse mt-2" id="packComparisonTable">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                            <tr>
                                                <th>Pack Size</th>
                                                <th>Total Price</th>
                                                <th>Price per Unit</th>
                                                <th>Savings per Unit</th>
                                                <th>Total Savings</th>
                                                <th>Stock</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($packSizeFamily as $packOption)
                                                @if($packOption->pricing && $packOption->pricing['show_prices'])
                                                    <tr class="{{ $packOption->id === $product->id ? 'table-primary' : '' }}">
                                                        <td>
                                                            <strong>{{ $packOption->pack_size ?? 1 }} {{ ($packOption->pack_size ?? 1) == 1 ? 'unit' : 'units' }}</strong>
                                                            @if($packOption->id === $product->id)
                                                                <span class="badge bg-primary ms-1">Current</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ config('app.currency', 'R') }}{{ number_format($packOption->pricing['price'], 2) }}</td>
                                                        <td>{{ config('app.currency', 'R') }}{{ number_format($packOption->unit_price ?? 0, 2) }}</td>
                                                        <td>
                                                            @if(isset($packOption->savings_per_unit) && $packOption->savings_per_unit > 0)
                                                                <span class="text-success">
                                                                    {{ config('app.currency', 'R') }}{{ number_format($packOption->savings_per_unit, 2) }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(isset($packOption->savings_per_unit) && $packOption->savings_per_unit > 0)
                                                                <span class="text-success">
                                                                    {{ config('app.currency', 'R') }}{{ number_format($packOption->savings_per_unit * ($packOption->pack_size ?? 1), 2) }}
                                                                    <small>({{ $packOption->savings_percentage }}%)</small>
                                                                </span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($packOption->stockHolding && $packOption->stockHolding->QuantityOnHand > 0)
                                                                <span class="badge bg-success">{{ $packOption->stockHolding->QuantityOnHand }}</span>
                                                            @else
                                                                <span class="badge bg-danger">0</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($priceData['has_promotion'])
                        <div class="promotion-calculator mb-4 p-3 bg-light border rounded">
                            <h6 class="mb-2">
                                <i class="fas fa-calculator me-1"></i>
                                Promotion Calculator
                            </h6>
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label for="promo-quantity" class="form-label small">Test Quantity:</label>
                                    <input type="number" class="form-control form-control-sm" id="promo-quantity"
                                           value="1" min="1" max="999" onchange="calculatePromotion()">
                                </div>
                                <div class="col-md-8">
                                    <div id="promo-result" class="small text-success">
                                        <!-- Results will be populated here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <hr>

                    <!-- Add to Cart Section -->
                    @auth
                        @if(($product->stockHolding && $product->stockHolding->QuantityOnHand > 0) || \App\Helpers\Features::backordersEnabled())
                            <div class="mb-4">
                                <form id="add-to-cart-form" onsubmit="return false;">
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
                                        @if($priceData['has_promotion'])
                                            <div class="col-md-8">
                                                <div id="quantity-promo-preview" class="small text-success">
                                                    <!-- Quantity-based promotion preview -->
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-amazon-secondary btn-lg add-to-cart-btn" data-product-id="{{ $product->id }}">
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
                        @if($priceData['has_promotion'])
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="promotion-tab" data-bs-toggle="tab" data-bs-target="#promotion-details" type="button" role="tab">
                                    <i class="fas fa-tag me-1"></i>Promotion Details
                                </button>
                            </li>
                        @endif
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

                        <!-- Promotion Details Tab -->
                        @if($priceData['has_promotion'])
                            <div class="tab-pane fade" id="promotion-details" role="tabpanel">
                                <h5 class="mb-3">
                                    <i class="fas fa-tag text-success me-2"></i>
                                    Active Promotion Details
                                </h5>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="promotion-info">
                                            <h6>{{ $priceData['promotion']->name }}</h6>
                                            @if($priceData['promotion']->description)
                                                <p class="text-muted">{{ $priceData['promotion']->description }}</p>
                                            @endif

                                            <div class="promotion-type-info mb-3 p-3 bg-light border rounded">
                                                @if($priceData['promotion']->type === 'bogo')
                                                    <h6 class="text-success"><i class="fas fa-gift me-1"></i> Buy One Get One Free</h6>
                                                    <p>Buy {{ $priceData['promotion']->buy_quantity ?: 1 }} item(s) and get {{ $priceData['promotion']->get_quantity ?: 1 }} absolutely free!</p>
                                                @elseif($priceData['promotion']->type === 'quantity_break')
                                                    <h6 class="text-info"><i class="fas fa-boxes me-1"></i> Quantity Break Discount</h6>
                                                    <p>Purchase {{ $priceData['promotion']->min_quantity }} or more items and save
                                                        @if($priceData['promotion']->discount_percentage)
                                                            {{ $priceData['promotion']->discount_percentage }}% on each item
                                                        @elseif($priceData['promotion']->discount_amount)
                                                            ${{ number_format($priceData['promotion']->discount_amount / 100, 2) }} on each item
                                                        @endif
                                                    </p>
                                                @elseif($priceData['promotion']->type === 'price_break')
                                                    <h6 class="text-primary"><i class="fas fa-layer-group me-1"></i> Volume Pricing</h6>
                                                    @if($priceData['promotion']->price_breaks)
                                                        <div class="table-responsive">
                                                            <table class="table table-sm">
                                                                <thead>
                                                                <tr>
                                                                    <th>Quantity</th>
                                                                    <th>Price Each</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @foreach($priceData['promotion']->price_breaks as $break)
                                                                    <tr>
                                                                        <td>{{ $break['qty'] }}+</td>
                                                                        <td>${{ number_format($break['price'] / 100, 2) }}</td>
                                                                    </tr>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @endif
                                                @else
                                                    <h6 class="text-warning"><i class="fas fa-star me-1"></i> Special Offer</h6>
                                                    <p>{{ ucwords(str_replace('_', ' ', $priceData['promotion']->type)) }} promotion active</p>
                                                @endif
                                            </div>

                                            <div class="promotion-validity">
                                                <h6 class="mb-2">Promotion Period</h6>
                                                <p class="mb-1">
                                                    <i class="fas fa-calendar-start text-success me-1"></i>
                                                    <strong>Started:</strong> {{ $priceData['promotion']->starts_at->format('M j, Y g:i A') }}
                                                </p>
                                                <p class="mb-1">
                                                    <i class="fas fa-calendar-times text-danger me-1"></i>
                                                    <strong>Ends:</strong> {{ $priceData['promotion']->ends_at->format('M j, Y g:i A') }}
                                                </p>
                                                <p class="text-muted small">
                                                    Time remaining: {{ $priceData['promotion']->ends_at->diffForHumans() }}
                                                </p>
                                            </div>

                                            @if($priceData['promotion']->usage_limit_total)
                                                <div class="promotion-limits mt-3">
                                                    <h6>Usage Limits</h6>
                                                    @php
                                                        $usagePercent = ($priceData['promotion']->usage_count / $priceData['promotion']->usage_limit_total) * 100;
                                                    @endphp
                                                    <div class="progress mb-2">
                                                        <div class="progress-bar bg-{{ $usagePercent > 80 ? 'danger' : 'success' }}"
                                                             style="width: {{ min($usagePercent, 100) }}%">
                                                            {{ number_format($usagePercent, 1) }}%
                                                        </div>
                                                    </div>
                                                    <p class="small text-muted">
                                                        {{ $priceData['promotion']->usage_count }} / {{ $priceData['promotion']->usage_limit_total }} uses
                                                        ({{ $priceData['promotion']->usage_limit_total - $priceData['promotion']->usage_count }} remaining)
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="promotion-summary card">
                                            <div class="card-body">
                                                <h6 class="card-title">Your Savings</h6>
                                                <div class="text-center">
                                                    <div class="display-6 text-success">{{ $priceData['formatted']['savings'] }}</div>
                                                    <p class="text-muted">per item</p>
                                                </div>

                                                @if($priceData['promotion']->is_online_only)
                                                    <div class="alert alert-info alert-sm">
                                                        <i class="fas fa-globe me-1"></i>
                                                        <strong>Online Exclusive!</strong> This promotion is only available on our website.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

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
                                                        data-product-id="{{ $relatedProduct->id }}">
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

@push('scripts')
    <script>
        $(document).ready(function() {
            // Track product view
            trackProductView({{ $product->id }});

            const currentPackSize = {{ $product->pack_size ?? 1 }};

            // Promotion related
            @if($priceData['has_promotion'])
            // Initialize promotion calculator
            calculatePromotion();
            updateQuantityPromotion();

            // Real-time promotion calculation
            window.calculatePromotion = function() {
                const quantity = parseInt(document.getElementById('promo-quantity').value) || 1;

                fetch('{{ route("admin.promotions.test-calculation") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        promotion_id: {{ $priceData['promotion']->id }},
                        quantity: quantity,
                        customer_tier: {{ $customerTier }}
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        const resultDiv = document.getElementById('promo-result');
                        if (data.success && data.calculation.applicable) {
                            const calc = data.calculation;
                            let message = `${quantity} items: `;
                            message += `${(calc.discounted_price / 100).toFixed(2)} each `;
                            message += `(Save ${(calc.total_savings / 100).toFixed(2)} total)`;

                            if (calc.bonus_quantity > 0) {
                                message += ` + ${calc.bonus_quantity} bonus items!`;
                            }

                            resultDiv.innerHTML = `<i class="fas fa-check-circle me-1"></i>${message}`;
                        } else {
                            resultDiv.innerHTML = `<i class="fas fa-info-circle me-1"></i>Quantity ${quantity}: No additional discount`;
                        }
                    })
                    .catch(error => {
                        document.getElementById('promo-result').innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Unable to calculate';
                    });
            };

            window.updateQuantityPromotion = function() {
                const quantity = parseInt(document.getElementById('product-quantity').value) || 1;
                const previewDiv = document.getElementById('quantity-promo-preview');

                if (!previewDiv) return;

                // Update the promo calculator quantity to match
                const promoQtyInput = document.getElementById('promo-quantity');
                if (promoQtyInput) {
                    promoQtyInput.value = quantity;
                    calculatePromotion();
                }

                // Show promotion preview for selected quantity
                @if($priceData['promotion']->type === 'bogo')
                const buyQty = {{ $priceData['promotion']->buy_quantity ?: 1 }};
                const getQty = {{ $priceData['promotion']->get_quantity ?: 1 }};
                const sets = Math.floor(quantity / buyQty);
                const bonusItems = sets * getQty;

                if (bonusItems > 0) {
                    previewDiv.innerHTML = `<i class="fas fa-gift me-1"></i>You'll get ${bonusItems} bonus item${bonusItems > 1 ? 's' : ''} free!`;
                } else {
                    previewDiv.innerHTML = `<i class="fas fa-info-circle me-1"></i>Buy ${buyQty} to get ${getQty} free`;
                }
                @elseif($priceData['promotion']->type === 'quantity_break')
                const minQty = {{ $priceData['promotion']->min_quantity }};
                if (quantity >= minQty) {
                    previewDiv.innerHTML = `<i class="fas fa-check-circle me-1"></i>Quantity discount applies!`;
                } else {
                    previewDiv.innerHTML = `<i class="fas fa-info-circle me-1"></i>Buy ${minQty}+ for discount`;
                }
                @endif
            };
            @endif

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

                // Load product data via AJAX
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

            const currentPackSize = {{ $product->pack_size ?? 1 }};

            // Update quantity selector to show total units
            function updateTotalUnitsDisplay() {
                const quantity = parseInt($('#product-quantity').val()) || 1;
                const totalUnits = quantity * currentPackSize;

                let unitsText = '';
                if (currentPackSize > 1) {
                    unitsText = `<div class="total-units-display">
                <i class="fas fa-calculator me-1"></i>
                Total units: ${totalUnits} (${quantity} × ${currentPackSize})
            </div>`;
                }

                $('.quantity-info').remove(); // Remove existing
                $('#product-quantity').parent().append('<div class="quantity-info">' + unitsText + '</div>');
            }

            // Handle pack size switching
            $('.pack-size-option').on('click', function(e) {
                if ($(this).hasClass('selected')) return;

                e.preventDefault();
                const productUrl = $(this).data('product-url');

                if (!productUrl) {
                    console.error('No product URL found');
                    return;
                }

                const switchBtn = $(this).find('.switch-pack-btn');

                // Show loading state
                if (switchBtn.length) {
                    switchBtn.html('<i class="fas fa-spinner fa-spin me-1"></i>Switching...');
                }

                // Redirect to the selected pack size product
                window.location.href = productUrl;
            });

            // Handle quantity changes
            $('#product-quantity').on('change', function() {
                updateTotalUnitsDisplay();

                @if(isset($priceData) && $priceData['has_promotion'])
                updateQuantityPromotion();
                @endif
            });

            // Initialize
            updateTotalUnitsDisplay();

            // Make functions globally accessible
            window.showCartToast = showCartToast;
            window.trackProductView = trackProductView;

            $(document).on('click', '.add-to-cart-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                console.log('Add to cart button clicked');

                const productId = $(this).data('product-id');
                const button = $(this);
                const originalText = button.html();

                console.log('Product ID:', productId);

                if (!productId) {
                    console.error('No product ID found');
                    alert('Error: No product ID found');
                    return;
                }

                // Show loading state
                button.html('<i class="bi bi-hourglass-split me-1"></i>Adding...');
                button.prop('disabled', true);

                console.log('Making AJAX request to:', '{{ route("shop.cart.add") }}');

                $.ajax({
                    url: '{{ route("shop.cart.add") }}',
                    type: 'POST',
                    data: {
                        product_id: productId,
                        quantity: 1,
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        console.log('AJAX request starting....')
                    },
                    success: function(response) {
                        console.log('AJAX success:', response);

                        if (response.success) {
                            // Update cart badge
                            $('.cart-badge').text(response.cart_count);

                            // Show success state
                            button.html('<i class="bi bi-check-circle me-1"></i>Added!');
                            button.removeClass('btn-amazon-primary').addClass('btn-success');

                            // Show toast notification
                            showCartToast('Product added to cart successfully', 'success');

                            // Reset button after 2 seconds
                            setTimeout(function() {
                                button.html(originalText);
                                button.removeClass('btn-success').addClass('btn-amazon-primary');
                                button.prop('disabled', false);
                            }, 2000);
                        } else {
                            console.error('Server returned success=false:', response);
                            alert('Error: ' + (response.message || 'Unknown error'));
                            button.html(originalText);
                            button.prop('disabled', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            error: error
                        });

                        let errorMessage = 'Error adding product to cart';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        alert(errorMessage);

                        // Reset button
                        button.html(originalText);
                        button.prop('disabled', false);
                    },
                    complete: function() {
                        console.log('AJAX request completed');
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            const currentPackSize = {{ $product->pack_size ?? 1 }};

            // Update quantity selector to show total units
            function updateTotalUnitsDisplay() {
                const quantity = parseInt($('#product-quantity').val()) || 1;
                const totalUnits = quantity * currentPackSize;

                let unitsText = '';
                if (currentPackSize > 1) {
                    unitsText = `<div class="total-units-display">
                <i class="fas fa-calculator me-1"></i>
                Total units: ${totalUnits} (${quantity} × ${currentPackSize})
            </div>`;
                }

                $('.quantity-info').remove(); // Remove existing
                $('#product-quantity').parent().append('<div class="quantity-info">' + unitsText + '</div>');
            }

            // Handle pack size switching
            $('.pack-size-option').on('click', function(e) {
                if ($(this).hasClass('selected')) return;

                e.preventDefault();
                const productId = $(this).data('product-id');
                const switchBtn = $(this).find('.switch-pack-btn');

                if (!productId) {
                    console.error('No product ID found');
                    return;
                }

                // Show loading state
                switchBtn.html('<i class="fas fa-spinner fa-spin me-1"></i>Switching...');

                // Redirect to the selected pack size product
                window.location.href = "{{ route('shop.products.switch-pack', ['productId' => '__PRODUCT_ID__']) }}".replace('__PRODUCT_ID__', productId);
            });

            // Handle quantity changes
            $('#product-quantity').on('change', function() {
                updateTotalUnitsDisplay();

                @if($priceData['has_promotion'])
                updateQuantityPromotion();
                @endif
            });

            // Initialize
            updateTotalUnitsDisplay();

            // Update the add to cart functionality to be pack-size aware
            $('.add-to-cart-btn').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const productId = $(this).data('product-id');
                const quantity = parseInt($('#product-quantity').val()) || 1;
                const button = $(this);
                const originalText = button.html();

                if (!productId) {
                    alert('Error: No product ID found');
                    return;
                }

                // Show loading state
                button.html('<i class="bi bi-hourglass-split me-1"></i>Adding...');
                button.prop('disabled', true);

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
                            button.html('<i class="bi bi-check-circle me-1"></i>Added!');
                            button.removeClass('btn-amazon-primary').addClass('btn-success');

                            // Calculate total units added
                            const totalUnits = quantity * currentPackSize;
                            let message = `Added ${quantity} pack${quantity > 1 ? 's' : ''}`;
                            if (currentPackSize > 1) {
                                message += ` (${totalUnits} total units)`;
                            }
                            message += ' to cart successfully';

                            // Show toast notification
                            showCartToast(message, 'success');

                            // Reset button after 2 seconds
                            setTimeout(function() {
                                button.html(originalText);
                                button.removeClass('btn-success').addClass('btn-amazon-primary');
                                button.prop('disabled', false);
                            }, 2000);
                        } else {
                            alert('Error: ' + (response.message || 'Unknown error'));
                            button.html(originalText);
                            button.prop('disabled', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = 'Error adding product to cart';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        alert(errorMessage);

                        // Reset button
                        button.html(originalText);
                        button.prop('disabled', false);
                    }
                });
            });
        });
    </script>


@endpush
