@php
    $pricingContent = getContent('pricing.content', true);
    $plans = App\Models\SubscriptionPlan::active()->orderby('monthly_price', 'asc')->get();
@endphp

<section class="pricing-plan pricing py-120 bg-light-shape">
    <div class="container">
        <div class="col-lg-12">
            <div class="section-heading">
                <h2 class="section-heading__title wow animationfadeUp" data-highlight="2" data-wow-delay="0.3s">
                    {{ __($pricingContent->data_values->heading ?? '') }}
                </h2>
                <p class="section-heading__desc wow animationfadeUp" data-wow-delay="0.4s">
                    {{ __($pricingContent->data_values->subheading ?? '') }}
                </p>
            </div>
        </div>

        <div class="row justify-content-center gy-4">
            <div class="pricing-billing-priod wow animationfadeUp" data-wow-delay="0.6s">
                <span class="pricing-billing-priod__label">@lang('Monthly')</span>
                <div class="form--switch gradient">
                    <input class="form-check-input recurringType" type="checkbox">
                </div>
                <span class="pricing-billing-priod__label">@lang('Yearly')</span>
            </div>
            @foreach ($plans as $plan)
                <div class="col-md-6 col-lg-4">
                    <div class="pricing-card  wow animationfadeUp" data-wow-delay="0.4s">
                        <div class="pricing-card__header">
                            <h5 class="pricing-card__title">{{ __($plan->name) }}</h5>
                            <p class="pricing-card__desc">{{ __($plan->description) }}</p>
                            <h2 class="pricing-card__price">
                                <span class="pricing-card__symbol">{{ gs('cur_sym') }}</span>
                                <span class="pricing-card__total">
                                    <span class="monthly-price monthly_price"> {{ getAmount($plan->monthly_price) }}
                                    </span>
                                    <span class="yearly_price d-none"> {{ getAmount($plan->yearly_price) }} </span>
                                </span>
                            </h2>
                        </div>
                        <div class="pricing-card__body">
                            <div class="pricing-card-block">
                                <ul class="pricing-card-info">
                                    <li class="pricing-card-info__item">
                                        <span>@lang('Product Limit :')</span>
                                        {{ printLimit($plan->product_limit) }}
                                    </li>
                                    <li class="pricing-card-info__item">
                                        <span>@lang('User Limit :')</span>
                                        {{ printLimit($plan->user_limit) }}
                                    </li>
                                    <li class="pricing-card-info__item">
                                        <span>@lang('Warehouse Limit :')</span>
                                        {{ printLimit($plan->warehouse_limit) }}
                                    </li>
                                    <li class="pricing-card-info__item">
                                        <span>@lang('Supplier Limit :')</span>
                                        {{ printLimit($plan->supplier_limit) }}
                                    </li>
                                    <li class="pricing-card-info__item">
                                        <span>@lang('Coupon Limit :')</span>
                                        {{ printLimit($plan->coupon_limit) }}
                                    </li>
                                    <li class="pricing-card-info__item ">
                                        <span>@lang('Customer Limit'):</span>
                                        <span>@lang('Unlimited')</span>
                                    </li>
                                    <li
                                        class="pricing-card-info__item @if ($plan->hrm_access) yes @else no @endif">
                                        <span>@lang('HRM Access')</span>
                                    </li>
                                    <li class="pricing-card-info__item yes">
                                        <span>@lang('Unlimited Report')</span>
                                    </li>
                                    <li class="pricing-card-info__item yes">
                                        <span>@lang('Inventory Management')</span>
                                    </li>
                                    <li class="pricing-card-info__item yes">
                                        <span>@lang('Multi Language Support')</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="pricing-card__footer">
                            @auth
                                <button type="button" class="w-100 btn btn--dark purchaseBtn"
                                    data-plan='@json($plan)'>
                                    @if (auth()->user()->plan_id == $plan->id)
                                        @lang('Renew Now')
                                    @else
                                        @lang('Purchase Now')
                                    @endif
                                </button>
                            @else
                                <a class="w-100 btn btn--dark" href="{{ route('user.login') }}">
                                    @lang('Purchase Now')
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@auth
    <x-purchase_modal />
@endauth


@push('script')
    <script>
        "use strict";
        (function($) {
            $('.recurringType').on('change', function() {
            
                if (this.checked) {
                    window.recurringType = parseInt("{{ Status::YEARLY }}");
                    $('.yearly_price').removeClass('d-none');
                    $('.monthly_price').addClass('d-none');

                    $(".recurring-type").last().addClass('border border--primary');
                    $(".recurring-type").first().removeClass('border border--primary');

                } else {
                    window.recurringType = parseInt("{{ Status::MONTHLY }}");
                    $('.yearly_price').addClass('d-none');
                    $('.monthly_price').removeClass('d-none');

                    $(".recurring-type").last().removeClass('border border--primary');
                    $(".recurring-type").first().addClass('border border--primary');
                }
            });
        })
        (jQuery);
    </script>
@endpush

@push('style')
    <style>
        .custom__wrap {
            background-color: hsl(var(--gray-d-300));
            border: 1px solid hsl(var(--gray-d-300));
        }
        .list-group {
            --bs-list-group-color: hsl(var(--body-color));
            --bs-list-group-bg: hsl(var(--gray-d-300));
            border-radius: 6px !important;
            overflow: hidden;
        }

        .list-group-item:not(:first-child) {
            border-top: var(--bs-list-group-border-width) dashed hsl(var(--white)/0.1) !important;
        }

        .list-group-item span:last-child {
            color: hsl(var(--white));
            font-weight: 600
        }
    </style>
@endpush
