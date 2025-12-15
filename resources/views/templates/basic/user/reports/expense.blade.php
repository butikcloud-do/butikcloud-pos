@extends($activeTemplate . 'layouts.master')
@section('panel')

    <div class="row">
        <div class="col-12">
            <x-panel.ui.card class="table-has-filter">
                <x-panel.ui.card.body :paddingZero="true">
                    <x-panel.ui.table.layout filterBoxLocation="Template::user.reports.expense_filter_form" :hasRecycleBin="false" :renderExportButton="false">
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Purpose')</th>
                                    <th>@lang('Reference No')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Added By')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($expenses as $expense)
                                    <tr>
                                        <td>{{ showDateTime($expense->expense_date, 'Y-m-d') }}</td>
                                        <td>{{ __(@$expense->category->name) }}</td>
                                        <td>{{ __(@$expense->reference_no ?? 'N/A') }}</td>
                                        <td>{{ showAmount(@$expense->amount) }}</td>
                                        <td>{{ __(@$expense->user->username) }}</td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($expenses->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($expenses) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>
@endsection
