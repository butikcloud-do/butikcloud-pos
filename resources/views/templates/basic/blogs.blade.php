@php
    $blogContent = getContent('blog.content', true);
    $blogElements = getContent('blog.element', false, orderById: true);
@endphp

@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog-section py-120 bg-light-shape">
        <div class="container">
            <div class="section-heading">
                <h2 class="section-heading__title wow animationfadeUp" data-wow-delay="0.3s" data-highlight="-1">
                    {{ __($blogContent->data_values->heading) }}</h2>
                <p class="section-heading__desc wow animationfadeUp" data-wow-delay="0.4s">
                    {{ __($blogContent->data_values->subheading) }}</p>
            </div>
            <div class="row gy-4 mt-4 justify-content-center">
                @foreach ($blogElements as $blogElement)
                    <div class="col-md-6 col-xl-4 col-sm-12">
                        <div class="post-item">
                            <div class="post-thumb">
                                <a href="{{ route('blog.details', $blogElement->slug) }}" class="d-block">
                                    <img class="fit-image"
                                        src="{{ frontendImage('blog', 'thumb_' . @$blogElement->data_values->image, '415x230') }}">
                                </a>
                            </div>
                            <div class="post-content">
                                <div class="meta-post">
                                    <div class="date blog-date">
                                        <span class="d-inline-block">
                                            <i class="far fa-calendar-alt text-muted"></i>
                                            {{ $blogElement->created_at }}
                                        </span>
                                    </div>
                                </div>
                                <div class="blog-header pt-0">
                                    <h5 class="title">
                                        <a href="{{ route('blog.details', $blogElement->slug) }}">
                                            {{ __(strLimit(@$blogElement->data_values->title, 50)) }}
                                        </a>
                                    </h5>
                                </div>
                                <p class="entry-content">
                                    @php
                                        echo strLimit(strip_tags($blogElement->data_values->description), 100);
                                    @endphp
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>


    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
