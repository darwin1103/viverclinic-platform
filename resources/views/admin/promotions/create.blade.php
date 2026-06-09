@extends('layouts.admin')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0 fw-semibold text-white">Crear Promoción</h4>
        <a href="{{ route('admin.promotions.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="card mt-3">
        <div class="card-header fw-semibold">
            <span><i class="bi bi-plus-circle me-2"></i>Nueva Promoción</span>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.promotions.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <!-- Título -->
                    <div class="col-12 col-md-6">
                        <label class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Ej. Promoción de Verano" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Sucursal -->
                    <div class="col-12 col-md-6">
                        <label class="form-label">Sucursal <span class="text-danger">*</span></label>
                        <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                            <option value="">Seleccione una sucursal</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Tratamiento -->
                    <div class="col-12 col-md-6">
                        <label class="form-label">Tratamiento <span class="text-danger">*</span></label>
                        <select name="treatment_id" id="treatment_id" class="form-select @error('treatment_id') is-invalid @enderror" required>
                            <option value="">Seleccione un tratamiento</option>
                            @foreach($treatments as $treatment)
                                <option value="{{ $treatment->id }}" {{ old('treatment_id') == $treatment->id ? 'selected' : '' }}>
                                    {{ $treatment->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('treatment_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Paquete -->
                    <div class="col-12 col-md-6">
                        <label class="form-label">Paquete / Plan <span class="text-danger">*</span></label>
                        <select name="branch_treatment_id" id="branch_treatment_id" class="form-select @error('branch_treatment_id') is-invalid @enderror" required>
                            <option value="">Seleccione sucursal y tratamiento</option>
                        </select>
                        @error('branch_treatment_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Tipo de Descuento -->
                    <div class="col-12 col-md-3">
                        <label class="form-label">Tipo de Descuento <span class="text-danger">*</span></label>
                        <select name="discount_type" id="discount_type" class="form-select @error('discount_type') is-invalid @enderror" required>
                            <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Porcentaje (%)</option>
                            <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Monto Fijo ($)</option>
                        </select>
                        @error('discount_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Monto Descuento -->
                    <div class="col-12 col-md-3">
                        <label class="form-label">Descuento <span class="text-danger">*</span></label>
                        <input type="text" inputmode="numeric" name="discount" class="form-control currency-input @error('discount') is-invalid @enderror" value="{{ old('discount') }}" placeholder="Ej. 15 o 50000" required>
                        @error('discount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Descripción -->
                    <div class="col-12">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Detalles de la promoción...">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Modo de Activación -->
                    <div class="col-12 col-md-4">
                        <label class="form-label">Modo de Activación <span class="text-danger">*</span></label>
                        <select name="activation_mode" id="activation_mode" class="form-select @error('activation_mode') is-invalid @enderror" required>
                            <option value="manual" {{ old('activation_mode', 'manual') == 'manual' ? 'selected' : '' }}>Manual (Toggle Activo/Inactivo)</option>
                            <option value="scheduled" {{ old('activation_mode') == 'scheduled' ? 'selected' : '' }}>Agendada (Por fechas)</option>
                        </select>
                        @error('activation_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Fechas (Agendada) -->
                    <div class="col-12 col-md-8" id="dates_container" style="display: none;">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Fecha Inicio <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}">
                                @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Fecha Fin <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}">
                                @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const $ = window.jQuery || window.$;
    if (!$) {
        console.error('jQuery is not defined');
        return;
    }

    const $branch = $('#branch_id');
    const $treatment = $('#treatment_id');
    const $package = $('#branch_treatment_id');
    const $activationMode = $('#activation_mode');
    const $datesContainer = $('#dates_container');
    const $startDate = $('#start_date');
    const $endDate = $('#end_date');

    // Function to load packages dynamically
    function loadPackages(branchId, treatmentId, selectedPackageId = null) {
        if (!branchId || !treatmentId) {
            $package.html('<option value="">Seleccione sucursal y tratamiento</option>');
            return;
        }

        $package.html('<option value="">Cargando paquetes...</option>');

        $.getJSON("{{ route('admin.promotions.get-packages') }}", { 
            branch_id: branchId,
            treatment_id: treatmentId 
        })
            .done(function(data) {
                $package.html('<option value="">Seleccione un paquete</option>');
                if (data.length === 0) {
                    $package.html('<option value="">No hay paquetes disponibles para esta combinación</option>');
                    return;
                }
                data.forEach(function(pkg) {
                    const priceFormatted = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(pkg.price);
                    const selected = selectedPackageId && selectedPackageId == pkg.id ? 'selected' : '';
                    $package.append(`<option value="${pkg.id}" ${selected}>${pkg.name} – ${priceFormatted}</option>`);
                });
            })
            .fail(function() {
                $package.html('<option value="">Error al cargar paquetes</option>');
            });
    }

    // Handle change on branch or treatment
    function onChangeBranchOrTreatment() {
        const branchId = $branch.val();
        const treatmentId = $treatment.val();
        loadPackages(branchId, treatmentId);
    }

    $branch.on('change', onChangeBranchOrTreatment);
    $treatment.on('change', onChangeBranchOrTreatment);

    // Handle activation mode toggle
    function toggleDates() {
        if ($activationMode.val() === 'scheduled') {
            $datesContainer.show();
            $startDate.prop('required', true);
            $endDate.prop('required', true);
        } else {
            $datesContainer.hide();
            $startDate.prop('required', false);
            $endDate.prop('required', false);
            $startDate.val('');
            $endDate.val('');
        }
    }

    $activationMode.on('change', toggleDates);

    // Initial triggers on load
    toggleDates();

    // Set initial packages text if empty
    if (!$branch.val() || !$treatment.val()) {
        $package.html('<option value="">Seleccione sucursal y tratamiento</option>');
    }

    @if(old('branch_id') && old('treatment_id'))
        loadPackages("{{ old('branch_id') }}", "{{ old('treatment_id') }}", "{{ old('branch_treatment_id') }}");
    @endif
});
</script>
@endpush
@endsection
