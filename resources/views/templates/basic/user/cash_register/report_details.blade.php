<div class="row my-4">
    <div class="col-lg-4 col-sm-6">
        <h5>@lang('Starting Information')</h5>
        <div class="d-flex flex-column flex-wrap gap-1">
            <span>@lang('Starting Amount'): <strong>{{ showAmount($cashRegister->starting_amount) }}</strong> </span>
            <span>@lang('Starting Time'): <strong>{{ showDateTime($cashRegister->starting_time) }}</strong> </span>
            <span>@lang('Starting Note'): {{ __(@$cashRegister->starting_note ?? 'N/A') }} </span>
        </div>
    </div>
    <div class="col-lg-4 col-sm-6">
        <h5>@lang('Closing Information')</h5>
        <div class="d-flex flex-column flex-wrap gap-1">
            <span>@lang('Closing Amount'): <strong>{{ showAmount($cashRegister->closing_amount) }}</strong> </span>
            <span>@lang('Closing Time'): <strong>{{ showDateTime($cashRegister->closing_time) }}</strong> </span>
            <span>@lang('Closing Note'): {{ __(@$cashRegister->closing_note ?? 'N/A') }} </span>
        </div>
    </div>
    <div class="col-lg-4 col-sm-12">
        <h5>@lang('User Information')</h5>
        <div class="d-flex flex-column flex-wrap gap-1">
            <span>@lang('Name'): {{ __(@$cashRegister->user->fullname ?? 'N/A') }} </span>
            <span>@lang('Email'): {{ @$cashRegister->user->email ?? 'N/A' }} </span>
            <span>@lang('Mobile'): {{ @$cashRegister?->user?->mobileNumber ?? 'N/A' }} </span>
        </div>
    </div>
</div>
<ul class="list-group list-group-flush mb-4">
    <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 px-0 list-group-title">
        <span>@lang('Payment Type')</span>
        <span>@lang('Total Sale')</span>
        <span>@lang('Total Expense')</span>
        <span>@lang('Other Credit')</span>
        <span>@lang('Other Debit')</span>
    </li>
    @foreach ($paymentTypes as $paymentType)
        <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 px-0">
            <span>{{ __(@$paymentType->name) }}</span>
            <span>{{ showAmount($paymentType->total_sale) }}</span>
            <span>{{ showAmount($paymentType->total_expense) }}</span>
            <span>{{ showAmount($paymentType->total_expense) }}</span>
            <span>{{ showAmount($paymentType->total_expense) }}</span>
        </li>
    @endforeach
</ul>

<div class="row justify-content-end">
    <div class="col-lg-5 col-sm-12">
        <h5>@lang('Register Summary')</h5>
        <ul class="list-group list-group-flush mb-4">
            <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 px-0">
                <span>@lang('Total Sale')</span>
                <span>{{ showAmount($paymentTypes->sum('total_sale')) }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 px-0">
                <span>@lang('Total Expense')</span>
                <span>{{ showAmount($paymentTypes->sum('total_expense')) }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 px-0">
                <span>@lang('Total Other Credit')</span>
                <span>{{ showAmount($paymentTypes->sum('total_other_credit')) }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 px-0">
                <span>@lang('Total Other Debit')</span>
                <span>{{ showAmount($paymentTypes->sum('total_other_debit')) }}</span>
            </li>
        </ul>
    </div>
</div>
