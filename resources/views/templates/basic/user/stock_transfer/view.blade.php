@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row  responsive-row">
        <div class="col-12">
            @include('Template::user.stock_transfer.invoice')
        </div>
        <div class="col-12">
            <div class="d-flex flex-wrap align-items-center  gap-3">
                <x-staff_permission_check permission="edit stock transfer">
                    <a class="btn btn--primary" href="{{ route('user.stock.transfer.edit', $transfer->id) }}">
                        <i class="las la-pencil-alt"></i>
                        @lang('Edit Transfer')
                    </a>
                </x-staff_permission_check>
                <x-staff_permission_check permission="view stock transfer">
                    <a class="btn btn--warning" href="{{ route('user.stock.transfer.pdf', $transfer->id) }}">
                        <i class="las la-file-download "></i>
                        @lang('Download PDF')
                    </a>
                </x-staff_permission_check>
                <x-staff_permission_check permission="view stock transfer">
                    <a class="btn btn--info"
                        href="{{ route('user.download.attachment', encrypt(getFilePath('stock_transfer_attachment') . '/' . $transfer->attachment)) }}">
                        <i class="las la-file-download "></i>
                        @lang('Download Attachment')
                    </a>
                </x-staff_permission_check>
                <x-staff_permission_check permission="view stock transfer">
                    <button type="button" class="btn btn--success print-btn">
                        <i class="las la-print"></i>
                        @lang('Print Invoice')
                    </button>
                </x-staff_permission_check>
                <x-staff_permission_check permission="view stock transfer">
                    <x-back_btn route="{{ route('user.stock.transfer.list') }}" />
                </x-staff_permission_check>
            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/ovopanel/css/invoice.css') }}">
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
