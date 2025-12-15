@extends($activeTemplate . 'layouts.master')
@section('panel')
    @include('Template::user.expense.widget')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card>
                <x-panel.ui.card.body :paddingZero=true>
                    <x-panel.ui.table.layout filterBoxLocation="Template::user.expense.filter_form">
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('Date') | @lang('Purpose')</th>
                                    <th>@lang('Expense From Account')</th>
                                    <th>@lang('Reference No')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($expenses as $expense)
                                    <tr>
                                        <td>
                                            <div class="text-start">
                                                <span
                                                    class="d-block">{{ showDateTime($expense->expense_date, 'Y-m-d') }}</span>
                                                <span class="d-block fs-12">{{ __(@$expense->category->name) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <span class="d-block">
                                                    {{ __(@$expense->paymentAccount->account_name) }} -
                                                    {{ __(@$expense->paymentAccount->account_number) }}
                                                </span>
                                                <span class="d-block fs-12">{{ __(@$expense->paymentType->name) }}</span>
                                            </div>
                                        </td>
                                        <td>{{ __(@$expense->reference_no ?? 'N/A') }}</td>
                                        <td>{{ showAmount(@$expense->amount) }}</td>
                                        <td>
                                            <x-panel.ui.btn.table_action module="expense" :id="$expense->id">
                                                <x-staff_permission_check permission="edit expense">
                                                    <x-panel.ui.btn.edit data-expense="{{ $expense }}"
                                                        tag="btn" />
                                                </x-staff_permission_check>
                                                @if (@$expense->attachment)
                                                    <a href="{{ route('user.download.attachment', encrypt(getFilePath('expense_attachment') . '/' . @$expense->attachment)) }}"
                                                        class="btn btn--success">
                                                        <i class="las la-download text--success"></i>
                                                        @lang('Attachment')
                                                    </a>
                                                @endif
                                            </x-panel.ui.btn.table_action>
                                        </td>
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

    <x-panel.ui.modal id="modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add Expense')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-panel.ui.modal.header>
        <x-panel.ui.modal.body>
            <form method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="form-group col-lg-12">
                        <label>@lang('Expense Date')</label>
                        <input type="text" class="form-control date-picker" name="expense_date" required
                            value="{{ old('expense_date') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Expense Purpose')</label>
                        <select name="expense_purpose" class="form-control select2" required>
                            <option value="" selected disabled>@lang('Select Purpose')</option>
                            @foreach ($expenseCategories as $expenseCategory)
                                <option value="{{ @$expenseCategory->id }}">{{ __(@$expenseCategory->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Amount')</label>
                        <div class="input-group input--group">
                            <span class="input-group-text">{{ gs('cur_sym', getParentUser()->id) }}</span>
                            <input type="number" step="any" class="form-control" name="amount" required
                                value="{{ old('amount') }}">
                            <span class="input-group-text">{{ __(gs('cur_text', getParentUser()->id)) }}</span>
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label>@lang('Payment Type')</label>
                        <select name="payment_type" class="form-control select2">
                            <option value="" selected disabled>@lang('Select Option')</option>
                            @foreach ($paymentMethods as $paymentMethod)
                                <option value="{{ $paymentMethod->id }}">
                                    {{ __($paymentMethod->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Reference No')</label>
                        <input type="text" class="form-control" name="reference_no" value="{{ old('reference_no') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Attachment')</label>
                        <input type="file" class="form-control" name="attachment">
                    </div>
                    <div class="form-group col-lg-12">
                        <label>@lang('Comment')</label>
                        <textarea name="comment" class="form-control">{{ old('comment') }}</textarea>
                    </div>
                    <div class="form-group col-lg-12">
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
            const $form = $modal.find('form');

            $('.add-btn').on('click', function() {
                const action = "{{ route('user.expense.create') }}";

                $modal.find('.modal-title').text("@lang('Add Expense')");
                $form.trigger('reset');

                $form.attr('action', action);
                $modal.find('[name=payment_type]').attr('disabled', false).trigger('change');
                $modal.find('[name=payment_account]').attr('disabled', false).trigger('change');

                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.expense.update', ':id') }}";
                const expense = $(this).data('expense');

                $modal.find('.modal-title').text("@lang('Date')");
                $modal.find('input[name=name]').val(expense.name);
                $modal.find('input[name=expense_date]').val(expense.expense_date);
                $modal.find('input[name=reference_no]').val(expense.reference_no);
                $modal.find('textarea[name=comment]').val(expense.comment);
                $modal.find('input[name=amount]').val(getAmount(expense.amount));
                $modal.find('[name=expense_purpose]').val(expense.category_id).trigger('change');
                $modal.find('[name=payment_type]').val(expense.payment_type_id).attr('disabled', true).trigger(
                    'change');

                $form.attr('action', action.replace(':id', expense.id));
                $modal.modal('show');
            });

            $(".date-picker").flatpickr({
                maxDate: new Date(),
            });


            @if (@request()->popup == 'yes')
                setTimeout(() => {
                    $('.add-btn').trigger('click');
                }, 500);
            @endif

          
        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
    <x-panel.ui.btn.add tag="btn" />
@endpush


@push('script-lib')
    <script src="{{ asset('assets/global/js/flatpickr.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/global/css/flatpickr.min.css') }}">
@endpush
