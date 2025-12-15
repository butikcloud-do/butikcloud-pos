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
                                    <th>@lang('Address')</th>
                                    <th>@lang('Country')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($companies as $company)
                                    <tr>
                                        <td>{{ __($company->name) }}</td>
                                        <td>{{ $company->email }}</td>
                                        <td>{{ $company->mobile }}</td>
                                        <td>{{ __($company->address) }}</td>
                                        <td>{{ __($company->country) }}</td>
                                        <td>
                                            <x-panel.other.status_switch :status="$company->status" :action="route('user.company.status.change', $company->id)"
                                                title="company" />
                                        </td>
                                        <td>
                                            <x-panel.ui.btn.table_action module="company" :id="$company->id">
                                                <x-staff_permission_check permission="edit company">
                                                    <x-panel.ui.btn.edit tag="btn" :data-company="$company" />
                                                </x-staff_permission_check>
                                            </x-panel.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($companies->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($companies) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <x-panel.ui.modal id="modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add Company')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-panel.ui.modal.header>
        <x-panel.ui.modal.body>
            <form method="POST">
                @csrf
                <div class="form-group">
                    <label>@lang('Name')</label>
                    <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label>@lang('Email')</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <label>@lang('Mobile')</label>
                    <input type="tel" class="form-control" name="mobile" value="{{ old('mobile') }}">
                </div>
                <div class="form-group">
                    <label>@lang('Address')</label>
                    <input type="text" class="form-control" name="address" value="{{ old('address') }}">
                </div>
                <div class="form-group">
                    <label>@lang('Country')</label>
                    <input type="text" class="form-control" name="country" value="{{ old('country') }}">
                </div>
                <div class="form-group">
                    <x-panel.ui.btn.modal />
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
                const action = "{{ route('user.company.create') }}"
                $modal.find('.modal-title').text("@lang('Add Company')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.company.update', ':id') }}";
                const company = $(this).data('company');
                $modal.find('.modal-title').text("@lang('Edit Company')");
                $modal.find('input[name=name]').val(company.name);
                $modal.find('input[name=email]').val(company.email);
                $modal.find('input[name=mobile]').val(company.mobile);
                $modal.find('input[name=country]').val(company.country);
                $modal.find('input[name=address]').val(company.address);
                $form.attr('action', action.replace(':id', company.id));
                $modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
    <x-staff_permission_check permission="add company">
        <x-panel.ui.btn.add tag="btn" />
    </x-staff_permission_check>
@endpush
