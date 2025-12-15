@extends($activeTemplate . 'layouts.master')
@section('panel')
    @php
        $paymentTypes = paymentDetailsForCashRegister($cashRegister);
    @endphp
    <div class="row justify-content-2 mb-4">
        <div class="col-sm-6">
            <x-panel.ui.card class="h-100">
                <x-panel.ui.card.header class="d-flex gap-2 justify-content-between flex-wrap">
                    <h4 class="card-title">@lang('Register Information')</h4>
                    <button class="btn btn--danger close-register" type="button">
                        <i class="fa-regular fa-times-circle"></i> @lang('Close Register')
                    </button>
                </x-panel.ui.card.header>
                <x-panel.ui.card.body>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between gap-2 flex-wrap ps-0">
                            <span>@lang('Starting Amount')</span>
                            <span>
                                {{ showAmount($cashRegister->starting_amount) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between gap-2 flex-wrap ps-0 align-items-center">
                            <span>@lang('Starting Time')</span>
                            <span>
                                <span class="d-block">
                                    {{ showDateTime($cashRegister->starting_time) }}
                                </span>
                                <span class="fs-14 text-end text--warning w-100">
                                    {{ diffForHumans($cashRegister->starting_time) }}
                                </span>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between gap-2 flex-wrap ps-0">
                            <span>@lang('Starting Note')</span>
                            <span>
                                {{ __($cashRegister->starting_note ?? 'N/A') }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between gap-2 flex-wrap ps-0">
                            <span>@lang('User')</span>
                            <span>
                                {{ __($user->fullname ?? 'N/A') }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between gap-2 flex-wrap ps-0">
                            <span>@lang('Total Sale Amount')</span>
                            <span>
                                {{ showAmount($paymentTypes->sum('total_sale')) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between gap-2 flex-wrap ps-0">
                            <span>@lang('Total Expense Amount')</span>
                            <span>
                                {{ showAmount($paymentTypes->sum('total_expense')) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between gap-2 flex-wrap ps-0">
                            <span>@lang('Total Other Credit Amount')</span>
                            <span>
                                {{ showAmount($paymentTypes->sum('total_other_credit')) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between gap-2 flex-wrap ps-0">
                            <span>@lang('Total Other Debit Amount')</span>
                            <span>
                                {{ showAmount($paymentTypes->sum('total_other_debit')) }}
                            </span>
                        </li>
                    </ul>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
        <div class="col-sm-6">
            <x-panel.ui.card>
                <x-panel.ui.card.header>
                    <h4 class="card-title">@lang('Quick Action')</h4>
                </x-panel.ui.card.header>
                <x-panel.ui.card.body>
                    <form action="{{ route('user.cash_register.store.transaction') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="">@lang('Amount')</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ __(gs('cur_sym', getParentUser()->id)) }}</span>
                                <input type="number" step="any" class="form-control" name="amount" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="">@lang('Action Type')</label>
                                    <select class="form-control form-select select2" name="action_type"
                                        data-minimum-results-for-search="-1">
                                        <option value="{{ Status::CASH_REGISTER_TYPE_SALE }}">
                                            @lang('Sale')
                                        </option>
                                        <option value="{{ Status::CASH_REGISTER_TYPE_EXPENSE }}">
                                            @lang('Expense')
                                        </option>
                                        <option value="{{ Status::CASH_REGISTER_OTHER_CREDIT }}">
                                            @lang('Other Credit')
                                        </option>
                                        <option value="{{ Status::CASH_REGISTER_OTHER_DEBIT }}">
                                            @lang('Other Debit')
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="">@lang('Payment Type')</label>
                                    <select class="form-control form-select select2" name="payment_type"
                                        data-minimum-results-for-search="-1">
                                        @foreach ($paymentTypes as $paymentType)
                                            <option value="{{ $paymentType->id }}">
                                                {{ __(@$paymentType->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="">@lang('Reason')</label>
                                    <input type="text" class="form-control" name="reason" required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn--primary w-100 btn-large">@lang('Submit')</button>
                            </div>
                        </div>
                    </form>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    @foreach ($paymentTypes as $paymentType)
        <div class="row">
            <div class="col-12">
                <div class="divider">
                    <span></span>
                    <h6 class="divider-title">{{ __(@$paymentType->name) }}</h6>
                    <span></span>
                </div>
            </div>
            <div class="col-sm-3">
                <x-panel.ui.widget.four :url="route('user.cash_register.account.wise.details', [
                    'cashRegisterId' => $cashRegister->id,
                    'accountId' => $paymentType->id,
                    'type' => Status::CASH_REGISTER_TYPE_SALE,
                ])" variant="success" title="Total Sales" :value="$paymentType->total_sale"
                    icon="las la-shopping-cart" />
            </div>
            <div class="col-sm-3">
                <x-panel.ui.widget.four :url="route('user.cash_register.account.wise.details', [
                    'cashRegisterId' => $cashRegister->id,
                    'accountId' => $paymentType->id,
                    'type' => Status::CASH_REGISTER_OTHER_DEBIT,
                ])" variant="warning" title="Total Expense" :value="$paymentType->total_expense"
                    icon="las la-comment-dollar" />
            </div>
            <div class="col-sm-3">
                <x-panel.ui.widget.four :url="route('user.cash_register.account.wise.details', [
                    'cashRegisterId' => $cashRegister->id,
                    'accountId' => $paymentType->id,
                    'type' => Status::CASH_REGISTER_OTHER_CREDIT,
                ])" variant="success" title="Other Credit" :value="$paymentType->total_other_credit"
                    icon="las la-folder-plus" />
            </div>
            <div class="col-sm-3">
                <x-panel.ui.widget.four :url="route('user.cash_register.account.wise.details', [
                    'cashRegisterId' => $cashRegister->id,
                    'accountId' => $paymentType->id,
                    'type' => Status::CASH_REGISTER_OTHER_DEBIT,
                ])" variant="danger" title="Other Debit" :value="$paymentType->total_other_debit"
                    icon="las la-folder-minus" />
            </div>
        </div>
    @endforeach

    <div id="modal" class="modal fade custom--modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Close Cash Register')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('user.cash_register.close') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">@lang('Closing Amount')</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ __(gs('cur_sym', getParentUser()->id)) }}</span>
                                <input type="number" step="any" class="form-control" name="closing_amount" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">@lang('Closing Note')</label>
                            <textarea name="closing_note" class="form-control" cols="30" rows="10"></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn--primary w-100 btn-large">
                                <span class="me-1"><i class="fa-regular fa-paper-plane"></i></span> @lang('Submit')
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {

            $('.close-register').on('click', function() {
                const $modal = $("#modal");
                $modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .divider {
            display: flex;
            width: 100%;
            margin: 20px auto;
            text-align: center;
        }

        .divider span {
            display: table-cell;
            position: relative;
        }

        .divider span:first-child,
        .divider span:last-child {
            width: 50%;
            top: 10px;
            background-size: 100% 2px;
            background-repeat: no-repeat;
        }

        .divider span:first-child {
            background-image: linear-gradient(to right, transparent, hsl(var(--primary)/0.6));
        }

        .divider .divider-title {
            padding: 0 16px;
            white-space: nowrap;
            color: hsl(var(--title-color));
            margin-bottom: 0px;
            font-weight: 500
        }

        .divider span:last-child {
            background-image: linear-gradient(to right, hsl(var(--primary)/0.6), transparent);
        }
    </style>
@endpush
