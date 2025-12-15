@php
    $companyInformation = gs('company_information');
@endphp
<div class="invoice-wrapper">
    <div class="py--4 bg-white a4-size position-relative">
        <div class="px-14 py-6">
            <table class="w-full border-collapse border-spacing-0">
                <tbody>
                    <tr>
                        <td class="w-full align-top">
                            <span>
                                <img src="{{ siteLogo() }}" class="h-12" />
                            </span>
                        </td>
                        <td class="align-top">
                            <div class="text-sm">
                                <table class="border-collapse border-spacing-0">
                                    <tbody>
                                        <tr>
                                            <td class="border-r pr-4">
                                                <div>
                                                    <p class="whitespace-nowrap text-slate-400">
                                                        @lang('Sale Date')
                                                    </p>
                                                    <p class="whitespace-nowrap font-bold text-main">
                                                        {{ showDateTime($sale->sale_date, 'F d, Y') }}
                                                    </p>
                                                    <p class="whitespace-nowrap  text-main text-right fs-12">
                                                        <span>@lang('Created At'):</span>
                                                        {{ showDateTime($sale->created_at) }}
                                                    </p>
                                                </div>
                                            </td>
                                            <td class="pl--4">
                                                <div>
                                                    <p class="whitespace-nowrap text-slate-400">
                                                        @lang('Invoice')
                                                    </p>
                                                    <p class="whitespace-nowrap font-bold text-main">
                                                        {{ __($sale->invoice_number) }}
                                                    </p>
                                                    <p class="whitespace-nowrap  text-main text-right fs-12">
                                                        <span>@lang('Reference Number'):</span>
                                                        {{ __($sale->reference_number ?? 'N/A') }}
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="bg-slate-100 px-14 py-6 text-sm">
            <table class="w-full border-collapse border-spacing-0">
                <tbody>
                    <tr>
                        <td class="w-1/2 align-top">
                            <span class="text-sm text-neutral-600">
                                <p class="font-bold">@lang('Customer Information')</p>
                                <p class="mt-2">@lang('Name'): {{ __(@$sale->customer->name) }}</p>
                                <p>@lang('Company Name'): {{ __(@$sale->customer->company_name) }}</p>
                                <p>@lang('Email'): {{ @$sale->customer->email ?? 'N/A' }}</p>
                                <p>@lang('Mobile'): {{ @$sale->customer->mobile ?? 'N/A' }}</p>
                                <p>@lang('Address'): {{ __(@$sale->customer->address ?? 'N/A') }}</p>
                            </span>
                        </td>
                        <td class="w-1/2 align-top">
                            <span class="text-sm text-neutral-600">
                                <p class="font-bold">@lang('Company Information')</p>
                                <p class="company-name my-3 fs-20 fw-bold">{{ __(@$companyInformation->name) }}
                                </p>
                                <p class="company-address">{{ __(@$companyInformation->address) }}</p>
                                <p class="company-contact">@lang('Email'):
                                    {{ @$companyInformation->email ?? __('N/A') }}
                                </p>
                                <p class="company-contact">@lang('Phone'):
                                    {{ @$companyInformation->phone ?? __('N/A') }}
                                </p>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="px-14 py-10 text-sm text-neutral-700">
            <table class="w-full border-collapse border-spacing-0">
                <thead>
                    <tr>
                        <td class="border-b-2 border-main pb--3 pl-2 font-bold text-main">@lang('Product')
                        </td>
                        <td class="border-b-2 border-main pb--3 pl-2 font-bold text-main">@lang('Unit Price')
                        </td>
                        <td class="border-b-2 border-main pb--3 pl-2 font-bold text-main">@lang('Tax')
                        </td>
                        <td class="border-b-2 border-main pb--3 pl-2 font-bold text-main">@lang('Discount')
                        </td>
                        <td class="border-b-2 border-main pb--3 pl-2 font-bold text-main">@lang('Sale Price')
                        </td>
                        <td class="border-b-2 border-main pb--3 pl-2 text-center font-bold text-main">
                            @lang('Qty')
                        </td>
                        <td class="border-b-2 border-main pb--3 pl-2 text-end font-bold text-main">
                            @lang('Subtotal')
                        </td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->saleDetails as $saleDetail)
                        <tr>
                            <td class="border-b py--3">
                                {{ strLimit(__(@$saleDetail->product->name), 10) }}
                                <span> - {{ @$saleDetail->productDetail->sku }}</span>
                                @if (@$saleDetail->product->product_type == Status::PRODUCT_TYPE_VARIABLE)
                                    <span>
                                        - {{ __(@$saleDetail->productDetail->attribute->name) }}
                                        - {{ __(@$saleDetail->productDetail->variant->name) }}
                                    </span>
                                @endif
                            </td>
                            <td class="border-b py--3">
                                {{ gs('cur_sym') }}{{ showAmount($saleDetail->unit_price, currencyFormat: false) }}
                            </td>
                            <td class="border-b py--3">
                                {{ gs('cur_sym') }}{{ showAmount($saleDetail->tax_amount, currencyFormat: false) }}
                                @if ($saleDetail->tax_amount > 0)
                                    - {{ getAmount($saleDetail->tax_percentage) }}%
                                @endif
                            </td>
                            <td class="border-b py--3">
                                {{ gs('cur_sym') }}{{ showAmount($saleDetail->discount_amount, currencyFormat: false) }}
                                @if ($saleDetail->discount_amount > 0 && $saleDetail->discount_type == Status::DISCOUNT_PERCENT)
                                    - {{ getAmount($saleDetail->discount_value) }}%
                                @endif
                            </td>
                            <td class="border-b py--3">
                                {{ gs('cur_sym') }}{{ showAmount($saleDetail->sale_price, currencyFormat: false) }}
                            </td>
                            <td class="border-b py--3">
                                {{ $saleDetail->quantity }}
                                {{ __(@$saleDetail->product->unit->short_name) }}
                            </td>
                            <td class="border-b py--3 text-end">
                                {{ gs('cur_sym') }}{{ showAmount($saleDetail->subtotal, currencyFormat: false) }}
                            </td>
                        </tr>
                    @endforeach

                    <tr>
                        <td colspan="7" class="pt--5">
                            <table class="w-full border-collapse border-spacing-0">
                                <tbody>
                                    <tr>
                                        <td class="w-full"></td>
                                        <td>
                                            <table class="w-full border-collapse border-spacing-0">
                                                <tbody>
                                                    <tr>
                                                        <td class="p--2">
                                                            <span
                                                                class="whitespace-nowrap text-slate-400">@lang('Subtotal'):</span>
                                                        </td>
                                                        <td class=" p--2">
                                                            <span
                                                                class="whitespace-nowrap font--medium text-main">{{ showAmount($sale->subtotal) }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="p--2 ">
                                                            <span class="whitespace-nowrap text-slate-400">
                                                                @lang('Discount')
                                                            </span>
                                                        </td>
                                                        <td class=" p--2 ">
                                                            <span class="whitespace-nowrap font--medium text-main">
                                                                {{ showAmount($sale->discount_amount) }}
                                                                @if ($sale->discount_amount > 0 && $sale->discount_type == Status::DISCOUNT_PERCENT)
                                                                    -
                                                                    {{ getAmount($sale->discount_value) }}%
                                                                @endif
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="p--2 pb--3 border-b">
                                                            <span class="whitespace-nowrap text-slate-400">
                                                                @lang('Shipping Charge'):
                                                            </span>
                                                        </td>
                                                        <td class="p--2 pb--3 text-right border-b">
                                                            <span class="whitespace-nowrap font--medium text-main">
                                                                {{ showAmount($sale->shipping_amount) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="p--2 pt--3">
                                                            <span
                                                                class="whitespace-nowrap font-bold">@lang('Total'):</span>
                                                        </td>
                                                        <td class="p--2 text-right pt--3">
                                                            <span class="whitespace-nowrap font-bold">
                                                                {{ showAmount($sale->total) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="px-14 text-sm text-neutral-700">
            <p class="text-main font-bold">@lang('PAYMENT DETAILS')</p>
            @foreach ($sale->payments as $payment)
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item d-flex justify-content-between gap-2 flex-wrap ps-0">
                        <span>@lang('Payment Type')</span>
                        <span>{{ __(@$payment->paymentType->name) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between gap-2 flex-wrap ps-0">
                        <span>@lang('Payment Amount')</span>
                        <span>{{ showAmount($payment->amount, currencyFormat: false) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between gap-2 flex-wrap ps-0">
                        <span>@lang('Payment Date')</span>
                        <span>{{ $payment->date }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between gap-2 flex-wrap ps-0">
                        <span>@lang('Payment Note')</span>
                        <span>{{ __($payment->note) }}</span>
                    </li>
                </ul>
            @endforeach
        </div>
        <div class="px-14 text-sm text-neutral-700">
            <p class="text-main font-bold">@lang('PAYMENT SUMMARY')</p>
            <ul class="list-group list-group-flush mb-4">
                <li class="list-group-item d-flex justify-content-between gap-2 flex-wrap ps-0">
                    <span>@lang('Total Payable')</span>
                    <span
                        class="fw-bold">{{ gs('cur_sym') }}{{ showAmount($sale->total, currencyFormat: false) }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between gap-2 flex-wrap ps-0">
                    <span>@lang('Total Paid')</span>
                    <span
                        class="fw-bold">{{ gs('cur_sym') }}{{ showAmount($sale->payments->sum('amount'), currencyFormat: false) }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>
