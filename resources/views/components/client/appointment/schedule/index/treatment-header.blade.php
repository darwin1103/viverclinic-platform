@props(['treatment', 'branch', 'attendedCount', 'missedCount', 'pendingCount'])

<div class="d-flex align-items-center justify-content-between flex-wrap mb-4">
    <h3 class="fw-semibold mb-3 mb-lg-0">Control de Tratamiento</h3>
    <div class="hstack gap-2 flex-wrap">
        <span class="badge text-bg-success">
            <span class="legend-dot" style="background:#8be0c2"></span>
            Asistidas: {{ $attendedCount }}
        </span>
        <span class="badge text-bg-danger">
            <span class="legend-dot" style="background:#ff9b9b"></span>
            No asistidas: {{ $missedCount }}
        </span>
        <span class="badge text-bg-secondary">
            <span class="legend-dot" style="background:#ccebef"></span>
            Pendientes: {{ $pendingCount }}
        </span>
    </div>
</div>

<div class="row g-4">
    @if($branch->logo)
    <div class="col-12 col-lg-2">
        <img src="{{ $branch->logo ? Storage::url($branch->logo) : '' }}" alt="Logo" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
    </div>
    @endif
    <div class="col-12 col-lg-6">
        <p class="mb-1">
            <strong>Tratamiento:</strong> {{ $treatment->name }}
        </p>
        <p class="mb-1">
            <strong>Sucursal:</strong> {{ $branch->name }}
        </p>
        <p class="mb-1">
            Dirección:
            <a href="{{ $branch->google_maps_url }}" target="_blank" class="link-primary">
                {{ $branch->address }} <i class="bi bi-box-arrow-up-right"></i>
            </a>
        </p>
        <p class="mb-1">
            Teléfono:
            <a href="tel:{{ $branch->phone }}" target="_blank" class="link-primary">
                {{ $branch->phone }}
            </a>
        </p>
    </div>
</div>
