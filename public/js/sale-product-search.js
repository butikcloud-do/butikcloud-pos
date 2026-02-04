"use strict";
(function ($) {
    // Enhanced product search for sale/add page only
    const $searchInput = $(".product-search-input");
    const searchResultsSelector = ".product-search-results-enhanced";
    let searchTimeout = null;
    let currentSelectedIndex = -1;

    // Debounced search handler
    $searchInput.on("input", function (e) {
        clearTimeout(searchTimeout);
        const searchValue = $(this).val();

        if (!searchValue || searchValue.length < 2) {
            $(searchResultsSelector).addClass("d-none").empty();
            return;
        }

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
            performEnhancedSearch(searchValue);
        }, 300);
    });

    function performEnhancedSearch(query) {
        const action = "{{ route('user.sale.product.search.enhanced') }}";
        const $resultsElement = $(searchResultsSelector);
        const warehouseId = $('select[name=warehouse_id]').val();

        $.ajax({
            type: "GET",
            url: action,
            dataType: "json",
            data: {
                search: query,
                warehouse_id: warehouseId,
            },
            beforeSend: function () {
                $resultsElement.removeClass("d-none").html(`
                    <div class="p-5 text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
            },
            success: function (response) {
                if (response.status == "success") {
                    const products = response.data.products || [];
                    if (products.length <= 0) {
                        $resultsElement.html(emptyResultHtml());
                        return;
                    }
                    renderEnhancedResults(products);

                    // Auto-select on exact match
                    if (response.data.exact_match && products.length === 1) {
                        $('.product-result-row').first().trigger('click');
                    }
                }
            },
            error: function () {
                $resultsElement.html(errorResultHtml());
            },
        });
    }

    function renderEnhancedResults(products) {
        const $resultsElement = $(searchResultsSelector);
        const isMobile = window.innerWidth < 768;
        
        if (isMobile) {
            // Render mobile cards
            let html = '<div class="enhanced-search-cards">';
            products.forEach((product, index) => {
                html += generateMobileCard(product, index);
            });
            html += '</div>';
            $resultsElement.html(html);
        } else {
            // Render desktop table
            let html = `
                <div class="table-responsive">
                    <table class="table table-hover enhanced-search-table mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Cost</th>
                                <th class="text-end">Quantity</th>
                                <th class="text-end d-none d-md-table-cell">Last Purchase</th>
                                <th class="text-end d-none d-lg-table-cell">Last Order</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            products.forEach((product, index) => {
                html += generateTableRow(product, index);
            });
            html += '</tbody></table></div>';
            $resultsElement.html(html);
        }
        
        currentSelectedIndex = -1;
    }

    function generateTableRow(product, index) {
        const currency = "{{ gs('cur_sym', getParentUser()->id) }}";
        return `
            <tr class="product-result-row" data-index="${index}" data-product='${JSON.stringify(product)}'>
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
            <div class="enhanced-product-card product-result-row" data-index="${index}" data-product='${JSON.stringify(product)}'>
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

    // Handle product selection
    $("body").on("click", ".product-result-row", function () {
        const product = $(this).data("product");
        
        if (parseInt(product.in_stock || 0) <= 0) {
            notify("error", `The product ${product.sku} is out of stock`);
            $(searchResultsSelector).addClass("d-none").empty();
            return;
        }

        // Trigger existing product selection logic
        // This simulates clicking on the original search result
        const $fakeListItem = $('<div class="product-search-list-item"></div>');
        $fakeListItem.data('product', product);
        $fakeListItem.trigger('click');
        
        // Clear search
        $searchInput.val("");
        $(searchResultsSelector).addClass("d-none").empty();
        currentSelectedIndex = -1;
    });

    // Keyboard navigation
    $searchInput.on("keydown", function (e) {
        const $results = $(searchResultsSelector);
        const $items = $results.find(".product-result-row");
        
        if ($items.length === 0) return;

        // Arrow Down
        if (e.keyCode === 40) {
            e.preventDefault();
            currentSelectedIndex = Math.min(currentSelectedIndex + 1, $items.length - 1);
            highlightItem($items, currentSelectedIndex);
        }
        // Arrow Up
        else if (e.keyCode === 38) {
            e.preventDefault();
            currentSelectedIndex = Math.max(currentSelectedIndex - 1, 0);
            highlightItem($items, currentSelectedIndex);
        }
        // Enter
        else if (e.keyCode === 13) {
            e.preventDefault();
            if (currentSelectedIndex >= 0 && currentSelectedIndex < $items.length) {
                $items.eq(currentSelectedIndex).trigger("click");
            }
        }
        // Escape
        else if (e.keyCode === 27) {
            e.preventDefault();
            $results.addClass("d-none").empty();
            currentSelectedIndex = -1;
        }
    });

    function highlightItem($items, index) {
        $items.removeClass("highlighted");
        if (index >= 0 && index < $items.length) {
            const $item = $items.eq(index);
            $item.addClass("highlighted");
            // Scroll into view if needed
            $item[0].scrollIntoView({ block: "nearest", behavior: "smooth" });
        }
    }

    // Close results when clicking outside
    $(document).on("click", function (e) {
        if (!$(e.target).closest('.form-group.position-relative').length) {
            $(searchResultsSelector).addClass("d-none").empty();
            currentSelectedIndex = -1;
        }
    });

})(jQuery);
