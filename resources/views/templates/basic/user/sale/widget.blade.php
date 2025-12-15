@php
    $today = now()->format('Y-m-d');
@endphp

<div class="row responsive-row">
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four url="{{ route('user.sale.list') }}?date={{ $today }}" variant="primary"
            title="Today Sale" :value="$widget['today_sale']" icon="las la-calendar" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four url="{{ route('user.sale.list') }}?date={{ now()->subDay()->format('Y-m-d') }}"
            variant="info" title="Yesterday Sale" :value="$widget['yesterday_sale']" icon="las la-calendar" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four
            url="{{ route('user.sale.list') }}?date={{ now()->startOfWeek()->format('Y-m-d') }}to{{ $today }}"
            variant="primary" title="This Week Sale" :value="$widget['this_week_sale']" icon="las la-calendar" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four
            url="{{ route('user.sale.list') }}?date={{ now()->subDays(7)->format('Y-m-d') }}to{{ $today }}"
            variant="info" title="Last 7 Days Sale" :value="$widget['last_7days_week_sale']" icon="las la-calendar" />
    </div>

    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four
            url="{{ route('user.sale.list') }}?date={{ now()->startOfMonth()->format('Y-m-d') }}to{{ $today }}"
            variant="info" title="This Month Sale" :value="$widget['this_month_sale']" icon="las la-calendar" />
    </div>

    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four
            url="{{ route('user.sale.list') }}?date={{ now()->subDays(30)->format('Y-m-d') }}to{{ $today }}"
            variant="primary" title="Last 30 Days Sale" :value="$widget['last_30days_month_sale']" icon="las la-calendar" />
    </div>

    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four url="{{ route('user.sale.list') }}" variant="info" title="All sale"
            :value="$widget['all_sale']" icon="las la-calendar" />
    </div>

    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four url="{{ route('user.sale.list') }}" variant="primary" title="Last Sale Amount"
            :value="$widget['last_sale_amount']" icon="las la-calendar" />
    </div>
</div>
