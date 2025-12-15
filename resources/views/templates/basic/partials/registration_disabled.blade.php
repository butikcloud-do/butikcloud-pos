@php
    $registrationDisabled = getContent('register_disable.content', true);
@endphp
<div class="py-60">

    <div class="container">
        <div class="row d-flex justify-content-center align-items-center">
            <div class="col-lg-8">
                <div class="register-disable text-center">
                    <div class="register-disable-image mb-4">
                        <img class="fit-image"
                            src="{{ frontendImage('register_disable', @$registrationDisabled->data_values->image, '280x280') }}"
                            alt="">
                    </div>
                    <h3 class="register-disable-title text--danger">
                        {{ __(@$registrationDisabled->data_values->heading) }}
                    </h3>
                    <p class="register-disable-desc mb-4">
                        {{ __(@$registrationDisabled->data_values->subheading) }}
                    </p>
                    <div class="text-center">
                        <a href="{{ @$registrationDisabled->data_values->button_url }}"
                            class="btn btn--base">{{ __(@$registrationDisabled->data_values->button_name) }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('style')
    <style>
        .register-disable-image img {
            max-width: 250px;
        }
    </style>
@endpush
