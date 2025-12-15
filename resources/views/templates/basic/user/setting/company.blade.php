@extends($activeTemplate . 'layouts.master')
@section('panel')
    <form method="POST" action="{{ route('user.setting.company.update') }}">
        <x-panel.ui.card>
            <x-panel.ui.card.body>
                @csrf
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label> @lang('Company Name')</label>
                            <input class="form-control" type="text" name="company_information[name]"
                                value="{{ @$companyInformation->company_information->name }}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label> @lang('Email')</label>
                            <input class="form-control" type="email" name="company_information[email]"
                                value="{{ @$companyInformation->company_information->email }}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label> @lang('Phone')</label>
                            <input class="form-control" type="tel" name="company_information[phone]" required
                                value="{{ @$companyInformation->company_information->phone }}">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label> @lang('Address')</label>
                            <input class="form-control" type="tel" name="company_information[address]" required
                                value="{{ @$companyInformation->company_information->address }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <x-panel.ui.btn.submit />
                    </div>
                </div>
            </x-panel.ui.card.body>
        </x-panel.ui.card>
    </form>
@endsection
