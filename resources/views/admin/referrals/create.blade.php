@extends('layouts.admin')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0 fw-semibold text-white">Registrar Referido</h4>
        <a href="{{ route('admin.referrals.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="card mt-3">
        <div class="card-header fw-semibold">
            <span><i class="bi bi-person-plus me-2"></i>Nuevo Referido</span>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.referrals.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Paciente que refiere <span class="text-danger">*</span></label>
                        <select name="referrer_id" class="form-select @error('referrer_id') is-invalid @enderror" required>
                            <option value="">Seleccione un paciente</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('referrer_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('referrer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Nombre del Referido <span class="text-danger">*</span></label>
                        <input type="text" name="referred_name" class="form-control @error('referred_name') is-invalid @enderror" value="{{ old('referred_name') }}" placeholder="Ej. Juan Pérez" required>
                        @error('referred_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Email del Referido</label>
                        <input type="email" name="referred_email" class="form-control @error('referred_email') is-invalid @enderror" value="{{ old('referred_email') }}" placeholder="Ej. juan@example.com">
                        @error('referred_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Teléfono del Referido</label>
                        <input type="text" name="referred_phone" class="form-control @error('referred_phone') is-invalid @enderror" value="{{ old('referred_phone') }}" placeholder="Ej. +123456789">
                        @error('referred_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
