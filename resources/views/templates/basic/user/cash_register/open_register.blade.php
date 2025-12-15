@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="form-group">
                <div class="alert alert--warning">
                    <p class="fs-18">
                        @lang('No active cash register detected. Please open a new cash register to continue your operations. Ensure all opening balances and relevant details are correctly entered before proceeding.')
                    </p>
                </div>
            </div>
        </div>
        <div class="col-7">
            <x-panel.ui.card>
                <x-panel.ui.card.body>
                    <form action="{{ route('user.cash_register.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">@lang('Starting Amount')</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ __(gs('cur_sym', getParentUser()->id)) }}</span>
                                <input type="number" step="any" class="form-control" name="starting_amount" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">@lang('Starting Note')</label>
                            <textarea name="starting_note" class="form-control" cols="30" rows="10"></textarea>
                        </div>
                        <button type="submit" class="btn btn--primary w-100 btn-large">
                            <span class="me-1"><i class="fa-regular fa-paper-plane"></i></span> @lang('Submit')
                        </button>
                    </form>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>
@endsection
