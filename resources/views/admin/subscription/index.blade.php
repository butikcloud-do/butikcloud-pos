@extends('admin.layouts.app')
@section('panel')
    <x-panel.ui.card class="table-has-filter">
        <x-panel.ui.card.body :paddingZero="true">
            <x-panel.ui.table.layout :renderExportButton="false">
                <x-panel.ui.table>
                    <x-panel.ui.table.header>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Monthly Price')</th>
                            <th>@lang('Yearly Price')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </x-panel.ui.table.header>
                    <x-panel.ui.table.body>
                        @forelse($subscriptionPlans as $subscriptionPlan)
                            <tr>
                                <td>
                                    {{ __(@$subscriptionPlan->name) }}
                                </td>
                                <td>
                                    {{ showAmount($subscriptionPlan->monthly_price) }}
                                </td>
                                <td>
                                    {{ showAmount($subscriptionPlan->yearly_price) }}
                                </td>
                                <td>
                                    <x-panel.other.status_switch :status="$subscriptionPlan->status" :action="route('admin.subscription.plan.status', $subscriptionPlan->id)"
                                        title="subscription plan" />
                                </td>
                                <td>
                                    <a href="{{ route('admin.subscription.plan.edit', $subscriptionPlan->id) }}"
                                        class="btn  btn-outline--primary">
                                        <i class="las la-pen"></i> @lang('Edit')
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <x-panel.ui.table.empty_message />
                        @endforelse
                    </x-panel.ui.table.body>
                </x-panel.ui.table>
                @if ($subscriptionPlans->hasPages())
                    <x-panel.ui.table.footer>
                        {{ paginateLinks($subscriptionPlans) }}
                    </x-panel.ui.table.footer>
                @endif
            </x-panel.ui.table.layout>
        </x-panel.ui.card.body>
    </x-panel.ui.card>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-permission_check permission="add sale">
        <x-panel.ui.btn.add href="{{ route('admin.subscription.plan.create') }}" text="New Plan" />
    </x-permission_check>
@endpush
