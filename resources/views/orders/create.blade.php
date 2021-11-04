@extends('layouts.app', ['page' => 'orders'])

@section('title', __('global.create_order'))

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
                    <h4 class="card-title">{{ __('global.new_order') }} </h4>

                </div>
                <div class="card-body">
                    <form action="{{ route('orders.store') }}" method="POST" >
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
    <script src="{{ URL::asset('plugins/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/timepicker/bootstrap-material-datetimepicker.js') }}"></script>
    <script src="{{ URL::asset('plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js') }}"></script>

    @include('orders._js')
    <script>
        $(document).ready(function() {
            $(".select2-canal").select2();

            addProductRow();

        });
    </script>
@endpush

