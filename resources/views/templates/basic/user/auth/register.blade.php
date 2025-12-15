@php
    $authContent = getContent('login_register.content', true);
    $authElements = getContent('login_register.element', false, null, true);
@endphp
@extends($activeTemplate . 'layouts.app')
@section('app-content')
    <main class="page-wrapper">
        @if (gs('registration'))
            <div class="container">
                <div class="row ">
                    <div class="col-lg-6">
                        <section class="account h-100">
                            <div class="account-content">
                                <div class="account-header text-center">
                                    <a class="account-logo" href="{{ route('home') }}">
                                        <img src="{{ siteLogo('dark') }}" alt="logo">
                                    </a>
                                    <div class="account-heading py-3">
                                        <h2 class="account-heading__title">
                                            {{ __($authContent->data_values->register_heading) }}
                                        </h2>
                                    </div>
                                </div>
                                <div class="account-body">
                                    @include($activeTemplate . 'partials.social_login')
                                    <form class="account-form verify-gcaptcha" action="{{ route('user.register') }}"
                                        method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-sm-6 mb-4">
                                                <label class="form--label required">@lang('First name')</label>
                                                <input class="form-control form--control" type="text"
                                                    placeholder="@lang('Enter first name')" value="{{ old('firstname') }}"
                                                    name="firstname" required>
                                            </div>

                                            <div class="col-sm-6 mb-4">
                                                <label class="form--label required">@lang('Last name')</label>
                                                <input class="form-control form--control" type="text"
                                                    placeholder="@lang('Enter last name')" value="{{ old('lastname') }}"
                                                    name="lastname" required>
                                            </div>
                                            <div class="col-sm-12 mb-4">
                                                <label class="form--label required">@lang('Email')</label>
                                                <input class="form-control form--control checkUser" type="email"
                                                    placeholder="@lang('Enter email')" value="{{ old('email') }}"
                                                    name="email" required>
                                                <span class="exists-error d-none fs-14"></span>
                                            </div>
                                            <div class="col-sm-6 mb-4">
                                                <div class="form-field form-field--password">
                                                    <label class="form--label required">@lang('Password')</label>
                                                    <div class="input-group input--group input--group-password">
                                                        <input class="form-control form--control" type="password"
                                                            placeholder="@lang('Enter password')" value="{{ old('password') }}"
                                                            name="password" required>
                                                        <span class="input-group-text input-group-btn">
                                                            <i class="far fa-eye-slash"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <x-strong-password />
                                            </div>
                                            <div class="col-sm-6 mb-4">
                                                <div class="form-field form-field--password">
                                                    <label class="form--label required">@lang('Confirm Password')</label>
                                                    <div class="input-group input--group input--group-password">
                                                        <input class="form-control form--control" type="password"
                                                            placeholder="@lang('Confirm your password')"
                                                            value="{{ old('password_confirmation') }}"
                                                            name="password_confirmation" required>
                                                        <span class="input-group-text input-group-btn">
                                                            <i class="far fa-eye-slash"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <x-captcha />

                                            @if (gs('agree'))
                                                @php
                                                    $policyPages = getContent(
                                                        'policy_pages.element',
                                                        false,
                                                        orderById: true,
                                                    );
                                                @endphp

                                                <div class="col-sm-12">
                                                    <div class="form--check gradient">
                                                        <input type="checkbox" id="agree" @checked(old('agree'))
                                                            name="agree" class="form-check-input" required>

                                                        <label for="agree" class="form-check-label fs-14">
                                                            @lang('I agree with')
                                                            @foreach ($policyPages as $policy)
                                                                <a href="{{ route('policy.pages', $policy->slug) }}"
                                                                    target="_blank" class="fs-14 ms-1">
                                                                    {{ __($policy->data_values->title) }}
                                                                </a>
                                                                @if (!$loop->last)
                                                                    ,
                                                                @endif
                                                            @endforeach
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <button class="w-100 btn btn--base mt-4" id="recaptcha" type="submit">
                                            @lang('Sign Up')
                                        </button>
                                    </form>
                                    <p class="account-info mt-2">
                                        @lang('Already have an account?')
                                        <a href="{{ route('user.login') }}">@lang('Login now')</a>
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
        @else
            @include($activeTemplate . 'partials.registration_disabled')
        @endif
    </main>
@endsection


@if (gs('registration'))
    @push('script')
        <script>
            "use strict";
            (function($) {

                $('.checkUser').on('focusout', function(e) {
                    var url = "{{ route('user.checkUser') }}";
                    var value = $(this).val();
                    var token = '{{ csrf_token() }}';

                    var data = {
                        email: value,
                        _token: token
                    }

                    $.post(url, data, function(response) {
                        if (response.data == true) {
                            $(".exists-error").html(`
                                        @lang('You’re already part of our community!')
                                        <a class="ms-1" href="{{ route('user.login') }}">@lang('Login now')</a>
                                    `).removeClass('d-none').addClass("text--danger mt-1 d-block");
                            $(`button[type=submit]`).attr('disabled', true).addClass('disabled');
                        } else {
                            $(".exists-error").empty().addClass('d-none').removeClass(
                                "text--danger mt-1 d-block");
                            $(`button[type=submit]`).attr('disabled', false).removeClass('disabled');
                        }
                    });
                });
            })(jQuery);
        </script>
    @endpush
@endif
