<!-- Desktop: 5-day week view -->
<div class="five-wrapper d-none d-lg-block">
    <div id="fiveGrid" class="five-grid">
        <!-- Days will be rendered here by JavaScript -->
    </div>
</div>

<!-- Mobile: Single day view -->
<div class="mobile-day-view d-lg-none">
    <div class="card">
        <div class="card-header bg-transparent">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary small text-uppercase" id="mobileDayName">Lunes</div>
                    <div class="fw-semibold fs-5" id="mobileDayNumber">28</div>
                </div>
                <div class="text-end">
                    <div class="text-secondary small">Total citas</div>
                    <div class="fw-semibold" id="mobileTotalCount">0</div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="mobileAppointmentsList" class="p-3">
                <!-- Appointments will be rendered here -->
            </div>
        </div>
    </div>
</div>

<!-- Loading State -->
<div id="loadingState" class="text-center py-5 d-none">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
    </div>
    <div class="mt-3 text-secondary">Cargando citas...</div>
</div>

<!-- Empty State -->
<div id="emptyState" class="card d-none">
    <div class="card-body text-center py-5">
        <i class="bi bi-calendar-x display-1 text-secondary opacity-25"></i>
        <h5 class="mt-3 text-secondary">No hay citas programadas</h5>
        <p class="text-secondary">No se encontraron citas para los filtros seleccionados</p>
    </div>
</div>
