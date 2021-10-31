@extends('layouts.app')

@push('style')
    <link href="{{ URL::asset('plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('plugins/timepicker/bootstrap-material-datetimepicker.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ trans('global.new_order') }} </h4>

                </div>
                <div class="card-body">
                    <p class="italic"><small>{{ trans('global.required_fields') }}</small></p>
                    <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <input type="hidden" name="order[LastEditedBy]" id="LastEditedBy" value="{{ auth()->user()->id }}" />
                                    <input type="hidden" name="order[OrderStatusID]" id="OrderStatusID" value="1" />
                                    <input type="hidden" name="order[Authorisation]" id="Authorisation" value="0" />
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="OrderNumber">{{ trans('cruds.order.fields.number') }}</label>
                                            <input type="text" name="order[OrderNumber]" id="OrderNumber" class="form-control"
                                                   value="{{ App\Models\Order::getNextOrderNumber() }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="required" for="CustomerID">{{ trans('cruds.order.fields.customer_name') }} *</label>
                                            <select class="form-control selectpicker {{ $errors->has('CustomerID') ? 'is-invalid' : '' }}" id="CustomerID" name="order[CustomerID]"
                                                    data-live-search="true" data-live-search-style="begins" title="Select customer..." required>
                                                @foreach($customers as $id => $customer)
                                                    <option value="{{ $id }}" >{{ $customer }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('CustomerID'))
                                                <em class="invalid-feedback">
                                                    {{ $errors->first('CustomerID') }}
                                                </em>
                                            @endif
                                            <p class="helper-block">
                                                {{ trans('cruds.order.fields.customer_name_helper') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="required" for="CustomerPurchaseOrderNumber">{{ trans('cruds.order.fields.ponumber') }}</label>
                                            <input type="text" name="order[CustomerPurchaseOrderNumber]"  class="form-control" required />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="required" for="OrderDate">{{ trans('cruds.order.fields.order_date') }}</label>
                                            <input type="text" name="order[OrderDate]"  class="form-control" value="{{ date('Y-m-d') }}" required id="mdate" style="text-align: center" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        @if (!auth()->user()->repcode)
                                            <input type="hidden" name="order[SalesPersonID]" id="SalesPersonID" value="{{ auth()->user()->id }}" />
                                        @else
                                            <input type="hidden" name="order[SalesPersonID]" id="SalesPersonID" value="{{ auth()->user()->RepCode }}" />
                                        @endif
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <label>{{ trans('global.selectProduct') }}</label>
                                    <div class="input-group mb-3">
                                        <button type="button" class="btn btn-secondary" id="button-addon1"><i class="fa fa-barcode"></i></button>
                                        <input type="text" name="product_code_name" id="productcodeSearch" class="form-control"
                                               placeholder="{{ trans('global.searchProduct') }}"  aria-describedby="button-addon1" />
                                    </div>
                                </div>
                                <div class="row mt-5">
                                    <div class="col-md-12">
                                        <h5>{{ trans('global.products') }}</h5>
                                        <div class="table-responsive mt-3">
                                            <table id="products_table" class="table table-hover order-list">
                                                <thead>
                                                    <tr>
                                                        <th>{{ trans('cruds.order.fields.productCode') }}</th>
                                                        <th>{{ trans('cruds.order.fields.productName') }}</th>
                                                        <th>{{ trans('cruds.order.fields.productQty') }}</th>
                                                        <th>{{ trans('cruds.order.fields.productUnitprice') }}</th>
                                                        <th>{{ trans('cruds.order.fields.productDiscount') }}</th>
                                                        <th>{{ trans('cruds.order.fields.productTax') }}</th>
                                                        <th>{{ trans('cruds.order.fields.productSubtotal') }}</th>
                                                        <th><i class="dripicons-trash"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot class="tfoot active">
                                                    <th colspan="2">{{ trans('global.invoice_total') }}</th>
                                                    <th id="total-qty">0</th>
                                                    <th></th>
                                                    <th id="total-discount">0.00</th>
                                                    <th id="total-tax">0.00</th>
                                                    <th id="total">0.00</th>
                                                    <th><i class="dripicons-trash"></i></th>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_qty" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_discount" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_tax" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_price" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="item" />
                                            <input type="hidden" name="order_tax" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="grand_total" />
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>

                        <div>
                            <input class="btn btn-danger float-right" type="submit" value="{{ trans('global.next') }}">
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary">{{ trans('global.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('custom-scripts')
    <script src="{{ URL::asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/timepicker/bootstrap-material-datetimepicker.js') }}"></script>
    <script src="{{ URL::asset('plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js') }}"></script>

    <script src="{{ URL::asset('pages/jquery.forms-advanced.js') }}"></script>


    <script>
        var product_array = [];
        var product_code = [];
        var product_name = [];
        var product_qty = [];
        var product_type = [];
        var product_id = [];
        var product_list = [];
        var qty_list = [];

        var product_price = [];
        var product_discount = [];
        var tax_rate = [];
        var tax_name = [];
        var tax_method = [];
        var unit_name = [];
        var rowindex;
        var row_product_price;

        $('select[name="CustomerID"]').on('change', function() {
            var id = $(this).val();
            $.get('getcustomergroup/' + id, function(data) {
                customer_group_rate = (data / 100);
            });
        });

        $('#productcodeSearch').on('input', function() {
            var customer_id = $('#CustomerID').val();
            temp_data = $('#productcodeSearch').val();
            if(!customer_id){
                $('#productcodeSearch').val(temp_data.substring(0, temp_data.length - 1));
                alert('Please select a Customer');
            }
        });

        var productcodeSearch = $('#productcodeSearch');

        productcodeSearch.autocomplete({
            source: function(request, response) {
                var matcher = new RegExp(".?" + $.ui.autocomplete.escapeRegExp(request.term), "i");
                response($.grep(product_array, function(item) {
                    return matcher.test(item);
                }));
            },
            response: function(event, ui) {
                if (ui.content.length == 1){
                    var data = ui.content[0].value;
                    $(this).autocomplete("close");
                    productSearch(data);
                };
            },
            select: function(event, ui) {
                var data = ui.item.value;
                productSearch(data);
            }
        });

        // Change quantity
        $("#products_table").on('input', '.qty', function() {
            rowindex = $(this).closest('tr').index();
            if($(this).val() < 0 && $(this).val() != '') {
                $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val(1);
                alert("Quantity cannot be less than 0");
            }
            checkQuantity($(this).val(), true);
        });

        // Delete product row
        $("table.order-list tbody").on("click", ".ibtnDel", function(event) {
            rowindex = $(this).closest('tr').index();
            product_price.splice(rowindex, 1);
            product_discount.splice(rowindex, 1);
            tax_rate.splice(rowindex, 1);
            tax_name.splice(rowindex, 1);
            tax_method.splice(rowindex, 1);
            unit_name.splice(rowindex, 1);
            unit_operator.splice(rowindex, 1);
            unit_operation_value.splice(rowindex, 1);
            $(this).closest("tr").remove();
            calculateTotal();
        })

        // Product Search
        function productSearch(data) {
            $.ajax({
                type: 'GET',
                url: 'product_search',
                data: {
                    data: data
                },
                success: function(data) {
                    var flag = 1;
                    $(".product-code").each(function(i) {
                        if ($(this).val() == data[1]) {
                            rowindex = i;
                            var qty = parseFloat($('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val()) + 1;
                            $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val(qty);
                            checkQuantity(String(qty), true);
                            flag = 0;
                        }
                    });
                    $("input[name='product_code_name']").val(' ');
                    if(flag){
                        var newRow = $("<tr>");
                        var cols = '';
                        pos = product_code.indexOf(data[1]);
                        temp_unit_name = (data[6]).split(', ');
                        cols += '<td>' + data[0] + '</td>';
                        cols += '<td>' + data[1] + '</td>';
                        cols += '<td><input type="number" class="form-control qty" name="qty[]" value="1" step="any" required /><td>';
                        cols += '<td class="net_unit_price"></td>';
                        cols += '<td class="discount">0.00</td>';
                        cols += '<td class="tax"></td>';
                        cols += '<td class="sub-total"></td>';
                        cols += '<td><button type="button" class="ibtnDel btn btn-md btn-danger">{{ trans("global.delete")}}</button></td>';
                        cols += '<input type="hidden" class="product-code" name="product_code[]" value="' + data[1] + '"/>';
                        cols += '<input type="hidden" class="product-id" name="product_id[]" value="' + data[9] + '"/>';
                        cols += '<input type="hidden" class="sale-unit" name="sale_unit[]" value="' + temp_unit_name[0] + '"/>';
                        cols += '<input type="hidden" class="net_unit_price" name="net_unit_price[]" />';
                        cols += '<input type="hidden" class="discount-value" name="discount[]" />';
                        cols += '<input type="hidden" class="tax-rate" name="tax_rate[]" value="' + data[3] + '"/>';
                        cols += '<input type="hidden" class="tax-value" name="tax[]" />';
                        cols += '<input type="hidden" class="subtotal-value" name="subtotal[]" />';

                        newRow.append(cols);
                        $("table.order-list tbody").prepend(newRow);
                        rowindex = newRow.index();

                        product_discount.splice(rowindex, 0, '0.00');
                        tax_rate.splice(rowindex, 0, parseFloat(data[3]));
                        tax_name.splice(rowindex, 0, data[4]);
                        tax_method.splice(rowindex, 0, data[5]);
                        unit_name.splice(rowindex, 0, data[6]);
                        unit_operator.splice(rowindex, 0, data[7]);
                        unit_operation_value.splice(rowindex, 0, data[8]);
                        checkQuantity(1, true);
                    }
                }
            });
        }

        function calculateTotal() {
            // Sum of quantity
            var total_qty = 0;
            $(".qty").each(function () {
                if ($(this).val() == ' ') {
                    total_qty += 0;
                } else {
                    total_qty += parseFloat($(this).val());
                }
            });
            $("#total-qty").text(total_qty);
            $('input[name="total_qty"]').val(total_qty);

            // Sum of discount
            var total_discount = 0;
            $(".discount").each(function () {
                total_discount += parseFloat($(this).text());
            });
            $("#total-discount").text(total_discount.toFixed(2));
            $('input[name="total_discount"]').val(total_discount.toFixed(2));

            // Sum of Tax
            var total_tax = 0;
            $(".tax").each(function() {
                total_tax += parseFloat($(this).text());
            });
            $("#total-tax").text(total_tax.toFixed(4));
            $('input[name="total_tax"]').val(total_tax.toFixed(4));

            // Sum of Subtotal
            var total = 0;
            $(".sub-total").each(function() {
                total += parseFloat($(this).text());
            });
            $("#total").text(total.toFixed(2));
            $('input[name="total_price"]').val(total.toFixed(2));

            calculateGrandTotal();
        }
        function calculateGrandTotal() {

            var item = $('table.order-list tbody tr:last').index();

            var total_qty = parseFloat($('#total-qty').text());
            var subtotal = parseFloat($('#total').text());
            var order_tax = parseFloat($('select[name="order_tax_rate"]').val());
            var order_discount = parseFloat($('input[name="order_discount"]').val());
            var shipping_cost = parseFloat($('input[name="shipping_cost"]').val());

            if (!order_discount)
                order_discount = 0.00;
            if (!shipping_cost)
                shipping_cost = 0.00;

            item = ++item + '(' + total_qty + ')';
            order_tax = (subtotal - order_discount) * (order_tax / 100);
            var grand_total = (subtotal + order_tax + shipping_cost) - order_discount;

            $('#item').text(item);
            $('input[name="item"]').val($('table.order-list tbody tr:last').index() + 1);
            $('#subtotal').text(subtotal.toFixed(2));
            $('#order_tax').text(order_tax.toFixed(2));
            $('input[name="order_tax"]').val(order_tax.toFixed(2));
            $('#order_discount').text(order_discount.toFixed(2));
            $('#shipping_cost').text(shipping_cost.toFixed(2));
            $('#grand_total').text(grand_total.toFixed(2));
            if( $('select[name="payment_status"]').val() == 4 ){
                $('#paying-amount').val('');
                $('#paid-amount').val(grand_total.toFixed(2));
            }
            $('input[name="grand_total"]').val(grand_total.toFixed(2));
        }

        $('input[name="order_discount"]').on("input", function() {
            calculateGrandTotal();
        });

        $('select[name="order_tax_rate"]').on("change", function() {
            calculateGrandTotal();
        });

        $(document).ready(function(){

            /*$('.selectpicker').selectpicker({
                style: 'btn-link'
            });*/

            let row_number = {{ count(old('products', [''])) }};
            $("#add_row").click(function(e){
                e.preventDefault();
                let new_row_number = row_number - 1;
                $('#product' + row_number).html($('#product' + new_row_number).html()).find('td:first-child');
                $('#products_table').append('<tr id="product' + (row_number + 1) + '"></tr>');
                row_number++;
            });
            $("#delete_row").click(function(e){
                e.preventDefault();
                if(row_number > 1){
                    $("#product" + (row_number - 1)).html('');
                    row_number--;
                }
            });

            $('#products_table tbody').on('keyup change', function() {
                calc();
            });

            $('#tax').on('keyup change', function() {
                calc_total();
            });
        });

        function calc()
        {
            $('#products_table tbody tr').each(function(i, element) {
                var html = $(this).html();
                if (html !='')
                {
                    var qty = $(this).find('.qty').val();
                    var price = $(this).find('.price').val();
                    $(this).find('.total').val(qty*price);

                    calc_total();
                }
            });
        }

        function calc_total()
        {
            total=0;
            $('.total').each(function() {
                total += parseInt($(this).val());
            });
            $('#sub_total').val(total.toFixed(2));
            tax_sum=total*0.15;
            $('#tax_amount').val(tax_sum.toFixed(2));
            inv_total=total*1.15;
            $('#total_amount').val(inv_total.toFixed(2));
        }
    </script>

@endpush
