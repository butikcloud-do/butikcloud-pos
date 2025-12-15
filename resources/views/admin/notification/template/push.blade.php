@extends('admin.layouts.app')
@section('panel')
    <form action="{{ route('admin.setting.notification.template.update', ['push', $template->id]) }}" method="post">
        @csrf
        <x-panel.ui.card>
            <x-panel.ui.card.header class="py-3 d-flex justify-content-between">
                <h4 class="card-title">@lang('Push Notification Template')</h4>
                <div class="form-check form-switch form--switch pl-0  form-switch-success">
                    <input class="form-check-input" name="push_status" type="checkbox" role="switch"
                        @checked($template->push_status)>
                </div>
            </x-panel.ui.card.header>
            <x-panel.ui.card.body>
                <div class="row gy-4">
                    @include('admin.notification.template.nav')
                    @include('admin.notification.template.shortcodes')
                    <div class="col-12">
                        <div class="form-group">
                            <label>@lang('Notification Title')</label>
                            <input type="text" class="form-control" placeholder="@lang('Notification Title')"
                                name="push_title" value="{{ $template->push_title }}">
                            <small class="text-primary"><i><i class="las la-info-circle"></i>
                                    @lang('Make the field empty if you want to use global template\'s title as notification title.')</i></small>
                        </div>
                        <div class="form-group">
                            <label>@lang('Message')</label>
                            <textarea name="push_body" rows="10" class="form-control" placeholder="@lang('Your message using short-codes')" required>{{ $template->push_body }}</textarea>
                        </div>
                    </div>
                </div>
                <x-panel.ui.btn.submit />
            </x-panel.ui.card.body>
        </x-panel.ui.card>
    </form>
@endsection

@push('breadcrumb-plugins')
    <x-back_btn route="{{ route('admin.setting.notification.templates') }}" />
@endpush
