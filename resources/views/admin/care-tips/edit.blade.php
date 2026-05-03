@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-7 col-lg-8">
            <h1>Editar Tip de Cuidado</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.care-tips.index') }}">Tips de Cuidado</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.care-tips.update', $careTip->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('Title') }}</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $careTip->title) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción Corta</label>
                            <textarea id="description" name="description" class="form-control" rows="2" required maxlength="500">{{ old('description', $careTip->description) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Imagen (Opcional)</label>
                            @if($careTip->image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($careTip->image) }}" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            @endif
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <small class="text-muted">Sube una nueva imagen solo si deseas reemplazar la actual.</small>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">{{ __('Content') }}</label>
                            <textarea id="content" name="content" class="form-control ckeditor-textarea">{{ old('content', $careTip->content) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                        <a href="{{ route('admin.care-tips.index') }}" class="btn btn-secondary">Cancelar</a>
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
