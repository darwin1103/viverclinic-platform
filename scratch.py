import sys

with open('resources/views/dashboards/admin.blade.php', 'r') as f:
    content = f.read()

pagos = """      <!-- Pagos recientes -->
      <div class="card mt-3 h-100">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
          <span><i class="bi bi-receipt me-2"></i>Pagos recientes</span>
          <button class="btn btn-sm btn-outline-light">Exportar</button>
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
"""

nuevos = """      <!-- Nuevos pacientes -->
      <div class="card mt-3 h-100">
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
"""

# 1. Insert Pagos at the end of Col Izquierda
content = content.replace('    </div>\n\n    <!-- Col Derecha -->', pagos + '    </div>\n\n    <!-- Col Derecha -->')

# 2. Insert Nuevos at the end of Col Derecha
content = content.replace('      </div>\n\n    </div>\n  </div>\n\n  <!-- Tablas -->', '      </div>\n\n' + nuevos + '    </div>\n  </div>\n\n  <!-- Tablas -->')

# 3. Delete the Tablas row
start_tablas = content.find('  <!-- Tablas -->')
end_tablas = content.find('  </div>\n\n</div>\n\n  <!-- OFFCANVAS FILTROS -->')
if start_tablas != -1 and end_tablas != -1:
    content = content[:start_tablas] + content[end_tablas+9:]

with open('resources/views/dashboards/admin.blade.php', 'w') as f:
    f.write(content)

print("Done")
