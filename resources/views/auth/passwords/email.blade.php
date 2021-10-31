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
                                    <a href="{{ route('home') }}" class="logo logo-admin">
                                        <img src="{{ asset('images/logo-sm-dark.png') }}" height="50" alt="logo" class="auth-logo">
                                    </a>
                                    <h4 class="mt-3 mb-1 fw-semibold text-white font-18">{{ trans('global.reset_password') }} - {{ trans('panel.site_title') }}</h4>
                                    <p class="text-muted mb-0">{{ trans('global.emailInstructions') }} </p>
                                </div>
                                @if(session('status'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif
                            </div>
                            <div class="card-body">
                                <form class="form-horizontal auth-form" method="POST" action="{{ route('password.email') }}">
                                    @csrf

                                    <div class="form-group mb-2">
                                        <label class="form-label" for="username">{{ trans('global.email') }}</label>
                                        <div class="input-group">
                                            <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" required autocomplete="email" autofocus placeholder="{{ trans('global.login_email') }}" value="{{ old('email') }}">

                                            @if($errors->has('email'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('email') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group mb-0 row">
                                        <div class="col-12 mt-2">
                                            <button type="submit" class="btn btn-primary w-100 waves-effect waves-light">
                                                {{ trans('global.send_password') }} <i class="fas fa-sign-in-alt ms-1"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <p class="text-muted mb-0 mt-3">Remember It ?  <a href="{{ route('login') }}" class="text-primary ms-2">Sign in here</a></p>
                            </div>
                            <div class="card-body bg-light-alt text-center">
                                <span class="text-muted d-none d-sm-inline-block"><a href="https://www.unisolv.co.za" target="_blank">Border Business Systems</a> © <?php echo date("Y"); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('status'))
                <div class="alert alert-success">{{ __('A new password reset link has been sent to your email address.') }}</div>
            @endif
        </div>
    </div>
@endsection
