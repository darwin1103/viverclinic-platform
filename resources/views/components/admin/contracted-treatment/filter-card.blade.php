@props(['treatments'])

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.contracted-treatment.index') }}" method="GET" id="filter-form">
            <div class="row g-3 align-items-end">

                {{-- Search by client name or email --}}
                <div class="col-12 col-md-6">
                    <label for="search" class="form-label">Buscar cliente</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Nombre o correo del cliente..." value="{{ request('search') }}">
                </div>

                {{-- Filter by Treatment --}}
                <div class="col-12 col-md-4">
                    <label for="treatment_id_filter" class="form-label">Tratamiento</label>
                    <select class="form-select" id="treatment_id_filter" name="treatment_id">
                        <option value="">Todos</option>
                        @if($treatments && $treatments->count() > 0)
                            @foreach ($treatments as $treatment)
                                <option value="{{ $treatment->id }}" @selected(request('treatment_id') == $treatment->id)>
                                    {{ $treatment->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Hidden input for branch filter (controlled by header selector via JS) --}}
                <input type="hidden" name="branch_id" id="branch_id_hidden" value="{{ request('branch_id') }}">

                {{-- Buttons --}}
                <div class="col-12 col-md-2">
                     <a href="{{ route('admin.contracted-treatment.index') }}" class="btn btn-secondary">
                        <i class="bi bi-eraser-fill me-1"></i> Limpiar Filtros
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
