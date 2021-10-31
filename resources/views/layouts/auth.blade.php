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

    <!-- App css -->
    <link href="{{ asset('/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    @stack('style')

</head>

<body class="account-body accountbg">

<!-- Log In page -->
@yield('content')
<!-- End Log In page -->

<!-- jQuery  -->
<script src="{{ asset('/js/jquery.min.js') }}"></script>
<script src="{{ asset('/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('/js/waves.js') }}"></script>
<script src="{{ asset('/js/feather.min.js') }}"></script>
<script src="{{ asset('/js/simplebar.min.js') }}"></script>
@stack('custom-scripts')

</body>

</html>
