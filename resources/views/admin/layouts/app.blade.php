<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>@yield('title')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta content="Innoveza CRM" name="description" />
  <meta content="MyraStudio" name="author" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- App favicon (fallback to pa.png when missing) -->
  <link rel="shortcut icon" href="{{ file_exists(public_path('newAdmin/images/solar-final.png')) ? asset('newAdmin/images/solar-final.png') : asset('newAdmin/images/pa.png') }}">

  {{-- css --}}
  @include('admin.particle.css')

</head>
<body>
<div class="layout-wrapper">

  <!-- Navbar -->
  <header id="page-topbar">
    @include('admin.particle.navbar')
  </header>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  {{-- @if(!Route::is('quatation.create', 'quatation.edit', 'quatation.show')) --}}
  <div class="vertical-menu">
    @include('admin.particle.sidebar')
  </div>
  {{-- @endif --}}

  <!-- Content Wrapper. Contains page content -->
    <div class="main-content">
    <div class="page-content">
      <div class="container-fluid">
        
        @php
            $routeName = Route::currentRouteName();
        @endphp
        @yield('content')
      </div>
    </div>
    
    <footer class="main-footer">
      @include('admin.particle.footer')
    </footer>
  </div>
  

</div>
<!-- ./wrapper -->

<!-- Overlay-->
<div class="menu-overlay"></div>

{{-- js --}}
@include('admin.particle.script')

{{-- page js --}}
@yield('pageScript')

<script>

  $(document).ready( function () {
      $("#vertical-btn").click(function(){
        $(".vertical-menu").toggleClass("collapsed");
        $(".main-content").toggleClass("expanded");
      });
  });
</script>

</body>
</html>
