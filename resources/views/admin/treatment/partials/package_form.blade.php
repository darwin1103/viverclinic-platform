<div class="row g-3 p-3 border rounded mb-3 package-form-row" data-package-id="{{ $key }}">
    {{-- Campos existentes --}}
    <div class="col-12 col-md-3">
        <label class="form-label">Nombre del paquete</label>
        <input type="text" name="branches[{{ $branch->id }}][packages][{{ $key }}][name]" class="form-control" placeholder="Ej: Paquete Premium" value="{{ $package->name ?? '' }}" required>
    </div>
    <div class="col-12 col-md-3">
        <label class="form-label">Precio Total</label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" name="branches[{{ $branch->id }}][packages][{{ $key }}][price]" class="form-control" placeholder="0.00" step="0.01" min="0" value="{{ $package->price ?? '' }}" required>
        </div>
    </div>
    <div class="col-12 col-md-2">
        <label class="form-label">Zonas Grandes</label>
        <input type="number" name="branches[{{ $branch->id }}][packages][{{ $key }}][big_zones]" class="form-control" step="1" min="0" value="{{ $package->big_zones ?? '' }}" required>
    </div>
    <div class="col-12 col-md-2">
        <label class="form-label">Mini Zonas</label>
        <input type="number" name="branches[{{ $branch->id }}][packages][{{ $key }}][mini_zones]" class="form-control" step="1" min="0" value="{{ $package->mini_zones ?? '' }}" required>
    </div>

    {{-- Sección de Cuotas --}}
    <div class="col-12 mt-3">
        <div class="form-check form-switch">
            <input class="form-check-input toggle-installments" type="checkbox" role="switch"
                   id="allow_installments_{{ $branch->id }}_{{ $key }}"
                   name="branches[{{ $branch->id }}][packages][{{ $key }}][allow_installments]"
                   value="1"
                   {{ (isset($package->allow_installments) && $package->allow_installments) ? 'checked' : '' }}>
            <label class="form-check-label" for="allow_installments_{{ $branch->id }}_{{ $key }}">Habilitar pago en cuotas</label>
        </div>

        <div class="installments-wrapper mt-3 ps-3 border-start border-3 border-info {{ (isset($package->allow_installments) && $package->allow_installments) ? '' : 'd-none' }}">
            <h6>Configuración de Cuotas</h6>
            <div class="mb-3">
                <label class="form-label fw-semibold">Condiciones de cuotas</label>
                <input type="text" name="branches[{{ $branch->id }}][packages][{{ $key }}][installment_conditions]" class="form-control"
                       value="{{ $package->installment_conditions ?? 'Cancela el 50% del tratamiento para comenzar y el otro 50% en la tercera sesión' }}" placeholder="Ej: Cancela el 50% del tratamiento para comenzar..." required>
            </div>
            <div class="installments-container" id="installments-container-{{ $branch->id }}-{{ $key }}">
                @php
                    $installments = $package->installments ?? [];
                    // Si viene de old input, necesitamos reconstruirlo, si no, de la relación
                    if(empty($installments) && isset($package->installments_data)) {
                         $installments = $package->installments_data; // Caso old input array
                    }
                @endphp

                @foreach($installments as $iKey => $inst)
                    <div class="row g-2 mb-2 installment-row">
                        <div class="col-auto d-flex align-items-center">
                            <span class="badge bg-secondary installment-label">Cuota {{ $loop->iteration }}</span>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Precio $</span>
                                <input type="number"
                                       name="branches[{{ $branch->id }}][packages][{{ $key }}][installments][{{ $iKey }}][price]"
                                       class="form-control" step="0.01" min="0"
                                       value="{{ is_array($inst) ? $inst['price'] : $inst->price }}" required>
                            </div>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-installment-btn"><i class="bi bi-x-lg"></i></button>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-sm btn-info text-white mt-2 add-installment-btn"
                    data-branch="{{ $branch->id }}" data-package="{{ $key }}">
                <i class="bi bi-plus"></i> Agregar Cuota
            </button>
            <div class="form-text text-muted">La cantidad de cuotas no puede superar el número de sesiones del tratamiento.</div>
        </div>
    </div>

    <div class="col-12 mt-3 text-end border-top pt-2">
        <button type="button" class="btn btn-danger btn-sm remove-package-btn">
            <i class="bi bi-trash-fill"></i> Quitar Paquete Completo
        </button>
    </div>
</div>
