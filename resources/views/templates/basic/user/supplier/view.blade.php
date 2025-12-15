@extends($activeTemplate . 'layouts.master')
@section('panel')
    @include('Template::user.supplier.widget')
    <div class="row responsive-row">
        <div class="col-lg-6">
            <x-panel.ui.card class="h-100">
                <x-panel.ui.card.header class="d-flex gap-1 flex-wrap justify-content-between align-items-center">
                    <h4 class="card-title">@lang('Purchase History')</h4>
                    <form method="GET" action="{{ url()->current() }}">
                        <div class="input-group input--group">
                            <input type="text" name="purchase_search" value="{{ request()->purchase_search }}"
                                placeholder="@lang('Search Purchases')" class="form-control custom--padding">
                            <button type="submit" class="search-btn input-group-text">
                                <i class="las la-search"></i>
                            </button>
                        </div>
                    </form>

                </x-panel.ui.card.header>
                <x-panel.ui.card.body class="p-0">
                    <x-panel.ui.table>
                        <x-panel.ui.table.header>
                            <tr>
                                <th>@lang('Invoice Number')</th>
                                <th>@lang('Purchase Date') | @lang('Created at')</th>
                                <th>@lang('Total Amount')</th>
                                <th>@lang('Status')</th>
                            </tr>
                        </x-panel.ui.table.header>
                        <x-panel.ui.table.body>
                            @forelse($purchases as $purchase)
                                <tr>
                                    <td>
                                        <a
                                            href="{{ route('user.purchase.view', $purchase->id) }}">{{ $purchase->invoice_number }}</a>
                                    </td>
                                    <td>
                                        <div>
                                            <span
                                                class="d-block">{{ showDateTime($purchase->purchase_date, 'Y-m-d') }}</span>
                                            <span>{{ showDateTime($purchase->created_at) }}</span>
                                        </div>
                                    </td>
                                    <td>{{ showAmount($purchase->total) }}</td>
                                    <td>@php echo $purchase->statusBadge @endphp</td>
                                </tr>
                            @empty
                                <x-panel.ui.table.empty_message />
                            @endforelse
                        </x-panel.ui.table.body>
                    </x-panel.ui.table>
                    @if ($purchases->hasPages())
                        <div class="p-3">
                            {{ paginateLinks($purchases) }}
                        </div>
                    @endif
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>

        <div class="col-lg-6">
            <x-panel.ui.card class="h-100">
                <x-panel.ui.card.header class="d-flex gap-1 flex-wrap justify-content-between align-items-center">
                    <h4 class="card-title">@lang('Payment History')</h4>
                    <form method="GET" action="{{ url()->current() }}">
                        <div class="input-group input--group">
                            <input type="text" name="payment_search" value="{{ request()->payment_search }}"
                                placeholder="@lang('Search Payments')" class="form-control custom--padding">
                            <button type="submit" class="search-btn input-group-text">
                                <i class="las la-search"></i>
                            </button>
                        </div>
                    </form>

                </x-panel.ui.card.header>
                <x-panel.ui.card.body :paddingZero=true>
                    <x-panel.ui.table>
                        <x-panel.ui.table.header>
                            <tr>
                                <th>@lang('Payment Date') | @lang('Created at')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Payment Method')</th>
                            </tr>
                        </x-panel.ui.table.header>
                        <x-panel.ui.table.body>
                            @forelse($payments as $payment)
                                <tr>
                                    <td>
                                        <div>
                                            <span
                                                class="d-block">{{ showDateTime($payment->payment_date, 'Y-m-d') }}</span>
                                            <span>{{ showDateTime($payment->created_at) }}</span>
                                        </div>
                                    </td>
                                    <td>{{ showAmount($payment->amount) }}</td>
                                    <td>{{ @$payment->paymentType->name }}</td>
                                </tr>
                            @empty
                                <x-panel.ui.table.empty_message />
                            @endforelse
                        </x-panel.ui.table.body>
                    </x-panel.ui.table>
                    @if ($payments->hasPages())
                        <div class="p-3">
                            {{ paginateLinks($payments) }}
                        </div>
                    @endif
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>



    <x-panel.ui.modal id="payment-modal" class="modal-xl">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add Payment')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-panel.ui.modal.header>
        <x-panel.ui.modal.body>
            <form action="{{ route('user.supplier.add.payment', $supplier->id) }}" method="POST">
                @csrf
                <div class="row gy-4">
                    <div class="col-12">
                        <div class="row purchase-supplier-info"></div>
                    </div>
                    <div class="col-12">
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label>@lang('Paid Amount')</label>
                                <div class="input-group input--group">
                                    <input type="number" step="any" class="form-control" name="paid_amount"
                                        placeholder="@lang('0.00')" required>
                                    <span class="input-group-text">
                                        {{ __(gs('cur_text', getParentUser()->id)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label>@lang('Paid Date')</label>
                                <div class="input-group input--group">
                                    <input type="text" class="form-control date-picker" name="paid_date"
                                        value="{{ date('Y-m-d') }}" required>
                                    <span class="input-group-text">
                                        <i class="las la-calendar"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group col-lg-12">
                                <label>@lang('Payment Type')</label>
                                <select name="payment_type" class="form-control select2">
                                    <option value="" selected disabled>@lang('Select Option')</option>
                                    @foreach ($paymentMethods as $paymentMethod)
                                        <option value="{{ $paymentMethod->id }}">
                                            {{ __($paymentMethod->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-group col-lg-12 mb-0">
                                <label>@lang('Payment Note')</label>
                                <textarea class="form-control" name="payment_note"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <x-panel.ui.btn.modal />
                        </div>
                    </div>
                </div>
            </form>
        </x-panel.ui.modal.body>
    </x-panel.ui.modal>
@endsection


@push('breadcrumb-plugins')
    <div class="d-flex gap-2">
        <x-staff_permission_check permission="add purchase payment">
            <x-panel.ui.btn.add data-bs-toggle="modal" data-bs-target="#payment-modal" text="Add Payment" />
        </x-staff_permission_check>
        <x-back_btn route="{{ route('user.supplier.list') }}" />
    </div>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/flatpickr.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/global/css/flatpickr.min.css') }}">
@endpush

@push('script')
    <script>
        $(".date-picker").flatpickr({
            maxDate: new Date()
        });
    </script>
@endpush

@push('style')
    <style>
        .custom--pading {
            padding: 4px 20px !important;
        }
    </style>
@endpush
