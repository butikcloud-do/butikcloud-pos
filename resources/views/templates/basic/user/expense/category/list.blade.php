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
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($expenseCategories as $expenseCategory)
                                    <tr>
                                        <td>{{ __($expenseCategory->name) }}</td>
                                        <td>
                                            <x-panel.other.status_switch :status="$expenseCategory->status"
                                                action="{{ route('user.expense.category.status.change', $expenseCategory->id) }}"
                                                title="expense category" />
                                        </td>
                                        <td>
                                            <x-panel.ui.btn.table_action module="expense_category" :id="$expenseCategory->id">
                                                <x-staff_permission_check permission="edit expense category">
                                                    <x-panel.ui.btn.edit tag="btn" :data-expense-category="$expenseCategory" />
                                                </x-staff_permission_check>
                                            </x-panel.ui.btn.table_action>

                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($expenseCategories->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($expenseCategories) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <x-panel.ui.modal id="modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add Expense Category')</h4>
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
                const action = "{{ route('user.expense.category.create') }}";

                $modal.find('.modal-title').text("@lang('Add Expense Category')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.expense.category.update', ':id') }}";
                const expenseCategory = $(this).data('expense-category');

                $modal.find('.modal-title').text("@lang('Edit Expense Category')");
                $modal.find('input[name=name]').val(expenseCategory.name);
                $form.attr('action', action.replace(':id', expenseCategory.id));
                $modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
<x-staff_permission_check permission="add expense category">
    <x-panel.ui.btn.add tag="btn" />
</x-staff_permission_check>
@endpush
