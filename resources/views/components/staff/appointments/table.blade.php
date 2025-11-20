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
                <th scope="col" class="text-white" class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($appointments as $appointment)
                @php
                $shots = ($appointment->contractedTreatment->treatment->needs_report_shots) ? intval($appointment->uses_of_hair_removal_shots) : '';
                $setAppointmentShotsUrl = ($shots == 0 ) ? (route('staff.appointment.set-shots', ['appointment' => $appointment->id])) : '';
                $markAsCompletedUrl = ($appointment->status == 'Atendida') ? route('staff.appointment.mark-as-completed', ['appointment' => $appointment->id]) : '';
                @endphp
                <tr>
                    <td>{{ $appointment->contractedTreatment->treatment->name }}</td>
                    <td>{{ $appointment->contractedTreatment->user->name }}</td>
                    <td><span class="badge bg-primary rounded-pill">{{ $appointment->session_number }}</span></td>
                    <td>{{ \Carbon\Carbon::parse($appointment->schedule)->isoFormat('dddd, D \d\e MMMM, YYYY') }}</td>
                    <td>{{ \Carbon\Carbon::parse($appointment->schedule)->isoFormat('hh:mm a') }}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#appointmentActionModal"
                            data-appointment-id="{{ $appointment->id }}"
                            data-patient-name="{{ $appointment->contractedTreatment->user->name }}"
                            data-appointment-details="{{ json_encode([
                                'treatment' => $appointment->contractedTreatment->treatment->name,
                                'session_number' => $appointment->session_number,
                                'date' => \Carbon\Carbon::parse($appointment->schedule)->isoFormat('dddd, D \d\e MMMM, YYYY'),
                                'time' => \Carbon\Carbon::parse($appointment->schedule)->isoFormat('hh:mm a'),
                                'status' => $appointment->status
                            ]) }}"
                            data-zones='@json($appointment->contractedTreatment->selected_zones)'
                            data-shots="{{$shots}}"
                            data-set-appointment-shots-url="{{$setAppointmentShotsUrl}}"
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
