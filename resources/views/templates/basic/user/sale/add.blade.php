@extends($activeTemplate . 'layouts.master')
@push('style')
    <style>
        .product-title-block {
            transition: background-color 0.2s;
            cursor: pointer;
        }

        .product-title-block:hover {
            background-color: rgba(var(--primary-rgb), 0.05) !important;
        }

        .product-title-block .product-name-text {
            color: hsl(var(--primary));
            font-weight: 600;
            text-decoration: underline;
            text-underline-offset: 3px;
        }
    </style>
@endpush
@section('panel')
    <form method="POST" class="sale-form no-submit-loader">
        @csrf
        <div class="row  responsive-row">
            <div class="col-12">
                <x-panel.ui.card>
                    <x-panel.ui.card.body>
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label class="form-label">@lang('Sale Date')</label>
                                <input type="text" class="form-control date-picker" name="sale_date" required
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-sm-6 form-group">
                                <label class="form-label">@lang('Customer')</label>
                                <div class="d-fle gap-2 flex-wrap">
                                    <div class="position-relative flex-grow-1" id="customer-select2">
                                        <select class="form-control form--control" name="customer_id" required>
                                            <option value="1" selected>@lang('Walk-in Customer')</option>
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn--primary btn-large add-customer"><i
                                            class="fa fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label class="form-label">@lang('Warehouse')</label>
                                <select class="form-control select2" name="warehouse_id" required>
                                    <option value="" selected disabled>@lang('Select One')</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ __($warehouse->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label class="form-label">@lang('Status')</label>
                                <select class="form-control select2" name="status" data-minimum-results-for-search="-1"
                                    required>
                                    <option value="{{ Status::SALE_FINAL }}">@lang('Final')</option>
                                    <option value="{{ Status::SALE_QUOTATION }}">@lang('Quotation')</option>
                                </select>
                            </div>
                        </div>
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
                            {{-- Enhanced search results panel --}}
                            <div class="product-search-results-enhanced d-none"></div>
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
                                        <th>@lang('Unit Price') ({{ gs('cur_sym', getParentUser()->id) }})</th>
                                        <th>@lang('Tax Amount') ({{ gs('cur_sym', getParentUser()->id) }})</th>
                                        <th>@lang('Discount')</th>
                                        <th>@lang('Sale Price') ({{ gs('cur_sym', getParentUser()->id) }})</th>
                                        <th>@lang('Quantity')</th>
                                        <th>@lang('Subtotal') ({{ gs('cur_sym', getParentUser()->id) }})</th>
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
                        <h4 class="card-title">@lang('Sale Summary')</h4>
                    </x-panel.ui.card.header>
                    <x-panel.ui.card.body>
                        <div class="form-group">
                            <label>@lang('Sale Discount')</label>
                            <div class="input-group input--group">
                                <span class="input-group-text">
                                    <select class="border-0 bg-transparent sale-discount-type" name="discount_type">
                                        <option value="{{ Status::DISCOUNT_FIXED }}">
                                            @lang('Fixed')
                                        </option>
                                        <option value="{{ Status::DISCOUNT_PERCENT }}">
                                            @lang('Percent')
                                        </option>
                                    </select>
                                </span>
                                <input type="number" step="any" class="form-control sale-discount-value"
                                    placeholder="@lang('0.00')" name="discount_value" min="0">
                                <span class="input-group-text fixed-percent-symbol">
                                    {{ __(gs('cur_text', getParentUser()->id)) }}
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Shipping Amount')</label>
                            <div class="input-group input--group">
                                <input type="number" step="any" class="form-control" name="shipping_amount"
                                    placeholder="@lang('0.00')" min="0">
                                <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
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
                                    <span class="summary-discount-amount">
                                        @lang('0.00')
                                    </span>
                                    {{ __(gs('cur_text', getParentUser()->id)) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                                <span>@lang('Shipping Amount')</span>
                                <span class="text--warning">
                                    <span class="summary-shipping-amount">
                                        @lang('0.00')
                                    </span>
                                    {{ __(gs('cur_text', getParentUser()->id)) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                                <span>@lang('Total')</span>
                                <span class="text--info">
                                    <span class="summary-total">
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
                        <h4 class="card-title">@lang('Payment Information')</h4>
                    </x-panel.ui.card.header>
                    <x-panel.ui.card.body>
                        <div class="row">
                            <div class="form-group col-12">
                                <label>@lang('Paid Amount')</label>
                                <div class="input-group input--group">
                                    <input type="number" step="any" class="form-control paid-amount"
                                        name="payment[0][amount]" placeholder="@lang('0.00')" required readonly>
                                    <span class="input-group-text">
                                        {{ __(gs('cur_text', getParentUser()->id)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>@lang('Payment Method')</label>
                                <select name="payment[0][payment_type]" class="form-control select2"
                                    required>
                                    <option value="" selected disabled>@lang('Select Option')</option>
                                    @foreach ($paymentMethods as $paymentMethod)
                                        <option value="{{ $paymentMethod->id }}">
                                            {{ __($paymentMethod->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-12">
                                <label>@lang('Payment Note')</label>
                                <textarea class="form-control" name="payment[0][note]"></textarea>
                            </div>
                        </div>
                    </x-panel.ui.card.body>
                </x-panel.ui.card>
            </div>
            <div class="col-12 ">
                <div class="d-flex gap-3 flex-wrap justify-content-end">
                    <button class="btn btn--success btn-large only-save" type="button">
                        <span class="me-1"><i class="fa-regular fa-paper-plane"></i></span>
                        @lang('Save')
                    </button>
                    <button class="btn btn--primary btn-large" type="submit">
                        <span class="me-1"><i class="fa fa-print"></i></span>
                        @lang('Save & Print')
                    </button>
                </div>
            </div>
        </div>
    </form>

    <x-panel.ui.modal id="customer-modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add Customer')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-panel.ui.modal.header>
        <x-panel.ui.modal.body>
            <form method="POST" action="{{ route('user.customer.create') }}?from=pos" class="customer-form">
                @csrf
                <div class="row">
                    <div class="form-group col-lg-12">
                        <label>@lang('Name')</label>
                        <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Email')</label>
                        <input type="email" class="form-control" name="email" required value="{{ old('email') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Mobile')</label>
                        <input type="tel" class="form-control" name="mobile" required value="{{ old('mobile') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Address')</label>
                        <input type="text" class="form-control" name="address" value="{{ old('address') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('City')</label>
                        <input type="text" class="form-control" name="city" value="{{ old('city') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('State')</label>
                        <input type="text" class="form-control" name="state" value="{{ old('state') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Zip')</label>
                        <input type="text" class="form-control" name="zip" value="{{ old('zip') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Postcode')</label>
                        <input type="text" class="form-control" name="postcode" value="{{ old('postcode') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Country')</label>
                        <input type="text" class="form-control" name="country" value="{{ old('country') }}">
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <x-panel.ui.btn.modal />
                        </div>
                    </div>
                </div>
            </form>
        </x-panel.ui.modal.body>
    </x-panel.ui.modal>
    <x-panel.ui.modal id="quick-view-modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Product Quick View')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-panel.ui.modal.header>
        <x-panel.ui.modal.body>
            <form class="quick-view-form no-submit-loader">
                <div class="row">
                    <div class="form-group col-lg-6">
                        <label>@lang('Product Name')</label>
                        <input type="text" class="form-control" name="modal_product_name" readonly disabled>
                    </div>
                    <div class="form-group col-lg-3">
                        <label>@lang('SKU')</label>
                        <input type="text" class="form-control" name="modal_product_sku" readonly disabled>
                    </div>
                    <div class="form-group col-lg-3">
                        <label>@lang('Current Stock')</label>
                        <input type="text" class="form-control" name="modal_product_stock" readonly disabled>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Unit Price')</label>
                        <div class="input-group">
                            <input type="number" step="any" class="form-control" name="modal_unit_price" readonly disabled>
                            <span class="input-group-text">{{ gs('cur_text', getParentUser()->id) }}</span>
                        </div>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Quantity')</label>
                        <div class="input-group">
                            <input type="number" step="any" class="form-control" name="modal_quantity" readonly disabled>
                            <span class="input-group-text modal-unit-name"></span>
                        </div>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Tax Type')</label>
                        <select name="modal_tax_type" class="form-control" readonly disabled>
                            <option value="{{ Status::TAX_TYPE_EXCLUSIVE }}">@lang('Exclusive')</option>
                            <option value="{{ Status::TAX_TYPE_INCLUSIVE }}">@lang('Inclusive')</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Tax')</label>
                        <select name="modal_tax_id" class="form-control" readonly disabled>
                            <option value="0">@lang('No Tax')</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Discount Type')</label>
                        <select name="modal_discount_type" class="form-control" readonly disabled>
                            <option value="{{ Status::DISCOUNT_PERCENT }}">@lang('Percent')</option>
                            <option value="{{ Status::DISCOUNT_FIXED }}">@lang('Fixed')</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Discount Value')</label>
                        <input type="number" step="any" class="form-control" name="modal_discount_value" readonly disabled>
                    </div>
                    <input type="hidden" name="modal_product_detail_id">
                    <div class="col-12 mt-3 text-end">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                    </div>
                </div>
            </form>
        </x-panel.ui.modal.body>
    </x-panel.ui.modal>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {

            const selectedProductIds = [];
            const $productTableElement = $('.product-table');
            const discountTypePercent = parseInt("{{ Status::DISCOUNT_PERCENT }}");
            const discountTypeFixed = parseInt("{{ Status::DISCOUNT_FIXED }}");
            let saveActionType = 'save_and_print';

            //event handler for base price and more input filed change
            $productTableElement.on('change input', '.discount-value, .discount-type, .quantity', function() {
                calculateAll();
            });

            $('.only-save').on('click', function() {
                saveActionType = "only_save";
                $(".sale-form").submit();
            });

            $(window).on('afterprint', function() {
                saveActionType = "save_and_print";
                $('body').find('.print-content').remove();
            });

            //form submit handler
            $(".sale-form").on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData($(this)[0])
                formData.append('save_action_type', saveActionType);
                formData.append('invoice_type', 'regular');
                formData.append('is_pos_sale', '{{ Status::NO }}');
                const $this = $(this);

                $.ajax({
                    url: "{{ route('user.sale.store') }}",
                    method: "POST",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $(".sale-form").find(`button`).addClass('disabled').attr(`disabled`, true);
                    },
                    complete: function() {
                        $(".sale-form").find(`button`).removeClass('disabled').attr(`disabled`,
                            false);
                    },
                    success: function(resp) {

                        if (resp.status == 'success') {
                            selectedProductIds.length = 0;
                            $(".sale-form").trigger('reset');
                            $('.product-table').find('tbody').html(htmlGenerateManager.emptyHtml());
                            //reset customer html
                            $('#customer-select2')
                                .find('#select2-customer_id-container')
                                .text("@lang('Walk In Customer')");
                            $('#customer-select2')
                                .find('select')
                                .html(
                                    `<option value="1" selected>@lang('Walk In Customer')</option>`
                                );
                            $("body").find(`select[name=customer_id]`).val(1);
                            calculateAll();
                            if (saveActionType == 'save_and_print') {
                                $('body').append(
                                    `<div class="print-content">${resp.data.html}</div>`);
                                window.print();
                            } else {
                                notify('success', resp.message);
                            }
                        } else {
                            notify('error', resp.message);
                        }
                    }
                });
            });

            //event handler for product select
            $('body').on('click', ".product-search-list-item", function() {
                const product = $(this).data('product');

                if (parseInt(product.in_stock || 0) <= 0) {
                    notify('error', `The product ${product.sku} is out of stock`);
                    $(".product-search-list").empty().addClass('d-none');
                    return;
                }

                let html = htmlGenerateManager.productHtml(product);

                $('.empty-message-row').remove();
                $('.product-table').find('tbody').append(html);
                $(".product-search-list").empty().addClass('d-none');
                calculateAll();
            });

            // ========== QUICK VIEW MODAL LOGIC ==========
            let $currentRow = null;
            let taxesData = [];

            $('body').on('click', '.product-title-block', function() {
                $currentRow = $(this).closest('tr');
                const productDetailId = $currentRow.data('product-detail-id');
                const warehouseId = $('select[name=warehouse_id]').val();

                console.log('Quick View Clicked:', { productDetailId, warehouseId });

                if (!productDetailId) {
                    notify('error', "@lang('Product ID missing')");
                    return;
                }

                $('#quick-view-modal').modal('show');

                let url = "{{ route('user.sale.product.quick.view', ':id') }}";
                url = url.replace(':id', productDetailId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        warehouse_id: warehouseId
                    },
                    success: function(resp) {
                        console.log('Quick View Response:', resp);
                        if (resp.status == 'success') {
                            const product = resp.data.product;
                            taxesData = resp.data.taxes;

                            const $modal = $('#quick-view-modal');
                            $modal.find('[name=modal_product_name]').val(product.name);
                            $modal.find('[name=modal_product_sku]').val(product.sku);
                            $modal.find('[name=modal_product_stock]').val(`${product.in_stock} ${product.unit_name}`);
                            $modal.find('.modal-unit-name').text(product.unit_name);
                            $modal.find('[name=modal_product_detail_id]').val(product.id);

                            // Get current values from the row to show in the modal (read-only)
                            const unitPrice = $currentRow.find('.unit-price').val();
                            const quantity = $currentRow.find('.quantity').val();
                            const discountType = $currentRow.find('.discount-type').val();
                            const discountValue = $currentRow.find('.discount-value').val();
                            const taxType = $currentRow.find('.row-tax-type').val();
                            const taxId = $currentRow.find('.row-tax-id').val();

                            $modal.find('[name=modal_unit_price]').val(unitPrice);
                            $modal.find('[name=modal_quantity]').val(quantity);
                            $modal.find('[name=modal_discount_type]').val(discountType);
                            $modal.find('[name=modal_tax_type]').val(taxType);
                            $modal.find('[name=modal_discount_value]').val(discountValue);

                            // Populate Taxes
                            let taxOptions = `<option value="0" data-percentage="0">@lang('No Tax')</option>`;
                            taxesData.forEach(tax => {
                                taxOptions += `<option value="${tax.id}" data-percentage="${tax.percentage}">${tax.name} (${getAmount(tax.percentage)}%)</option>`;
                            });
                            $modal.find('[name=modal_tax_id]').html(taxOptions).val(taxId);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Quick View AJAX Error:', { xhr, status, error });
                        notify('error', "@lang('Something went wrong')");
                        $('#quick-view-modal').modal('hide');
                    }
                });
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
                calculateAll();
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
                            <tr data-product-detail-id="${productDetail.id}">
                                <td class="product-title-block">
                                    <span class="d-block product-name-text">${product.name}</span>
                                    <span class="d-block"><strong class="product-code">${productDetail.sku}</strong></span>
                                    <span class="d-block">
                                        @lang('In Stock'):
                                        <span class="in-stock">${product.in_stock}</span>
                                        <span class="unit-name">${product.unit_name}</span>
                                    </span>
                                    <input name="sale_details[${productDetail.id}][product_id]" value="${productDetail.product_id}" type="hidden" />
                                    <input name="sale_details[${productDetail.id}][product_detail_id]" value="${productDetail.id}" type="hidden" />

                                    <input name="sale_details[${productDetail.id}][tax_id]" value="${productDetail.tax_id}" type="hidden" class="row-tax-id" />
                                    <input name="sale_details[${productDetail.id}][tax_type]" value="${productDetail.tax_type}" type="hidden" class="row-tax-type" />
                                    <input name="sale_details[${productDetail.id}][tax_amount]" value="${getAmount(productDetail.tax_amount)}" type="hidden" class="row-tax-amount" />
                                    <input name="sale_details[${productDetail.id}][tax_percentage]" value="${getAmount(productDetail.tax_percentage)}" type="hidden" class="row-tax-percentage" />
                                </td>
                                <td>
                                    <input value="${getAmount(productDetail.sale_price - productDetail.tax_amount)}"  readonly class="form-control row-unit-price-view"/>
                                </td>
                                <td>
                                    <input value="${getAmount(productDetail.tax_amount)} - ${getAmount(productDetail.tax_percentage)}%"  readonly class="form-control row-tax-view"/>
                                </td>
                                <td>
                                    <div class="input-group input--group">
                                        <span class="input-group-text">
                                            <select name="sale_details[${productDetail.id}][discount_type]"
                                                class="border-0 bg-transparent p-0 discount-type">
                                                <option value="${discountTypePercent}" ${isSelected(discountTypePercent == productDetail.discount_type)}>@lang('Percent')</option>
                                                <option value="${discountTypeFixed}" ${isSelected(discountTypeFixed == productDetail.discount_type)}>@lang('Fixed')</option>
                                            </select>
                                        </span>
                                        <input type="number" step="any" class="form-control  discount-value" name="sale_details[${productDetail.id}][discount_value]" value="${getAmount(productDetail.discount_value)}">
                                    </div>
                                </td>
                                 <td>
                                    <input value="${getAmount(productDetail.final_price)}"  readonly class="form-control sale-price"/>
                                    <input type="hidden" value="${getAmount(productDetail.sale_price)}"  readonly class="form-control unit-price "/>
                                </td>
                                 <td>
                                    <div class="input-group input--group">
                                        <input value="1"  type="number" step="any"  class="form-control quantity" name="sale_details[${productDetail.id}][quantity]"/>
                                        <span class="input-group-text">${product.unit_name}</span>
                                    </div>
                                </td>
                                 <td>
                                    <div class="input-group">
                                        <input value="${getAmount(productDetail.final_price)}"  readonly class="form-control sub-total"/>
                                        <button type="button" class="btn btn--danger remove-btn" data-id="${productDetail.id}">
                                            <i class="las la-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>`
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

            function calculateAll() {
                const $items = $productTableElement.find(`tbody tr`);
                let subtotal = 0;

                $.each($items, function(i, item) {
                    const $item = $(item);
                    const stock = parseFloat($item.find('.in-stock').text() || 0);
                    let qty = parseFloat($item.find('.quantity').val() || 0);

                    if (stock < qty) {
                        notify('error', `@lang('The stock is available ${stock}') ${$item.find('.unit-name').text()}`);
                        $item.find('.quantity').val(stock);
                        qty = stock;
                    }

                    const discountType = parseInt($item.find('.discount-type').val());
                    const discountValue = parseFloat($item.find(".discount-value").val() || 0);
                    const unitPrice = parseFloat($item.find(".unit-price").val() || 0);

                    var discountAmount = 0;

                    if (discountValue > 0) {
                        if (discountType == discountTypePercent) {
                            discountAmount = unitPrice / 100 * discountValue;
                        } else {
                            discountAmount = discountValue;
                        }
                    }

                    if (unitPrice < discountAmount) {
                        notify("error", "@lang('Discount value must be less than unit price')");
                        discountAmount = unitPrice;
                    }
                    const salePrice = unitPrice - discountAmount;
                    const singleSubTotal = parseFloat(salePrice) * parseFloat(qty);

                    subtotal += singleSubTotal;

                    $item.find('.sub-total').val(getAmount(singleSubTotal));
                    $item.find('.sale-price').val(getAmount(salePrice));
                });

                $('body').find('.summary-subtotal').text(showAmount(subtotal));

                calculateSummary();

            }

            function isSelected(selected) {
                return selected ? 'selected' : '';
            }

            $('.sale-discount-value,.sale-discount-type,[name=shipping_amount]').on('change input', function() {
                calculateSummary();
            });

            function calculateSummary() {
                const subtotal = parseFloat($('body').find(`.summary-subtotal`).text() || 0);
                const saleDiscountValue = parseFloat($('body').find(`.sale-discount-value`).val() || 0);
                const saleDiscountType = parseFloat($('body').find(`.sale-discount-type`).val() || 0);
                const shippingAmount = parseFloat($('body').find(`[name=shipping_amount]`).val() || 0);

                let saleDiscountAmount = 0;

                if (saleDiscountValue > 0 && subtotal > 0) {
                    if (saleDiscountType == discountTypePercent) {
                        saleDiscountAmount = subtotal / 100 * saleDiscountValue;
                    } else {
                        saleDiscountAmount = saleDiscountValue;
                    }
                }

                if (subtotal < saleDiscountAmount) {
                    notify("error", "@lang('The discount must be less than subtotal')");
                    saleDiscountAmount = subtotal;
                }

                const total = subtotal - saleDiscountAmount + shippingAmount;
                $('body').find('.summary-discount-amount').text(showAmount(saleDiscountAmount));
                $('body').find('.summary-shipping-amount').text(showAmount(shippingAmount));
                $('body').find('.summary-total').text(showAmount(total));
                $('body').find('.paid-amount').val(getAmount(total));
            }

            $(".date-picker").flatpickr({
                maxDate: new Date()
            });

            $("select[name=warehouse_id]").on('change', function(e) {
                selectedProductIds.length = 0;
                $('.product-table').find('tbody').html(htmlGenerateManager.emptyHtml());
                calculateAll();
            });


            $(`select[name=customer_id]`).select2({
                ajax: {
                    url: "{{ route('user.customer.lazy.loading') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 1000,
                    data: function(params) {
                        return {
                            search: params.term,
                            page: params.page,
                        };
                    },
                    processResults: function(response, params) {
                        params.page = params.page || 1;
                        let data = response.data.data;
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.mobile ? item.name + " - " + item.mobile : item.name,
                                    id: item.id
                                }
                            }),
                            pagination: {
                                more: response.more
                            }
                        };
                    },
                    cache: false,
                },
                dropdownParent: $('#customer-select2')
            });

            $('.add-customer').on('click', function() {
                $('#customer-modal').modal('show');
            });

            $('.customer-form').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData($(this)[0]);
                const action = $(this).attr('action');

                $.ajax({
                    type: "POST",
                    url: action,
                    data: formData,
                    processData: false,
                    contentType: false,
                    complete: function() {
                        $('.customer-form')
                            .find(`button[type=submit]`)
                            .attr('disabled', false)
                            .removeClass('disabled')
                            .html(
                                `<i class="fa-regular fa-paper-plane"></i> @lang('Submit')`)
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            notify('success', response.message);
                            $('#customer-modal').modal('hide');

                            $('#customer-select2')
                                .find('#select2-customer_id-container')
                                .text(response?.data?.customer?.name);
                            $('#customer-select2')
                                .find('select')
                                .html(
                                    `<option value="${response?.data?.customer?.id}" selected>${response?.data?.customer?.name}</option>`
                                );

                            $('.customer-form').trigger('reset');
                            $("body").find(`select[name=customer_id]`)
                                .val(response?.data?.customer?.id);
                        }
                    },
                    error: function(error) {
                        notify('error', error?.responseJSON?.message || "@lang('Something went wrong')")
                    }
                });
            });


            // ========== ENHANCED PRODUCT SEARCH ==========
            // Configuration & State
            const $searchInput = $(".product-search-input");
            const searchResultsSelector = ".product-search-results-enhanced";
            const rowHeight = 75; // Approx height for desktop table row
            const cardHeight = 150; // Approx height for mobile card
            const viewportHeight = 450; // Visible area height
            const bufferCount = 10; // Extra rows to render above/below
            
            let searchTimeout = null;
            let currentSearchRequest = null;
            let currentSelectedIndex = -1;
            let allProducts = [];
            let currentPage = 1;
            let hasMoreResults = true;
            let isLoadingMore = false;
            let lastScrollTop = 0;
            let lastSearchQuery = "";

            // Debounced search handler
            $searchInput.on("input", function (e) {
                clearTimeout(searchTimeout);
                const query = $(this).val();

                if (!query || query.length < 1) {
                    $(searchResultsSelector).addClass("d-none").empty();
                    lastSearchQuery = "";
                    return;
                }

                if (query === lastSearchQuery) return;

                if (
                    $('select[name=warehouse_id]').length &&
                    !$('select[name=warehouse_id]').val()
                ) {
                    notify("error", "@lang('Please select warehouse first')");
                    $('select[name=warehouse_id]').focus();
                    this.value = "";
                    return false;
                }

                searchTimeout = setTimeout(() => {
                    initiateNewSearch(query);
                }, 300);
            });

            function initiateNewSearch(query) {
                // Reset state for new search
                lastSearchQuery = query;
                currentPage = 1;
                allProducts = [];
                hasMoreResults = true;
                isLoadingMore = false;
                currentSelectedIndex = -1;
                
                // Abort previous request
                if (currentSearchRequest) {
                    currentSearchRequest.abort();
                }

                performEnhancedSearch(query, 1);
            }

            function performEnhancedSearch(query, page) {
                const action = "{{ route('user.sale.product.search.enhanced') }}";
                const $resultsElement = $(searchResultsSelector);
                const warehouseId = $('select[name=warehouse_id]').val();

                isLoadingMore = true;

                currentSearchRequest = $.ajax({
                    type: "GET",
                    url: action,
                    dataType: "json",
                    data: {
                        search: query,
                        warehouse_id: warehouseId,
                        page: page
                    },
                    beforeSend: function () {
                        if (page === 1) {
                            $resultsElement.removeClass("d-none").html(`
                                <div class="p-5 text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            `);
                        } else {
                            // Append loading more spinner
                            if (!$(".search-load-more").length) {
                                $resultsElement.append(`
                                    <div class="search-load-more">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                        <span class="ms-2">Loading more products...</span>
                                    </div>
                                `);
                            }
                        }
                    },
                    success: function (response) {
                        isLoadingMore = false;
                        if (response.status == "success") {
                            const newProducts = response.data.products || [];
                            const pagination = response.data.pagination;
                            
                            hasMoreResults = pagination.has_more;
                            
                            if (page === 1) {
                                if (newProducts.length <= 0) {
                                    $resultsElement.html(emptyResultHtml());
                                    return;
                                }
                                allProducts = newProducts;
                                renderEnhancedResultsContainer(allProducts);
                            } else {
                                $(".search-load-more").remove();
                                allProducts = allProducts.concat(newProducts);
                                updateEnhancedResults(allProducts);
                            }

                            // Auto-select on exact match (only for first page)
                            if (page === 1 && response.data.exact_match && newProducts.length === 1) {
                                $('.product-result-row').first().trigger('click');
                            }
                        }
                    },
                    error: function (xhr, status, error) {
                        if (status === 'abort') return;
                        isLoadingMore = false;
                        console.error('Search error:', error);
                        $resultsElement.html(errorResultHtml());
                    },
                    complete: function() {
                        currentSearchRequest = null;
                    }
                });
            }

            function renderEnhancedResultsContainer(products) {
                const $resultsElement = $(searchResultsSelector);
                const isMobile = window.innerWidth < 768;
                
                let containerHtml = "";
                if (isMobile) {
                    containerHtml = `
                        <div class="enhanced-search-cards" style="position: relative;">
                            <div class="virtual-scroll-spacer-top" style="height: 0px;"></div>
                            <div class="virtual-scroll-content"></div>
                            <div class="virtual-scroll-spacer-bottom" style="height: 0px;"></div>
                        </div>
                    `;
                } else {
                    containerHtml = `
                        <div class="table-responsive">
                            <table class="table table-hover enhanced-search-table mb-0" style="table-layout: fixed;">
                                <thead style="position: sticky; top: 0; background: #fff; z-index: 10;">
                                    <tr>
                                        <th style="width: 30%;">Product</th>
                                        <th style="width: 12%;" class="text-end">Price</th>
                                        <th style="width: 12%;" class="text-end">Cost</th>
                                        <th style="width: 12%;" class="text-end">Quantity</th>
                                        <th style="width: 17%;" class="text-end d-none d-md-table-cell">Last Purchase</th>
                                        <th style="width: 17%;" class="text-end d-none d-lg-table-cell">Last Sale</th>
                                    </tr>
                                </thead>
                                <tbody class="virtual-scroll-body">
                                </tbody>
                            </table>
                        </div>
                    `;
                }
                
                $resultsElement.html(containerHtml);
                updateEnhancedResults(products);
                
                // Add scroll event for infinite scroll and virtualization
                $resultsElement.off('scroll').on('scroll', function() {
                    handleResultsScroll($(this));
                });
            }

            function updateEnhancedResults(products) {
                const $resultsElement = $(searchResultsSelector);
                const scrollTop = $resultsElement.scrollTop();
                const isMobile = window.innerWidth < 768;
                const h = isMobile ? cardHeight : rowHeight;
                
                // Virtualization math
                const startNode = Math.max(0, Math.floor(scrollTop / h) - bufferCount);
                const endNode = Math.min(products.length, Math.floor((scrollTop + viewportHeight) / h) + bufferCount);
                
                const topPadding = startNode * h;
                const bottomPadding = (products.length - endNode) * h;
                
                if (isMobile) {
                    const $container = $resultsElement.find(".enhanced-search-cards");
                    $container.find(".virtual-scroll-spacer-top").height(topPadding);
                    $container.find(".virtual-scroll-spacer-bottom").height(bottomPadding);
                    
                    let html = "";
                    for (let i = startNode; i < endNode; i++) {
                        html += generateMobileCard(products[i], i);
                    }
                    $container.find(".virtual-scroll-content").html(html);
                } else {
                    const $tbody = $resultsElement.find(".virtual-scroll-body");
                    let html = `<tr class="virtual-scroll-spacer-top"><td colspan="6" style="padding: 0; border: 0; height: ${topPadding}px;"></td></tr>`;
                    
                    for (let i = startNode; i < endNode; i++) {
                        html += generateTableRow(products[i], i);
                    }
                    
                    html += `<tr class="virtual-scroll-spacer-bottom"><td colspan="6" style="padding: 0; border: 0; height: ${bottomPadding}px;"></td></tr>`;
                    $tbody.html(html);
                }
            }

            function handleResultsScroll($el) {
                const scrollTop = $el.scrollTop();
                const scrollHeight = $el[0].scrollHeight;
                const height = $el.height();

                // 1. Update virtualization
                updateEnhancedResults(allProducts);

                // 2. Infinite scroll detection
                if (hasMoreResults && !isLoadingMore) {
                    if (scrollTop + height > scrollHeight * 0.8) {
                        currentPage++;
                        performEnhancedSearch(lastSearchQuery, currentPage);
                    }
                }
            }

            function generateTableRow(product, index) {
                const currency = "{{ gs('cur_sym', getParentUser()->id) }}";
                return `
                    <tr class="product-result-row ${currentSelectedIndex === index ? 'highlighted' : ''}" data-index="${index}">
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="${product.image_src}" class="product-thumb" alt="${product.name}">
                                <div>
                                    <div class="product-name">${product.name}</div>
                                    <div class="product-sku text-muted">${product.sku}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-end fw-bold">${currency}${product.selling_price}</td>
                        <td class="text-end text-muted">${currency}${product.cost_price}</td>
                        <td class="text-end">${product.in_stock || 0} ${product.unit_name || ''}</td>
                        <td class="text-end text-muted small d-none d-md-table-cell">
                            ${product.last_purchase_date !== 'N/A' 
                                ? `${product.last_purchase_date}<br><span class="text-success">${currency}${product.last_purchase_price}</span>` 
                                : '<span class="text-muted">N/A</span>'}
                        </td>
                        <td class="text-end text-muted small d-none d-lg-table-cell">
                            ${product.last_sale_date !== 'N/A' 
                                ? `${product.last_sale_date}<br><span class="text-info">${currency}${product.last_sale_price}</span>` 
                                : '<span class="text-muted">N/A</span>'}
                        </td>
                    </tr>
                `;
            }

            function generateMobileCard(product, index) {
                const currency = "{{ gs('cur_sym', getParentUser()->id) }}";
                return `
                    <div class="enhanced-product-card product-result-row ${currentSelectedIndex === index ? 'highlighted' : ''}" data-index="${index}">
                        <div class="d-flex gap-3 mb-2">
                            <img src="${product.image_src}" class="product-thumb-mobile" alt="${product.name}">
                            <div class="flex-grow-1">
                                <div class="fw-bold">${product.name}</div>
                                <div class="text-muted small">${product.sku}</div>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="text-muted small">Price</div>
                                <div class="fw-bold">${currency}${product.selling_price}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small">Cost</div>
                                <div>${currency}${product.cost_price}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small">Quantity</div>
                                <div>${product.in_stock || 0} ${product.unit_name || ''}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small">Last Purchase</div>
                                <div class="small">${product.last_purchase_date !== 'N/A' ? product.last_purchase_date : 'N/A'}</div>
                            </div>
                        </div>
                    </div>
                `;
            }

            function emptyResultHtml() {
                return `
                    <div class="p-5 text-center">
                        <img src="{{ asset('assets/images/empty_box.png') }}" class="empty-message mb-3" alt="No results">
                        <h6>No Products Found</h6>
                        <p class="text-muted">No products match your search criteria</p>
                    </div>
                `;
            }

            function errorResultHtml() {
                return `
                    <div class="p-3 text-center text-danger">
                        <i class="las la-exclamation-triangle fs-1"></i>
                        <p>Error loading search results. Please try again.</p>
                    </div>
                `;
            }

            // Keyboard navigation for enhanced search
            $searchInput.on("keydown", function (e) {
                const $results = $(searchResultsSelector);
                if ($results.hasClass("d-none") || allProducts.length === 0) return;

                // Arrow Down
                if (e.keyCode === 40) {
                    e.preventDefault();
                    currentSelectedIndex = Math.min(currentSelectedIndex + 1, allProducts.length - 1);
                    ensureSelectedVisible();
                }
                // Arrow Up
                else if (e.keyCode === 38) {
                    e.preventDefault();
                    currentSelectedIndex = Math.max(currentSelectedIndex - 1, 0);
                    ensureSelectedVisible();
                }
                // Enter
                else if (e.keyCode === 13) {
                    e.preventDefault();
                    if (currentSelectedIndex >= 0 && currentSelectedIndex < allProducts.length) {
                        selectProduct(allProducts[currentSelectedIndex]);
                    }
                }
                // Escape
                else if (e.keyCode === 27) {
                    e.preventDefault();
                    $results.addClass("d-none").empty();
                    currentSelectedIndex = -1;
                }
            });

            function ensureSelectedVisible() {
                const $results = $(searchResultsSelector);
                const isMobile = window.innerWidth < 768;
                const h = isMobile ? cardHeight : rowHeight;
                
                const targetScrollTop = currentSelectedIndex * h;
                const currentScrollTop = $results.scrollTop();
                
                // If selected item is outside view, scroll to it
                if (targetScrollTop < currentScrollTop) {
                    $results.scrollTop(targetScrollTop);
                } else if (targetScrollTop + h > currentScrollTop + viewportHeight) {
                    $results.scrollTop(targetScrollTop - viewportHeight + h);
                }
                
                updateEnhancedResults(allProducts);
                highlightCurrentItem();
            }

            function highlightCurrentItem() {
                const $results = $(searchResultsSelector);
                $results.find(".product-result-row").removeClass("highlighted");
                $results.find(`.product-result-row[data-index="${currentSelectedIndex}"]`).addClass("highlighted");
            }

            // Centralized selection logic
            function selectProduct(product) {
                if (parseInt(product.in_stock || 0) <= 0) {
                    notify("error", `The product ${product.sku} is out of stock`);
                    $(searchResultsSelector).addClass("d-none").empty();
                    return;
                }

                let html = htmlGenerateManager.productHtml(product);

                if (html) {
                    $('.empty-message-row').remove();
                    $('.product-table').find('tbody').append(html);
                    calculateAll();
                }
                
                $searchInput.val("");
                $(searchResultsSelector).addClass("d-none").empty();
                currentSelectedIndex = -1;
                allProducts = [];
            }

            // Handle product selection click
            $("body").on("click", ".product-result-row", function () {
                const index = $(this).data("index");
                if (index !== undefined && allProducts[index]) {
                    selectProduct(allProducts[index]);
                }
            });


            // Close results when clicking outside
            $(document).on("click", function (e) {
                if (!$(e.target).closest('.form-group.position-relative').length) {
                    $(searchResultsSelector).addClass("d-none").empty();
                    currentSelectedIndex = -1;
                }
            });

        })(jQuery);
    </script>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/flatpickr.js') }}"></script>
@endpush



@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/global/css/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/ovopanel/css/invoice.css') }}">
@endpush


@push('breadcrumb-plugins')
    <x-staff_permission_check permission="view sale">
        <a class="btn btn--primary" href="{{ route('user.sale.list') }}">
            <i class="las la-list me-1"></i>@lang('Sale List')
        </a>
    </x-staff_permission_check>
@endpush

@push('style')
    <style>
        .product-image {
            max-width: 40px;
            border-radius: 5px;
        }

        /* Enhanced Product Search Styles */
        .product-search-results-enhanced {
            position: absolute;
            background: #fff;
            width: 100%;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            top: 60px;
            border-radius: 8px;
            max-height: 500px;
            overflow-y: auto;
            z-index: 999999;
            border: 1px solid #e0e0e0;
        }

        [data-theme=dark] .product-search-results-enhanced {
            background-color: hsl(var(--light));
            border-color: rgba(255, 255, 255, 0.1);
        }

        /* Table Styles */
        .enhanced-search-table {
            font-size: 14px;
        }

        .enhanced-search-table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 13px;
            padding: 12px 10px;
            border-bottom: 2px solid #dee2e6;
            white-space: nowrap;
        }

        [data-theme=dark] .enhanced-search-table thead th {
            background-color: hsl(var(--bg-color));
        }

        .enhanced-search-table tbody td {
            padding: 10px;
            vertical-align: middle;
        }

        .product-result-row {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .product-result-row:hover,
        .product-result-row.highlighted {
            background-color: #f0f7ff;
        }

        [data-theme=dark] .product-result-row:hover,
        [data-theme=dark] .product-result-row.highlighted {
            background-color: rgba(66, 153, 225, 0.1);
        }

        .product-thumb {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
        }

        .product-name {
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 2px;
        }

        [data-theme=dark] .product-name {
            color: hsl(var(--white));
        }

        .product-sku {
            font-size: 12px;
        }

        /* Mobile Card Styles */
        .enhanced-product-card {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s ease;
        }

        .enhanced-product-card:last-child {
            border-bottom: none;
        }

        .enhanced-product-card:hover,
        .enhanced-product-card.highlighted {
            background-color: #f0f7ff;
        }

        [data-theme=dark] .enhanced-product-card {
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }

        [data-theme=dark] .enhanced-product-card:hover,
        [data-theme=dark] .enhanced-product-card.highlighted {
            background-color: rgba(66, 153, 225, 0.1);
        }

        .product-thumb-mobile {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        /* Responsive adjustments */
        @media (max-width: 991px) {
            .product-search-results-enhanced {
                max-height: 400px;
            }
        }

        @media (max-width: 767px) {
            .product-search-results-enhanced {
                max-height: 350px;
                top: 55px;
            }
            
            .enhanced-product-card {
                font-size: 14px;
            }
        }

        /* Empty/Loading states */
        .empty-message {
            max-width: 120px;
            opacity: 0.5;
        }

        /* Scrollbar styling */
        .product-search-results-enhanced::-webkit-scrollbar {
            width: 8px;
        }

        .product-search-results-enhanced::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .product-search-results-enhanced::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .product-search-results-enhanced::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
@endpush
