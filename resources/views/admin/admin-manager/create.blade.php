@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <h1>Crear nuevo administrador</h1>
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-body m-0 m-lg-3">
                    <form method="POST" action="{{ route('admin.admin-manager.store') }}" class="row g-3">
                        @csrf
                        <h4>Datos personales</h4>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input id="name" type="text" placeholder="Nombre Completo" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                                <label for="name">Nombre Completo</label>
                                @error('name')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input id="email" type="email" placeholder="Correo Electrónico" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                                <label for="email">Correo Electrónico</label>
                                @error('email')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <select id="branch_id" name="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                                    <option value="">Selecciona una sucursal</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                <label for="branch_id">Sucursal</label>
                                @error('branch_id')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <h4 class="mt-4">Configuración financiera</h4>

                        <div class="col-12 col-md-4">
                            <div class="form-floating">
                                <input id="salary" type="text" inputmode="numeric" placeholder="Sueldo mensual" class="form-control currency-input @error('salary') is-invalid @enderror" name="salary" value="{{ old('salary', 0) }}" required>
                                <label for="salary">Sueldo mensual (COP)</label>
                                @error('salary')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="form-floating">
                                <input id="commission_divisor" type="number" min="1" placeholder="Divisor" class="form-control @error('commission_divisor') is-invalid @enderror" name="commission_divisor" value="{{ old('commission_divisor', 30) }}">
                                <label for="commission_divisor">Divisor de ventas</label>
                                @error('commission_divisor')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <small class="text-secondary">Fórmula: (ventas_mes / divisor) - base</small>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="form-floating">
                                <input id="commission_base" type="text" inputmode="numeric" placeholder="Base" class="form-control currency-input @error('commission_base') is-invalid @enderror" name="commission_base" value="{{ old('commission_base', 2500000) }}">
                                <label for="commission_base">Base a restar (COP)</label>
                                @error('commission_base')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary w-auto mt-2">Crear Administrador</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
