@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-7 col-lg-8">
            <h1>{{ __('Recommendations') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Recommendations') }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.recomentations.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Título</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción Corta</label>
                            <textarea id="description" name="description" class="form-control" rows="2" required maxlength="500"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Imagen (Opcional)</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">{{ __('Content') }}</label>
                            <textarea id="content" name="content" class="form-control ckeditor-textarea"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <h3 class="mb-3">Recomendaciones Registradas</h3>
            <div class="row g-4">
                @forelse($recommendations as $rec)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100">
                            @if($rec->image)
                                <img src="{{ Storage::url($rec->image) }}" class="card-img-top" alt="{{ $rec->title }}" style="height: 200px; object-fit: cover;">
                            @endif
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold">{{ $rec->title }}</h5>
                                <p class="card-text text-muted flex-grow-1">{{ $rec->description }}</p>
                                <a href="{{ route('admin.recomentations.show', $rec->id) }}" class="btn btn-sm btn-outline-primary w-100 mt-auto">Ver Detalles</a>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.recomentations.edit', $rec->id) }}" class="btn btn-sm btn-primary flex-grow-1"><i class="bi bi-pencil"></i> Editar</a>
                                    <form action="{{ route('admin.recomentations.destroy', $rec->id) }}" method="POST" class="d-inline flex-grow-1" onsubmit="return confirm('¿Eliminar esta recomendación?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger w-100"><i class="bi bi-trash"></i> Eliminar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">No hay recomendaciones registradas.</div>
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
