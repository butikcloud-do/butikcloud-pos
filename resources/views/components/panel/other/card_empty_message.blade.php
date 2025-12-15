<x-panel.ui.card class="text-center">
    <x-panel.ui.card.body>
        <div class="p-5">
            <img src="{{ asset('assets/images/empty_box.png') }}" class="empty-message">
            <span class="d-block">{{ __($emptyMessage) }}</span>
            <span class="d-block fs-13 text-muted">@lang('There are no available data to display on this page at the moment.')</span>
        </div>
    </x-panel.ui.card.body>
</x-panel.ui.card>
