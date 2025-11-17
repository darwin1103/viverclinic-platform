@extends('layouts.admin')
@section('content')

<div class="container-fluid">

  <!-- KPIs (actualizados) -->
  <div class="row g-3">
    <!-- Citas -->
    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card kpi h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="text-secondary small">Citas de hoy</div>
              <div class="kpi-value mt-1">{{ $todayAppointments }}</div>
            </div>
            <i class="bi bi-calendar-week fs-3 text-info"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Ingreso diario -->
    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card kpi h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="text-secondary small">Ingreso diario</div>
              <div class="kpi-value mt-1">$1.450.000</div>
            </div>
            <i class="bi bi-cash-stack fs-3 text-info"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Egresos de hoy -->
    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card kpi h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="text-secondary small">Egresos de hoy</div>
              <div class="kpi-value mt-1">$520.000</div>
            </div>
            <i class="bi bi-receipt fs-3 text-info"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Nuevos pacientes -->
    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card kpi h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="text-secondary small">Nuevos pacientes (7d)</div>
              <div class="kpi-value mt-1">{{ $patientCount }}</div>
            </div>
            <i class="bi bi-person-plus fs-3 text-info"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- GRID PRINCIPAL -->
  <div class="row g-3 mt-1">
    <!-- Col Izquierda -->
    <div class="col-12 col-lg-7">
      <!-- Agenda del día -->
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div class="fw-semibold"><i class="bi bi-clock me-2"></i>Agenda del día</div>
          <div class="d-flex gap-2">
            <!-- Botón ancho para evitar salto -->
            <a href="{{ route('admin.appointments.index') }}" class="btn btn-sm btn-outline-light btn-wide">Ver agenda</a>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="list-group list-group-flush scroll-area">

            @if(count($todayAppointmentsList) > 0)

              @foreach($todayAppointmentsList as $appointment)
              <div class="list-group-item">
                <div class="d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center gap-3">
                    <div class="text-secondary small" style="width:100px"><i class="bi bi-clock"></i> {{ Illuminate\Support\Carbon::parse($appointment->schedule)->isoFormat('hh:mm a')}}</div>
                    <div>
                      <div class="fw-semibold">{{ $appointment->contractedTreatment->user->name }}</div>
                      <div class="fw-semibold">{{ $appointment->contractedTreatment->treatment->name }}</div>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <span class="chip"><i class="bi bi-circle-fill" style="font-size:.55rem"></i> {{ $appointment->status }}</span>
                  </div>
                </div>
              </div>
              @endforeach
            @else
              <div class="fw-semibold text-center my-3 h4">No hay citas para hoy</div>
            @endif
          </div>
        </div>
      </div>

      <!-- Actividad reciente -->
      <div class="card mt-3">
        <div class="card-header fw-semibold"><i class="bi bi-activity me-2"></i>Actividad reciente</div>
        <div class="card-body scroll-area">
          <ul class="list-group list-group-flush">
            <li class="list-group-item"><span class="text-secondary small">09:15</span> · María registró pago de <strong>$350.000</strong> a <a href="#" class="link-muted">Ana Ramírez</a>.</li>
            <li class="list-group-item"><span class="text-secondary small">09:03</span> · Se creó promoción <strong>“Control Dental -20%”</strong>.</li>
            <li class="list-group-item"><span class="text-secondary small">08:42</span> · <strong>Super Admin</strong> actualizó rol <em>Recepción</em>.</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Col Derecha -->
    <div class="col-12 col-lg-5">
      <!-- Alertas (ya sin “consentimientos”) -->
      <div class="card">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
          <span><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Alertas</span>
          <a href="#" class="link-muted small">Ver todo</a>
        </div>
        <div class="card-body">
          <div class="vstack gap-2">
            <div class="d-flex justify-content-between align-items-center p-2 rounded border border-warning-subtle">
              <div><strong>Pagos pendientes:</strong> 4 pacientes</div>
              <button class="btn btn-sm btn-outline-warning">Revisar</button>
            </div>
            <div class="d-flex justify-content-between align-items-center p-2 rounded border border-secondary">
              <div><strong>Reagendar no asistidos:</strong> 3 citas</div>
              <button class="btn btn-sm btn-outline-light">Abrir lista</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Ingreso diario por categoría (SIN gráfico) -->
      <div class="card mt-3">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
          <span><i class="bi bi-journal-richtext me-2"></i>Ingreso diario</span>
          <span class="badge text-bg-success">$1.450.000</span>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-dot text-info fs-3 m-0 p-0"></i>
              <span>Depilación láser</span>
            </div>
            <strong>$950.000</strong>
          </div>
          <div class="d-flex justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-dot text-info fs-3 m-0 p-0"></i>
              <span>Reducción</span>
            </div>
            <strong>$500.000</strong>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Tablas -->
  <div class="row g-3 mt-1">
    <div class="col-12 col-xl-8">
      <div class="card">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
          <span><i class="bi bi-receipt me-2"></i>Pagos recientes</span>
          <button class="btn btn-sm btn-outline-light">Exportar</button>
        </div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead><tr><th>Fecha</th><th>Paciente</th><th>Concepto</th><th>Monto</th><th>Método</th><th>Estado</th></tr></thead>
            <tbody>
              <tr><td>Hoy 09:15</td><td>Ana Ramírez</td><td>Control</td><td>$350.000</td><td>Tarjeta</td><td><span class="badge text-bg-success">Pagado</span></td></tr>
              <tr><td>Hoy 08:40</td><td>Kevin Mora</td><td>Paquete x4</td><td>$1.200.000</td><td>PSE</td><td><span class="badge text-bg-success">Pagado</span></td></tr>
              <tr><td>Ayer</td><td>Nora Silva</td><td>Rx</td><td>$90.000</td><td>Efectivo</td><td><span class="badge text-bg-warning">Pendiente</span></td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-12 col-xl-4">
      <div class="card h-100">
        <div class="card-header fw-semibold"><i class="bi bi-person-plus me-2"></i>Nuevos pacientes (7d)</div>
        <div class="card-body scroll-area">
          <ul class="list-group list-group-flush">

            @if(count($patientList) > 0)
              @foreach($patientList as $patient)
              <li class="list-group-item d-flex justify-content-between align-items-center"><span>{{ $patient->name }}</span></li>
              @endforeach
            @else
              <div class="fw-semibold text-center my-3 h4">No hay nuevos pacientes</div>
            @endif
          </ul>
        </div>
      </div>
    </div>
  </div>

</div>

  <!-- OFFCANVAS FILTROS -->
  <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasFiltros">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title">Filtros del dashboard</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body">
      <div class="vstack gap-3">
        <div>
          <label class="form-label">Rango de fechas</label>
          <div class="d-flex gap-2">
            <input type="date" class="form-control" />
            <input type="date" class="form-control" />
          </div>
        </div>
        <div>
          <label class="form-label">Profesional</label>
          <select class="form-select"><option>Todos</option><option>Dra. Pérez</option><option>Dr. Gómez</option></select>
        </div>
        <div>
          <label class="form-label">Sucursal</label>
          <select class="form-select"><option>Actual</option><option>Todas</option></select>
        </div>
        <div class="d-grid"><button class="btn btn-primary">Aplicar</button></div>
      </div>
    </div>
  </div>
@endsection
