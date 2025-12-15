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
            <ul class="invoice-info">
                <li class="invoice-info__item">
                    <span class="label">@lang('Transfer Date')</span>
                    <p class="value">
                        {{ showDateTime($transfer->transfer_date, 'F d, Y') }}
                        <br>
                        <span>{{ showDateTime($transfer->created_at, 'h:m A') }}</span>
                    </p>
                </li>
                <li class="invoice-info__item">
                    <span class="label">@lang('Invoice Number')</span>
                    <p class="value">
                        {{ __($transfer->invoice_number) }}
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
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transfer->stockTransferDetails as $transferDetail)
                        <tr>
                            <td class="text-start text-nowrap">
                                {{ strLimit(__(@$transferDetail->product->name), 10) }}
                                <span> - {{ @$transferDetail->productDetail->sku }}</span>
                                @if (@$transferDetail->product->product_type == Status::PRODUCT_TYPE_VARIABLE)
                                    <span>
                                        - {{ __(@$transferDetail->productDetail->attribute->name) }}
                                        - {{ __(@$transferDetail->productDetail->variant->name) }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                {{ __($transferDetail->quantity) }} {{ __(@$transferDetail->productDetail->product->unit->short_name) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex gap-5 flex-wrap">
            <div class="invoice-customer">
                <h6 class="invoice-customer__title">@lang('From Warehouse')</h6>
                <ul class="invoice-customer-info">
                    <li class="invoice-customer-info__item"><span class="label">@lang('Name'):</span>
                        <span class="value">{{ __(@$transfer->fromWarehouse->name) }}</span>
                    </li>
                    <li class="invoice-customer-info__item"><span class="label">@lang('Mobile'):</span>
                        <span class="value">{{ @$transfer->fromWarehouse->contact_number ?? 'N/A' }}</span>
                    </li>
                    <li class="invoice-customer-info__item"><span class="label">@lang('Address'):</span>
                        <span class="value">{{ __(@$transfer->fromWarehouse->address ?? 'N/A') }}</span>
                    </li>
                </ul>
            </div>
            <div class="invoice-customer">
                <h6 class="invoice-customer__title">@lang('To Warehouse')</h6>
                <ul class="invoice-customer-info">
                    <li class="invoice-customer-info__item"><span class="label">@lang('Name'):</span>
                        <span class="value">{{ __(@$transfer->toWarehouse->name) }}</span>
                    </li>
                    <li class="invoice-customer-info__item"><span class="label">@lang('Mobile'):</span>
                        <span class="value">{{ @$transfer->toWarehouse->contact_number ?? 'N/A' }}</span>
                    </li>
                    <li class="invoice-customer-info__item"><span class="label">@lang('Address'):</span>
                        <span class="value">{{ __(@$transfer->toWarehouse->address ?? 'N/A') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>