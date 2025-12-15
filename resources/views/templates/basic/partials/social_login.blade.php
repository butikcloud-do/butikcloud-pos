@if (
    @gs('socialite_credentials')->linkedin->status ||
        @gs('socialite_credentials')->facebook->status == Status::ENABLE ||
        @gs('socialite_credentials')->google->status == Status::ENABLE)

    <div class="social-auth">
        @if (@gs('socialite_credentials')->google->status == Status::ENABLE)
            <a href="{{ route('user.social.login', 'google') }}"
                class="social-auth__btn @if (request()->routeIs('user.login')) w-100 @endif ">
                <img src="{{ asset($activeTemplateTrue . 'images/google.svg') }}" alt=" google">
                <span>@lang('Google')</span>
            </a>
        @endif
        @if (@gs('socialite_credentials')->facebook->status == Status::ENABLE)
            <a href="{{ route('user.social.login', 'facebook') }}"
                class="social-auth__btn @if (request()->routeIs('user.login')) w-100 @endif ">
                <img src="{{ asset($activeTemplateTrue . 'images/facebook.svg') }}" alt=" facebook">
                <span>@lang('Facebook')</span>
            </a>
        @endif
        @if (@gs('socialite_credentials')->linkedin->status == Status::ENABLE)
            <a href="{{ route('user.social.login', 'linkedin') }}"
                class="social-auth__btn @if (request()->routeIs('user.login')) w-100 @endif ">
                <img src="{{ asset($activeTemplateTrue . 'images/linkdin.svg') }}" alt=" linkedin">
                <span>@lang('Linkedin')</span>
            </a>
        @endif
    </div>
    <div class="account-divider">
        <span>@lang('OR')</span>
    </div>
@endif
