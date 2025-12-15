@props([
    'renderTableFilter' => true,
    'renderExportButton' => true,
    'renderFilterOption' => true,
    'filterBoxLocation' => null,
    'searchPlaceholder' => 'Search here',
    'hasRecycleBin' => true,
])

<div class="table-layout">
    @if ($renderTableFilter)
        <x-panel.ui.table.filter :renderExportButton=$renderExportButton :renderFilterOption=$renderFilterOption
            :searchPlaceholder=$searchPlaceholder :filterBoxLocation="$filterBoxLocation" :hasRecycleBin="$hasRecycleBin" />
    @endif
    {{ $slot }}
</div>
