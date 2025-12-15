@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row responsive-row justify-content-center">
        <div class="col-12">
            @include('Template::user.sale.invoice')
        </div>
        <div class="col-12">
            <div class="d-flex flex-wrap gap-3">
                <x-staff_permission_check permission="edit sale">
                    <a class="btn btn--primary" href="{{ route('user.sale.edit', $sale->id) }}">
                        <i class="las la-pencil-alt"></i>
                        @lang('Edit Sale')
                    </a>
                </x-staff_permission_check>
                <x-staff_permission_check permission="download sale invoice">
                    <a class="btn btn--info" href="{{ route('user.sale.pdf', $sale->id) }}">
                        <i class="las  la-file-download "></i>
                        @lang('Download PDF')
                    </a>
                </x-staff_permission_check>
                <x-staff_permission_check permission="print sale invoice">
                    <button type="button" class="btn btn--dark print-btn">
                        <i class="las la-print"></i>
                        @lang('Print Invoice')
                    </button>
                </x-staff_permission_check>
                <x-staff_permission_check permission="print pos sale invoice">
                    <button type="button" class="btn btn--success print-pos-invoice"
                        data-action="{{ route('user.sale.print', $sale->id) }}?invoice_type=pos">
                        <i class="las la-print"></i>
                        @lang('Print POS Invoice')
                    </button>
                </x-staff_permission_check>
                <x-staff_permission_check permission="view sale">
                    <x-back_btn route="{{ route('user.sale.list') }}" />
                </x-staff_permission_check>
            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset( 'assets/ovopanel/css/invoice.css') }}">
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.print-btn').on('click', function() {
                $('body')
                    .append(`<div class="print-content">${$('.invoice-wrapper').clone().html()}</div>`);
                window.print();
            });
            $(window).on('afterprint', function() {
                $('body').find('.print-content').remove();
            });

            $(".print-pos-invoice").on('click', function() {
                const action = $(this).data('action');
                $.ajax({
                    type: "GET",
                    url: action,
                    success: function(response) {
                        if (response.status == 'success') {
                            $('body')
                                .append(`<div class="print-content">${response.data.html}</div>`);
                            window.print();
                        } else {
                            notify('error', response.message);
                        }
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
