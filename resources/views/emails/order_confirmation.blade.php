<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            overflow: hidden;
        }
        .email-header {
            background-color: #007bff;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 20px;
        }
        .email-body h2 {
            color: #007bff;
        }
        .order-details {
            margin-top: 20px;
            border-collapse: collapse;
            width: 100%;
        }
        .order-details th, .order-details td {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
        }
        .order-details th {
            background-color: #f1f3f5;
        }
        .email-footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #6c757d;
        }
        .email-footer a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    <!-- Header -->
    <div class="email-header">
        <h1>Order Confirmation</h1>
        <p>Order #{{ $order->id }}</p>
    </div>

    <!-- Body -->
    <div class="email-body">
        <h2>Thank You for Your Order, {{ $order->customer->CustomerName }}!</h2>
        <p>Your order has been placed successfully:</p>

        <table class="order-details">
            <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product->StockItemName }}</td>
                    <td>{{ $item->Quantity }}</td>
                    <td>{{ number_format(($item->UnitPrice / 100), 2)  }}</td>
                    <td>{{ number_format($item->Quantity * ($item->UnitPrice / 100), 2) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th colspan="3" style="text-align: right;">Subtotal:</th>
                <td>{{ number_format(($order->sub_total / 100), 2) }}</td>
            </tr>
            <tr>
                <th colspan="3" style="text-align: right;">Tax:</th>
                {{--<td>{{ number_format($order->tax, 2) }}</td>--}}
                <td>{{ number_format(($order->total - ($order->total / 1.15)), 2) }}</td>
            </tr>
            <tr>
                <th colspan="3" style="text-align: right;">Total:</th>
                <td><strong>{{ number_format(($order->total / 100), 2) }}</strong></td>
            </tr>
            </tfoot>
        </table>

        <p>If you have any questions about your order, please don't hesitate to <a href="mailto:support@example.com">contact us</a>.</p>
    </div>

    <!-- Footer -->
    <div class="email-footer">
        <p>&copy; {{ date('Y') }} All rights reserved.</p>
        <p><a href="https://yourwebsite.com">Visit our website</a></p>
    </div>
</div>
</body>
</html>
