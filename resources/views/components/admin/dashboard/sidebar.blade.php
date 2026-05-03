  <aside id="sidebar" class="app-sidebar">
    <div class="d-flex align-items-center mb-4">
      <a href="{{ url('/') }}" class="brand fs-5 text-decoration-none">
        <img src="{{ asset('/storage/viver-clinic-logo.png') }}" style="width: 100%; height: aunto;">
      </a>
    </div>
    <hr class="border-secondary-subtle">
    <ul class="nav nav-pills flex-column gap-1 mb-auto">
      <li>
        <a href="{{ route('dashboard') }}" class="nav-link @if(Route::is('dashboard')) active @endif">
          <i class="bi bi-speedometer2 me-2"></i>
            Dashboard
        </a>
      </li>
      <li class="mt-2 text-uppercase text-secondary small px-2">
        Operación
      </li>
      <li>
        <a href="{{ route('admin.appointments.index') }}" class="nav-link @if(Route::is('admin.appointments.*')) active @endif">
            <i class="bi bi-calendar-week me-2"></i>
            Agenda
        </a>
      </li>
      <li>
        <a href="{{ route('admin.client.index') }}" class="nav-link @if(Route::is('admin.client.*')) active @endif">
            <i class="bi bi-people me-2"></i>
            Clientes
        </a>
      </li>
      @unlessrole('SALES')
      <li>
        <a href="{{ route('admin.payments.index') }}" class="nav-link @if(Route::is('admin.payments.*')) active @endif">
            <i class="bi bi-cash-coin me-2"></i>
            Pagos
        </a>
      </li>
      @role('SUPER_ADMIN|OWNER')
      <li>
        <a href="{{ route('admin.treatment.index') }}" class="nav-link @if(Route::is('admin.treatment.*')) active @endif">
            <i class="bi bi-bag-check me-2"></i>
            Tratamientos
        </a>
      </li>
      @endrole
      <li>
        <a href="{{ route('admin.contracted-treatment.index') }}" class="nav-link @if(Route::is('admin.contracted-treatment.*')) active @endif">
            <i class="bi bi-bag-heart me-2"></i>
            Paquetes contratados
        </a>
      </li>
      <li>
        <a href="{{ route('admin.products.index') }}" class="nav-link @if(Route::is('admin.products.*')) active @endif">
            <i class="bi bi-box me-2"></i>
            Productos
        </a>
      </li>
      <li>
        <a href="{{ route('admin.orders.index') }}" class="nav-link @if(Route::is('admin.orders.*') || Route::is('admin.manual-sales.*')) active @endif">
            <i class="bi bi-cart-fill me-2"></i>
            Venta de productos
        </a>
      </li>
      @endunlessrole
      @role('SUPER_ADMIN|OWNER')
      <li class="mt-3 text-uppercase text-secondary small px-2">
        Personal
      </li>
      <li>
        <a href="{{ route('admin.staff.index') }}" class="nav-link @if(Route::is('admin.staff.*')) active @endif">
            <i class="bi bi-person-fill-up me-2"></i>
            Trabajadores
        </a>
      </li>
      <li>
        <a href="{{ route('admin.owner.index') }}" class="nav-link @if(Route::is('admin.owner.*')) active @endif">
            <i class="bi bi-person-gear me-2"></i>
            Propietarios
        </a>
      </li>
      <li>
        <a href="{{ route('admin.admin-manager.index') }}" class="nav-link @if(Route::is('admin.admin-manager.*')) active @endif">
            <i class="bi bi-person-fill-gear me-2"></i>
            Administradores
        </a>
      </li>
      <li>
        <a href="{{ route('admin.sales-manager.index') }}" class="nav-link @if(Route::is('admin.sales-manager.*')) active @endif">
            <i class="bi bi-person-lines-fill me-2"></i>
            Ventas
        </a>
      </li>
      <li>
        <a href="{{ route('admin.role.index') }}" class="nav-link @if(Route::is('admin.roles.*')) active @endif">
            <i class="bi bi-shield-lock me-2"></i>
            {{ __('Role Management') }}
        </a>
      </li>
      @endrole
      @unlessrole('SALES')
      <li class="mt-3 text-uppercase text-secondary small px-2">
        Inventario y Activos
      </li>
      @role('SUPER_ADMIN|OWNER')
      <li>
        <a href="{{ route('admin.branch.index') }}" class="nav-link @if(Route::is('admin.branch.*')) active @endif">
            <i class="bi bi-building me-2"></i>
            {{ __('Branch Management') }}
        </a>
      </li>
      @endrole
      <li>
        <a href="{{ route('admin.assets.index') }}" class="nav-link @if(Route::is('admin.assets.*')) active @endif">
            <i class="bi bi-box-seam me-2"></i>
            Activos
        </a>
      </li>
      <li class="mt-3 text-uppercase text-secondary small px-2">
        Contenido
      </li>
      @role('SUPER_ADMIN|OWNER')
      <li>
        <a href="{{ route('admin.care-tips.index') }}" class="nav-link @if(Route::is('admin.care-tips.*')) active @endif">
            <i class="bi bi-heart-pulse me-2"></i>
            Tips de Cuidado
        </a>
      </li>
      <li>
        <a href="{{ route('admin.recomentations.index') }}" class="nav-link @if(Route::is('admin.recomentations.*')) active @endif">
            <i class="bi bi-star me-2"></i>
            Recomendaciones
        </a>
      </li>
      @endrole
      <li>
        <a href="{{ route('admin.trainings.index') }}" class="nav-link @if(Route::is('admin.trainings.*')) active @endif">
            <i class="bi bi-play-btn me-2"></i>
            Capacitaciones
        </a>
      </li>
      <li class="mt-3 text-uppercase text-secondary small px-2">
        Marketing
      </li>
      <li>
        <a href="{{ route('admin.promotions.index') }}" class="nav-link @if(Route::is('admin.promotions.*')) active @endif">
            <i class="bi bi-megaphone me-2"></i>
            Promociones
        </a>
      </li>
      <li>
        <a href="{{ route('admin.referrals.index') }}" class="nav-link @if(Route::is('admin.referrals.*')) active @endif">
            <i class="bi bi-send-check me-2"></i>
            Referidos
        </a>
      </li>
      <li class="mt-3 text-uppercase text-secondary small px-2">
        Finanzas
      </li>
      <li>
        <a href="{{ route('admin.accounting.index') }}" class="nav-link @if(Route::is('admin.accounting.*')) active @endif">
            <i class="bi bi-journal-text me-2"></i>
            Contabilidad
        </a>
      </li>
      @role('SUPER_ADMIN|OWNER')
      <li>
        <a href="{{ route('admin.payroll.index') }}" class="nav-link @if(Route::is('admin.payroll.*')) active @endif">
            <i class="bi bi-wallet2 me-2"></i>
            Liquidación
        </a>
      </li>
      @endrole
      <li class="mt-3 text-uppercase text-secondary small px-2">
        Reportes
      </li>
      <li>
        <a href="{{ route('admin.reports.index') }}" class="nav-link @if(Route::is('admin.reports.*')) active @endif">
            <i class="bi bi-graph-up me-2"></i>
            Reportes
        </a>
      </li>
      @endunlessrole
      @role('SUPER_ADMIN|OWNER')
      <li class="mt-3 text-uppercase text-secondary small px-2">
        Configuración
      </li>
      <li>
        <a href="{{ route('admin.settings.index') }}" class="nav-link @if(Route::is('admin.settings.*')) active @endif">
            <i class="bi bi-gear me-2"></i>
            Configuración
        </a>
      </li>
      @endrole
    </ul>
    <div class="mt-3">

      <a class="btn btn-sm btn-outline-light w-100" href="{{ route('logout') }}"
          onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
          <i class="bi bi-box-arrow-right me-2"></i>{{ __('Logout') }}
      </a>
      <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" class="d-none">
          @csrf
      </form>

    </div>
  </aside>

@push('scripts')
    <script type="text/javascript" src="{{ asset('js/admin/dashboard/sidebar-movil.js') }}"></script>
@endpush
