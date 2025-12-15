@php
    $serviceContent = getContent('service.content', true);
    $serviceElements = getContent('service.element', false, orderById: true);
@endphp

<section class="service-area py-120">
    <div class="container">
        <div class="section-heading">
            <h2 class="section-heading__title wow animationfadeUp" data-wow-delay="0.3s" data-highlight="0">{{ __($serviceContent->data_values->heading ?? '') }}
            </h2>
            <p class="section-heading__desc wow animationfadeUp" data-wow-delay="0.4s"> {{ __($serviceContent->data_values->subheading ?? '') }} </p>
        </div>

        <div class="row gy-4  align-items-center">
            <div class="col-lg-6">
                <div class="services-section__content">
                    <ul class="services-list nav flex-column nav-pills wow animationfadeUp" data-wow-delay="0.4s" id="services-tab" role="tablist" aria-orientation="vertical">
                        @foreach ($serviceElements as $serviceElement)
                            @php
                                $tabId = 'sevices-tab-' . $loop->iteration;
                                $paneId = 'servies-pane-' . $loop->iteration;
                            @endphp
                            <li class="nav-item" role="presentation">
                                <span class="services-item left-menu-item nav-link {{ $loop->first ? 'active' : '' }}" id="{{ $tabId }}" data-bs-toggle="pill" data-bs-target="#{{ $paneId }}" role="tab" aria-controls="{{ $paneId }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                    <div class="services-item__inner">
                                        <div class="services-item__icon">
                                            <img src="{{frontendImage('service', $serviceElement->data_values->icon_image ?? '', '60x60')}}" alt="thumbs" />
                                        </div>
                                        <div class="services-item__content">
                                            <h3 class="services-item__title">{{ __($serviceElement->data_values->title ?? '') }}</h3>
                                            <p class="services-item__desc">{{ __($serviceElement->data_values->description ?? '') }}</p>
                                        </div>
                                    </div>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="services-section__thumb-list right-tab-content tab-content" id="services-tabContent">
                    @foreach ($serviceElements as $serviceElement)
                    @php
                        $paneId = 'servies-pane-' . $loop->iteration;
                    @endphp
                        <div class="tab-pane fade services-section__thumb-item tab-item {{ $loop->first ? 'show active' : '' }}" id="{{ $paneId }}" role="tabpanel" aria-labelledby="services-tab-{{ $loop->iteration }}">
                            <img class="w-100" src="{{ frontendImage('service', $serviceElement->data_values->image ?? '', '1270x1135') }}" alt="Feature Thumbnail">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>