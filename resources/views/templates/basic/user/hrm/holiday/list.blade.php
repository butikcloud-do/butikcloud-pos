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
                                    <th>@lang('Holiday')</th>
                                    <th>@lang('Company')</th>
                                    <th>@lang('Start Date')</th>
                                    <th>@lang('End Date')</th>
                                    <th>@lang('Days')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($holidays as $holiday)
                                    <tr>
                                        <td>{{ __($holiday->title) }}</td>
                                        <td>{{ __(@$holiday->company->name) }}</td>
                                        <td>{{ $holiday->start_date }}</td>
                                        <td>
                                            {{ $holiday->end_date ??  $holiday->start_date }}
                                        </td>
                                        <td>{{ $holiday->days }}</td>
                                        <td>
                                            <x-panel.ui.btn.table_action module="holiday" :id="$holiday->id">
                                                <x-staff_permission_check permission="edit holiday">
                                                    <x-panel.ui.btn.edit tag="btn" :data-holiday="$holiday" />
                                                </x-staff_permission_check>
                                            </x-panel.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($holidays->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($holidays) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <x-panel.ui.modal id="modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add holiday')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-panel.ui.modal.header>
        <x-panel.ui.modal.body>
            <form method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-lg-12">
                        <label>@lang('Title')</label>
                        <input type="text" class="form-control" name="title" required value="{{ old('title') }}">
                    </div>
                    <div class="form-group col-lg-12">
                        <label>@lang('Company')</label>
                        <select class="form-control form--control select2" required name="company_id">
                            <option value="">@lang('Select Company')</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>{{ __($company->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-lg-6">
                        <label>@lang('Start Date')</label>
                        <div class="input-group input--group">
                            <input type="text" class="form-control date-picker-holiday" name="start_date"
                                value="{{ old('start_date') }}" required>
                            <span class="input-group-text">
                                <i class="las la-calendar"></i>
                            </span>
                        </div>
                    </div>

                    <div class="form-group col-lg-6">
                        <label>@lang('End Date')</label>
                        <div class="input-group input--group">
                            <input type="text" class="form-control date-picker-holiday" value="{{ old('end_date') }}"
                                name="end_date">
                            <span class="input-group-text">
                                <i class="las la-calendar"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-2 text-danger total-days-box">
                        @lang('Total Days'): <span class="total-days"> 0</span>
                    </div>

                    <div class="form-group col-lg-12">
                        <label>@lang('Description')</label>
                        <textarea name="description" class="form-control" cols="5" rows="5">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group col-lg-12">
                        <input type="checkbox" id="check-notify" name="notify">
                        <label for="check-notify">@lang('Send notification the all employee')</label>
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
                const action = "{{ route('user.holiday.create') }}"
                $modal.find('.modal-title').text("@lang('Add Holiday')");
                $form.trigger('reset');
                $modal.find('select[name=company_id]').trigger('change');
                $('.total-days-box').hide();
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.holiday.update', ':id') }}";
                const holiday = $(this).data('holiday');
                $modal.find('.modal-title').text("@lang('Edit Holiday')");
                $modal.find('select[name=company_id]').val(holiday.company_id).trigger('change');
                $modal.find('input[name=title]').val(holiday.title);
                $modal.find('input[name=start_date]').val(holiday.start_date);
                $modal.find('input[name=end_date]').val(holiday.end_date);
                $modal.find('textarea[name=description]').val(holiday.description);
                $('.total-days-box').show();
                $('.total-days').text(holiday.days);
                $form.attr('action', action.replace(':id', holiday.id));
                $modal.modal('show');
            });

            $(".date-picker-holiday").flatpickr({
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
    <x-staff_permission_check permission="add holiday">
        <x-panel.ui.btn.add tag="btn" />
    </x-staff_permission_check>
@endpush
