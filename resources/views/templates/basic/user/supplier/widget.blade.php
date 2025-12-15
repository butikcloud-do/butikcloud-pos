@php
    $today = now()->format('Y-m-d');
@endphp

<div class="row responsive-row">
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four url="javascript:void" variant="primary"
            title="Total Purchase" :value="$widget['total_purchase']" icon="las la-calendar" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four url="javascript:void"
            variant="info" title="Total Payment" :value="$widget['total_payment']" icon="las la-calendar" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four
            url="javascript:void"
            variant="primary" title="Total Due" :value="$widget['total_due']" icon="las la-calendar" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four
            url="javascript:void"
            variant="info" title="Today Payment" :value="$widget['today_payment']" icon="las la-calendar" />
    </div>


</div>
