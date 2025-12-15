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
                                    <th>@lang('Shift')</th>
                                    <th>@lang('Company')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($shifts as $shift)
                                    <tr>
                                        <td>{{ __($shift->name) }}</td>
                                        <td>{{ __(@$shift->company->name) }}</td>
                                        <td>
                                            <x-panel.other.status_switch :status="$shift->status" :action="route('user.shift.status.change', $shift->id)"
                                                title="shift" />
                                        </td>
                                        <td>
                                            <x-panel.ui.btn.table_action module="shift" :id="$shift->id">
                                                <x-staff_permission_check permission="edit shift">
                                                    <x-panel.ui.btn.edit tag="btn" :data-shift="$shift" />
                                                </x-staff_permission_check>
                                            </x-panel.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($shifts->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($shifts) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <x-panel.ui.modal id="modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add Shift')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-panel.ui.modal.header>
        <x-panel.ui.modal.body>
            <form method="POST">
                @csrf
                <div class="form-group">
                    <label>@lang('Name')</label>
                    <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label>@lang('Company')</label>
                    <select class="form-control form--control select2" required name="company_id">
                        <option value="">@lang('Select Company')</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>{{ __($company->name) }}
                            </option>
                        @endforeach
                    </select>
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
                const action = "{{ route('user.shift.create') }}"
                $modal.find('.modal-title').text("@lang('Add Shift')");
                $form.trigger('reset');
                $modal.find('select[name=company_id]').trigger('change');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.shift.update', ':id') }}";
                const shift = $(this).data('shift');
                $modal.find('.modal-title').text("@lang('Edit Shift')");
                $modal.find('input[name=name]').val(shift.name);
                $modal.find('select[name=company_id]').val(shift.company_id).trigger('change');
                $form.attr('action', action.replace(':id', shift.id));
                $modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
    <x-staff_permission_check permission="add shift">
        <x-panel.ui.btn.add tag="btn" />
    </x-staff_permission_check>
@endpush
