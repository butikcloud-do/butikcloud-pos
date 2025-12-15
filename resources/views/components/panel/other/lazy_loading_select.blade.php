@props(['name', 'route', 'displayFields' => 'name', 'isDisabled' => false, 'required' => true])
<div class="position-relative {{ $name }}-wrapper">
    <select class="form-control lazy-loading-option {{ $name }}" name="{{ $name }}" {{ $required }}
        @disabled($isDisabled)>
        <option value="" selected disabled>@lang('Select One')</option>
    </select>
</div>

@push('script')
    <script>
        "use strict";
        (function($) {
            const name = "{{ $name }}";
            const displayFields = ("{{ $displayFields }}").toString().split(',');

            $(`.${name}`).select2({
                ajax: {
                    url: "{{ $route }}",
                    type: "get",
                    dataType: 'json',
                    delay: 1000,
                    data: function(params) {
                        return {
                            search: params.term,
                            page: params.page,
                        };
                    },
                    processResults: function(response, params) {
                        params.page = params.page || 1;
                        let data = response.data.data;
                        return {
                            results: $.map(data, function(item) {
                                const text = formattedText(item);
                                return {
                                    text: text,
                                    id: item.id
                                }
                            }),
                            pagination: {
                                more: response.more
                            }
                        };
                    },
                    cache: false,
                },
                dropdownParent: $('body').find(`.${name}-wrapper`)
            });

            function formattedText(item) {
                let text = '';
                $.each(displayFields, function(i, displayField) {
                    if (i != 0) {
                        text += " - ";
                    }
                    text += item[displayField];
                });

                return text;

            }
        })(jQuery);
    </script>
@endpush
