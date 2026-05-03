@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-7 col-lg-8">
            <h1>Capacitaciones</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Capacitaciones</li>
                </ol>
            </nav>
        </div>
    </div>
    @role('SUPER_ADMIN|OWNER')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.trainings.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Título</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="youtube_url" class="form-label">URL de YouTube (Opcional)</label>
                            <input type="url" class="form-control" id="youtube_url" name="youtube_url" placeholder="https://www.youtube.com/watch?v=...">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción Corta</label>
                            <textarea id="description" name="description" class="form-control" rows="2" required maxlength="500"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Contenido Detallado</label>
                            <textarea id="content" name="content" class="form-control ckeditor-textarea"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Capacitación</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endrole

    <div class="row mt-4">
        <div class="col-12">
            <h3 class="mb-3">Capacitaciones Registradas</h3>
            <div class="row g-4">
                @forelse($trainings as $training)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100">
                            @if($training->youtube_id)
                                <div class="ratio ratio-16x9 card-img-top">
                                    <iframe src="https://www.youtube.com/embed/{{ $training->youtube_id }}" title="YouTube video" allowfullscreen></iframe>
                                </div>
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{ $training->title }}</h5>
                                <p class="card-text text-muted">{{ $training->description }}</p>
                                <a href="{{ route('admin.trainings.show', $training->id) }}" class="btn btn-sm btn-outline-primary w-100 mt-2">Ver Detalles</a>
                            </div>
                            @role('SUPER_ADMIN|OWNER')
                            <div class="card-footer bg-transparent">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.trainings.edit', $training->id) }}" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i> Editar</a>
                                    <form action="{{ route('admin.trainings.destroy', $training->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta capacitación?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Eliminar</button>
                                    </form>
                                </div>
                            </div>
                            @endrole
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">No hay capacitaciones registradas.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/editor.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('ckeditor/build/ckeditor.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const textareas = document.querySelectorAll('.ckeditor-textarea');
            textareas.forEach(textarea => {
                ClassicEditor.create(textarea).catch(error => { console.error(error); });
            });
        });
    </script>
@endpush
@endsection
