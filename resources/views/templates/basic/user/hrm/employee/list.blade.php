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
                                    <th>@lang('Name')</th>
                                    <th>@lang('Phone | Email')</th>
                                    <th>@lang('Company')</th>
                                    <th>@lang('Department')</th>
                                    <th>@lang('Designation')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Attachment')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($employees as $employee)
                                    <tr>
                                        <td>
                                            <div class="flex-thumb-wrapper">
                                                <div class="thumb">
                                                    <img class="thumb-img" src="{{ $employee->image_src }}">
                                                </div>
                                                <span class="ms-2">
                                                    {{ __($employee->name) }}<br>
                                                    @if ($employee->leave_status)
                                                        <span class="badge badge--info fs-12 mt-1">@lang('On Leave')</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </td>

                                        <td>
                                            <div>
                                                {{ $employee->phone }}<br>
                                                {{ $employee->email }}
                                            </div>
                                        </td>
                                        <td>{{ __(@$employee->company->name) }}</td>
                                        <td>{{ __(@$employee->department->name) }}</td>
                                        <td>{{ __(@$employee->designation->name) }}</td>
                                        <td>
                                            <x-panel.other.status_switch :status="$employee->status" :action="route('user.employee.status.change', $employee->id)"
                                                title="employee" />
                                        </td>
                                        <td>
                                            @if ($employee->attachment)
                                                <a href="{{ route('admin.download.attachment', encrypt(getFilePath('employeeAttachment') . '/' . $employee->attachment)) }}"
                                                    class="btn btn-sm btn-base">
                                                    <i class="las la-download"></i> @lang('Download')
                                                </a>
                                            @else
                                                <button type="button" disabled class="btn btn-sm btn-base">
                                                    <i class="las la-download"></i> @lang('Download')
                                                </button>
                                            @endif
                                        </td>
                                        <td>

                                            <x-panel.ui.btn.table_action module="employee" :id="$employee->id">
                                                <x-staff_permission_check permission="edit employee">
                                                    <x-panel.ui.btn.edit tag="btn" :data-employee="$employee" />
                                                </x-staff_permission_check>
                                            </x-panel.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($employees->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($employees) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <x-panel.ui.modal id="modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add employee')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-panel.ui.modal.header>
        <x-panel.ui.modal.body>
            <form method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="form-group col-lg-6">
                        <label>@lang('Name')</label>
                        <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Gender')</label>
                        <select class="form-control form--control select2" required name="gender"
                            data-minimum-results-for-search="-1">
                            <option value="male" @selected(old('gender') == 'male')>@lang('Male')</option>
                            <option value="female" @selected(old('gender') == 'female')>@lang('Female')</option>
                            <option value="other" @selected(old('gender') == 'other')>@lang('Other')</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Date of Birth')</label>
                        <div class="input-group input--group">
                            <input type="text" class="form-control date-pickers" value="{{ old('dob') }}"
                                name="dob">
                            <span class="input-group-text">
                                <i class="las la-calendar"></i>
                            </span>
                        </div>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Email')</label>
                        <input type="Email" class="form-control" name="email" value="{{ old('email') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Country')</label>
                        <input type="text" class="form-control" name="country" value="{{ old('country') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Phone')</label>
                        <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Joining Date')</label>
                        <div class="input-group input--group">
                            <input type="text" class="form-control date-pickers" value="{{ old('joining_date') }}"
                                name="joining_date">
                            <span class="input-group-text">
                                <i class="las la-calendar"></i>
                            </span>
                        </div>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Company')</label>
                        <select class="form-control form--control select2 company-select" name="company_id" required>
                            <option value="">@lang('Select Company')</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}" data-departments='@json($company->departments)'>
                                    {{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Department')</label>
                        <select class="form-control form--control select2 department-select" name="department_id" required>
                            <option value="">@lang('Select Department')</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Designation')</label>
                        <select class="form-control form--control select2 designation-select" name="designation_id"
                            required>
                            <option value="">@lang('Select Designation')</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Image')</label>
                        <input type="file" class="form-control" name="image">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Attachment')</label>
                        <input type="file" class="form-control" name="attachment">
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
                const action = "{{ route('user.employee.create') }}"
                $modal.find('.modal-title').text("@lang('Add Employee')");
                $form.trigger('reset');
                $modal.find('select[name=gender]').trigger('change');
                $modal.find('select[name=company_id]').trigger('change');
                $form.attr('action', action);
                $modal.modal('show');
            });


            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.employee.update', ':id') }}";
                const employee = $(this).data('employee');
                $modal.find('.modal-title').text("@lang('Edit Employee')");
                $modal.find('input[name=name]').val(employee.name);
                $modal.find('select[name=gender]').val(employee.gender).trigger('change');
                $modal.find('input[name=dob]').val(employee.dob);
                $modal.find('input[name=email]').val(employee.email);
                $modal.find('input[name=country]').val(employee.country);
                $modal.find('input[name=phone]').val(employee.phone);
                $modal.find('input[name=joining_date]').val(employee.joining_date);
                $modal.find('select[name=company_id]').val(employee.company_id).trigger('change');
                $modal.find('select[name=department_id]').val(employee.department_id).trigger('change');
                $modal.find('select[name=designation_id]').val(employee.designation_id).trigger('change');
                $form.attr('action', action.replace(':id', employee.id));
                $modal.modal('show');
            });

            $(".date-pickers").flatpickr({
                calendar: true

            });

            $('.company-select').on('change', function() {
                const departments = $(this).find(`option:selected`).data('departments');
                let html = `<option selected disabled>@lang('Select One')</option>`;

                if (departments && departments.length > 0) {
                    $.each(departments, function(i, department) {
                        html +=
                            `<option value="${department.id}" data-designations='${JSON.stringify(department.designations)}'>${department.name}</option>`;
                    });
                } else {
                    html = `<option selected disabled>@lang('No Department Found')</option>`;
                }

                $('.department-select').html(html).trigger('change');
                $('.designation-select').html(
                    `<option selected disabled>@lang('Select Designation')</option>`); // reset designations
            });



            $('.department-select').on('change', function() {
                const designations = $(this).find(`option:selected`).data('designations');
                let html = `<option selected disabled>@lang('Select One')</option>`;

                if (designations && designations.length > 0) {
                    $.each(designations, function(i, designation) {
                        html += `<option value="${designation.id}">${designation.name}</option>`;
                    });
                } else {
                    html = `<option selected disabled>@lang('No Designation Found')</option>`;
                }

                $('.designation-select').html(html).trigger('change');
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
    <x-staff_permission_check permission="add employee">
        <x-panel.ui.btn.add tag="btn" />
    </x-staff_permission_check>
@endpush
