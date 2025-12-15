@php
    $policyPages = getContent('policy_pages.element', false, orderById: true);
    $socialElements = getContent('social_icon.element', false, orderById: true);
    $footerContent = getContent('footer.content', true);
    $contactContent = getContent('contact_us.content', true);
@endphp

<footer class="footer">
    <div class="footer-top pb-40 pt-120">
        <div class="container">
            <div class="row justify-content-center gy-5">
                <div class="col-xl-4 col-sm-6 col-xsm-6">
                    <div class="footer-item footer-item-one">
                        <div class="footer-item__logo">
                            <a href="{{ route('home') }}">
                                <img src="{{ siteLogo('dark') }}">
                            </a>
                        </div>
                        <p class="footer-item__desc"> {{ __($footerContent->data_values->title ?? '') }} </p>
                        <ul class="social-list">
                            @foreach ($socialElements as $socialElement)
                                <li class="social-list__item">
                                    <a href="{{ $socialElement->data_values->url ?? '' }}"
                                        class="social-list__link flex-center" target="_blank">
                                        @php
                                            echo $socialElement->data_values->social_icon ?? '';
                                        @endphp
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6 col-xsm-6">
                    <div class="footer-item">
                        <h6 class="footer-item__title">@lang('Quick Links')</h6>
                        <ul class="footer-menu">
                            <li class="footer-menu__item">
                                <a href="{{ route('home') }}" class="footer-menu__link">@lang('Home')</a>
                            </li>
                            <li class="footer-menu__item">
                                <a href="{{ route('blogs') }}" class="footer-menu__link">@lang('Blog')</a>
                            </li>
                            <li class="footer-menu__item">
                                <a href="{{ route('contact') }}" class="footer-menu__link">
                                    @lang('Contact')</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-xsm-6">
                    <div class="footer-item">
                        <h6 class="footer-item__title">@lang('Contact Us')</h6>
                        <ul class="footer-contact-menu">
                            <li class="footer-contact-menu__item">
                                <div class="footer-contact-menu__item-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="footer-contact-menu__item-content">
                                    <p>{{ __($contactContent->data_values->contact_address ?? '') }}</p>
                                </div>
                            </li>
                            <li class="footer-contact-menu__item">
                                <div class="footer-contact-menu__item-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="footer-contact-menu__item-content">
                                    <a
                                        href="mailto:{{ __($contactContent->data_values->email_address ?? '') }}">{{ __($contactContent->data_values->email_address ?? '') }}</a>
                                </div>
                            </li>
                            <li class="footer-contact-menu__item">
                                <div class="footer-contact-menu__item-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="footer-contact-menu__item-content">
                                    <a
                                        href="tel:{{ __($contactContent->data_values->contact_number ?? '') }}">{{ __($contactContent->data_values->contact_number ?? '') }}</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-xsm-6">
                    <div class="footer-item">
                        <h6 class="footer-item__title">@lang('Subscribe')</h6>
                        <p class="fs-14 mb-3">
                            {{ __(@$footerContent->data_values->subscribe_title ?? '') }}
                        </p>
                        <div class="cta-form">
                            <form class="cta__subscribe mt-2 no-submit-loader">
                                @csrf
                                <div class="subscribe-group d-flex gap-2 form-group">
                                    <input type="email" class="form-control form--control" required name="email"
                                        placeholder="@lang('Enter your email')">
                                    <button class="btn btn--base flex-shrink-0"
                                        type="submit"><i class="fas fa-paper-plane"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="footer-bottom py-3">
            <div class="row ">
                <div class="col-md-12">
                    <div class="footer-bottom__menu gy-3">
                        <div class="bottom-footer-text">
                            © @lang('Copyright') {{ date('Y') }} . @lang('All rights reserved.')
                        </div>
                        <ul>
                            @foreach ($policyPages as $policy)
                                <li>
                                    <a
                                        href="{{ route('policy.pages', $policy->slug) }}">{{ __($policy->data_values->title ?? '') }}</a>
                                </li>
                            @endforeach
                            <li><a href="{{ route('cookie.policy') }}">@lang('Cookie Policy')</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>


@push('script')
    <script>
        "use strict";
        (function($) {

            $(document).on("submit", ".cta__subscribe", function(e) {
                e.preventDefault();

                let form = $(this);
                let formData = new FormData(this);

                $.ajax({
                    type: "POST",
                    url: "{{ route('subscribe') }}",
                    data: formData,
                    processData: false,
                    contentType: false,

                    success: function(response) {
                        if (response.status === 'success') {
                            form.trigger('reset');
                            notify('success', response.message);
                        } else {
                            notify('error', response.message);
                        }
                    }
                });
            });

        })(jQuery);
    </script>
@endpush
