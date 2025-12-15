@php
    $ctaContent = getContent('cta.content', true);
    $ctaElements = getContent('cta.element', false, orderById: true);
@endphp

<div class="cta-section my-80">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="cta-wrapper">
                    <svg class="cta-shape" xmlns="http://www.w3.org/2000/svg" width="547" height="412"
                        viewBox="0 0 547 412" fill="none">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M444.155 206.959C493.853 127.018 496.789 51.2806 443.336 17.3837C370.656 -28.707 221.244 20.0124 109.616 126.201C-2.01271 232.39 -33.586 355.837 39.0946 401.928C64.5173 418.05 99.328 422.572 138.626 417.092C108.475 499.03 130.418 569.685 198.957 587.326C242.631 598.57 296.696 586.09 348.147 556.971C349.333 590.546 371.05 616.156 407.942 621.138C461.641 628.382 527.159 589.171 554.279 533.555C581.401 477.939 559.853 426.98 506.152 419.733C499.619 418.851 492.91 418.657 486.117 419.098C541.68 320.496 525.128 227.795 445.186 207.22C444.843 207.132 444.499 207.045 444.155 206.959Z"
                            fill="hsl(var(--base))" />
                    </svg>
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="cta-content">
                                <h2 class="cta-content__title wow animationfadeUp" data-highlight="4_-1"
                                    data-wow-delay="0.2s">{{ __($ctaContent->data_values->heading ?? '') }}</h2>
                                <p class="cta-content__desc wow animationfadeUp" data-wow-delay="0.3s">
                                    {{ __($ctaContent->data_values->description ?? '') }}</p>
                                <div class="cta-content__buttons d-flex gap-3 wow animationfadeUp"
                                    data-wow-delay="0.4s">
                                    <a href="{{ $ctaContent->data_values->button_one_url ?? '' }}"
                                        class="btn btn--white">
                                        {{ __($ctaContent->data_values->button_one_name ?? '') }}
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16" fill="none">
                                            <path d="M3.33337 8H12.6667" stroke="CurrentColor" stroke-width="1.33333"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M8 3.33203L12.6667 7.9987L8 12.6654" stroke="CurrentColor"
                                                stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                    <a href="{{ $ctaContent->data_values->button_two_url ?? '' }}"
                                        class="btn btn-outline--white">
                                        {{ __($ctaContent->data_values->button_two_name ?? '') }}
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16" fill="none">
                                            <path d="M3.33337 8H12.6667" stroke="CurrentColor" stroke-width="1.33333"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M8 3.33203L12.6667 7.9987L8 12.6654" stroke="CurrentColor"
                                                stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 d-lg-block d-none">
                            <div class="cta-thumb wow animationfadeUp" data-wow-delay="0.3s">
                                <img src="{{ frontendImage('cta', $ctaContent->data_values->image ?? '', '1050x710') }}"
                                    alt="img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/odometer.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/odometer.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/viewport.jquery.js') }}"></script>
@endpush
