<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Unisolv eCommerce') }}</title>
    <link rel="stylesheet" href="{{ asset('print/css/style.css') }}">
</head>
<body>
    @yield('content')

    <script src="{{ asset('print/js/jquery.min.js') }}"></script>
    <script src="{{ asset('print/js/jspdf.min.js') }}"></script>
    <script src="{{ asset('print/js/html2canvas.min.js') }}"></script>
    <script src="{{ asset('print/js/main.js') }}"></script>
</body>
</html>
