<div class="modal fade" id="removeConfirmationModal" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="removeConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger border-2">
            <div class="modal-header bg-danger text-white">
                <h1 class="modal-title fs-5 fw-bold" id="removeConfirmationModalLabel">
                    <i class="bi bi-exclamation-triangle-fill"></i> ¡ACCIÓN CRÍTICA! Eliminar Paciente
                </h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <p class="fs-6 fw-bold text-danger mb-3">
                            ¿Está absolutamente seguro de que desea eliminar permanentemente este paciente?
                        </p>
                        <div class="alert alert-danger bg-danger text-white border-0 mb-3" role="alert">
                            Esta acción es irreversible y ejecutará un <strong>borrado en cascada</strong> completo de todos los datos asociados al usuario:
                        </div>
                        
                        <div class="card bg-light border-danger-subtle mb-3">
                            <div class="card-body py-2 text-danger">
                                <ul class="mb-0 fw-semibold">
                                    <li>Historial clínico completo</li>
                                    <li>Citas programadas e históricas</li>
                                    <li>Tratamientos contratados</li>
                                    <li>Pagos y transacciones</li>
                                </ul>
                            </div>
                        </div>

                        <div class="alert alert-warning border-warning d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                            <div>
                                <strong>Recomendación:</strong> Si solo desea impedir que el paciente acceda a la plataforma sin perder su información histórica, utilice la opción de <strong>Desactivar Paciente</strong> en la tabla.
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('Cancel') }}
                        </button>
                        <a class="btn btn-danger fw-bold" id="deleteElementBtn" role="button"
                            onclick="event.preventDefault(); document.getElementById('delete').submit();">
                            Sí, eliminar permanentemente
                        </a>
                        <form id="delete" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
