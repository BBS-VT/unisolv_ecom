@extends('shop.layouts.app')

@section('title', 'My Orders')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1">My Orders</h1>
                        <p class="text-muted mb-0">View and manage your order history</p>
                    </div>
                    <div>
                        <a href="{{ route('shop.products.index') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Place New Order
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="GET" action="{{ route('shop.account.orders.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Search Orders</label>
                                <input type="text"
                                       class="form-control"
                                       id="search"
                                       name="search"
                                       value="{{ request('search') }}"
                                       placeholder="Order # or PO #">
                            </div>

                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Statuses</option>
                                    @foreach($statusOptions as $id => $name)
                                        <option value="{{ $id }}" {{ request('status') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date"
                                       class="form-control"
                                       id="date_from"
                                       name="date_from"
                                       value="{{ request('date_from') }}">
                            </div>

                            <div class="col-md-2">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date"
                                       class="form-control"
                                       id="date_to"
                                       name="date_to"
                                       value="{{ request('date_to') }}">
                            </div>

                            <div class="col-md-2">
                                <label for="sort" class="form-label">Sort By</label>
                                <select class="form-select" id="sort" name="sort">
                                    <option value="OrderDate" {{ request('sort') == 'OrderDate' ? 'selected' : '' }}>Date</option>
                                    <option value="OrderNumber" {{ request('sort') == 'OrderNumber' ? 'selected' : '' }}>Order #</option>
                                    <option value="total" {{ request('sort') == 'total' ? 'selected' : '' }}>Total</option>
                                    <option value="OrderStatusID" {{ request('sort') == 'OrderStatusID' ? 'selected' : '' }}>Status</option>
                                </select>
                            </div>

                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <a href="{{ route('shop.account.orders.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0">
                                    Orders ({{ $orders->total() }})
                                    @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                                        <small class="text-muted">- filtered results</small>
                                    @endif
                                </h5>
                            </div>
                            <div class="col-auto">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ request()->fullUrlWithQuery(['direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}"
                                       class="btn btn-outline-secondary">
                                        <i class="fas fa-sort"></i>
                                        {{ request('direction') === 'asc' ? 'Desc' : 'Asc' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        @if($orders->count() > 0)
                            <!-- Desktop View -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Order Details</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Delivery</th>
                                        <th>Total</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong class="text-primary">{{ $order->OrderNumber }}</strong>
                                                    @if($order->CustomerPurchaseOrderNumber)
                                                        <br><small class="text-muted">PO: {{ $order->CustomerPurchaseOrderNumber }}</small>
                                                    @endif
                                                    @if($order->salesperson)
                                                        <br><small class="text-muted">Sales: {{ $order->salesperson->PreferredName }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    {{ $order->OrderDate->format('M d, Y') }}
                                                    <br><small class="text-muted">{{ $order->OrderDate->format('g:i A') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge {{ \App\Http\Controllers\Shop\OrderController::getStatusBadgeClass($order->OrderStatusID) }}">
                                                    {{ \App\Http\Controllers\Shop\OrderController::getStatusName($order->OrderStatusID) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>
                                                    <span class="badge bg-light text-dark">{{ ucfirst($order->delivery_method) }}</span>
                                                    @if($order->preferred_delivery_date)
                                                        <br><small class="text-muted">{{ \Carbon\Carbon::parse($order->preferred_delivery_date)->format('M d, Y') }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ \App\Helpers\PricingHelper::formatPrice($order->total / 100) }}</strong>
                                                    <br><small class="text-muted">{{ $order->items->count() }} items</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('shop.account.orders.show', $order) }}"
                                                       class="btn btn-outline-primary"
                                                       title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('shop.account.orders.reorder', $order) }}"
                                                       class="btn btn-outline-secondary"
                                                       title="Reorder">
                                                        <i class="fas fa-redo"></i>
                                                    </a>
                                                    @if($order->OrderStatusID == 1)
                                                        <button type="button"
                                                                class="btn btn-outline-danger"
                                                                title="Cancel Order"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#cancelModal{{ $order->id }}">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile View -->
                            <div class="d-md-none">
                                @foreach($orders as $order)
                                    @include('shop.account.orders.partials.order-card', ['order' => $order])
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="card-footer bg-white border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small">
                                        Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} orders
                                    </div>
                                    <div>
                                        {{ $orders->links() }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="p-5 text-center text-muted">
                                <i class="fas fa-shopping-bag fa-4x mb-3 opacity-25"></i>
                                <h5>No orders found</h5>
                                <p class="mb-3">
                                    @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                                        Try adjusting your search criteria or <a href="{{ route('shop.account.orders.index') }}">view all orders</a>.
                                    @else
                                        You haven't placed any orders yet.
                                    @endif
                                </p>
                                <a href="{{ route('shop.products.index') }}" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Start Shopping
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Order Modals -->
    @foreach($orders as $order)
        @if($order->OrderStatusID == 1)
            <div class="modal fade" id="cancelModal{{ $order->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('shop.account.orders.cancel', $order) }}">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Cancel Order #{{ $order->OrderNumber }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Are you sure you want to cancel this order? This action cannot be undone.
                                </div>

                                <div class="mb-3">
                                    <label for="cancel_reason{{ $order->id }}" class="form-label">
                                        Reason for cancellation <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="cancel_reason{{ $order->id }}" name="cancel_reason" required>
                                        <option value="">Select a reason...</option>
                                        <option value="Changed mind">Changed mind</option>
                                        <option value="Found better price">Found better price</option>
                                        <option value="Ordered by mistake">Ordered by mistake</option>
                                        <option value="No longer needed">No longer needed</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>

                                <div class="order-summary">
                                    <h6>Order Summary:</h6>
                                    <p class="mb-1"><strong>Total:</strong> {{ \App\Helpers\PricingHelper::formatPrice($order->total / 100) }}</p>
                                    <p class="mb-0"><strong>Items:</strong> {{ $order->items->count() }}</p>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Order</button>
                                <button type="submit" class="btn btn-danger">Cancel Order</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

@endsection

@push('styles')
    <style>
        .table th {
            border-top: none;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge {
            font-size: 0.75rem;
        }

        .order-card {
            border-bottom: 1px solid #dee2e6;
        }

        .order-card:last-child {
            border-bottom: none;
        }

        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
        }

        .modal-body .order-summary {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.375rem;
            margin-top: 1rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Auto-submit form when sort or direction changes
        document.getElementById('sort').addEventListener('change', function() {
            this.form.submit();
        });

        // Clear individual filters
        function clearFilter(filterName) {
            const url = new URL(window.location);
            url.searchParams.delete(filterName);
            window.location.href = url.toString();
        }

        // Show active filters
        document.addEventListener('DOMContentLoaded', function() {
            const activeFilters = [];
            const params = new URLSearchParams(window.location.search);

            if (params.get('search')) activeFilters.push('Search');
            if (params.get('status')) activeFilters.push('Status');
            if (params.get('date_from')) activeFilters.push('From Date');
            if (params.get('date_to')) activeFilters.push('To Date');

            if (activeFilters.length > 0) {
                console.log('Active filters:', activeFilters.join(', '));
            }
        });
    </script>
@endpush
