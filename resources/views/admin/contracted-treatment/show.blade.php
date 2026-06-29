@extends('layouts.admin') {{-- Asumiendo tu layout de admin --}}

@section('content')
<div class="container-fluid">
    <div class="row d-flex justify-content-center">
        <div class="col-12">

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Detalles del Tratamiento Contratado</h4>
                    <div class="d-flex gap-2">
                        @hasanyrole('SUPER_ADMIN|OWNER|ADMIN')
                            @if($contractedTreatment->canBeUpgraded())
                                <a href="{{ route('admin.contracted-treatment.upgrade', $contractedTreatment->id) }}" class="btn btn-success">
                                    <i class="bi bi-arrow-up-circle me-1"></i>
                                    Agrandar Paquete
                                </a>
                            @endif
                            <a href="{{ route('admin.contracted-treatment.edit', $contractedTreatment->id) }}" class="btn btn-warning">
                                <i class="bi bi-pencil-square me-1"></i>
                                Editar Tratamiento
                            </a>
                        @endhasanyrole
                        <a href="{{ route('admin.schedule-appointment.index', $contractedTreatment->id) }}" class="btn btn-info">
                            <i class="bi bi-calendar-check me-1"></i>
                            Ver Agenda
                        </a>
                        <a href="{{ route('admin.contracted-treatment.index') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-left-circle me-1"></i>
                            Volver al listado
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">

                    {{-- SECCIÓN PRINCIPAL CON LA INFORMACIÓN MÁS IMPORTANTE --}}
                    <div class="p-3 mb-4 rounded bg-primary-subtle">
                        <div class="row align-items-center gy-3">
                            <div class="col-12 col-md-6">
                                <h5 class="text-white-50 mb-1">Cliente</h5>
                                <p class="h4 fw-light mb-0 text-white">{{ $contractedTreatment->user->name ?? 'N/A' }}</p>
                                <small class="text-white-50">{{ $contractedTreatment->user->email ?? '' }}</small>
                            </div>
                            <div class="col-12 col-md-3 text-md-center">
                                <h5 class="text-white-50 mb-1">Estado</h5>
                                @if($contractedTreatment->status == 'Paid')
                                    <span class="badge fs-6 bg-success-subtle border border-success-subtle text-success-emphasis rounded-pill">Pagado</span>
                                @elseif($contractedTreatment->status == 'Pending')
                                    <span class="badge fs-6 bg-warning-subtle border border-warning-subtle text-warning-emphasis rounded-pill">Pendiente</span>
                                @else
                                    <span class="badge fs-6 bg-secondary-subtle border border-secondary-subtle text-secondary-emphasis rounded-pill">{{ $contractedTreatment->status }}</span>
                                @endif
                            </div>
                            <div class="col-12 col-md-3 text-md-end">
                                <h5 class="text-white-50 mb-1">Total</h5>
                                <p class="h4 fw-bold text-white mb-0">${{ number_format($contractedTreatment->total_price, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    @if($contractedTreatment->packageUpgrade)
                        @php
                            $upgrade = $contractedTreatment->packageUpgrade;
                        @endphp
                        <div class="alert alert-success border-success bg-success-subtle text-success-emphasis p-3 mb-4 rounded">
                            <h5 class="alert-heading fw-bold mb-2">
                                <i class="bi bi-check-circle-fill me-2"></i>Paquete Agrandado Exitosamente
                            </h5>
                            <div class="row text-start fs-6">
                                <div class="col-md-6 mb-2 mb-md-0 border-end">
                                    <strong>Paquete Anterior:</strong> {{ $upgrade->old_package_data['name'] ?? 'N/A' }} (${{ number_format($upgrade->old_package_data['price_at_purchase'] ?? 0, 2) }})<br>
                                    <strong>Nuevo Paquete:</strong> {{ $upgrade->new_package_data['name'] ?? 'N/A' }} (${{ number_format($upgrade->new_package_data['price'] ?? 0, 2) }})<br>
                                    <strong>Diferencia Pagada:</strong> ${{ number_format($upgrade->price_difference, 2) }} COP
                                </div>
                                <div class="col-md-6">
                                    <strong>Empleada que vendió:</strong> {{ $upgrade->staff->name ?? 'N/A' }}
                                    @hasanyrole('SUPER_ADMIN|OWNER')
                                        <button type="button" class="btn btn-sm btn-link text-success p-0 ms-1" data-bs-toggle="modal" data-bs-target="#changeStaffUpgradeModal" title="Cambiar empleada">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    @endhasanyrole
                                    <br>
                                    <strong>Método de Pago:</strong> {{ $upgrade->payment_method === 'CASH' ? 'Efectivo' : 'Transferencia' }} 
                                    <span class="badge {{ $upgrade->payment_status === 'APPROVED' ? 'bg-success' : 'bg-warning text-dark' }} ms-1">
                                        {{ $upgrade->payment_status === 'APPROVED' ? 'Aprobado' : 'Pendiente Verificación' }}
                                    </span><br>
                                    <strong>Procesado por:</strong> {{ $upgrade->processedBy->name ?? 'N/A' }} el {{ $upgrade->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($contractedTreatment->repurchaseSale)
                        @php
                            $repurchase = $contractedTreatment->repurchaseSale;
                        @endphp
                        <div class="alert alert-info border-info bg-info-subtle text-info-emphasis p-3 mb-4 rounded">
                            <h5 class="alert-heading fw-bold mb-2">
                                <i class="bi bi-arrow-repeat me-2"></i>Paquete de Recompra
                            </h5>
                            <div class="row text-start fs-6">
                                <div class="col-md-6 mb-2 mb-md-0 border-end border-info">
                                    <strong>Empleada que vendió:</strong> {{ $repurchase->staff->name ?? 'N/A' }}
                                    @hasanyrole('SUPER_ADMIN|OWNER')
                                        <button type="button" class="btn btn-sm btn-link text-info p-0 ms-1" data-bs-toggle="modal" data-bs-target="#changeStaffRepurchaseModal" title="Cambiar empleada">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    @endhasanyrole
                                    <br>
                                    <strong>Monto del primer pago:</strong> ${{ number_format($repurchase->first_payment_amount, 2) }} COP<br>
                                </div>
                                <div class="col-md-6">
                                    <strong>Fecha de registro:</strong> {{ $repurchase->created_at->format('d/m/Y H:i') }}<br>
                                    @if($repurchase->notes)
                                        <strong>Notas:</strong> {{ $repurchase->notes }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($contractedTreatment->referralSale)
                        @php
                            $referralSale = $contractedTreatment->referralSale;
                        @endphp
                        <div class="alert alert-warning border-warning bg-warning-subtle text-warning-emphasis p-3 mb-4 rounded">
                            <h5 class="alert-heading fw-bold mb-2">
                                <i class="bi bi-people-fill me-2"></i>Paquete por Referido
                            </h5>
                            <div class="row text-start fs-6">
                                <div class="col-md-6 mb-2 mb-md-0 border-end border-warning">
                                    <strong>Empleada que vendió:</strong> {{ $referralSale->staff->name ?? 'N/A' }}
                                    @hasanyrole('SUPER_ADMIN|OWNER')
                                        <button type="button" class="btn btn-sm btn-link text-warning p-0 ms-1" data-bs-toggle="modal" data-bs-target="#changeStaffReferralModal" title="Cambiar empleada">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    @endhasanyrole
                                    <br>
                                    <strong>Monto del primer pago:</strong> ${{ number_format($referralSale->first_payment_amount, 2) }} COP<br>
                                </div>
                                <div class="col-md-6">
                                    <strong>Fecha de registro:</strong> {{ $referralSale->created_at->format('d/m/Y H:i') }}<br>
                                </div>
                            </div>
                        </div>
                    @endif

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
                                <dd class="col-sm-7">{{ $contractedTreatment->is_pregnant ? 'No' : 'Sí' }}</dd>

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
                                <p class="text-white-50">No se especificaron zonas para este tratamiento.</p>
                            @endif

                        </div>
                    </div>
                    @endif












                    <hr class="my-5">

                    <div class="row g-4">

                        {{-- 1. ESTADO DE LAS CUOTAS O ABONOS --}}
                        <div class="col-12">
                            @if($contractedTreatment->payment_type === 'abono')
                                <h5 class="mb-3 fw-bold text-primary">
                                    <i class="bi bi-wallet2 me-2"></i> Estado de Abonos
                                </h5>
                                
                                <div class="card text-white mb-3" style="border: 1px solid rgba(255, 255, 255, 0.06); background: var(--vc-card, #0f2a30);">
                                    <div class="card-body">
                                        @php
                                            $totalPaid = $contractedTreatment->totalPaid();
                                            $totalPrice = $contractedTreatment->total_price;
                                            $remaining = $contractedTreatment->remainingBalance();
                                            $percent = $totalPrice > 0 ? min(100, round(($totalPaid / $totalPrice) * 100)) : 0;
                                        @endphp
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Progreso de Pago: <strong>{{ $percent }}%</strong></span>
                                            <span>${{ number_format($totalPaid, 0, ',', '.') }} / ${{ number_format($totalPrice, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="progress mb-3" style="height: 15px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="d-flex justify-content-between text-white-50 small">
                                            <span>Total Pagado: ${{ number_format($totalPaid, 0, ',', '.') }}</span>
                                            <span>Saldo Restante: <strong class="text-warning">${{ number_format($remaining, 0, ',', '.') }}</strong></span>
                                        </div>
                                    </div>
                                </div>
                            @elseif($contractedTreatment->installments->isEmpty())
                                <h5 class="mb-3 fw-bold text-primary">
                                    <i class="bi bi-list-ol me-2"></i> Plan de Cuotas
                                </h5>
                                <div class="alert alert-secondary">
                                    Este tratamiento no tiene cuotas configuradas (Pago único).
                                </div>
                            @else
                                <h5 class="mb-3 fw-bold text-primary">
                                    <i class="bi bi-list-ol me-2"></i> Plan de Cuotas
                                </h5>
                                <div class="table-responsive border rounded">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-transparent">
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
                                                        @role('SUPER_ADMIN|OWNER')
                                                            <form action="{{ route('admin.contracted-treatment.installment.toggle-status', $inst->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de marcar la cuota #{{ $inst->installment_number }} como {{ $inst->status == 'PAID' ? 'PENDIENTE' : 'PAGADA' }}?');" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent" title="Haz clic para cambiar el estado" style="vertical-align: middle;">
                                                                    @if($inst->status == 'PAID')
                                                                        <span class="badge bg-success" style="cursor: pointer;"><i class="bi bi-check-circle me-1"></i>Pagada</span>
                                                                    @else
                                                                        <span class="badge bg-secondary" style="cursor: pointer;"><i class="bi bi-hourglass me-1"></i>Pendiente</span>
                                                                    @endif
                                                                </button>
                                                            </form>
                                                        @else
                                                            @if($inst->status == 'PAID')
                                                                <span class="badge bg-success">Pagada</span>
                                                            @else
                                                                <span class="badge bg-secondary">Pendiente</span>
                                                            @endif
                                                        @endrole
                                                    </td>
                                                    <td class="small text-white-50">
                                                        {{ $inst->paid_at ? $inst->paid_at->format('d/m/Y H:i') : '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-2 text-end small text-white-50">
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
                                    <thead class="bg-transparent">
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
                                                    <span class="text-white-50">{{ $order->created_at->format('H:i') }}</span>
                                                </td>
                                                <td>
                                                    <span class="fw-bold d-block">{{ $order->payment_method }}</span>
                                                    <small class="text-white-50 text-truncate d-block" style="max-width: 150px;" title="{{ $order->payment_description }}">
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
                                                        <span class="badge bg-warning text-white"><i class="bi bi-hourglass-split"></i> Por Verificar</span>
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
                                                        <span class="text-white-50 small">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-white-50 py-3">No hay transacciones registradas.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    {{-- 3. NOTAS INTERNAS --}}
                    <hr class="my-5">
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3 fw-bold text-primary">
                                <i class="bi bi-journal-text me-2"></i> Notas Internas
                            </h5>

                            {{-- Formulario para Nueva Nota --}}
                            <form action="{{ route('admin.contracted-treatment.notes.store', $contractedTreatment->id) }}" method="POST" class="mb-4">
                                @csrf
                                <div class="input-group">
                                    <textarea name="content" class="form-control" rows="2" placeholder="Escribe una nota interna sobre este tratamiento..." required></textarea>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="bi bi-plus-circle me-1"></i> Agregar Nota
                                    </button>
                                </div>
                            </form>

                            {{-- Listado de Notas --}}
                            <div class="list-group">
                                @forelse($contractedTreatment->notes as $note)
                                    <div class="list-group-item bg-transparent flex-column align-items-start border-start border-4 @if($note->user->hasRole(['SUPER_ADMIN', 'OWNER'])) border-primary @else border-info @endif">
                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                            <h6 class="mb-1 fw-bold text-white">
                                                <i class="bi bi-person-circle me-1"></i> {{ $note->user->name }}
                                                @if($note->user->hasRole(['SUPER_ADMIN', 'OWNER']))
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle small ms-1">Owner</span>
                                                @endif
                                            </h6>
                                            <div class="d-flex align-items-center">
                                                <small class="text-white-50 me-3">
                                                    <i class="bi bi-calendar3 me-1"></i> {{ $note->created_at->format('d/m/Y H:i') }}
                                                </small>

                                                @role('SUPER_ADMIN|OWNER')
                                                    <div class="btn-group btn-group-sm">
                                                        {{-- Botón Editar --}}
                                                        <button type="button" class="btn btn-outline-secondary border-0" data-bs-toggle="modal" data-bs-target="#editNoteModal-{{ $note->id }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        {{-- Botón Eliminar --}}
                                                        <form action="{{ route('admin.contracted-treatment.notes.destroy', $note->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta nota?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger border-0">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>

                                                    {{-- Modal Editar Nota --}}
                                                    <div class="modal fade" id="editNoteModal-{{ $note->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form action="{{ route('admin.contracted-treatment.notes.update', $note->id) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title text-white">Editar Nota Interna</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body text-start">
                                                                        <textarea name="content" class="form-control" rows="4" required>{{ $note->content }}</textarea>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                        <button type="submit" class="btn btn-primary">Actualizar Nota</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endrole
                                            </div>
                                        </div>
                                        <p class="mb-1 text-white mt-2" style="white-space: pre-wrap;">{{ $note->content }}</p>
                                    </div>
                                @empty
                                    <div class="text-center py-4 bg-transparent rounded border border-secondary border-opacity-25">
                                        <i class="bi bi-journal-x fs-1 text-white-50 mb-2"></i>
                                        <p class="text-white-50 mb-0">No hay notas registradas para este tratamiento.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>












                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@hasanyrole('SUPER_ADMIN|OWNER')
@if($contractedTreatment->packageUpgrade)
<!-- Modal Cambiar Empleada Agrandamiento -->
<div class="modal fade" id="changeStaffUpgradeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cambiar Empleada (Agrandamiento)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.contracted-treatment.change-staff', $contractedTreatment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="sale_type" value="upgrade">
                <div class="modal-body">
                    <p class="small text-muted mb-3">Este cambio generará una nota automática en el historial del paquete.</p>
                    <div class="mb-3">
                        <label class="form-label">Nueva Empleada</label>
                        <select name="staff_user_id" class="form-select" required>
                            <option value="">Selecciona una empleada...</option>
                            @foreach($staffUsers as $staff)
                                <option value="{{ $staff->id }}" {{ ($contractedTreatment->packageUpgrade->staff_user_id == $staff->id) ? 'selected' : '' }}>
                                    {{ $staff->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambio</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($contractedTreatment->repurchaseSale)
<!-- Modal Cambiar Empleada Recompra -->
<div class="modal fade" id="changeStaffRepurchaseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cambiar Empleada (Recompra)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.contracted-treatment.change-staff', $contractedTreatment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="sale_type" value="repurchase">
                <div class="modal-body">
                    <p class="small text-muted mb-3">Este cambio generará una nota automática en el historial del paquete.</p>
                    <div class="mb-3">
                        <label class="form-label">Nueva Empleada</label>
                        <select name="staff_user_id" class="form-select" required>
                            <option value="">Selecciona una empleada...</option>
                            @foreach($staffUsers as $staff)
                                <option value="{{ $staff->id }}" {{ ($contractedTreatment->repurchaseSale->staff_user_id == $staff->id) ? 'selected' : '' }}>
                                    {{ $staff->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambio</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@if($contractedTreatment->referralSale)
<!-- Modal Cambiar Empleada Referido -->
<div class="modal fade" id="changeStaffReferralModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cambiar Empleada (Referido)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.contracted-treatment.change-staff', $contractedTreatment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="sale_type" value="referral">
                <div class="modal-body">
                    <p class="small text-muted mb-3">Este cambio generará una nota automática en el historial del paquete.</p>
                    <div class="mb-3">
                        <label class="form-label">Nueva Empleada</label>
                        <select name="staff_user_id" class="form-select" required>
                            <option value="">Selecciona una empleada...</option>
                            @foreach($staffUsers as $staff)
                                <option value="{{ $staff->id }}" {{ ($contractedTreatment->referralSale->staff_user_id == $staff->id) ? 'selected' : '' }}>
                                    {{ $staff->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambio</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endhasanyrole
