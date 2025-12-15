@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card>
                <x-panel.ui.card.body class="p-0">
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
                                @forelse($paymentTypes as $paymentType)
                                    <tr>
                                        <td>{{ __($paymentType->name) }}</td>
                                        <td>
                                            <x-panel.other.status_switch :status="$paymentType->status" :action="route('user.payment.type.status.change', $paymentType->id)"
                                                title="payment type" />
                                        </td>
                                        <td>

                                            <x-panel.ui.btn.table_action module="payment_type" :id="$paymentType->id">
                                                <x-staff_permission_check permission="edit payment type">
                                                    <x-panel.ui.btn.edit tag="btn" :data-paymentType="$paymentType" />
                                                </x-staff_permission_check>
                                            </x-panel.ui.btn.table_action>

                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($paymentTypes->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($paymentTypes) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <x-panel.ui.modal id="modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add Payment Type')</h4>
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
                    <label>@lang('Variant')</label>
                    <select class="form-control select2" name="variant" required data-minimum-results-for-search="-1"
                        data-width="100%">
                        <option value="primary" @selected(old('variant') == 'primary')>
                            @lang('Primary')
                        </option>
                        <option value="secondary" @selected(old('variant') == 'secondary')>
                            @lang('Secondary')
                        </option>
                        <option value="danger" @selected(old('variant') == 'danger')>
                            @lang('Danger')
                        </option>
                        <option value="info" @selected(old('variant') == 'info')>
                            @lang('Info')
                        </option>
                        <option value="warning" @selected(old('variant') == 'warning')>
                            @lang('Warning')
                        </option>
                        <option value="success" @selected(old('variant') == 'success')>
                            @lang('Success')
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label>@lang('Icon')</label>
                    <div class="input-group">
                        <input type="text" class="form-control iconPicker icon" autocomplete="off" name="icon"
                            required>
                        <span class="input-group-text  input-group-addon" data-icon="las la-home" role="iconpicker"></span>
                    </div>
                </div>
                <div class="form-group">
                    <x-panel.ui.btn.modal />
                </div>
            </form>
        </x-panel.ui.modal.body>
    </x-panel.ui.modal>

    <x-confirmation-modal />
@endsection


@push('style')
    <style>
        .popover-title input {
            color: #000 !important;
        }

        .popover-title input::placeholder {
            color: #000000a7;
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
                const action = "{{ route('user.payment.type.create') }}";

                $modal.find('.modal-title').text("@lang('Add Payment Type')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.payment.type.update', ':id') }}";
                const paymentType = $(this).data('paymenttype');

                $modal.find('.modal-title').text("@lang('Edit Payment Type')");
                $modal.find('input[name=name]').val(paymentType.name);
                $modal.find('select[name=variant]').val(paymentType.variant);
                $modal.find('input[name=icon]').val(paymentType.icon);
                $form.attr('action', action.replace(':id', paymentType.id));
                $modal.modal('show');
            });

            $('.iconPicker').iconpicker().on('iconpickerSelected', function(e) {
                $(this).closest('.form-group').find('.iconpicker-input').val(
                    `<i class="${e.iconpickerValue}"></i>`);
            });

        })(jQuery);
    </script>
@endpush

@push('breadcrumb-plugins')
    <x-staff_permission_check permission="add payment type">
        <x-panel.ui.btn.add tag="btn" />
    </x-staff_permission_check>
@endpush



@push('style-lib')
    <link href="{{ asset('assets/ovopanel/css/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
@endpush


@push('script-lib')
    <script src="{{ asset('assets/ovopanel/js/fontawesome-iconpicker.js') }}"></script>
@endpush
