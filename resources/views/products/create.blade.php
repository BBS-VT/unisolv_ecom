@extends('layouts.master')
@section('title') @lang('global.add_product') @endsection
@section('content')
    {{--@component('components.breadcrumb')
        @slot('title') Add Product @endslot
    @endcomponent--}}


@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/dropzone/dropzone-min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>
    <script src="{{ URL::asset('build/js/pages/ecommerce-create-product.init.js') }}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
