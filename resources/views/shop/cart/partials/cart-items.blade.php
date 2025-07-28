@foreach($cart as $item)
    @php
        $product = \App\Models\Product::find($item['product_id']);
        $pricing = $pricing = \App\Helpers\PricingHelper::getProductPricing($product);
        $itemTotal = $item['price'] * $item['quantity'];
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
            <select class="form-select cart-quantity" data-product-id="{{ $product->id }}">
                @for($i = 1; $i <= 20; $i++)
                    <option value="{{ $i }}" {{ $i == $item['quantity'] ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </td>
        {{--<td>
            <div class="input-group">
                <button class="btn btn-outline-secondary btn-sm" type="button"
                        onclick="document.getElementById('qty-{{ $item['product_id'] }}').stepDown(); $(document.getElementById('qty-{{ $item['product_id'] }}')).change();">
                    <i class="bi bi-dash"></i>
                </button>
                <input type="number" class="form-control text-center cart-quantity" id="qty-{{ $item['product_id'] }}"
                       value="{{ $item['quantity'] }}" min="1" data-product-id="{{ $item['product_id'] }}">
                <button class="btn btn-outline-secondary btn-sm" type="button"
                        onclick="document.getElementById('qty-{{ $item['product_id'] }}').stepUp(); $(document.getElementById('qty-{{ $item['product_id'] }}')).change();">
                    <i class="bi bi-plus"></i>
                </button>
            </div>
        </td>--}}
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
