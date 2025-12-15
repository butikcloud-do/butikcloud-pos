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
                                    <th>@lang('Leave Type')</th>
                                    <th>@lang('Start Date')</th>
                                    <th>@lang('End Date')</th>
                                    <th>@lang('Days')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($requests as $request)
                                    <tr>
                                        <td>
                                            <div class="flex-thumb-wrapper">
                                                <div class="thumb">
                                                    <img class="thumb-img" src="{{ $request->employee->image_src }}">
                                                </div>
                                                <span class="ms-2">
                                                    {{ __(@$request->employee->name) }}<br>
                                                    {{ __(@$request->employee->phone) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>{{ __(@$request->leaveType->name) }}</td>
                                        <td>{{ $request->start_date }}</td>
                                        <td>{{ $request->end_date ?? '-' }}</td>
                                        <td>{{ $request->days }}</td>
                                        <td>
                                            @php
                                                echo $request->statusBadge;
                                            @endphp
                                        </td>

                                        <td class="dropdown">
                                            @if (request()->trash)
                                                <button type="button" class="btn btn-outline--success confirmationBtn"
                                                    data-question='@lang('Are you sure to restore this sale?')'
                                                    data-action="{{ route('user.leave.request.trash.restore', $request->id) }}">
                                                    <i class="las la-undo"></i>
                                                    @lang('Restore')
                                                </button>
                                            @else
                                                <button class=" btn btn-outline--primary" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    @lang('Action') <i class="las la-angle-down"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown">

                                                    <x-staff_permission_check permission="edit leave request">
                                                        <button type="button"
                                                            class="dropdown-list d-block w-100 text-start edit-btn"
                                                            data-request='@json($request)'>
                                                            <span class="me-1">
                                                                <i class="las la-pencil-alt text--dark"></i>
                                                            </span>
                                                            @lang('Edit')
                                                        </button>
                                                    </x-staff_permission_check>

                                                    <x-staff_permission_check permission="view leave request">
                                                        <a class="dropdown-list d-block w-100 text-start"
                                                            href="{{ route('admin.download.attachment', encrypt(getFilePath('leaveAttachment') . '/' . $request->attachment)) }}">
                                                            <span class="me-1">
                                                                <i class="las  la-file-download text--info"></i>
                                                            </span>
                                                            @lang('Attachment')
                                                        </a>
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
                        @if ($requests->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($requests) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <x-panel.ui.modal id="modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add request')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-panel.ui.modal.header>
        <x-panel.ui.modal.body>
            <form method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">

                    <div class="form-group col-lg-6">
                        <label>@lang('Employee')</label>
                        <select class="form-control form--control select2 employee-select" required name="employee_id">
                            <option value="">@lang('Select Employee')</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" data-department="{{ $employee->department_id }}"
                                    @selected(old('employee_id') == $employee->id)>
                                    {{ __($employee->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-lg-6">
                        <label>@lang('Leave Type')</label>
                        <select class="form-control form--control select2" required name="leave_type_id">
                            <option value="">@lang('Select Leave Type')</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}" @selected(old('type_id') == $type->id)>
                                    {{ __($type->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-lg-6">
                        <label>@lang('Start Date')</label>
                        <div class="input-group input--group">
                            <input type="text" class="form-control date-picker-leave" name="start_date"
                                value="{{ old('start_date') }}" required>
                            <span class="input-group-text">
                                <i class="las la-calendar"></i>
                            </span>
                        </div>
                    </div>

                    <div class="form-group col-lg-6">
                        <label>@lang('End Date')</label>
                        <div class="input-group input--group">
                            <input type="text" class="form-control date-picker-leave" value="{{ old('end_date') }}"
                                name="end_date">
                            <span class="input-group-text">
                                <i class="las la-calendar"></i>
                            </span>
                        </div>
                    </div>


                    <div class="mb-2 text-danger total-days-box">
                        @lang('Total Days') - <span class="total-days"> 0</span>
                    </div>


                    <div class="form-group col-lg-6">
                        <label>@lang('Attachment')</label>
                        <input type="file" class="form-control" name="attachment">
                    </div>

                    <div class="form-group col-lg-6">
                        <label>@lang('Status')</label>
                        <select class="form-control form--control select2" required name="status">
                            <option value="">@lang('Select Status')</option>
                            <option value="{{ Status::APPROVED }}" @selected(old('status') == Status::APPROVED)>@lang('Approved')</option>
                            <option value="{{ Status::PENDING }}" @selected(old('status') == Status::PENDING)>@lang('Pending')</option>
                            <option value="{{ Status::REJECTED }}" @selected(old('status') == Status::REJECTED)>@lang('Rejected')</option>
                        </select>
                    </div>

                    <div class="form-group col-lg-12">
                        <label>@lang('Reason')</label>
                        <textarea name="reason" class="form-control" cols="5" rows="5"></textarea>
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

@push('style')
    <style>
        .total-days-box {
            display: none;
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            const $modal = $('#modal');
            const $form = $modal.find('form');

            $('.add-btn').on('click', function() {
                const action = "{{ route('user.leave.request.create') }}"
                $modal.find('.modal-title').text("@lang('Add Leave Request')");
                $form.trigger('reset');
                $modal.find('select[name=leave_type_id]').trigger('change');
                $modal.find('select[name=status]').trigger('change');
                $('.total-days-box').hide();
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.leave.request.update', ':id') }}";
                const request = $(this).data('request');
                $modal.find('.modal-title').text("@lang('Edit Leave Request')");
                $modal.find('select[name=employee_id]').val(request.employee_id).trigger('change');
                $modal.find('select[name=leave_type_id]').val(request.leave_type_id).trigger('change');
                $modal.find('select[name=status]').val(request.status).trigger('change');
                $modal.find('input[name=start_date]').val(request.start_date);
                $modal.find('input[name=end_date]').val(request.end_date);
                $modal.find('textarea[name=reason]').val(request.reason);
                $('.total-days-box').show();
                $('.total-days').text(request.days);
                $modal.find('input[name=days]').val(request.days);
                $form.attr('action', action.replace(':id', request.id));
                $modal.modal('show');
            });

            $(".date-picker-leave").flatpickr({
                calendar: true,
                minDate: new Date(),
            });

            function calculateDays() {
                let startDateVal = $('input[name="start_date"]').val();
                let endDateVal = $('input[name="end_date"]').val();

                if (startDateVal && !endDateVal) {
                    $('.total-days-box').show();
                    $('.total-days').text('1');
                    return;
                }

                if (startDateVal && endDateVal) {
                    let startDate = new Date(startDateVal);
                    let endDate = new Date(endDateVal);

                    if (!isNaN(startDate) && !isNaN(endDate)) {
                        let timeDiff = endDate.getTime() - startDate.getTime();
                        let daysDiff = Math.floor(timeDiff / (1000 * 3600 * 24)) + 1;

                        if (daysDiff > 0) {
                            $('.total-days-box').show();
                            $('.total-days').text(daysDiff);
                        } else {
                            $('.total-days-box').show();
                            $('.total-days').text('Invalid range');
                        }
                    } else {
                        $('.total-days-box').hide();
                        $('.total-days').text('');
                    }
                }
            }

            $('input[name="start_date"], input[name="end_date"]').on('change', calculateDays);


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
    <x-staff_permission_check permission="add leave request">
        <x-panel.ui.btn.add tag="btn" />
    </x-staff_permission_check>
@endpush
