<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>@yield('title')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
  <meta content="MyraStudio" name="author" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />

  <!-- App favicon (fallback to pa.png when missing) -->
  <link rel="shortcut icon" href="{{ file_exists(public_path('newAdmin/images/favicon.ico')) ? asset('newAdmin/images/favicon.ico') : asset('newAdmin/images/pa.png') }}">

  {{-- css --}}
  @include('tenant.particle.css')

</head>
<body>
<div class="layout-wrapper">

  <!-- Navbar -->
  <header id="page-topbar">
    @include('tenant.particle.navbar')
  </header>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <div class="vertical-menu">
    @include('tenant.particle.sidebar')
  </div>

  <!-- Content Wrapper. Contains page content -->
  <div class="main-content">
    
    <div class="page-content">
      <div class="container-fluid">
        @yield('content')
      </div>
    </div>
    
    <footer class="main-footer">
      @include('tenant.particle.footer')
    </footer>
  </div>
  

</div>
<!-- ./wrapper -->

<!-- Overlay-->
<div class="menu-overlay"></div>

{{-- js --}}
@include('tenant.particle.script')

{{-- page js --}}
@yield('pageScript')

</body>
</html>
