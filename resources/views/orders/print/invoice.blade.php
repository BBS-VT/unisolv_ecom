@extends('layouts.print')

@section('content')
    <div class="print-container">
        <div class="print-page">
            {{-- Print Actions (Screen Only) --}}
            <div class="print-actions no-print">
                <button onclick="window.print()" class="print-button">
                    <i class="mdi mdi-printer"></i> Print Invoice
                </button>
                <button onclick="window.close()" class="print-button" style="background: #718096;">
                    <i class="mdi mdi-close"></i> Close
                </button>
            </div>

            {{-- Header Section --}}
            <div class="print-header">
                <div class="company-info">
                    @if($currentCompany->logo)
                        <img src="{{ asset('storage/' . $currentCompany->logo) }}" alt="{{ $currentCompany->name }}" class="company-logo">
                    @endif
                    <h1 class="company-name">{{ $currentCompany->name }}</h1>
                    <div class="company-details">
                        @if($currentCompany->address)
                            <div>{{ $currentCompany->address }}</div>
                        @endif
                        @if($currentCompany->phone)
                            <div>Tel: {{ $currentCompany->phone }}</div>
                        @endif
                        @if($currentCompany->email)
                            <div>Email: {{ $currentCompany->email }}</div>
                        @endif
                        @if($currentCompany->vat_number)
                            <div>VAT No: {{ $currentCompany->vat_number }}</div>
                        @endif
                        @if($currentCompany->registration_number)
                            <div>Reg No: {{ $currentCompany->registration_number }}</div>
                        @endif
                    </div>
                </div>
                <div class="document-title">
                    <div class="doc-type">
                        @if($order->status === 'completed')
                            INVOICE
                        @else
                            ORDER CONFIRMATION
                        @endif
                    </div>
                    <div class="doc-number">Order #{{ $order->order_number }}</div>
                    <div class="doc-date">Date: {{ $order->OrderDate }}</div>
                    @if($order->status === 'completed' && $order->completed_at)
                        <div class="doc-date">Completed: {{ $order->completed_at }}</div>
                    @endif
                </div>
            </div>

            {{-- Customer and Order Information --}}
            <div class="info-section">
                {{-- Bill To --}}
                <div class="info-box highlighted">
                    <div class="info-box-title">Bill To</div>
                    <div class="info-box-content">
                        <div><strong>{{ $order->customer->CustomerName }}</strong></div>
                        @if($order->customer->company)
                            <div>{{ $order->customer->company }}</div>
                        @endif
                        @if($order->customer->address)
                            <div>{{ $order->customer->address }}</div>
                        @endif
                        @if($order->customer->city)
                            <div>{{ $order->customer->city }}, {{ $order->customer->postal_code }}</div>
                        @endif
                        @if($order->customer->phone)
                            <div>Tel: {{ $order->customer->phone }}</div>
                        @endif
                        @if($order->customer->email)
                            <div>Email: {{ $order->customer->email }}</div>
                        @endif
                        @if($order->customer->vat_number)
                            <div class="mt-2"><strong>VAT No:</strong> {{ $order->customer->vat_number }}</div>
                        @endif
                    </div>
                </div>

                {{-- Delivery/Collection Information --}}
                <div class="info-box">
                    <div class="info-box-title">
                        @if($order->delivery_method === 'delivery')
                            Delivery Details
                        @else
                            Collection Details
                        @endif
                    </div>
                    <div class="info-box-content">
                        @if($order->delivery_method === 'delivery')
                            @if($order->delivery_address)
                                <div><strong>Delivery Address:</strong></div>
                                <div>{{ $order->delivery_address }}</div>
                                @if($order->delivery_city)
                                    <div>{{ $order->delivery_city }}, {{ $order->delivery_postal_code }}</div>
                                @endif
                            @else
                                <div>Same as billing address</div>
                            @endif
                            @if($order->delivery_date)
                                <div class="mt-2"><strong>Expected:</strong> {{ \Carbon\Carbon::parse($order->delivery_date)->format('d M Y') }}</div>
                            @endif
                        @else
                            <div><strong>Collection Location:</strong></div>
                            <div>{{ $order->location->name }}</div>
                            @if($order->location->address)
                                <div>{{ $order->location->address }}</div>
                            @endif
                            @if($order->collection_date)
                                <div class="mt-2"><strong>Collection Date:</strong> {{ \Carbon\Carbon::parse($order->collection_date)->format('d M Y') }}</div>
                            @endif
                        @endif
                        @if($order->delivery_instructions)
                            <div class="mt-2"><strong>Instructions:</strong></div>
                            <div>{{ $order->delivery_instructions }}</div>
                        @endif
                    </div>
                </div>

                {{-- Order Details --}}
                <div class="info-box">
                    <div class="info-box-title">Order Details</div>
                    <div class="info-box-content">
                        <div><strong>Order Date:</strong> {{ $order->created_at->format('d M Y H:i') }}</div>
                        @if($order->customer_po_reference)
                            <div><strong>Your Reference:</strong> {{ $order->customer_po_reference }}</div>
                        @endif
                        <div><strong>Payment Terms:</strong> {{ $order->payment_terms ?? 'Net 30 days' }}</div>
                        @if($order->user)
                            <div><strong>Sales Rep:</strong> {{ $order->user->name }}</div>
                        @endif
                        <div class="mt-2">
                        <span class="status-badge {{ strtolower($order->status) }}">
                            {{ ucfirst($order->status) }}
                        </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order Items --}}
            <div class="items-section">
                <h2 class="section-title">Order Items</h2>

                <table class="items-table">
                    <thead>
                    <tr>
                        <th style="width: 10%;">Item Code</th>
                        <th style="width: 40%;">Description</th>
                        <th style="width: 15%;" class="text-center">Location</th>
                        <th style="width: 10%;" class="text-center">Qty</th>
                        <th style="width: 12%;" class="text-right">Unit Price</th>
                        <th style="width: 13%;" class="text-right">Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div class="product-sku">{{ $item->product->sku }}</div>
                                @if($item->product->barcode)
                                    <div class="product-barcode">{{ $item->product->barcode }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="product-name">{{ $item->product->name }}</div>
                                @if($item->product->description)
                                    <div class="text-muted" style="font-size: 9pt; margin-top: 3px;">
                                        {{ Str::limit($item->product->description, 100) }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="location-badge">{{ $item->location->name }}</span>
                            </td>
                            <td class="text-center">
                                <strong>{{ $item->quantity }}</strong>
                                @if($item->product->unit)
                                    <span class="text-muted">{{ $item->product->unit }}</span>
                                @endif
                            </td>
                            <td class="text-right">
                                R {{ number_format($item->price / 100, 2) }}
                            </td>
                            <td class="text-right">
                                <strong>R {{ number_format(($item->price * $item->quantity) / 100, 2) }}</strong>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals Section --}}
            <div class="totals-section">
                <div class="totals-box">
                    <div class="totals-row">
                        <span class="label">Subtotal:</span>
                        <span class="amount">R {{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="totals-row">
                        <span class="label">VAT ({{ number_format($vatRate * 100, 0) }}%):</span>
                        <span class="amount">R {{ number_format($vatAmount, 2) }}</span>
                    </div>
                    @if($order->discount_amount && $order->discount_amount > 0)
                        <div class="totals-row">
                            <span class="label">Discount:</span>
                            <span class="amount" style="color: #48bb78;">-R {{ number_format($order->discount_amount / 100, 2) }}</span>
                        </div>
                    @endif
                    @if($order->delivery_fee && $order->delivery_fee > 0)
                        <div class="totals-row">
                            <span class="label">Delivery Fee:</span>
                            <span class="amount">R {{ number_format($order->delivery_fee / 100, 2) }}</span>
                        </div>
                    @endif
                    <div class="totals-row total">
                        <span class="label">Total Amount:</span>
                        <span class="amount">R {{ number_format($total, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Additional Notes --}}
            @if($order->notes)
                <div class="notes-section">
                    <div class="notes-title">Order Notes</div>
                    <div class="notes-content">{{ $order->notes }}</div>
                </div>
            @endif

            {{-- Terms and Conditions --}}
            <div class="terms-section">
                <div class="terms-title">Terms and Conditions</div>
                <div class="terms-content">
                    <p><strong>Payment Terms:</strong> Payment is due {{ $order->payment_terms ?? 'within 30 days' }} from the invoice date unless otherwise agreed in writing.</p>

                    <p><strong>Delivery:</strong> Delivery dates are approximate and {{ $currentCompany->name }} shall not be liable for any delay in delivery. Risk in the goods shall pass to the buyer upon delivery.</p>

                    <p><strong>Returns:</strong> Goods may only be returned with prior written authorization. Return shipping costs are the responsibility of the buyer unless the goods are defective or incorrect.</p>

                    <p><strong>Liability:</strong> {{ $currentCompany->name }}'s liability for any claim shall be limited to the invoice value of the goods in question. We shall not be liable for any consequential or indirect losses.</p>

                    <p><strong>Title:</strong> Title in the goods shall remain with {{ $currentCompany->name }} until payment in full has been received.</p>

                    @if($currentCompany->terms_and_conditions)
                        <p class="mt-2">{{ $currentCompany->terms_and_conditions }}</p>
                    @endif
                </div>
            </div>

            {{-- Footer --}}
            <div class="print-footer">
                <p>Thank you for your business!</p>
                <p style="font-size: 8pt; margin-top: 5px;">
                    This is a {{ $order->status === 'completed' ? 'valid tax invoice' : 'pro forma invoice' }} generated on {{ now()->format('d M Y H:i') }}
                </p>
                <p style="font-size: 8pt;">
                    For queries, please contact {{ $currentCompany->email ?? 'us' }} or call {{ $currentCompany->phone ?? 'our office' }}
                </p>
                <div class="footer-gradient-bar"></div>
            </div>
        </div>
    </div>

    {{-- Print Script --}}
    <script>
        // Auto-print option (uncomment if needed)
        // window.onload = function() { window.print(); }

        // Close window after print
        window.onafterprint = function() {
            // window.close(); // Uncomment to auto-close after printing
        }
    </script>
@endsection
