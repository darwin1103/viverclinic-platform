<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('owner.index') }}" method="GET" id="filter-form">
            <div class="row g-3 align-items-end">

                {{-- Campo de búsqueda por nombre o correo --}}
                <div class="col-12 col-md-8">
                    <label for="search-input" class="form-label">Buscar por nombre o correo</label>
                    <input type="text" class="form-control" id="search-input" name="search" placeholder="Buscar por nombre o correo..." value="{{ request('search') }}">
                </div>

                {{-- Campo oculto para el filtro de sucursal (controlado por JS) --}}
                <input type="hidden" name="branch_id" id="branch-id-filter" value="{{ request('branch_id') }}">

                {{-- Botón para limpiar filtros --}}
                <div class="col-12 col-md-4 d-grid">
                    <a href="{{ route('owner.index') }}" class="btn btn-secondary"><i class="bi bi-eraser-fill"></i> Limpiar</a>
                </div>

            </div>
        </form>
    </div>
</div>
