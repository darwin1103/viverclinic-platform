@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-7 col-lg-8">
            <h1>{{ __('Care Tips') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Care Tips') }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('care-tips.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('Title') }}</label>
                            <input type="text" class="form-control" id="title" name="title">
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
            <h3 class="mb-3">Tips Registrados</h3>
            @forelse($careTips as $tip)
                <div class="card mb-3">
                    <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                        {{ $tip->title }}
                        <form action="{{ route('care-tips.destroy', $tip->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este tip?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                    <div class="card-body">
                        {!! $tip->content !!}
                    </div>
                </div>
            @empty
                <div class="alert alert-info">Aún no hay tips de cuidado registrados.</div>
            @endforelse
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
