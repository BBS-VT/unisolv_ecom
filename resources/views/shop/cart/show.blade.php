@extends('shop.layouts.app')

@section('content')
    {{--@php
        dd($cart);
    @endphp--}}
    <div class="container my-5">
        <h1 class="mb-4">Shopping Cart</h1>

        @if(count($cart) > 0)
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-cart">
                                    <thead>
                                    <tr>
                                        <th style="width: 100px;">Product</th>
                                        <th>Description</th>
                                        @auth()
                                            <th class="text-end">Price</th>
                                            <th style="width: 150px;">Quantity</th>
                                            <th class="text-end">Total</th>
                                        @else
                                            <th style="width: 150px;">Quantity</th>
                                        @endauth
                                        <th style="width: 50px;"></th>
                                    </tr>
                                    </thead>
                                    <tbody id="cart-items">
                                        @include('shop.cart.partials.cart-items')
                                    </tbody>
                                </table>
                            </div>

                            @include('shop.cart.partials.mobile')

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('shop.products.index') }}" class="btn btn-outline-primary">
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
                            @auth
                                @php
                                    $subtotal = 0;
                                    foreach($cart as $item) {
                                        $product = \App\Models\Product::find($item['product_id']);

                                        if (!$product) {
                                            continue;
                                        }
                                        $pricing = \App\Helpers\PricingHelper::getProductPricing($product);
                                        $subtotal += $pricing['price'] * $item['quantity'];
                                    }
                                    $taxRate = 0.15; // TODO: link to TAX model
                                    $tax = $subtotal * $taxRate;
                                    $orderTotal = $subtotal + $tax;
                                @endphp

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal ({{ count($cart) }} {{ Str::plural('item', count($cart)) }}</span>
                                    <span class="cart-subtotal">{{ \App\Helpers\PricingHelper::formatPrice($subtotal) }}</span>
                                </div>

                                @if(\App\Helpers\PricingHelper::hasWholesalePricing())
                                    @php
                                        $retailTotal = 0;
                                        foreach($cart as $item) {
                                            $retailTotal += $item['product']->SellingPrice * $item['quantity'];
                                        }
                                        $totalSavings = $retailTotal - $subtotal;
                                    @endphp
                                    @if($totalSavings > 0)
                                        <div class="d-flex justify-content-between mb-2 text-success">
                                            <span>{{ \App\Helpers\PricingHelper::getPriceTierName() }} Savings</span>
                                            <span>-{{ \App\Helpers\PricingHelper::formatPrice($totalSavings) }}</span>
                                        </div>
                                    @endif
                                @endif

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax ({{ number_format($taxRate * 100, 1) }}%)</span>
                                    <span class="cart-tax">{{ \App\Helpers\PricingHelper::formatPrice($tax) }}</span>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between mb-4">
                                    <strong>Order Total</strong>
                                    <strong class="cart-total">{{ \App\Helpers\PricingHelper::formatPrice($orderTotal) }}</strong>
                                </div>

                                <a href="{{ route('shop.checkout.index') }}" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-credit-card me-2"></i>Proceed to Checkout
                                </a>
                            @else
                                <div class="text-center mb-4">
                                    <i class="bi bi-info-circle text-primary mb-3" style="font-size: 2rem;"></i>
                                    <h6>Login Required</h6>
                                    <p class="text-muted mb-3">Please log in to see pricing and proceed to checkout.</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#shopLoginModal">
                                        <i class="bi bi-person me-2"></i>Login Now
                                    </button>
                                </div>
                            @endauth
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="bi bi-truck me-2"></i>{{ __('Delivery Information') }}
                            </h6>
                            <ul class="list-unstyled small mb-0">
                                <li class="mb-1"><i class="bi bi-clock text-info me-1"></i> Standard delivery: 2-3 business days</li>
                                <li><i class="bi bi-shield-check text-primary me-1"></i> Secure payment processing</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-cart-x text-muted" style="font-size: 5rem;"></i>
                </div>
                <h2>Your cart is empty</h2>
                <p class="text-muted">Looks like you haven't added any products to your cart yet.</p>
                <a href="{{ route('shop.products.index') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-bag me-2"></i> {{ __('Start Shopping') }}
                </a>
            </div>
        @endif
    </div>

    <!-- Toast Notification for Cart Actions -->
    {{--<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
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
    </div>--}}
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Update quantity
            $(document).on('change', '.cart-quantity', function() {
                const productId = $(this).data('product-id');
                const quantity = $(this).val();
                const $row = $(`[data-product-id="${productId}"]`);

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
                            $('.cart-badge').text(response.cart_count);

                            showToast('Cart updated successfully');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        showToast(response.message || 'Error updating cart', 'danger');
                        // Reset to previous quantity
                        location.reload();
                    }
                });
            });

            // Remove item from cart
            $(document).on('click', '.remove-from-cart', function() {
                const productId = $(this).data('product-id');

                if (confirm('Remove this item from your cart?')) {
                    $.ajax({
                        url: '{{ route('shop.cart.remove') }}',
                        type: 'POST',
                        data: {
                            product_id: productId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                showToast('Item removed from cart');

                                setTimout(() => {
                                    location.reload();
                                }, 1000);
                            }
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            showToast(response.message || 'Error removing item', 'danger');
                        }
                    });
                }

            });

            // Clear cart
            $(document).on('click', '.clear-cart-btn', function() {
                if (confirm('Are you sure you want to clear your entire cart?')) {
                    $.ajax({
                        url: '{{ route('shop.cart.clear') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                showToast('Cart cleared');

                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            }
                        },
                        error: function(xhr) {
                            showToast('Error cleared cart', 'danger');
                        }
                    });
                }
            });

            // Show toast notification
            function showToast(message, type = 'success') {
                const toastHtml = `
                    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header ${type === 'danger' ? 'bg-danger text-white' : 'bg-success text-white'}">
                            <i class="bi bi-cart-check me-2"></i>
                            <strong class="me-auto">Cart Update</strong>
                            <button type="button" class="btn-close ${type === 'danger' ? 'btn-close-white' : 'btn-close-white'}" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body">${message}</div>
                    </div>
                `;

                // Create container if it doesn't exist
                let container = $('.toast-container');
                if (container.length === 0) {
                    container = $('<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;"></div>');
                    $('body').append(container);
                }

                const $toast = $(toastHtml);
                container.append($toast);

                const toast = new bootstrap.Toast($toast[0]);
                toast.show();

                // Remove toast element after it's hidden
                $toast[0].addEventListener('hidden.bs.toast', () => {
                    $toast.remove();
                });
            }
        });
    </script>
@endpush
