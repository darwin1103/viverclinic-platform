@props(['branch'])

<div class="sticky-toolbar">
    <div class="card mb-3">
        <div class="card-body">
            <!-- Desktop Layout -->
            <div class="d-none d-lg-flex flex-row flex-wrap align-items-center gap-3">
                <!-- Navigation Buttons -->
                <div class="btn-group" role="group">
                    <button id="btnPrev5" class="btn btn-outline-light" title="Semana anterior">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button id="btnNext5" class="btn btn-outline-light" title="Semana siguiente">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                <!-- Range Display -->
                <a id="btnOpenRange" class="range-button fw-semibold text-decoration-underline" role="button">
                    Esta semana
                </a>

                <!-- Search -->
                <div class="input-group" style="max-width:280px">
                    <span class="input-group-text bg-transparent border-secondary">
                        <i class="bi bi-search"></i>
                    </span>
                    <input
                        type="text"
                        id="searchInput"
                        class="form-control border-secondary"
                        placeholder="Buscar paciente…"
                    />
                </div>

                <!-- Filters -->
                <div>
                <select id="filterStaff" class="form-control">
                    <option value="">Todos los profesionales</option>
                </select>
            </div>
                <div>
                <select id="filterTreatment" class="form-control">
                    <option value="">Todos los tratamientos</option>
                </select>
            </div>
                <div>
                <select id="filterStatus" class="form-control">
                    <option value="">Todos los estados</option>
                    <option value="Por confirmar">Pendiente</option>
                    <option value="Confirmada">Confirmada</option>
                    <option value="Atendida">Atendida</option>
                    <option value="Cancelada">Cancelada</option>
                </select>
            </div>

                <button id="btnClearFilters" class="btn btn-outline-secondary btn-sm" title="Limpiar filtros">
                    <i class="bi bi-x-circle"></i>
                </button>

            </div>

            <!-- Mobile Layout -->
            <div class="d-lg-none">
                <!-- Navigation -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="btn-group btn-group-sm" role="group">
                        <button id="btnPrevMobile" class="btn btn-outline-light">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button id="btnNextMobile" class="btn btn-outline-light">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                    <a id="btnOpenRangeMobile" class="range-button fw-semibold" role="button">
                        <i class="bi bi-calendar3 me-1"></i>
                        <span id="mobileDateLabel">Hoy</span>
                    </a>
                    <button class="btn btn-outline-light btn-sm" data-bs-toggle="collapse" data-bs-target="#mobileFilters">
                        <i class="bi bi-funnel"></i>
                    </button>
                </div>

                <!-- Collapsible Filters -->
                <div class="collapse" id="mobileFilters">
                    <div class="vstack gap-2">
                        <input
                            type="text"
                            id="searchInputMobile"
                            class="form-control form-control-sm"
                            placeholder="Buscar paciente…"
                        />
                        <select id="filterStaffMobile" class="form-select form-select-sm">
                            <option value="">Todos los profesionales</option>
                        </select>
                        <select id="filterTreatmentMobile" class="form-select form-select-sm">
                            <option value="">Todos los tratamientos</option>
                        </select>
                        <select id="filterStatusMobile" class="form-select form-select-sm">
                            <option value="">Todos los estados</option>
                        </select>
                        <button id="btnClearFiltersMobile" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle me-1"></i>Limpiar filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
