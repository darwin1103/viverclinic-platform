@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1>Nuestros Tratamientos</h1>
            <h5 class="text-muted">Disponibles en: {{ $branch->name }}</h5>
        </div>
    </div>
    <div class="row g-4">
        @forelse ($treatments as $treatment)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm treatment-card">

                    <a href="{{ route('client.treatment.show', ['treatment' => $treatment->id]) }}" class="text-decoration-none d-flex flex-column h-100">

                        <img src="{{ $treatment->main_image ? Storage::url($treatment->main_image) : 'https://via.placeholder.com/400x250' }}" class="card-img-top" alt="{{ $treatment->name }}" style="height: 250px; object-fit: cover;">

                        <div class="card-body d-flex flex-column pb-0">
                            <h5 class="card-title fw-bold">{{ $treatment->name }}</h5>
                            <p class="card-text text-muted flex-grow-1">{{ Str::limit($treatment->description, 120) }}</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item bg-transparent px-0">
                                    <span class="badge bg-primary rounded-pill">{{ $treatment->sessions }}</span>
                                    sesiones cada
                                    <span class="badge bg-secondary rounded-pill">{{ $treatment->days_between_sessions }} días</span>
                                </li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 p-3">
                           <div class="btn btn-primary w-100">
                               Ver detalles
                           </div>
                        </div>
                    </a>

                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    No hay tratamientos disponibles en esta sucursal en este momento.
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
/* Estilo opcional para que la tarjeta se sienta más clickeable */
.treatment-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.treatment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
}
</style>
@endsection
