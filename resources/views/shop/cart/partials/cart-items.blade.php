@foreach($cart as $item)
    @php
        $itemTotal = $item['price'] * $item['quantity'];
    @endphp
    <tr>
        <td>
            <img src="{{ 'https://dummyimage.com/300x300/cccccc/000000.png&text=No+Image' }}" class="amazon-product-image">

            {{--            <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" class="img-fluid" style="max-height: 80px;">--}}
        </td>
        <td>
            <h5>{{ $item['name'] }}</h5>
            <p class="text-muted small mb-0">SKU: {{ $item['product_id'] }}</p>
        </td>
        @if(\App\Helpers\Features::publicPricesEnabled())
            <td class="text-end">${{ number_format($item['price'], 2) }}</td>
        @endif
        <td>
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
        </td>
        @if(\App\Helpers\Features::publicPricesEnabled())
            <td class="text-end">${{ number_format($itemTotal, 2) }}</td>
        @endif
        <td>
            <button class="btn btn-sm text-danger remove-from-cart" data-product-id="{{ $item['product_id'] }}">
                <i class="bi bi-x-circle"></i>
            </button>
        </td>
    </tr>
@endforeach
