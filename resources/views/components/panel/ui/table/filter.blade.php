<div class="table-header">
    <div class="table-search">
        <input form="filter-form" type="search" name="search" placeholder="{{ $searchPlaceholder }}"
            value="{{ request()->search ?? '' }}" class="form-control form--control-sm">
        <button form="filter-form" type="submit" class="search-btn"> <i class="las la-search"></i> </button>
    </div>
    <div class="table-right">
        @if ($renderExportButton)
            <x-panel.ui.table.export_btn />
        @endif
        @if ($renderFilterOption)
            <x-panel.ui.table.filter_box :filterBoxLocation=$filterBoxLocation />
        @endif
        @if ($hasRecycleBin)
            <x-panel.ui.table.recycle_bin_btn />
        @endif
    </div>
</div>
