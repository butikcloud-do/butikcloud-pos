<x-panel.ui.modal id="shortcut-modal">
    <x-panel.ui.modal.header>
        <h4 class="modal-title">@lang('Keyboard Shortcut')</h4>
        <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
            <i class="las la-times"></i>
        </button>
    </x-panel.ui.modal.header>
    <x-panel.ui.modal.body>
        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex gap-2 justify-content-between flex-wrap">
                <span>@lang('Open Calculator')</span>
                <span>
                    <span class="badge badge--dark">@lang('Ctrl')</span>
                    <span>+</span>
                    <span class="badge badge--dark">@lang('Alt')</span>
                    <span>+</span>
                    <span class="badge badge--dark">@lang('C')</span>
                </span>
            </li>
            <li class="list-group-item d-flex gap-2 justify-content-between flex-wrap">
                <span>@lang('Add Customer')</span>
                <span>
                    <span class="badge badge--dark">@lang('Ctrl')</span>
                    <span>+</span>
                    <span class="badge badge--dark">@lang('Alt')</span>
                    <span>+</span>
                    <span class="badge badge--dark">@lang('A')</span>
                </span>
            </li>
            <li class="list-group-item d-flex gap-2 justify-content-between flex-wrap">
                <span>@lang('Clear Cart')</span>
                <span>
                    <span class="badge badge--dark">@lang('Ctrl')</span>
                    <span>+</span>
                    <span class="badge badge--dark">@lang('Alt')</span>
                    <span>+</span>
                    <span class="badge badge--dark">@lang('L')</span>
                </span>
            </li>
            <li class="list-group-item d-flex gap-2 justify-content-between flex-wrap">
                <span>@lang('Focus Product Search')</span>
                <span>
                    <span class="badge badge--dark">@lang('Ctrl')</span>
                    <span>+</span>
                    <span class="badge badge--dark">@lang('Alt')</span>
                    <span>+</span>
                    <span class="badge badge--dark">@lang('F')</span>
                </span>
            </li>
            <li class="list-group-item d-flex gap-2 justify-content-between flex-wrap">
                <span>@lang('Open Cash Payment')</span>
                <span>
                    <span class="badge badge--dark">@lang('Ctrl')</span>
                    <span>+</span>
                    <span class="badge badge--dark">@lang('Alt')</span>
                    <span>+</span>
                    <span class="badge badge--dark">@lang('P')</span>
                </span>
            </li>
            <li class="list-group-item d-flex gap-2 justify-content-between flex-wrap">
                <span>@lang('Open Cart Payment')</span>
                <span>
                    <span class="badge badge--dark">@lang('Ctrl')</span>
                    <span>+</span>
                    <span class="badge badge--dark">@lang('Alt')</span>
                    <span>+</span>
                    <span class="badge badge--dark">@lang('T')</span>
                </span>
            </li>
            <li class="list-group-item d-flex gap-2 justify-content-between flex-wrap">
                <span>@lang('Open Multiple Payment')</span>
                <span>
                    <span class="badge badge--dark">@lang('Ctrl')</span>
                    <span>+</span>
                    <span class="badge badge--dark">@lang('Alt')</span>
                    <span>+</span>
                    <span class="badge badge--dark">@lang('M')</span>
                </span>
            </li>
            <li class="list-group-item d-flex gap-2 justify-content-between flex-wrap">
                <span>@lang('Add Discount')</span>
                <span>
                    <span class="badge badge--dark">@lang('Ctrl')</span>
                    <span>+</span>
                    <span class="badge badge--dark">@lang('Alt')</span>
                    <span>+</span>
                    <span class="badge badge--dark">@lang('D')</span>
                </span>
            </li>
            <li class="list-group-item d-flex gap-2 justify-content-between flex-wrap">
                <span>@lang('Add Shipping Amount')</span>
                <span>
                    <span class="badge badge--dark">@lang('Ctrl')</span>
                    <span>+</span>
                    <span class="badge badge--dark">@lang('Alt')</span>
                    <span>+</span>
                    <span class="badge badge--dark">@lang('S')</span>
                </span>
            </li>
        </ul>
    </x-panel.ui.modal.body>
</x-panel.ui.modal>

@push('script')
    <script>
        "use strict";
        (function($) {
            $(document).on('keydown', function(event) {
                // Check if Ctrl + Alt are pressed
                if (event.ctrlKey && event.altKey) {
                    // Open Calculator (Ctrl + Alt + C)
                    if (event.key === 'c' || event.key === 'C') {
                        event.preventDefault();
                        $('body').find('.calculator-open-btn').trigger('click');
                    }

                    // Add Customer (Ctrl + Alt + A)
                    if (event.key === 'a' || event.key === 'A') {
                        event.preventDefault();
                        $('body').find('.add-customer').trigger('click');
                    }

                    // Clear Cart (Ctrl + Alt + L)
                    if (event.key === 'l' || event.key === 'L') {
                        event.preventDefault();
                        $('body').find('.cancelBtn').trigger('click');
                    }

                    // Focus Product Search (Ctrl + Alt + F)
                    if (event.key === 'f' || event.key === 'F') {
                        event.preventDefault();
                        $('body').find('.product-search-input').trigger('focus');
                    }

                    // Open Cash Payment (Ctrl + Alt + P)
                    if (event.key === 'p' || event.key === 'P') {
                        event.preventDefault();
                        $('body').find('.payment-btn').first().trigger('click');
                    }

                    // Open Cart Payment (Ctrl + Alt + T)
                    if (event.key === 't' || event.key === 'T') {
                        event.preventDefault();
                        $('body').find('.payment-btn').last().trigger('click');
                    }

                    // Open Multiple Payment (Ctrl + Alt + M)
                    if (event.key === 'm' || event.key === 'M') {
                        event.preventDefault();
                        $('body').find('.multiple-pay-btn').trigger('click');
                    }

                    // Change Discount (Ctrl + Alt + D)
                    if (event.key === 'd' || event.key === 'D') {
                        event.preventDefault();
                        $('body').find('.summary-discount-btn').trigger('click');
                    }

                    // Add Shipping Amount (Ctrl + Alt + S)
                    if (event.key === 's' || event.key === 'S') {
                        event.preventDefault();
                        $('body').find('.summary-discount-btn').trigger('click');
                    }
                }
            });

        })(jQuery);
    </script>
@endpush
