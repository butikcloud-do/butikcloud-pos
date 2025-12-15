@extends($activeTemplate . 'layouts.master')
@section('panel')
    <x-panel.ui.card class="table-has-filter">
        <x-panel.ui.card.body :paddingZero="true">
            <x-panel.ui.table.layout :renderExportButton="false">
                <x-panel.ui.table>
                    <x-panel.ui.table.header>
                        <tr>
                            <th>@lang('Gateway | Transaction')</th>
                            <th>@lang('Initiated')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Conversion')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Details')</th>
                        </tr>
                    </x-panel.ui.table.header>
                    <x-panel.ui.table.body>
                        @forelse($deposits as $deposit)
                            <tr>
                                <td>
                                    <div class="text-start">
                                        <span class="fw-bold">
                                            <span class="text--primary">
                                                @if ($deposit->method_code < 5000)
                                                    {{ __(@$deposit->gateway->name) }}
                                                @else
                                                    @lang('Google Pay')
                                                @endif
                                            </span>
                                        </span>
                                        <br>
                                        <small> {{ $deposit->trx }} </small>
                                    </div>
                                </td>

                                <td>
                                    <div class="text-end text-md-center">
                                        {{ showDateTime($deposit->created_at) }}<br>{{ diffForHumans($deposit->created_at) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-end text-md-center">
                                        {{ showAmount($deposit->amount) }} + <span class="text--danger"
                                            data-bs-toggle="tooltip"
                                            title="@lang('Processing Charge')">{{ showAmount($deposit->charge) }}
                                        </span>
                                        <br>
                                        <strong data-bs-toggle="tooltip" title="@lang('Amount with charge')">
                                            {{ showAmount($deposit->amount + $deposit->charge) }}
                                        </strong>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-end text-md-center">
                                        {{ showAmount(1, forceDefault: true) }} =
                                        {{ showAmount($deposit->rate, currencyFormat: false) }}
                                        {{ __($deposit->method_currency) }}
                                        <br>
                                        <strong>{{ showAmount($deposit->final_amount, currencyFormat: false) }}
                                            {{ __($deposit->method_currency) }}
                                        </strong>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-end text-md-center">
                                        @php echo $deposit->statusBadge @endphp
                                    </div>
                                </td>
                                @php
                                    $details = [];
                                    if ($deposit->method_code >= 1000 && $deposit->method_code <= 5000) {
                                        foreach (@$deposit->detail ?? [] as $key => $info) {
                                            $details[] = $info;
                                            if ($info->type == 'file') {
                                                $details[$key]->value = route(
                                                    'user.download.attachment',
                                                    encrypt(getFilePath('verify') . '/' . $info->value),
                                                );
                                            }
                                        }
                                    }
                                @endphp

                                <td>
                                    @if ($deposit->method_code >= 1000 && $deposit->method_code <= 5000)
                                        <a href="javascript:void(0)" class="btn btn--primary  detailBtn"
                                            data-info="{{ json_encode($details) }}"
                                            @if ($deposit->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $deposit->admin_feedback }}" @endif>
                                            <i class="fas fa-desktop"></i>
                                        </a>
                                    @else
                                        <span class="badge badge--success">
                                            @lang('Automatically processed')
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <x-panel.ui.table.empty_message />
                        @endforelse
                    </x-panel.ui.table.body>
                </x-panel.ui.table>
                @if ($deposits->hasPages())
                    <x-panel.ui.table.footer>
                        {{ paginateLinks($deposits) }}
                    </x-panel.ui.table.footer>
                @endif
            </x-panel.ui.table.layout>
        </x-panel.ui.card.body>
    </x-panel.ui.card>



    {{-- APPROVE MODAL --}}
    <div id="detailModal" class="modal fade custom--modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <ul class="list-group userData mb-2 list-group-flush">
                    </ul>
                    <div class="feedback"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark " data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');

                var userData = $(this).data('info');
                var html = '';
                if (userData) {
                    userData.forEach(element => {
                        if (element.type != 'file') {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${element.name}</span>
                                <span">${element.value}</span>
                            </li>`;
                        } else {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${element.name}</span>
                                <span"><a href="${element.value}"><i class="fa-regular fa-file"></i> @lang('Attachment')</a></span>
                            </li>`;
                        }
                    });
                }

                modal.find('.userData').html(html);

                if ($(this).data('admin_feedback') != undefined) {
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                } else {
                    var adminFeedback = '';
                }

                modal.find('.feedback').html(adminFeedback);


                modal.modal('show');
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-title], [data-bs-title]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

        })(jQuery);
    </script>
@endpush


@push('breadcrumb-plugins')
    <form>
        <div class="mb-3 d-flex justify-content-end">
            <div class="input-group">
                <input type="search" name="search" class="form-control" value="{{ request()->search }}"
                    placeholder="@lang('Search by transactions')">
                <button class="input-group-text bg--primary text-white">
                    <i class="las la-search"></i>
                </button>
            </div>
        </div>
    </form>
@endpush
