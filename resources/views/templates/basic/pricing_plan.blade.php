@extends($activeTemplate . 'layouts.frontend')
@section('content')

    @include('Template::sections.pricing')
    
    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @includeif($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
