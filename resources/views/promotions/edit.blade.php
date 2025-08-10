@extends('layouts.master')

@section('title', 'Edit Promotion')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">{{ __('Edit Promotion') }}</h1>
                <p class="mb-0 text-muted">Update promotion details and configuration</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('promotions.show', $promotion) }}" class="btn btn-secondary">
                    <i class="fas fa-eye me-1"></i> {{ __('View Details') }}
                </a>
                <a href="{{ route('promotions.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('Back to List') }}
                </a>
            </div>
        </div>

        <form action="{{ route('promotions.update', $promotion) }}" method="POST" id="promotionForm">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-lg-8">

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">{{ __('Basic Information') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="name" class="form-label">{{ __('Promotion Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $promotion->name) }}" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label">{{ __('Description') }}</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="3">{{ old('description', $promotion->description) }}</textarea>
                                    @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="type" class="form-label">{{ __('Promotion Type') }} <span class="text-danger">*</span></label>
                                    <select class="form-control @error('type') is-invalid @enderror"
                                            id="type" name="type" required onchange="togglePromotionFields()">
                                        <option value="">Select Type</option>
                                        <option value="date_range" {{ old('type', $promotion->type) === 'date_range' ? 'selected' : '' }}>Date Range Promotion</option>
                                        <option value="bogo" {{ old('type', $promotion->type) === 'bogo' ? 'selected' : '' }}>Buy One Get One (BOGO)</option>
                                        <option value="quantity_break" {{ old('type', $promotion->type) === 'quantity_break' ? 'selected' : '' }}>Quantity Break Discount</option>
                                        <option value="bonus_quantity" {{ old('type', $promotion->type) === 'bonus_quantity' ? 'selected' : '' }}>Bonus Quantity</option>
                                        <option value="price_break" {{ old('type', $promotion->type) === 'price_break' ? 'selected' : '' }}>Price Break Tiers</option>
                                        <option value="online_only" {{ old('type', $promotion->type) === 'online_only' ? 'selected' : '' }}>Online Only</option>
                                    </select>
                                    @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">{{ __('Status') }} <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror"
                                            id="promotion_status" name="status" required>
                                        <option value="active" {{ old('status', $promotion->status) === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $promotion->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="scheduled" {{ old('status', $promotion->status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                        <option value="expired" {{ old('status', $promotion->status) === 'expired' ? 'selected' : '' }}>Expired</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Product & Timing</h6>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="stock_code" class="form-label">Product <span class="text-danger">*</span></label>
                                    <select class="form-control @error('stock_code') is-invalid @enderror"
                                            id="stock_code" name="stock_code" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->StockCode }}"
                                                {{ old('stock_code', $promotion->stock_code) === $product->StockCode ? 'selected' : '' }}>
                                                {{ $product->StockCode }} - {{ Str::limit($product->StockItemName, 50) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('stock_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="is_online_only"
                                               name="is_online_only" value="1"
                                            {{ old('is_online_only', $promotion->is_online_only) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_online_only">
                                            {{ __('Online Only Promotion') }}
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="starts_at" class="form-label">Start Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('starts_at') is-invalid @enderror"
                                           id="starts_at" name="starts_at"
                                           value="{{ old('starts_at', $promotion->starts_at ? $promotion->starts_at->format('Y-m-d\TH:i') : '') }}" required>
                                    @error('starts_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="ends_at" class="form-label">End Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('ends_at') is-invalid @enderror"
                                           id="ends_at" name="ends_at"
                                           value="{{ old('ends_at', $promotion->ends_at ? $promotion->ends_at->format('Y-m-d\TH:i') : '') }}" required>
                                    @error('ends_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Customer Targeting</h6>
                        </div>
                        <div class="card-body">
                            <label class="form-label">Applicable Customer Tiers</label>
                            <div class="row">
                                @for($i = 1; $i <= 4; $i++)
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   id="tier_{{ $i }}" name="customer_tiers[]" value="{{ $i }}"
                                                {{ in_array($i, old('customer_tiers', $promotion->customer_tiers ?? [1,2,3,4])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tier_{{ $i }}">
                                                Customer Tier {{ $i }}
                                            </label>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            <small class="text-muted">Select which customer pricing tiers this promotion applies to</small>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Promotion Configuration</h6>
                        </div>
                        <div class="card-body">
                            <div id="date_range_fields" class="promotion-type-fields" style="display: none;">
                                <h6 class="text-secondary mb-3">Special Pricing</h6>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="sale_price_1" class="form-label">Tier 1 Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R</span>
                                            <input type="number" class="form-control" id="sale_price_1" name="sale_price_1"
                                                   step="0.01" min="0" value="{{ old('sale_price_1', $promotion->sale_price_1 ? $promotion->sale_price_1 / 100 : '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="sale_price_2" class="form-label">Tier 2 Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R</span>
                                            <input type="number" class="form-control" id="sale_price_2" name="sale_price_2"
                                                   step="0.01" min="0" value="{{ old('sale_price_2', $promotion->sale_price_2 ? $promotion->sale_price_2 / 100 : '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="sale_price_3" class="form-label">Tier 3 Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R</span>
                                            <input type="number" class="form-control" id="sale_price_3" name="sale_price_3"
                                                   step="0.01" min="0" value="{{ old('sale_price_3', $promotion->sale_price_3 ? $promotion->sale_price_3 / 100 : '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="sale_price_4" class="form-label">Tier 4 Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R</span>
                                            <input type="number" class="form-control" id="sale_price_4" name="sale_price_4"
                                                   step="0.01" min="0" value="{{ old('sale_price_4', $promotion->sale_price_4 ? $promotion->sale_price_4 / 100 : '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="bogo_fields" class="promotion-type-fields" style="display: none;">
                                <h6 class="text-secondary mb-3">Buy One Get One Configuration</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="buy_quantity" class="form-label">Buy Quantity</label>
                                        <input type="number" class="form-control" id="buy_quantity" name="buy_quantity"
                                               min="1" value="{{ old('buy_quantity', $promotion->buy_quantity ?? 1) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="get_quantity" class="form-label">Get Free Quantity</label>
                                        <input type="number" class="form-control" id="get_quantity" name="get_quantity"
                                               min="1" value="{{ old('get_quantity', $promotion->get_quantity ?? 1) }}">
                                    </div>
                                </div>
                            </div>

                            <div id="quantity_break_fields" class="promotion-type-fields" style="display: none;">
                                <h6 class="text-secondary mb-3">Quantity Break Discount</h6>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="min_quantity" class="form-label">Minimum Quantity</label>
                                        <input type="number" class="form-control" id="min_quantity" name="min_quantity"
                                               min="1" value="{{ old('min_quantity', $promotion->min_quantity ?? 1) }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="discount_percentage" class="form-label">Discount Percentage</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="discount_percentage"
                                                   name="discount_percentage" step="0.01" min="0" max="100"
                                                   value="{{ old('discount_percentage', $promotion->discount_percentage) }}">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="discount_amount" class="form-label">OR Fixed Discount</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R</span>
                                            <input type="number" class="form-control" id="discount_amount"
                                                   name="discount_amount" step="0.01" min="0"
                                                   value="{{ old('discount_amount', $promotion->discount_amount ? $promotion->discount_amount / 100 : '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="price_break_fields" class="promotion-type-fields" style="display: none;">
                                <h6 class="text-secondary mb-3">Price Break Tiers</h6>
                                <div id="price_breaks_container">
                                    @if($promotion->price_breaks)
                                        @foreach($promotion->price_breaks as $index => $break)
                                            <div class="row price-break-row mb-2">
                                                <div class="col-md-4">
                                                    <input type="number" class="form-control"
                                                           name="price_breaks[{{ $index }}][qty]"
                                                           placeholder="Quantity" value="{{ $break['qty'] }}" min="1">
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <span class="input-group-text">R</span>
                                                        <input type="number" class="form-control"
                                                               name="price_breaks[{{ $index }}][price]"
                                                               placeholder="Price" value="{{ $break['price'] / 100 }}" step="0.01" min="0">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removePriceBreak(this)">
                                                        Remove
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addPriceBreak()">
                                    <i class="fas fa-plus me-1"></i> Add Price Break
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Usage Limits</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="quantity_limit_per_customer" class="form-label">Limit Per Customer</label>
                                    <input type="number" class="form-control" id="quantity_limit_per_customer"
                                           name="quantity_limit_per_customer" min="1"
                                           value="{{ old('quantity_limit_per_customer', $promotion->quantity_limit_per_customer) }}">
                                    <small class="text-muted">Maximum items per customer (leave empty for no limit)</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="usage_limit_total" class="form-label">Total Usage Limit</label>
                                    <input type="number" class="form-control" id="usage_limit_total"
                                           name="usage_limit_total" min="1"
                                           value="{{ old('usage_limit_total', $promotion->usage_limit_total) }}">
                                    <small class="text-muted">Maximum total uses across all customers</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">

                    @if($promotion->product)
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Current Product</h6>
                            </div>
                            <div class="card-body">
                                <h6>{{ $promotion->product->ProductName }}</h6>
                                <p class="text-muted mb-2">Stock Code: {{ $promotion->stock_code }}</p>

                                <div class="row text-center">
                                    <div class="col-6">
                                        <small class="text-muted">Tier 1</small><br>
                                        <strong>R{{ number_format($promotion->product->SellingPrice, 2) }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Tier 2</small><br>
                                        <strong>R{{ number_format($promotion->product->SellingPrice2, 2) }}</strong>
                                    </div>
                                </div>
                                <div class="row text-center mt-2">
                                    <div class="col-6">
                                        <small class="text-muted">Tier 3</small><br>
                                        <strong>R{{ number_format($promotion->product->SellingPrice3 , 2) }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Tier 4</small><br>
                                        <strong>R{{ number_format($promotion->product->SellingPrice4, 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Update Promotion
                                </button>

                                <button type="button" class="btn btn-outline-secondary" onclick="previewPromotion()">
                                    <i class="fas fa-eye me-1"></i> Preview Changes
                                </button>

                                <a href="{{ route('promotions.show', $promotion) }}" class="btn btn-outline-info">
                                    <i class="fas fa-chart-bar me-1"></i> View Analytics
                                </a>

                                <hr>

                                <a href="{{ route('promotions.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Help</h6>
                        </div>
                        <div class="card-body">
                            <small>
                                <strong>Promotion Types:</strong><br>
                                • <strong>Date Range:</strong> Special pricing during period<br>
                                • <strong>BOGO:</strong> Buy X get Y free<br>
                                • <strong>Quantity Break:</strong> Discount for bulk purchase<br>
                                • <strong>Price Break:</strong> Tiered pricing by quantity<br>
                                • <strong>Online Only:</strong> Web exclusive deals
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Promotion Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="previewContent">
                    <!-- Preview content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('promotionForm').submit()">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function togglePromotionFields() {
        const type = document.getElementById('type').value;
        const allFields = document.querySelectorAll('.promotion-type-fields');

        allFields.forEach(field => field.style.display = 'none');

        if (type) {
            const targetField = document.getElementById(type + '_fields');
            if (targetField) {
                targetField.style.display = 'block';
            }
        }
    }

    let priceBreakIndex = {{ $promotion->price_breaks ? count($promotion->price_breaks) : 0 }};

    function addPriceBreak() {
        const container = document.getElementById('price_breaks_container');
        const html = `
            <div class="row price-break-row mb-2">
                <div class="col-md-4">
                    <input type="number" class="form-control"
                           name="price_breaks[${priceBreakIndex}][qty]"
                           placeholder="Quantity" min="1">
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control"
                               name="price_breaks[${priceBreakIndex}][price]"
                               placeholder="Price" step="0.01" min="0">
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removePriceBreak(this)">
                        Remove
                    </button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        priceBreakIndex++;
    }

    function removePriceBreak(button) {
        button.closest('.price-break-row').remove();
    }

    function previewPromotion() {
        const formData = new FormData(document.getElementById('promotionForm'));
        const previewData = {};

        for (let [key, value] of formData.entries()) {
            previewData[key] = value;
        }

        // Build preview HTML
        let html = `
        <div class="row">
            <div class="col-md-6">
                <h6>Basic Information</h6>
                <p><strong>Name:</strong> ${previewData.name || 'Untitled Promotion'}</p>
                <p><strong>Type:</strong> ${previewData.type ? previewData.type.replace('_', ' ').toUpperCase() : 'Not set'}</p>
                <p><strong>Status:</strong> <span class="badge bg-primary">${previewData.status || 'active'}</span></p>
                <p><strong>Duration:</strong> ${previewData.starts_at || 'Not set'} to ${previewData.ends_at || 'Not set'}</p>
            </div>
            <div class="col-md-6">
                <h6>Configuration</h6>
    `;

        if (previewData.type === 'date_range') {
            html += '<p><strong>Special Pricing:</strong></p><ul>';
            for (let i = 1; i <= 4; i++) {
                const price = previewData[`sale_price_${i}`];
                if (price) {
                    html += `<li>Tier ${i}: ${price}</li>`;
                }
            }
            html += '</ul>';
        } else if (previewData.type === 'bogo') {
            html += `<p><strong>BOGO:</strong> Buy ${previewData.buy_quantity || 1}, Get ${previewData.get_quantity || 1} Free</p>`;
        } else if (previewData.type === 'quantity_break') {
            html += `<p><strong>Quantity Break:</strong> ${previewData.min_quantity || 1}+ items`;
            if (previewData.discount_percentage) {
                html += ` get ${previewData.discount_percentage}% off`;
            } else if (previewData.discount_amount) {
                html += ` get ${previewData.discount_amount} off each`;
            }
            html += '</p>';
        }

        html += `
            </div>
        </div>
        <hr>
        <p class="text-muted"><small>This is a preview of how your promotion will appear. Click "Save Changes" to apply these updates.</small></p>
    `;

        document.getElementById('previewContent').innerHTML = html;
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        togglePromotionFields();

        // Add change listener to type field
        document.getElementById('type').addEventListener('change', togglePromotionFields);

        // Form validation
        document.getElementById('promotionForm').addEventListener('submit', function(e) {
            const startDate = new Date(document.getElementById('starts_at').value);
            const endDate = new Date(document.getElementById('ends_at').value);

            if (endDate <= startDate) {
                e.preventDefault();
                alert('End date must be after start date');
                return false;
            }

            // Additional validation can be added here
            return true;
        });
    });
</script>
@endpush
