@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card class="table-has-filter">
                <x-panel.ui.card.body :paddingZero="true">
                    <x-panel.ui.table.layout searchPlaceholder="Search Username" filterBoxLocation="admin.reports.filter_form">
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Login at')</th>
                                    <th>@lang('IP')</th>
                                    <th>@lang('Location')</th>
                                    <th>@lang('Browser | OS')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($loginLogs as $log)
                                    <tr>
                                        <td>
                                            <x-panel.other.user_info :user="$log->user" />
                                        </td>
                                        <td>
                                            {{ showDateTime($log->created_at) }} <br> {{ diffForHumans($log->created_at) }}
                                        </td>
                                        <td>
                                            <span class="fw-bold">
                                                <a
                                                    href="{{ route('admin.report.login.ipHistory', [$log->user_ip]) }}">{{ $log->user_ip }}</a>
                                            </span>
                                        </td>

                                        <td>{{ __($log->city) }} <br> {{ __($log->country) }}</td>
                                        <td>
                                            <div>
                                                <span class="d-block">
                                                    <i class="la la-{{ strtolower($log->browser) }}"></i>
                                                    {{ __($log->browser) }}
                                                </span>
                                                <span>
                                                    <i class="la la-{{ strtolower($log->os) }}"></i>
                                                    {{ __($log->os) }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($loginLogs->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($loginLogs) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>
@endsection



@if (request()->routeIs('admin.report.login.ipHistory'))
    @push('breadcrumb-plugins')
        <a href="https://www.ip2location.com/{{ $ip }}" target="_blank" class="btn  btn-outline--primary">
            <i class="las la-server"></i> @lang('Lookup IP') {{ $ip }}
        </a>
    @endpush
@endif
