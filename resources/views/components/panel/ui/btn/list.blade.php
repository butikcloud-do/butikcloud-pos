@props(['text' => 'View List'])

<a {{ $attributes->merge(['class' => 'btn  btn--primary float-end']) }}>
    <i class="las la-list me-1"></i>{{ __($text) }}
</a>
