@extends('layouts.admin')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0 fw-semibold text-white">Promociones</h4>
        <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Añadir nueva
        </a>
    </div>

    <div class="card mt-3">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-megaphone me-2"></i>Gestión de Promociones</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Tratamiento / Paquete</th>
                        <th>Descuento</th>
                        <th>Modo Activación</th>
                        <th>Vigencia / Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promotions as $promotion)
                    <tr>
                        <td>
                            <span class="fw-semibold text-white">{{ $promotion->title }}</span>
                            @if($promotion->description)
                                <br><small class="text-muted">{{ Str::limit($promotion->description, 50) }}</small>
                            @endif
                        </td>
                        <td>
                            @if($promotion->branch)
                                <span class="badge bg-dark border border-secondary text-white mb-1">{{ $promotion->branch->name }}</span>
                                <br>
                            @endif
                            <span class="fw-semibold">{{ $promotion->treatment->name }}</span>
                            <br>
                            <small class="text-secondary">
                                {{ $promotion->package->name }}
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark fw-bold">
                                {{ $promotion->formatted_discount }}
                            </span>
                        </td>
                        <td>
                            @if($promotion->activation_mode === 'manual')
                                <span class="badge bg-info">Manual</span>
                            @else
                                <span class="badge bg-warning text-dark">Agendada</span>
                            @endif
                        </td>
                        <td>
                            @if($promotion->activation_mode === 'manual')
                                @if($promotion->is_active)
                                    <span class="badge bg-success">Activa</span>
                                @else
                                    <span class="badge bg-secondary">Inactiva</span>
                                @endif
                            @else
                                @php
                                    $today = now()->startOfDay();
                                    $start = $promotion->start_date ? $promotion->start_date->startOfDay() : null;
                                    $end = $promotion->end_date ? $promotion->end_date->endOfDay() : null;
                                    
                                    $isActive = true;
                                    if ($start && $today->lt($start)) $isActive = false;
                                    if ($end && $today->gt($end)) $isActive = false;
                                @endphp
                                
                                @if($isActive)
                                    <span class="badge bg-success">Activa</span>
                                @elseif($start && $today->lt($start))
                                    <span class="badge bg-info">Programada</span>
                                @else
                                    <span class="badge bg-danger">Vencida</span>
                                @endif
                                <br>
                                <small class="text-muted">
                                    {{ $promotion->start_date ? $promotion->start_date->format('d/m/Y') : 'N/A' }} 
                                    - 
                                    {{ $promotion->end_date ? $promotion->end_date->format('d/m/Y') : 'N/A' }}
                                </small>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2 align-items-center">
                                @if($promotion->activation_mode === 'manual')
                                    <form action="{{ route('admin.promotions.toggle-active', $promotion->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-{{ $promotion->is_active ? 'warning' : 'success' }}">
                                            {{ $promotion->is_active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                @endif
                                
                                <a href="{{ route('admin.promotions.edit', $promotion->id) }}" class="btn btn-sm btn-outline-primary">
                                    Editar
                                </a>
                                
                                <form action="{{ route('admin.promotions.destroy', $promotion->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center fw-semibold py-3 text-muted">Aún no hay promociones registradas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará la promoción permanentemente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
});
</script>
@endpush

@endsection
