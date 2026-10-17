<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'BMT Cinde Barokah') - BMT Cinde Barokah</title>
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/bmt-cinde-barokah.png') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/bmt-theme.css') }}">
  @stack('styles')
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    <aside class="left-sidebar">
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="{{ route('dashboard') }}" class="bmt-brand text-nowrap">
            <img src="{{ asset('assets/images/logos/bmt-cinde-barokah.png') }}" alt="Logo BMT Cinde Barokah">
            <span class="bmt-brand-name"><strong>BMT CINDE BAROKAH</strong><small>Koperasi Konsumen Cinde Barokah Sejahtera</small></span>
          </a>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse"><i class="ti ti-x fs-8"></i></div>
        </div>
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <li class="nav-small-cap"><i class="ti ti-dots nav-small-cap-icon fs-4"></i><span class="hide-menu">HOME</span></li>
            <li class="sidebar-item">
              <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <span><i class="ti ti-layout-dashboard"></i></span><span class="hide-menu">Dashboard</span>
              </a>
            </li>
            <li class="nav-small-cap"><i class="ti ti-dots nav-small-cap-icon fs-4"></i><span class="hide-menu">VIEW</span></li>
            <li class="sidebar-item">
              <a class="sidebar-link {{ request()->routeIs('penjualan.index', 'penjualan.master-data') ? 'active' : '' }}" href="{{ route('penjualan.index') }}">
                <span><i class="ti ti-shopping-cart"></i></span><span class="hide-menu">Penjualan (HJ)</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link {{ request()->routeIs('pembelian.index', 'pembelian.master-data') ? 'active' : '' }}" href="{{ route('pembelian.index') }}">
                <span><i class="ti ti-basket"></i></span><span class="hide-menu">Pembelian (HB)</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.laba-rugi') }}">
                <span><i class="ti ti-report-money"></i></span><span class="hide-menu">Laba Rugi (HB & HJ)</span>
              </a>
            </li>
            <li class="nav-small-cap"><i class="ti ti-dots nav-small-cap-icon fs-4"></i><span class="hide-menu">MASTER DATA</span></li>
            <li class="sidebar-item">
              <a class="sidebar-link {{ request()->routeIs('penjualan.create') ? 'active' : '' }}" href="{{ route('penjualan.create') }}">
                <span><i class="ti ti-plus"></i></span><span class="hide-menu">Create Penjualan</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link {{ request()->routeIs('pembelian.create') ? 'active' : '' }}" href="{{ route('pembelian.create') }}">
                <span><i class="ti ti-plus"></i></span><span class="hide-menu">Create Pembelian</span>
              </a>
            </li>
            <li class="nav-small-cap"><i class="ti ti-dots nav-small-cap-icon fs-4"></i><span class="hide-menu">AUTH</span></li>
            <li class="sidebar-item">
              <a class="sidebar-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
                <span><i class="ti ti-login"></i></span><span class="hide-menu">Login</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">
                <span><i class="ti ti-user-plus"></i></span><span class="hide-menu">Register</span>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </aside>
    <div class="body-wrapper">
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)"><i class="ti ti-menu-2"></i></a>
            </li>
          </ul>
          <div class="navbar-collapse justify-content-end px-0">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
              <li class="nav-item"><span class="nav-link">BMT Cinde Barokah</span></li>
              <li class="nav-item">
                <img src="{{ asset('assets/images/profile/user-1.jpg') }}" alt="Profil" width="35" height="35" class="rounded-circle">
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
          <h4 class="fw-semibold mb-0">@yield('page-title')</h4>
        </div>
        @if (session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif
        @if ($errors->any())
          <div class="alert alert-danger" role="alert">
            <div class="fw-semibold mb-1">Periksa kembali data yang dimasukkan:</div>
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        @yield('content')
      </div>
    </div>
  </div>
  <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/sidebarmenu.js') }}"></script>
  <script src="{{ asset('assets/js/app.min.js') }}"></script>
  @stack('scripts')
</body>
</html>
