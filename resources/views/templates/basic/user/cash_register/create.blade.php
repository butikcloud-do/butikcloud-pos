@extends($activeTemplate . 'layouts.master')
@section('panel')
    @if ($cashRegister)
        @include('Template::user.cash_register.dashboard')
    @else
        @include('Template::user.cash_register.open_register')
    @endif
@endsection
