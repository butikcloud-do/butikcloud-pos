@extends('admin.layouts.app')
@section('panel')
    <x-panel.ui.widget.group.dashboard.subscription_amount :widget="$widget" />
    <x-panel.ui.widget.group.dashboard.users :widget="$widget" />

    <div class="row gy-4 mb-4">
        <x-panel.other.dashboard_trx_chart />
        <div class="col-xl-4">
            <x-panel.other.dashboard_login_chart :userLogin=$userLogin />
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/ovopanel/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/ovopanel/js/charts.js') }}"></script>
    <script src="{{ asset('assets/global/js/flatpickr.js') }}"></script>
@endpush


@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/global/css/flatpickr.min.css') }}">
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $(".date-picker").flatpickr({
                mode: 'range',
                maxDate: new Date(),
            });
        })(jQuery);
    </script>
@endpush
