<header class="topbar-fixed">

  <button class="btn btn-outline-light d-lg-none btn-icon" id="btnToggleSidebar" aria-label="Abrir menú">
    <i class="bi bi-list"></i>
  </button>

  <span class="fs-5 fw-semibold d-none d-md-inline">
      ¡{{ __('Hello') }}, {{ Auth::user()->name }}!
  </span>

  <div class="ms-auto d-flex align-items-center gap-2">

  @role('SUPER_ADMIN|OWNER')
  @if($branches && $branches->count() > 0)
      <form id="global-branch-form" method="POST" action="{{ route('admin.switch-branch') }}" class="m-0">
          @csrf
          <select class="form-select form-select-sm" name="branch_id" id="global-branch-selector" style="min-width:220px" aria-label="Seleccionar sucursal" onchange="this.form.submit()">
              <option value="" @if(empty($selectedBranchID)) selected @endif>Todos</option>
              @foreach($branches as $branch)
                  <option value="{{ $branch->id }}" @if($selectedBranchID == $branch->id) selected @endif>{{ $branch->name }}</option>
              @endforeach
          </select>
      </form>
  @endif
  @endrole

    <!-- SOLO ICONO "+" -->
    <button class="btn btn-primary btn-sm btn-icon" aria-label="Crear" title="Crear" data-bs-toggle="modal" data-bs-target="#modalQuickAdd">
      <i class="bi bi-plus-lg"></i>
    </button>

    <span class="nav-item dropdown">
        <a id="navbarDropdown" class="nav-link fw-bold" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
            <img alt="photo profile" width="32px" height="32px" class="rounded-circle navbar-photo me-2" src="{{ Auth::user()->photo_profile ? asset(Storage::url(Auth::user()->photo_profile)) : asset('images/icons/default-avatar.svg') }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&size=32&background=6c757d&color=fff'">
        </a>
        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
            <a class="dropdown-item show-spinner" href="{{ route('profile.index') }}">
                <i class="bi bi-person-circle"></i>&nbsp;&nbsp;&nbsp;{{ __('Profile') }}
            </a>
            <a class="dropdown-item" href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-left"></i>&nbsp;&nbsp;&nbsp;{{ __('Logout') }}
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </span>

  </div>
</header>
