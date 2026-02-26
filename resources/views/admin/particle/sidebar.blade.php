<div data-simplebar class="h-100">
    <div id="sidebar-menu">
                <ul class="metismenu list-unstyled" id="side-menu">
            <li class="menu-title">Navigation</li>
                
                    <li class="{{ request()->routeIs('dashboard') || request()->routeIs('user.dashboard') ? 'mm-active' : '' }}">
                        @if(auth()->check() && auth()->user()->email === 'superadmin@gmail.com')
                            <a href="{{ route('dashboard') }}" class="waves-effect {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="mdi mdi-view-dashboard"></i><span>Dashboard</span></a>
                        @else
                            <a href="{{ route('user.dashboard') }}" class="waves-effect {{ request()->routeIs('user.dashboard') ? 'active' : '' }}"><i class="mdi mdi-view-dashboard"></i><span>Dashboard</span></a>
                        @endif
                    </li>
                    @if(auth()->check() && auth()->user()->email === 'superadmin@gmail.com')
                    <li class="{{ Route::is('users.*') || Route::is('superadmin.dashboard') ? 'mm-active' : '' }}">
                        <a href="{{ route('users.index') }}" class="waves-effect {{ Route::is('users.*') || Route::is('superadmin.dashboard') ? 'active' : '' }}"><i class="mdi mdi-account"></i><span>Partner</span></a>
                    </li>
                    
                    <li class="#">
                        <a href="javascript: void(0);" class="has-arrow waves-effect mm-active" aria-expanded="#"><i class="dripicons-gear"></i><span>Setting
                        </span></a>
                        <ul class="sub-menu mm-collapse" aria-expanded="false">
        
                        <li class="{{ Route::is('foods.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('foods.index') }}" class="waves-effect {{ Route::is('foods.*') ? 'active' : '' }}"><i class="mdi mdi-food"></i><span>Foods</span></a>
                        </li>
                        <li class="{{ Route::is('chapter.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('chapter.index') }}" class="waves-effect {{ Route::is('chapter.*') ? 'active' : '' }}"><i class="mdi mdi-book-open-page-variant"></i><span>Chapters</span></a>
                        </li>
                        <li class="{{ Route::is('relations.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('relations.index') }}" class="waves-effect {{ Route::is('relations.*') ? 'active' : '' }}"><i class="mdi mdi-account-group"></i><span>Relations</span></a>
                        </li>
                         <li class="{{ Route::is('events.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('events.index') }}" class="waves-effect {{ Route::is('events.*') ? 'active' : '' }}"><i class="mdi mdi-calendar"></i><span>Events</span></a>
                        </li>
                        </ul>
                    </li>
                    <li class="{{ Route::is('activity.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('activity.logs') }}" class="waves-effect {{ Route::is('activity.*') ? 'active' : '' }}"><i class="fab fa-wpforms"></i><span>Activity</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                            href="{{ route('reports.event') }}">
                            <i class="mdi mdi-chart-bar menu-icon"></i>
                                <span class="menu-title">Reports</span>
                         </a>
                    </li>
                    @endif
        </ul>
    </div>
</div>