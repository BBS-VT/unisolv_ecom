<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            @if (!auth()->user()->repcode)
                <input type="hidden" name="salesperson_id" value="{{ auth()->user()->id }}" />
            @else
                <input type="hidden" name="salesperson_id" value="{{ auth()->user()->RepCode }}" />
            @endif
                <label for="order_number">{{ __('cruds.order.fields.number') }}</label>
            <input type="text" name="order_number" id="order_number" class="form-control" value="{{ $order->order_number }}" readonly>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="required" for="customer">{{ __('cruds.order.fields.customer_name') }}</label>
            <select id="customer" name="customer_id" data-toggle="select" class="form-control select2-hidden-accessible" data-select2-id="customer">
                <option disabled selected>{{ __('global.pleaseSelect') }}</option>
                @if($order->customer_id)
                    <option value="{{ $order->customer_id }}"
                        selected=""
                        data-currency="{{ $order->customer->currency }}"
                    >
                    {{ $order->customer->CustomerName }}
                    </option>
                @endif
            </select>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label class="required" for="reference_number">{{ __('cruds.order.fields.ponumber') }}</label>
            <input type="text" name="reference_number" id="reference_number" class="form-control" required />
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label class="required" for="order_date">{{ __('cruds.order.fields.order_date') }}</label>
            <input type="text" name="order_date" id="order_date" class="form-control" value="{{ date('Y-m-d') }}" required id="mdate" style="text-align: center" />
        </div>
    </div>
</div>

<div class="col-12 mt-5">
    <div class="table-responsive" data-toggle="lists">
        <table class="table table-xl mb-0 thead-border-top-0 table-striped" id="orderDetail">
            <thead>
                <tr>
                    @if($tax_per_item and $discount_per_item)
                        <th >{{ __('global.products') }}</th>
                        <th >{{ __('global.taxes') }}</th>
                        <th >{{ __('global.quantity') }}</th>
                        <th >{{ __('global.stockOnhand') }}</th>
                        <th >{{ __('global.price') }}</th>
                        <th >{{ __('global.discount') }}</th>
                        <th class="col-2 col-sm-2 text-right">{{ __('global.total') }}</th>
                    @elseif($tax_per_item and !$discount_per_item)
                        <th class="w-40">{{ __('global.products') }}</th>
                        <th class="w-25">{{ __('global.taxes') }}</th>
                        <th class="w-10">{{ __('global.quantity') }}</th>
                        <th class="col-2 col-sm-2">{{ __('global.stockOnhand') }}</th>
                        <th class="w-15">{{ __('global.price') }}</th>
                        <th class="text-right w-10">{{ __('global.total') }}</th>
                    @elseif(!$tax_per_item and $discount_per_item)
                        <th class="w-40">{{ __('global.products') }}</th>
                        <th class="w-10">{{ __('global.quantity') }}</th>
                        <th class="col-2 col-sm-2">{{ __('global.stockOnhand') }}</th>
                        <th class="w-20">{{ __('global.price') }}</th>
                        <th class="w-20">{{ __('global.discount') }}</th>
                        <th class="text-right w-10">{{ __('global.total') }}</th>
                    @elseif(!$tax_per_item and !$discount_per_item)
                        <th class="w-60">{{ __('global.products') }}</th>
                        <th class="w-10">{{ __('global.quantity') }}</th>
                        <th class="col-2 col-sm-2">{{ __('global.stockOnhand') }}</th>
                        <th class="w-20">{{ __('global.price') }}</th>
                        <th class="text-right w-10">{{ __('global.total') }}</th>
                    @endif
                    <th></th>
                </tr>
            </thead>
            <tbody class="list" id="items">
                <tr id="product_row_template" class="d-none col-4 col-sm-4">
                    <td width="25%" class="select-container">
                        <select name="product[]" class="select2 form-control priceListener select-with-footer" required>
                            <option disabled selected>{{ __('global.pleaseSelect') }}</option>
                        </select>
                    </td>
                    @if($tax_per_item)
                        <td class="select-container d-none d-xl-block" style="visibility:hidden;">
                            <select name="taxes[]" type="hidden" multiple class="form-control priceListener">
                                @foreach(get_tax_types_select2_array($currentCompany->id) as $option )
                                    <option value="{{ $option['id'] }}" data-percent="{{ $option['percent'] }}">{{ $option['text'] }}</option>
                                @endforeach
                            </select>
                        </td>
                    @endif
                    <td class="col-2 col-sm-2">
                        <input name="quantity[]" type="number" class=" form-control priceListener" value="1" required>
                    </td>
                    <td class="col-2 col-sm-2">
                        <input name="QuantityOnHand[]" type="text" class=" form-control stock_input" value="" readonly>
                    </td>
                    <td class="col-2 col-sm-2">
                        <input name="price[]" type="text" class="form-control price_input priceListener"  autocomplete="off" value="0" readonly>
                    </td>
                    @if($discount_per_item)
                        <td class="col-2 col-sm-2">
                            <div class="input-group input-group-merge">
{{--                                <input name="discount[]" type="number" class="form-control form-control-prepended priceListener discountListener" id="chDiscount" onchange="validateDiscount(this.value)" value="">--}}
                                <input name="discount[]" type="number" class="form-control form-control-prepended priceListener discountListener" id="chDiscount" value="">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">%</div>
                                </div>
                            </div>

                        </td>

                    @endif
                    <td class="text-right">
                        <p class="mb-1">
                            <input type="text" name="total[]" class=" price_input text-right price-text amount_price" value="0" readonly>
                        </p>
                        <div class="tax_list"></div>
                    </td>
                    <td>
                        <a onclick="removeRow(this)">
                            <i data-feather="trash"></i>
                        </a>
                    </td>
                </tr>
                @if($order->items->count() > 0)
                    @foreach($order->items as $item)
                        <tr>
                            <td class="select-container col-4 col-sm-2">
                                <select name="product[]" class="form-control mb-3 priceListener select-with-footer" style="width: 100%; height:36px;" >
                                    <option value="">-- choose product --</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $item->product_id }}" selected="">
                                            {{ intval( ltrim( $product->StockCode, '0')) }} &nbsp;
                                            {{ $product->StockItemName }} -
                                            (R {{ !floatval($product->DiscountPercentage) ?
                                                                ( !empty($product->UnitPrice) ? number_format($product->UnitPrice, 2) : number_format($product->SellingPrice, 2) )
                                                                : number_format($product->SellingPrice - (($product->DiscountPercentage / 100) * $product->SellingPrice), 2) }})&nbsp;
                                            [ {{ !empty($product->stockHolding->QuantityOnHand) ? $product->stockHolding->QuantityOnHand : '' }} ]
                                        </option>
                                    @endforeach
                                </select>

                            </td>
                            @if($tax_per_item)
                                <td class="select-container d-none d-xl-block" style="visibility:hidden;">
                                    <select name="taxes[]" multiple class="form-control priceListener select-with-footer select2-hidden-accessible">
                                        @foreach(get_tax_types_select2_array($currentCompany->id) as $option)
                                            <option value="{{ $option['id'] }}" data-percent="{{ $option['percent'] }}" {{ $item->hasTax($option['id']) ? 'selected=""' : '' }}>{{ $option['text'] }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            @endif
                            <td class="col-2 col-sm-2">
                                <input name="quantity[]" type="number" class="form-control priceListener" value="{{ $item->quantity }}" required>
                            </td>
                            <td class="col-2 col-sm-2">
                                <input name="QuantityOnHand[]" type="number" class="form-control stock_input " value="{{ $item->QuantityOnHand }}" readonly>
                            </td>
                            <td class="col-2 col-sm-2">
                                <input name="price[]" type="text" class="form-control price_input priceListener" autocomplete="off" value="{{ $item->price }}" readonly>
                            </td>
                            @if($discount_per_item)
                                <td class="col-2 col-sm-2">
                                    <div class="input-group input-group-merge">
{{--                                        <input name="discount[]" type="number" class="form-control form-control-prepended priceListener" id="chDiscount" onchange="validateDiscount(this.value)" value="{{ $item->discount_val }}">--}}
                                        <input name="discount[]" type="number" class="form-control form-control-prepended priceListener" id="chDiscount" value="{{ $item->discount_val }}">

                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                %
                                            </div>
                                        </div>
                                    </div>

                                </td>

                            @endif
                            <td class="text-right">
                                <p class="mb-1">
                                    <input type="text" name="total[]" class="col-4 col-sm-4 price_input text-right price-text amount_price" value="{{ $item->total }}" readonly>
                                </p>
                                <div class="tax_list"></div>
                            </td>
                            <td>
                                <a onclick="removeRow(this)">
                                    <i class="material-icons icon-16pt">clear</i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <div class="row card-body pagination justify-content-center text-center">
        <button id="add_product_row" type="button" class="btn btn-light">
            <i data-feather="plus-circle" class="align-self-center icon-xs"></i> {{ __('global.add') }} {{ __('cruds.product.title_singular') }}
        </button>
    </div>
</div>

<div class="row">
    <div class="col-md-5 mt-4 pr-4">
        <div class="form-group">
            <label for="notes">{{ __('global.notes') }}</label>
            <textarea name="notes" id="notes" class="form-control" rows="2">{{ $order->notes }}</textarea>
        </div>

        <div class="form-group">
            <label for="private_notes">{{ __('global.private_notes') }}</label>
            <textarea name="private_notes" id="private_notes" class="form-control" rows="2">{{ $order->private_notes }}</textarea>
        </div>
    </div>

    <div class="col-md-4 offset-md-3 mt-5 pl-4">
        <div class="card card-body shadow-none border">

            <div class="d-flex align-items-center mb-3">
                <div class="h6 mb-0 w-50">
                    <strong class="text-muted">{{ __('global.sub_total') }}</strong>
                </div>
                <div class="ml-auto h6 mb-0">
                    <input id="sub_total" name="sub_total" type="text" class="price_input price-text w-100 fs-inherit" value="{{ $order->sub_total ?? 0 }}" readonly>
                </div>
            </div>

            @if($tax_per_item == false)
                <div class="row mb-1">
                    <div class="col-12 h6 mb-1">
                        <strong class="text-muted">{{ __('global.taxes') }}</strong>
                    </div>
                    <div class="col-12 h6 mb-0">
                        <div class="form-group select-container">
                            <select id="total_taxes" name="total_taxes[]" data-toggle="select" multiple class="form-control priceListener select-with-footer" data-select2-id="total_taxes">
                                @foreach(get_tax_types_select2_array($currentCompany->id) as $option)
                                    <option value="{{ $option['id'] }}" data-percent="{{ $option['percent'] }}" {{ $order->hasTax($option['id']) ? 'selected=""' : '' }}>{{ $option['text'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            <div class="total_tax_list"></div>

            @if($discount_per_item == false)
                <div class="row mt-2 mb-1">
                    <div class="col-12 h6 mb-1">
                        <strong class="text-muted">{{ __('messages.discount') }}</strong>
                    </div>
                    <div class="col-12 h6 mb-0">
                        <div class="form-group">
                            <div class="input-group input-group-merge">
                                <input id="total_discount" name="total_discount" type="number" class="form-control form-control-prepended priceListener" value="{{ $order->discount_val ?? 0 }}">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        %
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <hr>
            <div class="d-flex align-items-center mb-3">
                <div class="h5 mb-0">
                    <strong class="text-muted">{{ __('global.total') }}</strong>
                </div>
                <div class="ml-auto h5 mb-0">
                    <input id="grand_total" name="grand_total" type="text" class="price_input price-text w-100 fs-inherit" value="{{ $order->total ?? 0 }}" readonly>
                </div>
            </div>
        </div>
        <div class="col-12 text-center float-right mt-3">
            <button type="button" class="btn btn-danger save_form_button pull-right">{{ __('global.save') }}</button>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">{{ __('global.cancel') }}</a>
        </div>
    </div>
</div>
