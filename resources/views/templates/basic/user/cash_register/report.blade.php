@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card>
                <x-panel.ui.card.body :paddingZero=true>
                    <x-panel.ui.table.layout :renderExportButton="false" :hasRecycleBin="false"
                        filterBoxLocation="Template::user.cash_register.filter_box">
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('Starting Time')</th>
                                    <th>@lang('Closing Time')</th>
                                    <th>@lang('Staring Amount')</th>
                                    <th>@lang('Closing Amount')</th>
                                    <th>@lang('Staff')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($cashRegisters as $cashRegister)
                                    <tr>
                                        <td>{{ showDateTime($cashRegister->starting_time) }} </td>
                                        <td>{{ showDateTime($cashRegister->closing_time) }} </td>
                                        <td>{{ showAmount($cashRegister->starting_amount) }} </td>
                                        <td>{{ showAmount($cashRegister->closing_amount) }} </td>
                                        <td>{{ __(@$cashRegister->user->username) }} </td>
                                        <td>
                                            <button class="btn btn--primary viewReport" type="button"
                                                data-id="{{ $cashRegister->id }}">
                                                <i class="fa-regular fa-eye"></i> @lang('View')
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($cashRegisters->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($cashRegisters) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <div id="modal" class="modal fade register-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Register Details')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-center align-items-center py-5 flex-column">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden"></span>
                        </div>
                        <h5 class="mt-3">@lang('Loading')...</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        "use strict";
        (function($) {
            $('.viewReport').on('click', function() {
                const id = $(this).data('id');
                const url = "{{ route('user.cash_register.report.details', ':id') }}";
                const $modal = $('#modal');

                $modal.modal('show');
                $.ajax({
                    type: "GET",
                    url: url.replace(':id', id),
                    timeout: 30000,
                    beforeSend: function() {
                        $modal.find('.modal-body').html(`
                            <div class="d-flex justify-content-center align-items-center py-5 flex-column">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden"></span>
                                </div>
                                <h5 class="mt-3">@lang('Loading')...</h5>
                            </div>
                        `);
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            setTimeout(() => {
                                $modal.find('.modal-body').html(response.data.html);
                            }, 1500);
                        } else {
                            notify('error', "@lang('Something went wrong, please try later.')");
                        }
                    },
                    error: function(error) {
                        notify('error', "@lang('Something went wrong, please try later.')");
                    }
                });

            });
        })(jQuery);
    </script>
@endpush


@push('style')
    <style>
        .register-modal .list-group-item {
            color: var(--bs-modal-color) !important;
        }

        .register-modal .list-group-item.list-group-title span {
            color: hsl(var(--title-color)) !important;
            font-weight: 600;
        }
    </style>
@endpush
