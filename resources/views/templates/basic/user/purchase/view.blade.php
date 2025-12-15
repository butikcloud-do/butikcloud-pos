@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row gy-4">
        <div class="col-12">
            @include('Template::user.purchase.invoice')
        </div>
        <div class="col-12">
            <div class="d-flex gap-3 flex-wrap">
                <x-staff_permission_check permission="edit purchase">
                    <a class="btn btn--primary" href="{{ route('user.purchase.edit', $purchase->id) }}">
                        <i class="las la-pencil-alt"></i>
                        @lang('Edit Purchase')
                    </a>
                </x-staff_permission_check>
                <x-staff_permission_check permission="download purchase invoice">
                    <a class="btn btn--info" href="{{ route('user.purchase.pdf', $purchase->id) }}">
                        <i class="las  la-file-download "></i>
                        @lang('Download PDF')
                    </a>
                </x-staff_permission_check>
                <x-staff_permission_check permission="print purchase invoice">
                    <button type="button" class="btn btn--dark print-btn">
                        <i class="las la-print"></i>
                        @lang('Print Invoice')
                    </button>
                </x-staff_permission_check>
                <x-staff_permission_check permission="download purchase invoice">
                    <a class="btn btn--success"
                        @if ($purchase->attachment) href="{{ route('user.download.attachment', encrypt(getFilePath('purchase_attachment') . '/' . $purchase->attachment)) }}"
                    @else href="javascript:void(0)" @endif>
                        <i class="las la-download"></i>
                        @lang('Attachment')
                    </a>
                </x-staff_permission_check>
                <x-staff_permission_check permission="view purchase">
                    <x-back_btn route="{{ route('user.purchase.list') }}" />
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
