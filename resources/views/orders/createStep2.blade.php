@extends('layouts.app')

@push('style')
{{--    <link href="{{ URL::asset('plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />--}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css">

    <link href="{{ URL::asset('plugins/timepicker/bootstrap-material-datetimepicker.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />

@endpush

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                @if($creditStatus == '1')
                    <div class="alert alert-warning border-0" role="alert">
                        <strong>Warning!</strong> Customer on Credit Hold
                    </div>
                @endif
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
                    <form action="{{ route('orders.create.step.two.post') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                {{ trans('global.products') }}
                                <div class="card-tools float-right">
                                    <button class="btn btn-gradient-primary btn-sm" id="btn-add-lookup"><i
                                            class="fa fa-plus"></i> {{ trans('global.add') }} {{ trans('cruds.order.fields.previous_items') }}</button>
                                </div>
                            </div>

                            <div class="card-body">
                                <table class="table" id="products_table">
                                    <thead>
                                    <tr>
                                        <th> Product </th>
                                        <th> Quantity </th>
                                        <th class="text-center"> Unit Price </th>
                                        <th class="text-center"> Disc </th>
                                        <th class="text-center"> VAT % </th>
                                        <th class="text-right"> Total </th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach (old('products', ['']) as $index => $oldProduct)
                                        <tr id="product{{ $index }}" class="product_abc">
                                            <td>
                                                <select name="StockItem[]" id="product" class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;" >
                                                    <option value="">-- choose product --</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}">
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
                                            <td>
                                                <input type="number" name="Quantity[]" class="form-control qty text-center" step="0" min="1" value="{{ old('Quantity.' . $index) ?? '1' }}" />
                                            </td>
                                            <td>
                                                <input type="text" name="UnitPrice[]" id="UnitPrice" class="form-control price text-center" value="{{ old('UnitPrice.' . $index) ?? '' }}" />

                                            </td>
                                            <td>
                                                <input type="text" name="Discount[]" id="Discount" class="form-control discount text-center" value="{{ old('Discount.' . $index) ?? ''  }}" />

                                            </td>
                                            <td>
                                                <input type="number" name="TaxRate[]" value="{{ ($product->TaxRateID == '0') ? '0' : $tax }}" class="form-control tax text-center" readonly/>
                                            </td>
                                            <td>
                                                <input type="number" name="total[]" placeholder="0.00" class="form-control total text-right" style="text-align: right" readonly />
                                                <input type="hidden" name="LastEditedBy[]" value="{{ auth()->user()->id }}" readonly />
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr id="product{{ count(old('products', [''])) }}"></tr>
                                    </tbody>
                                </table>

                                <div class="row">
                                    <div class="col-md-12">
                                        <button id="add_row" class="btn btn-secondary pull-left">+ Add Row</button>
                                        <button id='delete_row' class="pull-right btn btn-danger">- Delete Row</button>
                                    </div>
                                </div>
                                <div class="row clearfix" style="margin-top: 20px">
                                    <div class="col-md-12">
                                        <div class="float-right col-md-5">
                                            <table class="table table-bordered table-hover" id="tab_logic_total">
                                                <tbody>
                                                <tr>
                                                    <th class="text-center" width="50%">{{ trans('global.sub_total') }}</th>
                                                    <td class="text-center"><input type="number" name="sub_total" placeholder="0.00" class="form-control" id="sub_total"
                                                                                   style="text-align: right" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-center">{{ trans('global.invoice_vat') }}</th>
                                                    <td class="text-center"><input type="number" name='tax_amount' id="tax_amount" placeholder='0.00' class="form-control"
                                                                                   style="text-align: right" readonly/></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-center">{{ trans('global.invoice_total') }}</th>
                                                    <td class="text-center"><input type="number" name='total_amount' id="total_amount" placeholder='0.00' class="form-control"
                                                                                   style="text-align: right" readonly/></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary">{{ trans('global.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('custom-scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    <!-- <script src="{{ URL::asset('plugins/select2/select2.min.js') }}"></script> -->
    <script src="{{ URL::asset('plugins/timepicker/bootstrap-material-datetimepicker.js') }}"></script>

    <script src="{{ URL::asset('pages/jquery.forms-advanced.js') }}"></script>

    <script>
        $(document).ready(function(){

            $(".custom-select").select2();
            let row_number = {{ count(old('products', [''])) }};
            $("#add_row").click(function(e) {
                e.preventDefault();
                var options = $("#products_table tbody tr:first").find("[name='StockItem[]']").html();
                var cloned = $("#products_table tbody tr:first").clone().show();

                $(cloned).find("td:first").html('<select name="StockItem[]" class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">' + options + '</select>')
                $(cloned).find("input").val("")

                $('#products_table tbody').append('<tr class="product_abc" id="product' + (row_number + 1) + '">' + $(cloned).html() + '</tr>');
                $("select").select2();
                row_number++;
            });

            $("#delete_row").click(function(e){
                e.preventDefault();
                if(row_number > 1){
                    $("#product" + (row_number - 1)).html('');
                    row_number--;
                }
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.product_abc').on('keyup change', function(e){
                var product_id = $("#product").val();
                $.post('/orders/getprice/' + product_id,
                    function (data) {
                        $('#UnitPrice').val(data.SellingPrice);
                        $('#Discount').val(data.DiscountPercentage);
                    });
            });
           /*$('#product').on('keyup change', function(){
                var product_id = $("#product").val();
                $.post('/orders/getprice/' + product_id,
                    function (data) {
                        $('#UnitPrice').val(data.SellingPrice);
                        $('#Discount').val(data.DiscountPercentage);
                });
            })*/

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
                    var qty = parseFloat($(this).find('.qty').val());
                    var price = parseFloat($(this).find('.price').val());
                    var disc = parseFloat($(this).find('.discount').val());

                    if (disc != '') {
                        parseFloat($(this).find('.total').val(qty * (price - (price * disc /100))).text().replace("$", "")).toFixed(2);
                    } else {
                        parseFloat($(this).find('.total').val(qty * price).text().replace("$", "")).toFixed(2);
                    }

                    calc_total();
                }
            });
        }

        function calc_total()
        {
            total=0;
            $('.total').each(function() {
                total += parseFloat($(this).val());
            });
            $('#sub_total').val(total.toFixed(2));
            tax_sum=total-(total/1.15);
            $('#tax_amount').val(tax_sum.toFixed(2));
            inv_total=total;
            $('#total_amount').val(inv_total.toFixed(2));
        }
    </script>

@endpush
