@php
    $request = request();
@endphp

<form action="" id="filter-form">
    @if ($request->user_id)
        <input type="hidden" name="user_id" value="{{ $request->user_id }}">
    @endif
    <x-panel.other.filter_date />
    <x-panel.other.order_by />
    <x-panel.other.per_page_record />
    <x-panel.other.filter_dropdown_btn />

</form>
