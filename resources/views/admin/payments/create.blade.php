@extends('layouts.admin')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0 fw-semibold text-white">Registrar Pago</h4>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="card mt-3">
        <div class="card-header fw-semibold">
            <span><i class="bi bi-cash-coin me-2"></i>Nuevo Pago</span>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.payments.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Paciente <span class="text-danger">*</span></label>
                        <select class="form-select @error('user_id') is-invalid @enderror" name="user_id" required>
                            <option value="">Seleccione un paciente</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('user_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Tratamiento Contratado <span class="text-danger">*</span></label>
                        <select class="form-select @error('contracted_treatment_id') is-invalid @enderror" name="contracted_treatment_id" required>
                            <option value="">Seleccione un tratamiento</option>
                            @foreach($contractedTreatments as $contract)
                                <option value="{{ $contract->id }}" {{ old('contracted_treatment_id') == $contract->id ? 'selected' : '' }}>
                                    {{ $contract->treatment->name ?? 'Tratamiento sin nombre' }} ({{ $contract->status }})
                                </option>
                            @endforeach
                        </select>
                        @error('contracted_treatment_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Monto <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="total" class="form-control @error('total') is-invalid @enderror" value="{{ old('total') }}" placeholder="Ej. 10000" required>
                        @error('total')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Método de Pago <span class="text-danger">*</span></label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" name="payment_method" required>
                            <option value="">Seleccione método</option>
                            <option value="Efectivo" {{ old('payment_method') == 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                            <option value="Tarjeta de Crédito" {{ old('payment_method') == 'Tarjeta de Crédito' ? 'selected' : '' }}>Tarjeta de Crédito</option>
                            <option value="Tarjeta de Débito" {{ old('payment_method') == 'Tarjeta de Débito' ? 'selected' : '' }}>Tarjeta de Débito</option>
                            <option value="Transferencia" {{ old('payment_method') == 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                        </select>
                        @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
