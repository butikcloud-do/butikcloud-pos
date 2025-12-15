@php
    $today = now()->format('Y-m-d');
@endphp
<div class="row responsive-row">
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four url="{{ route('user.expense.list') }}?date={{ $today }}" variant="primary"
            title="Today Expense" :value="$widget['today_expense']" icon="las la-calendar" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four url="{{ route('user.expense.list') }}?date={{ now()->subDay()->format('Y-m-d') }}"
            variant="info" title="Yesterday Expense" :value="$widget['yesterday_expense']" icon="las la-calendar" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four
            url="{{ route('user.expense.list') }}?date={{ now()->startOfWeek()->format('Y-m-d') }}to{{ $today }}"
            variant="primary" title="This Week Expense" :value="$widget['this_week_expense']" icon="las la-calendar" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four
            url="{{ route('user.expense.list') }}?date={{ now()->subDays(7)->format('Y-m-d') }}to{{ $today }}"
            variant="info" title="Last 7 Days Expense" :value="$widget['last_7days_week_expense']" icon="las la-calendar" />
    </div>

    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four
            url="{{ route('user.expense.list') }}?date={{ now()->startOfMonth()->format('Y-m-d') }}to{{ $today }}"
            variant="info" title="This Month Expense" :value="$widget['this_month_expense']" icon="las la-calendar" />
    </div>

    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four
            url="{{ route('user.expense.list') }}?date={{ now()->subDays(30)->format('Y-m-d') }}to{{ $today }}"
            variant="primary" title="Last 30 Days Expense" :value="$widget['last_30days_month_expense']" icon="las la-calendar" />
    </div>

    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four url="{{ route('user.expense.list') }}" variant="info" title="All Expense"
            :value="$widget['all_expense']" icon="las la-calendar" />
    </div>

    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four url="{{ route('user.expense.list') }}" variant="primary" title="Last Expense Amount"
            :value="$widget['last_expense_amount']" icon="las la-calendar" />
    </div>
</div>
