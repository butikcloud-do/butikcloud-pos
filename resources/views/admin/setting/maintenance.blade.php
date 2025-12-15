@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <form method="post" enctype="multipart/form-data">
                <x-panel.ui.card>
                    <x-panel.ui.card.header class="pt-3  pb-2 d-flex justify-content-between">
                        <h5 class="fs-16 mb-0">@lang('Maintenance Mode Content')</h5>
                        <div class="form-check form-switch form--switch pl-0 form-switch-success">
                            <input class="form-check-input" name="status" type="checkbox" role="switch"
                                @checked(@gs('maintenance_mode'))>
                        </div>
                    </x-panel.ui.card.header>
                    <x-panel.ui.card.body>
                        @csrf
                        <div class="row">
                            <div class="col-xl-4">
                                <div class="form-group">
                                    <label>@lang('Image')</label>
                                    <x-image-uploader class="w-100" :imagePath="getImage(
                                        getFilePath('maintenance') . '/' . @$maintenance->data_values->image,
                                        getFileSize('maintenance'),
                                    )" :size="getFileSize('maintenance')" :required="false"
                                        name="image" />
                                </div>
                            </div>
                            <div class="col-xl-8">
                                <div class="form-group">
                                    <label>@lang('Description')</label>
                                    <textarea class="form-control editor " id="editor" rows="10" name="description">@php echo @$maintenance->data_values->description @endphp</textarea>
                                </div>
                                <x-panel.ui.btn.submit />
                            </div>
                        </div>
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
