@forelse($assets as $asset)
<tr>
    <td>{{ $asset->id }}</td>
    <td>{{ $asset->name }}</td>
    <td>
        <span class="badge bg-info text-dark">{{ $asset->branch->name }}</span>
    </td>
    <td class="text-center fw-bold">{{ $asset->stock }}</td>
    <td>
        <div class="d-flex justify-content-center gap-2">
            {{-- Botón Modificar Stock --}}
            <button type="button" class="btn btn-warning btn-sm btn-stock-modal"
                data-id="{{ $asset->id }}"
                data-name="{{ $asset->name }}"
                data-stock="{{ $asset->stock }}">
                <i class="bi bi-box-seam"></i> Stock
            </button>

            {{-- Botón Editar --}}
            <a href="{{ route('admin.assets.edit', $asset) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square"></i>
            </a>

            {{-- Botón Eliminar --}}
            <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="{{ $asset->id }}">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="4" class="text-center text-muted">No se encontraron activos.</td>
</tr>
@endforelse
