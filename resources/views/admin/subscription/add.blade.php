@extends('admin.layouts.app')
@section('panel')
    <div class="row responsive-row">
        <div class="col-12">
            <div class="alert alert--info d-flex" role="alert">
                <div class="alert__icon">
                    <i class="las la-info"></i>
                </div>
                <div class="alert__content">
                    <p>
                        @lang('All subscription plans include essential core modules with unlimited access, such as unlimited customers, sales, purchases, multi-language support, and more. These features form the foundation of any standard POS SaaS system, ensuring your business runs smoothly from day one.')

                    </p>
                </div>
            </div>
        </div>

        <div class="col-12">
            <x-panel.ui.card>
                <x-panel.ui.card.body>
                    <form action="{{ route('admin.subscription.plan.save', @$plan->id) }}" method="POST"
                        class="product-create-form" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <label>@lang('Plan Name')</label>
                                <input type="text" class="form-control" name="name" required
                                    value="{{ old('name', @$plan->name) }}">
                            </div>

                            <div class="form-group col-sm-6">
                                <label>@lang('Product Limit')</label>
                                <input type="number" class="form-control" name="product_limit"
                                    value="{{ old('product_limit', @$plan->product_limit) }}" required>
                                <small class="mt-1 d-block">@lang('Use -1 for unlimited product upload to this plan.')</small>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>@lang('User Limit')</label>
                                <input type="number" class="form-control" name="user_limit"
                                    value="{{ old('user_limit', @$plan->user_limit) }}" required>
                                <small class="mt-1 d-block">@lang('Use -1 for unlimited staff add to this plan.')</small>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>@lang('Warehouse Limit')</label>
                                <input type="number" class="form-control" name="warehouse_limit"
                                    value="{{ old('warehouse_limit', @$plan->warehouse_limit) }}" required>
                                <small class="mt-1 d-block">@lang('Use -1 for unlimited warehouse add to this plan.')</small>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>@lang('Supplier Limit')</label>
                                <input type="number" class="form-control" name="supplier_limit"
                                    value="{{ old('supplier_limit', @$plan->supplier_limit) }}" required>
                                <small class="mt-1 d-block">@lang('Use -1 for unlimited supplier add to this plan.')</small>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>@lang('Coupon Limit')</label>
                                <input type="number" class="form-control" name="coupon_limit"
                                    value="{{ old('coupon_limit', @$plan->coupon_limit) }}" required>
                                <small class="mt-1 d-block">@lang('Use -1 for unlimited supplier add to this plan.')</small>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>@lang('HRM access')</label>
                                <select name="hrm_access" class="form-control select2" data-minimum-results-for-search="-1"
                                    required>
                                    <option value="{{ Status::NO }}">@lang('No')</option>
                                    <option value=" {{ Status::YES }} " @selected(old('hrm_access', @$plan->hrm_access) == Status::YES)>@lang('Yes')
                                    </option>
                                </select>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>@lang('Monthly Price')</label>
                                <div class="input-group input--group">
                                    <input type="number" min="0" class="form-control" name="monthly_price"
                                        value="{{ getAmount(old('monthly_price', @$plan->monthly_price)) }}" required>
                                    <span class="input-group-text"> {{ __(gs('cur_text')) }} </span>
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>@lang('Yearly Price')</label>
                                <div class="input-group input--group">
                                    <input type="number" min="0" class="form-control" name="yearly_price"
                                        value="{{ getAmount(old('yearly_price', @$plan->yearly_price)) }}" required>
                                    <span class="input-group-text"> {{ __(gs('cur_text')) }} </span>
                                </div>
                            </div>
                            <div class="col-12">
                                <x-panel.ui.btn.submit />
                            </div>
                        </div>
                    </form>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back_btn route="{{ route('admin.subscription.plan.list') }}" />
@endpush
