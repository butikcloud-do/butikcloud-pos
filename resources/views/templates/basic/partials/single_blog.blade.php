@php
    $blogContent = getContent('blog.content', true);
    $blogElements = getContent('blog.element', false, orderById: true);
@endphp
<div class="blog-card">
    <a href="{{ route('blog.details', $blogElement->slug ?? '') }}" class="blog-card__link"></a>
    <div class="blog-card__thumb" href="{{ route('blog.details', $blogElement->slug ?? '') }}">
        @if (request()->routeIs('home'))
            <img src="{{ frontendImage('blog', 'thumb_' . $blogElement->data_values->image ?? '', '425x530') }}"
                alt="" />
        @else
            <img src="{{ frontendImage('blog', $blogElement->data_values->image ?? '', '985x450') }}" alt="" />
        @endif
        <span class="blog-card__date">{{ showDateTime($blogElement->created_at, 'M d,Y') }}</span>
    </div>
    <div class="blog-card__content">
        <h5 class="blog-card__title">
            <a href="{{ route('blog.details', $blogElement->slug ?? '') }}" class="blog-card__title-link">
                {{ __($blogElement->data_values->title ?? '') }}</a>
        </h5>
        <p class="blog-card__excerpt">
            @php
                echo Str::words(strip_tags($blogElement->data_values->description), 100, '...');
            @endphp
        </p>

        <a href="{{ route('blog.details', $blogElement->slug ?? '') }}" class="read-more-btn">Read More
            <span class="read-more-btn__icon"><i class="fas fa-arrow-right-long"></i></span></a>
    </div>
</div>

