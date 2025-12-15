
<div class="row responsive-row">
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four url="javascript:void" variant="primary"
            title="Total Invoice" :value="$widget['total_invoices']" :currency="false" icon="las la-calendar" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four url="javascript:void"
            variant="info" title="Total Sales" :value="$widget['total_sale']" icon="las la-calendar" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four
            url="javascript:void"
            variant="primary" title="Total Purchase" :value="$widget['total_purchase']" icon="las la-calendar" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-panel.ui.widget.four
            url="javascript:void"
            variant="info" title="Gross Profit" :value="$widget['gross_profit']" icon="las la-calendar" />
    </div>

</div>
