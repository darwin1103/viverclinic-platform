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
