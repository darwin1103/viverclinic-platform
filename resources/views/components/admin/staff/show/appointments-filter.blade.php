@props(['treatments', 'staff'])

<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('admin.staff.show', ['staff' => $staff->id]) }}" method="GET" id="filter-form">
            <div class="row g-3 align-items-end">

                <input type="hidden" name="is_on_appointment_table" value="1">

                {{-- Search by patient name --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <label for="search" class="form-label">Buscar por Paciente</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Nombre del paciente..." value="{{ request('search') }}">
                    </div>
                </div>

                {{-- Filter by treatment --}}
                <div class="col-12 col-md-6 col-lg-2">
                    <label for="treatment_id" class="form-label">Tratamiento</label>
                    <select class="form-select" id="treatment_id" name="treatment_id">
                        <option value="">Todos</option>
                        @foreach ($treatments as $treatment)
                            <option value="{{ $treatment->id }}" @selected(request('treatment_id') == $treatment->id)>
                                {{ $treatment->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Min date filter --}}
                <div class="col-12 col-md-6 col-lg-2">
                    <label for="min_date" class="form-label">Desde</label>
                    <input type="date" class="form-control" id="min_date" name="min_date" value="{{ request('min_date') }}">
                </div>

                {{-- Max date filter --}}
                <div class="col-12 col-md-6 col-lg-2">
                    <label for="max_date" class="form-label">Hasta</label>
                    <input type="date" class="form-control" id="max_date" name="max_date" value="{{ request('max_date') }}">
                </div>

                {{-- Buttons --}}
                <div class="col-12 col-lg-2 d-flex align-items-end">
                    <div class="d-grid d-lg-flex gap-2 w-100">
                        <a href="{{ route('staff.appointment.index') }}" class="btn btn-secondary w-100"><i class="bi bi-eraser-fill"></i> Limpiar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
