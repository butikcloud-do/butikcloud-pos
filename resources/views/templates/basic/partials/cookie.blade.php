@php
    $cookie = App\Models\Frontend::where('data_keys', 'cookie.data')->first();
@endphp
@if ($cookie->data_values->status == Status::ENABLE && !\Cookie::get('gdpr_cookie'))
    <div class="cookies-card hide">
        <div class="cookies-card__header">
            <h3 class="cookies-card__title mb-0 text--base">@lang('This Site Uses Cookies')</h3>
        </div>
        <p class="cookies-card__content text-white">
            {{ __($cookie->data_values->short_desc) }}
        </p>
        <div class="cookies-card__footer">
            <a href="{{ route('cookie.policy') }}" class="btn btn-outline--base">@lang('View More')</a>
            <button type="button" class="btn btn--base policy">@lang('Accept All')</button>
        </div>
    </div>
@endif
