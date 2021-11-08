<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name', 'Unisolv CRM') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">

    @stack('style')

    <!-- App css -->
    <link href="{{ URL::asset('plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('css/metisMenu.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('css/custom.css') }}" rel="stylesheet" type="text/css" />

</head>

<body class="dark-sidenav">
<!-- Left Sidenav -->
@include('layouts.partials.sidebar')
<!-- end left-sidenav-->
<div class="page-wrapper">
    <!-- Top Bar Start -->
    @include('layouts.partials.topbar')
    <!-- Top Bar End -->

    <!-- Page Content-->
    <div class="page-content">
        <div class="container-fluid">
            @yield('content')
        </div>

        @include('layouts.partials.footer')
    </div>
    <!-- end page content -->
</div>
<!-- end page-wrapper -->

<!-- jQuery  -->
<script src="{{ URL::asset('js/jquery.min.js') }}"></script>
<script src="{{ URL::asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ URL::asset('js/metisMenu.min.js') }}"></script>
<script src="{{ URL::asset('js/waves.js') }}"></script>
<script src="{{ URL::asset('js/simplebar.min.js') }}"></script>
<script src="{{ URL::asset('js/dom-factory.js') }}"></script>
<script src="{{ URL::asset('js/feather.min.js') }}"></script>
<script src="{{ URL::asset('js/toggle-check-all.js') }}"></script>
<script src="{{ URL::asset('js/check-selected-row.js') }}"></script>
<script src="{{ URL::asset('js/dropdown.js') }}"></script>
<script src="{{ URL::asset('js/moment.js') }}"></script>
<script src="{{ URL::asset('plugins/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('js/select2.js') }}"></script>
<script src="{{ URL::asset('js/jquery.priceformat.min.js') }}"></script>
<script src="{{ URL::asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>

<!-- App js -->
<script src="{{ URL::asset('js/app.js') }}"></script>
<script src="{{ URL::asset('js/custom.js') }}"></script>

@stack('custom-scripts')

@include('layouts._flash')

</body>

</html>
