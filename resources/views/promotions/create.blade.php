@extends('layouts.master')

@section('title', 'Create New Promotion')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Create New Promotion</h1>
                <p class="mb-0 text-muted">Set up a new sales promotion or discount campaign</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('promotions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Promotions
                </a>
                <a href="{{ route('promotions.import') }}" class="btn btn-outline-primary">
                    <i class="fas fa-upload me-1"></i> Import from CSV
                </a>
            </div>
        </div>

        <form action="{{ route('promotions.store') }}" method="POST" id="promotionForm">
            @csrf

            <div class="row">
                <!-- Main Form -->
                <div class="col-lg-8">
                    <!-- Quick Start Templates -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-success">
                                <i class="fas fa-rocket me-1"></i> Quick Start Templates
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Choose a template to get started quickly, then customize as needed:</p>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <button type="button" class="btn btn-outline-primary w-100" onclick="applyTemplate('weekend_sale')">
                                        <i class="fas fa-calendar-weekend mb-2"></i><br>
                                        <strong>Weekend Sale</strong><br>
                                        <small>10% off this weekend</small>
                                    </button>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <button type="button" class="btn btn-outline-success w-100" onclick="applyTemplate('buy_one_get_one')">
                                        <i class="fas fa-gift mb-2"></i><br>
                                        <strong>Buy 1 Get 1</strong><br>
                                        <small>Classic BOGO offer</small>
                                    </button>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <button type="button" class="btn btn-outline-info w-100" onclick="applyTemplate('bulk_discount')">
                                        <i class="fas fa-boxes mb-2"></i><br>
                                        <strong>Bulk Discount</strong><br>
                                        <small>Save on larger quantities</small>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Basic Information -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="name" class="form-label">Promotion Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}" required
                                           placeholder="e.g., Summer Sale 2025 - 20% Off Electronics">
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Choose a descriptive name that clearly identifies this promotion</small>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="3"
                                              placeholder="Describe what this promotion offers and any special terms...">{{ old('description') }}</textarea>
                                    @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="type" class="form-label">Promotion Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('type') is-invalid @enderror"
                                            id="type" name="type" required onchange="togglePromotionFields()">
                                        <option value="">Select Promotion Type</option>
                                        <option value="date_range" {{ old('type') === 'date_range' ? 'selected' : '' }}>📅 Date Range Promotion</option>
                                        <option value="bogo" {{ old('type') === 'bogo' ? 'selected' : '' }}>🎁 Buy One Get One (BOGO)</option>
                                        <option value="quantity_break" {{ old('type') === 'quantity_break' ? 'selected' : '' }}>📦 Quantity Break Discount</option>
                                        <option value="bonus_quantity" {{ old('type') === 'bonus_quantity' ? 'selected' : '' }}>🔥 Bonus Quantity</option>
                                        <option value="price_break" {{ old('type') === 'price_break' ? 'selected' : '' }}>💰 Price Break Tiers</option>
                                        <option value="online_only" {{ old('type') === 'online_only' ? 'selected' : '' }}>🌐 Online Only</option>
                                    </select>
                                    @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror"
                                            id="promotion_status" name="status" required>
                                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>✅ Active</option>
                                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>⏸️ Inactive</option>
                                        <option value="scheduled" {{ old('status') === 'scheduled' ? 'selected' : '' }}>⏰ Scheduled</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product & Timing -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Product & Timing</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="stock_code" class="form-label">Product <span class="text-danger">*</span></label>
                                    <select class="form-control @error('stock_code') is-invalid @enderror"
                                            id="stock_code" name="stock_code" required onchange="loadProductInfo()">
                                        <option value="">Search and select a product...</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->StockCode }}"
                                                    data-price1="{{ $product->SellingPrice }}"
                                                    data-price2="{{ $product->SellingPrice2 }}"
                                                    data-price3="{{ $product->SellingPrice3 }}"
                                                    data-price4="{{ $product->SellingPrice4 }}"
                                                {{ old('stock_code') === $product->StockCode ? 'selected' : '' }}>
                                                {{ $product->StockCode }} - {{ Str::limit($product->StockItemName, 60) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('stock_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="is_online_only"
                                               name="is_online_only" value="1"
                                            {{ old('is_online_only') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_online_only">
                                            <strong>🌐 Online Only</strong><br>
                                            <small class="text-muted">Available only on website</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="starts_at" class="form-label">Start Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('starts_at') is-invalid @enderror"
                                           id="starts_at" name="starts_at"
                                           value="{{ old('starts_at', now()->format('Y-m-d\TH:i')) }}" required>
                                    @error('starts_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="ends_at" class="form-label">End Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('ends_at') is-invalid @enderror"
                                           id="ends_at" name="ends_at"
                                           value="{{ old('ends_at', now()->addDays(7)->format('Y-m-d\TH:i')) }}" required>
                                    @error('ends_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Quick Duration Buttons -->
                            <div class="mb-3">
                                <label class="form-label">Quick Duration:</label><br>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-secondary" onclick="setDuration(1)">1 Day</button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="setDuration(3)">3 Days</button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="setDuration(7)">1 Week</button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="setDuration(14)">2 Weeks</button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="setDuration(30)">1 Month</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Targeting -->
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
                                                {{ in_array($i, old('customer_tiers', [1,2,3,4])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tier_{{ $i }}">
                                                <strong>Tier {{ $i }}</strong><br>
                                                <small class="text-muted" id="tier_{{ $i }}_price">Select product first</small>
                                            </label>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            <small class="text-muted">Select which customer pricing tiers this promotion applies to. Prices shown are current regular prices.</small>
                        </div>
                    </div>

                    <!-- Promotion Configuration -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Promotion Configuration</h6>
                        </div>
                        <div class="card-body">
                            <!-- Type-specific fields will be shown/hidden based on selection -->

                            <!-- Date Range Promotion Fields -->
                            <div id="date_range_fields" class="promotion-type-fields" style="display: none;">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Date Range Promotion:</strong> Set special prices for each customer tier during the promotion period.
                                </div>
                                <h6 class="text-secondary mb-3">Special Pricing</h6>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="sale_price_1" class="form-label">Tier 1 Special Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R</span>
                                            <input type="number" class="form-control" id="sale_price_1" name="sale_price_1"
                                                   step="0.01" min="0" value="{{ old('sale_price_1') }}"
                                                   placeholder="0.00">
                                        </div>
                                        <small class="text-muted">Regular: <span id="regular_price_1">-</span></small>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="sale_price_2" class="form-label">Tier 2 Special Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R</span>
                                            <input type="number" class="form-control" id="sale_price_2" name="sale_price_2"
                                                   step="0.01" min="0" value="{{ old('sale_price_2') }}"
                                                   placeholder="0.00">
                                        </div>
                                        <small class="text-muted">Regular: <span id="regular_price_2">-</span></small>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="sale_price_3" class="form-label">Tier 3 Special Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R</span>
                                            <input type="number" class="form-control" id="sale_price_3" name="sale_price_3"
                                                   step="0.01" min="0" value="{{ old('sale_price_3') }}"
                                                   placeholder="0.00">
                                        </div>
                                        <small class="text-muted">Regular: <span id="regular_price_3">-</span></small>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="sale_price_4" class="form-label">Tier 4 Special Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R</span>
                                            <input type="number" class="form-control" id="sale_price_4" name="sale_price_4"
                                                   step="0.01" min="0" value="{{ old('sale_price_4') }}"
                                                   placeholder="0.00">
                                        </div>
                                        <small class="text-muted">Regular: <span id="regular_price_4">-</span></small>
                                    </div>
                                </div>
                            </div>

                            <!-- BOGO Fields -->
                            <div id="bogo_fields" class="promotion-type-fields" style="display: none;">
                                <div class="alert alert-success">
                                    <i class="fas fa-gift me-1"></i>
                                    <strong>Buy One Get One:</strong> Customer buys a certain quantity and gets additional items free.
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="buy_quantity" class="form-label">Buy Quantity</label>
                                        <input type="number" class="form-control" id="buy_quantity" name="buy_quantity"
                                               min="1" value="{{ old('buy_quantity', 1) }}">
                                        <small class="text-muted">Number of items customer must buy</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="get_quantity" class="form-label">Get Free Quantity</label>
                                        <input type="number" class="form-control" id="get_quantity" name="get_quantity"
                                               min="1" value="{{ old('get_quantity', 1) }}">
                                        <small class="text-muted">Number of items customer gets free</small>
                                    </div>
                                </div>
                                <div class="alert alert-light">
                                    <strong>Example:</strong> Buy <span id="bogo_preview_buy">1</span>, Get <span id="bogo_preview_get">1</span> Free
                                </div>
                            </div>

                            <!-- Quantity Break Fields -->
                            <div id="quantity_break_fields" class="promotion-type-fields" style="display: none;">
                                <div class="alert alert-warning">
                                    <i class="fas fa-boxes me-1"></i>
                                    <strong>Quantity Break:</strong> Customers get a discount when they buy a minimum quantity.
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="min_quantity" class="form-label">Minimum Quantity</label>
                                        <input type="number" class="form-control" id="min_quantity" name="min_quantity"
                                               min="1" value="{{ old('min_quantity', 2) }}">
                                        <small class="text-muted">Minimum items to qualify</small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="discount_percentage" class="form-label">Discount Percentage</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="discount_percentage"
                                                   name="discount_percentage" step="0.01" min="0" max="100"
                                                   value="{{ old('discount_percentage') }}"
                                                   onchange="clearDiscountAmount()">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <small class="text-muted">OR use fixed amount below</small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="discount_amount" class="form-label">Fixed Discount</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R</span>
                                            <input type="number" class="form-control" id="discount_amount"
                                                   name="discount_amount" step="0.01" min="0"
                                                   value="{{ old('discount_amount') }}"
                                                   onchange="clearDiscountPercentage()">
                                        </div>
                                        <small class="text-muted">Fixed amount off per item</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Price Break Fields -->
                            <div id="price_break_fields" class="promotion-type-fields" style="display: none;">
                                <div class="alert alert-primary">
                                    <i class="fas fa-layer-group me-1"></i>
                                    <strong>Price Break Tiers:</strong> Different prices for different quantity levels.
                                </div>
                                <div id="price_breaks_container">
                                    <!-- Price breaks will be added dynamically -->
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addPriceBreak()">
                                    <i class="fas fa-plus me-1"></i> Add Price Break Tier
                                </button>
                            </div>

                            <!-- Online Only Fields -->
                            <div id="online_only_fields" class="promotion-type-fields" style="display: none;">
                                <div class="alert alert-info">
                                    <i class="fas fa-globe me-1"></i>
                                    <strong>Online Only:</strong> This promotion will only be available on your website.
                                </div>
                                <p>Configure the discount details above using Date Range pricing or other promotion types.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Usage Limits -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Usage Limits (Optional)</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="quantity_limit_per_customer" class="form-label">Limit Per Customer</label>
                                    <input type="number" class="form-control" id="quantity_limit_per_customer"
                                           name="quantity_limit_per_customer" min="1"
                                           value="{{ old('quantity_limit_per_customer') }}"
                                           placeholder="No limit">
                                    <small class="text-muted">Maximum items one customer can purchase with this promotion</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="usage_limit_total" class="form-label">Total Usage Limit</label>
                                    <input type="number" class="form-control" id="usage_limit_total"
                                           name="usage_limit_total" min="1"
                                           value="{{ old('usage_limit_total') }}"
                                           placeholder="No limit">
                                    <small class="text-muted">Maximum total uses across all customers</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Selected Product Info -->
                    <div class="card shadow mb-4" id="product_info_card" style="display: none;">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Selected Product</h6>
                        </div>
                        <div class="card-body" id="product_info_content">
                            <!-- Product info will be loaded here -->
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-plus me-1"></i> Create Promotion
                                </button>

                                <button type="button" class="btn btn-outline-primary" onclick="previewPromotion()">
                                    <i class="fas fa-eye me-1"></i> Preview Promotion
                                </button>

                                <button type="button" class="btn btn-outline-secondary" onclick="saveAsDraft()">
                                    <i class="fas fa-save me-1"></i> Save as Draft
                                </button>

                                <hr>

                                <a href="{{ route('promotions.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Promotion Types Help -->
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Promotion Types Guide</h6>
                        </div>
                        <div class="card-body">
                            <div class="accordion" id="helpAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#help1">
                                            📅 Date Range
                                        </button>
                                    </h2>
                                    <div id="help1" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                                        <div class="accordion-body small">
                                            Set special prices for different customer tiers during a specific time period. Perfect for sales events.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#help2">
                                            🎁 BOGO
                                        </button>
                                    </h2>
                                    <div id="help2" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                                        <div class="accordion-body small">
                                            Buy One Get One offers. Customer purchases X items and receives Y items free.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#help3">
                                            📦 Quantity Break
                                        </button>
                                    </h2>
                                    <div id="help3" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                                        <div class="accordion-body small">
                                            Discount when customers buy a minimum quantity. Great for encouraging bulk purchases.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-eye me-1"></i> Promotion Preview
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="previewContent">
                    <!-- Preview content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close Preview</button>
                    <button type="button" class="btn btn-success" onclick="document.getElementById('promotionForm').submit()">
                        <i class="fas fa-plus me-1"></i> Create This Promotion
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Price break counter
        let priceBreakIndex = 0;

        // Quick Templates
        function applyTemplate(templateType) {
            const now = new Date();
            const tomorrow = new Date(now);
            tomorrow.setDate(tomorrow.getDate() + 1);

            switch(templateType) {
                case 'weekend_sale':
                    document.getElementById('name').value = 'Weekend Sale - ' + now.getFullYear();
                    document.getElementById('description').value = 'Special weekend pricing on selected items';
                    document.getElementById('type').value = 'date_range';
                    document.getElementById('discount_percentage').value = '10';
                    // Set to this weekend
                    const friday = new Date(now);
                    friday.setDate(friday.getDate() + (5 - friday.getDay()));
                    const sunday = new Date(friday);
                    sunday.setDate(sunday.getDate() + 2);
                    document.getElementById('starts_at').value = friday.toISOString().slice(0,16);
                    document.getElementById('ends_at').value = sunday.toISOString().slice(0,16);
                    break;

                case 'buy_one_get_one':
                    document.getElementById('name').value = 'Buy 1 Get 1 Free';
                    document.getElementById('description').value = 'Purchase one item and receive another absolutely free';
                    document.getElementById('type').value = 'bogo';
                    document.getElementById('buy_quantity').value = '1';
                    document.getElementById('get_quantity').value = '1';
                    break;

                case 'bulk_discount':
                    document.getElementById('name').value = 'Bulk Purchase Discount';
                    document.getElementById('description').value = 'Save more when you buy more - perfect for bulk orders';
                    document.getElementById('type').value = 'quantity_break';
                    document.getElementById('min_quantity').value = '5';
                    document.getElementById('discount_percentage').value = '15';
                    break;
            }

            togglePromotionFields();
            updateBogoPreview();
        }

        // Show/hide fields based on promotion type
        function togglePromotionFields() {
            const type = document.getElementById('type').value;
            const allFields = document.querySelectorAll('.promotion-type-fields');

            // Hide all fields first
            allFields.forEach(field => field.style.display = 'none');

            // Show relevant fields
            if (type) {
                const targetField = document.getElementById(type + '_fields');
                if (targetField) {
                    targetField.style.display = 'block';
                }
            }
        }

        // Load product information when product is selected
        function loadProductInfo() {
            const select = document.getElementById('stock_code');
            const selectedOption = select.options[select.selectedIndex];
            const productCard = document.getElementById('product_info_card');
            const productContent = document.getElementById('product_info_content');

            if (selectedOption.value) {
                const stockCode = selectedOption.value;
                const productName = selectedOption.text.split(' - ')[1];
                const price1 = selectedOption.dataset.price1;
                const price2 = selectedOption.dataset.price2;
                const price3 = selectedOption.dataset.price3;
                const price4 = selectedOption.dataset.price4;

                // Update product info card
                productContent.innerHTML = `
            <h6>${productName}</h6>
            <p class="text-muted mb-2">Stock Code: ${stockCode}</p>

            <div class="row text-center">
                <div class="col-6">
                    <small class="text-muted">Tier 1</small><br>
                    <strong>${(price1 / 100).toFixed(2)}</strong>
                </div>
                <div class="col-6">
                    <small class="text-muted">Tier 2</small><br>
                    <strong>${(price2 / 100).toFixed(2)}</strong>
                </div>
            </div>
            <div class="row text-center mt-2">
                <div class="col-6">
                    <small class="text-muted">Tier 3</small><br>
                    <strong>${(price3 / 100).toFixed(2)}</strong>
                </div>
                <div class="col-6">
                    <small class="text-muted">Tier 4</small><br>
                    <strong>${(price4 / 100).toFixed(2)}</strong>
                </div>
            </div>
        `;

                // Update tier price displays
                document.getElementById('tier_1_price').textContent = `${(price1 / 100).toFixed(2)}`;
                document.getElementById('tier_2_price').textContent = `${(price2 / 100).toFixed(2)}`;
                document.getElementById('tier_3_price').textContent = `${(price3 / 100).toFixed(2)}`;
                document.getElementById('tier_4_price').textContent = `${(price4 / 100).toFixed(2)}`;

                // Update regular price displays in date range fields
                document.getElementById('regular_price_1').textContent = `${(price1 / 100).toFixed(2)}`;
                document.getElementById('regular_price_2').textContent = `${(price2 / 100).toFixed(2)}`;
                document.getElementById('regular_price_3').textContent = `${(price3 / 100).toFixed(2)}`;
                document.getElementById('regular_price_4').textContent = `${(price4 / 100).toFixed(2)}`;

                productCard.style.display = 'block';
            } else {
                productCard.style.display = 'none';
            }
        }

        // Quick duration setters
        function setDuration(days) {
            const now = new Date();
            const start = new Date(now);
            const end = new Date(now);
            end.setDate(end.getDate() + days);

            document.getElementById('starts_at').value = start.toISOString().slice(0,16);
            document.getElementById('ends_at').value = end.toISOString().slice(0,16);
        }

        // Price break management
        function addPriceBreak() {
            const container = document.getElementById('price_breaks_container');
            const html = `
        <div class="row price-break-row mb-2">
            <div class="col-md-4">
                <input type="number" class="form-control"
                       name="price_breaks[${priceBreakIndex}][qty]"
                       placeholder="Quantity (e.g., 10)" min="1">
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
                    <i class="fas fa-trash me-1"></i> Remove
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

        // Clear discount fields to prevent conflicts
        function clearDiscountAmount() {
            if (document.getElementById('discount_percentage').value) {
                document.getElementById('discount_amount').value = '';
            }
        }

        function clearDiscountPercentage() {
            if (document.getElementById('discount_amount').value) {
                document.getElementById('discount_percentage').value = '';
            }
        }

        // Update BOGO preview
        function updateBogoPreview() {
            const buyQty = document.getElementById('buy_quantity')?.value || 1;
            const getQty = document.getElementById('get_quantity')?.value || 1;

            const buySpan = document.getElementById('bogo_preview_buy');
            const getSpan = document.getElementById('bogo_preview_get');

            if (buySpan) buySpan.textContent = buyQty;
            if (getSpan) getSpan.textContent = getQty;
        }

        // Preview functionality
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
                <h6>📋 Basic Information</h6>
                <p><strong>Name:</strong> ${previewData.name || 'Untitled Promotion'}</p>
                <p><strong>Type:</strong> ${previewData.type ? previewData.type.replace('_', ' ').toUpperCase() : 'Not selected'}</p>
                <p><strong>Status:</strong> <span class="badge bg-primary">${previewData.status || 'active'}</span></p>
                ${previewData.is_online_only ? '<p><span class="badge bg-info">🌐 Online Only</span></p>' : ''}
            </div>
            <div class="col-md-6">
                <h6>⏰ Duration</h6>
                <p><strong>Starts:</strong> ${previewData.starts_at ? new Date(previewData.starts_at).toLocaleString() : 'Not set'}</p>
                <p><strong>Ends:</strong> ${previewData.ends_at ? new Date(previewData.ends_at).toLocaleString() : 'Not set'}</p>
                <p><strong>Product:</strong> ${previewData.stock_code || 'Not selected'}</p>
            </div>
        </div>
        <hr>
        <h6>🎯 Promotion Details</h6>
    `;

            // Add type-specific preview
            if (previewData.type === 'date_range') {
                html += '<div class="alert alert-info"><strong>Special Pricing:</strong><br>';
                for (let i = 1; i <= 4; i++) {
                    const price = previewData[`sale_price_${i}`];
                    if (price) {
                        html += `Tier ${i}: ${price}<br>`;
                    }
                }
                html += '</div>';
            } else if (previewData.type === 'bogo') {
                html += `<div class="alert alert-success"><strong>BOGO Offer:</strong> Buy ${previewData.buy_quantity || 1}, Get ${previewData.get_quantity || 1} Free</div>`;
            } else if (previewData.type === 'quantity_break') {
                html += `<div class="alert alert-warning"><strong>Quantity Break:</strong> Buy ${previewData.min_quantity || 1}+ items and save `;
                if (previewData.discount_percentage) {
                    html += `${previewData.discount_percentage}%`;
                } else if (previewData.discount_amount) {
                    html += `${previewData.discount_amount} each`;
                }
                html += '</div>';
            }

            // Add limits if set
            if (previewData.quantity_limit_per_customer || previewData.usage_limit_total) {
                html += '<hr><h6>🚫 Usage Limits</h6>';
                if (previewData.quantity_limit_per_customer) {
                    html += `<p><strong>Per Customer:</strong> ${previewData.quantity_limit_per_customer} items max</p>`;
                }
                if (previewData.usage_limit_total) {
                    html += `<p><strong>Total Uses:</strong> ${previewData.usage_limit_total} uses max</p>`;
                }
            }

            html += '<hr><p class="text-muted"><small>📝 This is a preview of your promotion. Review the details and click "Create This Promotion" to save.</small></p>';

            document.getElementById('previewContent').innerHTML = html;
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
        }

        // Save as draft (sets status to inactive)
        function saveAsDraft() {
            document.getElementById('status').value = 'inactive';
            document.getElementById('promotionForm').submit();
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Set up event listeners
            document.getElementById('type').addEventListener('change', togglePromotionFields);
            document.getElementById('stock_code').addEventListener('change', loadProductInfo);

            // BOGO preview updates
            const buyQtyField = document.getElementById('buy_quantity');
            const getQtyField = document.getElementById('get_quantity');
            if (buyQtyField) buyQtyField.addEventListener('input', updateBogoPreview);
            if (getQtyField) getQtyField.addEventListener('input', updateBogoPreview);

            // Form validation
            document.getElementById('promotionForm').addEventListener('submit', function(e) {
                const startDate = new Date(document.getElementById('starts_at').value);
                const endDate = new Date(document.getElementById('ends_at').value);
                const type = document.getElementById('type').value;
                const stockCode = document.getElementById('stock_code').value;

                // Basic validation
                if (endDate <= startDate) {
                    e.preventDefault();
                    alert('⚠️ End date must be after start date');
                    return false;
                }

                if (!type) {
                    e.preventDefault();
                    alert('⚠️ Please select a promotion type');
                    return false;
                }

                if (!stockCode) {
                    e.preventDefault();
                    alert('⚠️ Please select a product');
                    return false;
                }

                // Type-specific validation
                if (type === 'quantity_break') {
                    const discountPercentage = document.getElementById('discount_percentage').value;
                    const discountAmount = document.getElementById('discount_amount').value;

                    if (!discountPercentage && !discountAmount) {
                        e.preventDefault();
                        alert('⚠️ Please set either a discount percentage OR a fixed discount amount');
                        return false;
                    }
                }

                if (type === 'price_break') {
                    const priceBreaks = document.querySelectorAll('.price-break-row');
                    if (priceBreaks.length === 0) {
                        e.preventDefault();
                        alert('⚠️ Please add at least one price break tier');
                        return false;
                    }
                }

                // Show loading state
                const submitBtn = e.target.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Creating...';

                // Re-enable button after 5 seconds (in case of error)
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 5000);

                return true;
            });

            // Initialize fields based on current values (for when there are validation errors)
            togglePromotionFields();
            if (document.getElementById('stock_code').value) {
                loadProductInfo();
            }
        });

        // Enable Select2 for product selection (if available)
        $(document).ready(function() {
            if (typeof $.fn.select2 !== 'undefined') {
                $('#stock_code').select2({
                    placeholder: 'Search and select a product...',
                    allowClear: true,
                    width: '100%'
                });
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        .promotion-type-fields {
            border-left: 4px solid #e3f2fd;
            padding-left: 15px;
            margin-left: 10px;
        }

        .price-break-row {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }

        .btn-group-sm .btn {
            font-size: 0.75rem;
        }

        .accordion-button:focus {
            box-shadow: none;
        }

        .alert {
            border-left: 4px solid;
        }

        .alert-info {
            border-left-color: #17a2b8;
        }

        .alert-success {
            border-left-color: #28a745;
        }

        .alert-warning {
            border-left-color: #ffc107;
        }

        .alert-primary {
            border-left-color: #007bff;
        }

        .form-check-label small {
            display: block;
            margin-top: 2px;
        }

        #product_info_card {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-lg {
            font-size: 1.1rem;
            padding: 0.75rem 1.5rem;
        }
    </style>
@endpush
