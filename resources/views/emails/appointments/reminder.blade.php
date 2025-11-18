@component('mail::message')
{{-- Saludo --}}
# Hola {{ $user->name }},

Te escribimos para recordarte tu próxima cita en **{{ $appName }}**. Es muy importante que confirmes tu asistencia para asegurar tu lugar.

@component('mail::panel')
## Detalles de tu Cita

**Tratamiento:** {{ $treatment->name }}<br>
**Sesión número:** {{ $appointment->session_number }}<br>
**Fecha:** {{ $appointment->schedule->format('d/m/Y') }}<br>
**Hora:** {{ $appointment->schedule->format('h:i A') }}
@endcomponent

@component('mail::panel')
## Información de la Sucursal

@if($branch->logo)
<img src="{{ asset('storage/' . $branch->logo) }}" alt="Logo de la sucursal" style="max-width: 150px; display: block; margin-bottom: 15px;">
@endif

<div style="display: flex; align-items: center; margin-bottom: 10px;">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
    <span style="margin-left: 10px;">{{ $branch->name }}</span>
</div>
<div style="display: flex; align-items: center;">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
    <span style="margin-left: 10px;">{{ $branch->address }}</span>
</div>
@if($branch->google_maps_url)
<a href="{{ $branch->google_maps_url }}" target="_blank" style="display: inline-block; margin-top: 15px;">Ver en Google Maps</a>
@endif
@endcomponent

Por favor, haz clic en el siguiente botón para gestionar tus citas y confirmar tu asistencia.

@component('mail::button', ['url' => $confirmationUrl])
Gestionar mis Citas
@endcomponent

**Importante:** Si no confirmas tu cita, esta podría ser marcada automáticamente como **'No asistida'** y perderías la sesión de tratamiento correspondiente.

Gracias,<br>
El equipo de {{ $appName }}
@endcomponent
