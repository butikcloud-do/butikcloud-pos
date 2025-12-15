@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card>
                <x-panel.ui.card.body :paddingZero=true>
                    <x-panel.ui.table.layout :hasRecycleBin="false">
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('Employee')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Payment Method')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($payrolls as $payroll)
                                    <tr>
                                        <td>
                                            <div class="flex-thumb-wrapper">
                                                <div class="thumb">
                                                    <img class="thumb-img" src="{{ $payroll->employee->image_src }}">
                                                </div>
                                                <span class="ms-2">
                                                    {{ __(@$payroll->employee->name) }}<br>
                                                    {{ __(@$payroll->employee->phone) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>{{ showAmount($payroll->amount) }}</td>
                                        <td>{{ __(@$payroll->paymentMethod->name) }}</td>
                                        <td>{{ showDateTime($payroll->date) }}</td>
                                        <td>
                                            <x-staff_permission_check permission="edit payroll">
                                                <x-panel.ui.btn.edit tag="btn" :data-payroll="$payroll" />
                                            </x-staff_permission_check>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($payrolls->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($payrolls) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <x-panel.ui.modal id="modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add payroll')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-panel.ui.modal.header>
        <x-panel.ui.modal.body>
            <form method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-lg-12">
                        <label>@lang('Employee')</label>
                        <select class="form-control form--control select2" required name="employee_id">
                            <option value="">@lang('Select Employee')</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>{{ __($employee->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Date')</label>
                        <div class="input-group input--group">
                            <input type="text" class="form-control date-picker-payroll" name="date"
                                value="{{ old('date') }}" required>
                            <span class="input-group-text">
                                <i class="las la-calendar"></i>
                            </span>
                        </div>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Amount')</label>
                        <input type="number" step="any" class="form-control" name="amount" required
                            value="{{ old('amount') }}">
                    </div>
                    <div class="form-group col-lg-12">
                        <label>@lang('Payment Method')</label>
                        <select class="form-control form--control select2" required name="payment_method_id">
                            <option value="">@lang('Select Payment Method')</option>
                            @foreach ($paymentMethods as $paymentMethod)
                                <option value="{{ $paymentMethod->id }}" @selected(old('payment_method_id') == $paymentMethod->id)>
                                    {{ __($paymentMethod->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <x-panel.ui.btn.modal />
                        </div>
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
                const action = "{{ route('user.payroll.create') }}"
                $modal.find('.modal-title').text("@lang('Add Payroll')");
                $form.trigger('reset');
                $modal.find('select[name=employee_id]').trigger('change');
                $modal.find('select[name=payment_method_id]').trigger('change');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.payroll.update', ':id') }}";
                const payroll = $(this).data('payroll');
                $modal.find('.modal-title').text("@lang('Edit Payroll')");
                $modal.find('select[name=employee_id]').val(payroll.employee_id).trigger('change');
                $modal.find('select[name=payment_method_id]').val(payroll.payment_method_id).attr('disabled',
                    true).trigger('change');
                $modal.find('input[name=amount]').val(getAmount(payroll.amount));
                $modal.find('input[name=date]').val(payroll.date);
                $form.attr('action', action.replace(':id', payroll.id));
                $modal.modal('show');
            });

            $(".date-picker-payroll").flatpickr({
                calendar: true,
                maxDate: new Date(),
            });


        })(jQuery);
    </script>
@endpush


@push('script-lib')
    <script src="{{ asset('assets/global/js/flatpickr.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/global/css/flatpickr.min.css') }}">
@endpush

@push('breadcrumb-plugins')
    <x-staff_permission_check permission="add payroll">
        <x-panel.ui.btn.add tag="btn" />
    </x-staff_permission_check>
@endpush
