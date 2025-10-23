@props(['sessions', 'totalSessions', 'paymentIsUpToDate'])

@php
    // LÓGICA CLAVE:
    // 1. Encontrar si ya existe una cita futura agendada en la colección.
    //    Esto es crucial para saber si debemos mostrar un botón de "Agendar" o de "Reprogramar".
    $futureAppointment = $sessions->where('attended', null)->firstWhere('date', '!=', null);

    // 2. Determinar cuál es la siguiente sesión que TOCA.
    //    Es la que sigue inmediatamente a la última sesión que ya fue marcada como 'asistida' o 'perdida'.
    //    Si ninguna se ha completado, empezamos por la 1.
    $lastCompletedSessionNumber = $sessions->whereNotNull('attended')->max('session_number') ?? 0;
    $nextSessionInSequence = $lastCompletedSessionNumber + 1;
@endphp

<div class="table-responsive">
    <table class="table table-hover align-middle text-center">
        <thead class="table-light">
            <tr>
                <th scope="col" class="text-white">Sesión</th>
                <th scope="col" class="text-white">Asistencia</th>
                <th scope="col" class="text-white">Fecha de Cita</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 1; $i <= $totalSessions; $i++)
                @php
                    // Obtenemos los datos de la sesión para la fila actual ($i).
                    $session = $sessions->firstWhere('session_number', $i);
                @endphp
                <tr>
                    <td class="fw-bold">{{ $i }}</td>
                    <td>
                        {{-- Iconos de asistencia (esta parte no cambia) --}}
                        @if (isset($session) && $session['attended'] === true)
                            <i class="bi bi-check-circle-fill fs-4 text-success" title="Asistió"></i>
                        @elseif (isset($session) && $session['attended'] === false)
                            <i class="bi bi-x-circle-fill fs-4 text-danger" title="No Asistió"></i>
                        @endif
                    </td>
                    <td>
                        @if (isset($session) && $session['attended'] !== null)
                            {{-- CASO 1: Sesión pasada (asistida o perdida). Mostramos su fecha. --}}
                            {{ \Carbon\Carbon::parse($session['date'])->format('d-m-Y') }}

                        @elseif (isset($session) && $session['date'] !== null)
                            {{-- CASO 2: Es una cita futura YA AGENDADA. Mostramos fecha y botón "Reprogramar". --}}
                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                                <span class="fw-bold align-self-center me-2 d-none d-sm-block">{{ \Carbon\Carbon::parse($session['date'])->format('d-m-Y') }}</span>
                                @if($paymentIsUpToDate)
                                <button class="btn btn-outline-primary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#scheduleAppointmentModal"
                                    data-bs-session-number="{{ $i }}"
                                    data-bs-date="{{ $session['date'] }}">
                                    <i class="bi bi-pencil-square me-1"></i> Reprogramar
                                </button>
                                @endif
                            </div>

                        @elseif ($i === $nextSessionInSequence && $futureAppointment)
                            {{-- CASO 3: ESTA es la siguiente sesión que TOCA agendar. --}}
                            {{-- Solo se muestra si NO hay otra cita futura ya agendada. --}}
                            @if($paymentIsUpToDate)
                                <button class="btn btn-primary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#scheduleAppointmentModal"
                                    data-bs-session-number="{{ $i }}">
                                    <i class="bi bi-calendar-plus me-1"></i> Agendar Cita
                                </button>
                            @endif
                        @endif
                        {{-- Para el resto de casos (sesiones futuras que aún no tocan), la celda queda vacía. --}}
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>
