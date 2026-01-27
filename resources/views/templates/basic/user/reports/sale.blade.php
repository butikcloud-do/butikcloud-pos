@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card class="table-has-filter">
                <x-panel.ui.card.body :paddingZero="true">
                    <x-panel.ui.table.layout filterBoxLocation="Template::user.reports.sale_filter_form" :hasRecycleBin="false" :renderExportButton="false">
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('Invoice Number') | @lang('Total Items')</th>
                                    <th>@lang('Sale Date') | @lang('Created At')</th>
                                    <th>@lang('Customer')</th>
                                    <th>@lang('Total Amount') | @lang('Purchase Value')</th>
                                    <th>@lang('Profit/Loss Amount')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($sales as $sale)
                                    @php
                                        $totalSaleAmount = $sale->total;
                                        $purchaseAmount = $sale->total_purchase_value;
                                        $profitLossAmount = $totalSaleAmount - $purchaseAmount;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div>
                                                <span class="d-block">
                                                    <a
                                                        href="{{ route('user.sale.view', $sale->id) }}">{{ __($sale->invoice_number) }}</a>
                                                </span>
                                                <span>{{ ($sale->sale_details_count) }} @lang('Items') </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ showDateTime($sale->sale_date, 'Y-m-d') }}</span>
                                                <span>{{ showDateTime($sale->created_at) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ __(@$sale->customer->name) }}</span>
                                                <span>{{ __(@$sale->customer->name) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block text--success">{{ showAmount($totalSaleAmount) }}</span>
                                                <span class="text--warning">{{ showAmount($purchaseAmount) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="@if ($profitLossAmount <= 0) text--danger @else text--success @endif">
                                                {{ showAmount($totalSaleAmount - $purchaseAmount) }}
                                            </span>
                                        </td>

                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($sales->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($sales) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>
@endsection
