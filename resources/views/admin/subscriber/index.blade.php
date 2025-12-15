@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card class="table-has-filter">
                <x-panel.ui.card.body :paddingZero="true">
                    <x-panel.ui.table.layout searchPlaceholder="Search subscribers">
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Subscribe At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($subscribers as $subscriber)
                                    <tr>
                                        <td>{{ $subscriber->email }}</td>
                                        <td>{{ showDateTime($subscriber->created_at) }}</td>
                                        <td>
                                            <button class="btn  btn-outline--danger confirmationBtn"
                                                data-question="@lang('Are you sure to remove this subscriber?')"
                                                data-action="{{ route('admin.subscriber.remove', $subscriber->id) }}">
                                                <i class="fa-regular fa-trash-can"></i> @lang('Remove')
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($subscribers->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($subscribers) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@if ($subscribers->count())
    @push('breadcrumb-plugins')
        <a href="{{ route('admin.subscriber.send.email') }}" class="btn  btn--primary">
            <i class="fa-regular fa-paper-plane"></i> @lang('Send Email')
        </a>
    @endpush
@endif
