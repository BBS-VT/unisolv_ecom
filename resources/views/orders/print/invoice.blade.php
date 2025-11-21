@extends('layouts.pdf')

@section('content')
    <div class="tm_container">
        <div class="tm_invoice_wrap">
            <div class="tm_invoice tm_style2" id="tm_download_section">
                <div class="tm_invoice_in">
                    <div class="tm_invoice_head tm_top_head tm_mb20">
                        <div class="tm_invoice_left">
                            <div class="tm_logo"><img src="{{ $currentCompany->avatar ?? asset('images/default-company-logo.png') }}" alt="{{ $currentCompany->name }}"></div>
                        </div>
                        <div class="tm_invoice_right">
                            <div class="tm_grid_row tm_col_3">
                                <div>
                                    <b class="tm_primary_color">Email</b> <br>
                                    {{ $currentCompany->email }} <br>
                                    VAT No: {{ $currentCompany->vat_number }}
                                </div>
                                <div>
                                    <b class="tm_primary_color">Phone</b> <br>
                                    {{ $currentCompany->phone }} <br>
                                    Reg No: {{ $currentCompany->registration_number }}
                                </div>
                                <div>
                                    <b class="tm_primary_color">Address</b> <br>
                                    9 Paul Street, London <br>
                                    England EC2A 4NE
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tm_invoice_info tm_mb10">
                        <div class="tm_invoice_info_left">
                            <p class="tm_mb2"><b>Invoice To:</b></p>
                            <p>
                                <b class="tm_f16 tm_primary_color">{{ $order->customer->CustomerName }}</b> <br>
                                {{ $order->customer->PostalAddressLine1 }}, {{ $order->customer->PostalAddressLine2 }}<br>
                                {{ $order->customer->PostalCity }}, {{ $order->customer->PostalPostalCode }} <br>
                                {{ $order->customer->GeneralEmailAddress }} <br>
                                {{ $order->customer->PhoneNumber }}
                            </p>
                        </div>
                        <div class="tm_invoice_info_right">
                            <div class="tm_ternary_color tm_f50 tm_text_uppercase tm_text_center tm_invoice_title tm_mb15 tm_mobile_hide">
                                @if($order->status === 'completed')
                                    INVOICE
                                @else
                                    ORDER
                                @endif
                            </div>
                            <div class="tm_grid_row tm_col_3 tm_invoice_info_in tm_accent_bg">
                                <div>
                                    <span class="tm_white_color_60"></span> <br>
                                    <b class="tm_f16 tm_white_color"></b>
                                </div>
                                <div>
                                    <span class="tm_white_color_60">Invoice Date:</span> <br>
                                    <b class="tm_f16 tm_white_color">10 March 2022</b>
                                </div>
                                <div>
                                    <span class="tm_white_color_60">
                                        @if($order->status === 'completed')
                                            Invoice No
                                        @else
                                            Order No
                                        @endif:</span> <br>
                                    <b class="tm_f16 tm_white_color">#&nbsp;{{ $order->OrderNumber }}</b>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tm_table tm_style1">
                        <div class="tm_round_border">
                            <div class="tm_table_responsive">
                                <table>
                                    <thead>
                                    <tr>
                                        <th class="tm_width_7 tm_semi_bold tm_accent_color">Item</th>
                                        <th class="tm_width_2 tm_semi_bold tm_accent_color">Price</th>
                                        <th class="tm_width_1 tm_semi_bold tm_accent_color">Qty</th>
                                        <th class="tm_width_2 tm_semi_bold tm_accent_color tm_text_right">Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($order->items as $item)
                                        <tr class="tm_gray_bg">
                                            <td class="tm_width_7">
                                                <p class="tm_m0 tm_f16 tm_primary_color">{{ $item->product->StockCode }}</p>
                                                {{ $item->product->StockItemName }}
                                            </td>
                                            <td class="tm_width_2">R {{ number_format($item->UnitPrice / 100, 2) }}</td>
                                            <td class="tm_width_1">{{ $item->Quantity }}</td>
                                            <td class="tm_width_2 tm_text_right">R {{ number_format(($item->UnitPrice * $item->Quantity) / 100, 2) }}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tm_invoice_footer tm_mb15 tm_m0_md">
                            <div class="tm_left_footer">
                                <div class="tm_card_note tm_ternary_bg tm_white_color"><b>Payment info: </b></div>
                                <p class="tm_mb2"><b class="tm_primary_color">Important Note:</b></p>
                                <p class="tm_m0">{{ $order->notes }}</p>
                            </div>
                            <div class="tm_right_footer">
                                <table class="tm_mb15">
                                    <tbody>
                                    <tr>
                                        <td class="tm_width_3 tm_primary_color tm_border_none tm_bold">Subtoal</td>
                                        <td class="tm_width_3 tm_primary_color tm_text_right tm_border_none tm_bold">R {{ number_format($subtotal, 2) }}</td>
                                    </tr>
                                    @if($order->discount_amount && $order->discount_amount > 0)
                                        <tr>
                                            <td class="tm_width_3 tm_danger_color tm_border_none tm_pt0">Discount</td>
                                            <td class="tm_width_3 tm_danger_color tm_text_right tm_border_none tm_pt0">-R {{ number_format($order->discount_amount / 100, 2) }}</td>
                                        </tr>
                                    @endif
                                    @if($order->delivery_fee && $order->delivery_fee > 0)
                                        <tr>
                                            <td class="tm_width_3 tm_danger_color tm_border_none tm_pt0">Delivery Fee</td>
                                            <td class="tm_width_3 tm_danger_color tm_text_right tm_border_none tm_pt0">R {{ number_format($order->delivery_fee / 100, 2) }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="tm_width_3 tm_primary_color tm_border_none tm_pt0">VAT ({{ number_format($vatRate * 100, 0) }}%)</td>
                                        <td class="tm_width_3 tm_primary_color tm_text_right tm_border_none tm_pt0">R {{ number_format($vatAmount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="tm_width_3 tm_border_top_0 tm_bold tm_f16 tm_white_color tm_accent_bg tm_radius_6_0_0_6">Grand Total	</td>
                                        <td class="tm_width_3 tm_border_top_0 tm_bold tm_f16 tm_primary_color tm_text_right tm_white_color tm_accent_bg tm_radius_0_6_6_0">R {{ number_format($total, 2) }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tm_invoice_footer tm_type1">
                            <div class="tm_left_footer"></div>
                            <div class="tm_right_footer">
                                <div class="tm_sign tm_text_center">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tm_note tm_font_style_normal tm_text_center">
                        <hr class="tm_mb15">
                        <p class="tm_mb2"><b class="tm_primary_color">Terms & Conditions:</b></p>
                        <br class="tm_m0"><br><strong>Payment Terms:</strong> Payment is due {{ $order->payment_terms ?? 'within 30 days' }} from the invoice date unless otherwise agreed in writing.</br>

                        <strong>Delivery:</strong> Delivery dates are approximate and {{ $currentCompany->name }} shall not be liable for any delay in delivery. Risk in the goods shall pass to the buyer upon delivery.</br>
                        <strong>Returns:</strong> Goods may only be returned with prior written authorization. Return shipping costs are the responsibility of the buyer unless the goods are defective or incorrect.</br>

                        <strong>Liability:</strong> {{ $currentCompany->name }}'s liability for any claim shall be limited to the invoice value of the goods in question. We shall not be liable for any consequential or indirect losses.</br>

                        <strong>Title:</strong> Title in the goods shall remain with {{ $currentCompany->name }} until payment in full has been received.</p>
                    </div><!-- .tm_note -->
                </div>
            </div>
            <div class="tm_invoice_btns tm_hide_print">
                <a href="javascript:window.print()" class="tm_invoice_btn tm_color1">
          <span class="tm_btn_icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><path d="M384 368h24a40.12 40.12 0 0040-40V168a40.12 40.12 0 00-40-40H104a40.12 40.12 0 00-40 40v160a40.12 40.12 0 0040 40h24" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><rect x="128" y="240" width="256" height="208" rx="24.32" ry="24.32" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><path d="M384 128v-24a40.12 40.12 0 00-40-40H168a40.12 40.12 0 00-40 40v24" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><circle cx="392" cy="184" r="24" fill='currentColor'/></svg>
          </span>
                    <span class="tm_btn_text">Print</span>
                </a>
                <button id="tm_download_btn" class="tm_invoice_btn tm_color2">
          <span class="tm_btn_icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><path d="M320 336h76c55 0 100-21.21 100-75.6s-53-73.47-96-75.6C391.11 99.74 329 48 256 48c-69 0-113.44 45.79-128 91.2-60 5.7-112 35.88-112 98.4S70 336 136 336h56M192 400.1l64 63.9 64-63.9M256 224v224.03" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/></svg>
          </span>
                    <span class="tm_btn_text">Download</span>
                </button>
            </div>
        </div>
    </div>

@endsection
