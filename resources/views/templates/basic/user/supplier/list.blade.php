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
                                    <th>@lang('Supplier')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Mobile')</th>
                                    <th>@lang('Balance')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Joined At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($suppliers as $supplier)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong class="d-block">{{ __($supplier->name) }}</strong>
                                                <small class="d-block">{{ __($supplier->company_name) }}</small>
                                            </div>
                                        </td>
                                        <td>{{ __($supplier->email ?? 'N/A') }}</td>
                                        <td>{{ $supplier->mobile }}</td>
                                        <td>
                                            <span class="badge badge--{{ $supplier->balance > 0 ? 'danger' : 'success' }}">
                                                {{ showAmount($supplier->balance) }}
                                            </span>
                                        </td>
                                        <td>
                                            <x-panel.other.status_switch :status="$supplier->status" :action="route('user.supplier.status.change', $supplier->id)"
                                                title="supplier" />
                                        </td>
                                        <td>
                                            <div>
                                                <strong class="d-block">{{ showDateTime($supplier->created_at) }}</strong>
                                                <small class="d-block"> {{ diffForHumans($supplier->created_at) }}</small>
                                            </div>
                                        </td>

                                        <td class="dropdown">
                                            <button class=" btn btn-outline--primary" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                @lang('Action') <i class="las la-angle-down"></i>
                                            </button>

                                            <div class="dropdown-menu dropdown">
                                                @if (request()->trash)
                                                    <x-staff_permission_check permission="trash supplier">
                                                        <button type="button"
                                                            class="dropdown-list d-block confirmationBtn  w-100 text-start"
                                                            data-question='@lang('Are you sure to restore this supplier?')'
                                                            data-action="{{ route('user.supplier.trash.restore', $supplier->id) }}">
                                                            <span class="me-1">
                                                                <i class="las la-undo text--success"></i>
                                                            </span>
                                                            @lang('Restore')
                                                        </button>
                                                    </x-staff_permission_check>
                                                @else
                                                    <x-staff_permission_check permission="edit supplier">
                                                        <button type="button"
                                                            class="dropdown-list d-block w-100 text-start edit-btn"
                                                            data-supplier="{{ $supplier }}">
                                                            <span class="me-1">
                                                                <i class="las la-pencil-alt text--info"></i>
                                                            </span>
                                                            @lang('Edit')
                                                        </button>
                                                    </x-staff_permission_check>
                                                    <x-staff_permission_check permission="view supplier">
                                                        <a class="dropdown-list d-block w-100 text-start"
                                                            href="{{ route('user.supplier.view', $supplier->id) }}">
                                                            <span class="me-1">
                                                                <i class="las la-eye text--dark"></i>
                                                            </span>
                                                            @lang('View')
                                                        </a>
                                                    </x-staff_permission_check>
                                                    <x-staff_permission_check permission="trash supplier">
                                                        <button type="button"
                                                            class="dropdown-list d-block confirmationBtn  w-100 text-start"
                                                            data-question='@lang('Are you sure to move this supplier to trash?')'
                                                            data-action="{{ route('user.supplier.trash.temporary', $supplier->id) }}">
                                                            <span class="me-1">
                                                                <i class="las la-trash text--danger"></i>
                                                            </span>
                                                            @lang('Trash')
                                                        </button>
                                                    </x-staff_permission_check>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($suppliers->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($suppliers) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <x-panel.ui.modal id="modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add Admin')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-panel.ui.modal.header>
        <x-panel.ui.modal.body>
            <form method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-lg-6">
                        <label>@lang('Company Name')</label>
                        <input type="text" class="form-control" name="company_name" required
                            value="{{ old('company_name') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Name')</label>
                        <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Email')</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Mobile')</label>
                        <input type="tel" class="form-control" name="mobile" value="{{ old('mobile') }}" required>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Address')</label>
                        <input type="text" class="form-control" name="address" value="{{ old('address') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('City')</label>
                        <input type="text" class="form-control" name="city" value="{{ old('city') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('State')</label>
                        <input type="text" class="form-control" name="state" value="{{ old('state') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Zip')</label>
                        <input type="text" class="form-control" name="zip" value="{{ old('zip') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Postcode')</label>
                        <input type="text" class="form-control" name="postcode" value="{{ old('postcode') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Country')</label>
                        <input type="text" class="form-control" name="country" value="{{ old('country') }}">
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <x-panel.ui.btn.modal />
                        </div>
                    </div>
                </div>
            </form>
        </x-panel.ui.modal.body>
    </x-panel.ui.modal>

    <x-confirmation-modal />
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            const $modal = $('#modal');
            const $form = $modal.find('form');

            $('.add-btn').on('click', function() {
                const action = "{{ route('user.supplier.create') }}"
                $modal.find('.modal-title').text("@lang('Add Supplier')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.supplier.update', ':id') }}";
                const supplier = $(this).data('supplier');
                $modal.find('.modal-title').text("@lang('Edit Supplier')");
                $modal.find('input[name=name]').val(supplier.name);
                $modal.find('input[name=company_name]').val(supplier.company_name);
                $modal.find('input[name=email]').val(supplier.email);
                $modal.find('input[name=mobile]').val(supplier.mobile);
                $modal.find('input[name=address]').val(supplier.address);
                $modal.find('input[name=city]').val(supplier.city);
                $modal.find('input[name=state]').val(supplier.state);
                $modal.find('input[name=country]').val(supplier.country);
                $modal.find('input[name=zip]').val(supplier.zip);
                $modal.find('input[name=postcode]').val(supplier.postcode);
                $form.attr('action', action.replace(':id', supplier.id));
                $modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
    <x-staff_permission_check permission="add supplier">
        <x-panel.ui.btn.add tag="btn" />
    </x-staff_permission_check>
@endpush
