@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card>
                <x-panel.ui.card.body :paddingZero="true">
                    <x-panel.ui.table.layout :renderTableFilter=false>
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('Start At')</th>
                                    <th>@lang('End At')</th>
                                    <th>@lang('Execution Time')</th>
                                    <th>@lang('Error')</th>
                                    <th>@lang('Actions')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse ($logs as $log)
                                    <tr>
                                        <td>{{ showDateTime($log->start_at) }} </td>
                                        <td>{{ showDateTime($log->end_at) }} </td>
                                        <td>{{ $log->duration }} @lang('Seconds')</td>
                                        <td>{{ $log->error }}</td>
                                        <td>
                                            @if ($log->error != null)
                                                <button type="button" class="btn  btn-outline--success confirmationBtn"
                                                    data-action="{{ route('admin.cron.schedule.log.resolved', $log->id) }}"
                                                    data-question="@lang('Are you sure to resolved this log?')">
                                                    <i class="la la-check"></i> @lang('Resolved')
                                                </button>
                                            @else
                                                --
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($logs->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($logs) }}
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
    <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn  btn-outline--danger confirmationBtn"
            data-action="{{ route('admin.cron.log.flush', $cronJob->id) }}" data-question="@lang('Are you sure to flush all logs?')">
            <i class="la la-history"></i> @lang('Flush Logs')
        </button>
        <x-back_btn route="{{ route('admin.cron.index') }}" />
    </div>
@endpush
