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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/metisMenu/3.0.6/metisMenu.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app.min.css') }}" rel="stylesheet" type="text/css" />


</head>

<body class="dark-sidenav">

<div class="page-wrapper">


    <!-- Page Content-->
    <div class="page-content">
        <div class="container-fluid">
            @yield('content')
        </div>


    </div>
    <!-- end page content -->
</div>
<!-- end page-wrapper -->

<!-- jQuery  -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/metisMenu/3.0.6/metisMenu.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/node-waves/0.7.6/waves.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/simplebar/5.3.0/simplebar.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

@stack('custom-scripts')

<!-- App js -->
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>


</body>

</html>
