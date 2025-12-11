@extends('layouts.admin')

@section('content')
<x-admin-card title="Configuración">
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">

            <div class="col-12">
                <label for="wompi_public_key" class="form-label fw-bold">Wompi Public Key</label>
                <input type="text" class="form-control" id="wompi_public_key" name="wompi_public_key"
                       value="{{ $wompiPublicKey }}" placeholder="pub_prod_...">
            </div>

            <div class="col-12">
                <label for="wompi_integrity_secret" class="form-label fw-bold">Wompi Integrity Secret</label>
                <input type="text" class="form-control" id="wompi_integrity_secret" name="wompi_integrity_secret"
                       value="{{ $wompiIntegritySecret }}" placeholder="prod_integrity_...">
            </div>

            <div class="col-12 mt-4 text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar Configuración
                </button>
            </div>
        </div>
    </form>
</x-admin-card>
@endsection
