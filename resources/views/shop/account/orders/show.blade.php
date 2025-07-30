@extends('shop.layouts.app')

@section('title', 'Order #' . $order->OrderNumber)

@section('content')
    <div class="container-fluid py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('shop.account.index') }}">Account</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shop.account.orders.index') }}">Orders</a></li>
                <li class="breadcrumb-item active">Order #{{ $order->OrderNumber }}</li>
            </ol>
        </nav>

        <!-- Order Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-3">
                    <h1 class="h3 mb-0 me-3">Order #{{ $order->OrderNumber }}</h1>
                    <span class="badge {{ \App\Http\Controllers\Shop\OrderController::getStatusBadgeClass($order->OrderStatusID) }} fs-6">
                    {{ \App\Http\Controllers\Shop\OrderController::getStatusName($order->OrderStatusID) }}
                </span>
                </div>

                <div class="row g-3 text-muted">
                    <div class="col-sm-6">
                        <i class="fas fa-calendar me-2"></i>
                        <strong>Order Date:</strong> {{ $order->OrderDate->format('F d, Y g:i A') }}
                    </div>
                    @if($order->CustomerPurchaseOrderNumber)
                        <div class="col-sm-6">
                            <i class="fas fa-file-alt me-2"></i>
                            <strong>PO Number:</strong> {{ $order->CustomerPurchaseOrderNumber }}
                        </div>
                    @endif
                    @if($order->salesperson)
                        <div class="col-sm-6">
                            <i class="fas fa-user-tie me-2"></i>
                            <strong>Sales Rep:</strong> {{ $order->salesperson->PreferredName }}
                        </div>
                    @endif
                    <div class="col-sm-6">
                        <i class="fas fa-truck me-2"></i>
                        <strong>Delivery:</strong> {{ ucfirst($order->delivery_method) }}
                    </div>
                </div>
            </div>

            <div class="col-md-4 text-md-end">
                <div class="btn-group mb-2" role="group">
                    <a href="{{ route('shop.account.orders.reorder', $order) }}" class="btn btn-outline-primary">
                        <i class="fas fa-redo"></i> Reorder
                    </a>
                    <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="fas fa-print"></i> Print
                    </button>
                    @if($order->OrderStatusID == 1)
                        <button type="button"
                                class="btn btn-outline-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#cancelOrderModal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    @endif
                </div>

                <div class="text-end">
                    <div class="h4 text-primary mb-0">{{ \App\Helpers\PricingHelper::formatPrice($order->total / 100) }}</div>
                    <small class="text-muted">Total Amount</small>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Order Items -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">Order Items ({{ $order->items->count() }})</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product && $item->product->image)
                                                    <img src="{{ $item->product->image }}"
                                                         alt="{{ $item->product->StockItemName }}"
                                                         class="rounded me-3"
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-box text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-1">{{ $item->product->StockItemName ?? 'Product Unavailable' }}</h6>
                                                    @if($item->product && $item->product->description)
                                                        <small class="text-muted">{{ Str::limit($item->product->description, 60) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <code class="bg-light px-2 py-1 rounded">{{ $item->product->StockCode ?? 'N/A' }}</code>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $item->Quantity }}</span>
                                        </td>
                                        <td class="text-end">
                                            {{ \App\Helpers\PricingHelper::formatPrice($item->UnitPrice / 100) }}
                                        </td>
                                        <td class="text-end">
                                            <strong>{{ \App\Helpers\PricingHelper::formatPrice($item->total / 100) }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary & Details -->
            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0">Order Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>{{ \App\Helpers\PricingHelper::formatPrice($order->sub_total / 100) }}</span>
                        </div>
                        @if($order->total != $order->sub_total)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax/Fees:</span>
                                <span>{{ \App\Helpers\PricingHelper::formatPrice(($order->total - $order->sub_total) / 100) }}</span>
                            </div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong class="text-primary">{{ \App\Helpers\PricingHelper::formatPrice($order->total / 100) }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Delivery Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0">Delivery Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Method:</strong><br>
                            <span class="badge bg-info">{{ ucfirst($order->delivery_method) }}</span>
                        </div>

                        @if($order->preferred_delivery_date)
                            <div class="mb-3">
                                <strong>Preferred Date:</strong><br>
                                {{ \Carbon\Carbon::parse($order->preferred_delivery_date)->format('F d, Y') }}
                            </div>
                        @endif

                        <div class="mb-0">
                            <strong>Address:</strong><br>
                            {{ $order->customer->DeliveryAddressLine1 }}<br>
                            @if($order->customer->DeliveryAddressLine2)
                                {{ $order->customer->DeliveryAddressLine2 }}<br>
                            @endif
                            {{ $order->customer->DeliveryCity }}, {{ $order->customer->DeliveryState }} {{ $order->customer->DeliveryPostCode }}
                        </div>
                    </div>
                </div>

                <!-- Order Status Timeline -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0">Order Status</h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($statusHistory as $status)
                                <div class="timeline-item {{ $loop->first ? 'active' : '' }}">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">{{ $status['status'] }}</h6>
                                        <p class="text-muted small mb-1">{{ $status['date']->format('M d, Y g:i A') }}</p>
                                        @if($status['notes'])
                                            <p class="small mb-0">{{ $status['notes'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if($order->Comments)
                    <!-- Order Comments -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0">Order Notes</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $order->Comments }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Cancel Order Modal -->
    @if($order->OrderStatusID == 1)
        <div class="modal fade" id="cancelOrderModal" tabindex="-1">
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
                                <label for="cancel_reason" class="form-label">
                                    Reason for cancellation <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="cancel_reason" name="cancel_reason" required>
                                    <option value="">Select a reason...</option>
                                    <option value="Changed mind">Changed mind</option>
                                    <option value="Found better price">Found better price</option>
                                    <option value="Ordered by mistake">Ordered by mistake</option>
                                    <option value="No longer needed">No longer needed</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="order-summary bg-light p-3 rounded">
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

@endsection

@push('styles')
    <style>
        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0.75rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-marker {
            position: absolute;
            left: -1.75rem;
            top: 0.25rem;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #6c757d;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #dee2e6;
        }

        .timeline-item.active .timeline-marker {
            background: #0d6efd;
            box-shadow: 0 0 0 2px #0d6efd;
        }

        .timeline-title {
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        @media print {
            .btn, .breadcrumb, .modal {
                display: none !important;
            }

            .card {
                border: 1px solid #000 !important;
                box-shadow: none !important;
            }

            .timeline::before {
                background: #000 !important;
            }

            .timeline-marker {
                background: #000 !important;
                border-color: #fff !important;
                box-shadow: 0 0 0 2px #000 !important;
            }
        }
    </style>
@endpush
