<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Fulfillment - Order #{{ $order->OrderNumber }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: #343a40;;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .alert strong {
            display: block;
            margin-bottom: 5px;
        }
        .info-section {
            margin-bottom: 25px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 6px;
        }
        .info-section h2 {
            margin-top: 0;
            font-size: 18px;
            color: #667eea;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: 600;
            color: #666;
        }
        .location-section {
            margin-bottom: 30px;
            border: 2px solid #667eea;
            border-radius: 6px;
            overflow: hidden;
        }
        .location-header {
            background-color: #667eea;
            color: white;
            padding: 15px;
            font-weight: 600;
            font-size: 16px;
        }
        .location-header .badge {
            background-color: rgba(255,255,255,0.2);
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            margin-left: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }
        th {
            background-color: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .total-row {
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 16px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #dee2e6;
        }
        .text-right {
            text-align: right;
        }
        .text-muted {
            color: #6c757d;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <h1>📦 New Order Fulfillment Required</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">Order #{{ $order->OrderNumber }}</p>
    </div>

    <div class="content">
        @if($order->OrderStatusID == 5)
            <div class="alert">
                <strong>⚠️ Credit Hold Notice</strong>
                This order is on credit hold and requires approval before fulfillment.
                @if($order->Comments)
                    <br><em>{{ $order->Comments }}</em>
                @endif
            </div>
        @endif

        <div class="info-section">
            <h2>Order Information</h2>
            <div class="info-grid">
                <span class="info-label">Order Number:</span>
                <span>#{{ $order->OrderNumber }}</span>

                <span class="info-label">Order Date:</span>
                <span>{{ $order->OrderDate->format('d M Y H:i') }}</span>

                <span class="info-label">Customer:</span>
                <span>{{ $order->customer->acc_name ?? 'N/A' }} ({{ $order->CustomerID }})</span>

                @if($order->CustomerPurchaseOrderNumber)
                    <span class="info-label">Customer PO:</span>
                    <span>{{ $order->CustomerPurchaseOrderNumber }}</span>
                @endif

                <span class="info-label">Delivery Method:</span>
                <span>{{ ucfirst($order->delivery_method) }}</span>

                @if($order->preferred_delivery_date)
                    <span class="info-label">Preferred Date:</span>
                    <span>{{ \Carbon\Carbon::parse($order->preferred_delivery_date)->format('d M Y') }}</span>
                @endif
            </div>

            @if($order->Comments)
                <div style="margin-top: 15px;">
                    <span class="info-label">Customer Notes:</span>
                    <div style="margin-top: 5px; padding: 10px; background: white; border-left: 3px solid #667eea; border-radius: 3px;">
                        {{ $order->Comments }}
                    </div>
                </div>
            @endif
        </div>

        @if($order->delivery_method === 'delivery' && $order->customer)
            <div class="info-section">
                <h2>Delivery Address</h2>
                <address style="margin: 0; font-style: normal;">
                    @if($order->customer->DeliveryAddressLine1)
                        {{ $order->customer->DeliveryAddressLine1 }}<br>
                    @endif
                    @if($order->customer->DeliveryAddressLine2)
                        {{ $order->customer->DeliveryAddressLine2 }}<br>
                    @endif
                    @if($order->customer->DeliveryCity)
                        {{ $order->customer->DeliveryCity }}
                    @endif
                    @if($order->customer->DeliveryPostalCode)
                        {{ $order->customer->DeliveryPostalCode }}
                    @endif
                </address>
            </div>
        @endif

        <h2 style="margin-top: 30px; color: #667eea;">Items to Fulfill</h2>

        @foreach($itemsByLocation as $locationCode => $items)
            @php
                $location = \App\Models\Location::where('LocationCode', $locationCode)->first();
            @endphp

            <div class="location-section">
                <div class="location-header">
                    📍 {{ $location ? $location->LocationName : $locationCode }}
                    <span class="badge">{{ $items->count() }} {{ Str::plural('item', $items->count()) }}</span>
                    @if($location && $location->fulfillment_email)
                        <span style="float: right; font-size: 13px; opacity: 0.9;">
                                ✉️ {{ $location->fulfillment_email }}
                            </span>
                    @endif
                </div>

                <table>
                    <thead>
                    <tr>
                        <th>Stock Code</th>
                        <th>Product Name</th>
                        <th class="text-right">Quantity</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $locationTotal = 0; @endphp
                    @foreach($items as $item)
                        @php
                            $lineTotal = ($item->UnitPrice / 100) * $item->Quantity;
                            $locationTotal += $lineTotal;
                        @endphp
                        <tr>
                            <td><strong>{{ $item->StockItem }}</strong></td>
                            <td>{{ $item->product->StockItemName ?? 'N/A' }}</td>
                            <td class="text-right">{{ number_format($item->Quantity, 2) }}</td>
                            <td class="text-right">R {{ number_format($item->UnitPrice / 100, 2) }}</td>
                            <td class="text-right">R {{ number_format($lineTotal, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="4" class="text-right">Location Subtotal:</td>
                        <td class="text-right">R {{ number_format($locationTotal, 2) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        @endforeach

        <div class="info-section" style="margin-top: 30px;">
            <h2>Order Summary</h2>
            <div class="info-grid">
                <span class="info-label">Subtotal (excl VAT):</span>
                <span>R {{ number_format($order->sub_total / 100, 2) }}</span>

                <span class="info-label">VAT (15%):</span>
                <span>R {{ number_format(($order->total - $order->sub_total) / 100, 2) }}</span>

                <span class="info-label" style="font-size: 18px;">Total (incl VAT):</span>
                <span style="font-size: 18px; font-weight: 700; color: #667eea;">
                        R {{ number_format($order->total / 100, 2) }}
                    </span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p style="margin: 0 0 10px 0;">This is an automated fulfillment notification.</p>
        <p style="margin: 0; font-size: 12px;" class="text-muted">
            Generated {{ now()->format('d M Y H:i') }}
        </p>
    </div>
</div>
</body>
</html>
