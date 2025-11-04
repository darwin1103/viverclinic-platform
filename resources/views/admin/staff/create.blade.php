@extends('layouts.admin')
@section('content')
<div class="container">
    <h1>Crear nuevo trabajador</h1>
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">

                <div class="card-body m-0 m-lg-3">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.staff.store') }}" class="row g-3">
                        @csrf
                        <h4>Datos</h4>
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

                        <div class="col-12  col-md-6">
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

                        <div class="col-12">
                             <x-admin.staff.form.schedule-form :daysOfWeek="$daysOfWeek" />
                        </div>

                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary w-auto mt-2">Crear Usuario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/staff/schedule.js') }}"></script>
@endpush
