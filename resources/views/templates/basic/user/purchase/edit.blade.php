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
                                        value="{{ $purchase->purchase_date }}">
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label class="form-label">@lang('Supplier')</label>
                                    <select class="form-control select2" name="supplier_id" required disabled>
                                        <option value="{{ @$purchase->supplier_id }}">
                                            {{ __(@$purchase->supplier->name) }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label class="form-label">@lang('Warehouse')</label>
                                    <select class="form-control select2" name="warehouse_id" required disabled>
                                        <option value="{{ @$purchase->warehouse_id }}">
                                            {{ __(@$purchase->warehouse->name) }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label class="form-label">@lang('Status')</label>
                                    <select class="form-control select2" name="status" data-minimum-results-for-search="-1"
                                        required>
                                        <option value="{{ Status::PURCHASE_RECEIVED }}" @selected($purchase->status == Status::PURCHASE_RECEIVED)>
                                            @lang('Received')
                                        </option>
                                        <option value="{{ Status::PURCHASE_PENDING }}" @selected($purchase->status == Status::PURCHASE_PENDING)>
                                            @lang('Pending')
                                        </option>
                                        <option value="{{ Status::PURCHASE_ORDERED }}" @selected($purchase->status == Status::PURCHASE_ORDERED)>
                                            @lang('Ordered')
                                        </option>
                                    </select>
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label class="form-label">@lang('Reference No')</label>
                                    <input type="text" class="form-control" name="reference_no"
                                        placeholder="@lang('Reference No')" value="{{ $purchase->reference_number }}">
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label class="form-label">@lang('Attachment/Document')</label>
                                    <input type="file" class="form-control" name="attachment">
                                    @if ($purchase->attachment)
                                        <a class="text--primary mt-1 fs-13 d-block"
                                            href="{{ route('admin.download.attachment', encrypt(getFilePath('purchase_attachment') . '/' . $purchase->attachment)) }}">
                                            <i class="las la-download"></i>
                                            @lang('Download Attachment')
                                        </a>
                                    @endif
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
                                    @foreach ($purchase->purchaseDetails as $k => $purchaseDetail)
                                        <tr>
                                            <td>
                                                <span class="d-block">
                                                    {{ strLimit(__(@$purchaseDetail->product->name), 10) }}
                                                </span>
                                                <span class="d-block">
                                                    <strong class="product-code">
                                                        {{ @$purchaseDetail->productDetail->sku }}
                                                    </strong>
                                                </span>
                                            </td>
                                            <td class="mw-90 px-1">
                                                <input class="form-control quantity"
                                                    value="{{ $purchaseDetail->quantity }}"
                                                    name="purchase_details[{{ $k }}][qty]" type="number"
                                                    step="any">
                                                <input value="{{ $purchaseDetail->product_details_id }}"
                                                    name="purchase_details[{{ $k }}][product_details_id]"
                                                    type="hidden">
                                                <input value="{{ $purchaseDetail->id }}"
                                                    name="purchase_details[{{ $k }}][purchase_details_id]"
                                                    type="hidden">
                                            </td>
                                            <td class="mw-120 px-1">
                                                <input class="form-control base-price"
                                                    value="{{ getAmount($purchaseDetail->base_price) }}"
                                                    name="purchase_details[{{$k}}][base_price]"
                                                    type="number" step="any">
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <div class="flex-fill">
                                                        <select name="purchase_details[{{ $k }}][tax_type]"
                                                            class="form-control form-select  mw-120 fs-14 tax-type"
                                                            data-minimum-results-for-search="-1">
                                                            <option value="" selected>
                                                                @lang('Tax Type')
                                                            </option>
                                                            <option value="{{ Status::TAX_TYPE_EXCLUSIVE }}"
                                                                @selected($purchaseDetail->tax_type == Status::TAX_TYPE_EXCLUSIVE)>
                                                                @lang('Exclusive')
                                                            </option>
                                                            <option value="{{ Status::TAX_TYPE_INCLUSIVE }}"
                                                                @selected($purchaseDetail->tax_type == Status::TAX_TYPE_INCLUSIVE)>
                                                                @lang('Inclusive')
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="flex-fill">
                                                        <select name="purchase_details[{{ $k }}][tax_id]"
                                                            class="form-control form-select  mw-150 tax-rate"
                                                            data-minimum-results-for-search="-1">
                                                            <option value="" selected>
                                                                @lang('Select Tax')
                                                            </option>
                                                            @foreach ($taxes as $tax)
                                                                <option value="{{ $tax->id }}"
                                                                    data-tax-rate="{{ $tax->percentage }}"
                                                                    @selected($purchaseDetail->tax_id == $tax->id)>
                                                                    {{ __($tax->name) }} -
                                                                    {{ getAmount($tax->percentage) . '%' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="mw-110 px-1">
                                                <input name="purchase_details[{{ $k }}][purchase_price]"
                                                    type="number" step="any"
                                                    class="form-control mw-150 purchase-price" readonly
                                                    value="{{ getAmount($purchaseDetail->purchase_price) }}">
                                            </td>
                                            <td class="mw-90 px-1">
                                                <input readonly class="form-control subtotal"
                                                    value="{{ getAmount($purchaseDetail->purchase_price * $purchaseDetail->quantity) }}">
                                            </td>
                                            <td class="mw-110 px-1">
                                                <input class="form-control profit-margin"
                                                    value="{{ getAmount($purchaseDetail->profit_margin) }}"
                                                    name="purchase_details[{{ $k }}][profit_margin]"
                                                    type="number" step="any">
                                            </td>
                                            <td class="mw-110 px-1">
                                                <input class="form-control sale-price"
                                                    value="{{ getAmount($purchaseDetail->sale_price) }}"
                                                    name="purchase_details[{{ $k }}][sale_price]"
                                                    type="number" step="any">
                                            </td>
                                            <td class="mw-110 px-1">
                                                <div class="input-group input--group">
                                                    <span class="input-group-text">
                                                        <select
                                                            name="purchase_details[{{ $k }}][discount_type]"
                                                            class="border-0 bg-transparent p-0 discount-type">
                                                            <option value="{{ Status::DISCOUNT_PERCENT }}"
                                                                @selected($purchaseDetail->discount_type == Status::DISCOUNT_PERCENT)>
                                                                @lang('Percent')
                                                            </option>
                                                            <option value="{{ Status::DISCOUNT_FIXED }}"
                                                                @selected($purchaseDetail->discount_type == Status::DISCOUNT_FIXED)>
                                                                @lang('Fixed')</option>
                                                        </select>
                                                    </span>
                                                    <input type="number" step="any"
                                                        class="form-control mw-150 discount"
                                                        name="purchase_details[{{ $k }}][discount]"
                                                        value="{{ getAmount($purchaseDetail->discount_value) }}">
                                                </div>
                                            </td>
                                            <td class="mw-110 px-1">
                                                <div class="input-group">
                                                    <input type="number" step="any"
                                                        class="form-control mw-150 final-sale-price"
                                                        name="purchase_details[{{ $k }}][final_sale_price]"
                                                        readonly value="{{ getAmount($purchaseDetail->final_price) }}">
                                                    <button type="button"
                                                        class="btn btn--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to remove this item')?"
                                                        data-action="{{ route('user.purchase.remove.single.item', $purchaseDetail->id) }}">
                                                        <i class="las la-times"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-panel.ui.card.body>
                </x-panel.ui.card>
            </div>
            <div class="col-lg-12">
                <x-panel.ui.card>
                    <x-panel.ui.card.header>
                        <h4 class="card-title">@lang('Purchase Summary')</h4>
                    </x-panel.ui.card.header>
                    <x-panel.ui.card.body>
                        <div class="form-group">
                            <label>@lang('Purchase Discount')</label>
                            <div class="input-group input--group">
                                <span class="input-group-text">
                                    <select class="border-0 bg-transparent purchase-discount-type" name="discount_type">
                                        <option value="{{ Status::DISCOUNT_FIXED }}" @selected($purchase->discount_type == Status::DISCOUNT_FIXED)>
                                            @lang('Fixed')
                                        </option>
                                        <option value="{{ Status::DISCOUNT_PERCENT }}" @selected($purchase->discount_type == Status::DISCOUNT_PERCENT)>
                                            @lang('Percent')
                                        </option>
                                    </select>
                                </span>
                                <input type="number" step="any" class="form-control purchase-discount"
                                    placeholder="@lang('0.00')" name="discount"
                                    value="{{ getAmount($purchase->discount_value) }}">
                                <span class="input-group-text fixed-percent-symbol">
                                    @if ($purchase->discount_type == Status::DISCOUNT_FIXED)
                                        {{ __(gs('cur_text', getParentUser()->id)) }}
                                    @else
                                        @lang('%')
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Shipping Amount')</label>
                            <div class="input-group input--group">
                                <input type="number" step="any" class="form-control shipping-amount"
                                    name="shipping_amount" placeholder="@lang('0.00')"
                                    value="{{ getAmount($purchase->shipping_amount) }}">
                                <span class="input-group-text">{{ __(gs('cur_text', getParentUser()->id)) }}</span>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                                <span>@lang('Subtotal')</span>
                                <span class="text--info">
                                    <span class="summary-subtotal">
                                        {{ showAmount($purchase->subtotal, currencyFormat: false) }}
                                    </span>
                                    {{ __(gs('cur_text', getParentUser()->id)) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                                <span>@lang('Discount Amount')</span>
                                <span class="text--success">
                                    <span class="purchase-discount-text">
                                        {{ showAmount($purchase->discount_amount, currencyFormat: false) }}
                                    </span>
                                    {{ __(gs('cur_text', getParentUser()->id)) }}
                                    <span class="purchase-discount"></span>
                                    <span class="purchase-discount-percent">
                                        @if ($purchase->discount_type == Status::DISCOUNT_PERCENT)
                                            ({{ getAmount($purchase->discount_value) }}%)
                                        @endif
                                    </span>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                                <span>@lang('Shipping Amount')</span>
                                <span class="text--warning">
                                    <span class="shipping-amount-text">
                                        {{ showAmount($purchase->shipping_amount, currencyFormat: false) }}
                                    </span>
                                    {{ __(gs('cur_text', getParentUser()->id)) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                                <span>@lang('Total')</span>
                                <span class="text--info">
                                    <span class="total-text">
                                        {{ showAmount($purchase->total, currencyFormat: false) }}
                                    </span>
                                    {{ __(gs('cur_text', getParentUser()->id)) }}
                                </span>
                            </li>
                        </ul>
                    </x-panel.ui.card.body>
                </x-panel.ui.card>
            </div>
            <x-panel.ui.btn.submit />
        </div>
    </form>
    <x-confirmation-modal />
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            const selectedProductIds = @json($purchase->purchaseDetails->pluck('product_details_id')->toArray());


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

            //form submit handler
            $(".purchase-form").on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData($(this)[0])
                const $this = $(this);
                $.ajax({
                    url: "{{ route('user.purchase.update', $purchase->id) }}",
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
                                // location.reload();
                            }, 2000);
                        }
                    }
                });
            });

            //event handler for product select
            $('body').on('click', ".product-search-list-item", function() {
                const product = $(this).data('product');
                const html = htmlGenerateManager.productHtml(product);

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
                productHtml: function(product) {
                    if (selectedProductIds.includes(product.id)) {
                        return '';
                    }
                    selectedProductIds.push(product.id);
                    const length = $productTableElement.find("tbody tr").length;
                    const productDetail = product.original;
                    return `
                        <tr>
                            <td>
                                <span class="d-block">${product.name.substring(0,10)} </span>
                                <span class="d-block"><strong class="product-code">${productDetail.sku}</strong></span>
                            </td>
                            <td class="mw-70 px-1">
                                <input class="form-control quantity" value="1" name="purchase_details[${length}][qty]" type="number" step="any">
                                <input  value="${productDetail.id}" name="purchase_details[${length}][product_details_id]" type="hidden" >
                            </td>
                            <td class="mw-90 px-1">
                                <input class="form-control base-price" value="${getAmount(productDetail.base_price)}" name="purchase_details[${length}][base_price]" type="number" step="any">
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <div class="flex-fill">
                                        <select name="purchase_details[${length}][tax_type]" class="form-control form-select  tax-type" data-minimum-results-for-search="-1">
                                            <option value="" selected>@lang('Tax Type')</option>
                                            <option value="${taxTypeExclusive}"  ${ isSelected(taxTypeExclusive == productDetail.tax_type) }>@lang('Exclusive')</option>
                                            <option value="${taxTypeInclusive}" ${isSelected(taxTypeInclusive == productDetail.tax_type) }>@lang('Inclusive')</option>
                                        </select>
                                    </div>
                                    <div class="flex-fill">
                                        <select name="purchase_details[${length}][tax_id]" class="form-control form-select  tax-rate" data-minimum-results-for-search="-1">
                                                ${htmlGenerateManager.taxHtml(productDetail.tax_id)}
                                        </select>
                                    </div>
                                </div
                            </td>
                             <td class="mw-110 px-1">
                                 <input readonly class="form-control purchase-price" value="${getAmount(productDetail.purchase_price)}" name="purchase_details[${length}][purchase_price]">
                             </td>
                             <td class="mw-90 px-1">
                                 <input readonly class="form-control subtotal" value="${getAmount(productDetail.purchase_price)}">
                             </td>
                             <td class="mw-110 px-1">
                                <input class="form-control profit-margin" value="${getAmount(productDetail.profit_margin)}" name="purchase_details[${length}][profit_margin]" type="number" step="any">
                             </td>
                             <td class="mw-110 px-1">
                                <input class="form-control sale-price" value="${getAmount(productDetail.sale_price)}" name="purchase_details[${length}][sale_price]" type="number" step="any">
                             </td>
                             <td class="mw-110 px-1">
                                <div class="input-group input--group">
                                    <span class="input-group-text">
                                        <select name="purchase_details[${length}][discount_type]"
                                            class="border-0 bg-transparent p-0 discount-type">
                                            <option value="${discountTypePercent}" ${isSelected(discountTypePercent == productDetail.discount_type)}>@lang('Percent')</option>
                                            <option value="${discountTypeFixed}" ${isSelected(discountTypeFixed == productDetail.discount_type)}>@lang('Fixed')</option>
                                        </select>
                                    </span>
                                    <input type="number" step="any" class="form-control mw-150 discount" name="purchase_details[${length}][discount]">
                                </div>
                            </td>
                             <td class="mw-110 px-1">
                                 <div class="input-group">
                                    <input class="form-control final-sale-price" value="${getAmount(productDetail.final_price)}" name="purchase_details[${length}][final_sale_price]" type="number" step="any">
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
                 * calculate the total amoun
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
                 * Retrieves the purchase price from the UI.
                 *
                 * @returns {number}
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
                        $(".purchase-discount-percent").text(`(${getAmount(discountValue)}%)`);
                        $(".fixed-percent-symbol").text("%");
                    }
                    return discountAmount;
                }
            }

            function isSelected(selected) {
                return selected ? 'selected' : '';
            }

            $(".date-picker").flatpickr({
                maxDate: new Date()
            });

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
    <a class="btn btn--primary" href="{{ route('user.purchase.list') }}">
        <i class="las la-list me-1"></i>@lang('Purchase List')
    </a>
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
