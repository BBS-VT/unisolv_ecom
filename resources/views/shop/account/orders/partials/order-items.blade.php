<div class="order-items">
    @if($order->items->count() > 0)
        @foreach($order->items as $item)
            <div class="order-item d-flex align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                <!-- Product Image -->
                <div class="flex-shrink-0 me-3">
                    @if($item->product && $item->product->image)
                        <img src="{{ $item->product->image }}"
                             alt="{{ $item->product->StockItemName }}"
                             class="rounded"
                             style="width: 50px; height: 50px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                             style="width: 50px; height: 50px;">
                            <i class="fas fa-box text-muted"></i>
                        </div>
                    @endif
                </div>

                <!-- Product Details -->
                <div class="flex-grow-1">
                    <h6 class="mb-1">{{ $item->product->StcckItemName ?? 'Product Unavailable' }}</h6>
                    <div class="row g-2 small text-muted">
                        <div class="col-auto">
                            <strong>SKU:</strong> {{ $item->product->StockCode ?? 'N/A' }}
                        </div>
                        <div class="col-auto">
                            <strong>Qty:</strong> {{ $item->Quantity }}
                        </div>
                        <div class="col-auto">
                            <strong>Unit Price:</strong> {{ \App\Helpers\PricingHelper::formatPrice($item->UnitPrice / 100) }}
                        </div>
                    </div>
                </div>

                <!-- Item Total -->
                <div class="text-end">
                    <div class="fw-bold">
                        {{ \App\Helpers\PricingHelper::formatPrice($item->total / 100) }}
                    </div>
                    @if($item->product && !$item->product->IsActive)
                        <small class="text-muted">Discontinued</small>
                    @endif
                </div>
            </div>
        @endforeach

        <!-- Order Totals -->
        <div class="order-totals mt-3 pt-3 border-top">
            <div class="row">
                <div class="col-6 offset-6">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Subtotal:</span>
                        <span>{{ \App\Helpers\PricingHelper::formatPrice($order->sub_total / 100) }}</span>
                    </div>
                    @if($order->total != $order->sub_total)
                        <div class="d-flex justify-content-between mb-1 text-muted small">
                            <span>Tax/Fees:</span>
                            <span>{{ \App\Helpers\PricingHelper::formatPrice(($order->total - $order->sub_total) / 100) }}</span>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between border-top pt-2">
                        <strong>Total:</strong>
                        <strong class="text-primary">{{ \App\Helpers\PricingHelper::formatPrice($order->total / 100) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center text-muted py-4">
            <i class="fas fa-box-open fa-2x mb-2"></i>
            <p class="mb-0">No items found in this order.</p>
        </div>
    @endif
</div>
