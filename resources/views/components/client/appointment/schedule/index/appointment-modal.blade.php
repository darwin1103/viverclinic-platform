<div class="modal fade" id="modalAgendar" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-calendar-plus me-2"></i>
                    Selecciona fecha y hora - Sesión #<span id="modalSessionNumber">1</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form method="POST" action="{{ route('client.schedule-appointment.store') }}" id="appointmentForm">
                @csrf
                @method('PUT')

                <input type="hidden" name="session_number" id="sessionNumberInput">
                <input type="hidden" name="appointment_date" id="appointmentDateInput">
                <input type="hidden" name="appointment_time" id="appointmentTimeInput">
                <input type="hidden" name="branch_id" id="branchIdInput">

                <div class="modal-body">
                    <div class="row g-4">
                        <!-- Calendar Section -->
                        <div class="col-12 col-lg-7">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="calendar-header mb-3">
                                        <button type="button" class="btn btn-outline-light btn-sm" id="btnPrevMonth">
                                            <i class="bi bi-chevron-left"></i>
                                        </button>
                                        <div class="text-center flex-fill">
                                            <div class="fw-semibold" id="lblMonthYear">Mes Año</div>
                                            <div class="text-secondary small">Selecciona un día disponible</div>
                                        </div>
                                        <button type="button" class="btn btn-outline-light btn-sm" id="btnNextMonth">
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

                                    <div id="calendarDays" class="calendar-grid"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Time Slots Section -->
                        <div class="col-12 col-lg-5">
                            <div class="card h-100">
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-2">
                                        <div class="fw-semibold">Horarios disponibles:</div>
                                        <div id="lblSelectedDate" class="text-info small">Selecciona un día</div>
                                    </div>

                                    <div id="slotsContainer" class="vstack gap-2 flex-grow-1"
                                         style="overflow:auto; max-height:340px;">
                                        <div class="text-secondary text-center py-4">
                                            Selecciona un día para ver horarios disponibles
                                        </div>
                                    </div>

                                    <div class="mt-3 d-flex gap-2">
                                        <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="modal">
                                            Cancelar
                                        </button>
                                        <button type="submit" id="btnConfirm" class="btn btn-primary flex-fill" disabled>
                                            Confirmar cita
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
