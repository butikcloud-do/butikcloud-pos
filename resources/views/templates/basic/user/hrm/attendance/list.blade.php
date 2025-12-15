@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card>
                <x-panel.ui.card.body :paddingZero=true>
                    <x-panel.ui.table.layout>
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('Employee')</th>
                                    <th>@lang('Company')</th>
                                    <th>@lang('Shift')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Check In')</th>
                                    <th>@lang('Check Out')</th>
                                    <th>@lang('Duration')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($attendances as $attendance)
                                    <tr>
                                        <td>
                                            <div class="flex-thumb-wrapper">
                                                <div class="thumb">
                                                    <img class="thumb-img" src="{{ $attendance->employee->image_src }}">
                                                </div>
                                                <span class="ms-2">
                                                    {{ __(@$attendance->employee->name) }}<br>
                                                    {{ __(@$attendance->employee->phone) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>{{ __(@$attendance->company->name) }}</td>
                                        <td>{{ __(@$attendance->shift->name) }}</td>
                                        <td>{{ $attendance->date }}</td>
                                        <td>{{ showDateTime($attendance->check_in, 'H:i A') }}</td>
                                        <td>{{ showDateTime($attendance->check_out, 'H:i A') }}</td>
                                        <td>{{ $attendance->duration }}</td>
                                        <td>
                                            <x-panel.ui.btn.table_action module="attendance" :id="$attendance->id">
                                                <x-staff_permission_check permission="edit attendance">
                                                    <x-panel.ui.btn.edit tag="btn" :data-attendance="$attendance" />
                                                </x-staff_permission_check>
                                            </x-panel.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($attendances->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($attendances) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <x-panel.ui.modal id="modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add attendance')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-panel.ui.modal.header>
        <x-panel.ui.modal.body>
            <form method="POST">
                @csrf
                <div class="row">

                    <div class="form-group col-lg-6">
                        <label>@lang('Company')</label>
                        <select class="form-control form--control select2 company-select" required name="company_id">
                            <option value="">@lang('Select Company')</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)
                                    data-employees='@json($company->employees)'
                                    data-shifts='@json($company->shifts)'>
                                    {{ __($company->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-lg-6">
                        <label>@lang('Employee')</label>
                        <select class="form-control form--control select2 employee-select" required name="employee_id">
                            <option value="">@lang('Please Select The Company')</option>
                        </select>
                    </div>


                    <div class="form-group col-lg-6">
                        <label>@lang('Shift')</label>
                        <select class="form-control form--control select2 shift-select" required name="shift_id">
                            <option value="">@lang('Please Select The Company')</option>
                        </select>
                    </div>


                    <div class="form-group col-lg-6">
                        <label>@lang('Date')</label>
                        <div class="input-group input--group">
                            <input type="text" class="form-control date-pickers" name="date"
                                value="{{ old('date') }}" required>
                            <span class="input-group-text">
                                <i class="las la-calendar"></i>
                            </span>
                        </div>
                    </div>

                    <div class="form-group col-lg-6">
                        <label>@lang('Check In')</label>
                        <input type="time" class="form-control time-pickers" name="check_in" required
                            value="{{ old('check_in') }}">
                    </div>

                    <div class="form-group col-lg-6">
                        <label>@lang('Check Out')</label>
                        <input type="time" class="form-control time-pickers" name="check_out" required
                            value="{{ old('check_out') }}">
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
                const action = "{{ route('user.attendance.create') }}"
                $modal.find('.modal-title').text("@lang('Add Attendance')");
                $form.trigger('reset');
                $modal.find('select[name=company_id]').trigger('change');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.attendance.update', ':id') }}";
                const attendance = $(this).data('attendance');
                $modal.find('.modal-title').text("@lang('Edit Attendance')");
                $modal.find('select[name=company_id]').val(attendance.company_id).trigger('change');
                $modal.find('select[name=employee_id]').val(attendance.employee_id).trigger('change');
                $modal.find('select[name=shift_id]').val(attendance.shift_id).trigger('change');
                $modal.find('input[name=date]').val(attendance.date);
                $modal.find('input[name=check_in]').val(attendance.check_in);
                $modal.find('input[name=check_out]').val(attendance.check_out);
                $form.attr('action', action.replace(':id', attendance.id));
                $modal.modal('show');
            });


            $(".date-pickers").flatpickr({
                minDate: new Date(),
                maxDate: new Date(),

            });

            $(".time-pickers").flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",

            });


            $('.company-select').on('change', function() {
                const employees = $(this).find('option:selected').data('employees');
                const shifts = $(this).find('option:selected').data('shifts');

                let employeeHtml = `<option selected disabled>@lang('Select One')</option>`;
                if (employees && employees.length > 0) {
                    $.each(employees, function(i, employee) {
                        employeeHtml += `<option value="${employee.id}">${employee.name}</option>`;
                    });
                } else {
                    employeeHtml = `<option selected disabled>@lang('No Employee Found')</option>`;
                }
                $('.employee-select').html(employeeHtml).trigger('change');

                let shiftHtml = `<option selected disabled>@lang('Select One')</option>`;
                if (shifts && shifts.length > 0) {
                    $.each(shifts, function(i, shift) {
                        shiftHtml += `<option value="${shift.id}">${shift.name}</option>`;
                    });
                } else {
                    shiftHtml = `<option selected disabled>@lang('No Shift Found')</option>`;
                }
                $('.shift-select').html(shiftHtml).trigger('change');
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
    <x-staff_permission_check permission="add attendance">
        <x-panel.ui.btn.add tag="btn" />
    </x-staff_permission_check>
@endpush
