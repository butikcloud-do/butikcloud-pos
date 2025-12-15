<form action="" id="filter-form">
    @if (request()->trash)
        <input type="hidden" name="trash" value="1">
    @endif
    <x-panel.other.filter_date label="Expense Date" />
    <x-panel.other.order_by />
    <x-panel.other.per_page_record />
    <x-panel.other.filter_dropdown_btn />
</form>
