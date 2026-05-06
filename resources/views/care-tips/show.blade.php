@extends('layouts.app')
@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('care-tips.index') }}">Tips de Cuidado</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $careTip->title }}</li>
        </ol>
    </nav>

    <div class="card shadow-sm border-0">
        @if($careTip->image)
            <img src="{{ Storage::url($careTip->image) }}" class="card-img-top" alt="{{ $careTip->title }}" style="max-height: 400px; object-fit: cover;">
        @endif
        <div class="card-body p-5">
            <h1 class="fw-bold text-primary mb-3">{{ $careTip->title }}</h1>
            <p class="lead text-muted border-bottom pb-3 mb-4">{{ $careTip->description }}</p>
            
            <div class="content-body" style="line-height: 1.8; font-size: 1.1rem;">
                {!! $careTip->content !!}
            </div>

            <div class="mt-5">
                <a href="{{ route('care-tips.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i> Volver a Tips</a>
            </div>
        </div>
    </div>
</div>
@endsection
