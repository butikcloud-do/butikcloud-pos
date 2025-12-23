@extends($activeTemplate . 'layouts.master')
@section('panel')
<div class="row gy-4">
    <div class="col-lg-12">
        <x-panel.ui.card>
            <x-panel.ui.card.body>
                <div class="form-group position-relative">
                    <label class="form-label">@lang('Search Product')</label>
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
    <div class="col-lg-8">
        <x-panel.ui.card class="h-100">
            <x-panel.ui.card.header>
                <h4 class="card-title">@lang('Selected Product')</h4>
            </x-panel.ui.card.header>
            <x-panel.ui.card.body class="p-0">
                <x-panel.ui.table class="product-table">
                    <x-panel.ui.table.header>
                        <tr>
                            <th>@lang('Product')</th>
                            <th>@lang('Quantity')</th>
                        </tr>
                    </x-panel.ui.table.header>
                    <x-panel.ui.table.body>
                        <x-panel.ui.table.empty_message />
                    </x-panel.ui.table.body>
                </x-panel.ui.table>
            </x-panel.ui.card.body>
        </x-panel.ui.card>
    </div>
    <div class="col-lg-4">
        <x-panel.ui.card class="h-100">
            <x-panel.ui.card.header>
                <h4 class="card-title">@lang('Barcode Setting')</h4>
            </x-panel.ui.card.header>
            <x-panel.ui.card.body>
                <div class="form-group">
                    <label class="form-label">
                        @lang('Page Size')
                    </label>
                    <select class="form-control form-select select2 per-page" data-minimum-results-for-search="-1">
                        <option value="twenty-label" data-per-page="20" data-bar-width="2">@lang('20 Labels per Sheet')
                        </option>
                        <option value="thirty-label" data-per-page="30" data-bar-width="1">@lang('30 Labels per Sheet')
                        </option>
                        <option value="thirty-two-label" data-per-page="32" data-bar-width="1">@lang('32 Labels per
                            Sheet')
                        </option>
                        <option value="forty-label" data-per-page="40" data-bar-width="1">@lang('40 Labels per Sheet')
                        </option>
                    </select>
                </div>
                <div
                    class="form-check form-switch form--switch form-switch-success d-flex gap-2 align-items-center ps-0 mb-2">
                    <input class="form-check-input" type="checkbox" role="switch" id="product_name" checked>
                    <label class="form-check-label" for="product_name">
                        @lang('Product Name')
                    </label>
                </div>
                <div
                    class="form-check form-switch form--switch form-switch-success d-flex gap-2 align-items-center ps-0 mb-2">
                    <input class="form-check-input" type="checkbox" role="switch" id="product_variation" checked>
                    <label class="form-check-label" for="product_variation">
                        @lang('Product Variation')
                    </label>
                </div>
                <div
                    class="form-check form-switch form--switch form-switch-success d-flex gap-2 align-items-center ps-0 mb-2">
                    <input class="form-check-input" type="checkbox" role="switch" id="product_price" checked>
                    <label class="form-check-label" for="product_price">
                        @lang('Product Price')
                    </label>
                </div>
                <button class="btn btn--primary btn-large printBtn">
                    <i class="las la-print"></i> @lang('Print')
                </button>
            </x-panel.ui.card.body>
        </x-panel.ui.card>
    </div>
    <div class="col-12">
        <div class="a4-page d-none">
            <div class="barcode-wrapper d-flex flex-wrap  twenty-label justify-content-center">
            </div>
        </div>
    </div>
</div>
@endsection


@push('script')
<script>
    "use strict";
        (function($) {

            const selectedProductIds = [];
            const $perPageSelectElement = $('.per-page');
            const $productTableElement = $('.product-table');
            const $a4PageElement = $('.a4-page');
            const $barcodeWrapperElement = $a4PageElement.find('.barcode-wrapper');
            const $barcodeItemElement = $barcodeWrapperElement.find('.barcode-item');


            //event handlers for product select
            $('body').on('click', ".product-search-list-item", function() {
                const product = $(this).data('product');
                const html = htmlGenerateManager.productHtml(product);

                $('.empty-message-row').remove();
                $('.product-table').find('tbody').append(html);
                $(".product-search-list").empty().addClass('d-none');
                generateBarcode();
            });

            //event handlers for change per page item
            $perPageSelectElement.on('change', function() {
                const value = $(this).val();


                $barcodeWrapperElement.attr('class', '').addClass(
                    `barcode-wrapper d-flex flex-wrap justify-content-center ${value}`);
                reArrangePageBreak();
            });

            //event handlers for display product name
            $('#product_name').on('change', function() {
                const checked = $(this).is(':checked');
                if (checked) {
                    $barcodeWrapperElement.find('.barcode-product-name').removeClass('d-none');
                } else {
                    $barcodeWrapperElement.find('.barcode-product-name').addClass('d-none');
                }
            });

            //event handlers for display product price
            $('#product_price').on('change', function() {
                const checked = $(this).is(':checked');
                if (checked) {
                    $barcodeWrapperElement.find('.barcode-price').removeClass('d-none');
                } else {
                    $barcodeWrapperElement.find('.barcode-price').addClass('d-none');
                }
            });

            // event handlers for display product variation
            $('#product_variation').on('change', function() {
                const checked = $(this).is(':checked');
                if (checked) {
                    $barcodeWrapperElement.find('.barcode-variant-name').removeClass('d-none');
                } else {
                    $barcodeWrapperElement.find('.barcode-variant-name').addClass('d-none');
                }
            });

            //print event handler
            $('.printBtn').on('click', function() {
                if ($barcodeWrapperElement.find('.barcode-item').length <= 0) {
                    notify('warning', "@lang('Please select some product first')");
                    return
                }
                $('body').append(`
                    <div class="print-content">
                        <div class="a4-page">${$a4PageElement.html()} </div>
                    </div>
                `)
                window.print();
            });

            $(window).on('afterprint', function() {
                $('body').find('.print-content').remove();
            });

            //event handlers for change product quantity
            $productTableElement.on('input change', ".quantity", function() {
                generateBarcode();
            });

            //generate barcode
            function generateBarcode() {
                const $productElements = $productTableElement.find('tbody tr');
                let html = '';
                const perPage = parseInt($perPageSelectElement.find('option:selected').data('per-page') || 20);
                $.each($productElements, function(i, element) {
                    const $productElement = $(element);
                    const productName = $productElement.find('.product-name').text().substring(0, 20);
                    const productCode = $productElement.find('.product-code').text();
                    const variantName = $productElement.find('.product-variant').text();
                    const qty = $productElement.find('.quantity').val() || 1;
                    const price = $productElement.find('.quantity').data('price');
                    for (let index = 1; index <= qty; index++) {
                        html += htmlGenerateManager.barcodeHtml(productName, productCode, variantName, price, $productElement);
                    }
                });
                $a4PageElement.removeClass('d-none');
                $barcodeWrapperElement.empty().append(html);

                reArrangePageBreak();

            }


            //re arrange page break element when per page change
           function reArrangePageBreak() {
                $barcodeWrapperElement.find('.page-break').remove();
                const perPage = parseInt($perPageSelectElement.find('option:selected').data('per-page') || 20);
                const $barcodeElements = $barcodeWrapperElement.find('.barcode-item');
                let totalPage=2;

                // Loop through and add page breaks
                for (let i = perPage; i < $barcodeElements.length; i += perPage) {

                    $barcodeElements.eq(i - 1).after(htmlGenerateManager.pageBreakHtml(totalPage));
                    totalPage++;
                }
            }

            const htmlGenerateManager = {
                /**
                 * Generates an HTML element for a page break.
                 *
                 * @returns {string} A `<div>` element with the class `page-break`.
                 */
                pageBreakHtml: function(pageNumber) {
                    return `<div class="page-break w-100 position-relative"> <br/> <h5 class="text-center py-3 divider-title">PAGE - ${pageNumber}</h5></div>`;
                },
                /**
                 * Generates HTML for a barcode item with product details.
                 *
                 * @param {string} productName - The name of the product.
                 * @param {string} productCode - A unique code for the product (e.g., SKU or barcode).
                 * @param {string} variantName - The name of the product variant (if any).
                 * @param {string} price - The price of the product.
                 * @returns {string} A structured HTML template for displaying barcode information.
                 */
                barcodeHtml: function(productName, productCode, variantName, price, $productElement) {
                    return `
                    <div class="barcode-item">
                        <div class="barcode-item-content">
                            <div class="barcode-item-top-content">
                                <span class="barcode-product-name">
                                ${productName}
                                </span>
                                ${variantName ? `<span class="barcode-variant-name"> ${variantName}</span>` : '' }
                            </div>
                            <div class="barcode-item-middle-content ">
                                <div class="barcode-item-thumb">
                                  <div class="px-2">
                                    ${$productElement.find('.product-barcode-html').html()}
                                 </div>
                                 <span class="fw-bold mt-2">${productCode}</span>
                                </div>
                            </div>
                            <div class="barcode-item-bottom-content ">
                                <span class="barcode-price d-block">{{ gs('cur_sym',getParentUser()->id) }}${showAmount(price)}</span>
                            </div>
                        </div>
                    </div>
                `
                },
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
                    const productDetail = product.original;
                    selectedProductIds.push(product.id);
                    return `
                        <tr>
                            <td>
                                <div class="d-none product-barcode-html">${productDetail.barcode_html}</div>
                                <div class="d-flex gap-2">
                                    <img class="product-image" src="${product.image_src}">
                                    <div>
                                        <span class="d-block">
                                            <span class="product-name">${product.name}</span>
                                            ${product.attribute_name ? `<span class="fw-bold product-variant">${product.attribute_name} - ${product.variant_name} </span>` : ''}
                                        </span>
                                        <span class="d-block"><strong class="product-code">${productDetail.sku}</strong></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <input class="form-control quantity" value="1" data-price="${productDetail.final_price}" type="number" name="product[${productDetail.id}]">
                            </td>
                        </tr>`
                }

            }

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
                            generateBarcode();
                        }
                    }
                });
            @endif
        })(jQuery);
</script>
@endpush

@push('breadcrumb-plugins')
<x-staff_permission_check permission="view product">
    <a class="btn  btn-outline--primary" href="{{ route('user.product.list') }}">
        <i class="las la-list me-1"></i>@lang('Product List')
    </a>
</x-staff_permission_check>
@endpush

@push('style')
<style>
    .product-image {
        max-width: 60px;
        border-radius: 5px;
        max-width: 50px;
        max-height: 50px;
    }

    .barcode-item {
        text-align: center;
        overflow: hidden;
        border: 1px dashed #00000040;
    }

    .twenty-label .barcode-item {
        width: 4in;
        height: 1.17in;
    }

    .thirty-label .barcode-item {
        width: 2.65in;
        height: 1.16in;
    }

    .thirty-two-label .barcode-item {
        width: 2in;
        height: 1.45in;
    }

    .forty-label .barcode-item {
        width: 2in;
        height: 1.16in;
    }

    .barcode-item-content {
        display: flex;
        font-size: 12px;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        height: 100%;
        padding: 5px;
    }

    .barcode-item-top-content,
    .barcode-item-middle-content {
        margin-bottom: 3px;
    }

    .barcode-item span {
        line-height: 1;
    }

    .barcode-product-code {
        font-weight: 600;
        font-size: 14px;
        margin-top: 2px;
    }

    .barcode-product-name {
        font-size: 11px;
    }

    .a4-page:empty {
        display: none;
    }

    .a4-page {
        width: 8.27in;
        min-height: 11.69in;
        height: auto;
        padding-top: 0.6rem;
    }

    .a4-page h5 {
        background-color: hsl(var(--bg-color));
    }

    .divider-title::after {
        position: absolute;
        content: '';
        top: 52px;
        left: 250px;
        background: #6b6b6b65;
        height: 2px;
        width: 80px;
    }


    .divider-title::before {
        position: absolute;
        content: '';
        top: 52px;
        right: 250px;
        background: #6b6b6b65;
        height: 2px;
        width: 80px;
    }

    @media print {
        .page-break>* {
            display: none;
        }

        .a4-page {
            height: 11.69in;
            padding-top: unset;
        }
    }
</style>
@endpush