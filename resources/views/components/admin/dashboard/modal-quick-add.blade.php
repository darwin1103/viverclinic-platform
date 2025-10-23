  <div class="modal fade" id="modalQuickAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Crear rápido</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="row g-2">
            <div class="col-12 col-sm-6">
              <a href="#" class="btn btn-outline-light w-100">
                <i class="bi bi-calendar-plus me-2"></i>
                Nueva cita
              </a>
            </div>
            <div class="col-12 col-sm-6">
              <a href="{{ route('users.create') }}" class="btn btn-outline-light w-100">
                <i class="bi bi-person-plus me-2"></i>
                Nuevo paciente
              </a>
            </div>
            <div class="col-12 col-sm-6">
              <a href="#" class="btn btn-outline-light w-100">
                <i class="bi bi-cash-coin me-2"></i>
                Registrar pago
              </a>
            </div>
            <div class="col-12 col-sm-6">
              <a href="#" class="btn btn-outline-light w-100">
                <i class="bi bi-megaphone me-2"></i>Crear
                   promoción
                 </a>
               </div>
          </div>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button></div>
      </div>
    </div>
  </div>
