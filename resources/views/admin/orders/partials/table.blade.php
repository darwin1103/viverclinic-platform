<x-admin-card title="Gestión de Compras">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-white"># Orden</th>
                    <th class="text-white">Paciente</th>
                    <th class="text-white">Sucursal</th>
                    <th class="text-white">Fecha Compra</th>
                    <th class="text-white">Estado</th>
                    <th class="text-end text-white">Total</th>
                    <th class="text-center text-white">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold">{{ $order->user->name }}</span>
                                <span class="text-muted small">{{ $order->user->email }}</span>
                            </div>
                        </td>
                        <td><span class="badge bg-info text-dark">{{ $order->branch->name }}</span></td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @php
                                $statusColor = match($order->status) {
                                    'Pago completado' => 'success',
                                    'Pago por verificar' => 'warning',
                                    'Entregado' => 'primary',
                                    'Cancelado' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $statusColor }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="text-end">$ {{ number_format($order->total, 2, ',', '.') }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary" title="Ver Detalles">
                                <i class="bi bi-eye"></i> Gestionar
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">No se encontraron órdenes con estos criterios.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $orders->links() }}
    </div>
</x-admin-card>
