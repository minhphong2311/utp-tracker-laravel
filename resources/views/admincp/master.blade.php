<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link id="favicon" rel="shortcut icon" href="{{ asset('/images/admin.png') }}" type="image/x-icon" />
  <title>UTP Timer Dashboard</title>

  <link rel="stylesheet" href="{{ asset('public/css/admincp.css') }}">
</head>

<body class="hold-transition sidebar-mini">
  <div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      {{-- <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
      </ul> --}}

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <!-- Sidebar user panel (optional) -->
          <div class="user-panel d-flex">
            {{-- <div class="image">
              <img src="https://adminlte.io/themes/v3/dist/img/user2-160x160.jpg" class="img-circle"
                alt="User Image">
            </div> --}}
            <div class="info">
              <a href="#" style="text-decoration: none;" class="d-block"><i class="fas fa-user"
                  aria-hidden="true"></i> {{ Auth::user()->name }}</a>
            </div>

          </div>
        </li>
        {{-- <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li> --}}
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="{{ url('/admincp') }}" class="brand-link" style="text-decoration: none;">
        <img src="{{ asset('/images/admin.png') }}" alt="Admin Logo" class="brand-image img-circle elevation-3"
          style="opacity: .8">
        <span class="brand-text font-weight-light">UTP Timer</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">


        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
            @include('admincp.menu')

          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      @yield('content')
    </div>
    <!-- /.content-wrapper -->


    <!-- Main Footer -->
    <footer class="main-footer">
      <div class="container-fluid text-center">
        <strong>Copyright &copy; <?php echo date('Y'); ?> UTP Timer.</strong> All rights reserved.
      </div>
    </footer>
  </div>
  <!-- ./wrapper -->

  <!-- REQUIRED SCRIPTS -->

  <!-- jQuery -->
  <script src="{{ asset('public/js/admincp.js') }}"></script>
  @yield('script')
</body>

</html>
