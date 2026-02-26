<div class="navbar-head" height="70px">
  <!-- LOGO -->
  <div class="navbar-brand-box d-flex align-items-left">
          <span>
              <img src="{{ asset('newAdmin/images/pa.png') }}" alt="" height="50">
          </span>
      </a>
      <button type="button" class="btn btn-sm mr-2 d-lg-none px-3 font-size-16 header-item waves-effect waves-dark" id="vertical-menu-btn">
          <i class="fa fa-fw fa-bars"></i>
      </button>
      <button type="button" class="btn btn-sm mr-2 d-desk-none px-3 font-size-16 header-item waves-effect waves-dark" id="vertical-btn">
          <i class="fa fa-fw fa-bars"></i>
      </button>
  </div>
    <div class="d-flex align-items-center">
            @auth
            <div class="dropdown d-inline-block ml-2">
                    <button type="button" class="btn header-item waves-effect waves-dark" id="page-header-user-dropdown"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="d-none d-sm-inline-block ml-1">
                                - {{ ucfirst(auth()->user()->name ?? '') }} {{ ucfirst(auth()->user()->last_name ?? '') }}
                        </span>
                            <i class="mdi mdi-chevron-down d-none d-sm-inline-block"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                    <!-- <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">
                      <span>Profile</span>
                  </a>-->
                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('password.change') }}">
                                    <span>Change Password</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <a href="{{ route('logout') }}" class="dropdown-item d-flex align-items-center justify-content-between"
                                                onclick="event.preventDefault();
                                                                        this.closest('form').submit();">
                                        <span class="text-danger">Log Out</span>
                                </a>
                            </form>
          </div>
      </div>
      @else
      <div class="ml-2">
          <a href="{{ route('login') }}" class="btn header-item">Login</a>
      </div>
      @endauth
  </div>
</div>
