<div class="order-card p-3">
    <div class="row align-items-start">
        <div class="col-8">
            <div class="mb-2">
                <h6 class="mb-1">
                    <a href="{{ route('shop.account.orders.show', $order) }}" class="text-decoration-none">
                        Order #{{ $order->OrderNumber }}
                    </a>
                </h6>
                @if($order->CustomerPurchaseOrderNumber)
                    <small class="text-muted d-block">PO: {{ $order->CustomerPurchaseOrderNumber }}</small>
                @endif
                <small class="text-muted">{{ $order->OrderDate->format('M d, Y g:i A') }}</small>
            </div>

            <div class="mb-2">
                <span class="badge {{ \App\Http\Controllers\Shop\OrderController::getStatusBadgeClass($order->OrderStatusID) }} me-2">
                    {{ \App\Http\Controllers\Shop\OrderController::getStatusName($order->OrderStatusID) }}
                </span>
                <span class="badge bg-light text-dark">{{ ucfirst($order->delivery_method) }}</span>
            </div>

            <div class="mb-0">
                <strong class="text-primary">{{ \App\Helpers\PricingHelper::formatPrice($order->total / 100) }}</strong>
                <small class="text-muted ms-2">{{ $order->items->count() }} items</small>
            </div>
        </div>

        <div class="col-4 text-end">
            <div class="btn-group-vertical btn-group-sm w-100">
                <a href="{{ route('shop.account.orders.show', $order) }}"
                   class="btn btn-outline-primary btn-sm mb-1">
                    <i class="fas fa-eye me-1"></i> View
                </a>
                <a href="{{ route('shop.account.orders.reorder', $order) }}"
                   class="btn btn-outline-secondary btn-sm mb-1">
                    <i class="fas fa-redo me-1"></i> Reorder
                </a>
                @if($order->OrderStatusID == 1)
                    <button type="button"
                            class="btn btn-outline-danger btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#cancelModal{{ $order->id }}">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if($order->preferred_delivery_date)
        <div class="row mt-2">
            <div class="col-12">
                <small class="text-muted">
                    <i class="fas fa-calendar me-1"></i>
                    Preferred delivery: {{ \Carbon\Carbon::parse($order->preferred_delivery_date)->format('M d, Y') }}
                </small>
            </div>
        </div>
    @endif

    @if($order->Comments)
        <div class="row mt-2">
            <div class="col-12">
                <small class="text-muted">
                    <i class="fas fa-comment me-1"></i>
                    {{ Str::limit($order->Comments, 50) }}
                </small>
            </div>
        </div>
    @endif
</div>
