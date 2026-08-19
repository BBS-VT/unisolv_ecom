@extends('layouts.master', ['page' => 'dashboard'])

@section('title', __('Customer Dashboard'))

@push('styles')
    <style>
        .summary-card {
            transition: all 0.3s ease;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .summary-icon {
            font-size: 24px;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 16px;
        }

        .balance-card {
            border-start: 4px solid;
        }

        .balance-current {
            border-start-color: #1C75BC;
        }

        .balance-30 {
            border-start-color: #0acf97;
        }

        .balance-60 {
            border-start-color: #ffbc00;
        }

        .balance-90 {
            border-start-color: #fa5c7c;
        }

        .table td, .table th {
            vertical-align: middle;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            margin-right: 6px;
            transition: all 0.2s;
        }

        .action-btn:hover {
            background-color: rgba(28, 117, 188, 0.1);
        }

        .welcome-card {
            background-image: linear-gradient(to right, #1C75BC, #2A3042);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .account-info-item {
            border-bottom: 1px dashed #eee;
            padding: 10px 0;
        }

        .account-info-item:last-child {
            border-bottom: none;
        }
        @media (max-width: 767.98px) {
            /* Reorganize layout for mobile devices */
            .welcome-card {
                padding: 15px;
            }

            .welcome-card h2 {
                font-size: 1.5rem;
            }

            /* Adjust spacing for balance cards on mobile */
            .balance-card {
                margin-bottom: 12px;
            }

            /* Ensure proper alignment of balance card elements */
            .summary-icon {
                width: 40px;
                height: 40px;
                font-size: 18px;
                margin-right: 10px;
            }

            /* Improve text sizes for mobile */
            .card-title {
                font-size: 1.1rem;
            }

            /* Make tables scroll horizontally on mobile */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            /* Adjust action buttons for better touch targets */
            .action-btn {
                width: 36px;
                height: 36px;
                margin-right: 4px;
            }

            /* Make tab navigation scroll horizontally if needed */
            .nav-tabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                overflow-y: hidden;
                -webkit-overflow-scrolling: touch;
            }

            .nav-tabs .nav-item {
                white-space: nowrap;
            }

            /* Improve spacing in account info items */
            .account-info-item {
                padding: 8px 0;
            }

            /* Better pagination display on small screens */
            .pagination {
                justify-content: center;
                flex-wrap: wrap;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Welcome Banner -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="welcome-card">
                        <h2 class="mb-2">{{ __('Welcome back') }}, {{ $customer->CustomerName ?? 'Customer' }}!</h2>
                        <p class="mb-0">{{ __('Here\'s an overview of your account and recent activities') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Customer Information Card -->
            <div class="col-lg-3">
                <div class="card summary-card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="dripicons-user me-1 text-primary"></i> {{ __('Account Information') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($customer)
                            <div class="account-info-item">
                                <small class="text-muted d-block">{{ __('Customer Name') }}</small>
                                <strong>{{ $customer->CustomerName }}</strong>
                            </div>
                            <div class="account-info-item">
                                <small class="text-muted d-block">{{ __('Account Code') }}</small>
                                <strong>{{ $customer->acc_code }}</strong>
                            </div>
                            <div class="account-info-item">
                                <small class="text-muted d-block">{{ __('Delivery Address') }}</small>
                                <strong>{{ $customer->DeliveryAddressLine1 }}, {{ $customer->DeliveryCity }}, {{ $customer->DeliveryPostalCode }}</strong>
                            </div>
                        @else
                            <p class="text-muted">{{ __('No customer data found.') }}</p>
                        @endif
                    </div>
                    <div class="card-footer bg-light">
                        <a href="{{ route('customer.profile') }}" class="btn btn-sm btn-outline-primary btn-block">
                            <i class="dripicons-pencil me-1"></i> {{ __('Update Profile') }}
                        </a>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="card summary-card mt-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="dripicons-lightbulb me-1 text-warning"></i> {{ __('Quick Actions') }}
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('orders.create') }}" class="list-group-item list-group-item-action">
                                <i class="dripicons-cart me-2 text-primary"></i> {{ __('Place New Order') }}
                            </a>
                            <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action">
                                <i class="dripicons-document-edit me-2 text-info"></i> {{ __('View All Orders') }}
                            </a>
                            <a href="{{ route('products.index') }}" class="list-group-item list-group-item-action">
                                <i class="dripicons-view-list me-2 text-success"></i> {{ __('Browse Products') }}
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="dripicons-message me-2 text-warning"></i> {{ __('Contact Support') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <!-- Balance Summary Cards -->
                <div class="row">
                    <div class="col-md-6 col-lg-3">
                        <div class="card summary-card balance-card balance-current mb-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="summary-icon bg-primary-light text-primary">
                                        <i class="dripicons-calendar"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-0 font-weight-semibold">{{ __('Current') }}</p>
                                        @if($customerBalance)
                                            <h4 class="mt-1 mb-0">R {{ number_format($customerBalance->AgedBalance1, 2) }}</h4>
                                        @else
                                            <p class="text-muted mb-0">{{ __('No data') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card summary-card balance-card balance-30 mb-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="summary-icon bg-success-light text-success">
                                        <i class="dripicons-calendar"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-0 font-weight-semibold">{{ __('30 Days') }}</p>
                                        @if($customerBalance)
                                            <h4 class="mt-1 mb-0">R {{ number_format($customerBalance->AgedBalance2, 2) }}</h4>
                                        @else
                                            <p class="text-muted mb-0">{{ __('No data') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card summary-card balance-card balance-60 mb-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="summary-icon bg-warning-light text-warning">
                                        <i class="dripicons-calendar"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-0 font-weight-semibold">{{ __('60 Days') }}</p>
                                        @if($customerBalance)
                                            <h4 class="mt-1 mb-0">R {{ number_format($customerBalance->AgedBalance3, 2) }}</h4>
                                        @else
                                            <p class="text-muted mb-0">{{ __('No data') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card summary-card balance-card balance-90 mb-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="summary-icon bg-danger-light text-danger">
                                        <i class="dripicons-calendar"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-0 font-weight-semibold">{{ __('90 Days') }}</p>
                                        @if($customerBalance)
                                            <h4 class="mt-1 mb-0">R {{ number_format($customerBalance->AgedBalance4, 2) }}</h4>
                                        @else
                                            <p class="text-muted mb-0">{{ __('No data') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders Card with Actions -->
                <div class="card summary-card">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="dripicons-cart me-1 text-primary"></i> {{ __('Recent Orders') }}
                        </h5>
                        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">
                            {{ __('View All') }}
                        </a>
                    </div>
                    <div class="card-body">
                        @if($orders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>{{ __('Order #') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Total') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>
                                                <strong>{{ $order->OrderNumber }}</strong>
                                            </td>
                                            <td>{{ Carbon\Carbon::parse($order->OrderDate)->format('d M, Y') }}</td>
                                            <td>
                                                <strong>R {{ number_format(($order->total/100), 2) }}</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = 'secondary';
                                                    if(isset($order->orderStatus)) {
                                                        switch(strtolower($order->orderStatus->name)) {
                                                            case 'pending':
                                                                $statusClass = 'warning';
                                                                break;
                                                            case 'processing':
                                                                $statusClass = 'info';
                                                                break;
                                                            case 'completed':
                                                                $statusClass = 'success';
                                                                break;
                                                            case 'cancelled':
                                                                $statusClass = 'danger';
                                                                break;
                                                        }
                                                    }
                                                @endphp
                                                <span class="badge badge-{{ $statusClass }} status-badge">
                                                        {{ $order->orderStatus->name ?? 'Unknown' }}
                                                    </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('orders.show', $order->id) }}" class="action-btn text-info" data-bs-toggle="tooltip" title="{{ __('View Order') }}">
                                                    <i class="dripicons-preview"></i>
                                                </a>
                                                {{--<a href="{{ route('orders.track', $order->id) }}" class="action-btn text-success" data-bs-toggle="tooltip" title="{{ __('Track Order') }}">
                                                    <i class="dripicons-location"></i>
                                                </a>--}}
                                                {{--<a href="{{ route('orders.reorder', $order->id) }}" class="action-btn text-primary" data-bs-toggle="tooltip" title="{{ __('Reorder') }}">
                                                    <i class="dripicons-refresh"></i>
                                                </a>--}}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <img src="{{ asset('images/empty-orders.svg') }}" alt="No orders" style="width: 120px; opacity: 0.6">
                                <p class="mt-3 text-muted">{{ __('No recent orders found.') }}</p>
                                <a href="{{ route('orders.create') }}" class="btn btn-primary mt-2">
                                    <i class="dripicons-cart me-1"></i> {{ __('Place Your First Order') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Order History with Tabs -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card summary-card">
                    <div class="card-header bg-light">
                        <ul class="nav nav-tabs card-header-tabs" id="orderHistoryTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="all-orders-tab" data-bs-toggle="tab" href="#all-orders" role="tab" aria-controls="all-orders" aria-selected="true">
                                    <i class="dripicons-document-edit me-1"></i> {{ __('All Orders') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pending-tab" data-bs-toggle="tab" href="#pending" role="tab" aria-controls="pending" aria-selected="false">
                                    <i class="dripicons-time-reverse me-1"></i> {{ __('Pending') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="completed-tab" data-bs-toggle="tab" href="#completed" role="tab" aria-controls="completed" aria-selected="false">
                                    <i class="dripicons-checkmark me-1"></i> {{ __('Completed') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="orderHistoryTabContent">
                            <div class="tab-pane fade show active" id="all-orders" role="tabpanel" aria-labelledby="all-orders-tab">
                                @if($allOrders->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                            <tr>
                                                <th>{{ __('Order #') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Total') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Actions') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($allOrders as $order)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $order->OrderNumber }}</strong>
                                                    </td>
                                                    <td>{{ Carbon\Carbon::parse($order->OrderDate)->format('d M, Y') }}</td>
                                                    <td>
                                                        <strong>R {{ number_format(($order->total/100), 2) }}</strong>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $statusClass = 'secondary';
                                                            if(isset($order->orderStatus)) {
                                                                switch(strtolower($order->orderStatus->name)) {
                                                                    case 'pending':
                                                                        $statusClass = 'warning';
                                                                        break;
                                                                    case 'processing':
                                                                        $statusClass = 'info';
                                                                        break;
                                                                    case 'completed':
                                                                        $statusClass = 'success';
                                                                        break;
                                                                    case 'cancelled':
                                                                        $statusClass = 'danger';
                                                                        break;
                                                                }
                                                            }
                                                        @endphp
                                                        <span class="badge badge-{{ $statusClass }} status-badge">
                                                                {{ $order->orderStatus->name ?? 'Unknown' }}
                                                            </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('orders.show', $order->id) }}" class="action-btn text-info" data-bs-toggle="tooltip" title="{{ __('View Order') }}">
                                                            <i class="dripicons-preview"></i>
                                                        </a>
                                                        {{--<a href="{{ route('orders.track', $order->id) }}" class="action-btn text-success" data-bs-toggle="tooltip" title="{{ __('Track Order') }}">
                                                            <i class="dripicons-location"></i>
                                                        </a>
                                                        <a href="{{ route('orders.reorder', $order->id) }}" class="action-btn text-primary" data-bs-toggle="tooltip" title="{{ __('Reorder') }}">
                                                            <i class="dripicons-refresh"></i>
                                                        </a>--}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3">
                                        {{ $allOrders->links() }}
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <img src="{{ asset('images/empty-orders.svg') }}" alt="No orders" style="width: 120px; opacity: 0.6">
                                        <p class="mt-3 text-muted">{{ __('No order history found.') }}</p>
                                        <a href="{{ route('orders.create') }}" class="btn btn-primary mt-2">
                                            <i class="dripicons-cart me-1"></i> {{ __('Place Your First Order') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                                <div class="p-3">
                                    <!-- Pending orders content - you would implement a filtered view here -->
                                    <p class="text-center text-muted">{{ __('Filtering functionality would be implemented here') }}</p>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                                <div class="p-3">
                                    <!-- Completed orders content - you would implement a filtered view here -->
                                    <p class="text-center text-muted">{{ __('Filtering functionality would be implemented here') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize tooltips
                $('[data-bs-toggle="tooltip"]').tooltip();

                // Initialize tabs
                $('#orderHistoryTab a').on('click', function (e) {
                    e.preventDefault();
                    $(this).tab('show');
                });
            });
        </script>
    @endpush
@endsection
