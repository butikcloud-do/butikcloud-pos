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
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($attributes as $attribute)
                                    <tr>
                                        <td>{{ __($attribute->name) }}</td>
                                        <td>
                                            <x-panel.other.status_switch :status="$attribute->status" :action="route('user.attribute.status.change', $attribute->id)"
                                                title="attribute" />
                                        </td>
                                        <td>
                                            <x-panel.ui.btn.table_action module="attribute" :id="$attribute->id">
                                                <x-staff_permission_check permission="edit attribute">
                                                <x-panel.ui.btn.edit tag="btn" :data-attribute="$attribute" />
                                                </x-staff_permission_check>
                                            </x-panel.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <x-panel.ui.modal id="modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add attribute')</h4>
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
                const action = "{{ route('user.attribute.create') }}";
                $modal.find('.modal-title').text("@lang('Add Attribute')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.attribute.update', ':id') }}";
                const attribute = $(this).data('attribute');

                $modal.find('.modal-title').text("@lang('Edit Attribute')");
                $modal.find('input[name=name]').val(attribute.name);
                $form.attr('action', action.replace(':id', attribute.id));
                $modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
<x-staff_permission_check permission="add attribute">
    <x-panel.ui.btn.add tag="btn" />
</x-staff_permission_check>
@endpush
