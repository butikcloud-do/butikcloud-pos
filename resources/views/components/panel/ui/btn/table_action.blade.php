@props(['id', 'module'])
@php
    $moduleNameForRoute = str_replace('_', '.', $module);
    $moduleNameForMessage = str_replace('_', ' ', $module);
    $trashPermissionName = 'trash ' . str_replace('_', ' ', $module);
@endphp
@if (request()->trash)
    <div class="btn--group">
        <button class="btn btn-outline--success confirmationBtn" data-question='@lang("Are you sure to restore this $moduleNameForMessage?")'
            data-action="{{ route("user.$moduleNameForRoute.trash.restore", $id) }}">
            <i class="las la-undo"></i> @lang('Restore')
        </button>
    </div>
@else
    <div class="btn--group">
        {{ $slot }}
        <x-permission_check :permission="$trashPermissionName">
            <button class="btn btn-outline--danger confirmationBtn" data-question='@lang("Are you sure to move this $moduleNameForMessage to trash?")'
                data-action="{{ route("user.$moduleNameForRoute.trash.temporary", $id) }}">
                <i class="las la-trash"></i> @lang('Trash')
            </button>
        </x-permission_check>
    </div>
@endif
