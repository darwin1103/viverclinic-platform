@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-8 col-lg-9 mx-auto">
            <nav aria-label="breadcrumb" class="mt-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.trainings.index') }}">Capacitaciones</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $training->title }}</li>
                </ol>
            </nav>

            <div class="card shadow-sm mt-3 border-0">
                @if($training->youtube_id)
                    <div class="ratio ratio-16x9 card-img-top">
                        <iframe src="https://www.youtube.com/embed/{{ $training->youtube_id }}" title="YouTube video" allowfullscreen></iframe>
                    </div>
                @endif
                <div class="card-body p-5">
                    <h1 class="card-title fw-bold text-primary mb-3">{{ $training->title }}</h1>
                    <p class="lead text-muted border-bottom pb-3 mb-4">{{ $training->description }}</p>
                    
                    <div class="content-body text-white" style="line-height: 1.8; font-size: 1.1rem;">
                        {!! $training->content !!}
                    </div>

                    <div class="mt-5 d-flex gap-2">
                        <a href="{{ route('admin.trainings.edit', $training->id) }}" class="btn btn-primary"><i class="bi bi-pencil me-2"></i> Editar</a>
                        <a href="{{ route('admin.trainings.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i> Volver a la lista</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
