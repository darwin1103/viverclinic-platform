@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-6 col-lg-4">
            <h1>Equipo de Ventas</h1>
        </div>
        <div class="col-12 col-md-6 col-lg-8 text-center text-md-end mb-3 mb-md-0" style="align-content: center;">
            <a class="btn btn-primary" href="{{ route('admin.sales-manager.create') }}" role="button">
                <i class="bi bi-plus-circle-fill"></i>&nbsp;Crear nuevo vendedor
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
                                    <th scope="col">Comisión (Divisor)</th>
                                    <th scope="col">{{ __('Created') }}</th>
                                    <th scope="col">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($salesUsers as $salesUser)
                                    <tr>
                                        <td>{{ $salesUser->name }}</td>
                                        <td>{{ $salesUser->email }}</td>
                                        <td>{{ $salesUser->salesProfile->branch->name ?? '-' }}</td>
                                        <td>{{ $salesUser->salesProfile->commission_divisor ?? 26 }}</td>
                                        <td>
                                            @php
                                                \Carbon\Carbon::setLocale('es');
                                                echo \Carbon\Carbon::parse($salesUser->created_at)->isoFormat('D MMM, YYYY');
                                            @endphp
                                        </td>
                                        <td>
                                            <a class="mx-1" href="{{ route('admin.sales-manager.edit', $salesUser) }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-title="{{__('Edit')}}"><i class="bi bi-pencil-square"></i></a>
                                            <form method="POST" action="{{ route('admin.sales-manager.destroy', $salesUser) }}"
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Está seguro de eliminar este vendedor?');">
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
                                            No hay vendedores registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @if (isset($salesUsers))
                            {{ $salesUsers->links('layouts.numbers-pagination') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
