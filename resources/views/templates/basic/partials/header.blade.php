<header class="header" id="header">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="navbar-brand logo" href="{{ route('home') }}"><img src="{{ siteLogo('dark') }}" alt="logo"></a>
            <button class="navbar-toggler header-button" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span id="hiddenNav"><i class="las la-bars"></i></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav nav-menu mx-auto align-items-lg-center">
                    <li class="nav-item d-block d-lg-none">
                        <div class="top-button d-flex flex-wrap justify-content-between align-items-center">
                            <ul class="login-registration-list d-flex flex-wrap align-items-center">
                                @auth
                                    <li class="login-registration-list__item">
                                        <a href="{{ route('user.home') }}" class="login-registration-list__link">
                                            <span class="login-registration-list__icon">
                                                <i class="fas fa-user"></i>
                                            </span>
                                            @lang('Dashboard')
                                        </a>
                                    </li>
                                @else
                                    <li class="login-registration-list__item"><a href="{{ route('user.login') }}"
                                            class="login-registration-list__link"><span
                                                class="login-registration-list__icon"><i class="fas fa-user"></i></span>
                                            @lang('Login')</a></li>
                                    <li class="login-registration-list__item"><a href="{{ route('user.register') }}"
                                            class="login-registration-list__link">@lang('Registration')</a></li>
                                @endauth
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="{{ route('home') }}">@lang('Home')</a>
                    </li>
                    @php
                        $pages = App\Models\Page::where('tempname', $activeTemplate)
                            ->where('is_default', Status::NO)
                            ->get();
                    @endphp
                    @foreach ($pages as $k => $data)
                        <li class="nav-item {{ menuActive('pages', null, $data->slug) }}">
                            <a href="{{ route('pages', [$data->slug]) }}" class="nav-link">
                                {{ __($data->name) }}
                            </a>
                        </li>
                    @endforeach
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="{{ route('features') }}">@lang('Features')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pricing.plan') }}">@lang('Pricing')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="{{ route('blogs') }}">@lang('Blogs')</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contact') }}">@lang('Contact')</a>
                    </li>
                </ul>
            </div>
            <div class="d-none d-lg-block">
                <ul class="header-login d-flex gap-3">
                    <li class="header-login__item">
                        @auth
                            <a class="btn btn-outline--white" href="{{ route('user.login') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                    fill="none">
                                    <path d="M3.33337 8H12.6667" stroke="CurrentColor" stroke-width="1.33333"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M8 3.33203L12.6667 7.9987L8 12.6654" stroke="CurrentColor"
                                        stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
                                </svg> @lang('Dashboard')
                            </a>
                        @else
                            <a class="btn btn-outline--white" href="{{ route('user.login') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                    fill="none">
                                    <path d="M3.33337 8H12.6667" stroke="CurrentColor" stroke-width="1.33333"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M8 3.33203L12.6667 7.9987L8 12.6654" stroke="CurrentColor"
                                        stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
                                </svg> @lang('Get Started')
                            </a>
                        @endauth
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
