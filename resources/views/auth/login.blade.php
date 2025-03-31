@extends('layouts.master-without-nav')
@section('title')
    @lang('global.login')
@endsection
@section('content')


    <section class="auth-page-wrapper py-5 position-relative d-flex align-items-center justify-content-center min-vh-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="card mb-0">
                        <div class="row g-0 align-items-center">
                            <div class="col-xxl-5">
                                <div class="card auth-card bg-secondary h-100 border-0 shadow-none d-none d-sm-block mb-0">
                                    <div class="card-body py-5 d-flex justify-content-between flex-column">
                                        <div class="text-center">
                                            <h3 class="text-white">Start your journey with us.</h3>
                                            <p class="text-white opacity-75 fs-base"></p>
                                        </div>

                                        <div class="auth-effect-main my-5 position-relative rounded-circle d-flex align-items-center justify-content-center mx-auto">
                                            <div class="effect-circle-1 position-relative mx-auto rounded-circle d-flex align-items-center justify-content-center">
                                                <div class="effect-circle-2 position-relative mx-auto rounded-circle d-flex align-items-center justify-content-center">
                                                    <div class="effect-circle-3 mx-auto rounded-circle position-relative text-white fs-4xl d-flex align-items-center justify-content-center">
                                                        Welcome to <span class="text-primary ms-1">Unisolv</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <ul class="auth-user-list list-unstyled">
                                                <li>
                                                    <div class="avatar-sm d-inline-block">
                                                        <div class="avatar-title bg-white shadow-lg overflow-hidden rounded-circle">
                                                            <img src="{{ URL::asset('build/images/users/avatar-1.jpg') }}" alt="" class="img-fluid">
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="avatar-sm d-inline-block">
                                                        <div class="avatar-title bg-white shadow-lg overflow-hidden rounded-circle">
                                                            <img src="{{ URL::asset('build/images/users/avatar-2.jpg') }}" alt="" class="img-fluid">
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="avatar-sm d-inline-block">
                                                        <div class="avatar-title bg-white shadow-lg overflow-hidden rounded-circle">
                                                            <img src="{{ URL::asset('build/images/users/avatar-3.jpg') }}" alt="" class="img-fluid">
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="avatar-sm d-inline-block">
                                                        <div class="avatar-title bg-white shadow-lg overflow-hidden rounded-circle">
                                                            <img src="{{ URL::asset('build/images/users/avatar-4.jpg') }}" alt="" class="img-fluid">
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="avatar-sm d-inline-block">
                                                        <div class="avatar-title bg-white shadow-lg overflow-hidden rounded-circle">
                                                            <img src="{{ URL::asset('build/images/users/avatar-5.jpg') }}" alt="" class="img-fluid">
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="text-center">
                                            <p class="text-white opacity-75 mb-0 mt-3">
                                                &copy; <script>
                                                    document.write(new Date().getFullYear())

                                                </script> <a href="http://www.unisolv.co.za" class="text-white">Border Business Systems</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xxl-6 mx-auto">
                                <div class="card mb-0 border-0 shadow-none mb-0">
                                    <div class="card-body p-sm-5 m-lg-4">
                                        <div class="text-center mt-5">
                                            <h5 class="fs-3xl">Welcome Back</h5>
                                            <p class="text-muted">Sign in to continue to Unisolv.</p>
                                        </div>
                                        <div class="p-2 mt-5">
                                            <form action="{{ route('login') }}" method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">{{ __('Username') }} <span class="text-danger">*</span></label>
                                                    <div class="position-relative ">
                                                        <input type="text" class="form-control password-input @error('email') is-invalid @enderror" id="email" name="email" placeholder="Enter username">
                                                        @error('email')
                                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <div class="float-end">
                                                        @if (Route::has('password.request'))
                                                            <a href="{{ route('password.request') }}" class="text-muted">{{ __('Forgot Password?') }}</a>
                                                        @endif
                                                    </div>
                                                    <label class="form-label" for="password">{{ __('Password') }} <span class="text-danger">*</span></label>
                                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                                        <input type="password" class="form-control pe-5 password-input @error('password') is-invalid @enderror" placeholder="Enter password" name="password" id="password">
                                                        @error('password')
                                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                                    </div>
                                                </div>

                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="" id="auth-remember-check {{ old('remember') ? 'checked' : '' }}">
                                                    <label class="form-check-label" for="auth-remember-check">{{ __('Remember Me') }}</label>
                                                </div>

                                                <div class="mt-4">
                                                    <button class="btn btn-primary w-100" type="submit">{{ __('Login') }}</button>
                                                </div>

                                                <div class="mt-4 pt-2 text-center">
                                                    <div class="signin-other-title position-relative">
                                                        <h5 class="fs-sm mb-4 title"></h5>
                                                    </div>
                                                    <div class="pt-2 hstack gap-2 justify-content-center">

                                                    </div>
                                                </div>
                                            </form>

                                            <div class="text-center mt-5">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>

        </div>

    </section>
@endsection
@section('script')

    <script src="{{ URL::asset('build/js/pages/password-addon.init.js') }}"></script>
    <script src="{{ URL::asset('build/libs/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/pages/swiper.init.js') }}"></script>

@endsection
