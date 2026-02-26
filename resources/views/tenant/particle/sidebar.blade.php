<div data-simplebar class="h-100">
  
  <div id="sidebar-menu">
    <ul class="metismenu list-unstyled" id="side-menu">
      <li class="menu-title">Menu</li>
      <li class="{{ Route::is('dashboard')  ? 'mm-active' : '' }}">
        <a href="{{ route('dashboard') }}" class="waves-effect {{ Route::is('dashboard')  ? 'active' : '' }}"><i class="mdi mdi-view-dashboard"></i><span>Dashboard</span></a>
      </li>
      <li class="{{ Route::is(['users.index','users.create','users.edit'])  ? 'mm-active' : '' }}">
        <a href="{{ route('users.index') }}" class="waves-effect {{ Route::is(['users.index','users.create','users.edit'])  ? 'active' : '' }}"><i class="fas fa-users"></i><span>User</span></a>
      </li>
    </ul>
  </div>

</div>
