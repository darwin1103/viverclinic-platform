@props(['appointments', 'treatments'])

<div class="table-responsive">
    <table class="table table-striped table-hover align-middle" id="appointmentsTable">
        <thead class="table-light">
            <tr>
                <th scope="col" class="text-white">Nombre del Paciente</th>
                <th scope="col" class="text-white">Fecha</th>
                <th scope="col" class="text-white">Hora</th>
                <th scope="col" class="text-white">Tratamiento</th>
                <th scope="col" class="text-white text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($appointments as $appointment)
                @php
                $shots = ($appointment->contractedTreatment->treatment->needs_report_shots) ? intval($appointment->uses_of_hair_removal_shots) : '';
                @endphp
                <tr>
                    <td data-label="Nombre del Paciente">{{ $appointment->contractedTreatment->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($appointment->schedule)->isoFormat('dddd, D \d\e MMMM, YYYY') }}</td>
                    <td>{{ \Carbon\Carbon::parse($appointment->schedule)->isoFormat('hh:mm a') }}</td>
                    <td data-label="Tratamiento">{{ $appointment->contractedTreatment->treatment->name }}</td>
                    <td data-label="Acciones" class="text-center">
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
                            data-shots="{{ $shots }}"
                            >
                            <i class="bi bi-eye"></i> Ver
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No hay citas pendientes.</td>
                </tr>
            @endforelse
            {{-- Fila para mostrar cuando no hay resultados de búsqueda --}}
            <tr id="noResultsMessage" style="display: none;">
                <td colspan="5" class="text-center">No se encontraron resultados.</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- Pagination Links --}}
@if ($appointments->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $appointments->links() }}
    </div>
@endif

{{-- Action Modal Component --}}
<x-staff.appointments.modal />

@push('scripts')
    {{-- Link to the specific JavaScript file for this view --}}
    <script src="{{ asset('js/staff/appointments.js') }}"></script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/general/responsive-table.css') }}">
@endpush
