@extends($activeTemplate . 'layouts.master')
@section('panel')
    @include('Template::user.sale.widget')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card>
                <x-panel.ui.card.body :paddingZero=true>
                    <x-panel.ui.table.layout filterBoxLocation="Template::user.sale.filter_box_sale" hasRecycleBin="false" :hasRecycleBin="false">
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('Invoice Number') | @lang('Total Items')</th>
                                    <th>@lang('Sale Date') | @lang('Created At')</th>
                                    <th>@lang('Warehouse') | @lang('Customer')</th>
                                    <th>@lang('Total Amount') | @lang('Paid Amount')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Add By')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @php
                                    \Log::info('Sale list view - Processing sales', [
                                        'total_sales' => $sales->count(),
                                        'sales_data' => $sales->map(function($sale) {
                                            return [
                                                'id' => $sale->id,
                                                'invoice_number' => $sale->invoice_number,
                                                'sale_details_count' => $sale->sale_details_count ?? 'NOT_SET',
                                                'has_sale_details_relation' => method_exists($sale, 'saleDetails'),
                                                'actual_count_via_relation' => method_exists($sale, 'saleDetails') ? $sale->saleDetails()->count() : 'N/A'
                                            ];
                                        })->toArray()
                                    ]);
                                @endphp
                                @forelse($sales as $sale)
                                    <tr>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ __($sale->invoice_number) }}</span>
                                                @php
                                                    $count = $sale->sale_details_count ?? 0;
                                                    \Log::info('Sale list view - Rendering count', [
                                                        'sale_id' => $sale->id,
                                                        'invoice_number' => $sale->invoice_number,
                                                        'sale_details_count' => $count,
                                                        'display_value' => $count . ' Items'
                                                    ]);
                                                @endphp
                                                <span>{{ __($count) }} @lang('Items') </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ showDateTime($sale->sale_date, 'Y-m-d') }}</span>
                                                <span>{{ showDateTime($sale->created_at) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ __(@$sale->warehouse->name) }}</span>
                                                <span>{{ __(@$sale->customer->name) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ showAmount($sale->total) }}</span>
                                                <span
                                                    class="text--success">{{ showAmount($sale->payments_sum_amount) }}</span>
                                            </div>
                                        </td>
                                        <td> @php echo $sale->statusBadge @endphp </td>
                                        <td> {{ __(@$sale->saleBy->username) }} </td>
                                        <td class="dropdown">
                                            @if (request()->trash)
                                                <button type="button" class="btn btn-outline--success confirmationBtn"
                                                    data-question='@lang('Are you sure to restore this sale?')'
                                                    data-action="{{ route('user.sale.trash.restore', $sale->id) }}">
                                                    <i class="las la-undo"></i>
                                                    @lang('Restore')
                                                </button>
                                            @else
                                                <button class=" btn btn-outline--primary" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    @lang('Action') <i class="las la-angle-down"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown">
                                                    <x-staff_permission_check permission="edit sale">
                                                        <a class="dropdown-list d-block w-100 text-start"
                                                            href="{{ route('user.sale.edit', $sale->id) }}">
                                                            <span class="me-1">
                                                                <i class="las la-pencil-alt text--primary"></i>
                                                            </span>
                                                            @lang('Edit Sale')
                                                        </a>
                                                    </x-staff_permission_check>
                                                    <x-staff_permission_check permission="view sale">
                                                        <a class="dropdown-list d-block w-100 text-start"
                                                            href="{{ route('user.sale.view', $sale->id) }}">
                                                            <span class="me-1">
                                                                <i class="las la-eye text--dark"></i>
                                                            </span>
                                                            @lang('View Invoice')
                                                        </a>
                                                    </x-staff_permission_check>

                                                    <x-staff_permission_check permission="print sale invoice">
                                                        <button type="button"
                                                            class="dropdown-list d-block w-100 text-start print-btn"
                                                            data-action="{{ route('user.sale.print', $sale->id) }}?invoice_type=regular">
                                                            <span class="me-1">
                                                                <i class="las la-print text--success"></i>
                                                            </span>
                                                            @lang('Print Invoice')
                                                        </button>
                                                    </x-staff_permission_check>

                                                    <x-staff_permission_check permission="print pos sale invoice">
                                                        <button type="button"
                                                            class="dropdown-list d-block w-100 text-start print-btn"
                                                            data-action="{{ route('user.sale.print', $sale->id) }}?invoice_type=pos">
                                                            <span class="me-1">
                                                                <i class="las la-print text--dark"></i>
                                                            </span>
                                                            @lang('Print POS Invoice')
                                                        </button>
                                                    </x-staff_permission_check>

                                                    <x-staff_permission_check permission="download sale invoice">
                                                        <a class="dropdown-list d-block w-100 text-start"
                                                            href="{{ route('user.sale.pdf', $sale->id) }}">
                                                            <span class="me-1">
                                                                <i class="las  la-file-download text--info"></i>
                                                            </span>
                                                            @lang('Download Invoice')
                                                        </a>
                                                    </x-staff_permission_check>

                                                    <x-staff_permission_check permission="view sale payment">
                                                        <button type="button"
                                                            class="dropdown-list d-block w-100 text-start payment-history"
                                                            data-sale='@json($sale)'>
                                                            <span class="me-1">
                                                                <i class="las la-list text--dark"></i>
                                                            </span>
                                                            @lang('Payment History')
                                                        </button>
                                                    </x-staff_permission_check>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($sales->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($sales) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>


    <x-panel.ui.modal id="payment-history-modal" class="modal-xl">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Payment History')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-panel.ui.modal.header>
        <x-panel.ui.modal.body>

        </x-panel.ui.modal.body>
    </x-panel.ui.modal>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-staff_permission_check permission="add sale">
        <x-panel.ui.btn.add href="{{ route('user.sale.add') }}" text="New Sale" />
    </x-staff_permission_check>
@endpush


@push('script')
    <script>
        "use strict";
        (function($) {
            // Debug logging for sale details count
            console.log('Sale list page loaded');
            $('.table tbody tr').each(function(index) {
                var $row = $(this);
                var invoiceNumber = $row.find('td:first .d-block').text();
                var itemsText = $row.find('td:first span:last').text();
                console.log('Row ' + index + ': Invoice ' + invoiceNumber + ' - ' + itemsText);
            });
            const $paymentModal = $('#payment-modal');
            const $paymentHistoryModal = $('#payment-history-modal');

            $(".payment-history").on('click', function() {
                const sale = $(this).data('sale');
                let html = "";
                $.each(sale.payments, function(i, payment) {
                    html += `
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between  gap-2 flex-wrap ps-0">
                                <span class="text-muted">@lang('Date')</span>
                                <span>${payment.date}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between  gap-2 flex-wrap ps-0">
                                <span class="text-muted">@lang('Amount')</span>
                                <span>{{ gs('cur_sym', getParentUser()->id) }}${getAmount(payment.amount)} </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between  gap-2 flex-wrap ps-0">
                                <span class="text-muted">@lang('Payment Method')</span>
                                <span>${payment?.payment_type?.name}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between  gap-2 flex-wrap ps-0">
                                <span class="text-muted">@lang('Payment Note')</span>
                                <span>${payment?.note || 'N/A'}</span>
                            </li>
                        </ul>
                    ${sale.payments.length == (i+1) ? '' : '<hr/>' }
                    `
                });

                $paymentHistoryModal.find('.modal-body').html(`
                    <div class="row gy-4 justify-content-between">
                        <div class="col-lg-4">
                            <h6 class="mb-2">@lang('Customer Information')</h6>
                            <div class="information">
                                <div class="d-flex gap-2 flex-wrap justify-content-between">
                                    <span class="text-muted">@lang('Name')</span>
                                    <span>${sale?.customer?.name || 'N/A'}</span>
                                </div>
                                <div class="d-flex gap-2 flex-wrap justify-content-between">
                                    <span class="text-muted">@lang('Email')</span>
                                    <span>${sale?.customer?.email || 'N/A'}</span>
                                </div>
                                <div class="d-flex gap-2 flex-wrap justify-content-between">
                                    <span class="text-muted">@lang('Mobile')</span>
                                    <span>${sale?.customer?.mobile || 'N/A'}</span>
                                </div>
                                <div class="d-flex gap-2 flex-wrap justify-content-between">
                                    <span class="text-muted">@lang('Address')</span>
                                    <span>${sale?.customer?.address || 'N/A'}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <h6 class="mb-2 text-end">@lang('Sale Information')</h6>
                            <div class="information">
                                <div class="d-flex gap-2 flex-wrap justify-content-between">
                                    <span class="text-muted">@lang('Invoice Number')</span>
                                    <span>${sale?.invoice_number}</span>
                                </div>
                                <div class="d-flex gap-2 flex-wrap justify-content-between">
                                    <span class="text-muted">@lang('Date')</span>
                                    <span>${sale?.sale_date}</span>
                                </div>
                                <div class="d-flex gap-2 flex-wrap justify-content-between">
                                    <span class="text-muted">@lang('Total Amount')</span>
                                    <span>{{ gs('cur_sym', getParentUser()->id) }}${showAmount(sale.total)}</span>
                                </div>
                                <div class="d-flex gap-2 flex-wrap justify-content-between">
                                    <span class="text-muted">@lang('Paid Amount')</span>
                                    <span>{{ gs('cur_sym', getParentUser()->id) }}${showAmount(sale.payments_sum_amount)}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                             <h6 class="mb-2">@lang('Payments Information')</h6>
                            ${html}
                        </div>
                    </div>
                `);
                $paymentHistoryModal.modal('show');
            });

            $(".print-btn").on('click', function() {
                const action = $(this).data('action');
                $.ajax({
                    type: "GET",
                    url: action,
                    success: function(response) {
                        if (response.status == 'success') {
                            $('body')
                                .append(`<div class="print-content">${response.data.html}</div>`);
                            window.print();
                        } else {
                            notify('error', response.message);
                        }
                    }
                });
            });

            $(window).on('afterprint', function() {
                $('body').find('.print-content').remove();
            });

        })(jQuery);
    </script>
@endpush
@push('style')
    <style>
        .btn-outline--primary i {
            transition: .2s linear;
        }

        .btn-outline--primary.show i {
            transform: rotate(180deg);
        }
    </style>
@endpush



@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/ovopanel/css/invoice.css') }}">
@endpush
