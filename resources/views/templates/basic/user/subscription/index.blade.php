@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="dashboard-container">
        <div class="container-top">
            <ul class="nav nav-pills custom--tab tab-three" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#subscription" type="button"
                        role="tab" data-tab-id="subscription">
                        @lang('Subscription Info')
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-plans" type="button"
                        role="tab" data-tab-id="pricing-plans">
                        @lang('Pricing Plans')
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#purchase-history" type="button"
                        role="tab" data-tab-id="purchase-history">
                        @lang('Purchase History')
                    </button>
                </li>
            </ul>
        </div>
        <div class="dashboard-container__body mt-4">
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="subscription" role="tabpanel">
                    @if ($plan && userSubscriptionExpiredCheck())
                        <div class="card">
                            <div class="card-body">
                                <div class="plan-wrapper">
                                    <div class="active-card">
                                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                            <span class="active-card__badge">
                                                @lang('MY ACTIVE PLAN')
                                            </span>
                                        </div>
                                        <div class="active-card__top">
                                            <h4 class="active-card__title"> {{ __(@$plan->name) }}</h4>
                                            <p class="active-card__desc">
                                                {{ __(@$plan->description) }}
                                            </p>
                                        </div>
                                        <div class="active-card__content">
                                            <ul class="text-list flex-column p-0">
                                                <li class="text-list__item  justify-content-between gap-1 flex-wrap">
                                                    <span class="active-plan-title">@lang('Total Amount')</span>
                                                    <span class="text--base fs-14">
                                                        {{ showAmount(@$activeSubscription->amount, forceDefault: true) }}
                                                    </span>
                                                </li>
                                                <li class="text-list__item justify-content-between gap-1 flex-wrap">
                                                    <span class="active-plan-title">@lang('Billing Cycle')</span>
                                                    <span class="text--base fs-14">
                                                        {{ __(@$activeSubscription->billing_cycle) }}
                                                    </span>
                                                </li>
                                                <li class="text-list__item justify-content-between gap-1 flex-wrap">
                                                    <span class="active-plan-title">@lang('Purchase At')</span>
                                                    <span class="text--base fs-14">
                                                        {{ showDateTime($activeSubscription->created_at) }}
                                                    </span>
                                                </li>
                                                <li class="text-list__item justify-content-between gap-1 flex-wrap">
                                                    <span class="active-plan-title">@lang('Activated On')</span>
                                                    <span class="text--base fs-14">
                                                        {{ showDateTime($activeSubscription->created_at) }}
                                                    </span>
                                                </li>
                                                <li class="text-list__item justify-content-between gap-1 flex-wrap">
                                                    @lang('Next Billing Date') <span class="text--base fs-14">
                                                        {{ showDateTime($user->plan_expired_at) }}
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="active-card__bottom">
                                            <button class="btn btn--primary btn-large w-100  purchaseBtn"
                                                data-plan='@json($plan)'
                                                data-subscription='@json($activeSubscription)'>
                                                @lang('Renew Now')
                                            </button>
                                        </div>
                                    </div>
                                    <div class="plan-wrapper__right">
                                        <div class="plan-wrapper__top mb-4">
                                            <h5 class="title mb-1"> @lang('Feature Remaining Information') </h5>
                                            <p class="plan-wrapper__desc">@lang('Stay informed about upcoming capabilities and progress')</p>
                                        </div>
                                        <div class="plan-details">
                                            <div class="plan-details__item">
                                                <span class="item-title">@lang('Product Limit')</span>
                                                {{ printLimit($user->product_limit) }}
                                            </div>
                                            <div class="plan-details__item">
                                                <span class="item-title">@lang('User Limit')</span>
                                                {{ printLimit(@$user->user_limit) }}
                                            </div>
                                            <div class="plan-details__item">
                                                <span class="item-title">@lang('Warehouse Limit')</span>
                                                {{ printLimit(@$user->warehouse_limit) }}
                                            </div>
                                            <div class="plan-details__item">
                                                <span class="item-title">@lang('Supplier Limit')</span>
                                                {{ printLimit(@$user->supplier_limit) }}
                                            </div>
                                            <div class="plan-details__item">
                                                <span class="item-title">@lang('Coupon Limit')</span>
                                                {{ printLimit(@$user->coupon_limit) }}
                                            </div>
                                            <div class="plan-details__item">
                                                <span class="item-title">@lang('HRM Access')</span>
                                                @if ($user->hrm_access)
                                                    <span class="text--success">@lang('Yes')</span>
                                                @else
                                                    <span class="text--danger">@lang('No')</span>
                                                @endif
                                            </div>
                                            <div class="plan-details__item">
                                                <span class="item-title">
                                                    @lang('Customer Limit'):
                                                </span>
                                                <span class="text--success">@lang('Unlimited')</span>
                                            </div>
                                            <div class="plan-details__item">
                                                <span class="item-title">
                                                    @lang('Report')
                                                </span>
                                                <span class="text--success">@lang('Unlimited')</span>
                                            </div>
                                            <div class="plan-details__item">
                                                <span class="item-title">
                                                    @lang('Inventory')
                                                </span>
                                                <span class="text--success">@lang('Unlimited')</span>
                                            </div>
                                            <div class="plan-details__item border-0">
                                                <span class="item-title">
                                                    @lang('Multi Language Support')
                                                </span>
                                                <span class="text--success">@lang('Yes')</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="p-5">
                                    <img src="{{ asset('assets/images/empty_box.png') }}" class="empty-message">
                                    <span class="d-block">@lang('You don\'t have any active subscription')</span>
                                    <span class="d-block fs-13 text-muted">@lang('There are no available data to display on this table at the moment.')</span>
                                    <a class="btn btn--primary btn-large btn-shadow mt-4"
                                        href="{{ route('user.subscription.index') }}?tab=pricing-plans">
                                        <i class="fa-solid fa-paper-plane"></i> @lang('Purchase Now')
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="tab-pane fade" id="pills-plans" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div class="pricing-card-top">
                                <p class="pricing-card-top__text">@lang('Monthly')</p>
                                <div class="form--switch">
                                    <input class="form-check-input recurringType " type="checkbox" role="switch" />
                                </div>
                                <p class="pricing-card-top__text">
                                    @lang('Yearly')</span>
                                </p>
                            </div>
                            <div class="row gy-4 justify-content-center align-items-center">
                                @include('Template::partials.pricing', ['cardTwo' => 'card-two'])
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="purchase-history" role="tabpanel">
                    <x-panel.ui.card>
                        <x-panel.ui.card.body class="p-0">
                            <x-panel.ui.table.layout :renderFilterOption="false" :renderExportButton="false">
                                <x-panel.ui.table>
                                    <x-panel.ui.table.header>
                                        <tr>
                                            <th>@lang('Plan Name')</th>
                                            <th>@lang('Purchase Price')</th>
                                            <th>@lang('Purchase Date')</th>
                                            <th>@lang('Expiration Date')</th>
                                            <th>@lang('Action')</th>
                                        </tr>
                                    </x-panel.ui.table.header>

                                    <x-panel.ui.table.body>
                                        @forelse ($subscriptions as $subscription)
                                                <tr>
                                                    <td>
                                                        <span class="d-block text--success">
                                                            {{ __(@$subscription->subscriptionPlan->name) }}
                                                        </span>
                                                    </td>

                                                    <td>
                                                        {{ showAmount(@$subscription->amount, forceDefault: true) }}
                                                    </td>

                                                    <td>
                                                        <span>
                                                            {{ showDateTime(@$subscription->created_at ) }}
                                                        </span>
                                                    </td>

                                                    <td>
                                                        <span class="text--warning">
                                                            {{ showDateTime(@$subscription->expired_at) }}
                                                        </span>
                                                    </td>

                                                    <td>
                                                        <a href="{{ route('user.subscription.invoice', @$subscription->id) }}"
                                                            class="btn btn--primary btn-large btn-shadow">
                                                            <i class="las la-eye"></i> @lang('View Invoice')
                                                        </a>
                                                    </td>

                                                </tr>
                                        @empty
                                            <x-panel.ui.table.empty_message />
                                        @endforelse
                                    </x-panel.ui.table.body>

                                </x-panel.ui.table>

                                @if ($subscriptionPlans->hasPages())
                                    <x-panel.ui.table.footer>
                                        {{ paginateLinks($subscriptionPlans) }}
                                    </x-panel.ui.table.footer>
                                @endif

                            </x-panel.ui.table.layout>

                        </x-panel.ui.card.body>
                    </x-panel.ui.card>

                </div>
            </div>
        </div>
    </div>
    <x-purchase_modal />
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush


@push('script')
    <script>
        "use strict";
        (function($) {

            $('.filter-form').find('select').on('change', function() {
                $('.filter-form').submit();
            });

            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get("tab");

            if (tabParam) {
                const $tab = $(`[data-tab-id="${tabParam}"]`);
                if ($tab.length) {
                    const tab = new bootstrap.Tab($tab[0]);
                    tab.show();
                }
            }

            $('[data-tab-id]').on('click', function(e) {
                const tabId = $(e.target).data('tab-id');
                const url = new URL(window.location);
                url.searchParams.set('tab', tabId);
                history.replaceState(null, '', url);
            });

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

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .plan-details__item {
            border-bottom: 1px solid hsl(var(--border-color));
            padding-bottom: 7px;
            margin-bottom: 7px;
            color: hsl(var(--black));
            font-size: 15px;
        }

        .plan-details__item .item-title {
            color: hsl(var(--black));
            font-weight: 400;
            font-size: 15px;
        }

        .active-plan-title {
            color: hsl(var(--body-color)) !important;
        }

        .copy-coupon {
            cursor: pointer !important;
        }
    </style>
@endpush
