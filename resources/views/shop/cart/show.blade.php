@extends('shop.layouts.app')

@section('content')
    <div class="container my-5">
        <h1 class="mb-4">Shopping Cart</h1>

        @if(count($cart) > 0)
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-cart">
                                    <thead>
                                    <tr>
                                        <th style="width: 100px;">Product</th>
                                        <th>Description</th>
                                        @if(\App\Helpers\Features::showPrices())
                                            <th class="text-end">Price</th>
                                        @endif
                                        <th style="width: 150px;">Quantity</th>
                                        @if(\App\Helpers\Features::showPrices())
                                            <th class="text-end">Total</th>
                                        @endif
                                        <th style="width: 50px;"></th>
                                    </tr>
                                    </thead>
                                    <tbody id="cart-items">
                                    @include('shop.cart.partials.cart-items')
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ url()->previous() }}" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-left me-2"></i>Continue Shopping
                                </a>
                                <button class="btn btn-danger clear-cart-btn">
                                    <i class="bi bi-trash me-2"></i>Clear Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            @if(\App\Helpers\Features::showPrices())
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal</span>
                                    <span>${{ number_format($cartTotal, 2) }}</span>
                                </div>

                                @php
                                    $tax = $cartTotal * 0.1; // Assuming 10% tax - adjust as needed
                                    $orderTotal = $cartTotal + $tax;
                                @endphp

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax (10%)</span>
                                    <span>${{ number_format($tax, 2) }}</span>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between mb-4">
                                    <strong>Order Total</strong>
                                    <strong>${{ number_format($orderTotal, 2) }}</strong>
                                </div>
                            @else
                                <p class="text-center mb-4">Please log in to see pricing information.</p>
                            @endif

                            <a href="{{ route('checkout') }}" class="btn btn-primary btn-lg w-100">
                                Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-cart-x" style="font-size: 5rem;"></i>
                </div>
                <h2>Your cart is empty</h2>
                <p class="text-muted">Looks like you haven't added any products to your cart yet.</p>
                <a href="{{ route('shop.products.index') }}" class="btn btn-primary mt-3">
                    Start Shopping
                </a>
            </div>
        @endif
    </div>

    <!-- Toast Notification for Cart Actions -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="cartToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="bi bi-cart-check me-2"></i>
                <strong class="me-auto">Cart Update</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Item has been added to your cart.
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Update quantity
            $(document).on('change', '.cart-quantity', function() {
                const productId = $(this).data('product-id');
                const quantity = $(this).val();

                $.ajax({
                    url: '{{ route('shop.cart.update') }}',
                    type: 'POST',
                    data: {
                        product_id: productId,
                        quantity: quantity,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#cart-items').html(response.cart_html);
                            updateCartSummary(response.cart_count, response.cart_total);
                            showToast('Cart updated successfully');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        showToast(response.message, 'danger');
                        // Reset to previous quantity
                        location.reload();
                    }
                });
            });

            // Remove item from cart
            $(document).on('click', '.remove-from-cart', function() {
                const productId = $(this).data('product-id');

                $.ajax({
                    url: '{{ route('shop.cart.remove') }}',
                    type: 'POST',
                    data: {
                        product_id: productId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.cart_count > 0) {
                                $('#cart-items').html(response.cart_html);
                                updateCartSummary(response.cart_count, response.cart_total);
                            } else {
                                // Reload if cart is empty
                                location.reload();
                            }
                            showToast('Item removed from cart');
                        }
                    }
                });
            });

            // Clear cart
            $(document).on('click', '.clear-cart-btn', function() {
                if (confirm('Are you sure you want to clear your cart?')) {
                    $.ajax({
                        url: '{{ route('shop.cart.clear') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            }
                        }
                    });
                }
            });

            // Helper function to update cart summary
            function updateCartSummary(count, total) {
                // Update the cart badge in navbar
                $('.cart-badge').text(count);

                // Update totals
                const tax = total * 0.1; // Assuming 10% tax
                const orderTotal = total + tax;

                $('.cart-subtotal').text('$' + total.toFixed(2));
                $('.cart-tax').text('$' + tax.toFixed(2));
                $('.cart-total').text('$' + orderTotal.toFixed(2));
            }

            // Show toast notification
            function showToast(message, type = 'success') {
                $('#cartToast .toast-body').text(message);

                if (type === 'danger') {
                    $('#cartToast .toast-header').addClass('bg-danger text-white');
                } else {
                    $('#cartToast .toast-header').removeClass('bg-danger text-white');
                }

                const toast = new bootstrap.Toast(document.getElementById('cartToast'));
                toast.show();
            }
        });
    </script>
@endsection
