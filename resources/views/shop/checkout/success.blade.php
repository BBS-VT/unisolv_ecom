@extends('shop.layouts.app')

@section('title', 'Order Confirmation')

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h1 class="text-success mb-3">Order Placed Successfully!</h1>
                    <p class="lead text-muted">
                        {{ __('messages.thanks_order')}}
                    </p>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-receipt me-2"></i>Order Details
                        </h5>
                    </div>

                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Order Information</h6>
                                <p class="mb-1"><strong>Order Number:</strong> #{{ $order->OrderNumber }}</p>
                                <p class="mb-1"><strong>Order Date:</strong> {{ $order->OrderDate->format('d M Y, H:i') }}</p>
                                <p class="mb-1"><strong>Status:</strong>
                                    <span class="badge bg-primary">New</span>
                                </p>
                                @if($order->CustomerPurchaseOrderNumber)
                                    <p class="mb-1"><strong>Your PO Number:</strong> {{ $order->CustomerPurchaseOrderNumber }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h6>Customer Information</h6>
                                <p class="mb-1"><strong>Customer:</strong> {{ $order->customer->CustomerName }}</p>
                                <p class="mb-1"><strong>Account:</strong> {{ $order->customer->acc_code }}</p>
                                <p class="mb-1"><strong>Sales Rep:</strong> {{ $order->salesperson->name }}</p>
                                <p class="mb-1"><strong>Email:</strong> {{ $order->customer->GeneralEmailAddress }}</p>
                            </div>
                        </div>


                        @if($order->Comments)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6>Order Notes</h6>
                                    <p class="text-muted">{{ $order->Comments }}</p>
                                </div>
                            </div>
                        @endif

                        <h6>Order Items</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>SKU</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order->items as $item)
                                    @php
                                        $product = \App\Models\Product::where('StockCode', $item->StockItem)->first();
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $product ? $product->StockItemName : $item->StockItem }}</div>
                                            @if($product && $product->MarketingComments)
                                                <small class="text-muted">{{ Str::limit($product->MarketingComments, 50) }}</small>
                                            @endif
                                        </td>
                                        <td><small class="text-muted">{{ $item->StockItem }}</small></td>
                                        <td class="text-center">{{ number_format($item->Quantity) }}</td>
                                        <td class="text-end">{{ \App\Helpers\PricingHelper::formatPrice($item->UnitPrice) }}</td>
                                        <td class="text-end">{{ \App\Helpers\PricingHelper::formatPrice($item->total) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Subtotal:</strong></td>
                                        <td class="text-end"><strong>{{ \App\Helpers\PricingHelper::formatPrice($order->sub_total) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>VAT (15%):</strong></td>
                                        <td class="text-end"><strong>{{ \App\Helpers\PricingHelper::formatPrice($order->total - $order->sub_total) }}</strong></td>
                                    </tr>
                                    <tr class="table-success">
                                        <td><strong>Total:</strong></td>
                                        <td class="text-end"><strong>{{ \App\Helpers\PricingHelper::formatPrice($order->total) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-list-check me-2"></i>What Happens Next?
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; flex-shrink: 0;">
                                        <span class="fw-bold">1</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Order Processing</h6>
                                        <p class="text-muted small mb-0">Your order will be reviewed and processed by our team within 1-2 business hours.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; flex-shrink: 0;">
                                        <span class="fw-bold">2</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Order Confirmation</h6>
                                        <p class="text-muted small mb-0">You'll receive an email confirmation with order details and expected delivery timeframe.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; flex-shrink: 0;">
                                        <span class="fw-bold">3</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Preparation & Dispatch</h6>
                                        <p class="text-muted small mb-0">Your order will be prepared and dispatched according to your delivery requirements.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; flex-shrink: 0;">
                                        <span class="fw-bold">4</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Delivery & Invoice</h6>
                                        <p class="text-muted small mb-0">You'll receive your order along with the invoice according to your account terms.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('shop.products.index') }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-bag me-2"></i>Continue Shopping
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('shop.account.orders.show', $order->id) }}" class="btn btn-primary w-100">
                            <i class="bi bi-eye me-2"></i>View Order Details
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body text-center">
                        <h6 class="mb-3">Need Help?</h6>
                        <p class="text-muted mb-3">
                            If you have any questions about your order, please don't hesitate to contact us.
                        </p>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <i class="bi bi-telephone me-2"></i>
                                <strong>Phone:</strong><br>
                                <small class="text-muted">{{ config('app.phone', '+27 11 123 4567') }}</small>
                            </div>
                            <div class="col-md-4 mb-2">
                                <i class="bi bi-envelope me-2"></i>
                                <strong>Email:</strong><br>
                                <small class="text-muted">{{ config('app.support_email', 'orders@company.com') }}</small>
                            </div>
                            <div class="col-md-4 mb-2">
                                <i class="bi bi-clock me-2"></i>
                                <strong>Hours:</strong><br>
                                <small class="text-muted">Mon-Fri: 8:00 - 17:00</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Print order functionality
            $('#print-order').on('click', function() {
                window.print();
            });

            // Track order success event (for analytics if needed)
            if (typeof gtag !== 'undefined') {
                gtag('event', 'purchase', {
                    'transaction_id': '{{ $order->OrderNumber }}',
                    'value': {{ $order->total }},
                    'currency': 'ZAR'
                });
            }
        });
    </script>
@endpush
