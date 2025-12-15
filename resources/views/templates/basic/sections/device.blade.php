@php
    $deviceContent = getContent('device.content', true);
@endphp
<section class="device-support py-120">
    <div class="container">
        <div class="row gy-4 align-items-center">
            <div class="col-lg-6">
                <div class="section-heading style-left">
                    <h2 class="section-heading__title wow animationfadeUp" data-highlight="5" data-wow-delay="0.3s"> {{ __($deviceContent->data_values->heading ?? '') }}</h2>
                    <p class="section-heading__desc wow animationfadeUp text--body" data-wow-delay="0.4s"> {{ __($deviceContent->data_values->description ?? '') }}</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="device-support__thumb wow animationfadeRight" data-wow-delay="0.6s">
                    <img src="{{ frontendImage('device', $deviceContent->data_values->image ?? '', '1270x740') }}" alt="img">
                </div>
            </div>
        </div>
    </div>
</section>