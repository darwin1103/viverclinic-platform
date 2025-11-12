<!-- Modal para Agendar/Reprogramar Cita -->
<div class="modal fade" id="scheduleAppointmentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="scheduleAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="scheduleAppointmentModalLabel">
                    Agendar Cita para Sesión #<span id="modalSessionNumberTitle"></span>
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- El action debe apuntar a tu ruta de actualización --}}
            <form method="POST" action="{{ route('schedule-appointment.store') }}">
                @csrf
                @method('PUT') {{-- O POST según tu definición de ruta --}}

                {{-- Hidden input to send the session number --}}
                <input type="hidden" name="session_number" id="modalSessionNumber">

                <div class="modal-body">
                    <p>Por favor, seleccione la fecha para su próxima cita.</p>
                    <div class="form-floating">
                         <input type="date" class="form-control" id="appointmentDate" name="appointment_date" placeholder="Fecha de la cita" required>
                         <label for="appointmentDate">Fecha de la cita</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" disabled>Guardar Cita</button>
                </div>
            </form>
        </div>
    </div>
</div>
