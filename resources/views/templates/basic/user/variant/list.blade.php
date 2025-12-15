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
                                    <th>@lang('Attribute')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($variants as $variant)
                                    <tr>
                                        <td>{{ __($variant->name) }}</td>
                                        <td>{{ __(@$variant->attribute->name) }}</td>
                                        <td>
                                            <x-panel.other.status_switch :status="$variant->status" :action="route('user.variant.status.change', $variant->id)"
                                                title="variant" />
                                        </td>
                                        <td>
                                            <x-panel.ui.btn.table_action module="variant" :id="$variant->id">
                                                <x-staff_permission_check permission="edit variant">
                                                <x-panel.ui.btn.edit tag="btn" :data-variant="$variant" />
                                                </x-staff_permission_check>
                                            </x-panel.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($variants->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($variants) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <x-panel.ui.modal id="modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add variant')</h4>
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
                    <label>@lang('Attribute')</label>
                    <select name="attribute" required class="form-control select2">
                        <option value="">@lang('Select One')</option>
                        @foreach ($attributes as $attribute)
                            <option value="{{ $attribute->id }}">
                                {{ __($attribute->name) }}
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
                const action = "{{ route('user.variant.create') }}";

                $modal.find('.modal-title').text("@lang('Add Variant')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.find('select[name=attribute]').val('');
                select2Initialize();
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.variant.update', ':id') }}";
                const variant = $(this).data('variant');

                $modal.find('.modal-title').text("@lang('Edit Variant')");
                $modal.find('input[name=name]').val(variant.name);
                $modal.find('select[name=attribute]').val(variant.attribute_id);
                $form.attr('action', action.replace(':id', variant.id));

                select2Initialize();
                $modal.modal('show');
            });

            function select2Initialize() {
                $.each($('.select2'), function() {
                    $(this)
                        .wrap(`<div class="position-relative"></div>`)
                        .select2({
                            dropdownParent: $(this).parent(),
                        });
                });
            }

        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
<x-staff_permission_check permission="add variant">
    <x-panel.ui.btn.add tag="btn" />
</x-staff_permission_check>
@endpush
