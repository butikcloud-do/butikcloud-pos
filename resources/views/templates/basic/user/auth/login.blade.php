@php
    $authContent = getContent('login_register.content', true);
    $authElements = getContent('login_register.element', false, null, true);
@endphp
@extends($activeTemplate . 'layouts.app')
@section('app-content')
    <main class="page-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <section class="account h-100">
                        <div class="account-content">
                            <div class="account-header text-center">
                                <a class="account-logo h-50 w-50" href="{{ route('home') }}">
                                    <img src="{{ siteLogo('dark') }}" alt="logo">
                                </a>
                                <div class="account-heading py-3">
                                    <h2 class="account-heading__title">
                                        {{ __($authContent->data_values->login_heading) }}
                                    </h2>
                                </div>
                            </div>
                            <div class="account-body">
                                @include($activeTemplate . 'partials.social_login')

                                <form class="account-form verify-gcaptcha" method="POST"
                                    action="{{ route('user.login') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="form--group col-sm-12">
                                            <label class="form--label required">@lang('Username')</label>
                                            <input class="form-control form--control" type="text"
                                                placeholder="@lang('Enter username')" name="username"
                                                value="{{ old('username') }}" required>
                                        </div>
                                        <div class="form--group col-sm-12">
                                            <div class="form-field form-field--password">
                                                <label class="form--label required">@lang('Password')</label>
                                                <div class="input-group input--group input--group-password">
                                                    <input class="form-control form--control" type="password"
                                                        name="password" placeholder="@lang('Enter Password')">
                                                    <span class="input-group-text input-group-btn">
                                                        <i class="far fa-eye-slash"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form--group col-sm-12">
                                            <div class="account-form__extra d-flex justify-content-between gap-2">
                                                <div class="form--check">
                                                    <input class="form-check-input" type="checkbox" id="remember-me">
                                                    <label class="form-check-label"
                                                        for="remember-me">@lang('Remember Me')</label>
                                                </div>
                                                <a href="{{ route('user.password.request') }}"
                                                    class="account-form__forgot-link">@lang('Forgot Password?')</a>
                                            </div>
                                        </div>
                                        <x-captcha />
                                        <div class="col-12">
                                            <button class="w-100 btn btn--base" type="submit">
                                                @lang('Sign in')
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <p class="account-info mt-3">
                                    @lang("Don't have an account?")
                                    <a href="{{ route('user.register') }}">@lang('Create an account')</a>
                                </p>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="col-lg-6 d-lg-block d-none">
                    <div class="account-login__slider">
                        @foreach ($authElements as $authElement)
                            <div class="account-login__slider-item">
                                <img src="{{ frontendImage('login_register', $authElement->data_values->image ?? '', '1270x1580') }}"
                                    class="empty-message">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
