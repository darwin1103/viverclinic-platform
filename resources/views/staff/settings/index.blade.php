@extends('layouts.employee')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0 fw-semibold text-white">Configuración de Cuenta</h4>
    </div>

    <div class="card mt-3">
        <div class="card-header fw-semibold">
            <span><i class="bi bi-shield-lock me-2"></i>Seguridad</span>
        </div>
        <div class="card-body">
            <form action="{{ route('staff.settings.password') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label">Contraseña Actual <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="••••••••" required>
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Nueva Contraseña <span class="text-danger">*</span></label>
                        <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="••••••••" required>
                        @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                        <input type="password" name="new_password_confirmation" class="form-control @error('new_password_confirmation') is-invalid @enderror" placeholder="••••••••" required>
                        @error('new_password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
