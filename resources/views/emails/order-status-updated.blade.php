<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #343a40; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .order-info { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #667eea; }
        .status-badge { display: inline-block; padding: 5px 15px; border-radius: 20px; font-weight: bold; }
        .status-ready { background: #28a745; color: white; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white !important; text-decoration: none; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Order Status Update</h1>
    </div>

    <div class="content">
        <p>Hello {{ $order->customer->CustomerName }},</p>

        @if($order->OrderStatusID == 6)
            {{-- Ready for Collection --}}
            <p><strong>Great news!</strong> Your order is ready for collection.</p>
        @elseif($order->OrderStatusID == 7)
            {{-- Ready for Delivery --}}
            <p><strong>Great news!</strong> Your order is out for delivery.</p>
        @elseif($order->OrderStatusID == 8)
            {{-- Completed --}}
            <p>Your order has been completed. Thank you for your business!</p>
        @elseif($order->OrderStatusID == 4)
            {{-- Invoiced --}}
            <p>Your order has been invoiced and will be processed shortly.</p>
        @else
            <p>Your order status has been updated.</p>
        @endif

        <div class="order-info">
            <p><strong>Order Number:</strong> #{{ $order->OrderNumber }}</p>
            <p><strong>Order Date:</strong> {{ $order->OrderDate }}</p>
            <p><strong>Status:</strong>
                <span class="status-badge status-ready">
                    {{ $order->orderstatus->name ?? 'Unknown' }}
                </span>
            </p>
        </div>

        @if($order->OrderStatusID == 6)
            {{-- Show collection details if status is "Ready for Collection" --}}
            <div style="background: #fff3cd; padding: 15px; margin: 15px 0; border-left: 4px solid #ffc107;">
                <h3 style="margin-top: 0;">Collection Details</h3>
                @if($order->company->hasAddress('collection'))
                    <p><strong>Collection Point:</strong> {{ $order->company->collection_address->name }}</p>
                    <p>{{ $order->company->collection_address->address_1 }}<br>
                        {{ $order->company->collection_address->city }}, {{ $order->company->collection_address->zip }}</p>
                    <p><strong>Phone:</strong> {{ $order->company->collection_address->phone }}</p>
                @endif

                <p><strong>Collection Hours:</strong></p>
                <ul style="margin: 5px 0;">
                    <li>{{ $order->company->getSetting('ecommerce_collection_hours_weekday', 'Monday - Friday: 8:00 AM - 4:30 PM') }}</li>
                    <li>{{ $order->company->getSetting('ecommerce_collection_hours_saturday', 'Saturday: 8:00 AM - 12:00 PM') }}</li>
                    <li>{{ $order->company->getSetting('ecommerce_collection_hours_sunday', 'Sunday: Closed') }}</li>
                </ul>

                <p><em>Please bring your order confirmation when collecting.</em></p>
            </div>
        @endif

        @if($order->OrderStatusID == 7)
            {{-- Show delivery info if status is "Ready for Delivery" --}}
            <div style="background: #e7f3ff; padding: 15px; margin: 15px 0; border-left: 4px solid #2196F3;">
                <h3 style="margin-top: 0;">Delivery Information</h3>
                <p>Your order is on its way and should arrive soon!</p>
                @if($order->delivery_address)
                    <p><strong>Delivering to:</strong></p>
                    <p>
                        {{ $order->delivery_address->address_1 }}<br>
                        @if($order->delivery_address->address_2)
                            {{ $order->delivery_address->address_2 }}<br>
                        @endif
                        {{ $order->delivery_address->city }}, {{ $order->delivery_address->zip }}
                    </p>
                @endif
                <p><em>Please ensure someone is available to receive the delivery.</em></p>
            </div>
        @endif

        @if($notes)
            <div style="background: #e7f3ff; padding: 15px; margin: 15px 0; border-left: 4px solid #2196F3;">
                <p><strong>Additional Information:</strong></p>
                <p>{{ $notes }}</p>
            </div>
        @endif

        <div style="text-align: center; margin: 20px 0;">
            <a href="{{ route('shop.account.orders.show', $order->id) }}" class="button">
                View Order Details
            </a>
        </div>

        <p>If you have any questions, please don't hesitate to contact us.</p>
    </div>

    <div class="footer">
        <p>{{ config('app.name') }}</p>
        <p>{{ $order->company->billing->phone ?? '' }} | {{ config('app.support_email') }}</p>
    </div>
</div>
</body>
</html>
