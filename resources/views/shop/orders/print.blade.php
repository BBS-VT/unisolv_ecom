<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order #{{ $order->OrderNumber }} - {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            padding: 20mm;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #000;
        }

        .header h1 {
            font-size: 24pt;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10pt;
            color: #666;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ccc;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-block h3 {
            font-size: 11pt;
            margin-bottom: 8px;
            color: #333;
        }

        .info-block p {
            font-size: 10pt;
            margin-bottom: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th {
            background-color: #f0f0f0;
            padding: 8px;
            text-align: left;
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            font-size: 10pt;
        }

        table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10pt;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .totals-table {
            width: 50%;
            margin-left: auto;
            margin-top: 20px;
        }

        .totals-table td {
            border: none;
            padding: 5px 10px;
        }

        .totals-table .total-row {
            font-weight: bold;
            font-size: 12pt;
            border-top: 2px solid #000;
        }

        .notes {
            background-color: #f9f9f9;
            padding: 15px;
            border-left: 3px solid #666;
            margin: 20px 0;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }

        @media print {
            body { padding: 10mm; }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body>
{{-- Header --}}
<div class="header">
    <h1>{{ config('app.name') }}</h1>
    <p>{{ $currentCompany->name }}</p>
    <p>
        Tel: {{ $currentCompany->billing->phone ?? 'N/A' }} |
        Email: {{ $currentCompany->billing->email ?? 'N/A' }} |
        Web: {{ config('app.url') }}
    </p>
    <h2 style="margin-top: 15px;">Order Confirmation</h2>
</div>

{{-- Order Information --}}
<div class="section">
    <div class="section-title">Order Details</div>
    <div class="info-grid">
        <div class="info-block">
            <h3>Order Information</h3>
            <p><strong>Order Number:</strong> #{{ $order->OrderNumber }}</p>
            <p><strong>Order Date:</strong> {{ $order->OrderDate }}</p>
            <p><strong>Status:</strong> New</p>
            @if($order->CustomerPurchaseOrderNumber)
                <p><strong>Your PO Number:</strong> {{ $order->CustomerPurchaseOrderNumber }}</p>
            @endif
        </div>
        <div class="info-block">
            <h3>Customer Information</h3>
            <p><strong>Customer:</strong> {{ $order->customer->CustomerName }}</p>
            <p><strong>Account:</strong> {{ $order->customer->acc_code }}</p>
            <p><strong>Sales Rep:</strong> {{ $order->salesperson->PreferredName }}</p>
            <p><strong>Email:</strong> {{ $order->customer->GeneralEmailAddress }}</p>
        </div>
    </div>
</div>

{{-- Order Notes --}}
@if($order->Comments)
    <div class="notes">
        <strong>Order Notes:</strong><br>
        {{ $order->Comments }}
    </div>
@endif

{{-- Order Items --}}
<div class="section">
    <div class="section-title">Order Items</div>
    <table>
        <thead>
        <tr>
            <th>Item</th>
            <th>SKU</th>
            <th class="text-center">Qty</th>
            <th class="text-right">Unit Price</th>
            <th class="text-right">Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->items as $item)
            @php
                $product = \App\Models\Product::where('StockCode', $item->StockItem)->first();
            @endphp
            <tr>
                <td>
                    <strong>{{ $product ? $product->StockItemName : $item->StockItem }}</strong>
                    @if($product && $product->MarketingComments)
                        <br><small style="color: #666;">{{ Str::limit($product->MarketingComments, 60) }}</small>
                    @endif
                </td>
                <td>{{ $item->StockItem }}</td>
                <td class="text-center">{{ number_format($item->Quantity) }}</td>
                <td class="text-right">{{ \App\Helpers\PricingHelper::formatPrice($item->UnitPrice / 100) }}</td>
                <td class="text-right">{{ \App\Helpers\PricingHelper::formatPrice($item->total / 100) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <table class="totals-table">
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">{{ \App\Helpers\PricingHelper::formatPrice($order->sub_total / 100) }}</td>
        </tr>
        <tr>
            <td>VAT (15%):</td>
            <td class="text-right">{{ \App\Helpers\PricingHelper::formatPrice(($order->total / 100) - ($order->sub_total / 100)) }}</td>
        </tr>
        <tr class="total-row">
            <td>Total:</td>
            <td class="text-right">{{ \App\Helpers\PricingHelper::formatPrice($order->total / 100) }}</td>
        </tr>
    </table>
</div>

{{-- What Happens Next --}}
<div class="section">
    <div class="section-title">What Happens Next?</div>
    <table style="border: none;">
        <tr>
            <td style="border: none; width: 50%; vertical-align: top; padding-right: 20px;">
                <p><strong>1. Order Processing</strong></p>
                <p style="font-size: 9pt; color: #666;">
                    Your order will be reviewed and processed by our team within
                    {{ $currentCompany->getSetting('ecommerce_processing_time', '1-2 business hours') }}.
                </p>
            </td>
            <td style="border: none; width: 50%; vertical-align: top;">
                <p><strong>2. Order Confirmation</strong></p>
                <p style="font-size: 9pt; color: #666;">
                    You'll receive an email confirmation with order details and expected delivery timeframe.
                </p>
            </td>
        </tr>
        <tr>
            <td style="border: none; padding-top: 15px; vertical-align: top; padding-right: 20px;">
                <p><strong>3. Preparation & Dispatch</strong></p>
                <p style="font-size: 9pt; color: #666;">
                    Your order will be prepared and dispatched according to your delivery requirements.
                </p>
            </td>
            <td style="border: none; padding-top: 15px; vertical-align: top;">
                <p><strong>4. Delivery & Invoice</strong></p>
                <p style="font-size: 9pt; color: #666;">
                    You'll receive your order along with the invoice according to your account terms.
                </p>
            </td>
        </tr>
    </table>
</div>

{{-- Footer --}}
<div class="footer">
    <p><strong>Need Help?</strong></p>
    <p>
        Phone: {{ $currentCompany->billing->phone ?? 'N/A' }} |
        Email: {{ config('app.support_email', 'orders@company.com') }} |
        Hours: Mon-Fri: 8:00 - 17:00
    </p>
    <p style="margin-top: 10px;">Thank you for your business!</p>
    <p style="font-size: 8pt; margin-top: 10px;">Printed: {{ now()->format('d M Y, H:i') }}</p>
</div>

<script>
    // Auto-print when page loads
    window.onload = function() {
        window.print();
        // Close window after printing (optional)
        // window.onafterprint = function() { window.close(); }
    }
</script>
</body>
</html>
