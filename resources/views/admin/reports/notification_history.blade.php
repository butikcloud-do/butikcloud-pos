@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card class="table-has-filter">
                <x-panel.ui.card.body :paddingZero="true">
                    <x-panel.ui.table.layout searchPlaceholder="Search Username" filterBoxLocation="admin.reports.filter_form">
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Sent')</th>
                                    <th>@lang('Sender')</th>
                                    <th>@lang('Subject')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>
                                            <x-panel.other.user_info :user="$log->user" />
                                        </td>
                                        <td>
                                            {{ showDateTime($log->created_at) }}
                                            <br>
                                            {{ diffForHumans($log->created_at) }}
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ keyToTitle($log->notification_type) }}</span> <br>
                                            @lang('via') {{ __($log->sender) }}
                                        </td>
                                        <td>
                                            @if ($log->subject)
                                                {{ __($log->subject) }}
                                            @else
                                                @lang('N/A')
                                            @endif
                                        </td>
                                        <td>
                                            @if ($log->notification_type == 'email')
                                                <button class="btn  btn-outline--primary notifyDetail"
                                                    data-type="{{ $log->notification_type }}"
                                                    data-message="{{ route('admin.report.email.details', $log->id) }}"
                                                    data-sent_to="{{ $log->sent_to }}">
                                                    <i class="las la-info-circle"></i>
                                                    @lang('Detail')
                                                </button>
                                            @else
                                                <button class="btn  btn-outline--primary notifyDetail"
                                                    data-type="{{ $log->notification_type }}"
                                                    data-message="{{ $log->message }}"
                                                    data-image="{{ asset(getFilePath('push') . '/' . $log->image) }}"
                                                    data-sent_to="{{ $log->sent_to }}">
                                                    <i class="las la-info-circle"></i>
                                                    @lang('Detail')
                                                </button>
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

    <x-panel.ui.modal id="notifyDetailModal">
        <x-panel.ui.modal.header>
            <h1 class="modal-title">@lang('Notification Details')</h1>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-panel.ui.modal.header>
        <x-panel.ui.modal.body>
            <h3 class="text-center mb-3">@lang('To'): <span class="sent_to"></span></h3>
            <div class="detail"></div>
        </x-panel.ui.modal.body>
    </x-panel.ui.modal>
@endsection

@if (request()->user_id)
    @push('breadcrumb-plugins')
        <a href="{{ route('admin.users.notification.single', request()->user_id) }}"
            class="btn btn--primary"><i class="fa-regular fa-paper-plane"></i>
            <span class="ms-1">@lang('Send Notification')</span>
        </a>
    @endpush
@endif

@push('script')
    <script>
        $('.notifyDetail').on('click', function() {
            var message = ''
            if ($(this).data('image')) {
                message += `<img src="${$(this).data('image')}" class="w-100 mb-2" alt="image">`;
            }
            message += $(this).data('message');
            var sent_to = $(this).data('sent_to');
            var modal = $('#notifyDetailModal');
            if ($(this).data('type') == 'email') {
                var message = `<iframe src="${message}" height="500" width="100%" title="Iframe Example"></iframe>`
            }
            $('.detail').html(message)
            $('.sent_to').text(sent_to)
            modal.modal('show');
        });

    </script>
@endpush
