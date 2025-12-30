@extends($activeTemplate . 'layouts.master')
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
                            <tr>
                                <td>
                                    <span class="d-block">${product.name}</span>
                                    <span class="d-block"><strong class="product-code">${productDetail.sku}</strong></span>
                                    <span class="d-block">
                                        @lang('In Stock'):
                                        <span class="in-stock">${product.in_stock}</span>
                                        <span class="unit-name">${product.unit_name}</span>
                                    </span>
                                    <input name="sale_details[${product.id}][product_id]" value="${productDetail.product_id}" type="hidden" />
                                    <input name="sale_details[${product.id}][product_detail_id]" value="${productDetail.id}" type="hidden" />

                                </td>
                                <td>
                                    <input value="${getAmount(productDetail.sale_price - productDetail.tax_amount)}"  readonly class="form-control"/>
                                </td>
                                <td>
                                    <input value="${getAmount(productDetail.tax_amount)} - ${getAmount(productDetail.tax_percentage)}%"  readonly class="form-control"/>
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
    </style>
@endpush
