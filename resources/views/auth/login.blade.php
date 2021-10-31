@extends('layouts.auth')

@section('content')
    <div class="container">
        <div class="row vh-100 d-flex justify-content-center">
            <div class="col-12 align-self-center">
                <div class="row">
                    <div class="col-lg-5 mx-auto">
                        <div class="card">
                            <div class="card-body p-0 auth-header-box">
                                <div class="text-center p-3">
                                    <a href="index.html" class="logo logo-admin">
                                        <img src="{{ asset('images/logo-sm-dark.png') }}" height="50" alt="logo" class="auth-logo">
                                    </a>
                                    <h4 class="mt-3 mb-1 font-weight-semibold text-white font-18">Unisolv CRM</h4>
                                    <p class="text-muted  mb-0">Sign in to continue to Unisolv CRM.</p>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <ul class="nav-border nav nav-pills" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active font-weight-semibold" data-toggle="tab" href="#LogIn_Tab" role="tab">Log In</a>
                                    </li>
                                    {{--<li class="nav-item">
                                        <a class="nav-link font-weight-semibold" data-toggle="tab" href="#Register_Tab" role="tab">Register</a>
                                    </li>--}}
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <div class="tab-pane active p-3" id="LogIn_Tab" role="tabpanel">
                                        <form class="form-horizontal auth-form" action="{{ route('login') }}" method="POST">
                                            @csrf
                                            <div class="form-group mb-2">
                                                <label for="email">{{ __('E-Mail Address') }}</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" id="email" placeholder="Enter username">
                                                    @error('email')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div><!--end form-group-->

                                            <div class="form-group mb-2">
                                                <label for="password">{{ __('Password') }}</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" placeholder="Enter password">
                                                    @error('password')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div><!--end form-group-->

                                            <div class="form-group row my-3">
                                                <div class="col-sm-6">
                                                    <div class="custom-control custom-switch switch-success">
                                                        <input type="checkbox" class="custom-control-input" name="remember" id="customSwitchSuccess" {{ old('remember') ? 'checked' : '' }}>
                                                        <label class="custom-control-label text-muted" for="customSwitchSuccess">{{ __('Remember Me') }}</label>
                                                    </div>
                                                </div><!--end col-->
                                                <div class="col-sm-6 text-right">
                                                    @if (Route::has('password.request'))
                                                        <a href="{{ route('password.request') }}" class="text-muted font-13"><i class="dripicons-lock"></i>
                                                            {{ __('Forgot Password?') }}</a>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group mb-0 row">
                                                <div class="col-12">
                                                    <button class="btn btn-primary btn-block waves-effect waves-light" type="submit">{{ __('Login') }} <i class="fas fa-sign-in-alt ml-1"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body bg-light-alt text-center">
                                <span class="text-muted d-none d-sm-inline-block"><a href="https://www.unisolv.co.za" target="_blank">Border Business Systems</a> © <?php echo date("Y"); ?></span>
                            </div>
                        </div><!--end card-->
                    </div><!--end col-->
                </div><!--end row-->
            </div><!--end col-->
        </div><!--end row-->
    </div><!--end container-->

@endsection
