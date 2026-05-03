@extends('layouts.admin')
@section('content')
<div class="container-fluid">

    {{-- Title --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0"><i class="bi bi-journal-text me-2"></i>Contabilidad</h4>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                <i class="bi bi-plus-lg me-1"></i>Registrar Gasto
            </button>
            @if($isSuperAdmin)
            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#manageCategoriesModal">
                <i class="bi bi-tags me-1"></i>Categorías
            </button>
            @endif
        </div>
    </div>

    @if($isSuperAdmin)
    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card kpi h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-secondary small">Ingresos</div>
                            <div class="kpi-value mt-1 text-success">${{ number_format($totalIncome, 0, ',', '.') }}</div>
                        </div>
                        <i class="bi bi-arrow-up-circle fs-3 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card kpi h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-secondary small">Egresos</div>
                            <div class="kpi-value mt-1 text-danger">${{ number_format($totalExpense, 0, ',', '.') }}</div>
                        </div>
                        <i class="bi bi-arrow-down-circle fs-3 text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card kpi h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-secondary small">Balance neto</div>
                            <div class="kpi-value mt-1 {{ $netBalance >= 0 ? 'text-success' : 'text-danger' }}">${{ number_format($netBalance, 0, ',', '.') }}</div>
                        </div>
                        <i class="bi bi-wallet fs-3 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card kpi h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-secondary small">Cuotas pendientes</div>
                            <div class="kpi-value mt-1">${{ number_format($pendingInstallments, 0, ',', '.') }}</div>
                        </div>
                        <i class="bi bi-hourglass-split fs-3 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.accounting.index') }}" class="row g-2 align-items-end">
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Desde</label>
                    <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Hasta</label>
                    <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Tipo</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="income" {{ $typeFilter === 'income' ? 'selected' : '' }}>Ingreso</option>
                        <option value="expense" {{ $typeFilter === 'expense' ? 'selected' : '' }}>Egreso</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Categoría</label>
                    <select name="category" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}" {{ $categoryFilter === $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                @if($isSuperAdmin)
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Sucursal</label>
                    <select name="branch_id" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $selectedBranchID == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search me-1"></i>Filtrar</button>
                    <a href="{{ route('admin.accounting.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Records Table --}}
    <div class="card">
        <div class="card-header fw-semibold">
            <i class="bi bi-table me-2"></i>Registros Contables
            <span class="badge bg-secondary ms-2">{{ $records->total() }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Fecha</th>
                        <th>Tipo</th>
                        <th>Categoría</th>
                        <th>Descripción</th>
                        <th>Monto</th>
                        <th>Sucursal</th>
                        <th>Registrado por</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                    <tr>
                        <td class="ps-3">{{ $record->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if(in_array($record->type, ['income', 'ingreso']))
                                <span class="badge bg-success">Ingreso</span>
                            @else
                                <span class="badge bg-danger">Egreso</span>
                            @endif
                        </td>
                        <td>{{ $record->category ?? '-' }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($record->description, 50) }}</td>
                        <td class="fw-bold {{ in_array($record->type, ['income', 'ingreso']) ? 'text-success' : 'text-danger' }}">
                            {{ in_array($record->type, ['income', 'ingreso']) ? '+' : '-' }}${{ number_format($record->amount, 0, ',', '.') }}
                        </td>
                        <td>{{ $record->branch->name ?? '-' }}</td>
                        <td>{{ $record->user->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            No hay registros para el periodo seleccionado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())
        <div class="card-footer">
            {{ $records->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modal: Add Expense --}}
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.accounting.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addExpenseModalLabel"><i class="bi bi-plus-circle me-2"></i>Registrar Gasto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="type" value="expense">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Monto (COP)</label>
                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <input type="text" class="form-control" id="description" name="description" maxlength="255" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Categoría</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">Sin categoría</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($isSuperAdmin)
                    <div class="mb-3">
                        <label for="branch_id" class="form-label">Sucursal</label>
                        <select class="form-select" id="branch_id" name="branch_id">
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $selectedBranchID == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Manage Categories --}}
<div class="modal fade" id="manageCategoriesModal" tabindex="-1" aria-labelledby="manageCategoriesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageCategoriesModalLabel"><i class="bi bi-tags me-2"></i>Gestionar Categorías</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Add new category --}}
                <form method="POST" action="{{ route('admin.expense-categories.store') }}" class="d-flex gap-2 mb-3">
                    @csrf
                    <input type="text" name="name" class="form-control form-control-sm" placeholder="Nueva categoría..." required>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i></button>
                </form>
                {{-- Existing categories --}}
                <ul class="list-group">
                    @forelse($categories as $cat)
                    <li class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center">
                        <span>{{ $cat->name }}</span>
                        <form method="POST" action="{{ route('admin.expense-categories.destroy', $cat) }}" class="d-inline"
                              onsubmit="return confirm('¿Eliminar esta categoría?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </li>
                    @empty
                    <li class="list-group-item bg-transparent text-muted text-center">No hay categorías creadas.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
