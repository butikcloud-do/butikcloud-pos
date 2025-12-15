<ul class="product-search-list list-group list-group-flush d-none">
</ul>

@push('script')
    <script>
        "use strict";
        (function($) {
            const productTypeVariable = parseInt("{{ Status::PRODUCT_TYPE_VARIABLE }}");

            $(".product-search-input").on('input', function(e) {
                if ($('select[name=warehouse_id]').length && !$('select[name=warehouse_id]').val()) {
                    notify('error', "@lang('Please select warehouse first')");
                    $('select[name=warehouse_id]').focus();
                    this.value = "";
                    return false;
                }

                const searchValue = $(this).val();
                const action = "{{ route('user.product.search') }}";
                const $searchResultElement = $(".product-search-list");
                const $loadingElement = $searchResultElement.find(".product-search-list-loader");


                const emptyResult = `
                <li class="empty-result">
                    <div class="product-search-list-loader p-5 text-center">
                        <div class="d-flex flex-column align-items-center gap-3">
                            <div>
                                <img src="{{ asset('assets/images/empty_box.png') }}" class="empty-message">
                            </div>
                            <div>
                                <h6 class="mb-1">@lang('Empty Result')</h6>
                                <p>@lang('No products were found matching your search criteria')</p>
                            </div>
                        </div>
                    </div>
                </li>`;

                if (!searchValue && searchValue.length <= 2) {
                    $searchResultElement.addClass('d-none');
                    return;
                }

                $searchResultElement.removeClass('d-none');

                $.ajax({
                    type: "GET",
                    url: action,
                    dataType: "json",
                    data: {
                        search: searchValue,
                        warehouse_id: $('select[name=warehouse_id]').val()
                    },
                    beforeSend: function() {
                        $loadingElement.removeClass('d-none');
                    },
                    complete: function() {
                        $loadingElement.addClass('d-none');
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            const products = response.data.products || [];
                            if (products.length <= 0) {
                                $searchResultElement.html(emptyResult);
                                return;
                            }
                            let html = ``;
                            products.forEach(product => {
                                html += productHtml(product);
                            });
                            $searchResultElement.html(html);
                            removeSkeleton();

                            //automatically click when exact match
                            if (response.data.exact_match) {
                                $('.product-search-list')
                                    .find('.product-search-list-item')
                                    .first()
                                    .trigger('click')
                            }
                        }
                    }
                });
            });


            //type placeholder
            const $input = $('.product-search-input');
            const placeholderText = $input.attr('placeholder');
            let index = 0;

            function typePlaceholder() {
                $input.attr('placeholder', placeholderText.substring(0, index));
                index++;
                if (index > placeholderText.length) {
                    index = 0;
                    setTimeout(typePlaceholder, 1000);
                    return;
                }
                setTimeout(typePlaceholder, 150);
            }

            setTimeout(() => {
                typePlaceholder();
            }, 500);

            function removeSkeleton() {
                setTimeout(() => {
                    $('body').find('.skeleton').removeClass('skeleton');
                }, 500);
            }

            function productName(product, productDetails) {
                if (product.product_type == productTypeVariable) {
                    return `${product.name}-<strong>${productDetails?.attribute?.name}</strong>-<strong>${productDetails?.variant?.name}</strong>`;
                } else {
                    return product.name;
                }
            }

            function productHtml(product, productDetails) {
                return `
                    <li class="list-group-item product-search-list-item"
                    data-product='${JSON.stringify(product)}'>
                        <div class="d-flex gap-2">
                            <div class="product-search-thumb skeleton">
                                <img src="${product.image_src}">
                            </div>
                            <div class="product-search-content">
                                <p class="skeleton">
                                    ${product.name} ${product.attribute_name ?  `<strong> - ${product.attribute_name} -  ${product.variant_name}</strong>` : '' }
                                </p>
                                <p class="skeleton">
                                    <strong>${product.sku}</strong>
                                </p>
                            </div>
                        </div>
                    </li>
                `;
            }
        })(jQuery);
    </script>
@endpush


@push('style')
    <style>
        .product-search-list {
            position: absolute;
            background: #fff;
            width: 100%;
            box-shadow: var(--dashboard-boxshadow);
            top: 90px;
            border-radius: 5px;
            max-height: 400px;
            overflow-y: auto;
            z-index: 999999;
        }

        [data-theme=dark] .product-search-list {
            background-color: hsl(var(--light));
        }

        .product-search-list-item {
            cursor: pointer;
        }

        .product-search-list .product-search-thumb {
            max-width: 50px;
            border-radius: 10px;
        }

        .product-search-list .product-search-thumb img {
            border-radius: 5px;
        }

        .list-group-item.product-search-list-item {
            transition: 0.3s;
        }

        .list-group-item.product-search-list-item:hover {
            background: #eeeeee;
        }

        [data-theme=dark] .list-group-item.product-search-list-item:hover {
            background: hsl(var(--bg-color));
        }
    </style>
@endpush
