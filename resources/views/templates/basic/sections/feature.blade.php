@php
    $featureContent = getContent('feature.content', true);
    $featureElements = getContent('feature.element', false, orderById: true)->take(4);
@endphp

<section class="features-area py-120 bg-light-shape">
    <div class="container">
        <div class="section-heading">
            <h2 class="section-heading__title wow animationfadeUp" data-wow-delay="0.3s" data-highlight="3_-1">
                {{ __($featureContent->data_values->heading ?? '') }}</h2>
            <p class="section-heading__desc wow animationfadeUp" data-wow-delay="0.4s">
                {{ __($featureContent->data_values->subheading ?? '') }}
            </p>
        </div>

        <div class="row gy-4 justify-content-center">
            @foreach ($featureElements as $featureElement)
                <div class="col-xl-3 col-sm-6">
                    <div class="features-item wow animationfadeUp" data-wow-delay="0.2s">
                        <div class="features-item__icon">
                            <img src="{{ frontendImage('feature', $featureElement->data_values->image ?? '', '70x70') }}"
                                alt="thumbs" />
                        </div>
                        <div class="features-item__content">
                            <h5 class="features-item__title">{{ __($featureElement->data_values->title ?? '') }}</h5>
                            <p class="features-item__desc">
                                {{ __($featureElement->data_values->description ?? '') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="col-12 text-center">
                <a href="{{ route('features') }}" class="btn btn--base wow animationfadeUp" data-wow-delay="0.2s">
                    @lang('View All Features')
                </a>
            </div>
        </div>
    </div>
</section>
