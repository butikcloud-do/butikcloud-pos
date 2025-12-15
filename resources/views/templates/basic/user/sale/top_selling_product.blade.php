@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card>
                <x-panel.ui.card.body :paddingZero=true>
                    <x-panel.ui.table.layout :renderExportButton="false" :hasRecycleBin="false">
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('Product')</th>
                                    <th>@lang('Total')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($topSellingProducts as $topSellingProduct)
                                    @php
                                        $productDetails = $topSellingProduct->productDetail;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-start">
                                                <span class="table-thumb">
                                                    <img src="{{ @$productDetails->product->image_src }}">
                                                </span>
                                                <div>
                                                    <strong class="d-block text-start">
                                                        {{ __(@$productDetails->product->name) }}<br>
                                                        @if (@$productDetails->product->product_type == Status::PRODUCT_TYPE_VARIABLE)
                                                            <span>
                                                                {{ __(@$productDetails->attribute->name) }}
                                                                - {{ __(@$productDetails->variant->name) }}
                                                            </span>
                                                        @endif
                                                        <span class="fw-bold"> -
                                                            {{ @$productDetails->sku }}</span>
                                                    </strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge--success">
                                                {{ $topSellingProduct->total_quantity }}
                                                {{ __(@$productDetails->product->unit->short_name) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>

                        @if ($topSellingProducts->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($topSellingProducts) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>
@endsection


@push('script')
    <script>
        "use strict";
        (function($) {
            const $paymentModal = $('#payment-modal');
            const $paymentHistoryModal = $('#payment-history-modal');
            const $statusModal = $('#status-modal');



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
                                <span>{{ gs('cur_sym',getParentUser()->id) }}${getAmount(payment.amount)} </span>
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
                                    <span>{{ gs('cur_sym',getParentUser()->id) }}${showAmount(sale.total)}</span>
                                </div>
                                <div class="d-flex gap-2 flex-wrap justify-content-between">
                                    <span class="text-muted">@lang('Paid Amount')</span>
                                    <span>{{ gs('cur_sym',getParentUser()->id) }}${showAmount(sale.payments_sum_amount)}</span>
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
