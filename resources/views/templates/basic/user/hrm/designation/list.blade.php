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
                                    <th>@lang('Designation')</th>
                                    <th>@lang('Company')</th>
                                    <th>@lang('Department')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($designations as $designation)
                                    <tr>
                                        <td>{{ __($designation->name) }}</td>
                                        <td>{{ __(@$designation->company->name) }}</td>
                                        <td>{{ __(@$designation->department->name) }}</td>
                                        <td>
                                            <x-panel.other.status_switch :status="$designation->status" :action="route('user.designation.status.change', $designation->id)"
                                                title="designation" />
                                        </td>
                                        <td>
                                            <x-panel.ui.btn.table_action module="designation" :id="$designation->id">
                                                <x-staff_permission_check permission="edit designation">
                                                    <x-panel.ui.btn.edit tag="btn" :data-designation="$designation" />
                                                </x-staff_permission_check>
                                            </x-panel.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($designations->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($designations) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <x-panel.ui.modal id="modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add Designation')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-panel.ui.modal.header>
        <x-panel.ui.modal.body>
            <form method="POST">
                @csrf
                <div class="form-group">
                    <label>@lang('Company')</label>
                    <select class="form-control form--control select2 company-select" required name="company_id">
                        <option value="">@lang('Select Company')</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)
                                data-departments='@json($company->departments)'>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>@lang('Department')</label>
                    <select class="form-control form--control select2 department-select" required name="department_id">
                        <option value="">@lang('Please Select The Company')</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>@lang('Name')</label>
                    <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <x-panel.ui.btn.modal />
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
                const action = "{{ route('user.designation.create') }}"
                $modal.find('.modal-title').text("@lang('Add Designation')");
                $form.trigger('reset');
                $modal.find('select[name=company_id]').trigger('change');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.designation.update', ':id') }}";
                const designation = $(this).data('designation');
                $modal.find('.modal-title').text("@lang('Edit Designation')");
                $modal.find('input[name=name]').val(designation.name);
                $modal.find('select[name=company_id]').val(designation.company_id).trigger('change');
                $modal.find('select[name=department_id]').val(designation.department_id).trigger('change');
                $form.attr('action', action.replace(':id', designation.id));
                $modal.modal('show');
            });

            $('.company-select').on('change', function() {
                const departments = $(this).find(`option:selected`).data('departments');
                let html = `<option selected disabled>@lang('Select One')</option>`;

                if (departments && departments.length > 0) {
                    $.each(departments, function(i, department) {
                        html += `<option value="${department.id}">${department.name}</option>`;
                    });
                } else {
                    html = `<option selected disabled>@lang('No Department Found')</option>`;
                }
                $('.department-select').html(html).trigger('change');

            });

        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
    <x-staff_permission_check permission="add designation">
        <x-panel.ui.btn.add tag="btn" />
    </x-staff_permission_check>
@endpush
