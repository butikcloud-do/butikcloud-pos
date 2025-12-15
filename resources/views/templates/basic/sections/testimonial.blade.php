@php
    $testimonialContent = getContent('testimonial.content', true);
    $testimonialElements = getContent('testimonial.element', false, orderById: true);
@endphp

<section class="testimonials py-120 bg-light-shape">
    <div class="container">
        <div class="section-heading">
            <h2 class="section-heading__title wow animationfadeUp" data-highlight="3"  ="0.3s">
                {{ __($testimonialContent->data_values->heading ?? '') }}</span></h2>
            <p class="section-heading__desc wow animationfadeUp" data-wow-delay="0.4s">
                {{ __($testimonialContent->data_values->subheading ?? '') }} </p>
        </div>
        <div class="testimonial-wrapper wow animationfadeUp" data-wow-delay="0.5s">
            <div class="testimonial-slider">
                @foreach ($testimonialElements as $testimonialElement)
                    <div class="testimonails-card">
                        <div class="testimonial-item">
                            <div class="testimonial-item__content">
                                <div class="testimonial-item__quote">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                        viewBox="0 0 32 32" fill="none">
                                        <path d="M0 4V28L12 16V4H0Z" fill="hsl(var(--base))" />
                                        <path d="M20 4V28L32 16V4H20Z" fill="hsl(var(--base))" />
                                    </svg>
                                </div>
                                <div class="testimonial-item__rating">
                                    <ul class="star-rating">

                                        @for ($i = 0; $i < $testimonialElement->data_values->rating; $i++)
                                            <li class="star-rating__item"><i class="fas fa-star"></i></li>
                                        @endfor
                                    </ul>
                                </div>
                            </div>
                            <p class="testimonial-item__desc">
                                {{ __($testimonialElement->data_values->description ?? '') }}</p>
                            <div class="testimonial-item__info">
                                <div class="testimonial-item__thumb">
                                    <img class="fit-image"
                                        src="{{ frontendImage('testimonial', $testimonialElement->data_values->thumb ?? '', '100x100') }}"
                                        alt="">
                                </div>
                                <div class="testimonial-item__details">
                                    <h5 class="testimonial-item__name">
                                        {{ __($testimonialElement->data_values->name ?? '') }}</h5>
                                    <span class="testimonial-item__designation">
                                        {{ __($testimonialElement->data_values->designation ?? '') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="testimonial-slider-ctrl wow animationfadeUp" data-wow-delay="0.6s"></div>
    </div>
</section>
