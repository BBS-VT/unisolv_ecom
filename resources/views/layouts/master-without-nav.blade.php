<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-sidebar="dark" data-sidebar-size="lg" data-preloader="disable" data-theme="default" data-bs-theme="light">

<head>

    <meta charset="utf-8" />
    <title> @yield('title') | {{ config('app.name', 'Unisolv CRM') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('build/images/favicon.ico') }}">

    @include('layouts.head-css')
    <style>
        .auth-header-box {
            background: linear-gradient(135deg, #1C75BC 0%, #2A3042 100%);
        }

        .card {
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .auth-logo {
            max-width: 100%;
            height: auto;
        }

        /* Optional: Add subtle animation on form focus */
        .form-control:focus {
            border-color: #1C75BC;
            box-shadow: 0 0 0 0.2rem rgba(28, 117, 188, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #1C75BC 0%, #2A3042 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2A3042 0%, #1C75BC 100%);
        }
    </style>
</head>

@yield('body')

@yield('content')

@include('layouts.vendor-scripts')
</body>

</html>
