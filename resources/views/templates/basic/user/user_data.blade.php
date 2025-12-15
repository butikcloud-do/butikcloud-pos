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
                                <div class="account-heading py-5 text-start">
                                    <h4>@lang('Complete Your Profile')</h4>
                                    <p>@lang('Complete your profile to start. It will also help you to secure your account and to protect your personal information.')</p>
                                </div>
                            </div>

                            <div class="account-body">
                                <form method="POST" action="{{ route('user.data.submit') }}">
                                    @csrf
                                    <div class="row gy-4">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">@lang('Username')</label>
                                                <input type="text" class="form-control form--control checkUser"
                                                    name="username" value="{{ old('username') }}" required>
                                                <span class="username-exists-error d-none"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">@lang('Country')</label>
                                                <select name="country" class="form-control form--control select2" required>
                                                    @foreach ($countries as $key => $country)
                                                        <option data-mobile_code="{{ $country->dial_code }}"
                                                            value="{{ $country->country }}" data-code="{{ $key }}">
                                                            {{ __($country->country) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">@lang('Mobile')</label>
                                                <div class="input-group ">
                                                    <span class="input-group-text mobile-code">

                                                    </span>
                                                    <input type="hidden" name="mobile_code">
                                                    <input type="hidden" name="country_code">
                                                    <input type="number" name="mobile" value="{{ old('mobile') }}"
                                                        class="form-control form--control checkUser" required>
                                                </div>
                                                <span class="mobile-exists-error d-none"></span>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label class="form-label">@lang('Address')</label>
                                            <input type="text" class="form-control form--control" name="address"
                                                value="{{ old('address') }}">
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label class="form-label">@lang('State')</label>
                                            <input type="text" class="form-control form--control" name="state"
                                                value="{{ old('state') }}">
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label class="form-label">@lang('Zip Code')</label>
                                            <input type="text" class="form-control form--control" name="zip"
                                                value="{{ old('zip') }}">
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label class="form-label">@lang('City')</label>
                                            <input type="text" class="form-control form--control" name="city"
                                                value="{{ old('city') }}">
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn--base w-100">
                                                @lang('Submit')
                                            </button>
                                        </div>
                                    </div>
                                </form>
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


@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush


@push('script')
    <script>
        "use strict";
        (function($) {

            @if ($mobileCode)
                $(`option[data-code={{ $mobileCode }}]`).attr('selected', '');
            @endif

            $('.select2').select2();

            $('select[name=country]').on('change', function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
                var value = $('[name=mobile]').val();
                var name = 'mobile';
                checkUser(value, name);
            });

            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));


            $('.checkUser').on('focusout', function(e) {
                var value = $(this).val();
                var name = $(this).attr('name')
                checkUser(value, name);
            });



            function checkUser(value, name) {
                var url = '{{ route('user.checkUser') }}';
                var token = '{{ csrf_token() }}';

                if (name == 'mobile') {
                    var mobile = `${value}`;
                    var data = {
                        mobile: mobile,
                        mobile_code: $('.mobile-code').text().substr(1),
                        _token: token
                    }
                }
                if (name == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
                }
                $.post(url, data, function(response) {
                    domModifyForExists(response, name);
                });
            }

            let usernameError = false;
            let mobileError = false;

            function domModifyForExists(response, name) {
                if (response.data == true) {
                    if (name == 'username') {
                        var message = `@lang('The username is not available.')`;
                        usernameError = true
                    } else {
                        var message = `@lang('The mobile number is already registered.')`;
                        mobileError = true;
                    }

                    $(`.${name}-exists-error`)
                        .html(`${message}`)
                        .removeClass('d-none')
                        .addClass("text--danger mt-1 d-block");
                } else {
                    $(`.${name}-exists-error`)
                        .empty()
                        .addClass('d-none')
                        .removeClass("text--danger mt-1 d-block");

                    if (name == 'username') {
                        usernameError = false;
                    } else {
                        mobileError = false;
                    }
                }

                if (!usernameError && !mobileError) {
                    $(`button[type=submit]`)
                        .attr('disabled', false)
                        .removeClass('disabled');
                } else {
                    $(`button[type=submit]`)
                        .attr('disabled', true)
                        .addClass('disabled');
                }
            }
        })(jQuery);
    </script>
@endpush
