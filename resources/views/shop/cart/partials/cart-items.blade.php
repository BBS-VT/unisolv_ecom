@foreach($cart as $item)
    @php
        $product = \App\Models\Product::find($item['product_id']);
        $pricing = $pricing = \App\Helpers\PricingHelper::getProductPricing($product);
        $itemTotal = $pricing['price'] * $item['quantity'];
    @endphp
    <tr data-product-id="{{ $product->id }}">
        <td>
            <img src="{{ $product->photo ? $product->photo->thumbnail : 'https://dummyimage.com/80x80/cccccc/000000.png&text=No+Image' }}"
                 alt="{{ $product->StockItemName }}"
                 class="img-fluid rounded"
                 style="width: 80px; height: 80px; object-fit: cover;">
        </td>
        {{-- <td>
            <img src="{{ 'https://dummyimage.com/300x300/cccccc/000000.png&text=No+Image' }}" class="amazon-product-image">
        </td>--}}
        <td>
            <h6 class="mb-1">{{ $product->StockItemName }}</h6>
            <small class="text-muted">SKU: {{ $product->StockCode }}</small>
            @if(\App\Helpers\PricingHelper::hasWholesalePricing())
                <br><span class="badge bg-primary">{{ \App\Helpers\PricingHelper::getPriceTierName() }} Price</span>
            @endif
        </td>
        @auth
            <td class="text-end">
                <strong>{{ \App\Helpers\PricingHelper::formatPrice($pricing['price']) }}</strong>
                @if($pricing['discount_percentage'] > 0)
                    <br><small class="text-muted"><s>{{ \App\Helpers\PricingHelper::formatPrice($pricing['base_price']) }}</s></small>
                    <br><small class="text-success">Save {{ $pricing['discount_percentage'] }}%</small>
                @endif
            </td>
        @endauth
        <td>
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
        </td>
        @auth()
            <td class="text-end">
                <strong class="item-total">{{ \App\Helpers\PricingHelper::formatPrice($itemTotal) }}</strong>
            </td>
        @endauth
        <td>
            <button class="btn btn-sm btn-outline-danger remove-from-cart" data-product-id="{{ $product->id }}">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
@endforeach
