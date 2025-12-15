<dialog class="pos-search position-relative">
    <button class="pos-search__search-btn" type="button">
        <i class="las la-search"></i>
    </button>
    <input class="pos-search__input product-search-input" type="text"
        placeholder="Enter Product Name / SKU / Scan Barcode">
    <a href="{{ route('user.product.create') }}" class="pos-search__add-btn" target="_blank">
        <i class="fas fa-plus"></i>
    </a>
    <x-panel.other.product_search />
</dialog>
<div class="pos-filter">
    <button class="pos-filter__btn category-btn" type="button">
        <x-panel.svg.category />
        <span>@lang('Category')</span>
    </button>
    <button class="pos-filter__btn brand-btn" type="button">
        <x-panel.svg.brand />
        <span>@lang('Brand')</span>
    </button>
</div>

<div class="offcanvas offcanvas-end filter--offcanvas" tabindex="-1" id="category-offcanvas">
    <div class="offcanvas-header gap-2">
        <h5 class="offcanvas-title">@lang('Filter Category')</h5>
        <button type="button" class="btn btn--danger ms-2" data-bs-dismiss="offcanvas">
            <i class="las la-times"></i> @lang('Close')
        </button>
    </div>
    <div class="offcanvas-body">
    </div>

    <div class="offcanvas-footer">
        <button class="w-100 pos-btn pos-btn--primary category-apply-btn" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-settings-2">
                <path d="M20 7h-9"></path>
                <path d="M14 17H5"></path>
                <circle cx="17" cy="17" r="3"></circle>
                <circle cx="7" cy="7" r="3"></circle>
            </svg>
            @lang('Apply Now')
        </button>
    </div>
</div>

<div class="offcanvas offcanvas-end filter--offcanvas" tabindex="-1" id="brand-offcanvas">
    <div class="offcanvas-header gap-2">
        <h5 class="offcanvas-title">@lang('Filter Brand')</h5>
        <button type="button" class="btn btn--danger ms-2" data-bs-dismiss="offcanvas">
            <i class="las la-times"></i> @lang('Close')
        </button>
    </div>
    <div class="offcanvas-body">
    </div>

    <div class="offcanvas-footer">
        <button class="w-100 pos-btn pos-btn--primary brand-apply-btn" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-settings-2">
                <path d="M20 7h-9"></path>
                <path d="M14 17H5"></path>
                <circle cx="17" cy="17" r="3"></circle>
                <circle cx="7" cy="7" r="3"></circle>
            </svg>
            @lang('Apply Now')
        </button>
    </div>
</div>

@push('script')
    <script>
        "use strict";
        (function($) {

            const catCanBas = new bootstrap.Offcanvas(document.getElementById('category-offcanvas'));
            const brandCanBas = new bootstrap.Offcanvas(document.getElementById('brand-offcanvas'));

            let categoryFetch = false;
            let brandFetch = false;

            //category btn click handler
            $('.category-btn').on('click', function() {
                catCanBas.show();
                if (!categoryFetch) {
                    ajaxManager.getCategoryList();
                }
            });

            //brand btn click handler
            $('.brand-btn').on('click', function() {
                brandCanBas.show();
                if (!brandFetch) {
                    ajaxManager.getBrandList();
                }
            });

            //single category btn handler
            $("body").on('click', ".single-category", function() {
                $(this).toggleClass('active');
            });

            //single category btn handler
            $("body").on('click', ".single-brand", function() {
                $(this).toggleClass('active');
            });

            //category apply btn click handler
            $('body').on('click', '.category-apply-btn', function() {
                const $selectedCategories = $("#category-offcanvas").find('.single-category.active');

                const categoryId = [];

                $.each($selectedCategories, function(i, selectedCategory) {
                    categoryId.push($(selectedCategory).data('id'));
                });


                window.category_id = categoryId;
                catCanBas.hide();
                window.product_page = 1;
                window.getProductList();
            });

            //brand apply btn click handler
            $('body').on('click', '.brand-apply-btn', function() {
                const $selectedBrands = $("#brand-offcanvas").find('.single-brand.active');

                const brandId = [];
                $.each($selectedBrands, function(i, selectedBrand) {
                    brandId.push($(selectedBrand).data('id'));
                });

                window.brand_id = brandId;
                brandCanBas.hide();
                window.product_page = 1;
                window.getProductList();
            });

            //event handler for product select
            $('body').on('click', ".product-search-list-item", function() {
                const product = $(this).data('product');

                if (!product.in_stock || parseInt(product.in_stock) <= 0) {
                    notify('error', `The product ${product.sku} is out of stock`, 'topLeft');
                    $(".product-search-list").empty().addClass('d-none');
                    document.getElementById("failed-audio").play();
                    return;
                }
                let html = htmlManager.cartItemHtml(product);
                $(".pos-cart-table__tbody").find('.product-empty-message').remove();
                $('body').find('.pos-sidebar-body').removeClass('product-cart-empty');
                $(".pos-cart-table__tbody").prepend(html);
                $(".product-search-list").empty().addClass('d-none');
                let productCount=parseInt($('body').find('.cart-count').text() || 0);
                $('body').find('.cart-count').text(productCount+1);

                window.calculateAll();
            });


            /**
             * Responsible for manage ajax request.
             *
             */
            const ajaxManager = {
                /**
                 * Fetches the list of categories via an AJAX GET request.
                 *
                 * - Displays a skeleton loader while the data is being fetched.
                 * - On successful response, dynamically renders the categories into the UI using `htmlManager.categoryHtml`.
                 * - Adds a slight delay to simulate content loading before displaying the categories.
                 * - If the request fails, displays an error notification.
                 */
                getCategoryList: function() {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('user.pos.category') }}",
                        beforeSend: function() {
                            htmlManager.skeletonLoaderHtml("#category-offcanvas");
                        },
                        success: function(response) {
                            if (response.status) {
                                let html = "";
                                categoryFetch = true;
                                $.each(response.data.categories, function(i, category) {
                                    html += htmlManager.categoryHtml(category);
                                });
                                setTimeout(() => {
                                    $('#category-offcanvas').find(".offcanvas-body").html(
                                        `<div class="category-container">${html}</div>`
                                    )
                                }, 1000);
                            } else {
                                notify('error', "@lang('Something went wrong'),'topLeft")
                            }
                        }
                    });
                },

                /**
                 * Fetches the list of brands via an AJAX GET request.
                 *
                 * - Displays a skeleton loader while the data is being fetched.
                 * - On successful response, dynamically renders the brans into the UI using `htmlManager.categoryHtml`.
                 * - Adds a slight delay to simulate content loading before displaying the brans.
                 * - If the request fails, displays an error notification.
                 */
                getBrandList: function() {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('user.pos.brand') }}",
                        beforeSend: function() {
                            htmlManager.skeletonLoaderHtml("#brand-offcanvas");
                        },
                        success: function(response) {
                            if (response.status) {
                                let html = "";
                                brandFetch = true;
                                $.each(response.data.brands, function(i, brand) {
                                    html += htmlManager.brandHtml(brand);
                                });
                                setTimeout(() => {
                                    $('#brand-offcanvas').find(".offcanvas-body").html(
                                        `<div class="brand-container">${html}</div>`
                                    )
                                }, 1000);
                            } else {
                                notify('error', "@lang('Something went wrong'),'topLeft")
                            }
                        }
                    });
                }
            }

            /**
             * Responsible for generate a html.
             *
             */
            const htmlManager = {
                /**
                 * Generates the HTML markup for a single category card.
                 *
                 * @param {Object} category - The category object containing `id`, `name`, and `image_src`.
                 * @returns {string} - The HTML string for a category card, including an image and name.
                 */
                categoryHtml: function(category) {
                    return `
                        <div class="single-category" data-id="${category.id}">
                            <img class="single-category__thumb" src="${category.image_src}">
                            <p class="single-category__name">${category.name}</p>
                        </div>`
                },
                /**
                 * Generates the HTML markup for a single brand card.
                 *
                 * @param {Object} brand - The brand object containing `id`, `name`, and `image_src`.
                 * @returns {string} - The HTML string for a brand card, including an image and name.
                 */
                brandHtml: function(brand) {
                    return `
                    <div class="single-brand" data-id="${brand.id}">
                        <img class="single-brand__thumb" src="${brand.image_src}">
                        <p class="single-brand__name">${brand.name}</p>
                    </div>`
                },
                /**
                 * Displays a skeleton loader in the UI to indicate loading state.
                 *
                 * - Generates a random number of skeleton elements (between 5 and 10) for variability.
                 * - Inserts the skeleton loader into the `$offCanvasBody` element.
                 */
                skeletonLoaderHtml: function(canvasId) {
                    let skeletonHtml = "";
                    const randomNumber = Math.floor(Math.random() * (10 - 5 + 1)) + 5;
                    for (let index = 0; index < randomNumber; index++) {
                        skeletonHtml += `<div class="single-skeleton skeleton"></div>`
                    }
                    $(canvasId).find('.offcanvas-body').html(
                        `<div class="skeleton-container">${skeletonHtml}</div>`
                    );
                },
                /**
                 * Generate the cart item html when product listing clicked
                 *
                 * @returns {string} - The HTML structure for the empty state message.
                 */
                cartItemHtml: function(product) {
                    const productIds = window.added_product_id || [];

                    if (productIds.includes(product.id)){
                        const  $cartElement= $(".pos-cart-table__tbody").find(`.card-item-product-id-${product.id}`);
                        $cartElement.find('.product-qty__increment').trigger('click');
                        return '';
                    };

                    window.added_product_id = [
                        ...productIds,
                        product.id
                    ];
                    document.getElementById("success-audio").play();
                    const productDetail = product.original;
                    return `
                    <div class="pos-cart-table__tr card-single-item card-item-product-id-${productDetail.id}">
                        <div class="pos-cart-table__td">
                                <div class="pos-cart-product">
                                    <div class="pos-cart-product__thumb">
                                        <img src="${product.image_src}">
                                        <input name="sale_details[${product.id}][product_id]" value="${productDetail.product_id}" type="hidden" />
                                        <input name="sale_details[${product.id}][product_detail_id]" value="${productDetail.id}" type="hidden" />
                                        <input name="sale_details[${product.id}][discount_type]" value="${productDetail.discount_type}" type="hidden" class="cart-discount-type" />
                                        <input name="sale_details[${product.id}][discount_value]" value="${productDetail.discount_value}" type="hidden" class="cart-discount-value" />
                                    </div>
                                    <div class="pos-cart-product__content">
                                        <h6 class="pos-cart-product__title">${product.name}</h6>
                                        <ul class="pos-cart-product-meta">
                                            <li class="pos-cart-product-meta__item">${productDetail.sku}</li>
                                            ${product.product_type == 2 ?
                                             `<li class="pos-cart-product-meta__item">
                                                            ${productDetail?.attribute?.name}: ${productDetail?.variant?.name}
                                                        </li>`
                                             : ''}
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
                            <div class="h-100 d-flex justify-content-end align-items-center px-2">
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
                            <div class="h-100 d-flex justify-content-end align-items-center px-2">
                                <span class="pos-cart-product__subtotal cursor-pointer product-price" data-id="${productDetail.id}">
                                    <u>{{ gs('cur_sym',getParentUser()->id) }}</u><u class="cart-price">${showAmount(productDetail.final_price)}</u>
                                </span>
                            </div>
                        </div>
                        <div class="pos-cart-table__td">
                            <div class="h-100 d-flex justify-content-end align-items-center px-2">
                                <span class="pos-cart-product__subtotal">
                                    {{ gs('cur_sym',getParentUser()->id) }}<span class="cart-sub-total">${showAmount(productDetail.final_price)}</span>
                                </span>
                            </div>
                        </div>
                        <div class="pos-cart-table__td">
                            <div class="h-100 d-flex justify-content-end align-items-center">
                                <button class="pos-cart-table__tr-close remove-cart-item" type="button" data-id="${productDetail.id}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    `
                }
            }
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>

    </style>
@endpush
