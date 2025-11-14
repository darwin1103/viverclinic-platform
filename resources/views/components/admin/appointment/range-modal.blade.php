<div class="modal fade" id="modalRange" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-calendar3 me-2"></i>
                    Seleccionar rango (5 días)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <div class="row g-4">
                    <!-- Calendar -->
                    <div class="col-12 col-lg-7">
                        <div class="card">
                            <div class="card-body">
                                <div class="calendar-header mb-3">
                                    <button type="button" class="btn btn-outline-light btn-sm" id="rangePrevMonth">
                                        <i class="bi bi-chevron-left"></i>
                                    </button>
                                    <div class="text-center flex-fill">
                                        <div class="fw-semibold" id="rangeMonthLabel">Mes Año</div>
                                        <div class="text-secondary small">
                                            Elige el <strong>día inicial</strong> del rango
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-light btn-sm" id="rangeNextMonth">
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

                                <div id="rangeDays" class="calendar-grid"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="col-12 col-lg-5">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="fw-semibold mb-2">Rango seleccionado</div>
                                <div id="rangePreview" class="mb-3 text-info">—</div>

                                <div class="alert alert-info small mb-3">
                                    <i class="bi bi-info-circle me-1"></i>
                                    El rango mostrará 5 días consecutivos a partir del día seleccionado
                                </div>

                                <div class="mt-auto d-flex gap-2">
                                    <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="modal">
                                        Cancelar
                                    </button>
                                    <button type="button" id="rangeApply" class="btn btn-primary flex-fill" disabled>
                                        Aplicar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
