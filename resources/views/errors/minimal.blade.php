<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <title>@yield('title')</title>

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('images/favicon.ico')}}">

        <!-- App css -->
        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('css/app.min.css') }}" rel="stylesheet" type="text/css" />
    </head>
    <body class="account-body accountbg">
        <div class="container">
            <div class="row vh-100 d-flex justify-content-center">
                <div class="col-12 align-self-center">
                    <div class="row">
                        <div class="col-lg-5 mx-auto">
                            <div class="card">
                                <div class="card-body p-0 auth-header-box">
                                    <div class="text-center p-3">
                                        <a href="{{ route('home') }}" class="logo logo-admin">
                                            <img src="{{ asset('images/logo-sm-dark.png') }}" height="50" alt="logo" class="auth-logo">
                                        </a>
                                        <h4 class="mt-3 mb-1 font-weight-semibold text-white font-18">An Error has occurred!</h4>
                                        <p class="text-muted  mb-0"></p>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="ex-page-content text-center">
                                        <img src="{{ asset('images/error.svg') }}" alt="0" class="" height="170">
                                        <h1 class="mt-5 mb-4">@yield('code')</h1>
                                        <h5 class="font-16 text-muted mb-5">@yield('message')</h5>
                                    </div>
                                    <a class="btn btn-primary btn-block waves-effect waves-light" href="{{ route('home') }}">Back to Dashboard <i class="fas fa-redo ml-1"></i></a>
                                </div>
                                <div class="card-body bg-light-alt text-center">
                                    <span class="text-muted d-none d-sm-inline-block"></span>
                                </div>
                            </div><!--end card-->
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end col-->
            </div><!--end row-->
        </div>

    </body>
</html>
