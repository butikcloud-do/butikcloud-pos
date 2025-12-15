@props(['is_dark' => false])
@php
$gatewayCurrency = App\Models\GatewayCurrency::whereHas('method', function ($gate) {
    $gate->where('status', Status::ENABLE);
})
    ->with('method')
    ->orderby('name')
    ->get();

$generalSetting = gs();
@endphp
<div class="modal fade custom--modal @if ($is_dark) dark-modal @endif" id="purchaseModal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header px-md-5 py-4">
                <h5 class="modal-title">@lang('Plan Purchase Preview')</h5>
                <button type="button" class="close close-btn" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <div class="modal-body px-md-5 py-4">
                <form method="POST" action="#" class="no-submit-loader purchase-form">
                    @csrf
                    <input type="hidden" name="recurring_type" value="{{ Status::MONTHLY }}">
                    <input type="hidden" name="currency">
                    <div class="mb-4">
                        <label class="form-label">@lang('Recurring Type')</label>
                        <div class=" d-flex gap-2 flex-wrap">
                            <div class="custom__wrap flex-fill rounded py-4 border border--primary recurring-type"
                                data-type="{{ Status::MONTHLY }}">
                                <span class="duration fs-20">@lang('Monthly')</span>
                            </div>
                            <div class="custom__wrap flex-fill rounded py-4 recurring-type"
                                data-type="{{ Status::YEARLY }}">
                                <span class="price fs-20">@lang('Yearly')</span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">@lang('Payment Gateway')</label>
                        <select class="form--control select2" data-minimum-results-for-search="-1" name="gateway"
                            required>
                            <option selected>@lang('Select Payment Gateway')</option>
                            @foreach ($gatewayCurrency as $data)
                                <option data-gateway='@json($data)'
                                    value="{{ $data->method_code }}"@selected(old('gateway') == $data->method_code)>
                                    {{ __($data->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <ul class="list-group list-group-flush ">
                            <li class="list-group-item d-flex justify-content-between flex-wrap">
                                <span>@lang('Plan Price')</span>
                                <span>
                                    <span class="plan_price"></span>
                                    <span>{{ __($generalSetting->cur_text) }}</span>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between flex-wrap">
                                <span>@lang('Processing Fee')</span>
                                <span>
                                    <span class="precessing_fee" data-bs-toggle="tooltip"
                                        data-bs-title="@lang('Processing Fee')">
                                        0.00 {{ __($generalSetting->cur_text) }}
                                    </span>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between flex-wrap">
                                <span>@lang('Total Amount')</span>
                                <span>
                                    <span class="total_amount"></span>
                                    <span>{{ __($generalSetting->cur_text) }}</span>
                                </span>
                            </li>
                            <li
                                class="deposit-info gateway-conversion d-none list-group-item d-flex justify-content-between flex-wrap">
                                <span>
                                    @lang('Conversion')
                                </span>
                                <span class="conversion-amount">

                                </span>

                            </li>
                            <li class="deposit-info conversion-currency d-none total-amount  list-group-item">
                                <span>
                                    @lang('In') <span class="gateway-currency"></span>
                                </span>
                                <span class="in-currency"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between flex-wrap d-none crypto-message">
                                <span>
                                    @lang('Conversion with')
                                </span>
                                <div>
                                    <span class="gateway-currency"></span> @lang('and final value will Show on next step')
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="mb-4">
                        <button type="submit" class="btn btn--primary btn-large btn w-100 purchaseSubmitBtn">
                            <i class="la la-telegram"></i> @lang('Pay Now')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        "use strict";

        (function($) {

            const $purchaseModal = $('#purchaseModal');


            let selectedPlan = null;
            let amount = 0;

            let recurringTypeMonthly = parseInt("{{ Status::MONTHLY }}");
            let recurringTypeYearly = parseInt("{{ Status::YEARLY }}");

            window.recurringType = recurringTypeMonthly;
            let gateway;

            $('.purchaseBtn').on('click', function() {
                selectedPlan = $(this).data('plan');

                if (recurringType == recurringTypeMonthly) {
                    amount = parseFloat(selectedPlan.monthly_price);
                } else {
                    amount = parseFloat(selectedPlan.yearly_price);
                }

                const action = "{{ route('user.subscription.plan.purchase', ':id') }}";
                $purchaseModal.find('form').attr('action', action.replace(':id', selectedPlan.id));
                $(".plan_price").text(amount.toFixed(2));
                $(".total_amount").text(amount.toFixed(2));
                $purchaseModal.modal('show');
                calculation();

            });


            $(`select[name=gateway]`).on('change', function() {

                let gatewayElement = $(this).find('option:selected');
                let methodCode = gatewayElement.val();

                gateway = gatewayElement.data('gateway');
                calculation();

            });

            $(".recurring-type").on("click", function() {

                $(".recurring-type").removeClass('border border--primary');
                $(this).addClass('border border--primary');

                window.recurringType = $(this).data('type');

                if (window.recurringType == recurringTypeMonthly) {
                    amount = parseFloat(selectedPlan.monthly_price);
                } else {
                    amount = parseFloat(selectedPlan.yearly_price);
                }

                $('input[name="recurring_type"]').val(recurringType);
                $(".plan_price").text(amount.toFixed(2));
                $(".total_amount").text(amount.toFixed(2));

                calculation();
            });


            function calculation() {

                if (!gateway) return;

                let percentCharge = 0;
                let fixedCharge = 0;
                let totalPercentCharge = 0;


                percentCharge = parseFloat(gateway.percent_charge);
                fixedCharge = parseFloat(gateway.fixed_charge);
                totalPercentCharge = parseFloat(amount / 100 * percentCharge);


                let totalCharge = parseFloat(totalPercentCharge + fixedCharge);
                let totalAmount = parseFloat((amount || 0) + totalPercentCharge + fixedCharge);


                let processingFeeInfo =
                    `${parseFloat(gateway.percent_charge).toFixed(2)}% with ${parseFloat(gateway.fixed_charge).toFixed(2)} {{ __($generalSetting->cur_text) }} charge for payment gateway processing fees`


                $(".precessing_fee").attr("data-bs-original-title", processingFeeInfo);
                $(".precessing_fee").attr("data-bs-title", processingFeeInfo);


                $(".total_amount").text(totalAmount.toFixed(2));
                $(".precessing_fee").text(totalCharge.toFixed(2) + " " + "{{ __($generalSetting->cur_text) }}");

                $("input[name=currency]").val(gateway.currency);
                $(".gateway-currency").text(gateway.currency);


                if (gateway.currency != "{{ $generalSetting->cur_text }}" && gateway.method.crypto != 1) {

                    $(".gateway-conversion, .conversion-currency").removeClass('d-none');

                    $(".gateway-conversion")
                        .find('.conversion-amount')
                        .html(
                            `1 {{ __($generalSetting->cur_text) }} = <span class="rate">${parseFloat(gateway.rate).toFixed(2)}</span>  <span class="method_currency">${gateway.currency}</span>`
                        );

                    $('.in-currency').text(parseFloat(totalAmount * gateway.rate).toFixed(gateway.method.crypto == 1 ?
                        8 : 2))
                } else {
                    $(".gateway-conversion, .conversion-currency").addClass('d-none');
                    $('.deposit-form').removeClass('adjust-height')
                }

                if (gateway.method.crypto == 1) {
                    $('.crypto-message').removeClass('d-none');
                } else {
                    $('.crypto-message').addClass('d-none');
                }

                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            }


        })(jQuery);
    </script>
@endpush



@push('style')
    <style>
        .custom__wrap {
            background-color:  #f5f5f5;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            cursor: pointer;

        }

        [data-theme="dark"] .custom__wrap {
            background-color: #25293c;
        }
    </style>
@endpush
