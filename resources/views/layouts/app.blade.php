<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF untuk AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'PanganCek Admin')</title>

    {{-- ====== Styles global (Bootstrap + ikon) ====== --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    {{-- Tema kecil agar selaras (header oranye seperti screenshot) --}}
    <style>
        :root{
            --brand:#f97316; /* oranye */
            --bg:#f8fafc;
        }
        body{ background:var(--bg); }
        .navbar-brand{ font-weight:600; letter-spacing:.2px; }
        .navbar-brand small{font-weight:400; opacity:.9;}
        .navbar{ background:var(--brand); }
        .navbar .nav-link, .navbar .navbar-brand{ color:#fff; }
        .navbar .nav-link.active{ font-weight:600; text-decoration:underline; text-underline-offset:6px; }
        .card{ border:0; border-radius:16px; box-shadow:0 8px 24px rgba(0,0,0,.06);}
        .page-title{ font-weight:600; }
        .breadcrumb{ --bs-breadcrumb-divider: '›'; }
        footer{ color:#6b7280; }
    </style>

    {{-- Tempat inject CSS halaman tertentu (DataTables dll) --}}
    @stack('styles')
    {{-- Jika pakai Vite/laravel-mix, aktifkan baris ini dan taruh resource CSS kamu --}}
    {{-- @vite(['resources/css/app.css']) --}}
</head>
<body>
    {{-- ====== Topbar ====== --}}
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                PanganCek <small>Admin</small>
            </a>

            <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#topnav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="topnav">
                <ul class="navbar-nav ms-auto">
                    {{-- Sesuaikan route name agar item aktif --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.commodities.*') ? 'active':'' }}"
                           href="{{ route('admin.commodities.index') }}">
                           <i class="bx bx-package me-1"></i> Komoditas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.villages.*') ? 'active':'' }}"
                           href="{{ route('admin.villages.index') }}">
                           <i class="bx bx-map-pin me-1"></i> Desa/Kelurahan
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- ====== Header halaman (opsional breadcrumb) ====== --}}
    @hasSection('page_header')
    <div class="container mt-4">
        @yield('page_header')
    </div>
    @endif

    {{-- ====== Konten utama ====== --}}
    <main class="container my-4">
        {{-- Flash message standar --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- ====== Footer kecil ====== --}}
    <footer class="container mb-4">
        <div class="d-flex justify-content-between small">
            <span>© {{ date('Y') }} PanganCek</span>
            <span>Build with Laravel & Bootstrap</span>
        </div>
    </footer>

    {{-- ====== Script global (jQuery + Bootstrap) ====== --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Setup CSRF untuk semua AJAX jQuery --}}
    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
        });
    </script>

    {{-- Tempat inject script halaman tertentu (DataTables, chart, dll) --}}
    @stack('scripts')
    {{-- Jika pakai Vite, aktifkan baris ini --}}
    {{-- @vite(['resources/js/app.js']) --}}
</body>
</html>
