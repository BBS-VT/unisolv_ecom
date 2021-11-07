<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{ config('app.name', 'Unisolv CRM') }}</title>

    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <link href="css/landing/bootstrap.min.css" rel="stylesheet">
    <link href="css/landing/fontawesome-all.min.css" rel="stylesheet">
    <link href="css/landing/styles.css" rel="stylesheet">

    <!-- Favicon  -->
    <link rel="icon" href="{{ asset('images/favicon.ico') }}">
</head>
<body data-bs-spy="scroll" data-bs-target="#navbarExample">

    <!-- Navigation -->
    <nav id="navbarExample" class="navbar navbar-expand-lg fixed-top navbar-light" aria-label="Main navigation">
        <div class="container">

            <!-- Image Logo -->
            <!-- <a class="navbar-brand logo-image" href="index.html"><img src="images/logo.svg" alt="alternative"></a> -->

            <!-- Text Logo - Use this if you don't have a graphic logo -->
            <a class="navbar-brand logo-text" href="index.html">Unisolv CRM</a>

            <button class="navbar-toggler p-0 border-0" type="button" id="navbarSideCollapse" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="navbar-collapse offcanvas-collapse flaot-right" id="navbarsExampleDefault">
                <ul class="navbar-nav ms-auto navbar-nav-scroll">

                </ul>
                <span class="nav-item">
                    <a class="btn-outline-sm" href="{{ route('login') }}">Log in</a>
                </span>
            </div>
        </div>
    </nav>

    <header id="header" class="header">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="text-container">
                        <h1 class="h1-large"><span class="replace-me">Unisolv CRM</span></h1><br>
                        <p class="p-large">a Stock-Aware CRM, perfect for growing Wholesale, Distributor & Manufacturing businesses</p><br>
                        <a class="btn-solid-lg" href="{{ route('login') }}">Log in</a><br>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="image-container">
                        <img class="img-fluid" src="{{ asset('images/header-illustration.svg') }}" alt="alternative">
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="copyright">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <p class="p-small">Copyright © <a href="https://www.unisolv.co.za">Border Business Systems</a></p>
                </div> <!-- end of col -->

                <div class="col-lg-6">
                    <p class="p-small"></p>
                </div>
            </div>
        </div>
    </div>

    <script src="js/landing/bootstrap.min.js"></script>
    <script src="js/landing/scripts.js"></script>
</body>
</html>
