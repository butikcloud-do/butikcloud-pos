@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card>
                <x-panel.ui.card.body :paddingZero=true>
                    <x-panel.ui.table.layout>
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('Product')</th>
                                    <th>@lang('Brand')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Sale Price')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-start">
                                                <span class="table-thumb">
                                                    <img src="{{ $product->image_src }}">
                                                </span>
                                                <div>
                                                    <strong class="d-block text-start">
                                                        {{ strLimit(__(@$product->name), 25) }}
                                                    </strong>
                                                    <span>
                                                        {{ __(@$product->product_code) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ __(@$product->brand->name) }}</td>
                                        <td>{{ __(@$product->category->name) }}</td>
                                        <td>
                                            {{ showAmount($product->details->min('final_price')) }}
                                            @if ($product->product_type == Status::PRODUCT_TYPE_VARIABLE)
                                                - {{ showAmount($product->details->max('final_price')) }}
                                            @endif
                                        </td>
                                        <td>
                                            <x-panel.other.status_switch :status="$product->status" :action="route('user.product.status.change', $product->id)"
                                                title="product" />
                                        </td>
                                        <td class="dropdown">
                                            @if (request()->trash)
                                                <button type="button" class="btn btn-outline--success confirmationBtn"
                                                    data-question='@lang('Are you sure to restore this product?')'
                                                    data-action="{{ route('user.product.trash.restore', $product->id) }}">
                                                    <i class="las la-undo"></i>
                                                    @lang('Restore')
                                                </button>
                                            @else
                                                <button class=" btn btn-outline--primary" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    @lang('Action') <i class="las la-angle-down"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown">
                                                    <x-staff_permission_check permission="edit product">
                                                        <a class="dropdown-list d-block"
                                                            href="{{ route('user.product.edit', $product->id) }}">
                                                            <span class="me-2">
                                                                <i class="las la-pencil-alt text--primary"></i>
                                                            </span>
                                                            @lang('Edit Product')
                                                        </a>
                                                    </x-staff_permission_check>
                                                    <x-staff_permission_check permission="view product">
                                                        <a class="dropdown-list d-block"
                                                            href="{{ route('user.product.view', $product->id) }}">
                                                            <span class="me-2">
                                                                <i class="las la-eye text--success"></i>
                                                            </span>
                                                            @lang('View Product')
                                                        </a>
                                                    </x-staff_permission_check>
                                                    <x-staff_permission_check permission="add purchase">
                                                        <a class="dropdown-list d-block"
                                                            href="{{ route('user.purchase.add') }}?product_code={{ $product->product_code }}">
                                                            <span class="me-2">
                                                                <i class="las la-plus text--info"></i>
                                                            </span>
                                                            @lang('Add Stock')
                                                        </a>
                                                    </x-staff_permission_check>
                                                    <x-staff_permission_check permission="print product barcode">
                                                        <a class="dropdown-list d-block"
                                                            href="{{ route('user.product.print.label') }}?product_code={{ $product->product_code }}">
                                                            <span class="me-2">
                                                                <i class="las la-barcode text--dark"></i>
                                                            </span>
                                                            @lang('Print Label')
                                                        </a>
                                                    </x-staff_permission_check>
                                                    <x-staff_permission_check permission="trash product">
                                                        <button type="button" class="dropdown-list d-block confirmationBtn"
                                                            data-question='@lang('Are you sure to move this product to trash?')'
                                                            data-action="{{ route('user.product.trash.temporary', $product->id) }}">
                                                            <span class="me-2">
                                                                <i class="las la-trash text--danger"></i>
                                                            </span>
                                                            @lang('Trash Product')
                                                        </button>
                                                    </x-staff_permission_check>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($products->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($products) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <x-confirmation-modal />
@endsection


@push('breadcrumb-plugins')
    <x-staff_permission_check permission="add product">
        <x-panel.ui.btn.add href="{{ route('user.product.create') }}" />
    </x-staff_permission_check>
@endpush

@push('style')
    <style>
        .btn-outline--primary i {
            transition: .2s linear;
        }

        .btn-outline--primary.show i {
            transform: rotate(180deg);
        }
    </style>
@endpush
