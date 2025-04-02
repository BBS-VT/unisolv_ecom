<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="horizontal" data-sidebar="dark" data-sidebar-size="lg" data-preloader="disable" data-theme="material" data-topbar="dark" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Unisolv CRM - Catalog</title>

    <link rel="shortcut icon" href="{{ URL::asset('build/images/favicon.ico') }}">
    @include('layouts.head-css')
</head>

<body>
    <div id="layout-wrapper">
        @include('layouts.front.topbar')
        @include('layouts.front.sidebar')

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
            @include('layouts.footer')
        </div>
    </div>
    @include('layouts.vendor-scripts')

</body>

</html>
