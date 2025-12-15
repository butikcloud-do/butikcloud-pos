@extends('admin.layouts.app')
@section('panel')
    <div class="row  responsive-row">
        <div class="col-xl-6 col-lg-12">
            <x-panel.ui.card class="h-100">
                <x-panel.ui.card.header>
                    <h4 class="card-title">@lang('Application Details')</h4>
                    <small class="text--secondary">@lang('Explore key details about your application, including its name, version, localization info, and more.')</small>
                </x-panel.ui.card.header>
                <x-panel.ui.card.body>
                    <ul class="list-group list-group-flush info-list">
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="flex-align gap-2">
                                <x-panel.svg.application-icon />
                                <span class="text--secondary">@lang('Application Name')</span>
                            </span>
                            <span class="fw-500">{{ $systemDetails['name'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="flex-align gap-2">
                                <x-panel.svg.code-icon />
                                <span class="text--secondary">@lang('Application Version')</span>
                            </span>
                            <span class="fw-500">{{ $systemDetails['web_version'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="flex-align gap-2">
                                <x-panel.svg.ovo />
                                <span class="text--secondary">@lang('OvoPanel Version')</span>
                            </span>
                            <span class="fw-500">{{ $systemDetails['admin_panel_version'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="flex-align gap-2">
                                <x-panel.svg.laravel-icon />
                                <span class="text--secondary">@lang('Laravel Version')</span>
                            </span>
                            <span class="fw-500">{{ $laravelVersion }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="flex-align gap-2">
                                <x-panel.svg.timezone />
                                <span class="text--secondary">@lang('Timezone')</span>
                            </span>
                            <span class="fw-500">{{ @$timeZone }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="flex-align gap-2">
                                <x-panel.svg.language-icon />
                                <span class="text--secondary">@lang('Default Language')</span>
                            </span>
                            <span class="fw-500">{{ config('app.locale') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="flex-align gap-2">
                                <x-panel.svg.maintaince />
                                <span class="text--secondary">@lang('Maintenance Mode')</span>
                            </span>
                            <span class="fw-500">
                                @if (gs('maintenance_mode') == Status::ENABLE)
                                    <span class="text--warning">@lang('Enable')</span>
                                @else
                                    <span class="text--success">@lang('Disable')</span>
                                @endif
                            </span>
                        </li>
                    </ul>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
        <div class="col-xl-6 col-lg-12">
            <x-panel.ui.card class="h-100">
                <x-panel.ui.card.header>
                    <h4 class="card-title">@lang('Server Details')</h4>
                    <small class="text--secondary">@lang('Explore key details about your server, including its php version, database info, server info and more.')</small>
                </x-panel.ui.card.header>
                <x-panel.ui.card.body>
                    <ul class="list-group list-group-flush info-list">
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="flex-align gap-2">
                                <x-panel.svg.php-icon />
                                <span class="text--secondary">@lang('PHP Version')</span>
                            </span>
                            <span class="fw-500">{{ phpversion() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="flex-align gap-2">
                                <x-panel.svg.database-icon />
                                <span class="text--secondary">@lang('Database')</span>
                            </span>
                            <span class="fw-500">
                                {{ DB::connection()->getPDO()->getAttribute(PDO::ATTR_DRIVER_NAME) }}
                                {{ DB::connection()->getPDO()->getAttribute(PDO::ATTR_SERVER_VERSION) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="flex-align gap-2">
                                <x-panel.svg.server-icon />
                                <span class="text--secondary">@lang('Server Software')</span>
                            </span>
                            <span class="fw-500">{{ @$serverDetails['SERVER_SOFTWARE'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="flex-align gap-2">
                                <x-panel.svg.location-icon />
                                <span class="text--secondary">@lang('Server IP Address')</span>
                            </span>
                            <span class="fw-500">{{ @$serverDetails['SERVER_ADDR'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="flex-align gap-2">
                                <x-panel.svg.protocol-icon />
                                <span class="text--secondary">@lang('Server Protocol')</span>
                            </span>
                            <span class="fw-500">{{ @$serverDetails['SERVER_PROTOCOL'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="flex-align gap-2">
                                <x-panel.svg.http-icon />
                                <span class="text--secondary">@lang('HTTP Host')</span>
                            </span>
                            <span class="fw-500">{{ @$serverDetails['HTTP_HOST'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="flex-align gap-2">
                                <x-panel.svg.port-icon />
                                <span class="text--secondary">@lang('Server Port')</span>
                            </span>
                            <span class="fw-500">{{ @$serverDetails['SERVER_PORT'] }}</span>
                        </li>
                    </ul>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
        <div class="col-xl-6 col-lg-12">
            <x-panel.ui.card class="h-100">
                <x-panel.ui.card.header>
                    <h4 class="card-title">@lang('App Information')</h4>
                    <small class="text--secondary">@lang('Explore key details about your mobile app, including its version and more.')</small>
                </x-panel.ui.card.header>
                <x-panel.ui.card.body>
                    <ul class="list-group list-group-flush info-list">
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="flex-align gap-2">
                                <x-panel.svg.mobile-icon />
                                <span class="text--secondary">@lang('App Version')</span>
                            </span>
                            <span class="fw-500">{{ $systemDetails['mobile_app_version'] }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="flex-align gap-2">
                                <x-panel.svg.flutter-icon />
                                <span class="text--secondary">@lang('Flutter Version')</span>
                            </span>
                            <span class="fw-500">{{ $systemDetails['flutter_version'] }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="flex-align gap-2">
                                <x-panel.svg.apple-icon />
                                <span class="text--secondary">@lang('iOS Support')</span>
                            </span>
                            <span class="fw-500 text--success">@lang('Yes')</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="flex-align gap-2">
                                <x-panel.svg.android-icon />
                                <span class="text--secondary">@lang('Android Version')</span>
                            </span>
                            <span class="fw-500">{{ $systemDetails['android_version'] }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="flex-align gap-2">
                                <x-panel.svg.apple-icon />
                                <span class="text--secondary">@lang('Apple Version')</span>
                            </span>
                            <span class="fw-500">{{ $systemDetails['ios_version'] }}</span>
                        </li>
                    </ul>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
        <div class="col-xl-6 col-lg-12">
            <x-panel.ui.card class="h-100">
                <x-panel.ui.card.header class="flex-between gap-2 flex-xxl-nowrap flex-xl-wrap flex-lg-nowrap">
                    <div class="">

                        <h4 class="card-title">@lang('Clear Cache')</h4>
                        <small>
                            @lang('If you clear the cache, your application will be optimized and ready to run smoothly.')
                        </small>
                    </div>
                    <a href="{{ route('admin.system.optimize.clear') }}" class="btn flex-shrink-0 btn--primary btn-large">
                        <i class="fas fa-broom"></i> @lang('Clear Now')
                    </a>
                </x-panel.ui.card.header>
                <x-panel.ui.card.body>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span><i class="fas fa-check-circle text--success me-2"></i> @lang('Compiled views will be cleared')</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span><i class="fas fa-check-circle text--success me-2"></i> @lang('Application cache will be cleared')</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span><i class="fas fa-check-circle text--success me-2"></i> @lang('Route cache will be cleared')</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span><i class="fas fa-check-circle text--success me-2"></i> @lang('Configuration cache will be cleared')</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span><i class="fas fa-check-circle text--success me-2"></i> @lang('Compiled services and packages files will be removed')</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span><i class="fas fa-check-circle text--success me-2"></i> @lang('Caches will be cleared')</span>
                        </li>
                    </ul>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
            <div class="mt-3">

            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .info-list .list-group-item {
            padding-block: 12px;
        }
    </style>
@endpush
