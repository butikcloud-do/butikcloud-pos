@extends($activeTemplate . 'layouts.master')
@section('panel')
    @csrf
    <div class="row responsive-row justify-content-start">
        <div class="col-12">
            <x-panel.ui.card>
                <x-panel.ui.card.body>
                    <form method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row responsive-row justify-content-center">
                            <div class="col-xl-4">
                                <label class="form-label fw-bold">@lang('Logo Light')</label>
                                <x-image-uploader image="" :imagePath="getImage(getFilePath('logoIcon') . '/' . @$general->logo_light)" name="logo_light" :size="false"
                                    class="w-100" :required=false />
                            </div>
                            <div class="col-xl-4">
                                <label class="form-label fw-bold">@lang('Logo Dark')</label>
                                <x-image-uploader image="" name="logo_dark" :imagePath="getImage(getFilePath('logoIcon') . '/' . @$general->logo_dark)" :size="false"
                                    class="w-100" :required=false />
                            </div>
                        </div>

                        <x-panel.ui.btn.submit />
                    </form>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>
@endsection
