@extends($activeTemplate . 'layouts.master')
@section('panel')
    @include('Template::user.reports.invoice_wise_profit_widget')
    <div class="col-12">
        <x-panel.ui.card class="table-has-filter">
            <x-panel.ui.card.body :paddingZero="true">
                <x-panel.ui.table.layout filterBoxLocation="Template::user.reports.filter_form" :hasRecycleBin="false" :renderExportButton="false">
                    <x-panel.ui.table>
                        <x-panel.ui.table.header>
                            <tr>
                                <th>@lang('Invoice Number') | @lang('Sales Date')</th>
                                <th>@lang('Customer Name')</th>
                                <th>@lang('Sales Price')</th>
                                <th>@lang('Purchase Price')</th>
                                <th>@lang('Gross Profit')</th>
                            </tr>
                        </x-panel.ui.table.header>
                        <x-panel.ui.table.body>
                            @forelse($invoicesWise as $invoiceWise)
                                <tr>
                                    <td>
                                        <div>
                                            <a href="{{ route('user.sale.view', $invoiceWise->id) }}">{{ $invoiceWise->invoice_number }}</a><br>
                                            {{ showDateTime($invoiceWise->sale_date) }}
                                        </div>
                                    </td>
                                    <td>
                                        {{ __(@$invoiceWise->customer->name) }}
                                    </td>
                                    <td>{{ showAmount(@$invoiceWise->total_sales_price) }}</td>
                                    <td>{{ showAmount(@$invoiceWise->total_purchase_price) }}</td>
                                    <td>{{ showAmount(@$invoiceWise->gross_profit) }}</td>
                                </tr>
                            @empty
                                <x-panel.ui.table.empty_message />
                            @endforelse
                        </x-panel.ui.table.body>
                    </x-panel.ui.table>
                    @if ($invoicesWise->hasPages())
                        <x-panel.ui.table.footer>
                            {{ paginateLinks($invoicesWise) }}
                        </x-panel.ui.table.footer>
                    @endif
                </x-panel.ui.table.layout>
            </x-panel.ui.card.body>
        </x-panel.ui.card>
    </div>
@endsection

@push('breadcrumb-plugins')
<a href="{{ route('user.report.profit.product_wise') }}" class="btn btn-outline--primary"><i class="las la-file-invoice-dollar"></i> @lang('Product Wise Profit') </a>
@endpush
