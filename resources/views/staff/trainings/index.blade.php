@extends('layouts.employee')
@section('content')
<div class="container-fluid p-0 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fw-bold">Capacitaciones</h1>
            <p class="text-muted">Material de entrenamiento y videos educativos para el personal.</p>
        </div>
    </div>

    <div class="row g-4">
        @forelse($trainings as $training)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    @if($training->youtube_id)
                        <div class="ratio ratio-16x9 card-img-top">
                            <iframe src="https://www.youtube.com/embed/{{ $training->youtube_id }}" title="YouTube video" allowfullscreen></iframe>
                        </div>
                    @else
                        <div class="card-img-top bg-secondary d-flex justify-content-center align-items-center" style="height: 200px;">
                            <i class="bi bi-play-circle text-white" style="font-size: 4rem;"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title fw-bold">{{ $training->title }}</h5>
                        <div class="card-text mt-3">
                            {!! $training->description !!}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm">
                    No hay capacitaciones disponibles en este momento.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
