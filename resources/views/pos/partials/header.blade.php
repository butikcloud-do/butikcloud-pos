@php
    $user = auth()->user();
@endphp
<header class="pos-header">
    <ul class="pos-info">
        <li class="pos-info-item d-none d-sm-flex ">
            <span class="pos-info-item__icon">
                <x-panel.svg.calendar />
            </span>
            <span class="pos-info-item__text">
                {{ date('D, d F Y') }}
                <span class="current-time text--primary">
                    {{ date('g:i:s A') }}
                </span>
            </span>
        </li>
        <li class="pos-info-item location">
            <div class="pos-location">
                <div class="pos-location-field">
                    <div class="pos-location-field__icon">
                        <x-panel.svg.map />
                    </div>
                    <select class="form-control form--control select2" name="warehouse_id" form="pos-form">
                        <option value="" selected disabled>@lang('Select Warehouse')</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" @selected($loop->first)>{{ __($warehouse->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </li>
    </ul>
    <div class="pos-header-btns">
        <div class="dropdown calculator--dropdown">
            <button class="pos-btn pos-btn--sm pos-btn-outline--primary dropdown-toggle calculator-open-btn"
                type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                <span class="icon">
                    <i class="fa fa-calculator"></i>
                </span>
                <span class="text">@lang('Calculator')</span>
            </button>
            <div class="dropdown-menu">
                @include('pos.partials.calculator')
            </div>
        </div>
        <button class="pos-btn pos-btn--sm pos-btn-outline--secondary" type="button" data-bs-toggle="modal"
            data-bs-target="#shortcut-modal">
            <span class="icon">
                <i class="fa-regular fa-bookmark"></i>
            </span>
            <span class="text">@lang('Shortcut')</span>
        </button>
        <x-staff_permission_check permission="view sale">
            <a class="pos-btn pos-btn--sm pos-btn-outline--info" href="{{ route('user.sale.list') }}">
                <span class="icon">
                    <i class="fas fa-list"></i>
                </span>
                <span class="text">@lang('Sale List')</span>
            </a>
        </x-staff_permission_check>

        <x-staff_permission_check permission="view dashboard">
            <a class="pos-btn pos-btn--sm pos-btn-outline--primary" href="{{ route('user.home') }}">
                <span class="icon">
                    <i class="la la-dashboard"></i>
                </span>
                <span class="text">@lang('Dashboard')</span>
            </a>
        </x-staff_permission_check>
        <x-staff_permission_check permission="view dashboard">
            <a class="pos-btn pos-btn--sm pos-btn-outline--secondary" href="{{ route('user.cash_register.dashboard') }}"
                target="_blank">
                <span class="icon">
                    <i class="las la-file-invoice-dollar"></i>
                </span>
                <span class="text">@lang('Cash Register')</span>
            </a>
        </x-staff_permission_check>
        <button class="pos-btn pos-btn--sm pos-btn-outline--primary pos-sidebar-toggle ms-auto d-lg-none" type="button"
            data-toggle="pos-sidebar" data-target="#pos-sidebar">
            <span class="text">
                @lang('View Cart')
                (<span class="cart-count">0</span>)
            </span>
        </button>
    </div>
    <div class="dropdown user--dropdown">
        <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="text">{{ $user->username }}</span>
        </button>
        <div class="dropdown-menu dropdown-menu-end">
            <div class="dropdown-menu-header">
                <div class="dropdown-menu-info">
                    <h6 class="dropdown-menu-info__name">{{ $user->username }}</h6>
                    <span class="dropdown-menu-info__email">{{ $user->email }}</span>
                </div>
                <a class="w-100 pos-btn pos-btn-outline--danger" href="{{ route('admin.logout') }}">
                    <i class="las la-dashboard"></i>
                    <span>@lang('Logout')</span>
                </a>
            </div>
            <div class="dropdown-menu-body">
                <a class="dropdown-item" href="{{ route('user.home') }}">
                    <i class="la la-dashboard"></i>
                    <span class="text">@lang('Dashboard')</span>
                </a>
                <a class="dropdown-item" href="{{ route('admin.profile') }}">
                    <i class="las la-user-edit"></i>
                    <span class="text">@lang('Profile')</span>
                </a>
                <a class="dropdown-item" href="{{ route('admin.password') }}">
                    <i class="las la-lock-open"></i>
                    <span class="text">@lang('Change Password')</span>
                </a>
            </div>
        </div>
    </div>
</header>

@push('script')
    <script>
        "use strict";
        (function($) {
            setInterval(() => {
                $(".current-time").text(new Date().toLocaleTimeString());
            }, 1000);

            $('select[name=warehouse_id]').on('change', function() {
                $('.pos-cart-table__tbody').html(`
                    <div class="product-empty-message">
                        <div class="p-5 text-center">
                            <img src="{{ asset('assets/images/empty_box.png') }}" class="empty-message">
                            <span class="d-block">@lang('No product you are select')</span>
                            <span class="d-block fs-13 text-muted">@lang('There are no available data to display on this table at the moment.')</span>
                        </div>
                    </div>
                `);
                $('body').find('.pos-sidebar-body').addClass('product-cart-empty');
                window.added_product_id = [];
                window.product_page = 1;
                window.calculateAll();
                window.getProductList();

            });
        })(jQuery);
    </script>
@endpush
