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
                                    <th>@lang('Name')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Mobile')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Joined At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($customers as $customer)
                                    <tr>
                                        <td>{{ __($customer->name) }}</td>
                                        <td>{{ $customer->email }}</td>
                                        <td>{{ __($customer->mobile) }}</td>
                                        <td>
                                            <x-panel.other.status_switch :status="$customer->status" :action="route('user.customer.status.change', $customer->id)"
                                                title="customer" />
                                        </td>
                                        <td>
                                            <div>
                                                <strong class="d-block">{{ showDateTime($customer->created_at) }}</strong>
                                                <small class="d-block"> {{ diffForHumans($customer->created_at) }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <x-panel.ui.btn.table_action module="customer" :id="$customer->id">
                                                <x-staff_permission_check permission="edit customer">
                                                    <x-panel.ui.btn.edit tag="btn" :data-customer="$customer" />
                                                </x-staff_permission_check>
                                            </x-panel.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($customers->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($customers) }}
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
                    <div class="form-group col-lg-12">
                        <label>@lang('Name')</label>
                        <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Email')</label>
                        <input type="email" class="form-control" name="email" required value="{{ old('email') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Mobile')</label>
                        <input type="tel" class="form-control" name="mobile" required value="{{ old('mobile') }}">
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
                const action = "{{ route('user.customer.create') }}"
                $modal.find('.modal-title').text("@lang('Add Customer')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.customer.update', ':id') }}";
                const customer = $(this).data('customer');
                $modal.find('.modal-title').text("@lang('Edit Customer')");
                $modal.find('input[name=name]').val(customer.name);
                $modal.find('input[name=email]').val(customer.email);
                $modal.find('input[name=mobile]').val(customer.mobile);
                $modal.find('input[name=address]').val(customer.address);
                $modal.find('input[name=city]').val(customer.city);
                $modal.find('input[name=state]').val(customer.state);
                $modal.find('input[name=country]').val(customer.country);
                $modal.find('input[name=zip]').val(customer.zip);
                $modal.find('input[name=postcode]').val(customer.postcode);
                $form.attr('action', action.replace(':id', customer.id));
                $modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
    <x-staff_permission_check permission="add customer">
        <x-panel.ui.btn.add tag="btn" />
    </x-staff_permission_check>
@endpush
