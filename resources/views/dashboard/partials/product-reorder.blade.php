<div class="col-lg-6">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">
                <i class="mdi mdi-refresh-circle me-2 text-primary"></i>
                {{ __('Frequently Reordered Products') }}
            </h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="thead-light">
                    <tr>
                        <th class="border-top-0">Product</th>
                        <th class="border-top-0 text-center">Orders</th>
                        <th class="border-top-0 text-center">Customers</th>
                        <th class="border-top-0">Reorder Rate</th>
                        <th class="border-top-0 text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($productReorderRates as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <!-- Product Image/Icon -->
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-sm rounded" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            <div class="avatar-title rounded">
                                                <i class="mdi mdi-package-variant text-white"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Product Info -->
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-semibold">{{ Str::limit($product->StockItemName, 40) }}</h6>
                                        <small class="text-muted">
                                            <i class="mdi mdi-barcode me-1"></i>{{ $product->StockCode }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info rounded-pill px-3">
                                    {{ $product->order_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary rounded-pill px-3">
                                    {{ $product->unique_customers }}
                                </span>
                            </td>
                            <td style="min-width: 200px;">
                                @php
                                    $reorderRatio = $product->reorder_ratio;
                                    $percentage = min(($reorderRatio / 5) * 100, 100); // Scale to max of 5 orders

                                    // Determine color based on ratio
                                    if ($reorderRatio >= 3) {
                                        $bgClass = 'bg-success';
                                        $textClass = 'text-success';
                                    } elseif ($reorderRatio >= 2) {
                                        $bgClass = 'bg-info';
                                        $textClass = 'text-info';
                                    } elseif ($reorderRatio >= 1.5) {
                                        $bgClass = 'bg-warning';
                                        $textClass = 'text-warning';
                                    } else {
                                        $bgClass = 'bg-secondary';
                                        $textClass = 'text-secondary';
                                    }
                                @endphp

                                    <!-- Progress Bar -->
                                <div class="progress mb-2" style="height: 8px; border-radius: 4px;">
                                    <div class="progress-bar {{ $bgClass }}"
                                         role="progressbar"
                                         style="width: {{ $percentage }}%;"
                                         aria-valuenow="{{ $reorderRatio }}"
                                         aria-valuemin="0"
                                         aria-valuemax="5">
                                    </div>
                                </div>

                                <!-- Reorder Info -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="{{ $textClass }} fw-semibold">
                                        <i class="mdi mdi-sync me-1"></i>{{ number_format($reorderRatio, 1) }}x
                                    </small>
                                    <small class="text-muted">per customer</small>
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('products.show', $product->id) }}"
                                   class="btn btn-sm btn-soft-primary"
                                   title="View Product">
                                    <i class="mdi mdi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <div class="py-3">
                                    <i class="mdi mdi-package-variant-closed mdi-48px d-block mb-3 opacity-50"></i>
                                    <p class="mb-0">No reordered products found in this period</p>
                                    <small class="text-muted">Products need multiple orders from same customer</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($productReorderRates->isNotEmpty())
                <!-- Summary Stats -->
                <div class="row g-3 mt-3 pt-3 border-top">
                    <div class="col-4 text-center">
                        <div class="mb-1">
                            <i class="mdi mdi-package-variant text-primary mdi-24px"></i>
                        </div>
                        <h6 class="mb-0">{{ $productReorderRates->count() }}</h6>
                        <small class="text-muted">Top Products</small>
                    </div>
                    <div class="col-4 text-center">
                        <div class="mb-1">
                            <i class="mdi mdi-sync text-success mdi-24px"></i>
                        </div>
                        <h6 class="mb-0">{{ number_format($productReorderRates->avg('reorder_ratio'), 1) }}x</h6>
                        <small class="text-muted">Avg. Reorder</small>
                    </div>
                    <div class="col-4 text-center">
                        <div class="mb-1">
                            <i class="mdi mdi-account-group text-info mdi-24px"></i>
                        </div>
                        <h6 class="mb-0">{{ $productReorderRates->sum('unique_customers') }}</h6>
                        <small class="text-muted">Total Customers</small>
                    </div>
                </div>

                <!-- Reorder Legend -->
                <div class="alert alert-light border mt-3 mb-0" role="alert">
                    <div class="d-flex align-items-center mb-2">
                        <i class="mdi mdi-information-outline me-2 text-primary"></i>
                        <strong class="mb-0">Reorder Rate Legend</strong>
                    </div>
                    <div class="row g-2 small">
                        <div class="col-6">
                            <span class="badge bg-success me-1">●</span>
                            <span class="text-muted">Excellent (3.0+)</span>
                        </div>
                        <div class="col-6">
                            <span class="badge bg-info me-1">●</span>
                            <span class="text-muted">Good (2.0-2.9)</span>
                        </div>
                        <div class="col-6">
                            <span class="badge bg-warning me-1">●</span>
                            <span class="text-muted">Fair (1.5-1.9)</span>
                        </div>
                        <div class="col-6">
                            <span class="badge bg-secondary me-1">●</span>
                            <span class="text-muted">Low (&lt;1.5)</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* Product Reorder specific styles */
    .avatar-sm {
        width: 40px;
        height: 40px;
    }

    .avatar-title {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .btn-soft-primary {
        background-color: rgba(102, 126, 234, 0.18);
        color: #667eea;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-soft-primary:hover {
        background-color: #667eea;
        color: white;
        transform: scale(1.1);
    }

    /* Progress bar enhancements */
    .progress {
        background-color: #f1f3f5;
    }

    .progress-bar {
        transition: width 0.6s ease;
    }

    /* Badge styling */
    .badge.rounded-pill {
        font-weight: 600;
        padding: 0.375rem 0.75rem;
    }

    /* Reorder rate color indicators */
    .text-success { color: #0acf97 !important; }
    .text-info { color: #4facfe !important; }
    .text-warning { color: #ffbc00 !important; }
    .text-secondary { color: #6c757d !important; }

    /* Legend badge dots */
    .alert .badge {
        font-size: 0.75rem;
        padding: 0.2rem 0.4rem;
    }
</style>
