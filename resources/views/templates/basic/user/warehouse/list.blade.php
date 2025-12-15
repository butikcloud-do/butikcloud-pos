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
                                    <th>@lang('Contact Number')</th>
                                    <th>@lang('Address')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($warehouses as $warehouse)
                                    <tr>
                                        <td>
                                            {{ __($warehouse->name) }}
                                        </td>
                                        <td>{{ $warehouse->contact_number }}</td>
                                        <td>{{ __($warehouse->address) }}</td>
                                        <td>
                                            <x-panel.other.status_switch :status="$warehouse->status" :action="route('user.warehouse.status.change', $warehouse->id)"
                                                title="warehouse" />
                                        </td>
                                        <td>
                                            <x-panel.ui.btn.table_action module="warehouse" :id="$warehouse->id">
                                                <x-staff_permission_check permission="edit warehouse">
                                                    <x-panel.ui.btn.edit tag="btn" :data-warehouse="$warehouse" />
                                                </x-staff_permission_check>
                                            </x-panel.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($warehouses->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($warehouses) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <x-panel.ui.modal id="modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add Warehouse')</h4>
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
                    <div class="form-group col-lg-12">
                        <label>@lang('Contact Mobile')</label>
                        <input type="tel" class="form-control" name="contact_number" value="{{ old('contact_number') }}"
                            required>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Address')</label>
                        <input type="text" class="form-control" name="address" value="{{ old('address') }}" required>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('City')</label>
                        <input type="text" class="form-control" name="city" value="{{ old('city') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('State')</label>
                        <input type="text" class="form-control" name="state" value="{{ old('state') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Postcode')</label>
                        <input type="text" class="form-control" name="postcode" value="{{ old('postcode') }}">
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
                const action = "{{ route('user.warehouse.create') }}"
                $modal.find('.modal-title').text("@lang('Add Warehouse')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.warehouse.update', ':id') }}";
                const warehouse = $(this).data('warehouse');
                $modal.find('.modal-title').text("@lang('Edit Warehouse')");
                $modal.find('input[name=name]').val(warehouse.name);
                $modal.find('input[name=contact_number]').val(warehouse.contact_number);
                $modal.find('input[name=address]').val(warehouse.address);
                $modal.find('input[name=city]').val(warehouse.city);
                $modal.find('input[name=state]').val(warehouse.state);
                $modal.find('input[name=postcode]').val(warehouse.postcode);
                $form.attr('action', action.replace(':id', warehouse.id));
                $modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush

@push('breadcrumb-plugins')
    <x-staff_permission_check permission="add warehouse">
        <x-panel.ui.btn.add tag="btn" />
    </x-staff_permission_check>
@endpush
