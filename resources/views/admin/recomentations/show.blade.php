@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-8 col-lg-9 mx-auto">
            <nav aria-label="breadcrumb" class="mt-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.recomentations.index') }}">Recomendaciones</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $recommendation->title }}</li>
                </ol>
            </nav>

            <div class="card shadow-sm mt-3 border-0">
                @if($recommendation->image)
                    <img src="{{ Storage::url($recommendation->image) }}" class="card-img-top" alt="{{ $recommendation->title }}" style="max-height: 400px; object-fit: cover;">
                @endif
                <div class="card-body p-5">
                    <h1 class="card-title fw-bold text-primary mb-3">{{ $recommendation->title }}</h1>
                    <p class="lead text-muted border-bottom pb-3 mb-4">{{ $recommendation->description }}</p>
                    
                    <div class="content-body text-white" style="line-height: 1.8; font-size: 1.1rem;">
                        {!! $recommendation->content !!}
                    </div>

                    <div class="mt-5 d-flex gap-2">
                        <a href="{{ route('admin.recomentations.edit', $recommendation->id) }}" class="btn btn-primary"><i class="bi bi-pencil me-2"></i> Editar</a>
                        <a href="{{ route('admin.recomentations.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i> Volver a la lista</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
