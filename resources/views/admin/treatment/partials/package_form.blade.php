{{-- resources/views/admin/treatments/partials/package_form.blade.php --}}
<div class="row g-3 p-3 border rounded mb-3 package-form-row">
    <div class="col-12 col-md-3">
        <label class="form-label">Nombre del paquete</label>
        <input type="text" name="branches[{{ $branch->id }}][packages][{{ $key }}][name]" class="form-control" placeholder="Ej: Paquete Premium" value="{{ $package->name ?? '' }}" required>
    </div>
    <div class="col-12 col-md-3">
        <label class="form-label">Precio</label>
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
    <div class="col-12 col-md-2 d-flex align-items-end">
        <button type="button" class="btn btn-danger w-100 remove-package-btn">
            <i class="bi bi-trash-fill"></i> Quitar
        </button>
    </div>
</div>
