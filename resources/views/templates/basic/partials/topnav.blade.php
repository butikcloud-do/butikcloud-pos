@php
    $user = auth()->user();
@endphp

<x-panel.other.header_search :menus=$menus />

<header class="dashboard__header">
    <div class="dashboard__header-left">
        <span class="breadcrumb-icon navigation-bar"><i class="fa-solid fa-bars"></i></span>
        <div class="header-search__input">
            <label for="desktop-search-input" class="header-search__icon open-search">
                <x-panel.svg.search />
            </label>
            <label for="desktop-search-input">
                <input type="search" id="desktop-search-input" placeholder="@lang('Search')...."
                    class="desktop-search header-search-filed open-search" autocomplete="false">
                <span class="search-instruction flex-align gap-2">
                    <span class="instruction__icon esc-text fw-bold">@lang('Ctrl')</span>
                    <span class="instruction__icon esc-text fw-bold">@lang('K')</span>
                </span>
            </label>
        </div>
    </div>

    <div class="dashboard__header-right">
        <x-staff_permission_check permission="add sale">
            <a class="btn btn--primary btn--pos" href="{{ route('user.pos.index') }}" role="button">
                <i class="las la-qrcode"></i>
                @lang('POS')
            </a>
        </x-staff_permission_check>
        <div class="dashboard-info flex-align gap-sm-2 gap-1">
            <div class="language-dropdown header-dropdown">
                <button class="header-dropdown__icon dropdown-toggle " data-bs-toggle="dropdown">
                    <span data-bs-toggle="tooltip" title="@lang('Language')">
                        <i class="las la-language"></i>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @php
                        $language = App\Models\Language::all();
                        $appLocal = strtoupper(config('app.locale')) ?? 'en';
                    @endphp
                    @foreach ($language as $item)
                        <li class="dropdown-menu__item  align-items-center gap-2 justify-content-between langSel">
                            <a href="{{ route('lang', $item->code) }}" class="lang-box-link">
                                <div class=" d-flex flex-wrap align-items-center gap-2">
                                    <span class="language-dropdown__icon">
                                        <img src="{{ @$item->image_src }}">
                                    </span>
                                    {{ ucfirst($item->name) }}
                                </div>
                            </a>
                            @if ($appLocal == strtoupper($item->code))
                                <span class="text--success">
                                    <i class="las la-check-double"></i>
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="header-dropdown">
                <button class=" dropdown-toggle header-dropdown__icon" type='button' data-bs-toggle="tooltip"
                    title="@lang('Theme')" id="switch-theme">
                    <span class=" dark-show">
                        <i class="las la-moon"></i>
                    </span>
                    <span class=" light-show">
                        <i class="las la-sun"></i>
                    </span>
                </button>
            </div>

            <div class="dashboard-header-user">
                <button class="header-dropdown__icon" data-bs-toggle="dropdown" aria-expanded="false">
                    <span data-bs-toggle="tooltip" title="@lang('Profile')">
                        <i class="las la-user"></i>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end user__area">
                    <div class="user__header">
                        <a href="{{ route('user.profile.setting') }}" class="user__info">
                            <div class="user__thumb">
                                <img src="{{ @$user->image_src }}">
                            </div>
                            <div class="user__details">
                                <h6 class="user__name">{{ @$user->username }}</h6>
                            </div>
                        </a>
                    </div>
                    <div class="user__body">
                        <nav class="user__link">
                            <a href="{{ route('user.profile.setting') }}" class="user__link-item">
                                <span class="user__link-item-icon">
                                    <i class="las la-user-alt"></i>
                                </span>
                                <span class="user__link-item-text">@lang('My Profile')</span>
                            </a>
                            @if (!auth()->user()->parent_id)
                                <a href="{{ route('user.subscription.plan.index') }}" class="user__link-item">
                                    <span class="user__link-item-icon">
                                        <i class="las la-dollar-sign"></i>
                                    </span>
                                    <span class="user__link-item-text">@lang('Subscription Info')</span>
                                </a>
                                <a href="{{ route('user.transactions.history') }}" class="user__link-item">
                                    <span class="user__link-item-icon">
                                        <i class="las la-history"></i>
                                    </span>
                                    <span class="user__link-item-text">@lang('Transaction History')</span>
                                </a>
                            @endif
                            <a href="{{ route('user.change.password') }}" class="user__link-item">
                                <span class="user__link-item-icon">
                                    <i class="las la-lock-open"></i>
                                </span>
                                <span class="user__link-item-text">@lang('Change Password')</span>
                            </a>
                        </nav>
                    </div>
                    <div class="user__footer">
                        <a href="{{ route('user.logout') }}" class="btn btn--danger ">
                            <span class="btn--icon"><i class="fas fa-sign-out text--danger"></i></span>
                            @lang('Logout')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
