@props(['appointments'])

<div class="table-responsive">
    <table class="table table-striped table-hover align-middle" id="appointmentsTable">
        <thead class="table-light">
            <tr>
                <th scope="col" class="text-white">Nombre del Paciente</th>
                <th scope="col" class="text-white">Fecha de la Cita</th>
                <th scope="col" class="text-white">Tratamiento</th>
                <th scope="col" class="text-white">Sucursal</th>
                <th scope="col" class="text-white text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($appointments as $appointment)
                <tr>
                    <td data-label="Nombre del Paciente">{{ $appointment['client_name'] }}</td>
                    <td data-label="Fecha de la Cita">{{ $appointment['date'] }}</td>
                    <td data-label="Tratamiento">{{ $appointment['treatment'] }}</td>
                    <td data-label="Sucursal">{{ $appointment['branch'] }}</td>
                    <td data-label="Acciones" class="text-center">
                        <a href="#" class="btn btn-sm btn-info me-1" title="Ver Detalles">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No hay citas pendientes.</td>
                </tr>
            @endforelse
            {{-- Fila para mostrar cuando no hay resultados de búsqueda --}}
            <tr id="noResultsMessage" style="display: none;">
                <td colspan="5" class="text-center">No se encontraron resultados.</td>
            </tr>
        </tbody>
    </table>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/general/responsive-table.css') }}">
@endpush
