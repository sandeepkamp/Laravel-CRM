<!DOCTYPE html>

<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="{{ url('assets') }}" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Forgot Password - {{ config('app.name') }}</title>

    <meta name="description" content="" />

    <link rel="icon" type="image/x-icon" href="{{ url('assets/img/logo.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ url('assets/vendor/fonts/materialdesignicons.css') }}" />

    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="{{ url('assets/vendor/libs/node-waves/node-waves.css') }}" />

    <link rel="stylesheet" href="{{ url('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ url('assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ url('assets/css/demo.css') }}" />

    <link rel="stylesheet" href="{{ url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <link rel="stylesheet" href="{{ url('assets/vendor/css/pages/page-auth.css') }}" />

    <script src="{{ url('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ url('assets/js/config.js') }}"></script>
    </head>

    <body>
        <div class="position-relative">
            <div class="authentication-wrapper authentication-basic container-p-y">
                <div class="authentication-inner py-4">
                    <!-- Logo -->
                    <div class="card p-2">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <div class="app-brand justify-content-center mt-5">
                            <a href="{{ url('/') }}" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">
                                    <img src="{{ url('assets/img/new-logo-final.webp') }}" width="200">
                                </span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <div class="card-body mt-2">
                            <h4 class="mb-2">{{ __('Reset Password') }}</h4>
                            <form id="formAuthentication" class="mb-3" method="POST" action="{{ route('password.update') }}">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                                    <label for="email">Email</label>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-floating form-floating-outline mb-3">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                    <label for="password">Password</label>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-floating form-floating-outline mb-3">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                    <label for="password-confirm">{{ __('Confirm Password') }}</label>
                                </div>
                                <button class="btn btn-primary d-grid w-100" type="submit">{{ __('Reset Password') }}</button>
                            </form>
                            <div class="text-center">
                                <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-chevron-left scaleX-n1-rtl mdi-24px"></i>
                                    Back to login
                                </a>
                            </div>
                        </div>
                    </div>
                    <img src="{{ url('assets/img/illustrations/tree-3.png') }}" alt="auth-tree" class="authentication-image-object-left d-none d-lg-block" />
                    <img src="{{ url('assets/img/illustrations/auth-basic-mask-light.png') }}" class="authentication-image d-none d-lg-block" alt="triangle-bg" data-app-light-img="illustrations/auth-basic-mask-light.png" data-app-dark-img="illustrations/auth-basic-mask-dark.png" />
                    <img src="{{ url('assets/img/illustrations/tree.png') }}" alt="auth-tree" class="authentication-image-object-right d-none d-lg-block" />
                </div>
            </div>
        </div>

        <script src="{{ url('assets/vendor/libs/jquery/jquery.js') }}"></script>
        <script src="{{ url('assets/vendor/libs/popper/popper.js') }}"></script>
        <script src="{{ url('assets/vendor/js/bootstrap.js') }}"></script>
        <script src="{{ url('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
        <script src="{{ url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
        <script src="{{ url('assets/vendor/js/menu.js') }}"></script>
        <script src="{{ url('assets/js/main.js') }}"></script>
    </body>
</html>