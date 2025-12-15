@php
    $bannerContent = getContent('banner.content', true);
@endphp

<section class="banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="banner-content text-center">
                    <h1 class="banner-content__title wow animationfadeUp" data-wow-delay="0.3s" data-highlight="3_-1">{{ __($bannerContent->data_values->heading ?? '') }}</h1>
                    <p class="banner-content__desc wow animationfadeUp" data-wow-delay="0.4s">{{ __($bannerContent->data_values->description ?? '') }}</p>
                    <a href="{{ $bannerContent->data_values->button_url }}" class="btn btn-outline--white wow animationfadeUp" data-wow-delay="0.5s">{{ __($bannerContent->data_values->button_name ?? '') }}</a>
                </div>
            </div>
            <div class="col-lg-12">
                <section class="banner__thumb-grid wow animationfadeUp" data-wow-delay="0.6s">
                    <div class="banner__thumb__item item-1">
                        <img class="w-100" src="{{ frontendImage('banner', $bannerContent->data_values->image_one ?? '', '590x590') }}" alt="">
                    </div>
                    <div class="banner__thumb__item item-2">
                        <img class="w-100" src="{{ frontendImage('banner', $bannerContent->data_values->image_two ?? '', '950x810') }}" alt="">
                    </div>
                    <div class="banner__thumb__item item-3">
                        <img class="w-100" src="{{ frontendImage('banner', $bannerContent->data_values->image_three ?? '', '590x590') }}" alt="">
                    </div>
                </section>

            </div>
        </div>
    </div>
</section>
