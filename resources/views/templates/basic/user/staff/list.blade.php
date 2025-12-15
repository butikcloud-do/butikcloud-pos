@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card>
                <x-panel.ui.card.body :paddingZero=true>
                    <x-panel.ui.table.layout>
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('Staff')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($staffs as $staff)
                                    <tr>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ __($staff->username) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <x-panel.ui.btn.table_action module="staff" :id="$staff->id">
                                                <x-staff_permission_check permission="edit staff">
                                                    <x-panel.ui.btn.edit tag="a" href="{{ route('user.staff.edit', $staff->id) }}" />
                                                    <a href="{{ route('user.staff.permissions', $staff->id) }}" class="btn btn-outline--info">
                                                        <i class="fas fa-user-check"></i> @lang('Permissions')
                                                    </a>
                                                </x-staff_permission_check>
                                            </x-panel.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($staffs->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($staffs) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-staff_permission_check permission="add sale">
        <x-panel.ui.btn.add href="{{ route('user.staff.create') }}" text="New Staff" />
    </x-staff_permission_check>
@endpush