@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold">Configuración Global de Agenda</h2>
            <p class="text-secondary">Administra los cupos, bloques de horarios y personal habilitado.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">

        <!-- SECCIÓN 1: CUPOS GLOBALES -->
        <div class="col-12">
            <x-admin-card title="Cupos por Bloque de 20 Minutos">
                <p class="text-secondary small mb-3">Define cuántos cupos están disponibles en cada bloque de 20 minutos para toda la agenda. Los cupos regulares son visibles para todos los usuarios. Los cupos de ventas son adicionales, visibles solo para roles de administración y ventas.</p>
                <form action="{{ route('admin.global-schedule.slots') }}" method="POST">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-4">
                            <label for="regular_slots" class="form-label fw-bold mb-0">Cupos Regulares</label>
                            <div class="text-secondary small mb-2" style="font-size: 0.75rem;">Para pacientes en cada bloque de 20 min.</div>
                            <input type="number" min="0" class="form-control" id="regular_slots" name="regular_slots" value="{{ $regularSlots }}" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="sales_slots" class="form-label fw-bold mb-0">Cupos de Ventas</label>
                            <div class="text-secondary small mb-2" style="font-size: 0.75rem;">Adicionales solo para administración/ventas.</div>
                            <input type="number" min="0" class="form-control" id="sales_slots" name="sales_slots" value="{{ $salesSlots }}" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-save me-2"></i>Guardar Cupos
                            </button>
                        </div>
                    </div>
                </form>
            </x-admin-card>
        </div>

        <!-- SECCIÓN 2: HORARIOS GLOBALES -->
        <div class="col-12 col-xl-8">
            <x-admin-card title="Horarios de Disponibilidad (Lunes a Domingo)">
                <p class="text-secondary small mb-3">Configura los rangos de horario en los que la clínica está disponible para agendar citas. La disponibilidad se dividirá en bloques de 20 minutos automáticamente.</p>
                <form action="{{ route('admin.global-schedule.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="branch_id" value="{{ $branchId }}">

                    <div class="accordion" id="accordionSchedules">
                        @foreach($daysOfWeek as $index => $day)
                            @php 
                                $dayBlocks = isset($schedules[$day]) ? $schedules[$day] : collect();
                            @endphp
                            <div class="accordion-item mb-2 rounded">
                                <h2 class="accordion-header" id="heading{{ $index }}">
                                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="false" aria-controls="collapse{{ $index }}">
                                        <i class="bi bi-calendar-day me-2 text-primary"></i> {{ $day }}
                                        <span class="badge bg-secondary ms-auto">{{ $dayBlocks->count() }} bloques</span>
                                    </button>
                                </h2>
                                <div id="collapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $index }}" data-bs-parent="#accordionSchedules">
                                    <div class="accordion-body">
                                        
                                        <div id="blocks-container-{{ $day }}">
                                            @foreach($dayBlocks as $bIndex => $block)
                                                <div class="row g-2 align-items-end mb-2 block-row">
                                                    <div class="col-5">
                                                        <label class="form-label small">Inicio</label>
                                                        <input type="time" class="form-control form-control-sm" name="schedules[{{ $day }}][{{ $bIndex }}][start_time]" value="{{ \Carbon\Carbon::parse($block->start_time)->format('H:i') }}" required>
                                                    </div>
                                                    <div class="col-5">
                                                        <label class="form-label small">Fin</label>
                                                        <input type="time" class="form-control form-control-sm" name="schedules[{{ $day }}][{{ $bIndex }}][end_time]" value="{{ \Carbon\Carbon::parse($block->end_time)->format('H:i') }}" required>
                                                    </div>
                                                    <div class="col-2 text-end">
                                                        <button type="button" class="btn btn-sm btn-outline-danger remove-block-btn w-100"><i class="bi bi-trash"></i></button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2 add-block-btn" data-day="{{ $day }}">
                                            <i class="bi bi-plus-circle me-1"></i> Añadir Bloque
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i>Guardar Horarios
                        </button>
                    </div>
                </form>
            </x-admin-card>
        </div>

        <!-- SECCIÓN LATERAL: EMPLEADOS Y FESTIVOS -->
        <div class="col-12 col-xl-4">
            
            <!-- Empleados Habilitados -->
            <x-admin-card title="Personal Habilitado">
                <p class="text-secondary small mb-3">Activa o desactiva a los empleados. Los desactivados no recibirán asignaciones de citas.</p>
                <div class="list-group">
                    @forelse($employees as $emp)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold d-block">{{ $emp->name }}</span>
                                <small class="text-secondary">{{ $emp->email }}</small>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge {{ $emp->is_enabled_for_appointments ? 'bg-success' : 'bg-danger' }}" id="status-badge-{{ $emp->id }}">
                                    {{ $emp->is_enabled_for_appointments ? 'Habilitado' : 'Deshabilitado' }}
                                </span>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input employee-toggle" type="checkbox" role="switch" data-user-id="{{ $emp->id }}" {{ $emp->is_enabled_for_appointments ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-secondary text-center py-2">No hay empleados registrados.</div>
                    @endforelse
                </div>
            </x-admin-card>

            <!-- Festivos -->
            <div class="mt-4">
                <x-admin-card title="Días Festivos">
                    <p class="text-secondary small mb-3">Bloquea por completo el agendamiento en estas fechas.</p>
                    
                    <form action="{{ route('admin.holidays.store') }}" method="POST" class="row g-2 align-items-end mb-4">
                        @csrf
                        <div class="col-12">
                            <label class="form-label small">Fecha</label>
                            <input type="date" class="form-control form-control-sm" name="date" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Motivo</label>
                            <input type="text" class="form-control form-control-sm" name="name" placeholder="Ej: Navidad" required>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                                <i class="bi bi-plus-circle me-1"></i>Agregar Festivo
                            </button>
                        </div>
                    </form>

                    @if($holidays->isEmpty())
                        <div class="text-secondary text-center py-2 small">No hay días festivos registrados.</div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($holidays as $holiday)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-3">
                                    <div>
                                        <span class="badge {{ $holiday->date->isPast() ? 'bg-secondary' : 'bg-danger' }} mb-1">
                                            {{ $holiday->date->isoFormat('DD MMM YYYY') }}
                                        </span>
                                        <small class="d-block">{{ $holiday->name }}</small>
                                    </div>
                                    <form action="{{ route('admin.holidays.destroy', $holiday) }}" method="POST" onsubmit="return confirm('¿Eliminar este festivo?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-admin-card>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Añadir nuevos bloques
    document.querySelectorAll('.add-block-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const day = this.getAttribute('data-day');
            const container = document.getElementById('blocks-container-' + day);
            const index = container.children.length;
            
            const html = `
                <div class="row g-2 align-items-end mb-2 block-row">
                    <div class="col-5">
                        <label class="form-label small">Inicio</label>
                        <input type="time" class="form-control form-control-sm" name="schedules[${day}][${index}][start_time]" required>
                    </div>
                    <div class="col-5">
                        <label class="form-label small">Fin</label>
                        <input type="time" class="form-control form-control-sm" name="schedules[${day}][${index}][end_time]" required>
                    </div>
                    <div class="col-2 text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-block-btn w-100"><i class="bi bi-trash"></i></button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        });
    });

    // Remover bloques
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-block-btn')) {
            e.target.closest('.block-row').remove();
        }
    });

    // Toggle estado empleado
    document.querySelectorAll('.employee-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const userId = this.getAttribute('data-user-id');
            const isEnabled = this.checked ? 1 : 0;
            
            fetch(`/admin/agenda-settings/employee/${userId}/toggle`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ is_enabled: isEnabled })
            })
            .then(response => response.json())
            .then(data => {
                if(!data.success) {
                    alert('Error actualizando estado');
                    this.checked = !this.checked;
                } else {
                    const badge = document.getElementById('status-badge-' + userId);
                    if (badge) {
                        if (isEnabled) {
                            badge.className = 'badge bg-success';
                            badge.textContent = 'Habilitado';
                        } else {
                            badge.className = 'badge bg-danger';
                            badge.textContent = 'Deshabilitado';
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.checked = !this.checked;
            });
        });
    });
});
</script>
@endpush
@endsection
