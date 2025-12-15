@php
    $aboutUsContent = getContent('about_us.content', true);
    $aboutUsElements = getContent('about_us.element', false, orderById: true);
@endphp

<section class="about-area py-120">
    <div class="container">
        <div class="row gy-4 align-items-center flex-wrap-reverse">
            <div class="col-lg-6">
                <div class="about-thumb wow animationfadeLeft" data-wow-delay="0.3s">
                    <img src="{{ frontendImage('about_us', $aboutUsContent->data_values->image ?? '', '635x635') }}"
                        alt="img">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about__content">
                    <div class="section-heading style-left">
                        <h2 class="section-heading__title wow animationfadeUp" data-wow-delay="0.3s"
                            data-highlight="-2">
                            {{ __($aboutUsContent->data_values->heading ?? '') }}
                        </h2>
                        <p class="section-heading__desc wow animationfadeUp text--body" data-wow-delay="0.4s">
                            {{ __($aboutUsContent->data_values->subheading ?? '') }}
                        </p>
                        <div class="about__content-wrapper">
                            @foreach ($aboutUsElements as $aboutUsElement)
                                <div class="about__content-item wow animationfadeUp" data-wow-delay="0.6s">
                                    <div class="about__content-item__count">
                                        <span class="number">{{ $loop->iteration }}</span>
                                    </div>
                                    <div class="about__content-item__content">
                                        <h5 class="about__content-item__title">
                                            {{ __($aboutUsElement->data_values->title ?? '') }}
                                        </h5>
                                        <p class="about__content-item__desc">
                                            {{ __($aboutUsElement->data_values->description ?? '') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
