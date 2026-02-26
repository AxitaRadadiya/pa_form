<div class="navbar-header">
  <!-- LOGO -->
  <div class="navbar-brand-box d-flex align-items-left">
      <a href="{{route('dashboard')}}" class="logo">
          <span>
              <img src="{{ asset('newAdmin/images/logo-light.png') }}" alt="" height="18">
          </span>
          <i>
              <img src="{{ asset('newAdmin/images/logo-small.png') }}" alt="" height="24">
          </i>
      </a>

      <button type="button" class="btn btn-sm mr-2 d-lg-none px-3 font-size-16 header-item waves-effect waves-light" id="vertical-menu-btn">
          <i class="fa fa-fw fa-bars"></i>
      </button>
  </div>

  <div class="d-flex align-items-center">

      <div class="dropdown d-inline-block">
          <button type="button" class="btn header-item noti-icon waves-effect waves-light" id="page-header-notifications-dropdown"
              data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="mdi mdi-bell"></i>
              {{-- <span class="badge badge-danger badge-pill">3</span> --}}
          </button>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-0"
              aria-labelledby="page-header-notifications-dropdown">
              <div class="p-3">
                  <div class="row align-items-center">
                      <div class="col">
                          <h6 class="m-0"> Notifications </h6>
                      </div>
                      <div class="col-auto">
                          <a href="#!" class="small"> View All</a>
                      </div>
                  </div>
              </div>
              <div data-simplebar style="max-height: 230px;">
                  <a href="" class="text-reset notification-item">
                      <div class="media">
                          <img src="{{ asset('newAdmin/images/users/avatar-2.jpg') }}"
                              class="mr-3 rounded-circle avatar-xs" alt="user-pic">
                          <div class="media-body">
                              <h6 class="mt-0 mb-1">Samuel Coverdale</h6>
                              <p class="font-size-12 mb-1">You have new follower on Instagram</p>
                              <p class="font-size-12 mb-0 text-muted"><i class="mdi mdi-clock-outline"></i> 2 min ago</p>
                          </div>
                      </div>
                  </a>
                  <a href="" class="text-reset notification-item">
                      <div class="media">
                          <div class="avatar-xs mr-3">
                              <span class="avatar-title bg-success rounded-circle">
                                  <i class="mdi mdi-cloud-download-outline"></i>
                              </span>
                          </div>
                          <div class="media-body">
                              <h6 class="mt-0 mb-1">Download Available !</h6>
                              <p class="font-size-12 mb-1">Latest version of admin is now available. Please download here.</p>
                              <p class="font-size-12 mb-0 text-muted"><i class="mdi mdi-clock-outline"></i> 4 hours ago</p>
                          </div>
                      </div>
                  </a>
                  <a href="" class="text-reset notification-item">
                      <div class="media">
                          <img src="{{ asset('newAdmin/images/users/avatar-3.jpg') }}"
                              class="mr-3 rounded-circle avatar-xs" alt="user-pic">
                          <div class="media-body">
                              <h6 class="mt-0 mb-1">Victoria Mendis</h6>
                              <p class="font-size-12 mb-1">Just upgraded to premium account.</p>
                              <p class="font-size-12 mb-0 text-muted"><i class="mdi mdi-clock-outline"></i> 1 day ago</p>
                          </div>
                      </div>
                  </a>
              </div>
              <div class="p-2 border-top">
                  <a class="btn btn-sm btn-light btn-block text-center" href="javascript:void(0)">
                      <i class="mdi mdi-arrow-down-circle mr-1"></i> Load More..
                  </a>
              </div>
          </div>
      </div>

      <div class="dropdown d-inline-block ml-2">
          <button type="button" class="btn header-item waves-effect waves-light" id="page-header-user-dropdown"
              data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <img class="rounded-circle header-profile-user" src="{{ asset('newAdmin/images/users/avatar-1.jpg') }}"
                  alt="Header Avatar">
              <span class="d-none d-sm-inline-block ml-1">{{ ucfirst (Auth()->user()->name) }}</span>
              <i class="mdi mdi-chevron-down d-none d-sm-inline-block"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-right">
              {{-- <a class="dropdown-item d-flex align-items-center justify-content-between" href="javascript:void(0)">
                  <span>Inbox</span>
                  <span>
                      <span class="badge badge-pill badge-info">3</span>
                  </span>
              </a> --}}
              @can('profile-edit')
              <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{route('admin.profile.edit')}}">
                  <span>Profile</span>
              </a>
              @endcan

              @canany(['general-settings-list', 'general-settings-view', 'general-settings-create', 'general-settings-edit', 'general-settings-delete'])

              <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route( 'general_settings.index') }}">
                  Settings
              </a>
              @endcanany

              @canany(['roles-list', 'roles-view', 'roles-create', 'roles-edit', 'roles-delete'])
                <a href="{{ route( 'roles.index') }}" class="dropdown-item d-flex align-items-center justify-content-between">
                  Roles
                </a>
              @endcanany

              @canany(['permissions-list', 'permissions-view', 'permissions-create', 'permissions-edit', 'permissions-delete'])
                <a href="{{ route( 'permissions.index') }}" class="dropdown-item d-flex align-items-center justify-content-between">
                  Permissions
                </a>
              @endcanany

              <form method="POST" action="{{ route('logout') }}">
                @csrf

                <a href="{{ route('logout') }}" class="dropdown-item d-flex align-items-center justify-content-between"
                        onclick="event.preventDefault();
                                    this.closest('form').submit();">
                    <span>Log Out</span>
                </a>
              </form>
          </div>
      </div>
      
  </div>
</div>