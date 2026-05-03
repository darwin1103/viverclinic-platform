@extends('layouts.admin')
@section('content')

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 col-md-8 col-lg-6">
            <h1>Editar vendedor</h1>
        </div>
        <div class="col-12 col-md-4 col-lg-6 text-end">
            <a class="btn btn-secondary" href="{{ route('admin.sales-manager.index') }}">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card shadow">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.sales-manager.update', $sales_manager) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $sales_manager->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $sales_manager->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="branch_id" class="form-label">Sucursal <span class="text-danger">*</span></label>
                            <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id" name="branch_id" required>
                                <option value="" disabled>Seleccione una sucursal</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id', $sales_manager->salesProfile->branch_id ?? '') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="commission_divisor" class="form-label">Divisor de comisión (Ventas / Divisor)</label>
                            <input type="number" class="form-control @error('commission_divisor') is-invalid @enderror" id="commission_divisor" name="commission_divisor" value="{{ old('commission_divisor', $sales_manager->salesProfile->commission_divisor ?? 26) }}" min="1" step="1">
                            <small class="text-muted">El vendedor ganará: Ventas Totales de Paquetes / Este número.</small>
                            @error('commission_divisor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Actualizar Vendedor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
