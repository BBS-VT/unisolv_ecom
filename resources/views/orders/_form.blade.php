<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            @if (!auth()->user()->repcode)
                <input type="hidden" name="salesperson_id" value="{{ auth()->user()->id }}" />
            @else
                <input type="hidden" name="salesperson_id" value="{{ auth()->user()->RepCode }}" />
            @endif
                <label for="order_number">{{ __('cruds.order.fields.number') }}</label>
            <input type="text" name="order_number" class="form-control" value="{{ $order->order_number }}" readonly>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="customer">{{ __('cruds.order.fields.customer_name') }}</label>
<!--            <select id="customer" name="customer_id" data-toggle="select" class="form-control select2-hidden-accessible" data-select2-id="customer">
                <option disabled selected>{{ __('global.pleaseSelect') }}</option>
                @if($order->CustomerID)
                    <option value="{{ $order->customer_id }}"
                        selected="">
                        {{ $order->customer->CustomerName }}
                    </option>
                @endif
            </select>-->
            <select class="form-control mb-3 select2-canal {{ $errors->has('CustomerID') ? 'is-invalid' : '' }}"  name="customer_id"  required>
                @foreach($customers as $id => $customer)
                    <option value="{{ $id }}" >{{ $customer }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label class="required" for="CustomerPurchaseOrderNumber">{{ __('cruds.order.fields.ponumber') }}</label>
            <input type="text" name="CustomerPurchaseOrderNumber"  class="form-control" required />
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label class="required" for="OrderDate">{{ __('cruds.order.fields.order_date') }}</label>
            <input type="text" name="OrderDate"  class="form-control" value="{{ date('Y-m-d') }}" required id="mdate" style="text-align: center" />
        </div>
    </div>
</div>

<div class="col-12 mt-5">
    <div class="table-responsive" data-toggle="lists">
        <table class="table table-xl mb-0 thead-border-top-0 table-striped">
            <thead>
                <tr>
                    @if($tax_per_item and $discount_per_item)
                        <th class="w-30">{{ __('global.products') }}</th>
                        <th class="w-20">{{ __('global.taxes') }}</th>
                        <th class="w-10">{{ __('global.quantity') }}</th>
                        <th class="w-15">{{ __('global.price') }}</th>
                        <th class="w-15">{{ __('global.discount') }}</th>
                        <th class="text-right w-10">{{ __('global.total') }}</th>
                    @elseif($tax_per_item and !$discount_per_item)
                        <th class="w-40">{{ __('global.products') }}</th>
                        <th class="w-25">{{ __('global.taxes') }}</th>
                        <th class="w-10">{{ __('global.quantity') }}</th>
                        <th class="w-15">{{ __('global.price') }}</th>
                        <th class="text-right w-10">{{ __('global.total') }}</th>
                    @elseif(!$tax_per_item and $discount_per_item)
                        <th class="w-40">{{ __('global.products') }}</th>
                        <th class="w-10">{{ __('global.quantity') }}</th>
                        <th class="w-20">{{ __('global.price') }}</th>
                        <th class="w-20">{{ __('global.discount') }}</th>
                        <th class="text-right w-10">{{ __('global.total') }}</th>
                    @elseif(!$tax_per_item and !$discount_per_item)
                        <th class="w-60">{{ __('global.products') }}</th>
                        <th class="w-10">{{ __('global.quantity') }}</th>
                        <th class="w-20">{{ __('global.price') }}</th>
                        <th class="text-right w-10">{{ __('global.total') }}</th>
                    @endif
                    <th></th>
                </tr>
            </thead>
            <tbody class="list" id="items">
                <tr id="product_row_template" class="d-none">
                    <td class="select2-container">
                        <select name="product[]" class="form-control priceListener" required>
                            <option disabled selected>{{ __('global.pleaseSelect') }}</option>
                        </select>
                    </td>
                    @if($tax_per_item)
                        <td class="select2-container">
                            <select name="taxes[]" multiple class="form-control priceListener">
                                @foreach(get_tax_types_select2_array($currentCompany->id) as $option )
                                    <option value="{{ $option['id'] }}" data-percent="{{ $option['percent'] }}">{{ $option['text'] }}</option>
                                @endforeach
                            </select>
                        </td>
                    @endif
                    <td>
                        <input name="quantity[]" type="number" class="form-control priceListener" value="1" required>
                    </td>
                    <td>
                        <input name="price[]" type="text" class="form-control price_input priceListener" autocomplete="off" value="0" required>
                    </td>
                    @if($discount_per_item)
                        <td>
                            <div class="input-group input-group-merge">
                                <input name="discount[]" type="number" class="form-control form-control-prepended priceListener" value="0">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">%</div>
                                </div>
                            </div>
                        </td>
                    @endif
                    <td class="text-right">
                        <p class="mb-1">
                            <input type="text" name="total[]" class="price_input price-text amount_price" value="0" readonly>
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
                            <td class="select-container">
                                <select name="product[]" class="form-control priceListener select-with-footer" required>
                                    <option value="{{ $item->product_id }}" selected="">{{ $item->product->name }}</option>
                                </select>
                            </td>
                            @if($tax_per_item)
                                <td class="select-container">
                                    <select name="taxes[]" multiple class="form-control priceListener select-with-footer">
                                        @foreach(get_tax_types_select2_array($currentCompany->id) as $option)
                                            <option value="{{ $option['id'] }}" data-percent="{{ $option['percent'] }}" {{ $item->hasTax($option['id']) ? 'selected=""' : '' }}>{{ $option['text'] }}</option>
                                        @endforeach
                                    </select>
                                    <div class="d-none select-footer">
                                        <a href="{{ route('settings.tax_types.create') }}" target="_blank" class="font-weight-300">+ {{ __('messages.add_new_tax') }}</a>
                                    </div>
                                </td>
                            @endif
                            <td>
                                <input name="quantity[]" type="number" class="form-control priceListener" value="{{ $item->quantity }}" required>
                            </td>
                            <td>
                                <input name="price[]" type="text" class="form-control price_input priceListener" autocomplete="off" value="{{ $item->price }}" required>
                            </td>
                            @if($discount_per_item)
                                <td>
                                    <div class="input-group input-group-merge">
                                        <input name="discount[]" type="number" class="form-control form-control-prepended priceListener" value="{{ $item->discount_val }}">
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
                                    <input type="text" name="total[]" class="price_input price-text amount_price" value="{{ $item->total }}" readonly>
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

    <div class="col-md-5 mt-5 pr-4">
        <div class="form-group">
            <label for="notes">{{ __('global.notes') }}</label>
            <textarea name="notes" class="form-control" rows="2">{{ $order->notes }}</textarea>
        </div>

        <div class="form-group">
            <label for="private_notes">{{ __('global.private_notes') }}</label>
            <textarea name="private_notes" class="form-control" rows="2">{{ $order->private_notes }}</textarea>
        </div>
    </div>

    <div class="col-md-4 offset-md-3 mt-5 pl-4">
        <div class="card card-body shadow-none border">

            <div class="d-flex align-items-center mb-3">
                <div class="h6 mb-0 w-50">
                    <strong class="text-muted">{{ __('global.sub_total') }}</strong>
                </div>
                <div class="ml-auto h6 mb-0">
                    <input id="sub-total" name="sub-total" type="text" class="price_input price-text w-100 fs-inherit" value="{{ $order->sub_total?? 0 }}" readonly>
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
            <button type="button" class="btn btn-primary save_form_button">{{ __('global.save') }}</button>
        </div>
    </div>
</div>
