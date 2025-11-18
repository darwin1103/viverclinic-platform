<div class="modal fade" id="appointmentActionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="appointmentActionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="appointmentActionModalLabel">Detalles de la Cita</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{--
                    Aquí puedes agregar el contenido del formulario.
                    El JavaScript se encargará de cargar datos dinámicos si es necesario,
                    utilizando el ID de la cita.
                --}}
                <p>El formulario para la cita se cargará aquí...</p>
                <p><strong>ID de la Cita:</strong> <span id="modalAppointmentId"></span></p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>
