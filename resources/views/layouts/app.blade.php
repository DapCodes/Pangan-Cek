<!doctype html>
<html lang="id">

<head>
    <!-- ===== Meta dasar ===== -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF untuk AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'PanganCek Admin')</title>

    <!-- ===== Fonts & Styles eksternal ===== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- ====== Theme baru (diadopsi dari contoh) ====== -->
    <style>
        /* =========================================================
           TOKENS
           ========================================================= */
        :root {
            --primary: #ff7a59;
            --primary-2: #ffb86c;
            --secondary: #5f7cff;
            --accent: #2ed573;
            --accent-2: #00d2d3;
            --dark: #1f2d3d;
            --bg: #0f172a;
            --card: #0b1220;
            --border: rgba(255, 255, 255, .08);
            --ring: rgba(255, 122, 89, .35);
            --ring-2: rgba(95, 124, 255, .25);

            --control-bg: rgba(255, 255, 255, .03);
            --control-bg-hover: rgba(255, 255, 255, .05);
            --control-border: rgba(255, 255, 255, .12);
            --control-radius: 12px;
            --control-padding-y: .55rem;
            --control-padding-x: .9rem;
            --control-placeholder: #94a3b8;
            --control-text: #e5e7eb;
            --dropdown-bg: #0a1220;
            --dropdown-border: rgba(255, 255, 255, .08);
            --dropdown-item: #dbeafe;
            --dropdown-item-active-bg: rgba(95, 124, 255, .25);
            --dropdown-item-active-text: #ffffff;
        }

        /* =========================================================
           BASE
           ========================================================= */
        html,
        body {
            height: 100%;
            background:
                radial-gradient(1200px 1200px at -10% -10%, rgba(255, 122, 89, .18), transparent 50%),
                radial-gradient(900px 900px at 110% 0%, rgba(95, 124, 255, .20), transparent 55%),
                radial-gradient(900px 900px at 120% 120%, rgba(46, 213, 115, .15), transparent 55%),
                var(--bg);
            color: #eef2ff;
            font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            letter-spacing: .1px;
        }

        /* =========================================================
           NAVBAR
           ========================================================= */
        .navbar {
            background: linear-gradient(135deg, rgba(255, 122, 89, .95) 0%, rgba(255, 184, 108, .95) 50%, rgba(95, 124, 255, .95) 100%);
            box-shadow: 0 10px 30px rgba(255, 122, 89, .25), inset 0 -1px 0 rgba(255, 255, 255, .12);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, .12);
        }

        .navbar-brand {
            color: #fff !important;
            font-weight: 700;
            letter-spacing: .3px;
            display: flex;
            align-items: center;
            gap: .55rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, .25);
        }

        .navbar-brand img {
            height: 43px;
            width: auto;
            display: block;
        }

        .navbar .nav-link {
            color: #fff;
            font-weight: 600;
            opacity: .9;
            border-radius: 999px;
            padding: .45rem .9rem;
            transition: all .15s ease;
        }

        .navbar .nav-link:hover {
            opacity: 1;
            background: rgba(255, 255, 255, .12);
        }

        .navbar .nav-link.active {
            background: #fff;
            color: #0f172a;
            text-decoration: none;
        }

        .navbar .btn-pill {
            border-radius: 999px;
            padding: .45rem .9rem;
            font-weight: 600;
            box-shadow: 0 10px 20px rgba(0, 0, 0, .08);
        }

        .navbar-toggler {
            border: 0;
            color: #fff;
        }

        .navbar-toggler i {
            font-size: 1.5rem;
        }

        /* =========================================================
           LAYOUT & CARD
           ========================================================= */
        .container-xxl {
            padding-block: 18px;
        }

        .card {
            background: linear-gradient(180deg, rgba(255, 255, 255, .02), rgba(255, 255, 255, .01)) padding-box,
                linear-gradient(135deg, rgba(255, 122, 89, .5), rgba(95, 124, 255, .4)) border-box;
            border: 1px solid transparent;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .25), inset 0 0 0 1px rgba(255, 255, 255, .02);
            overflow: hidden;
            color: #e5e7eb;
        }

        .card-header {
            background: linear-gradient(180deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .02));
            border-bottom: 1px dashed var(--border);
            border-radius: 16px 16px 0 0 !important;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .85rem 1rem;
            color: #f8fafc;
        }

        /* Breadcrumb & title agar kontras */
        .page-title {
            font-weight: 700;
            color: #f8fafc;
        }

        .breadcrumb {
            --bs-breadcrumb-divider: '›';
        }

        .breadcrumb .breadcrumb-item a {
            color: #dbeafe;
        }

        .breadcrumb .breadcrumb-item.active {
            color: #ffffff;
        }

        /* Alerts */
        .alert {
            border-radius: 12px;
            border: 1px solid transparent;
            color: #0b1220;
            font-weight: 600;
        }

        .alert-success {
            background: linear-gradient(180deg, #87f5a2, #49e38b);
            box-shadow: 0 10px 20px rgba(46, 213, 115, .25);
        }

        .alert-danger {
            background: linear-gradient(180deg, #ff9aa2, #ff6b6b);
            box-shadow: 0 10px 20px rgba(255, 107, 107, .25);
        }

        /* Footer */
        footer {
            color: #cbd5e1;
        }

        /* Form controls (senada contoh) */
        .form-control,
        .form-select {
            background: var(--control-bg);
            border: 1px solid var(--control-border);
            color: var(--control-text);
            padding: var(--control-padding-y) var(--control-padding-x);
            border-radius: var(--control-radius);
            height: auto;
            transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
        }

        .form-control:hover,
        .form-select:hover {
            background: var(--control-bg-hover);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: transparent;
            box-shadow: 0 0 0 .2rem var(--ring), 0 0 0 .35rem var(--ring-2);
            background: var(--control-bg-hover);
            color: var(--control-text);
        }

        ::placeholder {
            color: var(--control-placeholder) !important;
        }
    </style>

    @stack('styles')
    {{-- @vite(['resources/css/app.css']) --}}
</head>

<body>
    <!-- ===== Ornamen halus ===== -->
    <div class="position-fixed w-100 h-100"
        style="pointer-events:none;inset:0;z-index:-1;opacity:.18;background-image:radial-gradient(#fff 1px,transparent 1px);background-size:18px 18px;">
    </div>

    <!-- ====== Topbar ====== -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-xxl">
            <!-- Logo + teks: tambahkan image public/1.svg -->
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('1.svg') }}" alt="Logo">
                <span>PanganCek <small class="fw-normal">Admin</small></span>
            </a>

            <!-- Toggler: pakai ikon sendiri agar selalu terlihat -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topnav"
                aria-controls="topnav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bx bx-menu"></i>
            </button>

            <div class="collapse navbar-collapse" id="topnav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                    <!-- Komoditas -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('commodities.index') ? 'active' : '' }}"
                            href="{{ route('admin.commodities.index') }}">
                            <i class="bx bx-package me-1"></i> Komoditas
                        </a>
                    </li>

                    <!-- Pelaporan / Verifikasi laporan -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.verify-reports.index') ? 'active' : '' }}"
                            href="{{ route('admin.verify-reports.index') }}">
                            <i class="bx bx-check-shield me-1"></i> Verifikasi Laporan
                        </a>
                    </li>

                    <!-- User Management -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}"
                            href="{{ route('admin.users.index') }}">
                            <i class="bx bx-user me-1"></i> User Management
                        </a>
                    </li>

                    <!-- Profile dropdown -->
                    <li class="nav-item dropdown ms-lg-2">
                        <a class="nav-link dropdown-toggle btn-pill" href="#" id="profileDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-user-circle me-1"></i>
                            {{ auth()->user()->name ?? 'Profil' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown"
                            style="background:var(--dropdown-bg); border:1px solid var(--dropdown-border);">
                            <li>
                                <h6 class="dropdown-header text-white-50">Masuk sebagai</h6>
                            </li>
                            <li>
                                <span class="dropdown-item text-white fw-semibold">
                                    <i class="bx bx-id-card me-2"></i>{{ auth()->user()->name ?? 'Pengguna' }}
                                </span>
                            </li>
                            <li>
                                <hr class="dropdown-divider" style="border-color:rgba(255,255,255,.15)">
                            </li>
                            <!-- Jika punya halaman profil, bisa arahkan ke route profil -->
                            {{-- <li><a class="dropdown-item text-white" href="{{ route('profile.show') }}"><i class="bx bx-cog me-2"></i> Pengaturan Profil</a></li>
                <li><hr class="dropdown-divider" style="border-color:rgba(255,255,255,.15)"></li> --}}
                            <li>
                                <!-- Tombol Logout (POST) -->
                                <form action="{{ route('logout') }}" method="POST" class="px-3">
                                    @csrf
                                    <button type="submit" class="btn btn-light w-100 fw-semibold">
                                        <i class="bx bx-log-out me-1"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ====== Header halaman (opsional breadcrumb) ====== -->
    @hasSection('page_header')
        <div class="container-xxl mt-3">
            @yield('page_header')
        </div>
    @endif

    <!-- ====== Konten utama ====== -->
    <main class="container-xxl my-3">
        <!-- Flash message -->
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
    <!-- ====== Script global ====== -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Setup CSRF untuk semua AJAX jQuery -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
    </script>

    @stack('scripts')
    {{-- @vite(['resources/js/app.js']) --}}
</body>

</html>
