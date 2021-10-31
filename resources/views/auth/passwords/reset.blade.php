@extends('layouts.auth')

@section('title')
    {{ __('Reset Password') }}
@endsection

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
                                    <p class="text-muted mb-0">{{ trans('global.emailResetInstructions') }} </p>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('password.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ request()->route('token') }}" >
                                    <input type="hidden" name="email" value="{{ request()->get('email') }}" required>
                                    <div class="mb-3">
                                        <label for="inputPassword" class="form-label">{{ __('Password') }}</label>
                                        <input type="password" class="form-control" id="inputPassword" name="password" placeholder="{{ __('Enter password') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="inputConfirmPassword" class="form-label">{{ __('Confirm Password') }}</label>
                                        <input type="password" class="form-control" id="inputConfirmPassword" name="password_confirmation" placeholder="{{ __('Confirm password') }}" required>
                                    </div>
                                    <div class="form-group mb-0 row">
                                        <div class="col-12 mt-2">
                                            <button type="submit" class="btn btn-primary w-100 waves-effect waves-light">
                                                {{ __('Reset Password') }} <i class="fas fa-sign-in-alt ms-1"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <p class="text-muted mb-0 mt-3"><a href="{{ route('login') }}" class="text-primary ms-2">{{ __('Return to login') }} </a></p>
                            </div>
                            <div class="card-body bg-light-alt text-center">
                                <span class="text-muted d-none d-sm-inline-block"><a href="https://www.unisolv.co.za" target="_blank">Border Business Systems</a> © <?php echo date("Y"); ?></span>
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
    </div>
@endsection
