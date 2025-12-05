<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-white"># Orden</th>
                    <th class="text-white">Fecha</th>
                    <th class="text-white">Estado</th>
                    <th class="text-end text-white">Total</th>
                    <th class="text-center text-white">Detalles</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @php
                                $badge = match($order->status) {
                                    'Pago completado' => 'success',
                                    'Entregado' => 'success',
                                    'Pago por verificar' => 'warning',
                                    'Cancelado' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ $order->status }}</span>
                        </td>
                        <td class="text-end fw-bold">$ {{ number_format($order->total, 2) }}</td>
                        <td class="text-center">
                            <a href="{{ route('client.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No tienes compras registradas en este periodo.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">
    {{ $orders->links() }}
</div>
