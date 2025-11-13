@props(['sessions', 'totalSessions', 'paymentIsUpToDate', 'branchId', 'contractedTreatmentId'])

@php
    // Find if there's already a future appointment scheduled
    $futureAppointment = $sessions->first(function ($session) {
        return is_null($session['attended']) &&
               isset($session['date']) &&
               Illuminate\Support\Carbon::parse($session['date'])->isFuture();
    });

    // Determine the next session in sequence
    $lastCompletedSessionNumber = $sessions->whereNotNull('attended')->max('session_number') ?? 0;
    $nextSessionInSequence = $lastCompletedSessionNumber + 1;
@endphp

<div class="table-responsive">
    <table class="table align-middle" id="sessionsTable">
        <thead>
            <tr>
                <th>Sesión</th>
                <th class="text-center">Asistencia / Estado</th>
                <th class="text-center d-none d-md-table-cell">Fecha</th>
                <th class="text-center d-none d-lg-table-cell">Hora</th>
                <th class="text-end">Acción</th>
            </tr>
        </thead>
        <tbody id="sessionTableBody">
            @for ($i = 1; $i <= $totalSessions; $i++)
                @php
                    $session = $sessions->firstWhere('session_number', $i);
                    $isPast = isset($session) && $session['attended'] !== null;
                    $isNextSession = isset($session) && $session['date'] !== null && $session['attended'] === null;
                    $canSchedule = $i === $nextSessionInSequence && !$futureAppointment && $paymentIsUpToDate;
                    $isDisabled = !$isPast && !$isNextSession && !$canSchedule;
                    $canManageOptions = $isNextSession && Illuminate\Support\Carbon::parse($session['schedule'])->gt(Illuminate\Support\Carbon::now()->addHours(24));
                    $isConfirmed = isset($session) && $session['status'] === 'Confirmada';
                @endphp
                <tr data-session="{{ $i }}"
                    data-status="{{ $isPast ? ($session['attended'] ? 'ok' : 'bad') : ($isNextSession ? 'scheduled' : 'pending') }}"
                    data-date="{{ $session['date'] ?? '' }}"
                    data-time="{{ $session['time'] ?? '' }}"
                    data-review-score="{{ $session['review_score'] ?? '' }}"
                    data-can-schedule="{{ $canSchedule ? 'true' : 'false' }}"
                    data-is-disabled="{{ $isDisabled ? 'true' : 'false' }}">

                    <td class="fw-semibold">{{ $i }}</td>

                    <td class="text-center">
                        @if ($isPast && $session['attended'])
                            <span class="badge-assist text-bg-success">
                                <i class="bi bi-check-lg"></i>
                                <span class="d-none d-sm-inline">Asistida</span>
                            </span>
                        @elseif ($isPast && !$session['attended'])
                            <span class="badge-assist text-bg-danger">
                                <i class="bi bi-x-lg"></i>
                                <span class="d-none d-sm-inline">No asistida</span>
                            </span>
                        @elseif ($isNextSession)
                            <span class="badge-assist text-bg-info">
                                <i class="bi bi-clock"></i>
                                <span class="d-none d-sm-inline">Agendada</span>
                            </span>
                        @elseif ($canSchedule)
                            <span class="badge-assist badge-available">
                                <i class="bi bi-plus-circle-dotted"></i>
                                <span class="d-none d-sm-inline">Disponible</span>
                            </span>
                        @elseif ($isDisabled)
                            <span class="badge-assist badge-upcoming">
                                <i class="bi bi-arrow-90deg-right"></i>
                                <span class="d-none d-sm-inline">Próxima</span>
                            </span>
                        @else
                            <span class="badge-assist text-bg-secondary">
                                <i class="bi bi-question-square"></i>
                                <span class="d-none d-sm-inline">Pendiente</span>
                            </span>
                        @endif
                    </td>

                    <td class="text-center d-none d-md-table-cell">
                        @if (isset($session) && $session['date'])
                            <span class="session-date">
                                {{ Illuminate\Support\Carbon::parse($session['date'])->isoFormat('dddd, D \d\e MMMM, YYYY') }}
                            </span>
                        @endif
                    </td>

                    <td class="text-center d-none d-lg-table-cell">
                        @if (isset($session) && $session['time'])
                            <span class="session-time">
                            {{ Illuminate\Support\Carbon::parse($session['time'])->isoFormat('hh:mm a') }}
                        </span>
                        @endif
                    </td>

                    <td class="text-end">
                        @if ($isPast)
                            @if (isset($session['review_score']) && $session['review_score'] > 0)
                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                    <i class="bi bi-star-fill me-1"></i>
                                    <span class="d-none d-sm-inline">Calificado</span>
                                </button>
                            @else
                                <button class="btn btn-sm btn-outline-info btn-rate" data-session="{{ $i }}" data-appointment-id="{{ $session['id'] }}">
                                    <i class="bi bi-emoji-smile me-1"></i>
                                    <span class="d-none d-sm-inline">Calificar</span>
                                </button>
                            @endif
                        @elseif ($canManageOptions && !$isConfirmed)
                            <div class="btn-group btn-group-sm" role="group">
                                <button
                                class="btn btn-success btn-confirm"
                                data-session="{{ $i }}"
                                data-appointment-id="{{ $session['id'] }}"
                                data-confirm-url-template="{{ route('client.schedule-appointment.confirm', ['appointment' => $session['id']]) }}"
                                title="Confirmar"
                                >
                                    <i class="bi bi-check2"></i>
                                    <span class="d-none d-lg-inline ms-1">Confirmar</span>
                                </button>
                                <button class="btn btn-warning btn-resched" data-branch-id="{{ $branchId }}" data-session="{{ $i }}" data-appointment-id="{{ $session['id'] }}" title="Reagendar">
                                    <i class="bi bi-arrow-repeat"></i>
                                    <span class="d-none d-lg-inline ms-1">Reagendar</span>
                                </button>
                                <button
                                    class="btn btn-danger btn-cancel"
                                    data-session="{{ $i }}"
                                    data-appointment-id="{{ $session['id'] }}"
                                    data-cancel-url-template="{{ route('client.schedule-appointment.cancel', ['appointment' => $session['id']]) }}"
                                    title="Cancelar"
                                >
                                    <i class="bi bi-x-lg"></i>
                                    <span class="d-none d-lg-inline ms-1">Cancelar</span>
                                </button>
                            </div>
                        @elseif ($canSchedule && $paymentIsUpToDate)
                            <button
                                class="btn btn-sm btn-primary btn-open-scheduler"
                                data-branch-id="{{ $branchId }}"
                                data-contracted-treatment-id="{{ $contractedTreatmentId }}"
                                data-session="{{ $i }}"
                            >
                                <i class="bi bi-calendar-plus me-1"></i>
                                <span class="d-none d-sm-inline">Agendar Cita</span>
                            </button>
                        @elseif ($canSchedule)
                            <button class="btn btn-sm btn-primary" disabled>
                                <i class="bi bi-calendar-plus me-1"></i>
                                <span class="d-none d-sm-inline">Agendar Cita</span>
                            </button>
                        @elseif ($isDisabled)
                            <button class="btn btn-sm btn-primary" disabled>
                                <i class="bi bi-calendar-plus me-1"></i>
                                <span class="d-none d-sm-inline">Agendar Cita</span>
                            </button>
                        @endif
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>
