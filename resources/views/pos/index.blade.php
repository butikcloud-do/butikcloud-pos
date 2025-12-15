@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="preloader">
        <div class="spinner">
        </div>
        <img src="{{ siteFavicon() }}" alt="">
    </div>
    <main class="pos">
        @include('pos.partials.header')
        <section class="pos-section">
            @include('pos.partials.left_side')
            @include('pos.partials.right_side')
        </section>
        @include('pos.partials.footer')
    </main>
    <div class="pos-loader d-none">
        <span class="loader-spin"></span>
    </div>
    @include('pos.partials.audio')
    @include('pos.partials.shortcut')
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/ovopanel/css/pos.css') }}">
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            /* ==================== Pos Sidebar JS Start ======================== */
            $('[data-toggle="pos-sidebar"]').each(function(index, toggler) {
                let id = $(toggler).data('target');
                let sidebar = $(id);
                let sidebarClose = sidebar.find('.btn--close');
                let sidebarOverlay = $('.sidebar-overlay');

                let hideSidebar = function() {
                    sidebar.removeClass('show');
                    sidebarOverlay.removeClass('show');
                    $(toggler).removeClass('active');
                    $('body').removeClass('scroll-hide');
                    $(document).unbind('keydown', EscSidbear);
                }

                let EscSidbear = function(e) {
                    if (e.keyCode === 27) {
                        hideSidebar();
                    }
                }

                let showSidebar = function() {
                    $(toggler).addClass('active');
                    sidebar.addClass('show');
                    sidebarOverlay.addClass('show');
                    $('body').addClass('scroll-hide');
                    $(document).on('keydown', EscSidbear);
                }

                $(toggler).on('click', showSidebar);
                $(sidebarOverlay).on('click', hideSidebar);
                $(sidebarClose).on('click', hideSidebar);
            });
            /* ==================== Pos Sidebar JS End ======================== */

            /* ==================== Product QTY JS Start ================================== */

            /* ==================== Product QTY JS End ==================================== */

            /* ==================== Pos Section Offset Calculation JS Start ====================== */
            let pos = $('.pos');
            let posSection = pos.find('.pos-section');
            let posHeader = pos.find('.pos-header');
            let posPT = parseInt(pos.css('padding-top').replace('px', ''));
            let posPB = parseInt(pos.css('padding-bottom').replace('px', ''));
            let posHeaderMB = parseInt(posHeader.css('margin-bottom').replace('px', ''));
            let posHeaderHeight = posHeader.outerHeight();
            posSection.css('--offset', `${posPT + posPB + posHeaderMB + posHeaderHeight}px`);
            /* ==================== Pos Section Offset Calculation JS End ======================== */
        })(jQuery);
    </script>
@endpush
