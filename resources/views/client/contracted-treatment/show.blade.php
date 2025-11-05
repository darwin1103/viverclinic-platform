@extends('layouts.app') {{-- Asumiendo tu layout de admin --}}

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 col-md-7 col-lg-8">
            <h1>Tratamiento</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tratamiento</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row d-flex justify-content-center">
        <div class="col-12 col-md-7 col-lg-10 mx-auto p-0">

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Detalles del Tratamiento Contratado</h4>
                    <a href="{{ route('client.contracted-treatment.index') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left-circle me-1"></i>
                        Volver al listado
                    </a>
                </div>

                <div class="card-body p-4">

                    {{-- SECCIÓN PRINCIPAL CON LA INFORMACIÓN MÁS IMPORTANTE --}}
                    <div class="p-3 mb-4 rounded bg-primary-subtle">
                        <div class="row align-items-center gy-3">
                            <div class="col-12 col-md-6">
                                <h5 class="text-muted mb-1">Cliente</h5>
                                <p class="h4 fw-light mb-0">{{ $contractedTreatment->user->name ?? 'N/A' }}</p>
                                <small>{{ $contractedTreatment->user->email ?? '' }}</small>
                            </div>
                            <div class="col-12 col-md-3 text-md-center">
                                <h5 class="text-muted mb-1">Estado</h5>
                                @if($contractedTreatment->status == 'Paid')
                                    <span class="badge fs-6 bg-success-subtle border border-success-subtle text-success-emphasis rounded-pill">Pagado</span>
                                @elseif($contractedTreatment->status == 'Pending')
                                    <span class="badge fs-6 bg-warning-subtle border border-warning-subtle text-warning-emphasis rounded-pill">Pendiente</span>
                                @else
                                    <span class="badge fs-6 bg-secondary-subtle border border-secondary-subtle text-secondary-emphasis rounded-pill">{{ $contractedTreatment->status }}</span>
                                @endif
                            </div>
                            <div class="col-12 col-md-3 text-md-end">
                                <h5 class="text-muted mb-1">Total</h5>
                                <p class="h4 fw-bold text-white mb-0">${{ number_format($contractedTreatment->total_price, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- DETALLES ADICIONALES --}}
                    <div class="row g-4">
                        {{-- Columna Izquierda: Detalles del Tratamiento --}}
                        <div class="col-12 col-md-6">
                            <h5 class="mb-3 border-bottom pb-2"><i class="bi bi-heart-pulse me-2"></i>Información del Tratamiento</h5>
                            <dl class="row">
                                <dt class="col-sm-5">Tratamiento:</dt>
                                <dd class="col-sm-7">{{ $contractedTreatment->treatment->name ?? 'N/A' }}</dd>

                                <dt class="col-sm-5">Sucursal:</dt>
                                <dd class="col-sm-7">{{ $contractedTreatment->branch->name ?? 'N/A' }}</dd>

                                <dt class="col-sm-5">Plan de Sesiones:</dt>
                                <dd class="col-sm-7">{{ $contractedTreatment->sessions }} sesiones</dd>

                                <dt class="col-sm-5">Frecuencia:</dt>
                                <dd class="col-sm-7">Cada {{ $contractedTreatment->days_between_sessions }} días</dd>

                                <dt class="col-sm-5">Fecha de Contratación:</dt>
                                <dd class="col-sm-7">
                                    @php
                                        \Carbon\Carbon::setLocale('es');
                                        echo \Carbon\Carbon::parse($contractedTreatment->created_at)->isoFormat('D \d\e MMMM, YYYY');
                                    @endphp
                                </dd>
                            </dl>
                        </div>

                        {{-- Columna Derecha: Desglose del Contrato --}}
                        <div class="col-12 col-md-6">
                            <h5 class="mb-3 border-bottom pb-2"><i class="bi bi-receipt-cutoff me-2"></i>Desglose del Contrato</h5>

                            {{-- Paquetes Contratados --}}
                            @if($contractedTreatment->contracted_packages && count($contractedTreatment->contracted_packages) > 0)
                                <p class="fw-bold mb-1">Paquetes Contratados:</p>
                                <ul class="list-group list-group-flush mb-3">
                                    @foreach($contractedTreatment->contracted_packages as $package)
                                        @if($package['quantity'] == 0)
                                            @continue
                                        @endif
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $package['quantity'] ?? 1 }}x {{ $package['name'] ?? 'N/A' }}
                                            <span class="badge bg-primary rounded-pill">${{ number_format($package['price_at_purchase'] ?? 0, 2) }} c/u</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                             {{-- Adicionales Contratados --}}
                            @if($contractedTreatment->contracted_additionals && count($contractedTreatment->contracted_additionals) > 0)
                                <p class="fw-bold mb-1">Adicionales:</p>
                                <ul class="list-group list-group-flush mb-3">
                                    @foreach($contractedTreatment->contracted_additionals as $additional)
                                        @if($additional['quantity'] == 0)
                                            @continue
                                        @endif
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $additional['quantity'] ?? 1 }}x {{ $additional['name'] ?? 'N/A' }}
                                            <span class="badge bg-info-subtle border border-info-subtle text-info-emphasis rounded-pill">${{ number_format($additional['price_at_purchase'] ?? 0, 2) }} c/u</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    {{-- Zonas Seleccionadas --}}
                    @if($contractedTreatment->selected_zones && (is_array($contractedTreatment->selected_zones)))
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="mb-3 border-bottom pb-2"><i class="bi bi-geo-alt me-2"></i>Zonas Seleccionadas</h5>

                            {{-- Display Big Zones --}}
                            @if(!empty($contractedTreatment->selected_zones['big']))
                                <h6 class="mt-3 mb-2 fw-bold">Zonas Grandes:</h6>
                                <div>
                                    @foreach($contractedTreatment->selected_zones['big'] as $zoneName)
                                        <span class="badge bg-primary-subtle border border-primary-subtle text-primary-emphasis rounded-pill p-2 me-1 mb-1">
                                            {{ $zoneName }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Display Mini Zones --}}
                            @if(!empty($contractedTreatment->selected_zones['mini']))
                                <h6 class="mt-3 mb-2 fw-bold">Zonas Mini:</h6>
                                <div>
                                    @foreach($contractedTreatment->selected_zones['mini'] as $zoneName)
                                        <span class="badge bg-info-subtle border border-info-subtle text-info-emphasis rounded-pill p-2 me-1 mb-1">
                                            {{ $zoneName }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Fallback for empty zones --}}
                            @if(empty($contractedTreatment->selected_zones['big']) && empty($contractedTreatment->selected_zones['mini']))
                                <p class="text-muted">No se especificaron zonas para este tratamiento.</p>
                            @endif

                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
