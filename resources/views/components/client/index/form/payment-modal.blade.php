<div class="modal fade" id="pagoModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="pagoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="pagoModalLabel">Formulario de Pago</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-pago">
                    <div class="mb-3">
                        <label for="name-tarjeta" class="form-label">Nombre en la Tarjeta</label>
                        <input type="text" class="form-control" id="name-tarjeta" required>
                    </div>
                    <div class="mb-3">
                        <label for="numero-tarjeta" class="form-label">Número de Tarjeta</label>
                        <input type="text" class="form-control" id="numero-tarjeta" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fecha-exp" class="form-label">Fecha de Expiración</label>
                            <input type="text" class="form-control" id="fecha-exp" placeholder="MM/AA" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cvv" class="form-label">CVV</label>
                            <input type="text" class="form-control" id="cvv" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <!-- *** -->
                <!-- <button type="button" class="btn btn-primary" id="confirmar-pago">Confirmar Pago</button> -->
                <a href="{{route('schedule-appointment.index')}}" class="btn btn-primary" id="confirmar-pago">Confirmar Pago</a>
            </div>
        </div>
    </div>
</div>
