@extends('layouts.app')
@section('content')
<div class="container-fluid p-0 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fw-bold">Tips de Cuidado</h1>
            <p class="text-muted">Recomendaciones para el cuidado de tus tratamientos.</p>
        </div>
    </div>

    <div class="row g-4">
        @forelse($careTips as $tip)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    @if($tip->image)
                        <img src="{{ Storage::url($tip->image) }}" class="card-img-top" alt="{{ $tip->title }}" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-secondary d-flex justify-content-center align-items-center" style="height: 200px;">
                            <i class="bi bi-heart-pulse text-white" style="font-size: 4rem;"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title fw-bold">{{ $tip->title }}</h5>
                        <p class="card-text mt-3 text-muted">
                            {{ $tip->description }}
                        </p>
                        <a href="{{ route('care-tips.show', $tip->id) }}" class="btn btn-outline-primary w-100 mt-2">Leer más</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm">
                    Aún no hay tips de cuidado disponibles.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
