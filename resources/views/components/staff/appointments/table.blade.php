@props(['appointments'])

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th scope="col" class="text-white">Tratamiento</th>
                <th scope="col" class="text-white">Paciente</th>
                <th scope="col" class="text-white">Sesión</th>
                <th scope="col" class="text-white">Fecha</th>
                <th scope="col" class="text-white">Hora</th>
                <th scope="col" class="text-white">Estado</th>
                <th scope="col" class="text-white" class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($appointments as $group)
                @php
                $primaryApp = $group->first();
                $treatmentsList = implode(' + ', $group->map(fn($a) => $a->contractedTreatment?->treatment?->name ?? 'N/A')->toArray());
                $shots = ($primaryApp->contractedTreatment?->treatment?->needs_report_shots) ? intval($primaryApp->uses_of_hair_removal_shots) : '';
                $markAsCompletedUrl = ($primaryApp->status == 'Atendida') ? route('staff.appointment.mark-as-completed', ['appointment' => $primaryApp->id]) : '';
                
                // Resaltar la cita si está "Atendida"
                $rowClass = ($primaryApp->status == 'Atendida') ? 'table-highlight-primary' : '';

                $subAppointments = $group->map(function ($app) {
                    return [
                        'id' => $app->id,
                        'contracted_treatment_id' => $app->contracted_treatment_id,
                        'treatment' => $app->contractedTreatment?->treatment?->name ?? 'N/A',
                        'zones' => $app->contractedTreatment?->selected_zones,
                        'session_number' => $app->session_number,
                        'status' => $app->status,
                        'attended' => $app->attended,
                        'review' => $app->review,
                        'review_score' => $app->review_score,
                        'shots' => ($app->contractedTreatment?->treatment?->needs_report_shots && $app->uses_of_hair_removal_shots) ? $app->uses_of_hair_removal_shots : null,
                    ];
                })->toArray();
                @endphp
                <tr class="{{ $rowClass }}">
                    <td>{{ $treatmentsList }}</td>
                    <td>{{ $primaryApp->contractedTreatment?->user?->name ?? 'N/A' }}</td>
                    <td><span class="badge bg-primary rounded-pill">{{ $primaryApp->session_number }}</span></td>
                    <td>{{ \Carbon\Carbon::parse($primaryApp->schedule)->isoFormat('dddd, D \d\e MMMM, YYYY') }}</td>
                    <td>{{ \Carbon\Carbon::parse($primaryApp->schedule)->isoFormat('hh:mm a') }}</td>
                    <td>
                        @php
                            $badgeClass = 'bg-secondary';
                            $statusLabel = $primaryApp->status;
                            if ($primaryApp->status == 'Atendida') {
                                $badgeClass = 'bg-primary text-white';
                                $statusLabel = 'Atención';
                            } elseif ($primaryApp->status == 'Completada') {
                                $badgeClass = 'bg-success';
                            } elseif ($primaryApp->status == 'Agendada') {
                                $badgeClass = 'bg-info';
                            }
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#appointmentActionModal"
                            data-appointment-id="{{ $primaryApp->id }}"
                            data-patient-name="{{ $primaryApp->contractedTreatment?->user?->name ?? 'N/A' }}"
                            data-appointment-details="{{ json_encode([
                                'treatment' => $treatmentsList,
                                'session_number' => $primaryApp->session_number,
                                'date' => \Carbon\Carbon::parse($primaryApp->schedule)->isoFormat('dddd, D \d\e MMMM, YYYY'),
                                'time' => \Carbon\Carbon::parse($primaryApp->schedule)->isoFormat('hh:mm a'),
                                'status' => $primaryApp->status,
                                'sub_appointments' => $subAppointments
                            ]) }}"
                            data-zones='@json($primaryApp->contractedTreatment?->selected_zones ?? [])'
                            data-shots="{{$shots}}"
                            data-set-mark-as-completed-url="{{$markAsCompletedUrl}}"
                            >
                            <i class="bi bi-eye"></i> Ver
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <p class="mb-1"><i class="bi bi-calendar-x fs-2"></i></p>
                        No se encontraron citas con los filtros actuales.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination Links --}}
@if ($appointments->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $appointments->links() }}
    </div>
@endif
