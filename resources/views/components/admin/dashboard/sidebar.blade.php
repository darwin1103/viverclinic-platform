  <aside id="sidebar" class="sidebar">
    <div class="d-flex align-items-center mb-4">
      <a href="{{ url('/') }}" class="brand fs-5 text-decoration-none">
        <img src="{{ asset('/storage/viver-clinic-logo.png') }}" style="width: 100%; height: aunto;">
      </a>
    </div>
    <hr class="border-secondary-subtle">
    <ul class="nav nav-pills flex-column gap-1 mb-auto">
      <li>
        <a href="{{ route('home') }}" class="nav-link @if(Route::is('home')) active @endif">
          <i class="bi bi-speedometer2 me-2"></i>
            Dashboard
        </a>
      </li>
      <li class="mt-2 text-uppercase text-secondary small px-2">
        Operación
      </li>
      <li>
        <a href="#" class="nav-link">
            <i class="bi bi-calendar-week me-2"></i>
            Agenda
        </a>
      </li>
      <li>
        <a href="{{ route('users.index') }}" class="nav-link @if(Route::is('users.*')) active @endif">
            <i class="bi bi-people me-2"></i>
            {{ __('User Management') }}
        </a>
      </li>
      <li>
        <a href="#" class="nav-link">
            <i class="bi bi-cash-coin me-2"></i>
            Pagos
        </a>
      </li>
      <li>
        <a href="#" class="nav-link">
            <i class="bi bi-bag-check me-2"></i>
            Paquetes
        </a>
      </li>
      <li class="mt-3 text-uppercase text-secondary small px-2">
        Personas
      </li>
      <li>
        <a href="#" class="nav-link">
            <i class="bi bi-person-gear me-2"></i>
            Usuarios
        </a>
      </li>
      <li>
        <a href="{{ route('roles.index') }}" class="nav-link @if(Route::is('roles.*')) active @endif">
            <i class="bi bi-shield-lock me-2"></i>
            {{ __('Role Management') }}
        </a>
      </li>
      <li class="mt-3 text-uppercase text-secondary small px-2">
        Sucursales
      </li>
      <li>
        <a href="{{ route('branches.index') }}" class="nav-link @if(Route::is('branches.*')) active @endif">
            <i class="bi bi-building me-2"></i>
            {{ __('Branch Management') }}
        </a>
      </li>
      <li class="mt-3 text-uppercase text-secondary small px-2">
        Marketing
      </li>
      <li>
        <a href="#" class="nav-link">
            <i class="bi bi-megaphone me-2"></i>
            Promociones
        </a>
      </li>
      <li>
        <a href="#" class="nav-link">
            <i class="bi bi-send-check me-2"></i>
            Referidos
        </a>
      </li>
      <li class="mt-3 text-uppercase text-secondary small px-2">
        Reportes
      </li>
      <li>
        <a href="#" class="nav-link">
            <i class="bi bi-graph-up-arrow me-2"></i>
            Reportes
        </a>
      </li>
      <li class="mt-3 text-uppercase text-secondary small px-2">
        Configuración
      </li>
      <li>
        <a href="#" class="nav-link">
            <i class="bi bi-gear me-2"></i>
            Configuración
        </a>
      </li>
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
