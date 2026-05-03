@extends('layouts.app')
@section('content')
<div class="container-fluid p-0 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fw-bold">Recomendaciones</h1>
            <p class="text-muted">Descubre nuestras mejores sugerencias para ti.</p>
        </div>
    </div>

    <div class="row g-4">
        @forelse($recommendations as $rec)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    @if($rec->image)
                        <img src="{{ Storage::url($rec->image) }}" class="card-img-top" alt="{{ $rec->title }}" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-secondary d-flex justify-content-center align-items-center" style="height: 200px;">
                            <i class="bi bi-star text-white" style="font-size: 4rem;"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title fw-bold">{{ $rec->title }}</h5>
                        <p class="card-text mt-3 text-muted">
                            {{ $rec->description }}
                        </p>
                        <a href="{{ route('recomentations.show', $rec->id) }}" class="btn btn-outline-primary w-100 mt-2">Leer más</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm">
                    Aún no hay recomendaciones disponibles.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
