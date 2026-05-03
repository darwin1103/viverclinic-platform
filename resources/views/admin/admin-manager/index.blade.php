@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-6 col-lg-4">
            <h1>Administradores de Tienda</h1>
        </div>
        <div class="col-12 col-md-6 col-lg-8 text-center text-md-end mb-3 mb-md-0" style="align-content: center;">
            <a class="btn btn-primary" href="{{ route('admin.admin-manager.create') }}" role="button">
                <i class="bi bi-plus-circle-fill"></i>&nbsp;Crear nuevo administrador
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mt-3">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('Name') }}</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Sucursal</th>
                                    <th scope="col">Sueldo</th>
                                    <th scope="col">{{ __('Created') }}</th>
                                    <th scope="col">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($admins as $admin)
                                    <tr>
                                        <td>{{ $admin->name }}</td>
                                        <td>{{ $admin->email }}</td>
                                        <td>{{ $admin->adminProfile->branch->name ?? '-' }}</td>
                                        <td>${{ number_format($admin->adminProfile->salary ?? 0, 0, ',', '.') }}</td>
                                        <td>
                                            @php
                                                \Carbon\Carbon::setLocale('es');
                                                echo \Carbon\Carbon::parse($admin->created_at)->isoFormat('D MMM, YYYY');
                                            @endphp
                                        </td>
                                        <td>
                                            <a class="mx-1" href="{{ route('admin.admin-manager.edit', $admin) }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-title="{{__('Edit')}}"><i class="bi bi-pencil-square"></i></a>
                                            <form method="POST" action="{{ route('admin.admin-manager.destroy', $admin) }}"
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Está seguro de eliminar este administrador?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted">
                                            No hay administradores registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @if (isset($admins))
                            {{ $admins->links('layouts.numbers-pagination') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
