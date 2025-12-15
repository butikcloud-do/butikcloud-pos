@props(['widget'])
<div class="row responsive-row">
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.two :url="route('admin.subscription.plan.list')" variant="primary" title="Total Plans" :value="$widget['total_plans']" icon="las la-store" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.two :url="route('admin.user.subscriptions')" variant="info" title="Total Purchased Plans" :value="$widget['total_purchases']" icon="las la-money-check-alt" />
    </div>

</div>