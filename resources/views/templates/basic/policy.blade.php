@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="py-120 policy">
        <div class="container ">
            <div class="row">
                <div class="col-md-12">
                    @php
                        echo $policy->data_values->details;
                    @endphp
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
