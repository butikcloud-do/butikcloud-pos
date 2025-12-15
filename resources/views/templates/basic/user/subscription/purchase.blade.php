@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="container ">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <form action="{{ route('user.subscription.plan.purchase.insert') }}" method="post"
                    class="deposit-form no-submit-loader">
                    @csrf
                    <input type="hidden" name="currency">
                    <div class="gateway-card">
                        <div class="row justify-content-center gy-sm-4 gy-3">
                            <div class="col-lg-6">
                                <div class="card custom--card">
                                    <div class="card-header">
                                        <h4>@lang('Purchase Plan Information')</h4>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>@lang('Plan Name')</span>
                                                <span>{{ __(@$plan->name) }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>@lang('Plan Price')</span>
                                                <span>{{ showAmount(@$plan->monthly_price, forceDefault: true) }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>@lang('Duration')</span>
                                                <span>
                                                    @if (session('plan_recurring') == Status::YEARLY)
                                                        1 @lang('Year')
                                                    @else
                                                        1 @lang('Month')
                                                    @endif
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card custom--card">
                                    <div class="card-header">
                                        <h4>@lang('Available Payment Gateways')</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="payment-system-list is-scrollable gateway-option-list">
                                            @foreach ($gatewayCurrency as $data)
                                                <label for="{{ titleToKey($data->name) }}"
                                                    class="payment-item @if ($loop->index > 4) d-none @endif gateway-option">
                                                    <div class="payment-item__info">
                                                        <span class="payment-item__check"></span>
                                                        <span class="payment-item__name">{{ __($data->name) }}</span>
                                                    </div>
                                                    <div class="payment-item__thumb">
                                                        <img class="payment-item__thumb-img"
                                                            src="{{ getImage(getFilePath('gateway') . '/' . $data->method->image) }}"
                                                            alt="@lang('payment-thumb')">
                                                    </div>
                                                    <input class="payment-item__radio gateway-input"
                                                        id="{{ titleToKey($data->name) }}" hidden
                                                        data-gateway='@json($data)' type="radio"
                                                        name="gateway" value="{{ $data->method_code }}"
                                                        @checked(old('gateway', $loop->first) == $data->method_code)
                                                        data-min-amount="{{ showAmount($data->min_amount, forceDefault: true) }}"
                                                        data-max-amount="{{ showAmount($data->max_amount, forceDefault: true) }}">
                                                </label>
                                            @endforeach
                                            @if ($gatewayCurrency->count() > 4)
                                                <button type="button" class="payment-item__btn more-gateway-option">
                                                    <p class="payment-item__btn-text">@lang('Show All Payment Options')</p>
                                                    <span class="payment-item__btn__icon"><i
                                                            class="fas fa-chevron-down"></i></span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn--primary btn-large w-100">
                                    @lang('Proceed to Payment')
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {

            var amount = parseFloat($('.amount').val() || 0);
            var gateway, minAmount, maxAmount;


            $('.amount').on('input', function(e) {
                amount = parseFloat($(this).val());
                if (!amount) {
                    amount = 0;
                }
                calculation();
            });

            $('.gateway-input').on('change', function(e) {
                gatewayChange();
            });

            function gatewayChange() {
                let gatewayElement = $('.gateway-input:checked');
                let methodCode = gatewayElement.val();

                gateway = gatewayElement.data('gateway');
                minAmount = gatewayElement.data('min-amount');
                maxAmount = gatewayElement.data('max-amount');

                let processingFeeInfo =
                    `${parseFloat(gateway.percent_charge).toFixed(2)}% with ${parseFloat(gateway.fixed_charge).toFixed(2)} {{ __(gs('cur_text')) }} charge for payment gateway processing fees`
                $(".proccessing-fee-info").attr("data-bs-original-title", processingFeeInfo);
                calculation();
            }

            gatewayChange();

            $(".more-gateway-option").on("click", function(e) {
                let paymentList = $(".gateway-option-list");
                paymentList.find(".gateway-option").removeClass("d-none");
                $(this).addClass('d-none');
                paymentList.animate({
                    scrollTop: (paymentList.height() - 60)
                }, 'slow');
            });

            function calculation() {
                if (!gateway) return;
                $(".gateway-limit").text(minAmount + " - " + maxAmount);

                let percentCharge = 0;
                let fixedCharge = 0;
                let totalPercentCharge = 0;

                if (amount) {
                    percentCharge = parseFloat(gateway.percent_charge);
                    fixedCharge = parseFloat(gateway.fixed_charge);
                    totalPercentCharge = parseFloat(amount / 100 * percentCharge);
                }

                let totalCharge = parseFloat(totalPercentCharge + fixedCharge);
                let totalAmount = parseFloat((amount || 0) + totalPercentCharge + fixedCharge);

                $(".final-amount").text(totalAmount.toFixed(2));
                $(".processing-fee").text(totalCharge.toFixed(2));
                $("input[name=currency]").val(gateway.currency);
                $(".gateway-currency").text(gateway.currency);

                if (amount < Number(gateway.min_amount) || amount > Number(gateway.max_amount)) {
                    $(".deposit-form button[type=submit]").attr('disabled', true);
                } else {
                    $(".deposit-form button[type=submit]").removeAttr('disabled');
                }

                if (gateway.currency != "{{ gs('cur_text') }}" && gateway.method.crypto != 1) {
                    $('.deposit-form').addClass('adjust-height')

                    $(".gateway-conversion, .conversion-currency").removeClass('d-none');
                    $(".gateway-conversion").find('.deposit-info__input .text').html(
                        `1 {{ __(gs('cur_text')) }} = <span class="rate">${parseFloat(gateway.rate).toFixed(2)}</span>  <span class="method_currency">${gateway.currency}</span>`
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
            }

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
            $('.gateway-input').change();
        })(jQuery);
    </script>
@endpush
