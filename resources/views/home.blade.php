@extends('layouts.app')

@section('title', 'Beranda Admin')

@push('styles')
<style>
  /* ====== Grid menu ala POS dengan tema sama ====== */
  .menu-hero{
    background:linear-gradient(180deg, rgba(255,255,255,.05), rgba(255,255,255,.02));
    border:1px dashed var(--border);
    border-radius:16px;
    padding:1rem 1.25rem;
    color:#e5e7eb;
    margin-bottom:1rem;
  }
  .menu-grid{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:18px;
  }
  @media (max-width: 992px){
    .menu-grid{grid-template-columns:repeat(2,minmax(0,1fr));}
  }
  @media (max-width: 576px){
    .menu-grid{grid-template-columns:1fr;}
  }

  /* Kartu menu klikable */
  .menu-card{
    position:relative;
    display:block;
    text-decoration:none;
    color:#e5e7eb;
    background:
      linear-gradient(180deg, rgba(255,255,255,.02), rgba(255,255,255,.01)) padding-box,
      linear-gradient(135deg, rgba(255,122,89,.40), rgba(95,124,255,.35)) border-box;
    border:1px solid transparent;
    border-radius:16px;
    box-shadow:0 10px 30px rgba(0,0,0,.25), inset 0 0 0 1px rgba(255,255,255,.02);
    padding:18px;
    min-height:140px;
    transition:transform .15s ease, box-shadow .2s ease, background .2s ease;
    outline: none;
  }
  .menu-card:hover{
    transform:translateY(-2px);
    box-shadow:0 16px 40px rgba(0,0,0,.32), inset 0 0 0 1px rgba(255,255,255,.03);
    background:
      linear-gradient(180deg, rgba(255,255,255,.035), rgba(255,255,255,.02)) padding-box,
      linear-gradient(135deg, rgba(255,122,89,.55), rgba(95,124,255,.48)) border-box;
    text-decoration:none;
  }
  .menu-card:focus{
    box-shadow:
      0 16px 40px rgba(0,0,0,.35),
      0 0 0 .2rem var(--ring),
      0 0 0 .35rem var(--ring-2),
      inset 0 0 0 1px rgba(255,255,255,.04);
  }
  .menu-icon{
    width:48px;height:48px;border-radius:12px;
    display:grid;place-items:center;
    background:rgba(255,255,255,.06);
    border:1px solid rgba(255,255,255,.10);
    box-shadow:inset 0 0 0 1px rgba(0,0,0,.15);
    margin-bottom:10px;
  }
  .menu-icon i{font-size:24px;}
  .menu-title{font-weight:700;letter-spacing:.2px;margin:0;color:#f8fafc;}
  .menu-desc{margin:.25rem 0 0;color:#cbd5e1;font-size:.95rem;}
  .menu-foot{
    position:absolute;right:14px;bottom:12px;
    font-size:.9rem;color:#dbeafe;display:flex;align-items:center;gap:6px;
    opacity:.9;
  }
</style>
@endpush

@section('content')
<div class="container-xxl">
  <!-- Headline ringkas -->
  <div class="menu-hero">
    <strong>Selamat datang, {{ auth()->user()->name ?? 'Admin' }}!</strong>
    <div class="small text-white-50">Pilih salah satu menu utama di bawah ini untuk mulai bekerja.</div>
  </div>

  <!-- Grid 3 menu ala dashboard POS -->
  <div class="menu-grid" role="list">
    <!-- Komoditas -->
    <a role="listitem" class="menu-card" href="{{ route('admin.commodities.index') }}" aria-label="Buka halaman Komoditas">
      <div class="menu-icon"><i class="bx bx-package"></i></div>
      <h3 class="menu-title">Komoditas</h3>
      <p class="menu-desc">Kelola data komoditas & satuan. Tambah, ubah, atau arsipkan.</p>
      <span class="menu-foot">Buka <i class="bx bx-right-arrow-alt"></i></span>
    </a>

    <!-- Verifikasi Laporan -->
    <a role="listitem" class="menu-card" href="{{ route('admin.verify-reports.index') }}" aria-label="Buka halaman Verifikasi Laporan">
      <div class="menu-icon"><i class="bx bx-check-shield"></i></div>
      <h3 class="menu-title">Verifikasi Laporan</h3>
      <p class="menu-desc">Tinjau & validasi laporan harga/kelangkaan dari lapangan.</p>
      <span class="menu-foot">Buka <i class="bx bx-right-arrow-alt"></i></span>
    </a>

    <!-- User Management -->
    <a role="listitem" class="menu-card" href="{{ route('admin.users.index') }}" aria-label="Buka halaman User Management">
      <div class="menu-icon"><i class="bx bx-user"></i></div>
      <h3 class="menu-title">User Management</h3>
      <p class="menu-desc">Atur peran & akses pengguna. Aktifkan/nonaktifkan akun.</p>
      <span class="menu-foot">Buka <i class="bx bx-right-arrow-alt"></i></span>
    </a>
  </div>
</div>
@endsection
