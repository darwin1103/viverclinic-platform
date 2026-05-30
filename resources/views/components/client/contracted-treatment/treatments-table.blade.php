@props(['contractedTreatments'])

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-white">Paquete Contratado</th>
                        <th class="text-white">Estado</th>
                        <th class="text-center text-white">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contractedTreatments as $contract)
                        @php
                            // The 'contracted_packages' attribute is already cast to an array by the Eloquent model.
                            $packages = $contract->contracted_packages;

                            // Check if $packages is a non-empty array before accessing its first element.
                            $firstPackageName = (is_array($packages) && !empty($packages))
                                                ? ($packages[0]['name'] ?? 'Nombre no encontrado')
                                                : 'No especificado';
                        @endphp
                        <tr style="cursor: pointer;" onclick="window.location.href='{{ route('client.schedule-appointment.index', ['contracted_treatment' => $contract->id]) }}'">
                            <td>
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <span class="fw-semibold">{{ $firstPackageName }}</span>
                                    @if(!$contract->isPaymentUpToDate())
                                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill" style="font-size: 0.75rem;">
                                            <i class="bi bi-info-circle-fill me-1"></i>{{ $contract->payment_type === 'abono' ? 'Abono incompleto' : 'Pago pendiente' }}
                                        </span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $contract->treatment->name ?? 'N/A' }}</small>
                            </td>
                            <td>
                                {{-- Ejemplo de badges para el estado --}}
                                @if($contract->status == 'Paid')
                                    <span class="badge bg-success">Pagado</span>
                                @elseif($contract->status == 'Pending')
                                    <span class="badge bg-info text-dark">Pendiente</span>
                                @else
                                    <span class="badge bg-secondary">{{ $contract->status }}</span>
                                @endif
                            </td>
                            <td class="text-center" onclick="event.stopPropagation();">
                                <a href="{{ route('client.schedule-appointment.index', ['contracted_treatment' => $contract->id]) }}" class="btn btn-sm btn-outline-success" title="Gestionar citas">
                                    <i class="bi bi-calendar-check"></i>
                                </a>
                                <a href="{{ route('client.contracted-treatment.show', $contract->id) }}" class="btn btn-sm btn-outline-primary" title="Ver Detalles">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4">
                                No se encontraron tratamientos contratados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Links --}}
        @if ($contractedTreatments->hasPages())
            <div class="mt-3">
                {{ $contractedTreatments->links() }}
            </div>
        @endif
    </div>
</div>

