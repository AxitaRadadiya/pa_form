
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
      {{-- <img src="" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> --}}
      <span class="brand-text font-weight-light"></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          
          
          <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ Route::is('dashboard')  ? 'active' : '' }}">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link {{ Route::is(['users.index','users.create','users.edit'])  ? 'active' : '' }}">
              <i class="nav-icon fas fa-users"></i>
              <p>
                User
              </p>
            </a>
          </li>

          {{-- @can('create-role')
          <li class="nav-item">
            <a href="{{ route('roles.index') }}" class="nav-link {{ Route::is('roles.*')  ? 'active' : '' }}">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Roles
              </p>
            </a>
          </li>
          @endcan

          @can('create-permission')
          <li class="nav-item">
            <a href="{{ route('permissions.index') }}" class="nav-link {{ Route::is('permissions.*')  ? 'active' : '' }}">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Permissions
              </p>
            </a>
          </li>
          @endcan   --}}        
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->