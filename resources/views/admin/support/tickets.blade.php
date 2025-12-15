@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card class="table-has-filter">
                <x-panel.ui.card.body :paddingZero="true">
                    <x-panel.ui.table.layout searchPlaceholder="Search here..." filterBoxLocation="admin.support.filter_form">
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('Submitted By')</th>
                                    <th>@lang('Subject')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Priority')</th>
                                    <th>@lang('Last Reply')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($tickets as $ticket)
                                    <tr>
                                        <td>
                                            @if ($ticket->user_id)
                                                <x-panel.other.user_info :user="$ticket->user" />
                                            @else
                                                <div
                                                    class="d-flex align-items-center gap-2 flex-wrap justify-content-end justify-content-md-start">
                                                    <span class="table-thumb">
                                                        <img src="{{ siteFavicon() }}" alt="user">
                                                    </span>
                                                    <span>{{ $ticket->name }}</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.ticket.view', $ticket->id) }}" class="fw-smibold">
                                                [@lang('Ticket')#{{ $ticket->ticket }}]
                                                {{ strLimit($ticket->subject, 30) }}
                                            </a>
                                        </td>
                                        <td>
                                            @php echo $ticket->statusBadge; @endphp
                                        </td>
                                        <td>
                                            @php echo $ticket->priorityBadge; @endphp
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">
                                                    {{ diffForHumans($ticket->last_reply) }}
                                                </span>
                                                <small>{{ showDateTime($ticket->last_reply) }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.ticket.view', $ticket->id) }}"
                                                class="btn  btn-outline--primary">
                                                <i class="las la-info-circle"></i> @lang('Details')
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($tickets->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($tickets) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>
@endsection
