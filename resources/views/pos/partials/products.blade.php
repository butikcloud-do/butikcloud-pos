<div class="pos-product-list"></div>
<div class="loading-container pt-4 mb-5 pb-5"></div>
@push('script')
    <script>
        "use strict";
        (function($) {

            //product click handler
            $('body').on('click', ".single-product-item", function() {

                const product = $(this).data('product');
                const productHtml = htmlManager.cartItemHtml(product);

                if (!product.in_stock || parseInt(product.in_stock) <= 0) {
                    notify('error', `The product ${product.sku} is out of stock`);
                    document.getElementById("failed-audio").play();
                    return false;
                }


                $(".pos-cart-table__tbody").find('.product-empty-message').remove();
                $('body').find('.pos-sidebar-body').removeClass('product-cart-empty');
                $(".pos-cart-table__tbody").prepend(productHtml);
                let productCount = parseInt($('body').find('.cart-count').text() || 0);
                $('body').find('.cart-count').text(productCount + 1);

                window.calculateAll();
            });


            const htmlManager = {
                /**
                 * Generates skeleton placeholder HTML for a loading state.
                 *
                 * - Displays a skeleton layout to simulate product elements while the actual data is being fetched.
                 * - The structure includes placeholders for product image, name, SKU, stock, and an add-to-cart button.
                 * - Useful for providing visual feedback during loading to enhance user experience.
                 */
                skeletonPlaceHolderHtml: function() {
                    const html = `
                        @foreach (range(1, 10) as $index => $value)
                            <div class="pos-product">
                                <div class="pos-product__thumb skeleton">
                                    <img src="" alt="">
                                </div>
                                <div class="pos-product__content">
                                    <h6 class="pos-product__title skeleton">@lang('Placeholder product name')</h6>
                                    <span class="pos-product__sku skeleton">@lang('SKU'): <span>@lang('Placeholder SKU')</span></span>
                                </div>
                                <p class="pos-product__stock skeleton">@lang('In stock'): <span>@lang('Placeholder STock')</span></p>
                                <button class="pos-product__add-btn skeleton" type="button">
                                    <i class="las la-plus"></i>
                                </button>
                            </div>
                        @endforeach`
                    return html;
                },
                /**
                 * Generates dynamic HTML for a product item.
                 *
                 * - Accepts a product object containing details like name, image source, SKU, stock, and optionally variants.
                 * - Dynamically renders the product information within a structured layout.
                 * - Includes conditional rendering for products with additional attributes (e.g., variants like size or color).
                 * - Provides an add-to-cart button for interaction.
                 *
                 * @param {Object} product - The product data to render.
                 * @returns {string} - The generated HTML string for the product item.
                 */
                productHtml: function(product) {
                    delete product.original['barcode_html'];
                    return `<div class="pos-product single-product-item ${product.in_stock == 0 ? 'stock-out': ''}" data-product='${JSON.stringify(product)}'>
                        <div class="pos-product__thumb">
                            <img src="${product.image_src}" alt="">
                        </div>
                        <div class="pos-product__content">
                            <h6 class="pos-product__title">${product.name.length > 17 ? product.name.substring(0, 17) + '...' : product.name}</h6>
                            <ul class="pos-product-attr">
                                <li class="pos-product-attr__item">
                                    <span class="pos-product__sku">@lang('SKU'): <span>${product.sku}</span></span>
                                </li>
                                ${product.product_type == 2 ? `<li class="pos-product-attr__item"><span class="pos-product__sku">${product.attribute_name}: <span>${product.variant_name}</span></span></li>` : ''}
                            </ul>
                        </div>
                        <p class="pos-product__stock d-flex justify-content-between flex-wrap gap-2">
                            <span>
                                ${ product.in_stock != 0 ? `@lang('In stock'): <span>${product.in_stock} ${product?.unit_name}</span>` : `<span class="text--danger fw-bold">@lang('Out of stock')</span>` }
                            </span>
                            <span>{{ gs('cur_sym', getParentUser()->id) }}${showAmount(product.price)}</span>
                        </p>
                        ${product.in_stock != 0 ? `<button class="pos-product__add-btn" type="button"><i class="las la-plus"></i></button>`: ``}
                    </div>`
                },
                /**
                 * Generates an empty state message when no products are available.
                 *
                 * - Displays a visually appealing placeholder message with an illustration.
                 * - Informs the user that no products are currently available in the list.
                 * - Provides additional context with a subtle description below the main message.
                 *
                 * @returns {string} - The HTML structure for the empty state message.
                 */
                productEmptyHtml: function() {
                    return `
                    <div class="product-empty-message d-flex justify-content-center text-center w-100">
                        <div class="p-5">
                            <img src="{{ asset('assets/images/empty_box.png') }}" class="empty-message">
                            <span class="d-block">@lang('No product found')</span>
                            <span class="d-block fs-13 text-muted">@lang('There are no available data to display on this table at the moment.')</span>
                        </div>
                    </div>`
                },

                /**
                 * Generate the cart item html when product listing clicked
                 *
                 * @returns {string} - The HTML structure for the empty state message.
                 */
                cartItemHtml: function(product) {
                    const productIds = window.added_product_id || [];
                    if (productIds.includes(product.id)) {
                        const $cartElement = $(".pos-cart-table__tbody").find(
                            `.card-item-product-id-${product.id}`);
                        $cartElement.find('.product-qty__increment').trigger('click');
                        return '';
                    };
                    window.added_product_id = [
                        ...productIds,
                        product.id
                    ];
                    document.getElementById("success-audio").play();
                    return `
                    <div class="pos-cart-table__tr card-single-item card-item-product-id-${product.id}">
                            <div class="pos-cart-table__td">
                                <div class="pos-cart-product">
                                    <div class="pos-cart-product__thumb">
                                        <img src="${product.image_src}">
                                        <input name="sale_details[${product.id}][product_id]" value="${product.original.product_id}" type="hidden" />
                                        <input name="sale_details[${product.id}][product_detail_id]" value="${product.id}" type="hidden" />
                                        <input name="sale_details[${product.id}][discount_type]" value="${product.original.discount_type}" type="hidden" class="cart-discount-type" />
                                        <input name="sale_details[${product.id}][discount_value]" value="${product.original.discount_value}" type="hidden" class="cart-discount-value" />
                                    </div>
                                    <div class="pos-cart-product__content">
                                        <h6 class="pos-cart-product__title">${product.name.length > 17 ? product.name.substring(0, 17) + '...' : product.name}</h6>
                                        <ul class="pos-cart-product-meta">
                                            <li class="pos-cart-product-meta__item">${product.sku}</li>
                                            ${product.attribute_name ? `<li class="pos-cart-product-meta__item">${product.attribute_name}: ${product.variant_name}</li>` : ''}
                                        </ul>
                                         <p class="pos-cart-product__stock">
                                            @lang('In Stock'):
                                            <span class="in-stock">${product.in_stock}</span>
                                            <span class="unit-name">${product.unit_name}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <div class="pos-cart-table__td">
                            <div class="h-100 d-flex justify-content-center align-items-center px-2">
                                <div class="product-qty">
                                    <button class="product-qty__decrement" type="button">
                                        <i class="las la-minus"></i>
                                    </button>
                                    <input class="product-qty__value cart-qty" type="number" value="1" name="sale_details[${product.id}][quantity]">
                                    <button class="product-qty__increment" type="button">
                                        <i class="las la-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="pos-cart-table__td">
                            <div class="h-100 d-flex justify-content-center align-items-center px-2">
                                <span class="pos-cart-product__subtotal cursor-pointer product-price" data-id="${product.id}">
                                    <u>{{ gs('cur_sym', getParentUser()->id) }}</u><u class="cart-price">${showAmount(product.price)}</u>
                                </span>
                            </div>
                        </div>
                        <div class="pos-cart-table__td">
                            <div class="h-100 d-flex justify-content-center align-items-center px-2">
                                <span class="pos-cart-product__subtotal">
                                    {{ gs('cur_sym', getParentUser()->id) }}<span class="cart-sub-total">${showAmount(product.price)}</span>
                                </span>
                            </div>
                        </div>
                        <div class="pos-cart-table__td">
                            <div class="h-100 d-flex justify-content-end align-items-center">
                                <button class="pos-cart-table__tr-close remove-cart-item" type="button" data-id="${product.id}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    `
                }
            }

            // Fetch the product list
            window.getProductList = (page = 1, append = false) => {

                $(".pos-product-list").find('.loading-spinner').remove();
                $.ajax({
                    type: "GET",
                    url: "{{ route('user.pos.product') }}",
                    dataType: "json",
                    data: {
                        category_id: (window.category_id || []).join(","),
                        brand_id: (window.brand_id || []).join(","),
                        warehouse_id: $("select[name=warehouse_id]").val(),
                        page: page,
                    },
                    beforeSend: function() {
                        if (append) {
                            $(".pos-product-list").append(
                                `<div class="loading-spinner col-12 text-center"><img src="{{ asset('assets/images/loadings.gif') }}" alt="Loading..."></div>`
                                );
                        } else {
                            $(".pos-product-list").html(htmlManager.skeletonPlaceHolderHtml());
                        }
                    },
                    success: function(response) {
                        if (response.status) {
                            let html = "";
                            if (
                                response.data &&
                                Array.isArray(response.data.products) &&
                                response.data.products.length > 0
                            ) {
                                $.each(response.data.products, function(i, product) {
                                    html += htmlManager.productHtml(product);
                                });
                                if (append) {
                                    $(".pos-product-list").append(html);
                                } else {
                                    $(".pos-product-list").html(html);
                                }
                            } else if (!append) {
                                $(".pos-product-list").html(htmlManager.productEmptyHtml());
                            }

                        } else {
                            notify("error", "Something went wrong");
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching products:", xhr.responseText);
                    },
                    complete: function() {
                        $(".pos-product-list .loading-spinner").remove();
                    },
                });
            };


            let loading = false;
            window.product_page = 1;

            $(".pos-section-body").on("scroll", function() {
                const $this = $(this);
                const scrollPosition = $this.scrollTop() + $this.innerHeight();
                const scrollHeight = $this[0].scrollHeight;

                if (scrollPosition >= scrollHeight - 100 && !loading) {
                    loading = true;
                    window.product_page += 1;

                    window.getProductList(window.product_page, true);
                    setTimeout(() => (loading = false), 1000);
                }
            });

            window.getProductList();

        })(jQuery);
    </script>
@endpush


@push('style')
    <style>
        .loading-spinner img {
            max-height: 100px;
        }
    </style>
@endpush
