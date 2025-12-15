@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card>
                <x-panel.ui.card.body :paddingZero=true>
                    <x-panel.ui.table.layout :renderExportButton="false" :hasRecycleBin="false">
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('Details')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Time')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($transactions as $cashRegister)
                                    <tr>
                                        <td>{{ __(@$cashRegister->details) }} </td>
                                        <td>{{ showAmount($cashRegister->amount) }} </td>
                                        <td>{{ showDateTime($cashRegister->created_at) }} </td>
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


@push('breadcrumb-plugins')
    <a class="btn  btn--secondary" href="{{ url()->previous() }}">
        <i class="la la-undo"></i> @lang('Back')
    </a>
@endpush
