<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 8px;
            overflow: hidden;
        }
        .email-header {
            background-color: #343a40;
            color: #ffffff;
            text-align: center;
            padding: 15px;
        }
        .email-header h1 {
            margin: 0;
            font-size: 20px;
        }
        .urgent-tag {
            display: inline-block;
            background-color: #dc3545;
            color: #ffffff;
            font-size: 12px;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 4px;
            margin-top: 10px;
        }
        .email-body {
            padding: 20px;
        }
        .order-details {
            margin-top: 20px;
            border-collapse: collapse;
            width: 100%;
        }
        .order-details th, .order-details td {
            border: 1px solid #dddddd;
            padding: 10px;
            text-align: left;
        }
        .order-details th {
            background-color: #f8f9fa;
        }
        .notes-section {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dddddd;
            border-radius: 5px;
        }
        .actions {
            margin-top: 20px;
            text-align: center;
        }
        .actions a {
            display: inline-block;
            text-decoration: none;
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 14px;
        }
        .actions a:hover {
            background-color: #0056b3;
        }
        .email-footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    <!-- Header -->
    <div class="email-header">
        <h1>New Order Created</h1>
        <p>Order #{{ $order->id }}</p>
        @if ($order->is_urgent)
            <div class="urgent-tag">URGENT</div>
        @endif
    </div>

    <!-- Body -->
    <div class="email-body">
        <h2>Order Details</h2>
        <p>A new order has been created and requires your attention. Below are the items and quantities to prepare:</p>

        <h3>Order Items</h3>
        <table class="order-details">
            <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product->StockItemName }}</td>
                    <td>{{ $item->Quantity }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Notes Section -->
        @if (!empty($order->InternalComments))
            <div class="notes-section">
                <h4>Special Instructions</h4>
                <p>{{ $order->InternalComments }}</p>
            </div>
        @endif

        <div class="actions">
            <a href="{{ url('/orders/' . $order->id) }}">View Order</a>
        </div>
    </div>

    <!-- Footer -->
    <div class="email-footer">
        <p>&copy; {{ date('Y') }} Unisolv. All rights reserved.</p>
    </div>
</div>
</body>
</html>
