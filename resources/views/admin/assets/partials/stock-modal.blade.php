<div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modificar Stock: <span id="modal-asset-name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="stock-form">
                <div class="modal-body">
                    <input type="hidden" id="modal-asset-id" name="asset_id">

                    <div class="mb-3 text-center">
                        <h6>Stock Actual: <span class="badge bg-secondary" id="modal-current-stock">0</span></h6>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Operación</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="operationSwitch" checked>
                            <label class="form-check-label" for="operationSwitch" id="operationLabel">Agregar al inventario</label>
                        </div>
                        <input type="hidden" name="operation" id="operationValue" value="add">
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Cantidad" min="1" required>
                        <label for="quantity">Cantidad</label>
                    </div>

                    <div class="form-floating">
                        <textarea class="form-control" placeholder="Motivo del ajuste" id="stockNote" name="note" style="height: 100px" required></textarea>
                        <label for="stockNote">Nota del movimiento</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
