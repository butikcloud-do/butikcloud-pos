@php
    $request = request();
@endphp
<form action="" id="filter-form">
    @if (request()->trash)
        <input type="hidden" name="trash" value="1">
    @endif

    <div class="form-group">
        <label class="form-label">@lang('Supplier')</label>
        <x-panel.other.lazy_loading_select name="supplier_id" :required="false" :route="route('admin.supplier.lazy.loading')" />
    </div>
    <x-panel.other.filter_date />
    <x-panel.other.order_by />
    <x-panel.other.per_page_record />
    <x-panel.other.filter_dropdown_btn />
</form>

