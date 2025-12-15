@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <div class="card responsive-filter-card mb-4">
                <div class="card-body">
                    <form>
                        <div class="d-flex flex-wrap gap-4">

                            @if (!request()->routeIs('user.transactions'))
                                <div class="flex-grow-1">
                                    <label class="form-label">@lang('Transaction Number')</label>
                                    <input type="search" name="search" value="{{ request()->search }}" class="form-control form--control">
                                </div>
                            @endif

                            @if (request()->routeIs('user.transactions'))

                                <div class="flex-grow-1">
                                    <label class="form-label">@lang('Payment Type')</label>
                                    <select class="form-select form--control select2" name="payment_type_id" data-minimum-results-for-search="-1">

                                        <option value="">@lang('All')</option>

                                        @foreach ($paymentTypes as $type)
                                            <option value="{{ $type->id }}" @selected(request()->payment_type_id == $type->id)>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                            @endif


                            <div class="flex-grow-1 select2-parent">
                                <label class="form-label d-block">@lang('Transaction Type')</label>
                                <select name="trx_type" class="form-select form--control select2" data-minimum-results-for-search="-1">
                                    <option value="">@lang('All')</option>
                                    <option value="+" @selected(request()->trx_type == '+')>@lang('Plus')
                                    </option>
                                    <option value="-" @selected(request()->trx_type == '-')>@lang('Minus')
                                    </option>
                                </select>
                            </div>
                            <div class="flex-grow-1 select2-parent">
                                <label class="form-label d-block">@lang('Remark')</label>
                                <select class="form-select form--control select2" data-minimum-results-for-search="-1" name="remark">
                                    <option value="">@lang('All')</option>
                                    @foreach ($remarks as $remark)
                                        <option value="{{ $remark->remark }}" @selected(request()->remark == $remark->remark)>
                                            {{ __(keyToTitle($remark->remark)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-44"><i class="las la-filter"></i>
                                    @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card ">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table ">
                            <thead>
                                <tr>
                                    <th>@lang('Trx')</th>
                                    <th>@lang('Transacted')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Charge')</th>
                                    <th>@lang('Post Balance')</th>
                                    <th>@lang('Detail')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $trx)
                                    <tr>
                                        <td>
                                            <strong>{{ $trx->trx }}</strong>
                                        </td>
                                        <td>
                                            <div class="text-end text-md-center">
                                                {{ showDateTime($trx->created_at) }}<br>{{ diffForHumans($trx->created_at) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="fw-bold @if ($trx->trx_type == '+') text--success @else text--danger @endif">
                                                    {{ $trx->trx_type }} {{ showAmount($trx->amount) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            {{ showAmount($trx->charge) }}
                                        </td>
                                        <td>
                                            {{ showAmount($trx->post_balance) }}
                                        </td>
                                        <td>{{ __($trx->details) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">
                                            {{ __($emptyMessage) }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($transactions->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($transactions) }}
                    </div>
                @endif
            </div>
        </div>
@endsection

    @push('style')
        <style>
            .select2-container {
                width: 100% !important;
            }
        </style>
    @endpush