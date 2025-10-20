<div class="row justify-content-center my-2">
    <div class="col-12 mb-3">
        <div class="row gx-3 gy-3 my-2">
            @can('admin_dashboard_role_management')
                <div class="col-12 col-md-4 col-lg-3">
                    <a class="btn btn-custom btn-custom-height" href="{{ route('roles.index') }}" role="button">
                        {{ __('Role Management') }}
                    </a>
                </div>
            @endcan
            @can('admin_dashboard_user_management')
                <div class="col-12 col-md-4 col-lg-3">
                    <a class="btn btn-custom btn-custom-height" href="{{ route('users.index') }}" role="button">
                        {{ __('User Management') }}
                    </a>
                </div>
            @endcan
            @can('admin_dashboard_branch_management')
                <div class="col-12 col-md-4 col-lg-3">
                    <a class="btn btn-custom btn-custom-height" href="{{ route('branches.index') }}" role="button">
                        {{ __('Branch Management') }}
                    </a>
                </div>
            @endcan
        </div>
    </div>
</div>