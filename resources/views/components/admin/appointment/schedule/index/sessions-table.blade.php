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
                <th class="text-center">Estado</th>
                <th class="text-center">Fecha</th>
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
                                <span class="ms-1">Asistida</span>
                            </span>
                        @elseif (($isPast && !$session['attended']) || (isset($session) && $session['status'] === 'No asistida'))
                            <span class="badge-assist text-bg-danger">
                                <i class="bi bi-x-lg"></i>
                                <span class="ms-1">No asistida</span>
                            </span>
                        @elseif ($isNextSession)
                            <span class="badge-assist text-bg-info">
                                <i class="bi bi-clock"></i>
                                <span class="ms-1">Agendada</span>
                            </span>
                        @elseif ($canSchedule)
                            <span class="badge-assist badge-available">
                                <i class="bi bi-plus-circle-dotted"></i>
                                <span class="ms-1">Disponible</span>
                            </span>
                        @elseif ($isDisabled)
                            <span class="badge-assist badge-upcoming">
                                <i class="bi bi-arrow-90deg-right"></i>
                                <span class="ms-1">Próxima</span>
                            </span>
                        @else
                            <span class="badge-assist text-bg-secondary">
                                <i class="bi bi-question-square"></i>
                                <span class="ms-1">Pendiente</span>
                            </span>
                        @endif
                    </td>

                    <td class="text-center">
                        @if (isset($session) && $session['date'])
                            <span class="session-date d-none d-md-inline fw-semibold">
                                {{ Illuminate\Support\Carbon::parse($session['date'])->isoFormat('dddd, D \d\e MMMM, YYYY') }}
                            </span>
                            <span class="session-date d-md-none fw-semibold">
                                {{ Illuminate\Support\Carbon::parse($session['date'])->isoFormat('D/MM/YYYY') }}
                            </span>
                            <div class="session-time text-muted small mt-1">
                                <i class="bi bi-clock me-1"></i>{{ $session['time'] }}
                            </div>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>

                    <td class="text-end">
                        @if ($isPast)
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                @if ($session['attended'])
                                    <span class="text-success" title="Completada">
                                        <i class="bi bi-check-circle-fill fs-5"></i>
                                    </span>
                                @endif

                                @if (isset($session['review_score']) && $session['review_score'] > 0)
                                    <button class="btn btn-sm btn-outline-secondary" disabled>
                                        <i class="bi bi-star-fill me-1"></i>
                                        <span class="d-none d-sm-inline">Calificado</span>
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-emoji-smile me-1"></i>
                                        <span class="d-none d-sm-inline">Calificación pendiente</span>
                                    </button>
                                @endif
                            </div>
                        @elseif ($isNextSession)
                            <button
                                type="button"
                                class="btn btn-sm btn-info text-white btn-open-appointment-actions"
                                title="Acciones"
                                data-session="{{ $i }}"
                                data-appointment-id="{{ $session['id'] }}"
                                data-date-formatted="{{ Illuminate\Support\Carbon::parse($session['date'])->isoFormat('dddd, D \d\e MMMM, YYYY') }}"
                                data-time="{{ $session['time'] }}"
                                data-status="{{ $session['status'] }}"
                                data-can-manage="{{ $canManageOptions ? 'true' : 'false' }}"
                                data-is-confirmed="{{ $isConfirmed ? 'true' : 'false' }}"
                                data-is-near-limit="{{ (isset($session['schedule']) && Illuminate\Support\Carbon::parse($session['schedule'])->lt(Illuminate\Support\Carbon::now()->addHours(48))) ? 'true' : 'false' }}"
                                data-confirm-url="{{ route('admin.schedule-appointment.confirm', ['appointment' => $session['id']]) }}"
                                data-resched-url="{{ route('admin.schedule-appointment.resched', ['appointment' => $session['id']]) }}"
                                data-cancel-url="{{ route('admin.schedule-appointment.cancel', ['appointment' => $session['id']]) }}"
                                data-branch-id="{{ $branchId }}"
                            >
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                        @elseif ($canSchedule && $paymentIsUpToDate)
                            <button
                                class="btn btn-sm btn-primary btn-open-scheduler"
                                title="Agendar"
                                data-branch-id="{{ $branchId }}"
                                data-contracted-treatment-id="{{ $contractedTreatmentId }}"
                                data-store-url-template="{{ route('admin.schedule-appointment.store') }}"
                                data-session="{{ $i }}"
                            >
                                <i class="bi bi-calendar-plus"></i>
                            </button>
                        @elseif (($isPast && !$session['attended']) || (isset($session) && $session['status'] === 'No asistida'))
                            <span></span>
                        @else
                            <button class="btn btn-sm btn-primary" title="Agendar" disabled>
                                <i class="bi bi-calendar-plus"></i>
                            </button>
                        @endif
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>

<!-- Modal Popup para Acciones de Cita Agendada -->
<div class="modal fade" id="appointmentActionsModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-gear-fill me-2 text-info"></i>
                    Acciones - Sesión #<span id="actionsModalSessionNumber"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-4">
                    <div class="text-secondary small mb-1">Detalles de la cita agendada:</div>
                    <div class="fw-bold fs-5 text-white" id="actionsModalDateTime"></div>
                    <div class="mt-2">
                        <span class="badge bg-info text-white" id="actionsModalStatus">Agendada</span>
                    </div>
                </div>

                <div class="d-grid gap-3" id="actionsContainer">
                    <button type="button" class="btn btn-success py-2 d-none" id="btnConfirmAction">
                        <i class="bi bi-check2-circle me-2"></i>Confirmar Cita
                    </button>
                    <button type="button" class="btn btn-warning py-2 d-none" id="btnReschedAction">
                        <i class="bi bi-arrow-repeat me-2"></i>Reagendar Cita
                    </button>
                    <button type="button" class="btn btn-danger py-2 d-none" id="btnCancelAction">
                        <i class="bi bi-x-circle me-2"></i>Cancelar Cita
                    </button>
                </div>

                <div id="noActionsMessage" class="alert alert-warning d-none text-start my-2">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Esta cita ya no puede ser modificada ni cancelada (límite de 24 horas superado).
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

