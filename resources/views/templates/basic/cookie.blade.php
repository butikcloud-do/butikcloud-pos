@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="py-120 policy">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    @php echo $cookie?->data_values?->description @endphp
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        .policy h4 {
            margin-bottom: 1rem;
                color: hsl(var(--heading-color)/0.8);
        }
    </style>
@endpush

