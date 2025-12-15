@php
    $request = request();
    $remarks = App\Models\Transaction::distinct('remark')->orderBy('remark')->get('remark');
    $paymentTypes = App\Models\PaymentType::orderBy('name')->get();
@endphp

<form action="" id="filter-form">
    <div class="form-group">
        <label>@lang('Payment Type')</label>
        <select class="form-control select2" data-minimum-results-for-search="-1" name="payment_type">
            <option value="">@lang('All')</option>
            @foreach ($paymentTypes as $paymentType)
                <option value="{{ $paymentType->id }}" @selected($paymentType->id == $request->payment_type)>
                    {{ __($paymentType->name) }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label>@lang('Transaction Type')</label>
        <select name="trx_type" class="form-control select2" data-minimum-results-for-search="-1">
            <option value="">@lang('All')</option>
            <option value="+" @selected($request->trx_type == '+')>@lang('Plus')</option>
            <option value="-" @selected($request->trx_type == '-')>@lang('Minus')</option>
        </select>
    </div>
    <div class="form-group">
        <label>@lang('Remark')</label>
        <select class="form-control select2" data-minimum-results-for-search="-1" name="remark">
            <option value="">@lang('All')</option>
            @foreach ($remarks as $remark)
                <option value="{{ $remark->remark }}" @selected($request->remark == $remark->remark)>
                    {{ __(keyToTitle($remark->remark)) }}</option>
            @endforeach
        </select>
    </div>
    <x-panel.other.filter_date />
    <x-panel.other.order_by />
    <x-panel.other.per_page_record />
    <x-panel.other.filter_dropdown_btn />
</form>
