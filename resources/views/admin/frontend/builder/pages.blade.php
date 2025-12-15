@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-panel.ui.card>
                <x-panel.ui.card.body :paddingZero="true">
                    <x-panel.ui.table.layout :renderTableFilter=false>
                        <x-panel.ui.table>
                            <x-panel.ui.table.header>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Slug')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-panel.ui.table.header>
                            <x-panel.ui.table.body>
                                @forelse($pData as $k => $data)
                                    <tr>
                                        <td>{{ __($data->name) }}</td>
                                        <td>{{ __($data->slug) }}</td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2 flex-wrap">
                                                <a href="{{ route('admin.frontend.manage.pages.seo', $data->id) }}"
                                                    class="btn  btn-outline--info"><i class="la la-cog"></i>
                                                    @lang('SEO Setting')</a>
                                                <a href="{{ route('admin.frontend.manage.section', $data->id) }}"
                                                    class="btn  btn-outline--primary"><i class="la la-pencil"></i>
                                                    @lang('Edit')</a>
                                                @if ($data->is_default == Status::NO)
                                                    <button class="btn  btn-outline--danger confirmationBtn"
                                                        data-action="{{ route('admin.frontend.manage.pages.delete', $data->id) }}"
                                                        data-question="@lang('Are you sure to remove this page?')">
                                                        <i class="las la-trash"></i> @lang('Delete')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <x-panel.ui.table.empty_message />
                                @endforelse
                            </x-panel.ui.table.body>
                        </x-panel.ui.table>
                    </x-panel.ui.table.layout>
                </x-panel.ui.card.body>
            </x-panel.ui.card>
        </div>
    </div>

    <x-panel.ui.modal id="addModal">
        <x-panel.ui.modal.header>
            <h1 class="modal-title">@lang('Add New Page')</h1>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-panel.ui.modal.header>
        <x-panel.ui.modal.body>
            <form action="{{ route('admin.frontend.manage.pages.save') }}" method="POST">
                @csrf
                <div class="form-group">
                    <div class="d-flex justify-content-between">
                        <label> @lang('Page Name')</label>
                        <a href="javascript:void(0)" class="buildSlug"><i class="las la-link"></i>
                            @lang('Make Slug')</a>
                    </div>
                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <div class="d-flex justify-content-between">
                        <label> @lang('Slug')</label>
                        <div class="slug-verification d-none"></div>
                    </div>
                    <input type="text" class="form-control" name="slug" value="{{ old('slug') }}" required>
                </div>
                <div class="form-group">
                    <x-panel.ui.btn.modal />
                </div>
            </form>
        </x-panel.ui.modal.body>
    </x-panel.ui.modal>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button type="button" class="btn  btn--primary addBtn">
        <i class="las la-plus"></i> @lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.addBtn').on('click', function() {
                var modal = $('#addModal');
                modal.find('input[name=id]').val($(this).data('id'))
                modal.modal('show');
            });

            $('.buildSlug').on('click', function() {
                let closestForm = $(this).closest('form');
                let title = closestForm.find('[name=name]').val();
                closestForm.find('[name=slug]').val(title);
                closestForm.find('[name=slug]').trigger('input');
            });

            $('[name=slug]').on('input', function() {
                let closestForm = $(this).closest('form');
                closestForm.find('[type=submit]').addClass('disabled')
                let slug = $(this).val();
                slug = slug.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                $(this).val(slug);
                if (slug) {
                    $('.slug-verification').removeClass('d-none');
                    $('.slug-verification').html(`
                        <small class="text--info"><i class="las la-spinner la-spin"></i> @lang('Checking')</small>
                    `);
                    $.get("{{ route('admin.frontend.manage.pages.check.slug') }}", {
                        slug: slug
                    }, function(response) {
                        if (!response.exists) {
                            $('.slug-verification').html(`
                                <small class="text--success"><i class="las la-check"></i> @lang('Available')</small>
                            `);
                            closestForm.find('[type=submit]').removeClass('disabled')
                        }
                        if (response.exists) {
                            $('.slug-verification').html(`
                                <small class="text--danger"><i class="las la-times"></i> @lang('Slug already exists')</small>
                            `);
                        }
                    });
                } else {
                    $('.slug-verification').addClass('d-none');
                }
            })
        })(jQuery);
    </script>
@endpush
