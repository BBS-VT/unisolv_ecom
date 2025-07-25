<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - B2B Shop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.cdnfonts.com/css/amazon-ember" rel="stylesheet">
    <link href="{{ URL::asset('shop/style.css') }}" rel="stylesheet" type="text/css" />

    @stack('styles')

</head>
<body>
    @include('shop.components.navbar')

        <main class="amazon-main">
            @yield('content')
        </main>

        @include('shop.components.footer')

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
        <div id="cartToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <i class="bi bi-cart-check me-2"></i>
                <strong class="me-auto">Cart Update</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                Item has been added to your cart.
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="miniCartOffcanvas" style="width: 400px;">
        <div id="miniCartContent">
            <div class="d-flex justify-content-center align-items-center h-100">
                <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        console.log('jQuery loaded:', typeof $ !== 'undefined' ? 'YES' : 'NO');
        if (typeof $ !== 'undefined') {
            console.log('jQuery version:', $.fn.jquery);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/bc0680dc86.js" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {
            // Global search functionality
            $('#globalSearch').on('keypress', function(e) {
                if (e.which === 13) {
                    const query = $(this).val();
                    if (query.trim()) {
                        window.location.href = '{{ route("shop.products.index") }}?search=' + encodeURIComponent(query);
                    }
                }
            });

        });
    </script>
    @stack('scripts')

    @guest
        <div class="modal fade" id="shopLoginModal" tabindex="-1" aria-labelledby="shopLoginModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="shopLoginModalLabel">
                            <i class="fas fa-sign-in-alt me-2"></i>{{ __('Customer Login') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <h4>{{ __('Welcome Back!') }}</h4>
                            <p class="text-muted">{{ __('Please login to your account to continue shopping.') }}</p>>
                        </div>

                        <form method="POST" action="{{ route('login') }}" id="shop-login-form">
                            @csrf
                            <input type="hidden" name="redirect_to_shop" value="1">

                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('Email Address') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                           name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('Password') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password">
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-1"></i> {{ __('Login') }}
                                </button>
                            </div>

                            @if (Route::has('password.request'))
                                <div class="text-center mt-3">
                                    <a href="{{ route('password.request') }}" class="text-decoration-none">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const togglePasswordButtons = document.querySelectorAll('.toggle-password');
                togglePasswordButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const passwordInput = this.closest('.input-group').querySelector('input');
                        const icon = this.querySelector('i');

                        if (passwordInput.type === 'password') {
                            passwordInput.type = 'text';
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        } else {
                            passwordInput.type = 'password';
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        }
                    });
                });
            });
        </script>
    @endguest
</body>
</html>
