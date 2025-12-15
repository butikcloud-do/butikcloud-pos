@extends($activeTemplate . 'layouts.master')
@section('panel')

    <form action="{{ route('user.staff.update', $staff->id) }}" method="POST" id="staff-form" class="no-submit-loader">
        @csrf
        <div class="row responsive-row">
            <div class="col-12">
                <x-panel.ui.card>
                    <x-panel.ui.card.header>
                        <h4 class="card-title">@lang('Staff Information')</h4>
                    </x-panel.ui.card.header>

                    <x-panel.ui.card.body>
                        <div class="row gy-3">
                            <div class="col-sm-6 form-group">
                                <label class="form-label">@lang('First Name')</label>
                                <input type="text" class="form-control" name="firstname" placeholder="@lang('Enter firstname')" value="{{ $staff->firstname ?? old('firstname') }}" required>
                            </div>

                            <div class="col-sm-6 form-group">
                                <label class="form-label">@lang('Last Name')</label>
                                <input type="text" class="form-control" name="lastname" placeholder="@lang('Enter lastname')" required value="{{ $staff->lastname ?? old('lastname') }}">
                            </div>

                            <div class="col-sm-6 form-group">
                                <label class="form-label">@lang('Username')</label>
                                <input type="text" class="form-control checkUser" name="username" placeholder="@lang('Enter username')" required value="{{ $staff->username ?? old('username') }}">
                                <span class="username-exists-error d-none"></span>
                            </div>

                            <div class="col-sm-6 form-group">
                                <label class="form-label">@lang('Email Address')</label>
                                <input type="email" class="form-control checkUser" name="email" placeholder="@lang('Enter email')" required value="{{ $staff->email ?? old('email') }}">
                                <span class="email-exists-error d-none"></span>
                            </div>

                            <div class="col-md-6 form-group">
                                <label class="form-label">@lang('Country')</label>
                                <select name="country" class="form-control select2" required>
                                    @foreach ($countries as $key => $country)
                                        <option data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}" data-code="{{ $key }}" @if(isset($staff->country_name) && $staff->country_name == $country->country) selected @endif>
                                            {{ __($country->country) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 form-group">
                                <label class="form-label">@lang('Mobile')</label>
                                <div class="input-group input--group">
                                    <span class="input-group-text mobile-code"></span>
                                    <input type="hidden" name="mobile_code">
                                    <input type="hidden" name="country_code">
                                    <input type="number" name="mobile" value="{{ $staff->mobile ?? old('mobile') }}" class="form-control checkUser" required>
                                </div>
                                <span class="mobile-exists-error d-none"></span>
                            </div>

                            <div class="col-sm-6 form-group">
                                <label class="form-label">@lang('City')</label>
                                <input type="text" class="form-control" name="city" placeholder="@lang('Enter city')" value="{{ $staff->city ?? old('city') }}">
                            </div>

                            <div class="col-sm-6 form-group">
                                <label class="form-label">@lang('State')</label>
                                <input type="text" class="form-control" name="state" placeholder="@lang('Enter state')" value="{{ $staff->state ?? old('state') }}">
                            </div>

                            <div class="col-sm-6 form-group">
                                <label class="form-label">@lang('Zip Code')</label>
                                <input type="text" class="form-control" name="zip" placeholder="@lang('Enter zip')" value="{{ $staff->zip ?? old('zip') }}">
                            </div>

                            <div class="col-sm-6 form-group">
                                <label class="form-label">@lang('Address')</label>
                                <input type="text" class="form-control" name="address" placeholder="@lang('Enter address')" value="{{ $staff->address ?? old('address') }}">
                            </div>
                        </div>
                    </x-panel.ui.card.body>
                </x-panel.ui.card>
            </div>

            <div class="col-12">
                <div class="d-flex gap-3 flex-wrap justify-content-end">
                    <button type="submit" class="btn btn--primary btn-large">
                        <span class="me-1"><i class="fa fa-save"></i></span>
                        @lang('Save Staff')
                    </button>
                </div>
            </div>
        </div>
    </form>

@endsection


@push('breadcrumb-plugins')
    <x-back_btn route="{{ route('user.staff.list') }}" text="Staff List" />
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
@endpush

@push('script')
    <script>
        "use strict";
        (function ($) {
            @if ($mobileCode)
                $(`option[data-code={{ $mobileCode }}]`).attr('selected', '');
            @endif

            $('.select2').select2();

            $('select[name=country]').on('change', function () {
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

            $('.checkUser').on('focusout', function (e) {
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
                if (name == 'email') {
                    var data = {
                        email: value,
                        _token: token
                    }
                }
                $.post(url, data, function (response) {
                    domModifyForExists(response, name);
                });
            }

            let usernameError = false;
            let mobileError = false;

            function domModifyForExists(response, name) {
                if (response.data == true) {
                    if (name == 'username') {
                        var message = `@lang('Username already exists')`;
                        usernameError = true
                    } else if (name == 'email') {
                        var message = `@lang('Email already exists')`;
                        usernameError = true
                    } else {
                        var message = `@lang('Mobile already exists')`;
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
                    } else if (name == 'email') {
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