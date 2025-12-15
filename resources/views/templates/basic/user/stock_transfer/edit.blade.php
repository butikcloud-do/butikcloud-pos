@extends($activeTemplate . 'layouts.master')
@section('panel')
    <form method="POST" class="transfer-form no-submit-loader">
        @csrf
        <div class="row  responsive-row">
            <div class="col-12">
                <x-panel.ui.card>
                    <x-panel.ui.card.body>
                        <div class="row">
                            <div class="col-lg-4 col-sm-6 form-group">
                                <label class="form-label">@lang('Transfer Date')</label>
                                <input type="text" class="form-control date-picker" name="transfer_date" required
                                    value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="col-lg-4 col-sm-6 form-group">
                                <label class="form-label">@lang('From Warehouse')</label>
                                <select class="form-control select2 warehouse-dropdown from-warehouse" name="warehouse_id"
                                    required>
                                    <option value="" selected disabled>@lang('Select')</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" @selected($warehouse->id == $transfer->warehouse_id)>
                                            {{ __($warehouse->name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-4 col-sm-6 form-group">
                                <label class="form-label">@lang('To Warehouse')</label>
                                <select class="form-control select2 warehouse-dropdown to-warehouse" name="to_warehouse_id"
                                    required>
                                    <option value="" selected disabled>@lang('Select')</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" @selected($warehouse->id == $transfer->to_warehouse_id)>
                                            {{ __($warehouse->name) }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-lg-4 col-sm-6 form-group">
                                <label class="form-label">@lang('Status')</label>
                                <select class="form-control select2" name="status" data-minimum-results-for-search="-1"
                                    required>
                                    <option value="{{ Status::TRANSFER_SEND }}" @selected($transfer->status == Status::TRANSFER_SEND)>
                                        @lang('Sent')</option>
                                </select>
                            </div>
                            <div class="col-lg-4 col-sm-6 form-group">
                                <label class="form-label">@lang('Reference No')</label>
                                <input type="text" class="form-control" name="reference_no"
                                    placeholder="@lang('Reference No')"
                                    value="{{ old('reference_no', $transfer->reference_no) }}">
                            </div>
                            <div class="col-lg-4 col-sm-6 form-group">
                                <label class="form-label">@lang('Attachment/Document')</label>
                                <input type="file" class="form-control" name="attachment">
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
                                        <th class="w-50">@lang('Product')</th>
                                        <th>@lang('Quantity')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transfer->stockTransferDetails as $transferDetail)
                                        <tr>
                                            <td>
                                                <span class="d-block">{{ __(@$transferDetail->product->name) }}</span>
                                                <span class="d-block"><strong
                                                        class="product-code">{{ __(@$transferDetail->productDetail->sku) }}</strong></span>
                                                <span class="d-block">
                                                    @lang('In Stock'):
                                                    <span
                                                        class="in-stock">{{ @$transferDetail->productDetail->productStock->first()->stock }}</span>
                                                    <span
                                                        class="unit-name">{{ $transferDetail->productDetail->product->unit->short_name }}</span>
                                                </span>
                                                <input
                                                    name="stock_transfer[{{ $transferDetail->product_details_id }}][product_id]"
                                                    value="{{ $transferDetail->product_id }}" type="hidden" />
                                                <input
                                                    name="stock_transfer[{{ $transferDetail->product_details_id }}][product_details_id]"
                                                    value="{{ $transferDetail->product_details_id }}" type="hidden" />
                                            </td>
                                            <td>
                                                <div class="input-group input--group">
                                                    <input value="{{ $transferDetail->quantity }}" type="number"
                                                        step="any" class="form-control quantity"
                                                        name="stock_transfer[{{ $transferDetail->product_details_id }}][quantity]" />
                                                    <span
                                                        class="input-group-text">{{ __(@$transferDetail->productDetail->product->unit->short_name) }}</span>
                                                </div>
                                            </td>
                                    @endforeach
                                </tbody>
                            </table>
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
                </div>
            </div>
        </div>
    </form>
@endsection




@push('script')
    <script>
        "use strict";
        (function($) {

            const selectedProductIds = [];
            const $productTableElement = $('.product-table');

            let saveActionType = 'save_and_print';

            //event handler for base price and more input filed change
            $productTableElement.on('change input', '.quantity', function() {
                calculateAll();
            });

            $('.only-save').on('click', function() {
                saveActionType = "only_save";
                $(".transfer-form").submit();
            });

            $(window).on('afterprint', function() {
                saveActionType = "save_and_print";
                $('body').find('.print-content').remove();
            });

            //form submit handler
            $(".transfer-form").on('submit', function(e) {
                e.preventDefault();


                const fromWarehouse = $('.from-warehouse').val();
                const toWarehouse = $('.to-warehouse').val();


                if (!fromWarehouse || !toWarehouse) {
                    notify('error', '@lang('Please select both From Warehouse and To Warehouse.')');
                    return;
                }

                const formData = new FormData($(this)[0])
                const $this = $(this);

                $.ajax({
                    url: "{{ route('user.stock.transfer.update', $transfer->id) }}",
                    method: "POST",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $(".transfer-form").find(`button`).addClass('disabled').attr(`disabled`,
                            true);
                    },
                    complete: function() {
                        $(".transfer-form").find(`button`).removeClass('disabled').attr(`disabled`,
                            false);
                    },
                    success: function(resp) {
                        notify(resp.status, resp.message);
                        if (resp.status == 'success') {
                            selectedProductIds.length = 0;
                            calculateAll();
                            setTimeout(function() {
                                window.location.reload();
                            }, 1500);
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
                $(this).closest('tr').remove();
                if ($('.product-table tbody tr').length === 0) {
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
                                <input name="stock_transfer[${product.id}][product_id]" value="${productDetail.product_id}" type="hidden" />
                                <input name="stock_transfer[${product.id}][product_details_id]" value="${productDetail.id}" type="hidden" />

                            </td>


                             <td>
                                <div class="input-group input--group">
                                    <input value="1"  type="number" step="any"  class="form-control quantity" name="stock_transfer[${productDetail.id}][quantity]"/>
                                    <span class="input-group-text">${product.unit_name}</span>
                                </div>
                            </td>

                            <td>
                                 <span class="input-group-text btn btn--danger remove-btn" data-id="${productDetail.id}">
                                        <i class="las la-times"></i>
                                    </span>
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
                });


            }

            function isSelected(selected) {
                return selected ? 'selected' : '';
            }


            $(".date-picker").flatpickr({
                maxDate: new Date()
            });

            $("select[name=warehouse_id]").on('change', function(e) {
                selectedProductIds.length = 0;
                $('.product-table').find('tbody').html(htmlGenerateManager.emptyHtml());
                calculateAll();
            });


            $('.warehouse-dropdown').on('change', function() {
                const selectedFrom = $('.from-warehouse').val();
                const selectedTo = $('.to-warehouse').val();
                $('.warehouse-dropdown option').prop('disabled', false);
                if (selectedFrom) {
                    $('.to-warehouse option').each(function() {
                        if ($(this).val() === selectedFrom) {
                            $(this).prop('disabled', true);
                        }
                    });
                }
                if (selectedTo) {
                    $('.from-warehouse option').each(function() {
                        if ($(this).val() === selectedTo) {
                            $(this).prop('disabled', true);
                        }
                    });
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
@endpush


@push('breadcrumb-plugins')
    <x-staff_permission_check permission="view sale">
        <a class="btn btn--primary" href="{{ route('user.stock.transfer.list') }}">
            <i class="las la-list me-1"></i>@lang('Transfer List')
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
