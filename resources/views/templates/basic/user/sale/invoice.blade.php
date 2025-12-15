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
                <h6 class="invoice-customer__title">@lang('Customer Information')</h6>
                <ul class="invoice-customer-info">
                    <li class="invoice-customer-info__item"><span class="label">@lang('Name'):</span>
                        <span class="value">{{ __(@$sale->customer->name) }}</span>
                    </li>
                    <li class="invoice-customer-info__item"><span class="label">@lang('Email'):</span>
                        <span class="value">{{ @$sale->customer->email ?? 'N/A' }}</span>
                    </li>
                    <li class="invoice-customer-info__item"><span class="label">@lang('Mobile'):</span>
                        <span class="value">{{ @$sale->customer->mobile ?? 'N/A' }}</span>
                    </li>
                    <li class="invoice-customer-info__item"><span class="label">@lang('Address'):</span>
                        <span class="value">{{ __(@$sale->customer->address ?? 'N/A') }}</span>
                    </li>
                </ul>
            </div>

            <ul class="invoice-info">
                <li class="invoice-info__item">
                    <span class="label">@lang('Sale Date')</span>
                    <p class="value">
                        {{ showDateTime($sale->sale_date, 'F d, Y') }}
                        <br>
                        <span>{{ showDateTime($sale->created_at, 'h:m A') }}</span>
                    </p>
                </li>
                <li class="invoice-info__item">
                    <span class="label">@lang('Invoice Number')</span>
                    <p class="value">
                        {{ __($sale->invoice_number) }}
                    </p>
                </li>
            </ul>
        </div>

        <div class="invoice-table-responsive mb-4">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th class="text-start">@lang('Product')</th>
                        <th class="text-center">@lang('Unit Price')</th>
                        <th class="text-center">@lang('Tax')</th>
                        <th class="text-center">@lang('Discount')</th>
                        <th class="text-center">@lang('Sale Price')</th>
                        <th class="text-center">@lang('Qty')</th>
                        <th class="text-end">@lang('Subtotal')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->saleDetails as $saleDetail)
                        <tr>
                            <td class="text-start">
                                {{ strLimit(__(@$saleDetail->product->name), 10) }}
                                <span> - {{ @$saleDetail->productDetail->sku }}</span>
                                @if (@$saleDetail->product->product_type == Status::PRODUCT_TYPE_VARIABLE)
                                    <span>
                                        - {{ __(@$saleDetail->productDetail->attribute->name) }}
                                        - {{ __(@$saleDetail->productDetail->variant->name) }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                {{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($saleDetail->unit_price, currencyFormat: false) }}
                            </td>
                            <td class="text-center text-nowrap">
                                {{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($saleDetail->tax_amount, currencyFormat: false) }}
                                @if ($saleDetail->tax_amount > 0)
                                    ({{ getAmount($saleDetail->tax_percentage) }}%)
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                {{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($saleDetail->discount_amount, currencyFormat: false) }}
                                @if ($saleDetail->discount_value > 0 && $saleDetail->discount_type == Status::DISCOUNT_PERCENT)
                                    ({{ getAmount($saleDetail->discount_value) }}%)
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                {{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($saleDetail->sale_price, currencyFormat: false) }}
                            </td>
                            <td class="text-center text-nowrap">
                                {{ $saleDetail->quantity }}
                                {{ __(@$saleDetail->product->unit->short_name) }}
                            </td>
                            <td class="text-end">
                                {{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($saleDetail->subtotal, currencyFormat: false) }}
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
                    @foreach ($sale->payments as $payment)
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
                                <span class="value">{{ $payment->date }}</span>
                            </li>
                            <li class="invoice-payment-info__item">
                                <span class="label">@lang('Payment Note'):</span>
                                <span class="value">{{ __($payment->note) }}</span>
                            </li>
                        </ul>
                        @if ($sale->payments->count() != $loop->last)
                            <hr>
                        @endif
                    @endforeach
                </div>

                <div class="invoice-payment">
                    <h6 class="invoice-payment__title mb-2">@lang('Payment Summary')</h6>
                    <ul class="invoice-payment-info">
                        <li class="invoice-payment-info__item">
                            <span class="label">@lang('Total Payable'):</span>
                            <span class="value">{{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($sale->total, currencyFormat: false) }}</span>
                        </li>
                        <li class="invoice-payment-info__item">
                            <span class="label">@lang('Total Paid'):</span>
                            <span class="value">{{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($sale->payments->sum('amount'), currencyFormat: false) }}</span>
                        </li>
                    </ul>
                </div>

                <div class="invoice-pricing">
                    <h6 class="invoice-pricing__title text-sm-end mb-2">@lang('Summery')</h6>
                    <ul class="invoice-pricing-info text-sm-end ms-sm-auto">
                        <li class="invoice-pricing-info__item">
                            <span class="label">@lang('Subtotal'):</span>
                            <span class="value">{{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($sale->subtotal, currencyFormat: false) }}</span>
                        </li>
                        <li class="invoice-pricing-info__item">
                            <span class="label">@lang('Discount'):</span>
                            <span class="value">
                                {{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($sale->discount_amount, currencyFormat: false) }}
                                @if ($sale->discount_amount > 0 && $sale->discount_type == Status::DISCOUNT_PERCENT)
                                    ({{ getAmount($sale->discount_value) }}%)
                                @endif
                            </span>
                        </li>
                        <li class="invoice-pricing-info__item">
                            <span class="label">@lang('Shipping Charge'):</span>
                            <span class="value">{{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($sale->shipping_amount, currencyFormat: false) }}</span>
                        </li>
                    </ul>
                    <div class="invoice-total-price mt-3">
                        <h5 class="title">@lang('Total'):
                            {{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($sale->total, currencyFormat: false) }}
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>