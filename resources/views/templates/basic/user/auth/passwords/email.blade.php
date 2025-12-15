@extends($activeTemplate . 'layouts.app')
@section('app-content')
    <main class="page-wrapper account-wrapper-middle">
        <div class="container">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-12">
                    <section class="account verification-section">
                        <div class="account-content">
                            <div class="account-header text-center">
                                <a class="account-logo " href="{{ route('home') }}">
                                    <img src="{{ siteLogo('dark') }}" alt="logo">
                                </a>
                                <div class="account-heading py-3">
                                    <h2 class="account-heading__title">
                                        {{ __($pageTitle) }}
                                    </h2>
                                </div>
                            </div>
                            <div class="account-body">
                                <div class="mb-4">
                                    <div class="alert alert--warning" role="alert">
                                        <p>@lang('To recover your account please provide your email or username to find your account.')</p>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('user.password.email') }}" class="verify-gcaptcha">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label class="form-label">@lang('Email or Username')</label>
                                        <input type="text" class="form-control form--control" name="value"
                                            value="{{ old('value') }}" required autofocus="off">
                                    </div>
                                    <x-captcha />
                                    <div class="form-group">
                                        <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>
@endsection
