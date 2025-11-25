<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name', 'Unisolv eCommerce') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">

    <link rel="stylesheet" href="{{ URL::asset('build/css/bootstrap.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('print/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('print/css/simple-print.css') }}">

</head>

<body>
    @yield('content')

    <script src="{{ asset('print/js/jquery.min.js') }}"></script>
    <script src="{{ asset('print/js/jspdf.min.js') }}"></script>
    <script src="{{ asset('print/js/html2canvas.min.js') }}"></script>
    <script src="{{ asset('print/js/main.js') }}"></script>

</body>

</html>
