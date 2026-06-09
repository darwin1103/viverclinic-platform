@props(['treatments'])

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('admin.contracted-treatment.index') }}" class="row g-2 align-items-end" id="filter-form">
            <div class="col-12 col-md-3">
                <label class="form-label small mb-1">Buscar cliente</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Nombre o correo..." value="{{ request('search') }}">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">Tratamiento</label>
                <select name="treatment_id" class="form-select form-select-sm">
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
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">Desde</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">Hasta</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
            </div>
            
            <input type="hidden" name="branch_id" id="branch_id_hidden" value="{{ request('branch_id') }}">
            
            <div class="col-12 col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                <a href="{{ route('admin.contracted-treatment.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>
