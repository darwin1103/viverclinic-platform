@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h1>{{ __('Edit') }}</h1>
        </div>
    </div>
    <div class="row">
        {{-- COLUMNA FORMULARIO DE EDICIÓN --}}
        <div class="col-12 col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-body m-0 m-lg-3">
                    <h2 class="mb-3">{{__('Edit User')}}</h2>
                    <form action="{{ route('admin.client.update', ['client' => $client]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-floating">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="{{ __('Name') }}" value="{{ $client->name ?? '' }}">
                            <label for="name">{{ __('Name') }}</label>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-floating my-3">
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="{{ __('Email Address') }}" value="{{ $client->email ?? '' }}">
                            <label for="email">{{ __('Email Address') }}</label>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-floating my-3">
                            <select id="branch_id" class="form-control @error('branch_id') is-invalid @enderror" name="branch_id" required>
                                <option value="">Seleccionar</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" @if ($branch->id == ($client->patientProfile->branch_id ?? null) || old('branch_id') == $branch->id) selected @endif >{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            <label for="branch_id">Sucursal</label>
                            @error('branch_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mt-4 text-end">
                            <a href="{{ route('admin.client.index') }}" class="btn btn-secondary me-2">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- COLUMNA GESTIÓN DE ESTADO Y ELIMINACIÓN --}}
        <div class="col-12 col-lg-4 mb-4">
            {{-- TARJETA DE ESTADO --}}
            <div class="card mb-4 border-{{ $client->active ? 'success' : 'secondary' }}">
                <div class="card-header bg-{{ $client->active ? 'success' : 'secondary' }} text-white py-3">
                    <h3 class="card-title h5 mb-0 fw-bold">
                        <i class="bi {{ $client->active ? 'bi-shield-fill-check' : 'bi-shield-fill-x' }}"></i> Estado del Paciente
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="fw-semibold">Estado actual:</span>
                        @if ($client->active)
                            <span class="badge bg-success fs-6">Activo</span>
                        @else
                            <span class="badge bg-secondary fs-6">Inactivo</span>
                        @endif
                    </div>
                    <p class="text-muted small">
                        @if ($client->active)
                            Desactivar al paciente le impedirá iniciar sesión y acceder a la plataforma, pero conservará toda su información histórica (historia clínica, citas, etc.).
                        @else
                            Activar al paciente restaurará su acceso completo a la plataforma.
                        @endif
                    </p>
                    <form action="{{ route('admin.client.toggle-active', $client) }}" method="POST" class="d-grid mt-2">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-{{ $client->active ? 'warning' : 'success' }} fw-bold">
                            <i class="bi {{ $client->active ? 'bi-person-x-fill' : 'bi-person-check-fill' }}"></i>
                            {{ $client->active ? 'Desactivar Cuenta' : 'Activar Cuenta' }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- TARJETA ZONA DE PELIGRO --}}
            <div class="card border-danger">
                <div class="card-header bg-danger-subtle text-danger py-3">
                    <h3 class="card-title h5 mb-0 fw-bold">
                        <i class="bi bi-exclamation-octagon-fill"></i> Zona de Peligro
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-danger fw-bold small mb-2">Eliminar Paciente Permanentemente</p>
                    <p class="text-muted small mb-3">
                        Esta acción ejecutará un borrado completo de toda la información clínica, citas y pagos. No se puede deshacer de ninguna manera.
                    </p>
                    <div class="d-grid">
                        <button class="btn btn-danger fw-bold" type="button"
                                onclick="showDeleteConfirmation('{{ $client->id }}', '{{ url("/admin/client") }}')">
                            <i class="bi bi-trash-fill"></i> Eliminar Paciente
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.client.partials.deleteConfirmationModal')

@endsection

@push('scripts')
    <script src="{{ asset('js/admin/client/index/showDeleteConfirmation.js') }}"></script>
@endpush
