@php
    $faqContent = getContent('faq.content', true);
    $faqElements = getContent('faq.element', false, orderById: true);
@endphp

<section class="faq-section py-120">
    <div class="container">
        <div class="section-heading">
            <h2 class="section-heading__title  wow animationfadeUp" data-wow-delay="0.3s" data-highlight="-1">
                {{ __($faqContent->data_values->heading ?? '') }}
            </h2>
            <p class="section-heading__desc  wow animationfadeUp" data-wow-delay="0.4s">
                {{ __($faqContent->data_values->subheading ?? '') }}
            </p>
        </div>

        <div class="row gy-4">
            @foreach ($faqElements as $faqElement)
                <div class="col-md-6">
                    <div class="faq-item  wow animationfadeUp" data-wow-delay="0.3s">
                        <span class="faq-item__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none">
                                <path
                                    d="M12.0834 9.99984C12.0834 11.1504 11.1507 12.0832 10.0001 12.0832C8.8495 12.0832 7.91675 11.1504 7.91675 9.99984C7.91675 8.84925 8.8495 7.9165 10.0001 7.9165C11.1507 7.9165 12.0834 8.84925 12.0834 9.99984Z"
                                    stroke="currentColor" stroke-width="1.5" />
                                <path
                                    d="M15.8334 9.2848C15.5625 9.24555 15.2842 9.21572 15.0001 9.1958M5.00008 10.8046C4.71589 10.7848 4.43767 10.7549 4.16675 10.7156"
                                    stroke="#1E293B" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M10.0001 16.2498C8.88966 16.7685 7.43097 17.0832 5.83341 17.0832C4.94515 17.0832 4.09982 16.9859 3.33341 16.8103C2.08306 16.5239 1.66675 15.7718 1.66675 14.4882V5.51148C1.66675 4.69079 2.53344 4.12711 3.33341 4.31034C4.09982 4.48588 4.94515 4.58317 5.83341 4.58317C7.43097 4.58317 8.88966 4.26847 10.0001 3.74984C11.1105 3.2312 12.5692 2.9165 14.1667 2.9165C15.055 2.9165 15.9003 3.0138 16.6667 3.18934C17.9848 3.49124 18.3334 4.2668 18.3334 5.51148V14.4882C18.3334 15.3089 17.4667 15.8726 16.6667 15.6893C15.9003 15.5138 15.055 15.4165 14.1667 15.4165C12.5692 15.4165 11.1105 15.7312 10.0001 16.2498Z"
                                    stroke="currentColor" stroke-width="1.5" />
                            </svg>
                        </span>
                        <div class="faq-item__content">
                            <h5 class="faq-item__title">
                                {{ __($faqElement->data_values->question ?? '') }}
                            </h5>
                            <p class="faq-item__desc">
                                {{ __($faqElement->data_values->answer ?? '') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="col-12">
                <div class="faq-contact wow animationfadeUp" data-wow-delay="0.3s">
                    <div class="faq-contact__left">
                        <span class="thumb">
                            <i class="fas fa-question"></i>
                        </span>
                        <div class="content">
                            <h6 class="title">{{ __(@$faqContent->data_values->footer_title ?? '') }}</h6>
                            <p class="desc">{{ __(@$faqContent->data_values->footer_subtitle ?? '') }}</p>
                        </div>
                    </div>
                    <div class="faq-contact__right">
                        <a href="{{ route($faqContent->data_values->button_url ?? '') }}"
                            class="btn btn--base">{{ $faqContent->data_values->button_name ?? '' }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
