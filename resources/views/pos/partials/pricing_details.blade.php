<div class="mb-3">
    <ul class="list-group list-group-flush">
        <li class="list-group-item d-fle flex-wrap gap-2 justify-content-between">
            <span>@lang('Name')</span>
            <span>
                {{ __(@$product->product->name) }}
            </span>
        </li>
        <li class="list-group-item d-fle flex-wrap gap-2 justify-content-between">
            <span>@lang('SKU')</span>
            <span>{{ __(@$product->sku) }}</span>
        </li>
        <li class="list-group-item d-fle flex-wrap gap-2 justify-content-between">
            <span>@lang('Unit Price')</span>
            <strong>
                {{ gs('cur_sym', getParentUser()->id) }}<span>{{ showAmount($product->sale_price - $product->tax_amount, currencyFormat: false) }}
                </span>
                <span class="details-unit-price d-none">{{ getAmount($product->sale_price) }}</span>
            </strong>
        </li>
        <li class="list-group-item d-fle flex-wrap gap-2 justify-content-between">
            <span> <strong>(+)</strong> @lang('Tax Amount')</span>
            <strong>
                {{ gs('cur_sym', getParentUser()->id) }}<span
                    class="details-tax-amount">{{ showAmount($product->tax_amount, currencyFormat: false) }}</span>
            </strong>
        </li>
        <li class="list-group-item d-fle flex-wrap gap-2 justify-content-between align-items-center">
            <span>
                <strong>(-)</strong> @lang('Discount')
            </span>
            <div>
                <div class="input-group input--group">
                    <span class="input-group-text">
                        <select class="border-0 bg-transparent p-0 discount-type details-discount-type">
                            <option value="{{ Status::DISCOUNT_PERCENT }}" @selected(Status::DISCOUNT_PERCENT == $product->discount_type)>
                                @lang('Percent')
                            </option>
                            <option value="{{ Status::DISCOUNT_FIXED }}" @selected(Status::DISCOUNT_FIXED == $product->discount_type)>
                                @lang('Fixed')
                            </option>
                        </select>
                    </span>
                    <input type="number" step="any" class="form-control details-discount-value"
                        value="{{ getAmount($product->discount_value) }}">
                </div>
            </div>
        </li>

        <li class="list-group-item d-fle flex-wrap gap-2 justify-content-between">
            <span>@lang('Sale Price')</span>
            <strong class="text--primary">
                {{ gs('cur_sym', getParentUser()->id) }}<span class="details-sale-price">
                    {{ showAmount($product->final_price, currencyFormat: false) }}
                </span>
            </strong>
        </li>
    </ul>
</div>

<div class="form-group">
    <div class="d-flex flex-wrap gap-2 justify-content-end">
        <button type="button" class="btn btn--secondary btn-large" data-bs-dismiss="modal">
            <i class="fa-regular fa-times-circle"></i> @lang('Close')
        </button>
        <button type="button" class="btn btn--primary btn-large update-discount" data-id="{{ $product->id }}">
            <i class="fa-regular fa-check-circle"></i> @lang('Update')
        </button>
    </div>
</div>