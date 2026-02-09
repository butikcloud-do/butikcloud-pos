<div class="row my-4">
    <div class="col-lg-4 col-sm-6 mb-3">
        <h6 class="text--primary border-bottom pb-2 mb-2">@lang('Starting Information')</h6>
        <ul class="list-group list-group-flush">
            <li class="d-flex justify-content-between align-items-center border-0 px-0 py-1">
                <span class="text-muted small">@lang('Starting Amount')</span>
                <span class="fw-bold">{{ showAmount($cashRegister->starting_amount) }}</span>
            </li>
            <li class="d-flex justify-content-between align-items-center border-0 px-0 py-1">
                <span class="text-muted small">@lang('Starting Time')</span>
                <span class="fw-bold fs-13">{{ showDateTime($cashRegister->starting_time) }}</span>
            </li>
            <li class="d-flex justify-content-between flex-column border-0 px-0 py-1">
                <span class="text-muted small">@lang('Starting Note')</span>
                <span class="fs-13">{{ __(@$cashRegister->starting_note ?? 'N/A') }}</span>
            </li>
        </ul>
    </div>
    <div class="col-lg-4 col-sm-6 mb-3">
        <h6 class="text--primary border-bottom pb-2 mb-2">@lang('Closing Information')</h6>
        <ul class="list-group list-group-flush">
            <li class="d-flex justify-content-between align-items-center border-0 px-0 py-1">
                <span class="text-muted small">@lang('Closing Amount')</span>
                <span class="fw-bold">{{ showAmount($cashRegister->closing_amount) }}</span>
            </li>
            <li class="d-flex justify-content-between align-items-center border-0 px-0 py-1">
                <span class="text-muted small">@lang('Closing Time')</span>
                <span class="fw-bold fs-13">{{ showDateTime($cashRegister->closing_time) }}</span>
            </li>
            <li class="d-flex justify-content-between flex-column border-0 px-0 py-1">
                <span class="text-muted small">@lang('Closing Note')</span>
                <span class="fs-13">{{ __(@$cashRegister->closing_note ?? 'N/A') }}</span>
            </li>
        </ul>
    </div>
    <div class="col-lg-4 col-sm-12 mb-3">
        <h6 class="text--primary border-bottom pb-2 mb-2">@lang('User Information')</h6>
        <ul class="list-group list-group-flush">
            <li class="d-flex justify-content-between align-items-center border-0 px-0 py-1">
                <span class="text-muted small">@lang('Name')</span>
                <span class="fw-bold">{{ __(@$cashRegister->user->fullname ?? 'N/A') }}</span>
            </li>
            <li class="d-flex justify-content-between align-items-center border-0 px-0 py-1">
                <span class="text-muted small">@lang('Email')</span>
                <span class="fw-bold fs-13">{{ @$cashRegister->user->email ?? 'N/A' }}</span>
            </li>
            <li class="d-flex justify-content-between align-items-center border-0 px-0 py-1">
                <span class="text-muted small">@lang('Mobile')</span>
                <span class="fw-bold fs-13">{{ @$cashRegister?->user?->mobileNumber ?? 'N/A' }}</span>
            </li>
        </ul>
    </div>
</div>

<div class="table-responsive--md mb-4">
    <table class="table table--light style--two custom-data-table">
        <thead>
            <tr>
                <th>@lang('Payment Type')</th>
                <th>@lang('Total Sale')</th>
                <th>@lang('Total Expense')</th>
                <th>@lang('Other Credit')</th>
                <th>@lang('Other Debit')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($paymentTypes as $paymentType)
                <tr>
                    <td>{{ __(@$paymentType->name) }}</td>
                    <td class="fw-bold">{{ showAmount($paymentType->total_sale) }}</td>
                    <td class="text--danger">{{ showAmount($paymentType->total_expense) }}</td>
                    <td class="text-success">{{ showAmount($paymentType->total_other_credit) }}</td>
                    <td class="text--warning">{{ showAmount($paymentType->total_other_debit) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="row justify-content-end">
    <div class="col-lg-5 col-md-7 col-sm-12">
        <h6 class="text--primary border-bottom pb-2 mb-3">@lang('Register Summary')</h6>
        <ul class="list-group list-group-flush border">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="fw-bold">@lang('Total Sale')</span>
                <span class="fw-bold text--primary">{{ showAmount($paymentTypes->sum('total_sale')) }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="fw-bold">@lang('Total Expense')</span>
                <span class="fw-bold text--danger">{{ showAmount($paymentTypes->sum('total_expense')) }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="fw-bold">@lang('Total Other Credit')</span>
                <span class="fw-bold text--success">{{ showAmount($paymentTypes->sum('total_other_credit')) }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="fw-bold">@lang('Total Other Debit')</span>
                <span class="fw-bold text--warning">{{ showAmount($paymentTypes->sum('total_other_debit')) }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center bg--light">
                <h6 class="mb-0 fs-15 text-dark">@lang('Calculated Net Amount')</h6>
                <h6 class="mb-0 fs-15 text-dark">
                    {{ showAmount(
                        $cashRegister->starting_amount + 
                        $paymentTypes->sum('total_sale') + 
                        $paymentTypes->sum('total_other_credit') - 
                        $paymentTypes->sum('total_expense') - 
                        $paymentTypes->sum('total_other_debit')
                    ) }}
                </h6>
            </li>
        </ul>
    </div>
</div>
