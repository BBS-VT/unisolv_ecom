@extends('layouts.master-without-nav')

@section('content')
    <div class="container">
        <div class="row vh-100 d-flex justify-content-center align-items-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                <div class="card shadow-lg border-0">
                    <!-- Header with gradient -->
                    <div class="card-body p-0 auth-header-box">
                        <div class="text-center p-4">
                            <a href="{{ route('home') }}" class="logo logo-admin">
                                <img src="{{ asset('images/unisolv_light.png') }}" height="50" alt="logo" class="auth-logo mb-3">
                            </a>
                            <h4 class="mt-2 mb-2 fw-semibold text-white">{{ trans('global.reset_password') }}</h4>
                            <p class="text-white-50 mb-0">{{ trans('global.emailInstructions') }}</p>
                        </div>
                    </div>

                    <!-- Success Message -->
                    @if(session('status'))
                        <div class="alert alert-success mx-4 mt-4 mb-0 border-0" role="alert">
                            <i class="mdi mdi-check-circle me-2"></i>{{ session('status') }}
                        </div>
                    @endif

                    <!-- Reset Form -->
                    <div class="card-body p-4">
                        <form class="auth-form" method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label" for="email">{{ trans('global.email') }}</label>
                                <input id="email"
                                       type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       name="email"
                                       required
                                       autocomplete="email"
                                       autofocus
                                       placeholder="{{ trans('global.login_email') }}"
                                       value="{{ old('email') }}">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    {{ trans('global.send_password') }}
                                    <i class="mdi mdi-email-send ms-1"></i>
                                </button>
                            </div>
                        </form>

                        <div class="mt-4 text-center">
                            <p class="text-muted mb-0">
                                Remember your password?
                                <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">
                                    Sign in here
                                </a>
                            </p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer bg-light text-center py-3 border-0">
                        <p class="text-muted mb-0 small">
                            <a href="https://www.unisolv.co.za" target="_blank" class="text-decoration-none">Border Business Systems</a>
                            © {{ date('Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
