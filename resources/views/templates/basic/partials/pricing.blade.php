@php
    @$subscriptionPlans = \App\Models\SubscriptionPlan::active()->orderBy('monthly_price', 'asc')->get();
    @$user = auth()->user();
@endphp

@foreach ($subscriptionPlans ?? [] as $subscriptionPlan)
    <div class="col-lg-4 col-md-6 wow animationfadeUp" data-wow-delay="0.6s">
        <div class="pricing-card">
            <div class="pricing-card__top">
                <h4 class="pricing-card__title">{{ __(@$subscriptionPlan->name) }}</h4>
                <p class="pricing-card__desc">{{ __(@$subscriptionPlan->description) }}</p>
            </div>
            <h2 class="pricing-card__number">
                <span class="monthly_price">
                    <span
                        class="currency-type">{{ gs('cur_sym') }}</span>{{ showAmount(@$subscriptionPlan->monthly_price, currencyFormat: false) }}
                </span>
                <span class="yearly_price d-none">
                    <span
                        class="currency-type">{{ gs('cur_sym') }}</span>{{ showAmount(@$subscriptionPlan->yearly_price, currencyFormat: false) }}
                </span>
            </h2>
            <ul class="pricing-list">
                <li class="pricing-list__item justify-content-between">
                    <span class="d-flex gap-2">
                        <span class="pricing-list__item-icon fs-16">
                            <i class="las la-user-friends"></i>
                        </span>
                        @lang('Product Limit')
                    </span>
                    <span>{{ printLimit($subscriptionPlan->product_limit) }}</span>
                </li>
                <li class="pricing-list__item justify-content-between">
                    <span class="d-flex gap-2">
                        <span class="pricing-list__item-icon fs-16">
                            <i class="las la-user-tie"></i>
                        </span>
                        @lang('Staff Limit')
                    </span>
                    <span>{{ printLimit($subscriptionPlan->user_limit) }}</span>
                </li>
                <li class="pricing-list__item justify-content-between">
                    <span class="d-flex gap-2">
                        <span class="pricing-list__item-icon fs-16">
                            <i class="las la-address-book"></i>
                        </span>
                        @lang('Warehouse Limit')
                    </span>
                    <span>{{ printLimit($subscriptionPlan->warehouse_limit) }}</span>
                </li>
                <li class="pricing-list__item justify-content-between">
                    <span class="d-flex gap-2">
                        <span class="pricing-list__item-icon fs-16">
                            <i class="las la-copy"></i>
                        </span>
                        @lang('Supplier Limit')
                    </span>
                    <span>{{ printLimit($subscriptionPlan->supplier_limit) }}</span>
                </li>

                <li class="pricing-list__item justify-content-between">
                    <span class="d-flex gap-2">
                        <span class="pricing-list__item-icon fs-16">
                            <i class="lab la-rocketchat"></i>
                        </span>
                        @lang('Coupon Limit')
                    </span>
                    <span>{{ printLimit($subscriptionPlan->coupon_limit) }}</span>
                </li>
                <li class="pricing-list__item justify-content-between">
                    <span class="d-flex gap-2">
                        <span class="pricing-list__item-icon fs-16">
                            <i class="las la-smile"></i>
                        </span>
                        @lang('HRM Access')
                    </span>
                    @if ($subscriptionPlan->hrm_access)
                        <span class="text--success">@lang('Yes')</span>
                    @else
                        <span class="text--danger">@lang('No')</span>
                    @endif
                </li>
                <li class="pricing-list__item justify-content-between">
                    <span class="d-flex gap-2">
                        <span class="pricing-list__item-icon fs-16">
                            <i class="las la-users"></i>
                        </span>
                        @lang('Customer Limit') :
                    </span>
                    <span class="text--success">@lang('Unlimited')</span>
                </li>
                <li class="pricing-list__item justify-content-between">
                    <span class="d-flex gap-2">
                        <span class="pricing-list__item-icon fs-16">
                            <i class="las la-users"></i>
                        </span>
                        @lang('Report')
                    </span>
                    <span class="text--success">@lang('Unlimited')</span>
                </li>
                <li class="pricing-list__item justify-content-between">
                    <span class="d-flex gap-2">
                        <span class="pricing-list__item-icon fs-16">
                            <i class="las la-users"></i>
                        </span>
                        @lang('Inventory')
                    </span>
                    <span class="text--success">@lang('Unlimited')</span>
                </li>
                <li class="pricing-list__item justify-content-between">
                    <span class="d-flex gap-2">
                        <span class="pricing-list__item-icon fs-16">
                            <i class="las la-users"></i>
                        </span>
                        @lang('Multi Language Support')
                    </span>
                    <span class="text--success">@lang('Yes')</span>
                </li>

            </ul>
            <div class="pricing-card__btn">
                <button class="btn btn--primary btn-large w-100 purchaseBtn" data-plan='@json($subscriptionPlan)'>
                    @if (@$user->plan_id == $subscriptionPlan->id)
                        @lang('Renew Now')
                    @else
                        @lang('Buy Now')
                    @endif
                </button>
            </div>
        </div>
    </div>
@endforeach
