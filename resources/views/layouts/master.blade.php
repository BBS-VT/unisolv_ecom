<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" >

<head>
    <meta charset="utf-8" />
    <title> @yield('title') | Unisolv eCommerce </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('images/favicon.ico') }}">
    @include('layouts.head-css')
    @livewireStyles
</head>

@section('body')
<body data-sidebar="dark" data-layout-mode="light">
    @show

    <div id="layout-wrapper">
        @include('layouts.topbar')
        @include('layouts.sidebar')

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                    {{ $slot ?? '' }}
                </div>

            </div>

            @include('layouts.footer')
        </div>

    </div>

    @include('layouts.vendor-scripts')
    @livewireScripts
</body>

</html>
