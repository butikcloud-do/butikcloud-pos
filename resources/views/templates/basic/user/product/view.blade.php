@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row gy-4">
        <div class="col-lg-12">
            <x-panel.ui.card>
                <x-panel.ui.card.body>
                    <ul class="list-group list-group-flush">
                        <li class="d-flex gap-2 flex-wrap list-group-item justify-content-between px-0">
                            <span>@lang('Name')</span>
                            <span>{{ __($product->name) }}</span>
                        </li>
                        <li class="d-flex gap-2 flex-wrap list-group-item justify-content-between px-0">
                            <span>@lang('Code')</span>
                            <strong>{{ __($product->product_code) }}</strong>
                        </li>
                        <li class="d-flex gap-2 flex-wrap list-group-item justify-content-between px-0">
                            <span>@lang('Product Type')</span>
                            <span>
                                @if ($product->product_type == Status::PRODUCT_TYPE_STATIC)
                                    <span class="badge badge--info">@lang('Static')</span>
                                @else
                                    <span class="badge badge--primary">@lang('variable')</span>
                                @endif
                            </span>
                        </li>
                        <li class="d-flex gap-2 flex-wrap list-group-item justify-content-between px-0">
                            <span>@lang('Category')</span>
                            <span>{{ __(@$product->category->name) }}</span>
                        </li>
                        <li class="d-flex gap-2 flex-wrap list-group-item justify-content-between px-0">
                            <span>@lang('Brand')</span>
                            <span>{{ __(@$product->brand->name) }}</span>
                        </li>
                        <li class="d-flex gap-2 flex-wrap list-group-item justify-content-between px-0">
                            <span>@lang('Unit')</span>
                            <span>{{ __(@$product->unit->name) }}</span>
                        </li>
                        <li class="d-flex gap-2 flex-wrap list-group-item justify-content-between px-0">
                            <span>@lang('Description')</span>
                            <span>
                                {{ __(@$product->description ?? 'N/A') }}
                            </span>
                        </li>
                    </ul>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
        <div class="col-12">
            <x-panel.ui.card>
                <x-panel.ui.card.body class="p-0">
                    <x-panel.ui.table>
                        <x-panel.ui.table.header>
                            <tr>
                                <th>@lang('SKU')</th>
                                <th>@lang('Base Price')</th>
                                <th>@lang('Tax')</th>
                                <th>@lang('Purchase Price')</th>
                                <th>@lang('Profit Margin')</th>
                                <th>@lang('Sale Price')</th>
                                <th>@lang('Discount')</th>
                                <th>@lang('Final Price')</th>
                            </tr>
                        </x-panel.ui.table.header>
                        <x-panel.ui.table.body>
                            @forelse($product->details as $detail)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ __($detail->sku) }}</span>
                                        @if ($product->product_type == Status::PRODUCT_TYPE_VARIABLE)
                                            <br>
                                            <span> {{ __(@$detail->attribute->name) }} - {{ __(@$detail->variant->name) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ showAmount($detail->base_price) }}</td>
                                    <td>
                                        @if ($detail->tax_type)
                                            @if ($detail->tax_type == Status::TAX_TYPE_EXCLUSIVE)
                                                @lang('Exclusive')
                                            @else
                                                @lang('Inclusive')
                                            @endif
                                            - {{ getAmount(@$detail->tax->percentage) }}%
                                            @if (@$detail->tax_amount > 0)
                                                <br>
                                                {{ showAmount(@$detail->tax_amount) }}
                                            @endif
                                        @else
                                            <span class="badge badge--primary">@lang('No Tax')</span>
                                        @endif
                                    </td>
                                    <td>{{ showAmount($detail->purchase_price) }}</td>
                                    <td>{{ getAmount(@$detail->profit_margin) }}%</td>
                                    <td>{{ showAmount(@$detail->sale_price) }}</td>
                                    <td>
                                        {{ showAmount(@$detail->discount_amount) }}
                                        @if ($detail->discount_type == Status::DISCOUNT_PERCENT && $detail->discount_value > 0)
                                            <br>
                                            {{ getAmount($detail->discount_value) }}%
                                        @endif
                                    </td>
                                    <td>{{ showAmount(@$detail->final_price) }}</td>
                                </tr>
                            @empty
                                <x-panel.ui.table.empty_message />
                            @endforelse
                        </x-panel.ui.table.body>
                    </x-panel.ui.table>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class=" d-flex gap-2 flex-wrap">
        <x-staff_permission_check permission="edit product">
            <a class="btn btn--primary" href="{{ route('user.product.edit', $product->id) }}">
                <i class="las la-pencil-alt"></i>
                @lang('Edit')
            </a>
        </x-staff_permission_check>
        <x-staff_permission_check permission="view product">
            <x-back_btn route="{{ route('user.product.list') }}" />
        </x-staff_permission_check>
    </div>
@endpush
