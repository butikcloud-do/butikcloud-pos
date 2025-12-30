@extends($activeTemplate . 'layouts.master')
@section('panel')
    <form method="POST" class="purchase-form">
        @csrf
        <div class="row  responsive-row">
            <div class="col-12">
                <x-panel.ui.card>
                    <x-panel.ui.card.body>
                        <form action="{{ route('user.product.create') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 form-group">
                                    <label class="form-label">@lang('Purchase Date')</label>
                                    <input type="text" class="form-control date-picker" name="purchase_date" required
                                        value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label class="form-label">@lang('Supplier')</label>
                                    <x-panel.other.lazy_loading_select name="supplier_id" :route="route('user.supplier.lazy.loading')" />
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label class="form-label">@lang('Warehouse')</label>
                                    <select class="form-control select2" name="warehouse_id" required>
                                        <option value="" selected disabled>@lang('Select One')</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ __($warehouse->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label class="form-label">@lang('Status')</label>
                                    <select class="form-control select2" name="status" data-minimum-results-for-search="-1"
                                        required>
                                        <option value="{{ Status::PURCHASE_RECEIVED }}">@lang('Received')</option>
                                        <option value="{{ Status::PURCHASE_PENDING }}">@lang('Pending')</option>
                                        <option value="{{ Status::PURCHASE_ORDERED }}">@lang('Ordered')</option>
                                    </select>
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label class="form-label">@lang('Reference No')</label>
                                    <input type="text" class="form-control" name="reference_no"
                                        placeholder="@lang('Reference No')">
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label class="form-label">@lang('Attachment/Document')</label>
                                    <input type="file" class="form-control" name="attachment">
                                </div>
                            </div>
                        </form>
                    </x-panel.ui.card.body>
                </x-panel.ui.card>
            </div>
            <div class="col-lg-12">
                <x-panel.ui.card>
                    <x-panel.ui.card.header>
                        <h4 class="card-title">@lang('Search Product')</h4>
                    </x-panel.ui.card.header>
                    <x-panel.ui.card.body>
                        <div class="form-group position-relative">
                            <div class="input-group input--group">
                                <input type="text" class="form-control product-search-input"
                                    placeholder="@lang('Scan Barcode, Product Code, SKU')">
                                <span class="input-group-text">
                                    <i class="las la-barcode"></i>
                                </span>
                            </div>
                            <x-panel.other.product_search />
                        </div>
                    </x-panel.ui.card.body>
                </x-panel.ui.card>
            </div>
            <div class="col-12">
                <x-panel.ui.card>
                    <x-panel.ui.card.header>
                        <h4 class="card-title">@lang('Selected Product')</h4>
                    </x-panel.ui.card.header>
                    <x-panel.ui.card.body class="p-0">
                        <div class="table-responsive--md  table-responsive">
                            <table class="product-table table">
                                <thead>
                                    <tr>
                                        <th>@lang('Product')</th>
                                        <th>@lang('Qty')</th>
                                        <th>@lang('Base Price') ({{ __(gs('cur_sym', getParentUser()->id)) }})</th>
                                        <th>@lang('Tax')</th>
                                        <th>@lang('Purchase Price') ({{ __(gs('cur_sym', getParentUser()->id)) }})</th>
                                        <th>@lang('Subtotal') ({{ __(gs('cur_sym', getParentUser()->id)) }})</th>
                                        <th>@lang('Profit Margin') (%)</th>
                                        <th>@lang('Sale Price') ({{ __(gs('cur_sym', getParentUser()->id)) }})</th>
                                        <th>@lang('Sale Discount')</th>
                                        <th>@lang('Final Sale Price') ({{ __(gs('cur_sym', getParentUser()->id)) }})</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <x-panel.ui.table.empty_message message="No product you are selected" />
                                </tbody>
                            </table>
                        </div>
                    </x-panel.ui.card.body>
                </x-panel.ui.card>
            </div>
            <div class="col-lg-6">
                <x-panel.ui.card class="h-100">
                    <x-panel.ui.card.header>
                        <h4 class="card-title">@lang('Purchase Summary')</h4>
                    </x-panel.ui.card.header>
                    <x-panel.ui.card.body>
                        <div class="form-group">
                            <label>@lang('Purchase Discount')</label>
                            <div class="input-group input--group">
                                <span class="input-group-text">
                                    <select class="border-0 bg-transparent purchase-discount-type" name="discount_type">
                                        <option value="{{ Status::DISCOUNT_FIXED }}">
                                            @lang('Fixed')
                                        </option>
                                        <option value="{{ Status::DISCOUNT_PERCENT }}">
                                            @lang('Percent')
                                        </option>
                                    </select>
                                </span>
                                <input type="number" step="any" class="form-control purchase-discount"
                                    placeholder="@lang('0.00')" name="discount">
                                <span class="input-group-text fixed-percent-symbol">
                                    {{ __(gs('cur_text', getParentUser()->id)) }}
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Shipping Amount')</label>
                            <div class="input-group input--group">
                                <input type="number" step="any" class="form-control shipping-amount"
                                    name="shipping_amount" placeholder="@lang('0.00')">
                                <span class="input-group-text">{{ __(gs('cur_text', getParentUser()->id)) }}</span>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                                <span>@lang('Subtotal')</span>
                                <span class="text--info">
                                    <span class="summary-subtotal">
                                        @lang('0.00')
                                    </span>
                                    {{ __(gs('cur_text', getParentUser()->id)) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                                <span>@lang('Discount Amount')</span>
                                <span class="text--success">
                                    <span class="purchase-discount-text">
                                        @lang('0.00')
                                    </span>
                                    {{ __(gs('cur_text', getParentUser()->id)) }}
                                    <span class="purchase-discount"></span>
                                    <span class="purchase-discount-percent"></span>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                                <span>@lang('Shipping Amount')</span>
                                <span class="text--warning">
                                    <span class="shipping-amount-text">
                                        @lang('0.00')
                                    </span>
                                    {{ __(gs('cur_text', getParentUser()->id)) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                                <span>@lang('Total')</span>
                                <span class="text--info">
                                    <span class="total-text">
                                        @lang('0.00')
                                    </span>
                                    {{ __(gs('cur_text', getParentUser()->id)) }}
                                </span>
                            </li>
                        </ul>
                    </x-panel.ui.card.body>
                </x-panel.ui.card>
            </div>
            <div class="col-lg-6">
                <x-panel.ui.card class="h-100">
                    <x-panel.ui.card.header>
                        <h4 class="card-title">@lang('Supplier Payment')</h4>
                    </x-panel.ui.card.header>
                    <x-panel.ui.card.body>
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label>@lang('Paid Amount')</label>
                                <div class="input-group input--group">
                                    <input type="text" class="form-control" name="paid_amount"
                                        placeholder="@lang('0.00')">
                                    <span class="input-group-text">
                                        {{ __(gs('cur_text', getParentUser()->id)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label>@lang('Paid Date')</label>
                                <div class="input-group input--group">
                                    <input type="text" class="form-control date-picker" name="paid_date"
                                        value="{{ date('Y-m-d') }}">
                                    <span class="input-group-text">
                                        <i class="las la-calendar"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label>@lang('Due Amount')</label>
                                <div class="input-group input--group">
                                    <input type="text" class="form-control due-amount" name="due_amount"
                                        placeholder="@lang('0.00')" readonly>
                                    <span class="input-group-text">
                                        {{ __(gs('cur_text', getParentUser()->id)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label>@lang('Due Date')</label>
                                <div class="input-group input--group">
                                    <input type="text" class="form-control date-picker due-date-picker" name="due_date"
                                        value="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                    <span class="input-group-text">
                                        <i class="las la-calendar"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group col-lg-12">
                                <label>@lang('Payment Type')</label>
                                <select name="payment_type" class="form-control select2 payment-type">
                                    <option value="" selected disabled>@lang('Select Option')</option>
                                    @foreach ($paymentMethods as $paymentMethod)
                                        <option value="{{ $paymentMethod->id }}">
                                            {{ __($paymentMethod->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-lg-12">
                                <label>@lang('Payment Note')</label>
                                <textarea class="form-control" name="payment_note"></textarea>
                            </div>
                        </div>
                    </x-panel.ui.card.body>
                </x-panel.ui.card>
            </div>
            <x-panel.ui.btn.submit />
        </div>
    </form>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            const selectedProductIds = [];
            const taxes = @json($taxes);
            const $productTableElement = $('.product-table');
            const taxTypeExclusive = parseInt("{{ Status::TAX_TYPE_EXCLUSIVE }}");
            const taxTypeInclusive = parseInt("{{ Status::TAX_TYPE_INCLUSIVE }}");
            const discountTypePercent = parseInt("{{ Status::DISCOUNT_PERCENT }}");
            const discountTypeFixed = parseInt("{{ Status::DISCOUNT_FIXED }}");


            const calculationElementWhenInput =
                ".base-price, .tax-type, .tax-rate, .profit-margin, .discount, .discount-type";

            //event handler for base price and more input filed change
            $productTableElement.on('change input', calculationElementWhenInput, function() {
                const $parentElement = $(this).closest('tr');
                calculationManager.calculation($parentElement);
                calculationManager.totalCalculation();
            });

            //sale price change calculation
            $productTableElement.on('input change', ".sale-price", function() {
                const $parentElement = $(this).closest('tr');
                calculationManager.salePriceChangeCalculation($parentElement);
                calculationManager.totalCalculation();
            });

            //quantity change calculation
            $productTableElement.on('input change', ".quantity", function() {
                const $parentElement = $(this).closest('tr');
                calculationManager.qtyChangeCalculation($parentElement);
                calculationManager.totalCalculation();
            });

            //purchase discount change calculation
            $(".purchase-discount").on('input change', function() {
                calculationManager.totalCalculation();
            });

            //shipping amount change calculation
            $(".shipping-amount").on('input change', function() {
                calculationManager.totalCalculation();
            });

            //purchase discount type change calculation
            $(".purchase-discount-type").on('change', function() {
                calculationManager.totalCalculation();
            });

            //due amount calculation when paid amount changes
            $('input[name="paid_amount"]').on('input change', function() {
                calculationManager.calculateDueAmount();
            });

            //due amount calculation when total changes
            $(document).on('totalCalculated', function() {
                calculationManager.calculateDueAmount();
            });


            //form submit handler
            $(".purchase-form").on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData($(this)[0])
                const $this = $(this);
                $.ajax({
                    url: "{{ route('user.purchase.store') }}",
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
                            $(".purchase-form").trigger('reset');
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                    }
                });
            });

            //event handler for product select
            $('body').on('click', ".product-search-list-item", function() {
                const product = $(this).data('product');
                console.log(product);
                const productDetails = $(this).data('product-details');

                let html = htmlGenerateManager.productHtml(product);

                $('.empty-message-row').remove();
                $('.product-table').find('tbody').append(html);
                $(".product-search-list").empty().addClass('d-none');
                calculationManager.totalCalculation();
            });

            //remove product handler
            $('body').on('click', ".remove-btn", function() {
                const id = $(this).data('id');
                const idIndex = selectedProductIds.findIndex(selectedProductId => selectedProductId == id);
                $(this).closest('tr').remove();
                selectedProductIds.splice(idIndex, 1);
                if (selectedProductIds.length <= 0) {
                    $('.product-table').find('tbody').html(htmlGenerateManager.emptyHtml());
                }
                calculationManager.totalCalculation();
            });

            const htmlGenerateManager = {

                /**
                 * Generates an HTML row for a product in a table layout.
                 *
                 * @param {object} productDetail - Details about the specific product variant (e.g., ID, SKU, final price).
                 * @param {object} product - The main product object containing general details (e.g., image, name).
                 * @returns {string} A `<tr>` element containing product image, name, SKU, and quantity input field.
                 *                  Returns an empty string if the product ID is already in `selectedProductIds`.
                 */
                productHtml: function(product, index = undefined) {
                    if (selectedProductIds.includes(product.id)) {
                        return '';
                    }
                    selectedProductIds.push(product.id);
                    const productDetail = product.original;
                    return `
                        <tr>
                            <td>
                                <span class="d-block">${product.name.substring(0,10)}</span>
                                <span class="d-block"><strong class="product-code">${productDetail.sku}</strong></span>
                            </td>
                            <td class="mw-70 px-1">
                                <input class="form-control quantity" value="1" name="purchase_details[${product.id}][qty]" type="number" step="any">
                                <input  value="${productDetail.id}" name="purchase_details[${product.id}][product_details_id]" type="hidden" >
                            </td>
                            <td class="mw-120 px-1">
                                <input class="form-control base-price" value="${getAmount(productDetail.base_price)}" name="purchase_details[${product.id}][base_price]" type="number" step="any">
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <div class="flex-fill">
                                        <select name="purchase_details[${product.id}][tax_type]" class="form-control form-select  tax-type" data-minimum-results-for-search="-1">
                                            <option value="" selected>@lang('Tax Type')</option>
                                            <option value="${taxTypeExclusive}"  ${ isSelected(taxTypeExclusive == productDetail.tax_type) }>@lang('Exclusive')</option>
                                            <option value="${taxTypeInclusive}" ${isSelected(taxTypeInclusive == productDetail.tax_type) }>@lang('Inclusive')</option>
                                        </select>
                                    </div>
                                    <div class="flex-fill">
                                        <select name="purchase_details[${product.id}][tax_id]" class="form-control form-select  tax-rate" data-minimum-results-for-search="-1">
                                                ${htmlGenerateManager.taxHtml(productDetail.tax_id)}
                                        </select>
                                    </div>
                                </div
                            </td>
                             <td class="mw-110 px-1">
                                 <input readonly class="form-control purchase-price" value="${getAmount(productDetail.purchase_price)}" name="purchase_details[${product.id}][purchase_price]">
                             </td>
                             <td class="mw-90 px-1">
                                 <input readonly class="form-control subtotal" value="${getAmount(productDetail.purchase_price)}">
                             </td>
                             <td class="mw-70 px-1">
                                <input class="form-control profit-margin" value="${getAmount(productDetail.profit_margin)}" name="purchase_details[${product.id}][profit_margin]" type="number" step="any">
                             </td>
                             <td class="mw-110 px-1">
                                <input class="form-control sale-price" value="${getAmount(productDetail.sale_price)}" name="purchase_details[${product.id}][sale_price]" type="number" step="any">
                             </td>
                             <td class="mw-110 px-1">
                                <div class="input-group input--group">
                                    <span class="input-group-text">
                                        <select name="purchase_details[${product.id}][discount_type]"
                                            class="border-0 bg-transparent p-0 discount-type">
                                            <option value="${discountTypePercent}" ${isSelected(discountTypePercent == productDetail.discount_type)}>@lang('Percent')</option>
                                            <option value="${discountTypeFixed}" ${isSelected(discountTypeFixed == productDetail.discount_type)}>@lang('Fixed')</option>
                                        </select>
                                    </span>
                                    <input type="number" step="any" class="form-control mw-150 discount" name="purchase_details[${product.id}][discount]" value="${getAmount(productDetail.discount_value)}">
                                </div>
                            </td>
                             <td class="mw-110 px-1">
                                 <div class="input-group">
                                    <input class="form-control final-sale-price" value="${getAmount(productDetail.final_price)}" name="purchase_details[${product.id}][final_sale_price]" type="number" step="any">
                                    <button type="button" class="btn btn--danger remove-btn" data-id="${productDetail.id}">
                                        <i class="las la-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>`
                },

                /**
                 * Generates an HTML option for tax.
                 *
                 * @param {integer} productTaxId
                 *
                 */
                taxHtml: function(productTaxId) {
                    let html = '';
                    $.each(taxes, function(i, tax) {
                        html +=
                            `<option data-tax-rate="${tax.percentage}" value="${tax.id}" ${isSelected(tax.id == productTaxId)}>
                                ${tax.name} - ${getAmount(tax.percentage)}%
                            </option>`
                    });

                    return html;
                },
                /**
                 * Generates an HTML row for a product empty layout.
                 *
                 * @returns {string} A `<tr>` element containing product image, name, SKU, and quantity input field.
                 *                  Returns an empty string if the product ID is already in `selectedProductIds`.
                 */

                emptyHtml: function() {
                    return `
                        <tr class="text-center empty-message-row">
                            <td colspan="100%" class="text-center">
                                <div class="p-5">
                                    <img src="{{ asset('assets/images/empty_box.png') }}" class="empty-message">
                                    <span class="d-block">@lang('No product you are selected')</span>
                                    <span class="d-block fs-13 text-muted">@lang('There are no available data to display on this table at the moment.')</span>
                                </div>
                            </td>
                        </tr>
                    `
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


                    if (taxTypeExclusive == taxType) {
                        var purchasePrice = basePrice + taxAmount;
                    } else {
                        var purchasePrice = basePrice;
                    }

                    const profitAmount = purchasePrice / 100 * profileMargin;
                    const salePrice = purchasePrice + profitAmount;

                    //calculate the final sale price
                    const discountAmount = this.getDiscountAmount(salePrice);
                    const finalSalePrice = salePrice - discountAmount;
                    const qty = this.getQuantity();
                    const subtotal = purchasePrice * qty;

                    this.$parentElement.find('.purchase-price').val(getAmount(purchasePrice));
                    this.$parentElement.find('.sale-price').val(getAmount(salePrice));
                    this.$parentElement.find('.final-sale-price').val(getAmount(finalSalePrice));
                    this.$parentElement.find('.subtotal').val(getAmount(subtotal));
                },
                /**
                 * Recalculates the final sale price and profit margin based on updated sale price.
                 * Updates the corresponding fields in the UI.
                 *
                 * @param {Object} $parentElement - The parent DOM element containing price-related fields.
                 */
                salePriceChangeCalculation: function($parentElement) {
                    // return true;
                    this.$parentElement = $parentElement;
                    const purchasePrice = this.getPurchasePrice();
                    const salePrice = this.getSalePrice();

                    if (!salePrice || !purchasePrice) return;

                    const profitAmount = salePrice - purchasePrice;
                    const profitPercentage = profitAmount / purchasePrice * 100;

                    const discountAmount = this.getDiscountAmount(salePrice);
                    let finalSalePrice = salePrice - discountAmount;

                    this.$parentElement.find('.final-sale-price').val(getAmount(finalSalePrice));
                    this.$parentElement.find('.profit-margin').val(getAmount(profitPercentage));
                },

                /**
                 * calculate the sub total amount when change the quantity
                 * Updates the corresponding fields in the UI.
                 *
                 * @param {Object} $parentElement - The parent DOM element containing price-related fields.
                 */
                qtyChangeCalculation: function($parentElement) {
                    // return true;
                    this.$parentElement = $parentElement;
                    const purchasePrice = this.getPurchasePrice();
                    const qty = this.getQuantity();
                    if (!purchasePrice || !qty) return;
                    const subtotal = purchasePrice * qty;
                    this.$parentElement.find('.subtotal').val(getAmount(subtotal));
                },
                /**
                 * calculate the total amount
                 * Updates the corresponding fields in the UI.
                 *
                 */
                totalCalculation: function() {
                    let subtotal = 0;
                    const $elements = $productTableElement.find('tbody tr');
                    $elements.each(function(index, element) {
                        const $element = $(element);
                        calculationManager.$parentElement = $element;
                        const qty = calculationManager.getQuantity();
                        const purchasePrice = calculationManager.getPurchasePrice();
                        if (qty || purchasePrice) {
                            subtotal += purchasePrice * qty;
                        }
                    });

                    const getDiscountAmount = calculationManager.getPurchaseDiscountDetails(subtotal);

                    if (subtotal > 0) {
                        var discountAmount = getDiscountAmount;
                    } else {
                        var discountAmount = 0;
                    }
                    const shippingAmount = calculationManager.getShippingAmount();
                    const total = subtotal - discountAmount + shippingAmount;

                    $('.summary-subtotal').text(getAmount(subtotal));
                    $('.purchase-discount-text').text(getAmount(getDiscountAmount));
                    $('.shipping-amount-text').text(shippingAmount);
                    $('.total-text').text(getAmount(total));

                    // Trigger due amount calculation
                    $(document).trigger('totalCalculated');
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
                 * Calculate and update the due amount field
                 */
                calculateDueAmount: function() {
                    const total = parseFloat($('.total-text').text().replace(/,/g, '')) || 0;
                    const paidAmount = parseFloat($('input[name="paid_amount"]').val()) || 0;
                    const dueAmount = Math.max(0, total - paidAmount);
                    $('.due-amount').val(getAmount(dueAmount));
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
                },
                /**
                 * Retrieves the purchase price from the UI.
                 *
                 * @returns {number} - The sale price of the product.
                 */
                getPurchasePrice: function() {
                    return parseFloat(this.$parentElement.find('.purchase-price').val() || 0);
                },

                /**
                 * Retrieves the quantity from the UI.
                 *
                 * @returns {number}
                 */
                getQuantity: function() {
                    return parseFloat(this.$parentElement.find('.quantity').val() || 0);
                },
                /**
                 * Retrieves the shipping amount.
                 *
                 * @returns {number}
                 */
                getShippingAmount: function() {
                    return parseFloat($('.shipping-amount').val() || 0);
                },
                /**
                 * get the purchase discount details
                 *
                 */
                getPurchaseDiscountDetails: function(subtotal) {
                    const discountType = parseInt($('.purchase-discount-type').val());
                    const discountValue = parseFloat($(".purchase-discount").val() || 0);

                    var discountAmount = 0;
                    if (discountType == discountTypeFixed) {
                        discountAmount = discountValue;
                        $(".purchase-discount-percent").empty();
                        $(".fixed-percent-symbol").text("{{ __(gs('cur_text', getParentUser()->id)) }}");
                    } else {
                        if (discountValue > 100) {
                            notify('error', "@lang('Discount percent can not be greater then 100')");
                            return subtotal;
                        }
                        discountAmount = subtotal / 100 * discountValue;
                        $(".purchase-discount-percent").text(` -  ${getAmount(discountValue)}%`);
                        $(".fixed-percent-symbol").text("%");
                    }
                    return discountAmount;
                }
            }

            function isSelected(selected) {
                return selected ? 'selected' : '';
            }

            // Initialize regular date pickers (paid date)
            $(".date-picker:not(.due-date-picker)").flatpickr({
                maxDate: new Date()
            });

            // Initialize due date picker with restrictions
            $(".due-date-picker").flatpickr({
                minDate: new Date().fp_incr(1), // Tomorrow and later
                defaultDate: new Date().fp_incr(1), // Default to tomorrow
                disable: [
                    function(date) {
                        // Disable today and all past dates
                        return date < new Date().fp_incr(1);
                    }
                ]
            });

            // Initialize due amount calculation
            calculationManager.calculateDueAmount();

            //get product if request has product code
            @if (request()->product_code)
                $.ajax({
                    type: "GET",
                    url: "{{ route('user.product.search') }}",
                    dataType: "json",
                    data: {
                        search: "{{ request()->product_code }}"
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            if (response.data.products && Object.keys(response.data.products)
                                .length <= 0) {
                                $searchResultElement.html(emptyResult);
                                return;
                            }
                            const products = response.data.products;
                            let html = ``;
                            const productTypeVariable = parseInt("{{ Status::PRODUCT_TYPE_VARIABLE }}");

                            products.forEach(product => {
                                html += htmlGenerateManager.productHtml(product);
                            });
                            $('.empty-message-row').remove();
                            $('.product-table').find('tbody').append(html);
                            calculationManager.totalCalculation();
                        }
                    }
                });
            @endif


        })(jQuery);
    </script>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/flatpickr.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/global/css/flatpickr.min.css') }}">
@endpush

@push('breadcrumb-plugins')
    <x-staff_permission_check permission="view purchase">
        <a class="btn btn--primary" href="{{ route('user.purchase.list') }}">
            <i class="las la-list me-1"></i>@lang('Purchase List')
        </a>
    </x-staff_permission_check>
@endpush

@push('style')
    <style>
        .product-image {
            max-width: 40px;
            border-radius: 5px;
        }

        .mw-150 {
            max-width: 120px !important;
        }

        .mw-90 {
            max-width: 90px !important;
        }

        .mw-70 {
            max-width: 70px !important;
        }

        .mw-110 {
            max-width: 110px !important;
        }

        .mw-120 {
            max-width: 120px !important;
        }
    </style>
@endpush
