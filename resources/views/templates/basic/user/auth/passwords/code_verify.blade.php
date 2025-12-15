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
                                        <p>@lang('A 6 digit verification code sent to your email address') : {{ showEmailAddress($email) }}</p>
                                    </div>
                                </div>
                                <form action="{{ route('user.password.verify.code') }}" method="POST" class="submit-form">
                                    @csrf
                                    <input type="hidden" name="email" value="{{ $email }}">
                                    @include($activeTemplate . 'partials.verification_code')
                                    <div class="form-group mb-4">
                                        <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                                    </div>
                                    <div class="form-group mb-4">
                                        @lang('Please check including your Junk/Spam Folder. if not found, you can')
                                        <a href="{{ route('user.password.request') }}">@lang('Try to send again')</a>
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

@push('style')
    <style>
        .verification-section {
            max-width: 505px;
            margin: auto;
        }
    </style>
@endpush