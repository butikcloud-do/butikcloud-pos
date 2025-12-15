@php
    $request = request();

@endphp

<form action="" id="filter-form">

    <div class="form-group">
        <label class="form-label">@lang('Warehouse')</label>
        <select class="form-select select2" name="warehouse_id">
            @foreach ($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" @selected(request()->warehouse_id == $warehouse->id)>
                    {{ __(@$warehouse->name) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label class="form-label">@lang('Brand')</label>
        <select class="form-select select2" name="brand_id">
            <option value="">@lang('All Brands')</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" @selected(request()->brand_id == $brand->id)>
                    {{ __(@$brand->name) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label class="form-label">@lang('Category')</label>
        <select class="form-select select2" name="category_id">
            <option value="">@lang('All Categories')</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request()->category_id == $category->id)>
                    {{ __(@$category->name) }}
                </option>
            @endforeach
        </select>
    </div>


    <x-panel.other.order_by />
    <x-panel.other.per_page_record />
    <x-panel.other.filter_dropdown_btn />
</form>
