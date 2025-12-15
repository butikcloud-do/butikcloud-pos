@props(['permission'])
@if (is_array($permission))
    @if ($user->hasAnyStaffPermission($permission))
        {{ $slot }}
    @endif
@else
    @if ($user->hasStaffPermission($permission))
        {{ $slot }}
    @endif
@endif