<aside id="pos-sidebar" class="pos-sidebar">
    <div class="pos-sidebar-header">
        <button class="btn--close" type="button">
            <i class="las la-angle-double-left"></i>
        </button>
        <div class="pos-customer">
            <div class="pos-customer-field">
                <div class="pos-customer-field__icon">
                    <x-panel.svg.user />
                </div>
                <div class="position-relative" id="customer-select2">
                    <select class="form-control form--control" name="customer_id" form="pos-form">
                        <option value="1">@lang('Walk-in Customer')</option>
                    </select>
                </div>
            </div>
            <button class="pos-customer__btn add-customer" type="button">
                <i class="las la-plus"></i>
            </button>
        </div>
    </div>
    <form class="pos-sidebar-body product-cart-empty" id="pos-form">
        @csrf
        <input type="hidden" name="discount_value">
        <div class="pos-cart-table h-100">
            <div class="pos-cart-table__thead">
                <div class="pos-cart-table__tr">
                    <div class="pos-cart-table__th">@lang('Product')</div>
                    <div class="pos-cart-table__th">@lang('Quantity')</div>
                    <div class="pos-cart-table__th">@lang('Price')</div>
                    <div class="pos-cart-table__th">@lang('Subtotal')</div>
                    <div class="pos-cart-table__th">@lang('Action')</div>
                </div>
            </div>
            <div class="pos-cart-table__tbody">
                <div class="product-empty-message">
                    <div class="p-5 text-center">
                        <img src="{{ asset('assets/images/empty_box.png') }}" class="empty-message">
                        <span class="d-block">@lang('No product you are select')</span>
                        <span class="d-block fs-13 text-muted">@lang('There are no available data to display on this table at the moment.')</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="pos-sidebar-footer">
        <div class="pos-cart-summery">
            <ul class="pos-cart-summery-list">
                <li class="pos-cart-summery-list-item">
                    <div class="wrapper">
                        <span class="label">
                            (<i class="las la-info fs-14"></i>) @lang('Subtotal'):
                        </span>
                    </div>
                    <span class="value">
                        {{ gs('cur_sym',getParentUser()->id) }}<span class="summary-subtotal">@lang('0.00')</span>
                    </span>
                </li>
                <li class="pos-cart-summery-list-item">
                    <div class="wrapper">
                        <spa class="label">(-) @lang('Discount'):</spa>
                        <div class="d-flex align-items-center gap-2">
                            <button class="pos-cart-summery__info-btn summary-discount-btn" type="button">
                                <x-panel.svg.edit />
                            </button>
                        </div>
                    </div>
                    <span class="value">
                        {{ gs('cur_sym',getParentUser()->id) }}<span class="summary-discount">@lang('0.00')</span>
                    </span>
                </li>
                <li class="pos-cart-summery-list-item">
                    <div class="wrapper">
                        <spa class="label">(+) @lang('Shipping'):</spa>
                        <div class="d-flex align-items-center gap-2">
                            <button class="pos-cart-summery__info-btn summary-discount-btn" type="button">
                                <x-panel.svg.edit />
                            </button>
                        </div>
                    </div>
                    <span class="value">
                        {{ gs('cur_sym',getParentUser()->id) }}<span class="shipping-amount">@lang('0.00')</span>
                    </span>
                </li>
            </ul>
        </div>
    </div>
</aside>

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

<x-panel.ui.modal id="pricing-details-modal">
    <x-panel.ui.modal.header>
        <h4 class="modal-title">@lang('Product Price')</h4>
        <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
            <i class="las la-times"></i>
        </button>
    </x-panel.ui.modal.header>
    <x-panel.ui.modal.body>

    </x-panel.ui.modal.body>
</x-panel.ui.modal>

<x-panel.ui.modal id="discount-shipping-modal">
    <x-panel.ui.modal.header>
        <h4 class="modal-title">@lang('Discount & Shipping')</h4>
        <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
            <i class="las la-times"></i>
        </button>
    </x-panel.ui.modal.header>
    <x-panel.ui.modal.body>

        <div class="form-group">
            <label>@lang('Coupon')</label>
            <form class="coupon no-submit-loader">
                @csrf
                <div class="input-group input--group">
                    <input type="text" name="coupon" class="form-control coupon_code">&nbsp;
                    <button type="submit" class="btn btn--primary input-group-text"><i
                            class="las la-paper-plane"></i> @lang('Apply')</button>
                </div>
            </form>
        </div>

        <div class="form-group">
            <label>@lang('Discount')</label>
            <div class="input-group input--group">
                <span class="input-group-text">
                    <select class="border-0 bg-transparent p-0 discount-type summary-discount-type" form="pos-form"
                        name="discount_type">
                        <option value="{{ Status::DISCOUNT_PERCENT }}">
                            @lang('Percent')
                        </option>
                        <option value="{{ Status::DISCOUNT_FIXED }}">
                            @lang('Fixed')
                        </option>
                    </select>
                </span>
                <input type="number" step="any" class="form-control summary-discount-value" >
            </div>
        </div>
        <div class="form-group">
            <label>@lang('Shipping Amount')</label>
            <div class="input-group input--group">
                <span class="input-group-text">
                    {{ __(gs('cur_sym', getParentUser()->id)) }}
                </span>
                <input type="number" step="any" class="form-control shipping-amount" name="shipping_amount" value="0" form="pos-form">
            </div>
        </div>
        <div class="form-group">
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <button type="button" class="btn btn--secondary btn-large" data-bs-dismiss="modal">
                    <i class="fa-regular  fa-times-circle"></i> @lang('Close')
                </button>
                <button type="button" class="btn btn--primary btn-large update-discount-shipping">
                    <i class="fa-regular fa-check-circle"></i> @lang('Update')
                </button>
            </div>
        </div>

        <input type="hidden" name="coupon_id" class="coupon_id" form="pos-form">

    </x-panel.ui.modal.body>
</x-panel.ui.modal>

@push('script')
    <script>
        "use strict";
        (function($) {

            const $pricingDetailsModal = $("#pricing-details-modal");

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

            //product price click handler
            $('body').on('click', ".product-price", function() {
                const id = $(this).data('id');
                const action = "{{ route('user.pos.product.pricing.details', ':id') }}";
                const $modal = $("#pricing-details-modal");
                $.ajax({
                    type: "GET",
                    url: action.replace(":id", id),
                    success: function(response) {
                        if (response.status == 'success') {
                            $modal.find('.modal-body').html(response.data.html);
                            $modal.modal('show');
                        } else {
                            notify("error", "@lang('Something went wrong')");
                        }
                    }
                });
            });

            //details discount change handler
            $("#pricing-details-modal").on('change input keyup keypress', ".details-discount-value", function(e) {
                calculationDetailsSalePrice();

                //close modal and update discount if enter pressed
                if (e.which == 13) {
                    $("#pricing-details-modal").find('.update-discount').trigger('click')
                }
            });

            //details discount change handler
            $("#pricing-details-modal").on('change', ".details-discount-type", function() {
                calculationDetailsSalePrice();
            });

            //calculation the sale price
            function calculationDetailsSalePrice() {
                const discountTypePercent = parseInt("{{ Status::DISCOUNT_PERCENT }}");
                const discountType = parseInt($pricingDetailsModal.find('.details-discount-type').val());
                const discountValue = parseFloat($pricingDetailsModal.find(".details-discount-value").val() ||
                    0);

                const unitPrice = parseFloat($pricingDetailsModal.find(".details-unit-price").text() || 0);
                var discountAmount = 0;
                let isValid = true;

                if (discountValue > 0) {
                    if (discountType == discountTypePercent) {
                        discountAmount = unitPrice / 100 * discountValue;
                    } else {
                        discountAmount = discountValue;
                    }
                }

                if (unitPrice < discountAmount || discountValue < 0) {
                    isValid = false;
                    notify("error", "@lang('Discount value must be less than unit price and cannot be negative')");
                    discountAmount = Math.min(discountAmount, unitPrice);
                }

                const salePrice = Math.max(0, unitPrice - discountAmount);
                $pricingDetailsModal.find(".details-sale-price").text(showAmount(salePrice));

                // Enable/disable update button based on validity
                const $updateBtn = $pricingDetailsModal.find('.update-discount');
                if (isValid) {
                    $updateBtn.prop('disabled', false).removeClass('disabled');
                } else {
                    $updateBtn.prop('disabled', true).addClass('disabled');
                }
            }

            //details discount change handler
            $("#pricing-details-modal").on('click', ".update-discount", function() {
                // Prevent update if button is disabled (invalid discount)
                if ($(this).prop('disabled')) {
                    return false;
                }

                const discountType = $pricingDetailsModal.find('.details-discount-type').val();
                const discountValue = $pricingDetailsModal.find('.details-discount-value').val();
                const salePrice = $pricingDetailsModal.find('.details-sale-price').text();

                const productId = $(this).data('id');
                const $cartElement = $('.pos-cart-table__tbody')
                    .find(`.card-item-product-id-${productId}`);

                $("#pricing-details-modal").modal('hide');

                $cartElement.find('.cart-price').text(showAmount(salePrice));
                $cartElement.find('.cart-discount-type').val(discountType);
                $cartElement.find('.cart-discount-value').val(discountValue);
                window.calculateAll();
            });

            $('.pos-cart-table__tbody').on('change input', '.cart-qty', function() {
                const qty = parseFloat($(this).val());
                if (!qty || qty <= 0) {
                    $(this).val(0);
                }
                window.calculateAll();
            });

            $('.pos-cart-table__tbody').on('click', '.product-qty__increment', function() {
                const $qtyElement = $(this).parent().find('.cart-qty');
                $qtyElement.val(Math.max(0, parseFloat($qtyElement.val()) + 1));
                window.calculateAll();
            });

            $('.pos-cart-table__tbody').on('click', '.product-qty__decrement', function() {
                const $qtyElement = $(this).parent().find('.cart-qty');
                $qtyElement.val(Math.max(1, parseFloat($qtyElement.val()) - 1));
                window.calculateAll();
            });

            $('body').on('click', '.summary-discount-btn', function() {
                const $modal = $('#discount-shipping-modal');
                $modal.modal('show');
            });

            $('body').on('click', '.update-discount-shipping', function() {
                const $modal = $('#discount-shipping-modal');
                const discountTypePercent = parseInt("{{ Status::DISCOUNT_PERCENT }}");
                const discountType = parseInt($modal.find('.summary-discount-type').val());
                const discountValue = parseFloat($modal.find(".summary-discount-value").val() || 0);
                const shippingAmount = parseFloat($modal.find(".shipping-amount").val() || 0);
                const subtotal = parseFloat($('.pos-cart-summery').find(".summary-subtotal").text() || 0);

                let discountAmount = 0;

                if (discountValue > 0 && subtotal > 0) {
                    if (discountType == discountTypePercent) {
                        discountAmount = subtotal / 100 * discountValue;
                    } else {
                        discountAmount = discountValue;
                    }
                }

                $('body').find('.summary-discount').text(showAmount(discountAmount));
                $('#pos-form').find('input[name=discount_value]').val(discountValue);
                $('body').find('.shipping-amount').text(showAmount(shippingAmount));

                window.calculateAll();
                $modal.modal('hide');
            });

            $('body').on('click', '.remove-cart-item', function() {
                const productId = $(this).data('id');
                const findProductIdIndex = window.added_product_id.findIndex(windowProductId =>
                    windowProductId == productId);

                window.added_product_id.splice(findProductIdIndex, 1)

                $(this).closest('.card-single-item').remove();
                if ($('body').find('.card-single-item').length <= 0) {
                    $('.pos-cart-table__tbody').html(`
                        <div class="product-empty-message">
                            <div class="p-5 text-center">
                                <img src="{{ asset('assets/images/empty_box.png') }}" class="empty-message">
                                <span class="d-block">@lang('No product you are select')</span>
                                <span class="d-block fs-13 text-muted">@lang('There are no available data to display on this table at the moment.')</span>
                            </div>
                        </div>
                    `);
                    $('body').find('.pos-sidebar-body').addClass('product-cart-empty');
                }

                let productCount=parseInt($('body').find('.cart-count').text() || 0);
                if(productCount > 0){
                    $('body').find('.cart-count').text(productCount-1);
                }
                window.calculateAll();
            });

            $('#discount-shipping-modal').on('keypress keyup', 'input', function(e) {
                //close modal and update discount if enter pressed
                if (e.which == 13) {
                    $('#discount-shipping-modal').find('.update-discount-shipping').click();
                }
            });

            window.calculateAll = () => {
                const $cartItems = $(".pos-cart-table__tbody").find('.card-single-item');
                const discountAmount = parseFloat($('body').find('.summary-discount').text() || 0);
                const shippingAmount = parseFloat($('body').find('.shipping-amount').text() || 0);
                let subtotal = 0;

                $.each($cartItems, function(i, cartItem) {
                    const $cartItem = $(cartItem);
                    const stock = parseFloat($cartItem.find('.in-stock').text());
                    let qty = parseFloat($cartItem.find('.cart-qty').val());

                    if (stock < qty) {
                        notify('error', `@lang('The stock is available ${stock}') ${$cartItem.find('.unit-name').text()}`);
                        $cartItem.find('.cart-qty').val(stock);
                        qty = stock;
                    }
                    const price = parseFloat($cartItem.find('.cart-price').text());
                    const cartSubTotal = qty * price;
                    subtotal += cartSubTotal;
                    $cartItem.find('.cart-sub-total').text(showAmount(cartSubTotal));
                });

                const total = subtotal - discountAmount + shippingAmount;
                $('body').find('.summary-subtotal').text(showAmount(subtotal))
                $('body').find('.total-amount').text(showAmount(total))
            }


            // Coupon

            $('.coupon').on('submit', function(e) {
                e.preventDefault();
                const coupon_code = $('.coupon_code').val();
                const subtotal = parseFloat($('.pos-cart-summery').find(".summary-subtotal").text() || 0);

                $.ajax({
                    url: `{{ route('user.sale.apply.coupon') }}`,
                    type: 'POST',
                    data: {
                        coupon_code: coupon_code,
                        subtotal: subtotal,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status =='success') {
                            $('.summary-discount-value').val(response.amount);
                            if (response.discount_type == {{ Status::DISCOUNT_PERCENT }}) {
                                $('.summary-discount-type').val({{ Status::DISCOUNT_PERCENT }});
                            } else {
                                $('.summary-discount-type').val({{ Status::DISCOUNT_FIXED }});
                            }
                            $('.coupon_id').val(response.coupon_id);
                            notify('success', response.message);
                        } else {
                            notify('error', response.message);
                        }
                    }
                });
            });

        })(jQuery);
    </script>
@endpush
