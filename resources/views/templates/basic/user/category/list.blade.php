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
                                @forelse($categories as $category)
                                    <tr>
                                        <td>
                                            <div class="flex-thumb-wrapper">
                                                <div class="thumb">
                                                    <img class="thumb-img" src="{{ $category->image_src }}">
                                                </div>
                                                <span class="ms-2">{{ __($category->name) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <x-panel.other.status_switch :status="$category->status" :action="route('user.category.status.change', $category->id)"
                                                title="category" />
                                        </td>
                                        <td>
                                            <x-panel.ui.btn.table_action module="category" :id="$category->id">
                                                <x-staff_permission_check permission="edit category">
                                                <x-panel.ui.btn.edit tag="btn" :data-category="$category" />
                                                </x-staff_permission_check>
                                            </x-panel.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                        @if ($categories->hasPages())
                            <x-panel.ui.table.footer>
                                {{ paginateLinks($categories) }}
                            </x-panel.ui.table.footer>
                        @endif
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <x-panel.ui.modal id="modal">
        <x-panel.ui.modal.header>
            <h4 class="modal-title">@lang('Add Category')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-panel.ui.modal.header>
        <x-panel.ui.modal.body>
            <form method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>@lang('Name')</label>
                    <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label>@lang('Image')</label>
                    <x-image-uploader name="image" type="category" :required="false" />
                </div>
                <div class="form-group">
                    <x-panel.ui.btn.modal />
                </div>
            </form>
        </x-panel.ui.modal.body>
    </x-panel.ui.modal>

    @include('Template::user.category.import_modal')
    <x-confirmation-modal />
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {

            const $modal = $('#modal');
            const $form = $modal.find('form');

            $('.add-btn').on('click', function() {
                const action = "{{ route('user.category.create') }}";

                $modal.find('.modal-title').text("@lang('Add Category')");
                $modal.find('.image-upload__thumb img').attr('src',
                    "{{ asset('assets/images/drag-and-drop.png') }}");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.category.update', ':id') }}";
                const category = $(this).data('category');

                $modal.find('.modal-title').text("@lang('Edit Category')");
                $modal.find('.image-upload__thumb img').attr('src', category.image_src);
                $modal.find('input[name=name]').val(category.name);
                $form.attr('action', action.replace(':id', category.id));
                $modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
<x-staff_permission_check permission="add category">
    <x-panel.ui.btn.add tag="btn" />
    <button type="button" class="btn btn-outline--primary me-2" data-bs-toggle="modal" data-bs-target="#importCategoryModal">
        <i class="las la-file-import me-1"></i>@lang('Import Categories')
    </button>
    
</x-staff_permission_check>
@endpush
