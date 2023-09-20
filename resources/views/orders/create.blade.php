@extends('layouts.app', ['page' => 'orders'])

@section('title', __('global.create_order'))

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css">
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

@push('custom-scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    @include('orders._js')

    <script>
        $(document).ready(function() {
            addProductRow();
        });
        $(document).on("click", "#chDiscount", function () {
            var product_id = $(this).closest('tr').find('[name="product[]"]').val();
            //var discount = Number($(this).find('[name="discount[]"]').val());

            $.ajax({
                type: 'GET',
                url: '{{ route('ajax.maxdiscount') }}',
                data: { _token: CSRF_TOKEN,'id': product_id },
                dataType: 'json',
                success: function(data) {
                    //console.log("maxdiscount", data.maxdiscount);
                    $('#max_discount').val(data.maxdiscount);
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
        })
    </script>
    <script>
        function validateDiscount(value) {
            var maxDiscount = $(this.element).closest('tr').find('[name="discount[]"]').val();

            console.log(maxDiscount);
            /*var maxDiscount = currentRow.children("#max_discount").html();
            var discount = currentRow.children("#discount").html();
            console.log("maximum discount", maxDiscount);
            console.log("discount validation", discount);

            if (discount <= maxDiscount) {
                calculateRowPrice();
            } else {
                Swal.fire({
                    icon: 'error',
                    text: 'Discount cannot exceed ' + max_discount + ' % for this item',
                })
                $('#add_product_row').attr('disabled', true);
                $('#save_form_button').attr('disabled', true);
            }*/
        }
    </script>

@endpush

