@php
    $companyInformation = gs('company_information',getParentUser()->id);
@endphp

<div class="pos-invoice">
    <div class="header">
        <div class="company-info">
            <p class="company-name">{{ __(@$companyInformation->name) }}</p>
            <p class="company-address">{{ __(@$companyInformation->address) }}</p>
            <p class="company-contact">@lang('Email'):
                {{ @$companyInformation->email ?? __('N/A') }}
            </p>
            <p class="company-contact">@lang('Phone'):
                {{ @$companyInformation->phone ?? __('N/A') }}
            </p>
        </div>
        <div class="invoice-info">
            <h2>@lang('Invoice')</h2>
            <p><strong>@lang('Invoice') #{{ $sale->invoice_number }}</strong></p>
            <p>@lang('Date'): {{ showDateTime($sale->sale_date, 'Y-m-d') }}</p>
            <p>@lang('Time'): {{ showDateTime($sale->created_at, 'h:i s') }}</p>
        </div>
    </div>

    <div class="customer-info">
        <p><strong>@lang('Bill To'):</strong></p>
        <p>@lang('Name'): {{ __(@$sale->customer->name ?? __('N/A')) }}</p>
        <p>@lang('Address'): {{ __(@$sale->customer->address ?? __('N/A')) }}</p>
        <p>@lang('Email'): {{ @$sale->customer->email ?? __('N/A') }}</p>
        <p>@lang('Mobile'): {{ @$sale->customer->mobile ?? __('N/A') }}</p>
    </div>

    <div class="item-list">
        <table>
            <thead>
                <tr>
                    <th>@lang('Item')</th>
                    <th>@lang('Qty')</th>
                    <th>@lang('Price')</th>
                    <th>@lang('Tax')</th>
                    <th>@lang('Discount')</th>
                    <th>@lang('Subtotal')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->saleDetails as $saleDetails)
                    <tr>
                        <td>{{ @$saleDetails->productDetail->sku }}</td>
                        <td>{{ @$saleDetails->quantity }}</td>
                        <td>
                            {{ gs('cur_sym',getParentUser()->id) }}{{ showAmount(@$saleDetails->unit_price, currencyFormat: false) }}
                        </td>
                        <td>{{ gs('cur_sym',getParentUser()->id) }}{{ showAmount(@$saleDetails->tax_amount, currencyFormat: false) }}</td>
                        <td>{{ gs('cur_sym',getParentUser()->id) }}{{ showAmount(@$saleDetails->discount_amount, currencyFormat: false) }}
                        </td>
                        <td>{{ gs('cur_sym',getParentUser()->id) }}{{ showAmount(@$saleDetails->subtotal, currencyFormat: false) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="totals">
        <p>@lang('Subtotal'): {{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($sale->subtotal, currencyFormat: false) }}</p>
        <p>@lang('Discount'): {{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($sale->discount_amount, currencyFormat: false) }}</p>
        <p>@lang('Shipping'): {{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($sale->shipping_amount, currencyFormat: false) }}</p>
        <p>
            <strong>
                @lang('Total'):
                {{ gs('cur_sym',getParentUser()->id) }}{{ showAmount($sale->total, currencyFormat: false) }}
            </strong>
        </p>
    </div>

    <div class="footer">
        <p>@lang('Thank you for your purchase')!❤</p>
    </div>
</div>
