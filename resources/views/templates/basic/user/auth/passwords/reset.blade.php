@extends($activeTemplate . 'layouts.app')
@section('app-content')
    <main class="page-wrapper">
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
                                        <p>@lang('Your account is verified successfully. Now you can change your password. Please enter a strong password and don\'t share it with anyone.')</p>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('user.password.update') }}">
                                    @csrf
                                    <input type="hidden" name="email" value="{{ $email }}">
                                    <input type="hidden" name="token" value="{{ $token }}">
                                    <div class="form-group mb-4">
                                        <label class="form-label">@lang('Password')</label>
                                        <input type="password"
                                            class="form-control form--control @gs('secure_password')
secure-password
@endgs"
                                            name="password" required>
                                        <x-strong-password />
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="form-label">@lang('Confirm Password')</label>
                                        <input type="password" class="form-control form--control"
                                            name="password_confirmation" required>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn--base w-100"> @lang('Submit')</button>
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



@gs('secure_password')
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endgs
