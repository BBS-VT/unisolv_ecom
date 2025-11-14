@extends('layouts.master', ['page' => 'orders'])

@section('title', __('global.create_order'))

@push('styles')
    <link href="{{ URL::asset('build/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />
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

@push('scripts')
    <script src="{{ URL::asset('build/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            addProductRow();

        });

        $(document).on("click", "#reference_number", function(e) {
            var $select2 = $('.js-customer', $(this));

            $select2.parents('.form-group').removeClass('is-invalid')

            if ($select2.val() == '') {

                Swal.fire({
                    icon: 'error',
                    text: 'Please select a customer',
                }).then(
                    function () {
                        $select2.parents('.form-group').addClass('is-invalid')
                    }
                )
            }

            e.preventDefault();
            return false;
        })

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
                    var currentDiscount = parseInt(id);

                    //console.log("id", id);
                    //console.log("currentDiscount", currentDiscount);
                    //console.log("discountValidate", discountValidate);

                    if (currentDiscount <= discountValidate) {

                        calculateRowPrice();

                    } else {
                        Swal.fire({
                            icon: 'error',
                            text: 'Discount cannot exceed ' + discountValidate + ' % for this item',
                        }).then(
                            function () {
                                $('#add_product_row').attr('disabled', true);
                                $('#add_product_row').css('cursor', 'not-allowed');
                                $('#save_form_button').attr('disabled', true);
                                $('#save_form_button').css('cursor', 'not-allowed');
                            },
                            //function() { return false; }
                        );
                    }
                });
            }
        })
    </script>

    @include('orders._js')

@endpush

