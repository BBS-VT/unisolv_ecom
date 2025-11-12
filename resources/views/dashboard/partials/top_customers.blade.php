<div class="col-lg-6">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">
                <i class="mdi mdi-account-star me-2 text-primary"></i>
                Top Customers
            </h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="thead-light">
                    <tr>
                        <th class="border-top-0">Customer</th>
                        <th class="border-top-0 text-center">Orders</th>
                        <th class="border-top-0 text-end">Total Spent</th>
                        <th class="border-top-0 text-end">Avg. Order</th>
                        <th class="border-top-0 text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($topCustomers as $index => $customer)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <!-- Customer Rank Badge -->
                                    <div class="flex-shrink-0 me-3">
                                        @if($index === 0)
                                            <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center"
                                                 style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);">
                                                <i class="mdi mdi-trophy text-white"></i>
                                            </div>
                                        @elseif($index === 1)
                                            <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center"
                                                 style="background: linear-gradient(135deg, #C0C0C0 0%, #808080 100%);">
                                                <i class="mdi mdi-trophy text-white"></i>
                                            </div>
                                        @elseif($index === 2)
                                            <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center"
                                                 style="background: linear-gradient(135deg, #CD7F32 0%, #8B4513 100%);">
                                                <i class="mdi mdi-trophy text-white"></i>
                                            </div>
                                        @else
                                            <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center bg-light">
                                                <span class="text-muted fw-bold">{{ $index + 1 }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Customer Info -->
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-semibold">{{ $customer->CustomerName }}</h6>
                                        <small class="text-muted">
                                            <i class="mdi mdi-identifier me-1"></i>{{ $customer->acc_main }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary rounded-pill px-3">
                                    {{ $customer->order_count }}
                                </span>
                            </td>
                            <td class="text-end">
                                <strong class="text-success">R{{ number_format($customer->total_spent, 2) }}</strong>
                            </td>
                            <td class="text-end">
                                <span class="text-muted">R{{ number_format($customer->avg_order_value, 2) }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('customers.show', $customer->id) }}"
                                   class="btn btn-sm btn-soft-success"
                                   title="View Customer">
                                    <i class="mdi mdi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <div class="py-3">
                                    <i class="mdi mdi-account-alert-outline mdi-48px d-block mb-3 opacity-50"></i>
                                    <p class="mb-0">No customers found in this period</p>
                                    <small class="text-muted">Try selecting a different time period</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($topCustomers->isNotEmpty())
                <!-- Summary Stats -->
                <div class="row g-3 mt-3 pt-3 border-top">
                    <div class="col-4 text-center">
                        <div class="mb-1">
                            <i class="mdi mdi-account-group text-primary mdi-24px"></i>
                        </div>
                        <h6 class="mb-0">{{ $topCustomers->count() }}</h6>
                        <small class="text-muted">Top Customers</small>
                    </div>
                    <div class="col-4 text-center">
                        <div class="mb-1">
                            <i class="mdi mdi-cart text-success mdi-24px"></i>
                        </div>
                        <h6 class="mb-0">{{ $topCustomers->sum('order_count') }}</h6>
                        <small class="text-muted">Total Orders</small>
                    </div>
                    <div class="col-4 text-center">
                        <div class="mb-1">
                            <i class="mdi mdi-currency-usd text-warning mdi-24px"></i>
                        </div>
                        <h6 class="mb-0">R{{ number_format($topCustomers->sum('total_spent'), 0) }}</h6>
                        <small class="text-muted">Combined Value</small>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* Top Customers specific styles */
    .avatar-sm {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }

    .btn-soft-success {
        background-color: rgba(10, 207, 151, 0.18);
        color: #0acf97;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-soft-success:hover {
        background-color: #0acf97;
        color: white;
        transform: scale(1.1);
    }

    /* Trophy animation for top 3 */
    .mdi-trophy {
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }

    /* Better badge styling */
    .badge.rounded-pill {
        font-weight: 600;
        padding: 0.375rem 0.75rem;
    }
</style>
