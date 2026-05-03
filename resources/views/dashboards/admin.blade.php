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
              <div class="kpi-value mt-1">${{ number_format($ingresoDiario, 0, ',', '.') }}</div>
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
              <div class="kpi-value mt-1">${{ number_format($egresosHoy, 0, ',', '.') }}</div>
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
            @forelse($actividadReciente as $actividad)
            <li class="list-group-item">
              <span class="text-secondary small">{{ \Carbon\Carbon::parse($actividad->created_at)->format('H:i') }}</span> · 
              @if(($actividad->activity_type ?? '') === 'pago_producto')
                Venta de productos por 
              @else
                Pago de tratamiento por 
              @endif
              <strong>${{ number_format($actividad->total, 0, ',', '.') }}</strong> de 
              <a href="{{ $actividad->user ? route('admin.client.show', $actividad->user->id) : '#' }}" class="link-muted">
                {{ $actividad->user->name ?? 'Paciente' }}
              </a>.
            </li>
            @empty
            <li class="list-group-item text-center text-muted"><em>Aún no hay actividad registrada hoy.</em></li>
            @endforelse
          </ul>
        </div>
      </div>

      <!-- Gráfico de Citas -->
      <div class="card mt-3">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
          <span><i class="bi bi-graph-up text-info me-2"></i>Rendimiento de Citas (7 días)</span>
        </div>
        <div class="card-body">
          <div style="height: 250px;">
             <canvas id="appointmentsChart"></canvas>
          </div>
        </div>
      </div>
      <!-- Pagos recientes -->
      <div class="card mt-3">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
          <span><i class="bi bi-receipt me-2"></i>Pagos recientes</span>
          <a href="{{ route('admin.payments.export') }}" class="btn btn-sm btn-outline-light">Exportar</a>
        </div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead><tr><th>Fecha</th><th>Paciente</th><th>Concepto</th><th>Monto</th><th>Método</th><th>Estado</th></tr></thead>
            <tbody>
              @forelse($recentPayments as $payment)
                <tr>
                  <td>{{ \Carbon\Carbon::parse($payment->created_at)->diffForHumans() }}</td>
                  <td>{{ $payment->user->name ?? 'N/A' }}</td>
                  <td>{{ Str::limit($payment->contractedTreatment->treatment->name ?? $payment->payment_description, 20) }}</td>
                  <td>${{ number_format($payment->total, 0, ',', '.') }}</td>
                  <td>{{ $payment->payment_method }}</td>
                  <td>
                    @if(in_array($payment->status, ['Pago completado', 'Aprobado', 'Pagado', 'Paid']))
                      <span class="badge text-bg-success">{{ $payment->status }}</span>
                    @elseif(in_array($payment->status, ['Pago por verificar', 'Pendiente', 'Pending']))
                      <span class="badge text-bg-warning">{{ $payment->status }}</span>
                    @else
                      <span class="badge text-bg-secondary">{{ $payment->status }}</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center fw-semibold py-3">No hay pagos registrados</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Col Derecha -->
    <div class="col-12 col-lg-5">
      <!-- Alertas -->
      <div class="card">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
          <span><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Alertas</span>
          {{-- <a href="#" class="link-muted small">Ver todo</a> --}}
        </div>
        <div class="card-body">
          <div class="vstack gap-2">
            <div class="d-flex justify-content-between align-items-center p-2 rounded border border-warning-subtle">
              <div><strong>Pagos pendientes:</strong> {{ $pagosPendientesCount }} pacientes</div>
              <a href="{{ route('admin.payments.pending') }}" class="btn btn-sm btn-outline-warning">Revisar</a>
            </div>
            <div class="d-flex justify-content-between align-items-center p-2 rounded border border-secondary">
              <div><strong>Reagendar no asistidos:</strong> {{ $reagendarCount }} citas</div>
              <a href="{{ route('admin.appointments.reschedule-list') }}" class="btn btn-sm btn-outline-light">Abrir lista</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Ingreso diario por categoría -->
      <div class="card mt-3">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
          <span><i class="bi bi-journal-richtext me-2"></i>Ingreso diario por servicio</span>
          <span class="badge text-bg-success">${{ number_format($ingresoDiario, 0, ',', '.') }}</span>
        </div>
        <div class="card-body scroll-area" style="max-height: 250px;">
          @forelse($ingresosPorCategoria as $ingreso)
          <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-dot text-info fs-3 m-0 p-0"></i>
              <span>{{ $ingreso->category }}</span>
            </div>
            <strong>${{ number_format($ingreso->total, 0, ',', '.') }}</strong>
          </div>
          @empty
          <div class="text-muted text-center py-2">Sin ingresos registrados hoy</div>
          @endforelse
        </div>
      </div>

      <!-- Nuevos pacientes -->
      <div class="card mt-3">
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
      <form action="{{ route('dashboard') }}" method="GET" class="vstack gap-3">
        <div>
          <label class="form-label">Rango de fechas</label>
          <div class="d-flex gap-2">
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" />
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" />
          </div>
        </div>
        <div>
          <label class="form-label">Profesional</label>
          <select name="professional_id" class="form-select">
            <option value="">Todos</option>
            @foreach($professionals as $prof)
                <option value="{{ $prof->id }}" {{ request('professional_id') == $prof->id ? 'selected' : '' }}>{{ $prof->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="form-label">Sucursal</label>
          <select name="branch_id" class="form-select">
            <option value="">Todas</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="d-grid d-flex gap-2">
          <a href="{{ route('dashboard') }}" class="btn btn-secondary w-50">Limpiar</a>
          <button type="submit" class="btn btn-primary w-50">Aplicar</button>
        </div>
      </form>
    </div>
  </div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('appointmentsChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels ?? []) !!},
                datasets: [{
                    label: 'Citas agendadas',
                    data: {!! json_encode($chartValues ?? []) !!},
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { 
                        beginAtZero: true, 
                        ticks: { stepSize: 1 } 
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
});
</script>
@endpush

@endsection
