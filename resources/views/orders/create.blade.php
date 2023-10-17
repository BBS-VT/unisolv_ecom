@extends('layouts.app', ['page' => 'orders'])

@section('title', __('global.create_order'))

@section('style')
    {{--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css">
    <link href="{{ URL::asset('plugins/timepicker/bootstrap-material-datetimepicker.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />--}}
    <link href="{{ URL::asset('plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('plugins/timepicker/bootstrap-material-datetimepicker.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />
@endsection

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
                    <h4 class="card-title">{{ __('global.new_order') }} </h4>

                </div>
                <div class="card-body">
                    <form action="{{ route('orders.store') }}" method="POST" id="order_form">
                        @include('layouts._form_errors')
                        @csrf

                        @include('orders._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('plugins/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/timepicker/bootstrap-material-datetimepicker.js') }}"></script>
    <script src="{{ URL::asset('plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js') }}"></script>

    @include('orders._js')

    <script>
        $(document).ready(function() {
            addProductRow();

        });
        $(document).on("keyup", "#chDiscount", function () {
            var product_id = $(this).closest('tr').find('[name="product[]"]').val();

            $.ajax({
                type: 'GET',
                url: '{{ route('ajax.maxdiscount') }}',
                data: { _token: CSRF_TOKEN,'id': product_id },
                dataType: 'json',
                success: function(data) {
                    window.discountValidate = data.discValidate;
                    //console.log("discountValidate", discountValidate);
                    validateDiscount();
                },
                error: function(x , e) {
                    if (x.status==0) {
                        alert('You are offline!!\n Please check your network');
                    } else if(x.status==404) {
                        alert('Requested URL not found.');
                    } else if(x.status==500) {
                        alert('Internal Server Error.');
                    } else if(e=='parsererror') {
                        alert('Error.\nParsing JSON Request failed.');
                    } else if(e=='timeout') {
                        alert('Request Timed out');
                    } else {
                        alert('Unknown Error.\n'+x.responseText);
                    }
                }
            });


            function validateDiscount() {

                $('#orderDetail td').on('change', function() {
                    var row = $(this).closest('tr');
                    var id = $(row).find('[name="discount[]"]').val();

                    //console.log("id", id);
                    //console.log("discountValidate", discountValidate);

                    if (id <= discountValidate) {
                        calculateRowPriceNoContract();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            text: 'Discount cannot exceed ' + discountValidate + ' % for this item',
                        })
                    }
                });

            }
        })
    </script>


@endsection

