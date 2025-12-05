<x-mail::message>
# Gracias por tu compra, {{ $order->user->name }}

Tu orden **#{{ $order->id }}** ha sido registrada exitosamente.

**Estado:** {{ $order->status }}
**Total:** ${{ number_format($order->total, 2) }}

## Detalles de la compra:

<x-mail::table>
| Producto       | Cant. | Precio Unit. | Subtotal  |
| :------------- |:-----:| :-----------:| :--------:|
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | ${{ number_format($item->unit_price, 2) }} | ${{ number_format($item->subtotal, 2) }} |
@endforeach
</x-mail::table>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
