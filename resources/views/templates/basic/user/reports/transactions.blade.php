@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card class="table-has-filter">
                <x-panel.ui.card.body :paddingZero="true">
                    <x-panel.ui.table.layout filterBoxLocation="reports.transaction_filter_form" :hasRecycleBin="false">
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('Account')</th>
                                    <th>@lang('TRX')</th>
                                    <th>@lang('Transacted')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Post Balance')</th>
                                    <th>@lang('Details')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($transactions as $trx)
                                    <tr>
                                        <td>
                                            <div>
                                                <span class="d-block">
                                                    {{ __(@$trx->paymentAccount->account_name) }} -
                                                    {{ __(@$trx->paymentAccount->account_number) }}
                                                </span>
                                                <span
                                                    class="d-block">{{ __(@$trx->paymentAccount->paymentType->name) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $trx->trx }}</strong>
                                        </td>
                                        <td>
                                            {{ showDateTime($trx->created_at) }}<br>{{ diffForHumans($trx->created_at) }}
                                        </td>
                                        <td>
                                            <span
                                                class="fw-bold @if ($trx->trx_type == '+') text--success @else text--danger @endif">
                                                {{ $trx->trx_type }} {{ showAmount($trx->amount) }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ showAmount($trx->post_balance) }}
                                        </td>

                                        <td>{{ __($trx->details) }}</td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($transactions->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($transactions) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>
@endsection
