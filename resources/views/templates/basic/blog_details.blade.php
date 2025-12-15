@php
    @$blogElements = getContent('blog.element', false, orderById: true);
@endphp
@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog-section blog-details pt-80 pb-80">
        <div class="container">
            <div class="row gy-4 gy-md-5">
                <div class="col-lg-8 col-md-12 pe-lg-4 pe-xl-5">
                    <div class="post-item p-0">
                        <div class="post-item__thumb">
                            <img class="fit-image" src="{{ frontendImage('blog', @$blog->data_values->image, '830x460') }}"
                                alt="">
                        </div>
                        <div class="post-item__content">
                            <ul class="d-flex flex-wrap gap-4 mb-3 align-items-center">
                                <li><i class="far fa-calendar"></i> {{ showDateTime($blog->created_at, format: 'M d , Y') }}
                                </li>
                            </ul>
                            <h4 class="post-item__content-title">{{ __($blog->data_values->title) }}</h4>
                            <p>
                                @php echo $blog->data_values->description @endphp
                            </p>
                            <div class="mt-4">
                                <h5 class="blog-sidebar__title mt-0 mb-2">@lang('Share')</h5>
                                <ul class="social-list">
                                    <li class="social-list__item">
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                            class="social-list__link flex-center" target="_blank">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                    </li>
                                    <li class="social-list__item">
                                        <a href="https://x.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode(@$blog->data_values->title) }}"
                                            class="social-list__link flex-center" target="_blank">
                                            <i class="fab fa-x-twitter"></i>
                                        </a>
                                    </li>
                                    <li class="social-list__item">
                                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode(@$blog->data_values->title) }}"
                                            class="social-list__link flex-center" target="_blank">
                                            <i class="fab fa-linkedin-in"></i>
                                        </a>
                                    </li>
                                    <li class="social-list__item">
                                        <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&media={{ urlencode(frontendImage('blog', @$blog->data_values->image)) }}&description={{ urlencode(@$blog->data_values->title) }}"
                                            class="social-list__link flex-center" target="_blank">
                                            <i class="fab fa-pinterest"></i>
                                        </a>
                                    </li>
                                </ul>
                                <div class="fb-comments" data-href="{{ url()->current() }}" data-numposts="5"></div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-lg-4">
                    <div class="blog-sidebar">
                        <h4 class="blog-sidebar__title">@lang('Latest Blogs')</h4>
                        <ul class="latest-posts m-0">
                            @foreach ($blogElements as $blogElement)
                                <li>
                                    <div class="post-thumb">
                                        <a href="{{ route('blog.details', $blogElement->slug) }}">
                                            <img src="{{ frontendImage('blog', 'thumb_' . @$blogElement->data_values->image) }}"
                                                alt="image">
                                        </a>
                                    </div>
                                    <div class="post-info">
                                        <h6 class="title">
                                            <a href="{{ route('blog.details', $blogElement->slug) }}">
                                                {{ __($blogElement->data_values->title) }}
                                            </a>
                                        </h6>
                                        <span class="posts-date"><i class="far fa-calendar-alt"></i>
                                            {{ showDateTime($blogElement->created_at, format: 'M d , Y') }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="fb-comments" data-href="{{ url()->current() }}" data-numposts="5"></div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('fbComment')
    @php echo loadExtension('fb-comment') @endphp
@endpush


@push('style')
    <style>
        .blog-details  h4 {
            margin-bottom: 1rem;
                color: hsl(var(--heading-color)/0.8);
        }
    </style>
@endpush
