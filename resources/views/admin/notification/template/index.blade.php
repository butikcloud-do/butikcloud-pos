@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card>
                <x-panel.ui.card.body :paddingZero="true">
                    <x-panel.ui.table.layout :renderTableFilter="false">
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Subject')</th>
                                    <th>@lang('Edit Template')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($templates as $template)
                                    <tr>
                                        <td>{{ __($template->name) }}</td>
                                        <td>{{ __($template->subject) }}</td>
                                        <td>
                                            <div class=" flex-align justify-content-end gap-2">
                                                <a href="{{ route('admin.setting.notification.template.edit', ['email', $template->id]) }}"
                                                    class="notify-status  @if ($template->email_status != Status::ENABLE) disabled @else enable @endif">
                                                    <span class="notify-status__icon flex-center">
                                                        @if ($template->email_status != Status::ENABLE)
                                                            <i class="fas fa-times"></i>
                                                        @else
                                                            <i class="fa-solid fa-check"></i>
                                                        @endif
                                                    </span>
                                                    <span class="notify-status__link">
                                                        @lang('Email')
                                                    </span>
                                                </a>
                                                <a href="{{ route('admin.setting.notification.template.edit', ['sms', $template->id]) }}"
                                                    class="notify-status  @if ($template->sms_status != Status::ENABLE) disabled @else enable @endif">
                                                    <span class="notify-status__icon flex-center">
                                                        @if ($template->sms_status != Status::ENABLE)
                                                            <i class="fas fa-times"></i>
                                                        @else
                                                            <i class="fa-solid fa-check"></i>
                                                        @endif
                                                    </span>
                                                    <span class="notify-status__link">
                                                        @lang('SMS')
                                                    </span>
                                                </a>
                                                <a href="{{ route('admin.setting.notification.template.edit', ['push', $template->id]) }}"
                                                    class="notify-status  @if ($template->push_status != Status::ENABLE) disabled @else enable @endif">
                                                    <span class="notify-status__icon flex-center">
                                                        @if ($template->push_status != Status::ENABLE)
                                                            <i class="fas fa-times"></i>
                                                        @else
                                                            <i class="fa-solid fa-check"></i>
                                                        @endif
                                                    </span>
                                                    <span class="notify-status__link">
                                                        @lang('Push')
                                                    </span>
                                                </a>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>
@endsection
