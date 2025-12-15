@extends('admin.layouts.app')
@section('panel')
    @if (request()->routeIs('admin.deposit.list') || request()->routeIs('admin.deposit.method'))
        @include('admin.deposit.widget')
    @endif
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card class="table-has-filter">
                <x-panel.ui.card.body :paddingZero="true">
                    <x-panel.ui.table.layout searchPlaceholder="Username / TRX"
                        filterBoxLocation="admin.deposit.filter_form">
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Gateway | Transaction')</th>
                                    <th>@lang('Initiated')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Conversion')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($deposits as $deposit)
                                    <tr>
                                        <td>
                                            <x-panel.other.user_info :user="$deposit->user" />
                                        </td>
                                        <td>
                                            <div>
                                                <span class="fw-bold">
                                                    <a
                                                        href="{{ appendQuery('method', $deposit->method_code < 5000 ? @$deposit->gateway->alias : $deposit->method_code) }}">
                                                        @if ($deposit->method_code < 5000)
                                                            {{ __(@$deposit->gateway->name) }}
                                                        @else
                                                            @lang('Google Pay')
                                                        @endif
                                                    </a>
                                                </span>
                                                <br>
                                                <small> {{ $deposit->trx }} </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                {{ showDateTime($deposit->created_at) }}<br>{{ diffForHumans($deposit->created_at) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                {{ showAmount($deposit->amount) }} + <span class="text--danger"
                                                    title="@lang('charge')">{{ showAmount($deposit->charge) }} </span>
                                                <br>
                                                <strong title="@lang('Amount with charge')">
                                                    {{ showAmount($deposit->amount + $deposit->charge) }}
                                                </strong>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                {{ showAmount(1) }} =
                                                {{ showAmount($deposit->rate, currencyFormat: false) }}
                                                {{ __($deposit->method_currency) }}
                                                <br>
                                                <strong>{{ showAmount($deposit->final_amount, currencyFormat: false) }}
                                                    {{ __($deposit->method_currency) }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            @php echo $deposit->statusBadge @endphp
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.deposit.details', $deposit->id) }}"
                                                class="btn  btn-outline--primary ms-1 table-action-btn">
                                                <i class="las la-info-circle"></i> @lang('Details')
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($deposits->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($deposits) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>
@endsection
