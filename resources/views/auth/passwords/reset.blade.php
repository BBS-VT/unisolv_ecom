@extends('layouts.master-without-nav')

@section('title')
    {{ __('Reset Password') }}
@endsection

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
                            <p class="text-white-50 mb-0">{{ trans('global.emailResetInstructions') }}</p>
                        </div>
                    </div>

                    <!-- Error Messages -->
                    @if($errors->any())
                        <div class="alert alert-danger mx-4 mt-4 mb-0 border-0" role="alert">
                            <div class="d-flex align-items-start">
                                <i class="mdi mdi-alert-circle me-2 mt-1"></i>
                                <div>
                                    <strong>Please correct the following errors:</strong>
                                    <ul class="mb-0 mt-2 ps-3">
                                        @foreach($errors->all() as $message)
                                            <li>{{ $message }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Success Message -->
                    @if(session('status'))
                        <div class="alert alert-success mx-4 mt-4 mb-0 border-0" role="alert">
                            <i class="mdi mdi-check-circle me-2"></i>{{ session('status') }}
                        </div>
                    @endif

                    <!-- Reset Form -->
                    <div class="card-body p-4">
                        <form action="{{ route('password.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="token" value="{{ request()->route('token') }}">
                            <input type="hidden" name="email" value="{{ request()->get('email') }}">

                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('New Password') }}</label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           placeholder="{{ __('Enter new password') }}"
                                           required
                                           autofocus>
                                    <button class="btn btn-light border" type="button" id="password-toggle">
                                        <i class="mdi mdi-eye-outline" id="password-toggle-icon"></i>
                                    </button>
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Minimum 8 characters recommended</small>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control @error('password_confirmation') is-invalid @enderror"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           placeholder="{{ __('Confirm new password') }}"
                                           required>
                                    <button class="btn btn-light border" type="button" id="confirm-toggle">
                                        <i class="mdi mdi-eye-outline" id="confirm-toggle-icon"></i>
                                    </button>
                                    @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    {{ __('Reset Password') }}
                                    <i class="mdi mdi-lock-reset ms-1"></i>
                                </button>
                            </div>
                        </form>

                        <div class="mt-4 text-center">
                            <p class="text-muted mb-0">
                                <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">
                                    <i class="mdi mdi-arrow-left me-1"></i>{{ __('Return to login') }}
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

    @push('scripts')
        <script>
            // Password toggle functionality
            document.getElementById('password-toggle')?.addEventListener('click', function() {
                const passwordInput = document.getElementById('password');
                const toggleIcon = document.getElementById('password-toggle-icon');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleIcon.classList.remove('mdi-eye-outline');
                    toggleIcon.classList.add('mdi-eye-off-outline');
                } else {
                    passwordInput.type = 'password';
                    toggleIcon.classList.remove('mdi-eye-off-outline');
                    toggleIcon.classList.add('mdi-eye-outline');
                }
            });

            // Confirm password toggle functionality
            document.getElementById('confirm-toggle')?.addEventListener('click', function() {
                const confirmInput = document.getElementById('password_confirmation');
                const toggleIcon = document.getElementById('confirm-toggle-icon');

                if (confirmInput.type === 'password') {
                    confirmInput.type = 'text';
                    toggleIcon.classList.remove('mdi-eye-outline');
                    toggleIcon.classList.add('mdi-eye-off-outline');
                } else {
                    confirmInput.type = 'password';
                    toggleIcon.classList.remove('mdi-eye-off-outline');
                    toggleIcon.classList.add('mdi-eye-outline');
                }
            });
        </script>
    @endpush
@endsection
