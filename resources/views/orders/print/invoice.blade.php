@extends('layouts.pdf')

@section('content')
    <div class="print-container">
        <div class="print-page">
            {{-- Print Actions (Screen Only) --}}
            <div class="print-actions no-print">
                <button onclick="window.print()" class="print-button">
                    <i class="mdi mdi-printer"></i> Print Order
                </button>
                <button onclick="window.close()" class="print-button" style="background: #718096;">
                    <i class="mdi mdi-close"></i> Close
                </button>
            </div>

            {{-- Simple Header --}}
            <div class="simple-header">
                <div class="company-section">
                    @if($currentCompany->avatar)
                        <img src="{{ $currentCompany->avatar ?? asset('images/default-company-logo.png') }}" alt="{{ $currentCompany->name }}" class="company-logo-simple">
                    @endif
                    <div class="company-name-simple">{{ $currentCompany->name }}</div>
                    <div class="company-address-simple">
                        @if($currentCompany->address->billing)
                            {{ $currentCompany->address->billing->address_1 }}<br>
                        @endif
                        @if($currentCompany->city)
                            {{ $currentCompany->city }} {{ $currentCompany->postal_code }}<br>
                        @endif
                        @if($currentCompany->country)
                            {{ $currentCompany->country }}
                        @endif
                    </div>
                </div>

                <div class="invoice-title-section ">
                    @if($order->status === 'completed')
                        <h1 class="invoice-title">TAX INVOICE</h1>
                    @else
                        <h1 class="invoice-title p-3">SALES ORDER</h1>
                    @endif

                </div>
            </div>


            {{-- Quote Details Grid --}}
            <div class="details-grid">
                <div class="details-left">
                    <h3>Bill To</h3>
                    <p>
                        <strong>{{ $order->customer->CustomerName }}</strong><br>
                        @if($order->customer->PostalAddress1){{ $order->customer->PostalAddress1 }}<br>@endif
                        @if($order->customer->PostalAddress2){{ $order->customer->PostalAddress2 }}<br>@endif
                        @if($order->customer->PostalCity){{ $order->customer->PostalCity }}, @endif
                        @if($order->customer->PostalPostalCode){{ $order->customer->PostalPostalCode }}<br><br />@endif
                        @if($order->customer->VatNr){{ __('Vat nr: ') }} {{ $order->customer->VatNr }}@endif
                    </p>
                </div>
                <div class="details-right">
                    <table class="info-table">
                        <tr>
                            <td>Order #:</td>
                            <td>{{ $order->OrderNumber }}</td>
                        </tr>
                        <tr>
                            <td>Order Date:</td>
                            <td>{{ $order->OrderDate->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td>Account:</td>
                            <td>{{ $order->customer->acc_main }}</td>
                        </tr>
                        <tr>
                            <td>Rep:</td>
                            <td>{{ $order->salesperson->Repcode }}</td>
                        </tr>

                    </table>
                </div>
                <div class="pb-2 clearfix"></div>
            </div>

            {{-- Items Table --}}
            <div class="items-wrapper">
                <table>
                    <thead>
                    <tr>
                        <th style="width: 45%;">Item</th>
                        <th style="width: 10%;" class="text-right">Qty</th>
                        <th style="width: 13%;" class="text-right">U/Price</th>
                        <th style="width: 10%;" class="text-right">Disc</th>
                        <th style="width: 10%;" class="text-right">VAT</th>
                        <th style="width: 12%;" class="text-right">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($order->items as $index => $item)
                        @php
                            $itemSubtotal = ($item->total / 1.15) / 100;
                            $itemVat = ($item->total / 100) - $itemSubtotal;
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $item->product->StockItemName }}</strong>
                                @if($item->product->StockCode)<small>CODE: {{ $item->product->StockCode }}</small>@endif
                            </td>
                            <td class="text-right">{{ number_format($item->Quantity) }}</td>
                            <td class="text-right">R {{ number_format($item->UnitPrice / 100, 2) }}</td>
                            <td class="text-right">@if($item->discount_val > 0)R {{ number_format($item->discount_val / 100, 2) }}@else-@endif</td>
                            <td class="text-right">R {{ number_format($itemVat, 2) }}</td>
                            <td class="text-right"><strong>R {{ number_format($item->total / 100, 2) }}</strong></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals --}}
            <div class="totals-wrapper">
                <div class="totals">
                    <table>
                        <tr class="subtotal-row">
                            <td>Subtotal</td>
                            <td>R {{ number_format($order->sub_total / 100, 2) }}</td>
                        </tr>
                        @if($order->discount_amount > 0)
                            <tr>
                                <td>Discount</td>
                                <td>-R {{ number_format($order->discount_amount, 2) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td>Tax</td>
                            <td>R {{ number_format($order->tax_amount, 2) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td>Total</td>
                            <td>R {{ number_format($order->total / 100, 2) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="clearfix"></div>
            </div>

            {{-- Notes and Terms --}}
            @if($order->notes || $order->terms_and_conditions)
                <div class="notes-wrapper">
                    @if($order->notes)
                        <h4>Notes</h4>
                        <p>{{ $order->notes }}</p>
                    @endif
                    @if($order->terms_and_conditions)
                        <h4>Terms & Conditions</h4>
                        <p>{{ $order->terms_and_conditions }}</p>
                    @endif
                </div>
            @endif


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
