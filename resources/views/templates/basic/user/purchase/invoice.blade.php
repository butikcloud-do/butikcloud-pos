@php
    $companyInformation = gs('company_information',getParentUser()->id);
@endphp
<div class="invoice invoice-wrapper">
    <div class="invoice-header">
        <div class="invoice-logo">
            <img class="img-fluid dark-show" src="{{ getImage(getFilePath('logoIcon') . '/' . gs('logo_dark', getParentUser()->id)) }}" alt="image">
            <img class="img-fluid light-show" src="{{ getImage(getFilePath('logoIcon') . '/' . gs('logo_light', getParentUser()->id)) }}" alt="image">
        </div>
        <ul class="invoice-company-info">
            <li class="invoice-company-info__item">
                <span class="label">@lang('Address'):</span>
                {{ __(@$companyInformation->address) }}
            </li>
            <li class="invoice-company-info__item">
                <span class="label">@lang('Email'):</span>
                {{ @$companyInformation->email ?? __('N/A') }}
            </li>
            <li class="invoice-company-info__item">
                <span class="label">@lang('Phone'):</span>
                {{ @$companyInformation->phone ?? __('N/A') }}
            </li>
        </ul>
    </div>
    <div class="invoice-body">
        <div class="invoice-body__top mb-4">
            <div class="invoice-customer">
                <h6 class="invoice-customer__title">@lang('Supplier Information')</h6>
                <ul class="invoice-customer-info">
                    <li class="invoice-customer-info__item"><span class="label">@lang('Name'):</span>
                        <span class="value">{{ __(@$purchase->supplier->name) }}</span>
                    </li>
                    <li class="invoice-customer-info__item"><span class="label">@lang('Email'):</span>
                        <span class="value">{{ @$purchase->supplier->email ?? 'N/A' }}</span>
                    </li>
                    <li class="invoice-customer-info__item"><span class="label">@lang('Mobile'):</span>
                        <span class="value">{{ @$purchase->supplier->mobile ?? 'N/A' }}</span>
                    </li>
                    <li class="invoice-customer-info__item"><span class="label">@lang('Address'):</span>
                        <span class="value">{{ __(@$purchase->supplier->address ?? 'N/A') }}</span>
                    </li>
                </ul>
            </div>

            <ul class="invoice-info">
                <li class="invoice-info__item">
                    <span class="label">@lang('Purchase Date')</span>
                    <p class="value">
                        {{ showDateTime($purchase->purchase_date, 'F d, Y') }}
                        <br>
                        <span>{{ showDateTime($purchase->created_at, 'h:m A') }}</span>
                    </p>
                </li>
                <li class="invoice-info__item">
                    <span class="label">@lang('Invoice Number')</span>
                    <p class="value">
                        {{ __($purchase->invoice_number) }}
                        <br>
                        <span>@lang('Reference Number:') {{ __($purchase->reference_number ?? 'N/A') }}</span>
                    </p>
                </li>
            </ul>
        </div>
        <div class="invoice-table-responsive mb-4">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th class="text-start">@lang('Product')</th>
                        <th class="text-center">@lang('Qty')</th>
                        <th class="text-center">@lang('Purchase Price')</th>
                        <th class="text-end">@lang('Subtotal')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->purchaseDetails as $purchaseDetail)
                        <tr>
                            <td class="text-start">
                                {{ strLimit(__(@$purchaseDetail->product->name), 10) }}
                                <span class="fw-bold"> - {{ @$purchaseDetail->productDetail->sku }}</span>
                                @if (@$purchaseDetail->product->product_type == Status::PRODUCT_TYPE_VARIABLE)
                                    <span>
                                        - {{ __(@$purchaseDetail->productDetail->attribute->name) }}
                                        - {{ __(@$purchaseDetail->productDetail->variant->name) }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                {{ getAmount($purchaseDetail->quantity) }}
                                {{ __(@$purchaseDetail->product->unit->name) }}
                            </td>
                            <td class="text-center text-nowrap">
                                {{ showAmount($purchaseDetail->purchase_price) }}
                            </td>
                            <td class="text-end">
                                {{ showAmount($purchaseDetail->purchase_price * $purchaseDetail->quantity) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="invoice-body__bottom">
            <div class="w-100 d-flex flex-wrap flex-sm-nowrap align-items-start justify-content-between gap-3 mb-3">
                <div class="invoice-payment">
                    <h6 class="invoice-payment__title mb-2">@lang('Payment Details')</h6>
                    @forelse ($purchase->supplierPayments as $payment)
                        <ul class="invoice-payment-info">
                            <li class="invoice-payment-info__item">
                                <span class="label">@lang('Payment Type'):</span>
                                <span class="value">{{ __(@$payment->paymentType->name) }}</span>
                            </li>
                            <li class="invoice-payment-info__item">
                                <span class="label">@lang('Payment Amount'):</span>
                                <span class="value">{{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($payment->amount, currencyFormat: false) }}</span>
                            </li>
                            <li class="invoice-payment-info__item">
                                <span class="label">@lang('Payment Date'):</span>
                                <span class="value">{{ $payment->payment_date }}</span>
                            </li>
                            <li class="invoice-payment-info__item">
                                <span class="label">@lang('Payment Note'):</span>
                                <span class="value">{{ __($payment->note) }}</span>
                            </li>
                        </ul>
                        @if ($purchase->supplierPayments->count() != $loop->last)
                            <hr>
                        @endif
                    @empty
                        <span class="text-muted">@lang('No payment found against this invoice.')</span>
                    @endforelse
                </div>

                <div class="invoice-payment">
                    <h6 class="invoice-payment__title mb-2">@lang('Payment Summary')</h6>
                    <ul class="invoice-payment-info">
                        <li class="invoice-payment-info__item">
                            <span class="label">@lang('Total Payable'):</span>
                            <span class="value">{{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($purchase->total, currencyFormat: false) }}</span>
                        </li>
                        <li class="invoice-payment-info__item">
                            <span class="label">@lang('Total Paid'):</span>
                            <span class="value">{{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($purchase->supplierPayments->sum('amount'), currencyFormat: false) }}</span>
                        </li>
                    </ul>
                </div>

                <div class="invoice-pricing">
                    <h6 class="invoice-pricing__title text-sm-end mb-2">@lang('Summery')</h6>
                    <ul class="invoice-pricing-info text-sm-end ms-sm-auto">
                        <li class="invoice-pricing-info__item">
                            <span class="label">@lang('Subtotal'):</span>
                            <span class="value">{{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($purchase->subtotal, currencyFormat: false) }}</span>
                        </li>
                        <li class="invoice-pricing-info__item">
                            <span class="label">@lang('Discount'):</span>
                            <span class="value">
                                {{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($purchase->discount_amount, currencyFormat: false) }}
                                @if ($purchase->discount_amount > 0 && $purchase->discount_type == Status::DISCOUNT_PERCENT)
                                    ({{ getAmount($purchase->discount_value) }}%)
                                @endif
                            </span>
                        </li>
                        <li class="invoice-pricing-info__item">
                            <span class="label">@lang('Shipping Charge'):</span>
                            <span class="value">{{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($purchase->shipping_amount, currencyFormat: false) }}</span>
                        </li>
                    </ul>
                    <div class="invoice-total-price mt-3">
                        <h5 class="title">@lang('Total'):
                            {{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($purchase->total, currencyFormat: false) }}
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>