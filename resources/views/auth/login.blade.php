@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/auth.css') }}">
@endpush

@section('content')
    <div id="auth">
        <div class="row h-100 g-0">
            <div class="col-lg-5 col-12">
                <div id="auth-left" class="px-4 px-lg-5 py-5">
                    <div class="auth-logo mb-4">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('assets/compiled/svg/logo.svg') }}" alt="Logo">
                        </a>
                    </div>

                    <h1 class="auth-title">{{ __('Login') }}</h1>
                    <p class="auth-subtitle mb-5">{{ __('Log in with your credentials to continue.') }}</p>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group position-relative has-icon-left mb-4">
                            <input id="email" type="email"
                                class="form-control form-control-xl @error('email') is-invalid @enderror" name="email"
                                value="{{ old('email') }}" placeholder="{{ __('Email Address') }}" required
                                autocomplete="email" autofocus>
                            <div class="form-control-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <input id="password" type="password"
                                class="form-control form-control-xl @error('password') is-invalid @enderror" name="password"
                                placeholder="{{ __('Password') }}" required autocomplete="current-password">
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-check form-check-lg d-flex align-items-end mb-3">
                            <input class="form-check-input me-2" type="checkbox" name="remember" id="remember"
                                {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label text-gray-600" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-4">
                            {{ __('Login') }}
                        </button>
                    </form>

                    <div class="text-center mt-5 text-lg fs-6">
                        @if (Route::has('register'))
                            <p class="text-gray-600">
                                {{ __('Don\'t have an account?') }}
                                <a href="{{ route('register') }}" class="font-bold">{{ __('Sign up') }}</a>
                            </p>
                        @endif

                        @if (Route::has('password.request'))
                            <p>
                                <a class="font-bold"
                                    href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/static/js/initTheme.js') }}"></script>
@endpush
