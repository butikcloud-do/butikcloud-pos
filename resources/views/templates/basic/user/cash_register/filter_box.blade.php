@php
    $request = request();
    $user = auth()->user();
    $staffs = App\Models\User::active()
        ->where('parent_id', auth()->id())
        ->get();
@endphp
<form action="" id="filter-form">
    <div class="form-group">
        <label class="form-label">@lang('Staff')</label>
        <select name="user_id" id="" class="form-control select2">
            <option value="">@lang('All Staff')</option>
            <option value="{{ $user->id }}">{{ $user->fullname }}</option>
            @foreach ($staffs as $staff)
                <option value="{{ $staff->id }}" @selected($user->id == $staff->id)>
                    {{ @$staff->fullname }}
                </option>
            @endforeach
        </select>
    </div>
    <x-panel.other.filter_date />
    <x-panel.other.order_by />
    <x-panel.other.per_page_record />
    <x-panel.other.filter_dropdown_btn />
</form>
