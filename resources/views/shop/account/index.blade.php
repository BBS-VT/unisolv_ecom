@extends('shop.layouts.app')

@section('title', 'My Account - Dashboard')

@section('content')
    <div class="container-fluid py-4">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="h3 mb-2 text-dark">Welcome back, {{ $customer->CustomerName }}</h1>
                                <p class="text-muted mb-0">
                                    Account: {{ $customer->acc_code }} |
                                    Credit Limit: {{ \App\Helpers\PricingHelper::formatPrice($customer->CreditLimit / 100) }}
                                    @if($customer->IsOnCreditHold)
                                        <span class="badge bg-danger ms-2">Credit Hold</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <a href="{{ route('shop.products.index') }}" class="btn btn-primary me-2">
                                    <i class="fas fa-shopping-cart"></i> Start Shopping
                                </a>
                                <a href="{{ route('shop.account.orders.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-list"></i> View All Orders
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Account Statistics -->
            <div class="col-lg-8">
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="text-primary mb-2">
                                    <i class="fas fa-shopping-bag fa-2x"></i>
                                </div>
                                <h4 class="h5">{{ $orderStats['total_orders'] }}</h4>
                                <p class="text-muted small mb-0">Total Orders</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="text-info mb-2">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                                <h4 class="h5">{{ $orderStats['new_orders'] }}</h4>
                                <p class="text-muted small mb-0">New Orders</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="text-success mb-2">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                                <h4 class="h5">{{ $orderStats['delivered_orders'] }}</h4>
                                <p class="text-muted small mb-0">Completed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="text-warning mb-2">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                                <h4 class="h5">{{ \App\Helpers\PricingHelper::formatPrice($totalSpending / 100) }}</h4>
                                <p class="text-muted small mb-0">Total Spent (12m)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0">Recent Orders</h5>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('shop.account.orders.index') }}" class="btn btn-sm btn-outline-primary">
                                    View All Orders
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($recentOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td>
                                                <strong>{{ $order->OrderNumber }}</strong>
                                                @if($order->CustomerPurchaseOrderNumber)
                                                    <br><small class="text-muted">PO: {{ $order->CustomerPurchaseOrderNumber }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $order->OrderDate->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge {{ \App\Http\Controllers\Shop\OrderController::getStatusBadgeClass($order->OrderStatusID) }}">
                                                    {{ \App\Http\Controllers\Shop\OrderController::getStatusName($order->OrderStatusID) }}
                                                </span>
                                            </td>
                                            <td>{{ \App\Helpers\PricingHelper::formatPrice($order->total / 100) }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('shop.account.orders.show', $order) }}"
                                                       class="btn btn-outline-primary btn-sm">
                                                        View
                                                    </a>
                                                    <a href="{{ route('shop.account.orders.reorder', $order) }}"
                                                       class="btn btn-outline-secondary btn-sm">
                                                        Reorder
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-shopping-bag fa-3x mb-3 opacity-50"></i>
                                <p>No orders yet. <a href="{{ route('shop.products.index') }}">Start shopping!</a></p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Account Info Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('shop.products.index') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Place New Order
                            </a>
                            <a href="{{ route('shop.account.orders.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list"></i> View All Orders
                            </a>
                            <a href="{{ route('shop.account.profile') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-user"></i> Update Profile
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0">Account Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <strong>Credit Limit:</strong><br>
                                <span class="h5 text-success">{{ \App\Helpers\PricingHelper::formatPrice($customer->CreditLimit / 100) }}</span>
                            </div>

                            @if($creditUtilization > 0)
                                <div class="col-12">
                                    <strong>Credit Usage:</strong><br>
                                    <div class="progress mb-1" style="height: 8px;">
                                        <div class="progress-bar {{ $creditUtilization > 80 ? 'bg-danger' : ($creditUtilization > 60 ? 'bg-warning' : 'bg-success') }}"
                                             style="width: {{ min($creditUtilization, 100) }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ number_format($creditUtilization, 1) }}% utilized</small>
                                </div>
                            @endif

                            <div class="col-12">
                                <strong>Price Level:</strong><br>
                                <span class="badge bg-info">Level {{ $customer->price_level }}</span>
                            </div>

                            <div class="col-12">
                                <strong>Account Status:</strong><br>
                                @if($customer->IsOnCreditHold)
                                    <span class="badge bg-danger">Credit Hold</span>
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0">Contact Information</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <i class="fas fa-envelope text-muted me-2"></i>
                            {{ $customer->GeneralEmailAddress }}
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-map-marker-alt text-muted me-2"></i>
                            {{ $customer->DeliveryAddressLine1 }}<br>
                            @if($customer->DeliveryAddressLine2)
                                {{ $customer->DeliveryAddressLine2 }}<br>
                            @endif
                            {{ $customer->DeliveryCity }}, {{ $customer->DeliveryState }} {{ $customer->DeliveryPostCode }}
                        </p>
                        <hr>
                        <a href="{{ route('shop.account.profile') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i> Update Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .stats-card {
            border-left: 4px solid var(--bs-primary);
        }

        .progress {
            background-color: #e9ecef;
        }
    </style>
@endpush
