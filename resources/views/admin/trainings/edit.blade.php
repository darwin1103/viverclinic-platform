@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-7 col-lg-8">
            <h1>Editar Capacitación</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.trainings.index') }}">Capacitaciones</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.trainings.update', $training->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="title" class="form-label">Título</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $training->title) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="youtube_url" class="form-label">URL de YouTube (Opcional)</label>
                            <input type="url" class="form-control" id="youtube_url" name="youtube_url" value="{{ old('youtube_url', $training->youtube_url) }}" placeholder="https://www.youtube.com/watch?v=...">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción Corta</label>
                            <textarea id="description" name="description" class="form-control" rows="2" required maxlength="500">{{ old('description', $training->description) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Contenido Detallado</label>
                            <textarea id="content" name="content" class="form-control ckeditor-textarea">{{ old('content', $training->content) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                        <a href="{{ route('admin.trainings.index') }}" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
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
