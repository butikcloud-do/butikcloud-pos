@php
    $today = now()->format('Y-m-d');
@endphp

<div class="row responsive-row">
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four url="{{ route('user.purchase.list') }}?date={{ $today }}" variant="primary"
            title="Today Purchase" :value="$widget['today_purchase']" icon="las la-calendar" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four url="{{ route('user.purchase.list') }}?date={{ now()->subDay()->format('Y-m-d') }}"
            variant="info" title="Yesterday Purchase" :value="$widget['yesterday_purchase']" icon="las la-calendar" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four
            url="{{ route('user.purchase.list') }}?date={{ now()->startOfWeek()->format('Y-m-d') }}to{{ $today }}"
            variant="primary" title="This Week Purchase" :value="$widget['this_week_purchase']" icon="las la-calendar" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four
            url="{{ route('user.purchase.list') }}?date={{ now()->subDays(7)->format('Y-m-d') }}to{{ $today }}"
            variant="info" title="Last 7 Days Purchase" :value="$widget['last_7days_week_purchase']" icon="las la-calendar" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four
            url="{{ route('user.purchase.list') }}?date={{ now()->startOfMonth()->format('Y-m-d') }}to{{ $today }}"
            variant="info" title="This Month Purchase" :value="$widget['this_month_purchase']" icon="las la-calendar" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four
            url="{{ route('user.purchase.list') }}?date={{ now()->subDays(30)->format('Y-m-d') }}to{{ $today }}"
            variant="primary" title="Last 30 Days Purchase" :value="$widget['last_30days_month_purchase']" icon="las la-calendar" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four url="{{ route('user.purchase.list') }}" variant="info" title="All Purchase"
            :value="$widget['all_purchase']" icon="las la-calendar" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four url="{{ route('user.purchase.list') }}" variant="primary" title="Last Purchase Amount"
            :value="$widget['last_purchase_amount']" icon="las la-calendar" />
    </div>
</div>
