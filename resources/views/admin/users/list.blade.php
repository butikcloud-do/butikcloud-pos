@extends('admin.layouts.app')
@section('panel')
    @include('admin.users.widget')
    <x-panel.ui.card class="table-has-filter">
        <x-panel.ui.card.body :paddingZero="true">
            <x-panel.ui.table.layout searchPlaceholder="Search users" filterBoxLocation="admin.users.filter">
                <x-panel.ui.table>
                    <x-panel.ui.table.header>
                        <tr>
                            <th>@lang('User')</th>
                            <th>@lang('Email-Mobile')</th>
                            <th>@lang('Country')</th>
                            <th>@lang('Joined At')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </x-panel.ui.table.header>
                    <x-panel.ui.table.body>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <x-panel.other.user_info :user="$user" />
                                </td>
                                <td>
                                    <div>
                                        <strong class="d-block">
                                            {{ $user->email }}
                                        </strong>
                                        <small>{{ $user->mobileNumber }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-bold" title="{{ @$user->country_name }}">
                                            {{ $user->country_code }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong class="d-block ">{{ showDateTime($user->created_at) }}</strong>
                                        <small class="d-block"> {{ diffForHumans($user->created_at) }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                                        <a href="{{ route('admin.users.detail', $user->id) }}"
                                            class=" btn btn-outline--primary">
                                            <i class="las la-info-circle"></i>
                                            @lang('Details')
                                        </a>
                                        @if (!request()->routeIs('admin.users.staff'))
                                            <a href="{{ route('admin.users.staff', $user->id) }}"
                                                class=" btn btn-outline--info">
                                                <i class="las la-users"></i>
                                                @lang('Staff List')
                                            </a>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-panel.ui.table.empty_message />
                        @endforelse
                    </x-panel.ui.table.body>
                </x-panel.ui.table>
                @if ($users->hasPages())
                    <x-panel.ui.table.footer>
                        {{ paginateLinks($users) }}
                    </x-panel.ui.table.footer>
                @endif
            </x-panel.ui.table.layout>
        </x-panel.ui.card.body>
    </x-panel.ui.card>
@endsection
