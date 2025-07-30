@extends('shop.layouts.app')

@section('title', 'Checkout')

@section('content')

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="bi bi-credit-card me-2"></i>Checkout
            </h1>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('shop.checkout.process') }}" id="checkout-form">
        @csrf

        <div class="row">
            <!-- Left Column - Order Details -->
            <div class="col-lg-8">

                <!-- Customer Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-person me-2"></i>Customer Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Customer:</strong> {{ $customer->CustomerName }}</p>
                                <p><strong>Account Code:</strong> {{ $customer->acc_code }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Email:</strong> {{ $customer->GeneralEmailAddress }}</p>
                                <p><strong>Price Level:</strong>
                                    <span class="badge bg-primary">{{ \App\Helpers\PricingHelper::getPriceTierName($customer->price_level) }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delivery Address -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-truck me-2"></i>Delivery Address
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="delivery_address_line1" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('delivery_address_line1') is-invalid @enderror"
                                           id="delivery_address_line1" name="delivery_address_line1"
                                           value="{{ old('delivery_address_line1', $customer->DeliveryAddressLine1) }}" required>
                                    @error('delivery_address_line1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="delivery_address_line2" class="form-label">Address Line 2</label>
                                    <input type="text" class="form-control @error('delivery_address_line2') is-invalid @enderror"
                                           id="delivery_address_line2" name="delivery_address_line2"
                                           value="{{ old('delivery_address_line2', $customer->DeliveryAddressLine2) }}">
                                    @error('delivery_address_line2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="delivery_city" class="form-label">City <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('delivery_city') is-invalid @enderror"
                                           id="delivery_city" name="delivery_city"
                                           value="{{ old('delivery_city', $customer->DeliveryCity) }}" required>
                                    @error('delivery_city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="delivery_postal_code" class="form-label">Postal Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('delivery_postal_code') is-invalid @enderror"
                                           id="delivery_postal_code" name="delivery_postal_code"
                                           value="{{ old('delivery_postal_code', $customer->DeliveryPostalCode) }}" required>
                                    @error('delivery_postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="update_delivery_address" name="update_delivery_address" value="1">
                            <label class="form-check-label" for="update_delivery_address">
                                Update my default delivery address with these details
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-clipboard-data me-2"></i>Order Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="customer_po_number" class="form-label">Purchase Order Number</label>
                            <input type="text" class="form-control @error('customer_po_number') is-invalid @enderror"
                                   id="customer_po_number" name="customer_po_number"
                                   value="{{ old('customer_po_number') }}"
                                   placeholder="Your PO number (optional)">
                            @error('customer_po_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Order Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3"
                                      placeholder="Any special instructions or comments...">{{ old('notes') }}</textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Terms & Conditions -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input @error('terms_accepted') is-invalid @enderror"
                                   type="checkbox" id="terms_accepted" name="terms_accepted" value="1" required>
                            <label class="form-check-label" for="terms_accepted">
                                I agree to the <a href="#" target="_blank">Terms and Conditions</a> and <a href="#" target="_blank">Privacy Policy</a> <span class="text-danger">*</span>
                            </label>
                            @error('terms_accepted')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Order Summary -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-receipt me-2"></i>Order Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Cart Items -->
                        <div class="mb-3">
                            <h6 class="border-bottom pb-2">Items ({{ count($cartItems) }})</h6>
                            @foreach($cartItems as $item)
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1 me-2">
                                        <div class="fw-bold small">{{ Str::limit($item['product']->StockItemName, 30) }}</div>
                                        <div class="text-muted small">
                                            {{ \App\Helpers\PricingHelper::formatPrice($item['pricing']['price']) }} × {{ $item['quantity'] }}
                                            @if($item['pricing']['discount_percentage'] > 0)
                                                <span class="text-success">({{ $item['pricing']['discount_percentage'] }}% off)</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <strong class="small">{{ \App\Helpers\PricingHelper::formatPrice($item['line_total']) }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <hr>

                        <!-- Pricing Breakdown -->
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>{{ \App\Helpers\PricingHelper::formatPrice($subtotal) }}</span>
                        </div>

                        @if(\App\Helpers\PricingHelper::hasWholesalePricing())
                            @php
                                $retailTotal = 0;
                                foreach($cartItems as $item) {
                                    $retailTotal += $item['product']->SellingPrice * $item['quantity'];
                                }
                                $totalSavings = $retailTotal - $subtotal;
                            @endphp
                            @if($totalSavings > 0)
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>{{ \App\Helpers\PricingHelper::getPriceTierName() }} Savings:</span>
                                    <span>-{{ \App\Helpers\PricingHelper::formatPrice($totalSavings) }}</span>
                                </div>
                            @endif
                        @endif

                        <div class="d-flex justify-content-between mb-3">
                            <span>VAT ({{ number_format($vatRate * 100, 0) }}%):</span>
                            <span>{{ \App\Helpers\PricingHelper::formatPrice($vatAmount) }}</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-4">
                            <strong class="fs-5">Total:</strong>
                            <strong class="fs-5">{{ \App\Helpers\PricingHelper::formatPrice($total) }}</strong>
                        </div>

                        <!-- Place Order Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="place-order-btn">
                                <i class="bi bi-check-circle me-2"></i>Place Order
                            </button>
                            <a href="{{ route('shop.cart.show') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back to Cart
                            </a>
                        </div>

                        <!-- Security Notice -->
                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                <i class="bi bi-shield-check me-1"></i>
                                Your order information is secure and encrypted
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Form submission
        $('#checkout-form').on('submit', function(e) {
            const $submitBtn = $('#place-order-btn');
            const originalText = $submitBtn.html();

            // Show loading state
            $submitBtn.prop('disabled', true);
            $submitBtn.html('<i class="bi bi-hourglass-split me-2"></i>Processing Order...');
        });

        const addressFields = ['delivery_address_line1', 'delivery_address_line2', 'delivery_city', 'delivery_postal_code'];
        const originalValues = {};

        addressFields.forEach(field => {
            originalValues[field] = $('#' + field).val();
        });

        // Check for changes and suggest updating default address
        addressFields.forEach(field => {
            $('#' + field).on('change', function() {
                let hasChanges = false;
                addressFields.forEach(checkField => {
                    if ($('#' + checkField).val() !== originalValues[checkField]) {
                        hasChanges = true;
                    }
                });

                if (hasChanges && !$('#update_delivery_address').prop('checked')) {
                    $('#update_delivery_address').closest('.form-check').addClass('bg-light p-2 rounded');
                }
            });
        });

        // Terms and conditions validation
        $('#terms_accepted').on('change', function() {
            const $submitBtn = $('#place-order-btn');
            if ($(this).prop('checked')) {
                $submitBtn.prop('disabled', false);
            } else {
                $submitBtn.prop('disabled', true);
            }
        });

        const $submitBtn = $('#place-order-btn');
        if (!$('#terms_accepted').prop('checked')) {
            $submitBtn.prop('disabled', true);
        }
    });
</script>
@endpush
