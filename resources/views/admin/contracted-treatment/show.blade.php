@extends('layouts.admin') {{-- Asumiendo tu layout de admin --}}

@section('content')
<div class="container-fluid">
    <div class="row d-flex justify-content-center">
        <div class="col-12">

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Detalles del Tratamiento Contratado</h4>
                    <a href="{{ route('admin.contracted-treatment.index') }}" class="btn btn-primary">
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
                                        echo \Carbon\Carbon::parse($contractedTreatment->created_at)->isoFormat('dddd, D \d\e MMMM, YYYY');
                                    @endphp
                                </dd>

                                <dt class="col-sm-5">Términos y condiciones:</dt>
                                <dd class="col-sm-7">{{ $contractedTreatment->terms_acepted ? 'Aceptado' : 'No aceptado' }}</dd>

                                <dt class="col-sm-5">Esta en embarazo:</dt>
                                <dd class="col-sm-7">{{ $contractedTreatment->is_pregnant ? 'Si' : 'No' }}</dd>

                            </dl>
                        </div>

                        {{-- Columna Derecha: Desglose del Contrato --}}
                        <div class="col-12 col-md-6">
                            <h5 class="mb-3 border-bottom pb-2"><i class="bi bi-receipt-cutoff me-2"></i>Desglose del Contrato</h5>

                            {{-- Paquetes Contratados --}}
                            @php
                                // Aseguramos que los paquetes sean un array.
                                $packagesData = $contractedTreatment->contracted_packages;
                                if (is_string($packagesData)) {
                                    $packagesData = json_decode($packagesData, true);
                                }

                                // Filtramos la colección para quedarnos solo con los paquetes que tienen cantidad > 0.
                                $visiblePackages = collect($packagesData)->where('quantity', '>', 0);
                            @endphp

                            {{-- Solo mostramos el bloque si la colección filtrada no está vacía. --}}
                            @if($visiblePackages->isNotEmpty())
                                <p class="fw-bold mb-1">Paquetes Contratados:</p>
                                <ul class="list-group list-group-flush mb-3">
                                    {{-- Hacemos el bucle sobre la colección ya filtrada. --}}
                                    @foreach($visiblePackages as $package)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $package['quantity'] }}x {{ $package['name'] }}
                                            <span class="badge bg-primary rounded-pill">${{ number_format($package['price_at_purchase'], 2) }} c/u</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            {{-- Adicionales Contratados --}}
                            @php
                                // Primero, nos aseguramos de que los adicionales sean un array y no un string JSON.
                                $additionalsData = $contractedTreatment->contracted_additionals;
                                if (is_string($additionalsData)) {
                                    $additionalsData = json_decode($additionalsData, true);
                                }

                                // Luego, creamos una colección de Laravel y filtramos para quedarnos solo con los que tienen cantidad > 0.
                                $visibleAdditionals = collect($additionalsData)->where('quantity', '>', 0);
                            @endphp

                            {{-- Ahora la condición principal es muy simple: solo mostramos el bloque si la colección filtrada no está vacía. --}}
                            @if($visibleAdditionals->isNotEmpty())
                                <p class="fw-bold mb-1">Adicionales:</p>
                                <ul class="list-group list-group-flush mb-3">
                                    {{-- Hacemos el bucle sobre la colección ya filtrada. --}}
                                    @foreach($visibleAdditionals as $additional)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $additional['quantity'] }}x {{ $additional['name'] }}
                                            <span class="badge bg-info-subtle border border-info-subtle text-info-emphasis rounded-pill">${{ number_format($additional['price_at_purchase'], 2) }} c/u</span>
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
                            <h5 class="mb-3 border-bottom pb-2"><i class="bi bi-person-standing me-2"></i>Zonas Seleccionadas</h5>

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












                    <hr class="my-5">

                    <div class="row g-4">

                        {{-- 1. ESTADO DE LAS CUOTAS --}}
                        <div class="col-12">
                            <h5 class="mb-3 fw-bold text-primary">
                                <i class="bi bi-list-ol me-2"></i> Plan de Cuotas
                            </h5>

                            @if($contractedTreatment->installments->isEmpty())
                                <div class="alert alert-secondary">
                                    Este tratamiento no tiene cuotas configuradas (Pago único).
                                </div>
                            @else
                                <div class="table-responsive border rounded">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-white">#</th>
                                                <th class="text-white">Monto</th>
                                                <th class="text-white">Estado</th>
                                                <th class="text-white">Fecha Pago</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($contractedTreatment->installments->sortBy('installment_number') as $inst)
                                                <tr>
                                                    <td class="fw-bold">{{ $inst->installment_number }}</td>
                                                    <td>${{ number_format($inst->price, 2) }}</td>
                                                    <td>
                                                        @if($inst->status == 'PAID')
                                                            <span class="badge bg-success">Pagada</span>
                                                        @else
                                                            <span class="badge bg-secondary">Pendiente</span>
                                                        @endif
                                                    </td>
                                                    <td class="small text-muted">
                                                        {{ $inst->paid_at ? $inst->paid_at->format('d/m/Y H:i') : '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-2 text-end small text-muted">
                                    Progreso:
                                    <strong>{{ $contractedTreatment->installments->where('status', 'PAID')->count() }}</strong>
                                    de
                                    <strong>{{ $contractedTreatment->installments->count() }}</strong> pagadas.
                                </div>
                            @endif
                        </div>

                        {{-- 2. HISTORIAL DE ÓRDENES / APROBACIÓN --}}
                        <div class="col-12">
                            <h5 class="mb-3 fw-bold text-primary">
                                <i class="bi bi-cash-coin me-2"></i> Historial de Transacciones
                            </h5>

                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-white">Fecha</th>
                                            <th class="text-white">Método</th>
                                            <th class="text-white">Total</th>
                                            <th class="text-white">Estado</th>
                                            <th class="text-end text-white">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($contractedTreatment->orders->sortByDesc('created_at') as $order)
                                            <tr>
                                                <td class="small">
                                                    {{ $order->created_at->format('d/m/Y') }}<br>
                                                    <span class="text-muted">{{ $order->created_at->format('H:i') }}</span>
                                                </td>
                                                <td>
                                                    <span class="fw-bold d-block">{{ $order->payment_method }}</span>
                                                    <small class="text-muted text-truncate d-block" style="max-width: 150px;" title="{{ $order->payment_description }}">
                                                        {{ $order->payment_description }}
                                                    </small>
                                                    @if($order->payment_receipt)
                                                        <a href="{{ Storage::url($order->payment_receipt) }}" target="_blank" class="btn btn-link btn-sm p-0 text-decoration-none">
                                                            <i class="bi bi-paperclip"></i> Ver Comprobante
                                                        </a>
                                                    @endif
                                                </td>
                                                <td class="fw-bold">${{ number_format($order->total, 2) }}</td>
                                                <td>
                                                    @if($order->status == 'Pago completado')
                                                        <span class="badge bg-success"><i class="bi bi-check-lg"></i> Aprobado</span>
                                                    @elseif($order->status == 'Cancelado')
                                                        <span class="badge bg-danger"><i class="bi bi-x-lg"></i> Rechazado</span>
                                                    @elseif($order->status == 'Pago por verificar')
                                                        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Por Verificar</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $order->status }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @if($order->status == 'Pago por verificar')
                                                        <div class="btn-group btn-group-sm">
                                                            {{-- Botón Aprobar --}}
                                                            <form action="{{ route('admin.contracted-treatment.approve-payment', $order->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de APROBAR este pago? Esto marcará las cuotas como pagadas.');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success" title="Aprobar Pago">
                                                                    <i class="bi bi-check-circle"></i>
                                                                </button>
                                                            </form>

                                                            {{-- Botón Rechazar (Modal Trigger) --}}
                                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $order->id }}" title="Rechazar Pago">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        </div>

                                                        {{-- Modal de Rechazo --}}
                                                        <div class="modal fade" id="rejectModal-{{ $order->id }}" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <form action="{{ route('admin.contracted-treatment.reject-payment', $order->id) }}" method="POST">
                                                                    @csrf
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Rechazar Pago</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                        </div>
                                                                        <div class="modal-body text-start">
                                                                            <p>Indica el motivo del rechazo (visible para el cliente):</p>
                                                                            <textarea name="reason" class="form-control" rows="3" placeholder="Ej: Comprobante ilegible, monto incorrecto..."></textarea>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                            <button type="submit" class="btn btn-danger">Confirmar Rechazo</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">No hay transacciones registradas.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>












                </div>
            </div>
        </div>
    </div>
</div>
@endsection
