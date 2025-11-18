<div class="modal fade" id="modalAppointmentDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-calendar-event me-2"></i>
                    Detalle de la cita
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body" id="modalBodyAppointment">
                <!-- Content will be loaded dynamically -->
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>

            <div class="modal-footer" id="modalFooterAppointment">
                <button type="button" id="btnMarkAttended" class="btn btn-success d-none">
                    <i class="bi bi-check2-circle me-1"></i>
                    Marcar como atendida
                </button>
                <button type="button" id="btnConfirmAppointment" class="btn btn-info d-none">
                    <i class="bi bi-check-circle me-1"></i>
                    Confirmar cita
                </button>
                <button type="button" id="btnRescheduleAppointment" class="btn btn-warning d-none">
                    <i class="bi bi-arrow-repeat me-1"></i>
                    Reagendar
                </button>
                <button type="button" id="btnCancelAppointment" class="btn btn-danger d-none">
                    <i class="bi bi-x-octagon me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reschedule Modal -->
<div class="modal fade" id="modalReschedule" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-arrow-repeat me-2"></i>
                    Reagendar cita
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="rescheduleForm">
                <input type="hidden" id="rescheduleAppointmentId">
                <input type="hidden" id="rescheduleDateInput">
                <input type="hidden" id="rescheduleTimeInput">

                <div class="modal-body">
                    <div class="row g-4">
                        <!-- Calendar Section -->
                        <div class="col-12 col-lg-7">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="calendar-header mb-3">
                                        <button type="button" class="btn btn-outline-light btn-sm" id="btnReschedulePrevMonth">
                                            <i class="bi bi-chevron-left"></i>
                                        </button>
                                        <div class="text-center flex-fill">
                                            <div class="fw-semibold" id="rescheduleMonthLabel">Mes Año</div>
                                            <div class="text-secondary small">Selecciona un día disponible</div>
                                        </div>
                                        <button type="button" class="btn btn-outline-light btn-sm" id="btnRescheduleNextMonth">
                                            <i class="bi bi-chevron-right"></i>
                                        </button>
                                    </div>

                                    <div class="calendar-grid text-center small mb-2">
                                        <div class="text-secondary">Lun</div>
                                        <div class="text-secondary">Mar</div>
                                        <div class="text-secondary">Mié</div>
                                        <div class="text-secondary">Jue</div>
                                        <div class="text-secondary">Vie</div>
                                        <div class="text-secondary">Sáb</div>
                                        <div class="text-secondary">Dom</div>
                                    </div>

                                    <div id="rescheduleDays" class="calendar-grid"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Time Slots Section -->
                        <div class="col-12 col-lg-5">
                            <div class="card h-100">
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-2">
                                        <div class="fw-semibold">Horarios disponibles:</div>
                                        <div id="rescheduleSelectedDate" class="text-info small">Selecciona un día</div>
                                    </div>

                                    <div id="rescheduleSlotsContainer" class="vstack gap-2 flex-grow-1"
                                         style="overflow:auto; max-height:340px;">
                                        <div class="text-secondary text-center py-4">
                                            Selecciona un día para ver horarios disponibles
                                        </div>
                                    </div>

                                    <div class="mt-3 d-flex gap-2">
                                        <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="modal">
                                            Cancelar
                                        </button>
                                        <button type="submit" id="btnConfirmReschedule" class="btn btn-primary flex-fill" disabled>
                                            Confirmar reagendado
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
