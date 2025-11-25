@component('mail::message')
{{-- Saludo --}}
# Hola {{ $contractedTreatment->user->name }},

¡Gracias por tu confianza! Hemos confirmado la compra de tu tratamiento en **{{ $appName }}**. A continuación encontrarás el resumen de tu pedido.

@component('mail::panel')
## Resumen del Tratamiento

**Tratamiento:** {{ $contractedTreatment->treatment->name }}<br>
**Sesiones:** {{ $contractedTreatment->sessions }} sesiones<br>
**Frecuencia:** Cada {{ $contractedTreatment->days_between_sessions }} días

---

### Detalles de la compra

{{-- Listado de Paquetes --}}
@if(!empty($contractedTreatment->contracted_packages))
**Paquetes:**
@foreach($contractedTreatment->contracted_packages as $pkg)
- {{ $pkg['name'] }} (x{{ $pkg['quantity'] }}) - ${{ number_format($pkg['price_at_purchase'], 2) }}
@endforeach
<br>
@endif

{{-- Listado de Adicionales --}}
@if(!empty($contractedTreatment->contracted_additionals))
**Zonas Adicionales:**
@foreach($contractedTreatment->contracted_additionals as $add)
- {{ $add['name'] }} (x{{ $add['quantity'] }}) - ${{ number_format($add['price_at_purchase'], 2) }}
@endforeach
<br>
@endif

{{-- Zonas Seleccionadas (Big/Mini fusionadas visualmente) --}}
@php
    $zones = array_merge(
        $contractedTreatment->selected_zones['big'] ?? [],
        $contractedTreatment->selected_zones['mini'] ?? []
    );
@endphp

@if(count($zones) > 0)
**Zonas a tratar:**
@foreach($zones as $zone)
<span style="background-color: #f3f4f6; padding: 2px 8px; border-radius: 4px; font-size: 12px; margin-right: 4px; display:inline-block; margin-bottom: 4px;">{{ $zone }}</span>
@endforeach
<br><br>
@endif

**Total Pagado:** <span style="font-size: 1.2em; font-weight: bold;">${{ number_format($contractedTreatment->total_price, 2) }}</span>

@endcomponent

@component('mail::panel')
## Información de la Sucursal

@if($logoUrl)
<div style="text-align: center;">
    <img src="{{ $logoUrl }}" alt="Logo" style="max-width: 150px; display: inline-block; margin-bottom: 15px;">
</div>
@endif

{{-- Usamos una tabla simple para asegurar alineación en Outlook y Gmail --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td style="padding-bottom: 8px;">
<span style="font-size: 18px; margin-right: 8px;">🏢</span>
<span style="font-size: 14px; color: #333; font-weight: bold;">{{ $contractedTreatment->branch->name }}</span>
</td>
</tr>
<tr>
<td style="padding-bottom: 8px;">
<span style="font-size: 18px; margin-right: 8px;">📞</span>
<span style="font-size: 14px; color: #555;">{{ $contractedTreatment->branch->phone ?? 'No especificado' }}</span>
</td>
</tr>
<tr>
<td style="padding-bottom: 8px; vertical-align: top;">
<span style="font-size: 18px; margin-right: 8px;">📍</span>
<span style="font-size: 14px; color: #555;">{{ $contractedTreatment->branch->address }}</span>
</td>
</tr>
</table>

@if($contractedTreatment->branch->google_maps_url)
<div style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 10px;">
<a href="{{ $contractedTreatment->branch->google_maps_url }}" target="_blank" style="color: #3869d4; text-decoration: none; font-size: 14px; display: block;">
🗺️ Ver ubicación en Google Maps &rarr;
</a>
</div>
@endif
@endcomponent


El siguiente paso es agendar tu primera cita para comenzar tu tratamiento.

@component('mail::button', ['url' => route('client.schedule-appointment.index', ['contracted_treatment' => $contractedTreatment->id])])
Agendar mi Cita
@endcomponent

Gracias,<br>
El equipo de {{ $appName }}
@endcomponent
