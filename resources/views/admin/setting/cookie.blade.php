@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <form method="post">
                @csrf
                <x-panel.ui.card>
                    <x-panel.ui.card.header class="pt-3  pb-2 d-flex justify-content-between">
                        <h4 class="fs-16 mb-0">@lang('GDPR Cookie Policy')</h4>
                        <div class="form-check form-switch form--switch pl-0 form-switch-success">
                            <input class="form-check-input" name="status" type="checkbox" role="switch"
                                @checked(@$cookie->data_values->status)>
                        </div>
                    </x-panel.ui.card.header>
                    <x-panel.ui.card.body>
                        <div class="form-group">
                            <label>@lang('Short Description')</label>
                            <textarea class="form-control" rows="5" required name="short_desc">{{ @$cookie->data_values->short_desc }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>@lang('Description')</label>
                            <textarea class="form-control editor" rows="10" name="description">@php echo @$cookie->data_values->description @endphp</textarea>
                        </div>
                        <x-panel.ui.btn.submit />
                    </x-panel.ui.card.body>
                </x-panel.ui.card>
            </form>
        </div>
    </div>
@endsection



@push('script-lib')
    <script src="{{ asset('assets/global/js/summernote-lite.min.js') }}"></script>
@endpush
@push('style-lib')
    <link href="{{ asset('assets/global/css/summernote-lite.min.css') }}" rel="stylesheet">
@endpush
