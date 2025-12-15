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
                                    <th>@lang('Short Name')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($units as $unit)
                                    <tr>
                                        <td>{{ __($unit->name) }}</td>
                                        <td>{{ __($unit->short_name) }}</td>
                                        <td>
                                            <x-panel.other.status_switch :status="$unit->status" :action="route('user.unit.status.change', $unit->id)"
                                                title="unit" />
                                        </td>
                                        <td>
                                            <x-panel.ui.btn.table_action module="unit" :id="$unit->id">
                                                <x-staff_permission_check permission="edit unit">
                                                <x-panel.ui.btn.edit tag="btn" :data-unit="$unit" />
                                                </x-staff_permission_check>
                                            </x-panel.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($units->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($units) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <x-panel.ui.modal id="modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add unit')</h4>
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
                    <label>@lang('Short Name')</label>
                    <input type="text" class="form-control" name="short_name" required value="{{ old('short_name') }}">
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
                const action = "{{ route('user.unit.create') }}";

                $modal.find('.modal-title').text("@lang('Add Unit')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.unit.update', ':id') }}";
                const unit = $(this).data('unit');

                $modal.find('.modal-title').text("@lang('Edit Unit')");
                $modal.find('input[name=name]').val(unit.name);
                $modal.find('input[name=short_name]').val(unit.short_name);
                $form.attr('action', action.replace(':id', unit.id));
                $modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
<x-staff_permission_check permission="add unit">
    <x-panel.ui.btn.add tag="btn" />
</x-staff_permission_check>
@endpush
