@extends('layouts.app')

@push('style')
    <link href="{{ URL::asset('plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
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
                    <form action="/orders/create_step1" method="POST" >
                        @csrf
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    @if (!auth()->user()->repcode)
                                        <input type="hidden" name="order[SalesPersonID]" id="SalesPersonID" value="{{ auth()->user()->id }}" />
                                    @else
                                        <input type="hidden" name="order[SalesPersonID]" id="SalesPersonID" value="{{ auth()->user()->RepCode }}" />
                                    @endif
                                    <input type="hidden" name="order[LastEditedBy]" id="LastEditedBy" value="{{ auth()->user()->id }}" />
                                    <input type="hidden" name="order[OrderStatusID]" id="OrderStatusID" value="1" />
                                    <input type="hidden" name="order[Authorisation]" id="Authorisation" value="0" />
                                    <label for="OrderNumber">{{ trans('cruds.order.fields.number') }}</label>
                                    <input type="text" name="order[OrderNumber]" id="OrderNumber" class="form-control"
                                           value="{{ App\Models\Order::getNextOrderNumber() }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="CustomerID">{{ trans('cruds.order.fields.customer_name') }}</label>
                                    <select class="form-control mb-3 select2-canal {{ $errors->has('CustomerID') ? 'is-invalid' : '' }}"  name="order[CustomerID]"  required>
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
    <script src="{{ URL::asset('plugins/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/timepicker/bootstrap-material-datetimepicker.js') }}"></script>
    <script src="{{ URL::asset('plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js') }}"></script>

    <script src="{{ URL::asset('pages/jquery.forms-advanced.js') }}"></script>


    <script>
        $(document).ready(function(){

            $(".select2-canal").select2();

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
