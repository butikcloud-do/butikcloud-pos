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
                                    <th>@lang('Type')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($types as $type)
                                    <tr>
                                        <td>{{ __($type->name) }}</td>
                                        <td>
                                            <x-panel.other.status_switch :status="$type->status" :action="route('user.leave.type.status.change', $type->id)"
                                                title="leave type" />
                                        </td>
                                        <td>
                                            <x-panel.ui.btn.table_action module="leave.type" :id="$type->id">
                                                <x-staff_permission_check permission="edit leave type">
                                                    <x-panel.ui.btn.edit tag="btn" :data-type="$type" />
                                                </x-staff_permission_check>
                                            </x-panel.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($types->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($types) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <x-panel.ui.modal id="modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add Company')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-panel.ui.modal.header>
        <x-panel.ui.modal.body>
            <form method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-lg-12">
                        <label>@lang('Name')</label>
                        <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
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
                const action = "{{ route('user.leave.type.create') }}"
                $modal.find('.modal-title').text("@lang('Add Leave Type')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.leave.type.update', ':id') }}";
                const type = $(this).data('type');
                $modal.find('.modal-title').text("@lang('Edit Leave Type')");
                $modal.find('input[name=name]').val(type.name);
                $form.attr('action', action.replace(':id', type.id));
                $modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
    <x-staff_permission_check permission="add leave type">
        <x-panel.ui.btn.add tag="btn" />
    </x-staff_permission_check>
@endpush
