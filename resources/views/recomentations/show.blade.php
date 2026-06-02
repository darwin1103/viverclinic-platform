@extends('layouts.app')
@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('recomentations.index') }}">Recomendaciones</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $recommendation->title }}</li>
        </ol>
    </nav>

    <div class="card shadow-sm border-0">
        @if($recommendation->image)
            <img src="{{ Storage::url($recommendation->image) }}" class="card-img-top" alt="{{ $recommendation->title }}" style="max-height: 400px; object-fit: cover;">
        @endif
        <div class="card-body p-3 p-md-5">
            <h1 class="fw-bold text-primary mb-3">{{ $recommendation->title }}</h1>
            <p class="lead text-muted border-bottom pb-3 mb-4">{{ $recommendation->description }}</p>
            
            <div class="content-body" style="line-height: 1.8; font-size: 1.1rem;">
                {!! $recommendation->content !!}
            </div>

            <div class="mt-5">
                <a href="{{ route('recomentations.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i> Volver a Recomendaciones</a>
            </div>
        </div>
    </div>
</div>
@endsection
