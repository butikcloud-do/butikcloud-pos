@extends($activeTemplate . 'layouts.app')
@section('app-content')
    @include('Template::partials.header')
    <main>
        @yield('content')
    </main>
    @include('Template::partials.footer')
@endsection
