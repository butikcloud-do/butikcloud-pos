@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <x-panel.ui.card>
                <x-panel.ui.card.header>
                    <h4 class="card-title">@lang('Insert Robots txt')</h4>
                </x-panel.ui.card.header>
                <x-panel.ui.card.body>
                    <form method="post">
                        @csrf
                        <div class="form-group">
                            <textarea class="form-control" rows="10" name="robots">{{ $fileContent }}</textarea>
                        </div>
                        <x-panel.ui.btn.submit />
                    </form>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>
@endsection
