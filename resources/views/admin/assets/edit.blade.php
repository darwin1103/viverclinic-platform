@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        {{-- Columna Izquierda: Datos del Activo --}}
        <div class="col-12 col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-body m-3">
                    <h4 class="card-title mb-4">Editar Activo</h4>
                    <form method="POST" action="{{ route('admin.assets.update', $asset) }}" class="row g-3">
                        @csrf
                        @method('PUT')

                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $asset->name) }}" placeholder="Nombre">
                                <label for="name">Nombre</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating">
                                <select class="form-select" id="branch_id" name="branch_id">
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" @selected($asset->branch_id == $branch->id)>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                <label for="branch_id">Sucursal</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted">Stock Actual</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="display-stock" value="{{ $asset->stock }}" disabled>

                                <button type="button" class="btn btn-warning btn-stock-modal"
                                    data-id="{{ $asset->id }}"
                                    data-name="{{ $asset->name }}"
                                    data-stock="{{ $asset->stock }}">
                                    <i class="bi bi-box-seam"></i> Modificar Stock
                                </button>
                            </div>
                        </div>

                        <div class="col-12 text-end mt-4">
                            <a href="{{ route('admin.assets.index') }}" class="btn btn-secondary">Volver</a>
                            <button type="submit" class="btn btn-primary">Actualizar Datos</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Columna Derecha: Gestión de Notas --}}
        <div class="col-12 col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Notas del Activo</h5>
                </div>
                <div class="card-body overflow-auto" style="max-height: 500px;">

                    {{-- Formulario Nueva Nota --}}
                    <form action="{{ route('admin.assets.notes.store', $asset) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="input-group">
                            <textarea class="form-control" name="content" placeholder="Escribir nueva nota..." required rows="2"></textarea>
                            <button class="btn btn-outline-primary" type="submit"><i class="bi bi-send"></i></button>
                        </div>
                    </form>

                    {{-- Lista de Notas --}}
                    <div class="d-flex flex-column gap-3">
                        @forelse($asset->notes as $note)
                            <div class="card border-light shadow-sm">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted fw-bold">{{ $note->user->name }}</small>
                                        <small class="text-muted">{{ $note->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <p class="card-text mb-2">{{ $note->content }}</p>
                                    <div class="d-flex justify-content-end gap-2">
                                        {{-- Botón Editar Nota (Abre Modal) --}}
                                        <button class="btn btn-sm btn-link text-decoration-none"
                                            onclick="openEditNoteModal({{ $note->id }}, '{{ addslashes($note->content) }}')">
                                            Editar
                                        </button>

                                        {{-- Form Eliminar Nota --}}
                                        <form action="{{ route('admin.assets.notes.destroy', $note) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar nota?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger text-decoration-none">Eliminar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted">Sin notas registradas.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Editar Nota --}}
<div class="modal fade" id="editNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editNoteForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Nota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea class="form-control" name="content" id="editNoteContent" rows="4" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>

@include('admin.assets.partials.stock-modal')

@endsection

@push('scripts')
    <script src="{{ asset('js/admin/assets/edit.js') }}"></script>
@endpush

