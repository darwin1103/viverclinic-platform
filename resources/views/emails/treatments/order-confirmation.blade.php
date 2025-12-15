<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pago</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { background-color: #0d6efd; color: #fff; padding: 20px; text-align: center; }
        .content { padding: 30px; }
        .table-details { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table-details th, .table-details td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        .table-details th { background-color: #f8f9fa; color: #555; }
        .status-badge { display: inline-block; padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; color: #fff; }
        .bg-success { background-color: #198754; }
        .bg-warning { background-color: #ffc107; color: #000; }
        .bg-danger { background-color: #dc3545; }
        .footer { background-color: #eee; text-align: center; padding: 15px; font-size: 12px; color: #777; }
        .btn { display: inline-block; padding: 10px 20px; margin-top: 20px; background-color: #0d6efd; color: #fff; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Detalle de tu Pago</h1>
    </div>

    <div class="content">
        <p>Hola <strong>{{ $order->user->name }}</strong>,</p>

        @if($order->status == 'Pago completado')
            <p>Hemos recibido tu pago exitosamente. A continuación encontrarás los detalles de la transacción.</p>
        @elseif($order->status == 'Pago por verificar')
            <p>Hemos registrado tu solicitud de pago. Estamos validando la información (comprobante o efectivo) y te notificaremos pronto.</p>
        @else
            <p>Se ha registrado una actualización en tu orden.</p>
        @endif

        <table class="table-details">
            <tr>
                <th>Referencia de Orden</th>
                <td>#{{ $order->id }}</td>
            </tr>
            <tr>
                <th>Tratamiento</th>
                <td>{{ $order->contractedTreatment->treatment->name ?? 'Tratamiento' }}</td>
            </tr>
            <tr>
                <th>Descripción</th>
                <td>{{ $order->payment_description }}</td>
            </tr>
            <tr>
                <th>Método de Pago</th>
                <td>{{ $order->payment_method }}</td>
            </tr>
            <tr>
                <th>Estado</th>
                <td>
                    @if($order->status == 'Pago completado')
                        <span class="status-badge bg-success">Aprobado</span>
                    @elseif($order->status == 'Pago por verificar')
                        <span class="status-badge bg-warning">En Revisión</span>
                    @else
                        <span class="status-badge bg-danger">{{ $order->status }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Total Pagado</th>
                <td style="font-size: 18px; font-weight: bold;">
                    ${{ number_format($order->total, 0, ',', '.') }} COP
                </td>
            </tr>
        </table>

        <div style="text-align: center;">
            <a href="{{ route('client.schedule-appointment.index', ['contracted_treatment' => $order->contracted_treatment_id]) }}" class="btn">
                Ver mi Tratamiento y Agendar
            </a>
        </div>
    </div>

    <div class="footer">
        <p>Si tienes alguna duda, contáctanos a través de nuestros canales de atención.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
    </div>
</div>

</body>
</html>
