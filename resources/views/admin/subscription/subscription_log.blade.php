@extends('admin.layouts.app')
@section('panel')
    @include('admin.subscription.widget')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card class="table-has-filter">
                <x-panel.ui.card.body :paddingZero="true">
                    <x-panel.ui.table.layout :renderExportButton="false">
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Plan Name')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Recurring Type')</th>
                                    <th>@lang('Purchase Date')</th>
                                    <th>@lang('Expire Date')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($subscriptions as $subscription)
                                    <tr>
                                        <td>
                                            <x-panel.other.user_info :user="$subscription->user" />
                                        </td>
                                        <td>{{ __(@$subscription->subscriptionPlan->name) }}</td>
                                        <td>{{ showAmount(@$subscription->amount) }}</td>
                                        <td>{{ __(@$subscription->billing_cycle) }}</td>
                                        <td>{{ showDateTime(@$subscription->created_at) }}</td>
                                        <td>{{ showDateTime(@$subscription->expired_at) }}</td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($subscriptions->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($subscriptions) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>
@endsection
