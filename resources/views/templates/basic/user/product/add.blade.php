@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card>
                <x-panel.ui.card.body>
                    <form action="{{ route('user.product.create') }}" method="POST" class="product-create-form"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>@lang('Product Name')</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>
                                    @lang('Product Code')
                                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Leave this field blank to automatically generate the content')">
                                        <i class="las la-info-circle"></i>
                                    </span>
                                </label>
                                <div class="input-group input--group">
                                    <input type="text" class="form-control" name="product_code">
                                    <span class="input-group-text cursor-pointer auto-code-generate-btn">
                                        <i class="las la-magic"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>@lang('Category')</label>
                                <select name="category_id" class="form-control select2" required>
                                    <option value="">@lang('Select Category')</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ __($category->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>@lang('Brand')</label>
                                <select name="brand_id" class="form-control select2" required>
                                    <option value="">@lang('Select Brand')</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ __($brand->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>@lang('Unit')</label>
                                <select name="unit_id" class="form-control select2" required>
                                    <option value="">@lang('Select Unit')</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">
                                            {{ __($unit->short_name) }} - {{ __($unit->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>@lang('Product Type')</label>
                                <select name="product_type" class="form-control select2 product-type" required
                                    data-minimum-results-for-search="-1">
                                    <option value="{{ Status::PRODUCT_TYPE_STATIC }}">@lang('Static')</option>
                                    <option value="{{ Status::PRODUCT_TYPE_VARIABLE }}">@lang('Variable')</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>@lang('Description')</label>
                                <textarea name="description" class="form-control" cols="5" rows="5"></textarea>
                            </div>
                            <div class="form-group col-sm-6 align-self-start">
                                <label>@lang('Image')</label>
                                <input type="file" class="form-control" name="image">
                            </div>
                            <div class="form-group product-attribute-variant d-none col-lg-12">
                                <div class="row">
                                    <div class="col-sm-6 form-group">
                                        <label class="required">@lang('Attribute')</label>
                                        <select name="attribute" class="form-control select2 product-attribute"
                                            data-minimum-results-for-search="-1" multiple>
                                            <option value="" disabled>@lang('Select One')</option>
                                            @foreach ($attributes as $attribute)
                                                <option value="{{ $attribute->id }}"
                                                    data-variants="{{ $attribute->variants }}">
                                                    {{ __($attribute->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-6 form-group">
                                        <label class="required">@lang('Variant')</label>
                                        <select name="variant" class="form-control select2 product-variant"
                                            data-minimum-results-for-search="-1" multiple>
                                            <option value="" disabled>@lang('Select Attribute')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-lg-12 ">
                                <div class="row product-variation-row">
                                    <div class="col-12 single-product-variation-row">
                                        <div class="my-4 variant-divider skelton-here">
                                            <h5 class="divider-title">@lang('Product Details')</h5>
                                        </div>
                                        <div class="d-flex gap-3  skelton-here overflow-auto">
                                            <div class="form-group">
                                                <label class="form-label">
                                                    @lang('SKU')
                                                    <span data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="@lang('Leave this field blank to automatically generate the content')">
                                                        <i class="las la-info-circle"></i>
                                                    </span>
                                                </label>
                                                <input type="text" class="form-control "
                                                    name="product_detail[0][sku]">
                                                <input type="hidden" name="product_detail[0][attribute_id]"
                                                    class="attribute_id">
                                                <input type="hidden" name="product_detail[0][variant_id]"
                                                    class="variant_id">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">@lang('Base Price') ({{ __(gs('cur_sym', getParentUser()->id)) }})</label>
                                                <input type="number" step="any" class="form-control  base-price"
                                                    name="product_detail[0][base_price]">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">@lang('Tax')</label>
                                                <div class="d-flex gap-1">
                                                    <div class="flex-fill">
                                                        <select name="product_detail[0][tax_type]"
                                                            class="form-control form-select   fs-14 tax-type"
                                                            data-minimum-results-for-search="-1">
                                                            <option value="" selected>
                                                                @lang('Tax Type')
                                                            </option>
                                                            <option value="{{ Status::TAX_TYPE_EXCLUSIVE }}">
                                                                @lang('Exclusive')
                                                            </option>
                                                            <option value="{{ Status::TAX_TYPE_INCLUSIVE }}">
                                                                @lang('Inclusive')
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="flex-fill">
                                                        <select name="product_detail[0][tax_id]"
                                                            class="form-control form-select   tax-rate"
                                                            data-minimum-results-for-search="-1">
                                                            <option value="" selected>
                                                                @lang('Select Tax')
                                                            </option>
                                                            @foreach ($taxes as $tax)
                                                                <option value="{{ $tax->id }}"
                                                                    data-tax-rate="{{ $tax->percentage }}">
                                                                    {{ __($tax->name) }} -
                                                                    {{ getAmount($tax->percentage) . '%' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">
                                                    @lang('Purchase Price') ({{ __(gs('cur_sym', getParentUser()->id)) }})
                                                    <span data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ productTooltip()['purchase_price'] }}">
                                                        <i class="las la-info-circle"></i>
                                                    </span>
                                                </label>
                                                <input name="product_detail[0][purchase_price]" type="number"
                                                    step="any" class="form-control  purchase-price"
                                                    readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">@lang('Profit Margin') (%)</label>
                                                <input type="number" step="any"
                                                    class="form-control  profit-margin"
                                                    name="product_detail[0][profit_margin]">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">
                                                    @lang('Sale Price') ({{ __(gs('cur_sym', getParentUser()->id)) }})
                                                    <span data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ productTooltip()['sale_price'] }}">
                                                        <i class="las la-info-circle"></i>
                                                    </span>
                                                </label>
                                                <input type="number" step="any"
                                                    class="form-control  sale-price"
                                                    name="product_detail[0][sale_price]">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">@lang('Discount')</label>
                                                <div class="input-group input--group">
                                                    <input type="number" step="any"
                                                    class="form-control  discount"
                                                    name="product_detail[0][discount]">
                                                    <span class="input-group-text">
                                                        <select name="product_detail[0][discount_type]"
                                                            class="border-0 bg-transparent p-0 discount-type">
                                                            <option value="{{ Status::DISCOUNT_PERCENT }}">
                                                                @lang('%')</option>
                                                            <option value="{{ Status::DISCOUNT_FIXED }}">
                                                                {{  __(gs('cur_text', getParentUser()->id)) }}
                                                            </option>
                                                        </select>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">
                                                    @lang('Final Sale Price') ({{ __(gs('cur_sym', getParentUser()->id)) }})
                                                    <span data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ productTooltip()['final_sale_price'] }}">
                                                        <i class="las la-info-circle"></i>
                                                    </span>
                                                </label>
                                                <input type="number" step="any"
                                                    class="form-control  final-sale-price"
                                                    name="product_detail[0][final_sale_price]" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">@lang('Alert Qty')</label>
                                                <input type="number" step="any" class="form-control "
                                                    name="product_detail[0][alert_quantity]">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <x-panel.ui.btn.submit />
                            </div>
                        </div>
                    </form>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>
@endsection

@push('script')
    <script></script>
    <script>
        "use strict";
        (function($) {

            const attributesVariants = [];
            const attributes = @json($attributes);

            let selectAttributeIds = [];
            let selectVariantIds = [];

            //define some selector
            const $productAttributeElement = $('.product-attribute');
            const $productVariantElement = $(".product-variant");
            const $productTypeElement = $(".product-type");
            const $productVariantRowElements = $(".product-variation-row");

            //define tax and discount type
            const discountTypePercent = parseInt("{{ Status::DISCOUNT_PERCENT }}");
            const taxTypeExclusive = parseInt("{{ Status::TAX_TYPE_EXCLUSIVE }}");
            const calculationElementWhenInput =
                ".base-price, .tax-type, .tax-rate, .profit-margin, .discount, .discount-type";

            //product type change event
            $productTypeElement.on('change', function() {
                const productTypeVariable = parseInt("{{ Status::PRODUCT_TYPE_VARIABLE }}");
                const value = parseInt($(this).val());

                if (value == productTypeVariable) {
                    $(".product-attribute-variant").removeClass('d-none');
                } else {
                    otherManager.resetProductAttributeAndVariation();
                }

            });

            //product attribute change event
            $productAttributeElement.on('change', function() {
                const currentSelectedAttributeIds = $(this).val() || [];
                if (currentSelectedAttributeIds.length < selectAttributeIds
                    .length) { // attribute remove if match this condition
                    attributeChangeManager.removeVariant(selectAttributeIds, currentSelectedAttributeIds);
                } else {
                    attributeChangeManager.appendAttributeId(currentSelectedAttributeIds, selectAttributeIds);
                }
                //append variant
                attributeChangeManager.modifyVariantOption(currentSelectedAttributeIds);
                select2Initialize($productVariantElement);
            });

            //product variant change event
            $productVariantElement.on('change', function() {
                const currentSelectedVariantIds = $(this).val() || [];
                if (currentSelectedVariantIds.length < selectVariantIds
                    .length) { // variant remove if match this condition
                    variantChangeManager.removeVariant(selectVariantIds, currentSelectedVariantIds);
                } else {
                    variantChangeManager.addNewVariant(currentSelectedVariantIds);
                }
            });

            //auto product code generate event
            $(".auto-code-generate-btn").on('click', function() {
                ajaxActionManager.generateProductCodeAction();
            });

            //auto product code generate event
            $(".product-create-form").on('submit', function(e) {
                e.preventDefault();
                const $this = $(this);
                ajaxActionManager.createProduct($this);
            });

            //calculate the price
            $productVariantRowElements.on('input change', calculationElementWhenInput, function(event) {
                const $parentElement = $(this).closest('.single-product-variation-row');
                calculationManager.calculation($parentElement);
            });

            //sale price change calculation
            $productVariantRowElements.on('input change', ".sale-price", function(event) {
                const $parentElement = $(this).closest('.single-product-variation-row');
                calculationManager.salePriceChangeCalculation($parentElement);
            });


            //initialization select2
            function select2Initialize(selectors) {
                $.each(selectors, function() {
                    $(this)
                        .wrap(`<div class="position-relative"></div>`)
                        .select2({
                            dropdownParent: $(this).parent(),
                        });
                });
            }

            //attribute change manager
            const attributeChangeManager = {
                /**
                 * Removes variants from the selected attributes and DOM options.
                 * This method identifies attributes that were selected but are no longer valid
                 * and removes them from both the selectAttributeIds array and the corresponding
                 * options in the DOM.
                 *
                 * @param {Array} selectAttributeIds - Array of selected attribute IDs to be filtered.
                 * @param {Array} currentSelectedAttributeIds - Array of currently valid selected attribute IDs.
                 */
                removeVariant: (selectAttributeIds, currentSelectedAttributeIds) => {
                    const diffs = selectAttributeIds.filter(item => !currentSelectedAttributeIds.includes(
                        item));

                    diffs.forEach((diff, index) => {
                        const diffIndex = selectAttributeIds.findIndex(item => item == diff);
                        selectAttributeIds.splice(diffIndex, 1);
                        $productVariantElement.find(
                            `option[data-attribute-id="${diff}"]`
                        ).remove();
                        $productVariantRowElements.find(`[data-attribute="${diff}"]`).remove();
                        const productVariationsRowLength = $productVariantRowElements.find(
                            ".single-product-variation-row").length;
                        if (productVariationsRowLength == 0) {
                            variantChangeManager.newStaticVariantHtml();
                        }
                    });
                },
                /**
                 * Modifies the available variant options based on the current selected attributes.
                 * This method appends new variants to the select options for attributes that are selected.
                 * If a variant option already exists in the DOM, it will be skipped.
                 *
                 * @param {Array} currentSelectedAttributeIds - Array of currently selected attribute IDs.
                 */
                modifyVariantOption: function(currentSelectedAttributeIds) {
                    currentSelectedAttributeIds.forEach(currentSelectedAttributeId => {
                        const selectedAttribute = attributes.find(attribute => attribute.id ==
                            currentSelectedAttributeId);
                        const variants = selectedAttribute?.variants || [];
                        variants.forEach(variant => {
                            const $existsElement = $productVariantElement.find(
                                `option[value="${variant.id}-${variant.attribute_id}"]`);
                            if ($existsElement.length) return;
                            $productVariantElement.append(
                                `<option data-attribute-id="${variant.attribute_id}" value="${variant.id}-${variant.attribute_id}">${variant.name} </option>`
                            );
                        });

                    });
                },
                /**
                 * Appends new selected attribute IDs to the selectAttributeIds array.
                 * If an attribute ID from the current selected attributes is not already in the selectAttributeIds array,
                 * it will be added.
                 *
                 * @param {Array} currentSelectedAttributeIds - Array of currently selected attribute IDs.
                 * @param {Array} selectAttributeIds - Array that will be updated with the new selected attribute IDs.
                 */
                appendAttributeId: function(currentSelectedAttributeIds, selectAttributeIds) {
                    currentSelectedAttributeIds.forEach(currentSelectedAttributeId => {
                        if (!selectAttributeIds.includes(currentSelectedAttributeId)) {
                            selectAttributeIds.push(currentSelectedAttributeId);
                        }
                    });
                }
            };

            //variant change manager
            const variantChangeManager = {
                /**
                 * Adds new variants to the selected variant list and updates the UI accordingly.
                 *
                 * @param {Array} currentSelectedVariantIds - Array of selected variant IDs to add.
                 */
                addNewVariant: function(currentSelectedVariantIds) {
                    currentSelectedVariantIds.forEach(currentSelectedVariantId => {
                        if (!selectVariantIds.includes(currentSelectedVariantId)) {
                            selectVariantIds.push(currentSelectedVariantId);

                            const [variantId, attributeId] = currentSelectedVariantId.split("-");
                            const selectedAttribute = attributes.find(attribute => attribute.id ==
                                attributeId);
                            const selectedVariant = selectedAttribute?.variants.find(variant => variant
                                .id == variantId);

                            if (selectVariantIds.length == 1 && currentSelectedVariantIds.length == 1) {
                                variantChangeManager.modifyFirstVariant(selectedAttribute,
                                    selectedVariant);
                            } else {
                                variantChangeManager.newVariantHtml(selectedAttribute, selectedVariant);
                            }
                        }
                    });
                },

                /**
                 * Creates and appends the HTML for a new variant to the product variant section.
                 *
                 * @param {Object} attribute - The selected attribute object.
                 * @param {Object} variant - The selected variant object.
                 */
                newVariantHtml: function(attribute, variant) {
                    const productVariationsLength = $productVariantRowElements.find(
                        ".single-product-variation-row").length;
                    const html = `
                        <div class="col-12 single-product-variation-row" data-variant="${variant.id}-${attribute.id}" data-attribute="${attribute.id}">
                            <div class="my-4 variant-divider skeleton">
                                <h5 class="divider-title">
                                    ${attribute.name} -  ${variant.name}
                                </h5>
                            </div>
                            <div class="d-flex gap-3 overflow-auto skeleton">
                                <div class="form-group">
                                    <label class="form-label">
                                        @lang('SKU')
                                        <span data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="@lang('Leave this field blank to automatically generate the content')">
                                            <i class="las la-info-circle"></i>
                                        </span>
                                    </label>
                                    <input type="text" class="form-control "
                                        name="product_detail[${productVariationsLength}][sku]">
                                    <input type="hidden" name="product_detail[${productVariationsLength}][attribute_id]" value="${attribute.id}">
                                    <input type="hidden" name="product_detail[${productVariationsLength}][variant_id]" value="${variant.id}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('Base Price') ({{ __(gs('cur_sym', getParentUser()->id)) }})</label>
                                    <input type="number" step="any" class="form-control  base-price"
                                        name="product_detail[${productVariationsLength}][base_price]">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('Tax')</label>
                                    <div class="d-flex gap-1">
                                        <div class="flex-fill">
                                            <select name="product_detail[${productVariationsLength}][tax_type]"
                                                class="form-control form-select select2 tax-type"
                                                data-minimum-results-for-search="-1">
                                                <option value="" selected>
                                                    @lang('Tax Type')
                                                </option>
                                                <option value="{{ Status::TAX_TYPE_EXCLUSIVE }}">
                                                    @lang('Exclusive')
                                                </option>
                                                <option value="{{ Status::TAX_TYPE_INCLUSIVE }}">
                                                    @lang('Inclusive')
                                                </option>
                                            </select>
                                        </div>
                                        <div class="flex-fill">
                                            <select name="product_detail[${productVariationsLength}][tax_id]"
                                                class="form-control form-select select2 tax-rate"
                                                data-minimum-results-for-search="-1">
                                                <option value="" selected>
                                                    @lang('Select Tax')
                                                </option>
                                                @foreach ($taxes as $tax)
                                                    <option value="{{ $tax->id }}" data-tax-rate="{{ $tax->percentage }}">
                                                        {{ __($tax->name) }} -
                                                        {{ getAmount($tax->percentage) . '%' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">
                                        @lang('Purchase Price') ({{ __(gs('cur_sym', getParentUser()->id)) }})
                                        <span data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="{{ productTooltip()['purchase_price'] }}">
                                            <i class="las la-info-circle"></i>
                                        </span>
                                    </label>
                                    <input name="product_detail[${productVariationsLength}][purchase_price]" type="number"
                                        step="any" class="form-control  purchase-price" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('Profit Margin') (%)</label>
                                    <input type="number" step="any" class="form-control  profit-margin"
                                        name="product_detail[${productVariationsLength}][profit_margin]">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">
                                        @lang('Sale Price') ({{ __(gs('cur_sym', getParentUser()->id)) }})
                                        <span data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="@lang('Sale Price = Purchase Price + Profit Margin')">
                                            <i class="las la-info-circle"></i>
                                        </span>
                                    </label>
                                    <input type="number" step="any" class="form-control  sale-price"
                                        name="product_detail[${productVariationsLength}][sale_price]">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('Discount')</label>
                                    <div class="input-group input--group">
                                        <input type="number" step="any" class="form-control  discount" name="product_detail[${productVariationsLength}][discount]">
                                           <span class="input-group-text">
                                            <select name="product_detail[${productVariationsLength}][discount_type]"
                                                class="border-0 bg-transparent p-0 discount-type">
                                                <option value="{{ Status::DISCOUNT_PERCENT }}">
                                                    @lang('%')</option>
                                                <option value="{{ Status::DISCOUNT_FIXED }}">
                                                    {{  __(gs('cur_text', getParentUser()->id)) }}
                                                </option>
                                            </select>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">
                                        @lang('Final Sale Price') ({{ __(gs('cur_sym', getParentUser()->id)) }})
                                        <span data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="{{ productTooltip()['final_sale_price'] }}">
                                            <i class="las la-info-circle"></i>
                                        </span>
                                    </label>
                                    <input type="number" step="any" class="form-control  final-sale-price"
                                        name="product_detail[${productVariationsLength}][final_sale_price]" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('Alert Qty')</label>
                                    <input type="number" step="any" class="form-control "
                                        name="product_detail[${productVariationsLength}][alert_quantity]">
                                </div>
                            </div>
                        </div>
                    `;

                    $productVariantRowElements.append(html);
                    otherManager.scrollToBottom();
                    otherManager.removeSkeleton();
                    otherManager.reInitializeTooltip();
                },
                /**
                 * Creates and appends the HTML for a new static variant to the product variant section.
                 */
                newStaticVariantHtml: function() {
                    const html = `
                        <div class="col-12 single-product-variation-row">
                            <div class="my-4 variant-divider skeleton">
                                <h5 class="divider-title">@lang('Product Details')</h5>
                            </div>
                            <div class="d-flex gap-3 flex-wrap flex-xxl-nowrap skeleton">
                                <div class="form-group">
                                    <label class="form-label">
                                        @lang('SKU')
                                        <span data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="@lang('Leave this field blank to automatically generate the content')">
                                            <i class="las la-info-circle"></i>
                                        </span>
                                    </label>
                                    <input type="text" class="form-control "
                                        name="product_detail[0][sku]">
                                        <input type="hidden" name="product_detail[0][attribute_id]"
                                                    class="attribute_id">
                                                <input type="hidden" name="product_detail[0][variant_id]"
                                                    class="variant_id">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('Base Price') ({{ __(gs('cur_sym', getParentUser()->id)) }})</label>
                                    <input type="number" step="any" class="form-control  base-price"
                                        name="product_detail[0][base_price]">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('Tax')</label>
                                    <div class="d-flex gap-1">
                                        <div class="flex-fill">
                                            <select name="product_detail[0][tax_type]"
                                                class="form-control form-select select2 tax-type"
                                                data-minimum-results-for-search="-1">
                                                <option value="" selected>
                                                    @lang('Tax Type')
                                                </option>
                                                <option value="{{ Status::TAX_TYPE_EXCLUSIVE }}">
                                                    @lang('Exclusive')
                                                </option>
                                                <option value="{{ Status::TAX_TYPE_INCLUSIVE }}">
                                                    @lang('Inclusive')
                                                </option>
                                            </select>
                                        </div>
                                        <div class="flex-fill">
                                            <select name="product_detail[0][tax_id]"
                                                class="form-control form-select select2 tax-rate"
                                                data-minimum-results-for-search="-1">
                                                <option value="" selected>
                                                    @lang('Select Tax')
                                                </option>
                                                @foreach ($taxes as $tax)
                                                    <option value="{{ $tax->id }}" data-tax-rate="{{ $tax->percentage }}">
                                                        {{ __($tax->name) }} -
                                                        {{ getAmount($tax->percentage) . '%' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">
                                        @lang('Purchase Price') ({{ __(gs('cur_sym', getParentUser()->id)) }})
                                        <span data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="{{ productTooltip()['purchase_price'] }}">
                                            <i class="las la-info-circle"></i>
                                        </span>
                                    </label>
                                    <input name="product_detail[0][purchase_price]" type="number"
                                        step="any" class="form-control  purchase-price" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('Profit Margin') (%)</label>
                                    <input type="number" step="any" class="form-control  profit-margin"
                                        name="product_detail[0][profit_margin]">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">
                                        @lang('Sale Price') ({{ __(gs('cur_sym', getParentUser()->id)) }})
                                        <span data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="{{ productTooltip()['sale_price'] }}">
                                            <i class="las la-info-circle"></i>
                                        </span>
                                    </label>
                                    <input type="number" step="any" class="form-control  sale-price"
                                        name="product_detail[0][sale_price]">
                                </div>
                                <div class="form-group flex-fill">
                                    <label class="form-label">@lang('Discount')</label>
                                    <div class="input-group input--group">
                                        <span class="input-group-text">
                                            <select name="product_detail[0][discount_type]"
                                                class="border-0 bg-transparent p-0 discount-type">
                                                <option value="{{ Status::DISCOUNT_PERCENT }}">
                                                    @lang('Percent')</option>
                                                <option value="{{ Status::DISCOUNT_FIXED }}">
                                                    @lang('Fixed')</option>
                                            </select>
                                        </span>
                                        <input type="number" step="any" class="form-control  discount" name="product_detail[0][discount]">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">
                                        @lang('Final Sale Price') ({{ __(gs('cur_sym', getParentUser()->id)) }})
                                        <span data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="@lang('Final Sale Price = Sale Price - Discount')">
                                            <i class="las la-info-circle"></i>
                                        </span>
                                    </label>
                                    <input type="number" step="any" class="form-control  final-sale-price"
                                        name="product_detail[0][final_sale_price]" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('Alert Qty')</label>
                                    <input type="number" step="any" class="form-control "
                                        name="product_detail[0][alert_quantity]">
                                </div>
                            </div>
                        </div>
                    `;

                    $productVariantRowElements.html(html);
                    otherManager.scrollToBottom();
                    otherManager.removeSkeleton();
                },

                /**
                 * Modifies the first variant row when it's the only selected variant.
                 *
                 * @param {Object} attribute - The selected attribute object.
                 * @param {Object} variant - The selected variant object.
                 */
                modifyFirstVariant: function(attribute, variant) {
                    const $firstVariantRow = $productVariantRowElements.find(`.single-product-variation-row`)
                        .first();
                    $firstVariantRow.attr('data-variant', `${variant.id}-${attribute.id}`)
                    $firstVariantRow.attr('data-attribute', `${attribute.id}`)
                    $firstVariantRow.find('.divider-title').text(`${attribute.name} - ${variant.name}`);
                    $firstVariantRow.find('.skelton-here').addClass('skeleton');
                    $firstVariantRow.find('.attribute_id').val(attribute.id);
                    $firstVariantRow.find('.variant_id').val(variant.id);
                    otherManager.removeSkeleton();
                },

                /**
                 * Removes variants from the selected list and updates the UI accordingly.
                 *
                 * @param {Array} selectVariantIds - Array of current selected variant IDs.
                 * @param {Array} currentSelectedVariantIds - Array of updated selected variant IDs.
                 */
                removeVariant: (selectVariantIds, currentSelectedVariantIds) => {
                    const countElement = $productVariantRowElements.find(`.single-product-variation-row`)
                        .length;
                    if (countElement == 1) {
                        const $firstVariantRow = $productVariantRowElements.find(
                                `.single-product-variation-row`)
                            .first();
                        $firstVariantRow.removeAttr('data-variant')
                        $firstVariantRow.find('.divider-title').text("@lang('Product Details')");
                        $firstVariantRow.find('.attribute_id').val('');
                        $firstVariantRow.find('.variant_id').val('');
                        selectVariantIds.splice(0, 1);
                    } else {
                        const diffs = selectVariantIds.filter(item => !currentSelectedVariantIds.includes(
                            item));
                        diffs.forEach(diff => {
                            const diffIndex = selectVariantIds.findIndex(item => item == diff);
                            selectVariantIds.splice(diffIndex, 1);

                            $productVariantRowElements.find(
                                `[data-variant="${diff}"]`
                            ).remove();
                        });
                    }
                },
            }

            // other manager
            const otherManager = {
                /**
                 * Removes the 'skeleton' class from elements after a 1-second delay.
                 */
                removeSkeleton: function() {
                    setTimeout(() => {
                        $('body').find('.skeleton').removeClass(
                            'skeleton');
                    }, 500);
                },

                /**
                 * Scrolls the page to the bottom smoothly.
                 */
                scrollToBottom: function() {
                    $("html, body").animate({
                        scrollTop: $(document).height()
                    }, "slow");
                },

                /**
                 * Reset product attribute and variation when product type is static
                 */
                resetProductAttributeAndVariation: function() {
                    $productVariantRowElements.find('.single-product-variation-row').not(":first").remove();

                    const $firstElement = $productVariantRowElements.find('.single-product-variation-row')
                        .first();
                    $firstElement.find('.divider-title').text("@lang('Product Details')");
                    $firstElement.find('.attribute_id').val('');
                    $firstElement.find('.variant_id').val('');
                    $(".product-attribute-variant").addClass('d-none');
                    $productAttributeElement.val([]);
                    $productVariantElement.val([]);
                    select2Initialize($productAttributeElement);
                    select2Initialize($productVariantElement);
                    selectAttributeIds = [];
                    selectVariantIds = [];
                },

                /**
                 * reInitializeTooltip when new product attribute-variant are added
                 */
                reInitializeTooltip: function() {
                    const tooltipTriggerList = document.querySelectorAll('[title]')
                    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(
                        tooltipTriggerEl));
                }
            }

            //action manager
            const ajaxActionManager = {
                //auto generated product code
                generateProductCodeAction: function() {
                    $.ajax({
                        type: "get",
                        url: "{{ route('user.product.code.generate') }}",
                        dataType: "json",
                        success: function(response) {
                            if (response.status == 'success') {
                                $(`input[name=product_code]`).val(response.data.code);
                            }
                        }
                    });
                },

                //create a new product
                createProduct: function($this) {
                    let formData = new FormData($this[0]);
                    $.ajax({
                        url: "{{ route('user.product.create') }}",
                        method: "POST",
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        complete: function() {
                            $this.find(`button[type=submit]`).attr('disabled', false).html(
                                `<span class="me-1"><i class="fa-regular fa-paper-plane"></i></span>@lang('Submit')`
                            ).removeClass("disabled");
                        },
                        success: function(resp) {
                            notify(resp.status, resp.message);
                            if (resp.status == 'success') {
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            }
                        }
                    })
                }
            }

            //calculation the price
            const calculationManager = {
                /**
                 * Holds the reference to the parent DOM element containing price-related fields.
                 * This is used to interact with and update the UI dynamically.
                 */
                $parentElement: null,
                /**
                 * Performs the primary calculation for price components including tax, profit margin, and final sale price.
                 * Updates the corresponding fields in the UI.
                 *
                 * @param {Object} $parentElement - The parent DOM element containing price-related fields.
                 */
                calculation: function($parentElement) {
                    this.$parentElement = $parentElement;
                    const basePrice = this.getBasePrice();

                    //calculate the tax
                    const [taxType, taxTypeExclusive, taxPercentage, taxAmount] = this.getTaxDetails(basePrice);

                    //calculate the profit margin and sale price
                    const profileMargin = this.getProfitMargin();
                    const profitAmount = basePrice / 100 * profileMargin;

                    if (taxTypeExclusive == taxType) {
                        var purchasePrice = basePrice + taxAmount;
                    } else {
                        var purchasePrice = basePrice;
                    }

                    const salePrice = parseFloat(purchasePrice) + (parseFloat(purchasePrice) * parseFloat(
                        profileMargin / 100));

                    //calculate the final sale price
                    const discountAmount = this.getDiscountAmount(salePrice);
                    let finalSalePrice = salePrice - discountAmount;

                    this.$parentElement.find('.purchase-price').val(getAmount(purchasePrice));
                    this.$parentElement.find('.sale-price').val(getAmount(salePrice));
                    this.$parentElement.find('.final-sale-price').val(getAmount(finalSalePrice));
                },
                /**
                 * Recalculates the final sale price and profit margin based on updated sale price.
                 * Updates the corresponding fields in the UI.
                 *
                 * @param {Object} $parentElement - The parent DOM element containing price-related fields.
                 */
                salePriceChangeCalculation: function($parentElement) {

                    this.$parentElement = $parentElement;
                    const salePrice = this.getSalePrice();
                    const purchasePrice = this.getPurchasePrice();

                    if (!salePrice || !purchasePrice) return;

                    const profitAmount = salePrice - purchasePrice;
                    const profitPercentage = profitAmount / purchasePrice * 100;

                    const discountAmount = this.getDiscountAmount(salePrice);
                    let finalSalePrice = salePrice - discountAmount;

                    this.$parentElement.find('.final-sale-price').val(getAmount(finalSalePrice));
                    this.$parentElement.find('.profit-margin').val(getAmount(profitPercentage));
                },
                /**
                 * Retrieves tax-related details based on the base price.
                 *
                 * @param {number} basePrice - The base price of the product.
                 * @returns {Array} - An array containing tax type, exclusivity, percentage, and amount.
                 */
                getTaxDetails: function(basePrice) {
                    const taxType = this.getTaxType();
                    const taxPercentage = this.getTaxRate();
                    const taxAmount = basePrice / 100 * taxPercentage;

                    return [taxType, taxTypeExclusive, taxPercentage, taxAmount];
                },
                /**
                 * Calculates the discount amount based on the sale price and discount type.
                 *
                 * @param {number} salePriceWithTax - The sale price including tax.
                 * @returns {number} - The calculated discount amount.
                 */
                getDiscountAmount: function(salePriceWithTax) {
                    const discountType = this.getDiscountType();
                    const discountValue = this.getDiscountValue();

                    let discountAmount = 0;

                    if (discountTypePercent == discountType) {
                        if (discountValue > 100) {
                            notify("error", "The maximum discount value is 100%");
                            this.$parentElement.find('.discount').val(100);
                            discountAmount = salePriceWithTax;
                        } else {
                            discountAmount = salePriceWithTax / 100 * discountValue;
                        }
                    } else {
                        discountAmount = discountValue;
                    }
                    return discountAmount;

                },
                /**
                 * Retrieves the base price from the UI.
                 *
                 * @returns {number} - The base price of the product.
                 */
                getBasePrice: function() {
                    return parseFloat(this.$parentElement.find('.base-price').val() || 0);
                },
                /**
                 * Retrieves the selected tax type from the UI.
                 *
                 * @returns {number} - The tax type as an integer.
                 */
                getTaxType: function() {
                    return parseInt(this.$parentElement.find('.tax-type').val() || 0)
                },
                /**
                 * Retrieves the tax rate from the selected option in the UI.
                 *
                 * @returns {number} - The tax rate as a percentage.
                 */
                getTaxRate: function() {
                    return parseFloat(this.$parentElement.find('.tax-rate option:selected').attr(
                        'data-tax-rate') || 0);
                },
                /**
                 * Retrieves the profit margin percentage from the UI.
                 *
                 * @returns {number} - The profit margin as a percentage.
                 */
                getProfitMargin: function() {
                    return parseFloat(this.$parentElement.find('.profit-margin').val() || 0);
                },
                /**
                 * Retrieves the selected discount type from the UI.
                 *
                 * @returns {number} - The discount type as an integer.
                 */
                getDiscountType: function() {
                    return parseInt(this.$parentElement.find('.discount-type').val() || 0);
                },
                /**
                 * Retrieves the discount value from the UI.
                 *
                 * @returns {number} - The discount value.
                 */
                getDiscountValue: function() {
                    return parseFloat(this.$parentElement.find('.discount').val() || 0);
                },
                /**
                 * Retrieves the sale price from the UI.
                 *
                 * @returns {number} - The sale price of the product.
                 */
                getSalePrice: function() {
                    return parseFloat(this.$parentElement.find('.sale-price').val() || 0);
                }
            }
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>

        .divider-title {
            position: relative;
            text-align: center;
            width: max-content;
            margin: 0 auto;
        }

        .divider-title::before {
            position: absolute;
            content: '';
            top: 14px;
            left: -90px;
            background: #6b6b6b65;
            height: 2px;
            width: 80px;
        }

        .divider-title::after {
            position: absolute;
            content: '';
            top: 14px;
            right: -90px;
            background: #6b6b6b65;
            height: 2px;
            width: 80px;
        }

        .product-variation-row .form-label {
            white-space: nowrap !important;
        }
        .product-variation-row .form-group{
            min-width: 150px;
        }

    </style>
@endpush


@push('breadcrumb-plugins')
 <x-staff_permission_check permission="view product">
    <a class="btn  btn-outline--primary" href="{{ route('user.product.list') }}">
        <i class="las la-list me-1"></i>@lang('Product List')
    </a>
 </x-staff_permission_check>
 {{-- Import Products Button --}}
 <x-staff_permission_check permission="add product">
    <button type="button" class="btn btn-outline--primary" data-bs-toggle="modal" data-bs-target="#importProductModal">
        <i class="las la-file-import me-1"></i>@lang('Import Products')
    </button>
 </x-staff_permission_check>
@endpush

{{-- Include Import Modal Component --}}
@include('Template::user.product.import_modal')
