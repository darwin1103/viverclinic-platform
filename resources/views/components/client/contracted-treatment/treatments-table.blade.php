@props(['contractedTreatments'])

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-white">Cliente</th>
                        <th class="text-white">Tratamiento</th>
                        <th class="text-white">Paquete Contratado</th>
                        <th class="text-white">Fecha de Contratación</th>
                        <th class="text-end text-white">Total</th>
                        <th class="text-white">Pago</th>
                        <th class="text-center text-white">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contractedTreatments as $contract)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $contract->user->name }}</div>
                                <small class="text-muted">{{ $contract->user->email }}</small>
                            </td>
                            <td>{{ $contract->treatment->name ?? 'N/A' }}</td>
                            <td>
                                @php
                                    // The 'contracted_packages' attribute is already cast to an array by the Eloquent model.
                                    $packages = $contract->contracted_packages;

                                    // Check if $packages is a non-empty array before accessing its first element.
                                    $firstPackageName = (is_array($packages) && !empty($packages))
                                                        ? ($packages[0]['name'] ?? 'Nombre no encontrado')
                                                        : 'No especificado';
                                @endphp
                                {{ $firstPackageName }}
                            </td>
                            <td>
                                @php
                                    \Carbon\Carbon::setLocale('es');
                                    $formattedDate = \Carbon\Carbon::parse($contract->created_at)
                                        ->isoFormat('dddd, D \d\e MMMM, YYYY');
                                @endphp
                                {{ $formattedDate }}
                            </td>
                            <td class="text-end fw-bold">${{ number_format($contract->total_price, 2) }}</td>
                            <td>
                                {{-- Ejemplo de badges para el estado --}}
                                @if($contract->status == 'Paid')
                                    <span class="badge bg-success">Pagado</span>
                                @elseif($contract->status == 'Pending')
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                @else
                                    <span class="badge bg-secondary">{{ $contract->status }}</span>
                                @endif
                            </td>
                            <td class="text-center">
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
                            <td colspan="8" class="text-center py-4">
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
