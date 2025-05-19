
<div class="mini-cart-wrapper">
    <div class="mini-cart-header d-flex justify-content-between align-items-center p-3 border-bottom">
        <h6 class="mb-0">Your Cart ({{ $cartCount }})</h6>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="mini-cart-body p-3">
        @if(count($cart) > 0)
            <div class="mini-cart-items">
                @foreach($cart as $item)
                    <div class="mini-cart-item d-flex mb-3 pb-3 border-bottom">
                        <div class="mini-cart-item-image me-3">
                            <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" style="width: 60px; height: 60px; object-fit: contain;">
                        </div>
                        <div class="mini-cart-item-details flex-grow-1">
                            <h6 class="mb-1">{{ $item['name'] }}</h6>
                            <div class="d-flex justify-content-between">
                            <span class="text-muted">{{ $item['quantity'] }} ×
                                @if(\App\Helpers\Features::showPrices())
                                    ${{ number_format($item['price'], 2) }}
                                @else
                                    -
                                @endif
                            </span>
                                <button class="btn btn-sm text-danger p-0 remove-from-cart" data-product-id="{{ $item['product_id'] }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if(\App\Helpers\Features::showPrices())
                <div class="mini-cart-subtotal d-flex justify-content-between mt-3 pt-3 border-top">
                    <strong>Subtotal:</strong>
                    <strong>${{ number_format($cartTotal, 2) }}</strong>
                </div>
            @endif

            <div class="mini-cart-actions mt-3 d-grid gap-2">
                <a href="{{ route('cart.show') }}" class="btn btn-outline-primary">View Cart</a>
                <a href="{{ route('checkout') }}" class="btn btn-primary">Checkout</a>
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-cart-x mb-3" style="font-size: 2.5rem;"></i>
                <p>Your cart is empty</p>
                <a href="{{ route('shop.products') }}" class="btn btn-primary btn-sm">Shop Now</a>
            </div>
        @endif
    </div>
</div>
