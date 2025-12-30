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
                                    value="{{ $sale->sale_date ?? date('Y-m-d') }}">
                            </div>
                            <div class="col-sm-6 form-group">
                                <label class="form-label">@lang('Customer')</label>
                                <select class="form-control form--control select2" name="customer_id"
                                    data-minimum-results-for-search="-1">
                                    <option value="{{ $sale->customer_id }}" selected>
                                        {{ __($sale->customer->name) }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label class="form-label">@lang('Warehouse')</label>
                                <select class="form-control select2" name="warehouse_id" required
                                    data-minimum-results-for-search="-1">
                                    <option value="{{ $sale->warehouse_id }}" selected>
                                        {{ __($sale->warehouse->name) }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label class="form-label">@lang('Status')</label>
                                <select class="form-control select2" name="status" data-minimum-results-for-search="-1"
                                    required>
                                    <option value="{{ Status::SALE_FINAL }}" @selected($sale->status == Status::SALE_FINAL)>
                                        @lang('Final')
                                    </option>
                                    <option value="{{ Status::SALE_QUOTATION }}" @selected($sale->status == Status::SALE_QUOTATION)>
                                        @lang('Quotation')
                                    </option>
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
                                    @foreach ($sale->saleDetails as $saleDetails)
                                        <tr>
                                            <td>
                                                <span class="d-block">
                                                    {{ strLimit(__(@$saleDetails->product->name), 10) }}

                                                </span>
                                                @if (@$saleDetails->product->product_type == Status::PRODUCT_TYPE_VARIABLE)
                                                    <span class="d-block">
                                                        - {{ __(@$saleDetails->productDetail->attribute->name) }}
                                                        - {{ __(@$saleDetails->productDetail->variant->name) }}
                                                    </span>
                                                @endif
                                                <span>{{ @$saleDetails->productDetail->sku }}</span>

                                                <span class="d-block">
                                                    @lang('In Stock'):
                                                    <span
                                                        class="in-stock">{{ @$saleDetails->productDetail->productStock->first()->stock }}</span>
                                                    <span
                                                        class="unit-name">{{ __(@$saleDetails->product->unit->name) }}</span>
                                                </span>
                                                <input
                                                    name="sale_details[{{ $saleDetails->product_details_id }}][product_id]"
                                                    value="{{ $saleDetails->product_id }}" type="hidden" />
                                                <input
                                                    name="sale_details[{{ $saleDetails->product_details_id }}][product_detail_id]"
                                                    value="{{ $saleDetails->product_details_id }}" type="hidden" />

                                                <input name="sale_details[{{ $saleDetails->product_details_id }}][id]"
                                                    value="{{ $saleDetails->id }}" type="hidden" />
                                            </td>
                                            <td>
                                                <input value="{{ getAmount($saleDetails->unit_price) }}" readonly
                                                    class="form-control" />
                                            </td>
                                            <td>
                                                <input value="{{ getAmount($saleDetails->tax_amount) }}" readonly
                                                    class="form-control" />
                                            </td>
                                            <td>
                                                <div class="input-group input--group">
                                                    <span class="input-group-text">
                                                        <select
                                                            name="sale_details[{{ $saleDetails->product_details_id }}][discount_type]"
                                                            class="border-0 bg-transparent p-0 discount-type">
                                                            <option value="{{ Status::DISCOUNT_PERCENT }}"
                                                                @selected($saleDetails->discount_type == Status::DISCOUNT_PERCENT)>
                                                                @lang('Percent')
                                                            </option>
                                                            <option value="{{ Status::DISCOUNT_FIXED }}"
                                                                @selected($saleDetails->discount_type == Status::DISCOUNT_FIXED)>
                                                                @lang('Fixed')
                                                            </option>
                                                        </select>
                                                    </span>
                                                    <input type="number" step="any"
                                                        class="form-control  discount-value"
                                                        name="sale_details[{{ $saleDetails->product_details_id }}][discount_value]"
                                                        value="{{ getAmount($saleDetails->discount_value) }}">
                                                </div>
                                            </td>
                                            <td>
                                                <input value="{{ getAmount($saleDetails->sale_price) }}" readonly
                                                    class="form-control sale-price" />
                                                <input type="hidden"
                                                    value="{{ getAmount($saleDetails->unit_price + $saleDetails->tax_amount) }}"
                                                    readonly class="form-control unit-price " />
                                            </td>
                                            <td>
                                                <div class="input-group input--group">
                                                    <input value="{{ $saleDetails->quantity }}" type="number"
                                                        step="any" class="form-control quantity"
                                                        name="sale_details[{{ $saleDetails->product_details_id }}][quantity]" />
                                                    <span
                                                        class="input-group-text">{{ __(@$saleDetails->product->unit->name) }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input value="{{ getAmount($saleDetails->subtotal) }}" readonly
                                                        class="form-control sub-total" />
                                                    <button type="button"
                                                        class="btn btn--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to remove this item')?"
                                                        data-action="{{ route('user.sale.remove.single.item', $saleDetails->id) }}">
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
            @php
                $paymentsCount = $sale->payments->count();
            @endphp
            <div class="col-lg-6">
                <x-panel.ui.card class="h-100">
                    <x-panel.ui.card.header>
                        <h4 class="card-title">@lang('Payment Information')</h4>
                    </x-panel.ui.card.header>
                    <x-panel.ui.card.body>
                        @foreach ($sale->payments as $payment)
                            <div class="mb-4">
                                <div class="row">
                                    <div class="form-group col-lg-6">
                                        <label>@lang('Paid Amount')</label>
                                        <div class="input-group input--group">
                                            <input type="number" step="any"
                                                class="form-control @if ($paymentsCount <= 1) paid-amount @endif"
                                                name="payment[{{ $loop->index }}][amount]"
                                                placeholder="@lang('0.00')" required
                                                value="{{ getAmount($payment->amount) }}" min="0">
                                            <input type="hidden" class="form-control"
                                                name="payment[{{ $loop->index }}][id]" value="{{ $payment->id }}">
                                            <span class="input-group-text">
                                                {{ __(gs('cur_text', getParentUser()->id)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <label>@lang('Payment Method')</label>
                                        <select name="payment[{{ $loop->index }}][payment_type]"
                                            class="form-control select2" required disabled>
                                            <option value="" selected disabled>@lang('Select Option')</option>
                                            @foreach ($paymentMethods as $paymentMethod)
                                                <option value="{{ $paymentMethod->id }}" @selected($payment->payment_type == $paymentMethod->id)>
                                                    {{ __($paymentMethod->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-12">
                                        <label>@lang('Payment Note')</label>
                                        <textarea class="form-control" name="payment[{{ $loop->index }}][note]">{{ $payment->note }}</textarea>
                                    </div>
                                </div>
                            </div>
                            @if (!$loop->last)
                                <hr>
                                <hr>
                            @endif
                        @endforeach
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
                                        <option value="{{ Status::DISCOUNT_FIXED }}" @selected($sale->discount_type == Status::DISCOUNT_FIXED)>
                                            @lang('Fixed')
                                        </option>
                                        <option value="{{ Status::DISCOUNT_PERCENT }}" @selected($sale->discount_type == Status::DISCOUNT_PERCENT)>
                                            @lang('Percent')
                                        </option>
                                    </select>
                                </span>
                                <input type="number" step="any" class="form-control sale-discount-value"
                                    placeholder="@lang('0.00')" name="discount_value"
                                    value="{{ getAmount($sale->discount_value) }}">
                                <span class="input-group-text fixed-percent-symbol">
                                    {{ __(gs('cur_text', getParentUser()->id)) }}
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Shipping Amount')</label>
                            <div class="input-group input--group">
                                <input type="number" step="any" class="form-control" name="shipping_amount"
                                    placeholder="@lang('0.00')" value="{{ getAmount($sale->shipping_amount) }}">
                                <span class="input-group-text">{{ __(gs('cur_text', getParentUser()->id)) }}</span>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                                <span>@lang('Subtotal')</span>
                                <span class="text--info">
                                    {{ gs('cur_sym', getParentUser()->id) }}<span class="summary-subtotal">
                                        {{ getAmount($sale->subtotal) }}
                                    </span>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                                <span>@lang('Discount Amount')</span>
                                <span class="text--success">
                                    {{ gs('cur_sym', getParentUser()->id) }}<span class="summary-discount-amount">
                                        {{ getAmount($sale->discount_amount) }}
                                    </span>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                                <span>@lang('Shipping Amount')</span>
                                <span class="text--warning">
                                    {{ gs('cur_sym', getParentUser()->id) }}<span class="summary-shipping-amount">
                                        {{ getAmount($sale->shipping_amount) }}
                                    </span>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                                <span>@lang('Total')</span>
                                <span class="text--info">
                                    {{ gs('cur_sym', getParentUser()->id) }}<span class="summary-total">
                                        {{ getAmount($sale->total) }}
                                    </span>
                                </span>
                            </li>
                        </ul>
                    </x-panel.ui.card.body>
                </x-panel.ui.card>
                <div class="d-flex gap-3 flex-wrap justify-content-end mt-3">
                    <button class="btn btn--success btn-large only-save" type="button">
                        <span class="me-1"><i class="fa-regular fa-paper-plane"></i></span>
                        @lang('Update')
                    </button>
                    <button class="btn btn--primary btn-large" type="submit">
                        <span class="me-1"><i class="fa fa-print"></i></span>
                        @lang('Update & Print')
                    </button>
                </div>
            </div>
        </div>
    </form>

    <x-confirmation-modal />
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {

            const selectedProductIds = @json($sale->saleDetails->pluck('product_details_id')->toArray());
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
                const $this = $(this);
                $.ajax({
                    url: "{{ route('user.sale.update', $sale->id) }}",
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
                        notify(resp.status, resp.message);
                        if (resp.status == 'success' && saveActionType == 'save_and_print') {
                            $('body').append(`<div class="print-content">${resp.data.html}</div>`);
                            window.print();
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
                                <input value="${getAmount(productDetail.sale_price - productDetail.tax_amount )}"  readonly class="form-control"/>
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
                                <input type="hidden" value="${getAmount(productDetail.sale_price)}"  readonly class="form-control unit-price"/>
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
    <a class="btn btn--primary" href="{{ route('user.sale.list') }}">
        <i class="las la-list me-1"></i>@lang('Sale List')
    </a>
@endpush

@push('style')
    <style>
        .product-image {
            max-width: 40px;
            border-radius: 5px;
        }
    </style>
@endpush
