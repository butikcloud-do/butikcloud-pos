@extends($activeTemplate . 'layouts.app')
@section('app-content')
    <main class="page-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <section class="account h-100 verification-section">
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
                                        <p>
                                            @lang('A 6 digit verification code sent to your email address'):
                                            {{ showEmailAddress(auth()->user()->email) }}
                                        </p>
                                    </div>
                                </div>
                                <form action="{{ route('user.verify.email') }}" method="POST" class="submit-form">
                                    @csrf

                                    @include($activeTemplate . 'partials.verification_code')

                                    <div class="mb-3">
                                        <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                                    </div>

                                    <div class="mb-3">
                                        <p>
                                            @lang('If you don\'t get any code'), <span class="countdown-wrapper">@lang('try again after') <span
                                                    id="countdown" class="fw-bold">--</span> @lang('seconds')</span> <a
                                                href="{{ route('user.send.verify.code', 'email') }}"
                                                class="try-again-link d-none"> @lang('Try again')</a>
                                        </p>
                                        <a href="{{ route('user.logout') }}">@lang('Logout')</a>
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



@push('script')
    <script>
        var distance = Number("{{ @$user->ver_code_send_at->addMinutes(2)->timestamp - time() }}");
        var x = setInterval(function() {
            distance--;
            document.getElementById("countdown").innerHTML = distance;
            if (distance <= 0) {
                clearInterval(x);
                document.querySelector('.countdown-wrapper').classList.add('d-none');
                document.querySelector('.try-again-link').classList.remove('d-none');
            }
        }, 1000);
    </script>
@endpush


@push('style')
    <style>
        .verification-section {
            max-width: 505px;
            margin: auto;
        }
    </style>
@endpush