
<div class="d-md-none" id="mobile-cart-items">
@foreach($cart as $item)
    @php
        $product = \App\Models\Product::find($item['product_id']);
        $pricing = \App\Helpers\PricingHelper::getProductPricing($product);
        $itemTotal = $pricing['price'] * $item['quantity'];
    @endphp
    <div class="card mb-3" data-product-id="{{ $product->id }}">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-3">
                    <img src="{{ $product->photo ? $product->photo->thumbnail : 'https://dummyimage.com/80x80/cccccc/000000.png&text=No+Image' }}"
                         alt="{{ $product->StockItemName }}"
                         class="img-fluid rounded">
                </div>
                <div class="col-9">
                    <h6 class="mb-1">{{ $product->StockItemName }}</h6>
                    <small class="text-muted">SKU: {{ $product->StockCode }}</small>
                    @auth
                        <div class="mt-2">
                            <strong>{{ \App\Helpers\PricingHelper::formatPrice($pricing['price']) }}</strong>
                            @if($pricing['discount_percentage'] > 0)
                                <small class="text-success ms-2">Save {{ $pricing['discount_percentage'] }}%</small>
                            @endif
                        </div>
                    @endauth
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div class="input-group" style="width: 120px;">
                            <button class="btn btn-outline-secondary btn-sm qty-decrease" type="button" data-product-id="{{ $product->id }}">
                                <i class="bi bi-dash"></i>
                            </button>
                            <input type="number" class="form-control form-control-sm text-center cart-quantity-input"
                                   value="{{ $item['quantity'] }}"
                                   min="1"
                                   max="999"
                                   data-product-id="{{ $product->id }}"
                                   data-original-value="{{ $item['quantity'] }}">
                            <button class="btn btn-outline-secondary btn-sm qty-increase" type="button" data-product-id="{{ $product->id }}">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                        @auth
                            <strong class="item-total">{{ \App\Helpers\PricingHelper::formatPrice($itemTotal) }}</strong>
                        @endauth
                        <button class="btn btn-sm btn-outline-danger remove-from-cart" data-product-id="{{ $product->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
