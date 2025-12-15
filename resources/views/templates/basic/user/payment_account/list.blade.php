@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card>
                <x-panel.ui.card.body class="p-0">
                    <x-panel.ui.table.layout>
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('Account Name')</th>
                                    <th>@lang('Account Number')</th>
                                    <th>@lang('Account Type')</th>
                                    <th>@lang('Account Balance')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($paymentAccounts as $paymentAccount)
                                    <tr>
                                        <td>{{ __($paymentAccount->account_name) }}</td>
                                        <td>{{ __($paymentAccount->account_number) }}</td>
                                        <td>{{ __(@$paymentAccount->paymentType->name) }}</td>
                                        <td>{{ showAmount($paymentAccount->balance) }}</td>
                                        <td>
                                            <x-panel.other.status_switch :status="$paymentAccount->status"
                                                :action="route('user.payment.account.status.change', $paymentAccount->id, )"
                                                title="payment Account" />
                                        </td>

                                        <td class="dropdown">
                                            @if (request()->trash)
                                                <button type="button" class="btn btn-outline--success confirmationBtn"
                                                    data-question='@lang('Are you sure to restore this payment account?')'
                                                    data-action="{{ route('user.payment.account.restore', $paymentAccount->id) }}">
                                                    <i class="las la-undo"></i>
                                                    @lang('Restore')
                                                </button>
                                            @else
                                                <button class=" btn btn-outline--primary" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    @lang('Action') <i class="las la-angle-down"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown">
                                                    <x-staff_permission_check permission="edit payment account">
                                                        <button type="button"
                                                            class="dropdown-list d-block w-100 text-start edit-btn"
                                                            data-paymenttype='@json($paymentAccount)'>
                                                            <span class="me-1">
                                                                <i class="las la-pencil-alt text--primary"></i>
                                                            </span>
                                                            @lang('Edit')
                                                        </button>
                                                    </x-staff_permission_check>
                                                    <x-staff_permission_check permission="adjust payment account balance">
                                                        <button type="button"
                                                            class="dropdown-list d-block w-100 text-start add-balance"
                                                            data-id="{{ $paymentAccount->id }}">
                                                            <span class="me-1">
                                                                <i class="las la-plus-circle text--success"></i>
                                                            </span>
                                                            @lang('Add Balance')
                                                        </button>
                                                        <button type="button"
                                                            class="dropdown-list d-block w-100 text-start minus-balance"
                                                            data-id="{{ $paymentAccount->id }}">
                                                            <span class="me-1">
                                                                <i class="las la-minus-circle text--warning"></i>
                                                            </span>
                                                            @lang('Subtract Balance')
                                                        </button>
                                                        <button type="button"
                                                            class="dropdown-list d-block w-100 text-start transfer-balance"
                                                            data-id="{{ $paymentAccount->id }}">
                                                            <span class="me-1">
                                                                <i class="las la-exchange-alt text--success"></i>
                                                            </span>
                                                            @lang('Transfer Balance')
                                                        </button>
                                                    </x-staff_permission_check>
                                                    <x-staff_permission_check permission="trash payment account">
                                                        <a class="dropdown-list d-block w-100 text-start"
                                                            href="{{ route('user.report.transaction') }}?payment_account_id={{ $paymentAccount->id }}">
                                                            <i class="las la-exchange-alt text--info"></i>
                                                            @lang('Transaction History')
                                                        </a>
                                                    </x-staff_permission_check>
                                                    <x-staff_permission_check permission="trash payment account">
                                                        <button
                                                            class="dropdown-list d-block w-100 text-start confirmationBtn"
                                                            data-question='@lang('Are you sure to move this payment account to trash?')'
                                                            data-action="{{ route('user.payment.account.trash.temporary', $paymentAccount->id) }}">
                                                            <i class="las la-trash text--danger"></i> @lang('Trash')
                                                        </button>
                                                    </x-staff_permission_check>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($paymentAccounts->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($paymentAccounts) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <x-panel.ui.modal id="modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add Payment Account')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-panel.ui.modal.header>
        <x-panel.ui.modal.body>
            <form method="POST">
                @csrf
                <div class="form-group">
                    <label>@lang('Account Type')</label>
                    <select class="form-control select2" name="payment_type" required>
                        <option value="" selected disabled>@lang('Select One')</option>
                        @foreach ($paymentTypes as $paymentType)
                            <option value="{{ @$paymentType->id }}">
                                {{ __($paymentType->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>@lang('Account Name')</label>
                    <input type="text" class="form-control" name="account_name" required
                        value="{{ old('account_name') }}">
                </div>
                <div class="form-group">
                    <label>@lang('Account Number')</label>
                    <input type="text" class="form-control" name="account_number" required
                        value="{{ old('account_number') }}">
                </div>
                <div class="form-group">
                    <label>@lang('Note')/@lang('Comment')</label>
                    <textarea name="note" class="form-control" rows="3">{{ old('note') }}</textarea>
                </div>
                <div class="form-group">
                    <label>@lang('Initial Balance')</label>
                    <input type="number" step="any" class="form-control" name="balance" value="{{ old('balance') }}">
                </div>
                <div class="form-group">
                    <x-panel.ui.btn.modal />
                </div>
            </form>
        </x-panel.ui.modal.body>
    </x-panel.ui.modal>


    <x-panel.ui.modal id="balance-modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add Balance')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-panel.ui.modal.header>
        <x-panel.ui.modal.body>
            <form method="POST">
                @csrf
                <input type="hidden" name="trx_type">
                <div class="form-group">
                    <label>@lang('Amount')</label>
                    <input type="number" step="any" class="form-control" name="amount" required
                        value="{{ old('amount') }}">
                </div>
                <div class="form-group">
                    <label>@lang('Note')</label>
                    <textarea name="note" class="form-control" rows="3" required>{{ old('note') }}</textarea>
                </div>
                <div class="form-group">
                    <x-panel.ui.btn.modal />
                </div>
            </form>
        </x-panel.ui.modal.body>
    </x-panel.ui.modal>

    <x-panel.ui.modal id="transfer-modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add Transfer')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-panel.ui.modal.header>
        <x-panel.ui.modal.body>
            <form method="POST">
                <div class="row">
                    @csrf
                    <div class="col-lg-6 form-group">
                        <label class="form-label">@lang('From Account')</label>
                        <select class="form-control select2 account-dropdown from-account" name="from_account_id" required
                            disabled>
                            @foreach ($paymentAccounts as $account)
                                <option value="{{ $account->id }}">
                                    {{ __($account->account_name) }} - {{ __($account->account_number) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6 form-group">
                        <label class="form-label">@lang('To Account')</label>
                        <select class="form-control select2 account-dropdown to-account" name="to_account_id" required>
                            <option value="" selected disabled>@lang('Select one')</option>
                            @foreach ($paymentAccounts as $paymentAccount)
                                <option value="{{ $paymentAccount->id }}">
                                    {{ __($paymentAccount->account_name) }} - {{ __($paymentAccount->account_number) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>@lang('Amount')</label>
                        <input type="number" step="any" class="form-control" name="amount" required
                            value="{{ old('amount') }}">
                    </div>
                    <div class="form-group">
                        <x-panel.ui.btn.modal />
                    </div>
                </div>
            </form>
        </x-panel.ui.modal.body>
    </x-panel.ui.modal>


    <x-confirmation-modal />
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {

            const $modal = $('#modal');
            const $balanceModal = $('#balance-modal');
            const $transferModal = $('#transfer-modal');
            const $form = $modal.find('form');
            const $balanceForm = $balanceModal.find('form');
            const $transferForm = $transferModal.find('form');

            $('.add-btn').on('click', function() {
                const action = "{{ route('user.payment.account.create') }}";

                $modal.find('.modal-title').text("@lang('Add Payment Account')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.add-balance').on('click', function() {
                const action = "{{ route('user.payment.account.adjust.balance', ':id') }}";
                const id = $(this).data('id');

                $balanceModal.find('.modal-title').text("@lang('Add Balance')");
                $balanceModal.find(`[name=trx_type]`).val("+");
                $balanceForm.attr('action', action.replace(':id', id));
                $balanceModal.modal('show');
            });

            $('.minus-balance').on('click', function() {
                const action = "{{ route('user.payment.account.adjust.balance', ':id') }}";
                const id = $(this).data('id');

                $balanceModal.find('.modal-title').text("@lang('Subtract Balance')");
                $balanceModal.find(`[name=trx_type]`).val("-");
                $balanceForm.attr('action', action.replace(':id', id));
                $balanceModal.modal('show');
            });


            $('.transfer-balance').on('click', function() {
                const action = "{{ route('user.payment.account.transfer.balance', ':id') }}";
                const id = $(this).data('id');

                $transferModal.find('.modal-title').text("@lang('Transfer Balance')");
                $transferModal.find('select[name=from_account_id]').val(id).trigger('change');
                $transferForm.attr('action', action.replace(':id', id));
                $transferModal.modal('show');
            });



            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.payment.account.update', ':id') }}";
                const paymentType = $(this).data('paymenttype');

                $modal.find('.modal-title').text("@lang('Edit Payment Account')");
                $modal.find('select[name=payment_type]').val(paymentType.payment_type_id).trigger('change');
                $modal.find('input[name=account_name]').val(paymentType.account_name);
                $modal.find('input[name=account_number]').val(paymentType.account_number);
                $modal.find('textarea[name=note]').val(paymentType.note);
                $modal.find('input[name=balance]').parent().addClass('d-none');
                $form.attr('action', action.replace(':id', paymentType.id));
                $modal.modal('show');
            });



        })(jQuery);
    </script>
@endpush

@push('breadcrumb-plugins')
    <x-staff_permission_check permission="add payment account">
        <x-panel.ui.btn.add tag="btn" />
    </x-staff_permission_check>
@endpush
