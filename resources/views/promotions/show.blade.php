@extends('layouts.master')

@section('title', 'Promotion Details')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">{{ $promotion->name }}</h1>
                <p class="mb-0 text-muted">{{ __('Promotion Details & Analytics') }}</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('promotions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Promotions') }}
                </a>
                <a href="{{ route('promotions.edit', $promotion->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> {{ __('Edit Promotion') }}
                </a>
                @if($promotion->product)
                    <a href="{{ route('shop.products.show', $promotion->product->id) }}" class="btn btn-outline-primary" target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i> View Product
                    </a>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('Promotion Overview') }}</h6>
                        <div>
                            @php
                                $statusColors = [
                                    'active' => 'success',
                                    'inactive' => 'secondary',
                                    'scheduled' => 'warning',
                                    'expired' => 'danger'
                                ];
                                $statusColor = $statusColors[$promotion->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $statusColor }} badge-lg">
                                {{ ucfirst($promotion->status) }}
                            </span>
                            @if($promotion->is_online_only)
                                <span class="badge bg-info ms-1">Online Only</span>
                            @endif
                            @if($promotion->is_imported)
                                <span class="badge bg-secondary ms-1">Imported</span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">Basic Details</h6>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Promotion Name:</label>
                                    <p class="mb-0">{{ $promotion->name }}</p>
                                </div>

                                @if($promotion->description)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Description:</label>
                                        <p class="mb-0">{{ $promotion->description }}</p>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Promotion Type:</label>
                                    <span class="badge bg-primary">{{ ucwords(str_replace('_', ' ', $promotion->type)) }}</span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Duration:</label>
                                    <p class="mb-0">
                                        <i class="fas fa-calendar-start me-1"></i>
                                        <strong>From:</strong> {{ $promotion->starts_at->format('M j, Y g:i A') }}
                                        <br>
                                        <i class="fas fa-calendar-end me-1"></i>
                                        <strong>To:</strong> {{ $promotion->ends_at->format('M j, Y g:i A') }}
                                        <br>
                                        <small class="text-muted">
                                            Duration: {{ $promotion->starts_at->diffForHumans($promotion->ends_at, true) }}
                                        </small>
                                    </p>
                                </div>

                                @if($promotion->location_name)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Location:</label>
                                        <p class="mb-0">
                                            {{ $promotion->location_name }}
                                            @if($promotion->location_code)
                                                <small class="text-muted">({{ $promotion->location_code }})</small>
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">Product Information</h6>

                                @if($promotion->product)
                                    <div class="card border-light mb-3">
                                        <div class="card-body p-3">
                                            <h6 class="card-title mb-2">{{ $promotion->product->ProductName }}</h6>
                                            <p class="card-text">
                                                <strong>Stock Code:</strong> {{ $promotion->stock_code }}<br>
                                                @if($promotion->product->description)
                                                    <strong>Description:</strong> {{ Str::limit($promotion->product->description, 100) }}
                                                @endif
                                            </p>

                                            <div class="row text-center">
                                                <div class="col-3">
                                                    <small class="text-muted">Tier 1</small><br>
                                                    <strong>R{{ number_format($promotion->product->SellingPrice, 2) }}</strong>
                                                </div>
                                                <div class="col-3">
                                                    <small class="text-muted">Tier 2</small><br>
                                                    <strong>R{{ number_format($promotion->product->SellingPrice2, 2) }}</strong>
                                                </div>
                                                <div class="col-3">
                                                    <small class="text-muted">Tier 3</small><br>
                                                    <strong>R{{ number_format($promotion->product->SellingPrice3, 2) }}</strong>
                                                </div>
                                                <div class="col-3">
                                                    <small class="text-muted">Tier 4</small><br>
                                                    <strong>R{{ number_format($promotion->product->SellingPrice4, 2) }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Product not found for Stock Code: {{ $promotion->stock_code }}
                                    </div>
                                @endif

                                @if($promotion->customer_tiers)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Applicable Customer Tiers:</label>
                                        <p class="mb-0">
                                            @foreach($promotion->customer_tiers as $tier)
                                                <span class="badge bg-info me-1">Tier {{ $tier }}</span>
                                            @endforeach
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <hr>
                        <h6 class="text-primary mb-3">Promotion Configuration</h6>

                        <div class="row">
                            @if($promotion->type === 'date_range')
                                <div class="col-md-12">
                                    <h6>Special Pricing</h6>
                                    <div class="row">
                                        @for($i = 1; $i <= 4; $i++)
                                            @php $price = $promotion->{"sale_price_{$i}"}; @endphp
                                            @if($price)
                                                <div class="col-md-3">
                                                    <div class="text-center p-2 border rounded">
                                                        <small class="text-muted">Tier {{ $i }}</small><br>
                                                        <strong class="text-success">R{{ number_format($price / 100, 2) }}</strong>
                                                    </div>
                                                </div>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            @endif

                            @if($promotion->type === 'bogo')
                                <div class="col-md-6">
                                    <h6>Buy One Get One Configuration</h6>
                                    <p>
                                        <strong>Buy:</strong> {{ $promotion->buy_quantity ?: 1 }} item(s)<br>
                                        <strong>Get:</strong> {{ $promotion->get_quantity ?: 1 }} free
                                    </p>
                                </div>
                            @endif

                            @if($promotion->type === 'quantity_break')
                                <div class="col-md-6">
                                    <h6>Quantity Break Discount</h6>
                                    <p>
                                        <strong>Minimum Quantity:</strong> {{ $promotion->min_quantity }}<br>
                                        @if($promotion->discount_percentage)
                                            <strong>Discount:</strong> {{ $promotion->discount_percentage }}%
                                        @elseif($promotion->discount_amount)
                                            <strong>Discount:</strong> ${{ number_format($promotion->discount_amount / 100, 2) }}
                                        @endif
                                    </p>
                                </div>
                            @endif

                            @if($promotion->price_breaks)
                                <div class="col-md-6">
                                    <h6>Price Break Structure</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                            <tr>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($promotion->price_breaks as $break)
                                                <tr>
                                                    <td>{{ $break['qty'] }}+</td>
                                                    <td>${{ number_format($break['price'] / 100, 2) }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            @if($promotion->bonus_breaks)
                                <div class="col-md-6">
                                    <h6>Bonus Quantity Structure</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                            <tr>
                                                <th>Buy</th>
                                                <th>Get Bonus</th>
                                                <th>Discount</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($promotion->bonus_breaks as $break)
                                                <tr>
                                                    <td>{{ $break['break_qty'] }}</td>
                                                    <td>{{ $break['bonus_qty'] }}</td>
                                                    <td>{{ $break['discount'] }}%</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($promotion->quantity_limit_per_customer || $promotion->usage_limit_total)
                            <hr>
                            <h6 class="text-primary mb-3">Usage Limits</h6>
                            <div class="row">
                                @if($promotion->quantity_limit_per_customer)
                                    <div class="col-md-6">
                                        <strong>Per Customer Limit:</strong> {{ $promotion->quantity_limit_per_customer }} items
                                    </div>
                                @endif
                                @if($promotion->usage_limit_total)
                                    <div class="col-md-6">
                                        <strong>Total Usage Limit:</strong> {{ $promotion->usage_limit_total }} uses
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                @if($usageStats['recent_usage']->isNotEmpty())
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Recent Usage</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Quantity</th>
                                        <th>Savings</th>
                                        <th>Order</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($usageStats['recent_usage'] as $usage)
                                        <tr>
                                            <td>{{ $usage->created_at->format('M j, Y g:i A') }}</td>
                                            <td>
                                                @if($usage->customer)
                                                    {{ $usage->customer->name }}
                                                    <br><small class="text-muted">{{ $usage->customer->email }}</small>
                                                @else
                                                    Guest Customer
                                                @endif
                                            </td>
                                            <td>
                                                {{ $usage->quantity_purchased }}
                                                @if($usage->bonus_quantity > 0)
                                                    <small class="text-success">+{{ $usage->bonus_quantity }} bonus</small>
                                                @endif
                                            </td>
                                            <td class="text-success">${{ number_format($usage->total_savings_cents / 100, 2) }}</td>
                                            <td>
                                                @if($usage->order_id)
                                                    <a href="{{ route('orders.show', $usage->order_id) }}" class="btn btn-sm btn-outline-primary">
                                                        #{{ $usage->order_id }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">No order</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Usage Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <div class="h3 text-primary mb-0">{{ number_format($usageStats['total_usage']) }}</div>
                                    <small class="text-muted">Total Uses</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="h3 text-success mb-0">{{ number_format($usageStats['unique_customers']) }}</div>
                                <small class="text-muted">Unique Customers</small>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <strong>Total Savings Generated:</strong>
                            <h4 class="text-success">R{{ number_format($usageStats['total_savings'] / 100, 2) }}</h4>
                        </div>

                        @if($promotion->usage_limit_total)
                            <div class="mb-3">
                                <strong>Usage Progress:</strong>
                                @php $percentage = ($promotion->usage_count / $promotion->usage_limit_total) * 100; @endphp
                                <div class="progress mt-1">
                                    <div class="progress-bar bg-{{ $percentage > 80 ? 'danger' : ($percentage > 60 ? 'warning' : 'primary') }}"
                                         style="width: {{ min($percentage, 100) }}%">
                                        {{ number_format($percentage, 1) }}%
                                    </div>
                                </div>
                                <small class="text-muted">
                                    {{ $promotion->usage_count }} / {{ $promotion->usage_limit_total }} uses
                                </small>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Test Calculator</h6>
                    </div>
                    <div class="card-body">
                        <form id="testCalculator">
                            <div class="mb-3">
                                <label class="form-label">Quantity:</label>
                                <input type="number" class="form-control" id="test_quantity" value="1" min="1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Customer Tier:</label>
                                <select class="form-control" id="test_tier">
                                    <option value="1">Tier 1</option>
                                    <option value="2">Tier 2</option>
                                    <option value="3">Tier 3</option>
                                    <option value="4">Tier 4</option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm w-100" onclick="testCalculation()">
                                <i class="fas fa-calculator me-1"></i> Calculate Discount
                            </button>
                        </form>

                        <div id="calculation_result" class="mt-3" style="display: none;">
                            <!-- Results will be populated here -->
                        </div>
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('promotions.edit', $promotion) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit Promotion
                            </a>

                            @if($promotion->status === 'active')
                                <button class="btn btn-secondary btn-sm" onclick="updateStatus('inactive')">
                                    <i class="fas fa-pause me-1"></i> Deactivate
                                </button>
                            @elseif($promotion->status === 'inactive')
                                <button class="btn btn-success btn-sm" onclick="updateStatus('active')">
                                    <i class="fas fa-play me-1"></i> Activate
                                </button>
                            @endif

                            <button class="btn btn-danger btn-sm" onclick="deletePromotion()">
                                <i class="fas fa-trash me-1"></i> Delete Promotion
                            </button>

                            @if($promotion->product)
                                <a href="{{ route('shop.products.show', $promotion->product->id) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                                    <i class="fas fa-external-link-alt me-1"></i> View in Shop
                                </a>
                            @endif

                            @if($promotion->is_imported && $promotion->import_batch_id)
                                <a href="{{ route('promotions.index', ['batch_id' => $promotion->import_batch_id]) }}" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-download me-1"></i> View Import Batch
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this promotion? This action cannot be undone.
                    @if($usageStats['total_usage'] > 0)
                        <div class="alert alert-warning mt-2">
                            <strong>Warning:</strong> This promotion has been used {{ $usageStats['total_usage'] }} times.
                            Deleting it will also remove all usage history.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" action="{{ route('promotions.destroy', $promotion) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Promotion</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function testCalculation() {
        const quantity = document.getElementById('test_quantity').value;
        const tier = document.getElementById('test_tier').value;
        const resultDiv = document.getElementById('calculation_result');

        fetch('{{ route('promotions.test-calculation') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
            promotion_id: {{ $promotion->id }},
                quantity: parseInt(quantity),
                customer_tier: parseInt(tier)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const calc = data.calculation;
                resultDiv.innerHTML = `
                <div class="alert alert-${calc.applicable ? 'success' : 'info'}">
                    <h6>${calc.applicable ? 'Discount Applied!' : 'No Discount'}</h6>
                    <p><strong>Original Price:</strong> ${data.original_price_formatted}</p>
                    ${calc.applicable ? `
                        <p><strong>Discounted Price:</strong> R${(calc.discounted_price / 100).toFixed(2)}</p>
                        <p><strong>Total Savings:</strong> R${(calc.total_savings / 100).toFixed(2)}</p>
                        ${calc.bonus_quantity > 0 ? `<p><strong>Bonus Items:</strong> ${calc.bonus_quantity}</p>` : ''}
                    ` : ''}
                    <p><small>${calc.message}</small></p>
                </div>
            `;
            } else {
                resultDiv.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
            }
            resultDiv.style.display = 'block';
        })
        .catch(error => {
            resultDiv.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
            resultDiv.style.display = 'block';
        });
    }

    function updateStatus(newStatus) {
        if (confirm(`Are you sure you want to change this promotion status to ${newStatus}?`)) {
            fetch('{{ route("promotions.update", $promotion) }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    status: newStatus,
                    _method: 'PATCH'
                })
            })
                .then(() => {
                    location.reload();
                })
                .catch(error => {
                    alert('Failed to update status: ' + error.message);
                });
        }
    }

    function deletePromotion() {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>
@endpush
