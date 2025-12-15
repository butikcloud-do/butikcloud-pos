@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        @foreach ($templates as $temp)
            <div class="col-xl-4 col-md-6">
                <x-panel.ui.card>
                    <x-panel.ui.card.header class="d-flex justify-content-between gap-2 py-2 align-items-center">
                        <h4 class="card-title">{{ __(keyToTitle($temp['name'])) }}</h4>
                        @if (gs('active_template') == $temp['name'])
                            <button type="submit" name="name" value="{{ $temp['name'] }}" class="btn btn--info ">
                                @lang('SELECTED')
                            </button>
                        @else
                            <form method="post">
                                @csrf
                                <button type="submit" name="name" value="{{ $temp['name'] }}" class="btn btn--success">
                                    @lang('SELECT')
                                </button>
                            </form>
                        @endif
                    </x-panel.ui.card.header>
                    <x-panel.ui.card.body>
                        <img src="{{ $temp['image'] }}" alt="Template" class="w-100 rounded">
                    </x-panel.ui.card.body>
                </x-panel.ui.card>
            </div>
        @endforeach
        @if ($extraTemplates)
            @foreach ($extraTemplates as $temp)
                <div class="col-lg-3">
                    <x-panel.ui.card>
                        <x-panel.ui.card.header>
                            <h4 class="card-title"> {{ __(keyToTitle($temp['name'])) }}</h4>
                        </x-panel.ui.card.header>
                        <x-panel.ui.card.body>
                            <img src="{{ $temp['image'] }}" alt="Template" class="w-100">
                            <a href="{{ $temp['url'] }}" target="_blank"
                                class="btn btn--primary mt-3 ">@lang('Get This')</a>
                        </x-panel.ui.card.body>
                    </x-panel.ui.card>
                </div>
            @endforeach
        @endif
    </div>
@endsection
