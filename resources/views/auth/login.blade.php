@extends('layouts.master-without-nav')

@push('scripts')

@endpush

@section('content')
    <div class="container">
        <div class="row vh-100 d-flex justify-content-center align-items-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                <div class="card shadow-lg border-0">
                    <!-- Header with gradient -->
                    <div class="card-body p-0 auth-header-box">
                        <div class="text-center p-4">
                            <a href="/" class="logo logo-admin">
                                <img src="{{ asset('images/unisolv_light.png') }}" height="50" alt="logo" class="auth-logo mb-3">
                            </a>
                            <h4 class="mt-2 mb-2 fw-semibold text-white">Business Management Software</h4>
                            <p class="text-white-50 mb-0">Sign in to continue</p>
                        </div>
                    </div>

                    <!-- Login Form -->
                    <div class="card-body p-4">
                        <form class="auth-form" action="{{ route('login') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('E-Mail Address') }}</label>
                                <input type="text"
                                       class="form-control @error('email') is-invalid @enderror"
                                       name="email"
                                       id="email"
                                       placeholder="Enter your email"
                                       value="{{ old('email') }}"
                                       autofocus>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('Password') }}</label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           name="password"
                                           id="password"
                                           placeholder="Enter your password">
                                    <button class="btn btn-light border" type="button" id="password-toggle">
                                        <i class="mdi mdi-eye-outline" id="toggle-icon"></i>
                                    </button>
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="remember"
                                           id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-muted small">
                                        <i class="mdi mdi-lock-outline me-1"></i>{{ __('Forgot Password?') }}
                                    </a>
                                @endif
                            </div>

                            <div class="d-grid">
                                <button class="btn btn-primary btn-lg" type="submit">
                                    {{ __('Sign In') }}
                                    <i class="mdi mdi-login ms-1"></i>
                                </button>
                            </div>
                        </form>
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
                const toggleIcon = document.getElementById('toggle-icon');

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
        </script>
    @endpush
@endsection
