@extends('layouts.admin')
@section('title', 'Dashboard Admin - LMS Raudlatul Hikmah')
@section('page_title', 'Dashboard Admin')

@section('content')

{{-- ===== WELCOME HEADER ===== --}}
<div class="mb-8">
    <h2 class="text-2xl font-bold text-on-surface">Selamat Datang, Admin 👋</h2>
    <p class="text-on-surface-variant mt-1">Pusat kendali sistem LMS SMP Islam Raudlatul Hikmah.</p>
</div>

{{-- ===== STATS CARDS ===== --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    <div class="col-span-1 bg-surface-container-lowest rounded-2xl border border-surface-container p-5 shadow-sm flex flex-col items-center text-center gap-2">
        <div class="w-12 h-12 bg-primary-container rounded-xl flex items-center justify-center text-on-primary-container">
            <span class="material-symbols-outlined text-2xl">school</span>
        </div>
        <p class="text-2xl font-black text-on-surface">{{ $totalSiswa ?? 0 }}</p>
        <p class="text-xs font-semibold text-on-surface-variant">Siswa</p>
    </div>
    <div class="col-span-1 bg-surface-container-lowest rounded-2xl border border-surface-container p-5 shadow-sm flex flex-col items-center text-center gap-2">
        <div class="w-12 h-12 bg-tertiary-container rounded-xl flex items-center justify-center text-on-tertiary-container">
            <span class="material-symbols-outlined text-2xl">assignment_ind</span>
        </div>
        <p class="text-2xl font-black text-on-surface">{{ $totalGuru ?? 0 }}</p>
        <p class="text-xs font-semibold text-on-surface-variant">Guru</p>
    </div>
    <div class="col-span-1 bg-surface-container-lowest rounded-2xl border border-surface-container p-5 shadow-sm flex flex-col items-center text-center gap-2">
        <div class="w-12 h-12 bg-[#d4af37]/20 rounded-xl flex items-center justify-center text-[#8f6d00]">
            <span class="material-symbols-outlined text-2xl">menu_book</span>
        </div>
        <p class="text-2xl font-black text-on-surface">{{ $totalMateri ?? 0 }}</p>
        <p class="text-xs font-semibold text-on-surface-variant">Materi</p>
    </div>
    <div class="col-span-1 bg-surface-container-lowest rounded-2xl border border-surface-container p-5 shadow-sm flex flex-col items-center text-center gap-2">
        <div class="w-12 h-12 bg-primary-container/50 rounded-xl flex items-center justify-center text-on-primary-container">
            <span class="material-symbols-outlined text-2xl">quiz</span>
        </div>
        <p class="text-2xl font-black text-on-surface">{{ $totalKuis ?? 0 }}</p>
        <p class="text-xs font-semibold text-on-surface-variant">Kuis</p>
    </div>
    <div class="col-span-1 bg-surface-container-lowest rounded-2xl border border-surface-container p-5 shadow-sm flex flex-col items-center text-center gap-2">
        <div class="w-12 h-12 bg-surface-container-high rounded-xl flex items-center justify-center text-on-surface">
            <span class="material-symbols-outlined text-2xl">class</span>
        </div>
        <p class="text-2xl font-black text-on-surface">{{ $totalKelas ?? 0 }}</p>
        <p class="text-xs font-semibold text-on-surface-variant">Kelas</p>
    </div>
    <div class="col-span-1 bg-surface-container-lowest rounded-2xl border border-surface-container p-5 shadow-sm flex flex-col items-center text-center gap-2">
        <div class="w-12 h-12 bg-surface-container-high rounded-xl flex items-center justify-center text-on-surface">
            <span class="material-symbols-outlined text-2xl">history</span>
        </div>
        <p class="text-2xl font-black text-on-surface">{{ $totalAktivitas ?? 0 }}</p>
        <p class="text-xs font-semibold text-on-surface-variant">Log Aktivitas</p>
    </div>
</div>

{{-- ===== MENU TILES ===== --}}
<h3 class="text-base font-bold text-on-surface-variant mb-4 uppercase tracking-wider">Menu Cepat</h3>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

    <a href="{{ route('admin.user.index') }}" class="group block">
        <div class="bg-surface-container-lowest rounded-2xl border border-surface-container group-hover:border-primary group-hover:shadow-[0_4px_20px_0_rgba(0,105,72,0.12)] transition-all p-6 flex items-start gap-4">
            <div class="w-14 h-14 bg-primary-container rounded-xl flex items-center justify-center text-on-primary-container flex-shrink-0 group-hover:bg-primary group-hover:text-on-primary transition-colors">
                <span class="material-symbols-outlined text-3xl">manage_accounts</span>
            </div>
            <div>
                <h2 class="text-lg font-bold text-on-surface mb-1">Kelola Pengguna</h2>
                <p class="text-on-surface-variant text-sm">Tambah, edit, hapus, dan import massal siswa & guru.</p>
            </div>
        </div>
    </a>

    <a href="{{ route('admin.config.index') }}" class="group block">
        <div class="bg-surface-container-lowest rounded-2xl border border-surface-container group-hover:border-primary group-hover:shadow-[0_4px_20px_0_rgba(0,105,72,0.12)] transition-all p-6 flex items-start gap-4">
            <div class="w-14 h-14 bg-surface-container-high rounded-xl flex items-center justify-center text-on-surface flex-shrink-0 group-hover:bg-primary group-hover:text-on-primary transition-colors">
                <span class="material-symbols-outlined text-3xl">settings</span>
            </div>
            <div>
                <h2 class="text-lg font-bold text-on-surface mb-1">Konfigurasi Sistem</h2>
                <p class="text-on-surface-variant text-sm">Atur parameter global seperti tahun ajaran dan bobot leaderboard.</p>
            </div>
        </div>
    </a>

    <a href="{{ route('admin.testing.mae') }}" class="group block">
        <div class="bg-surface-container-lowest rounded-2xl border border-surface-container group-hover:border-[#d4af37] group-hover:shadow-[0_4px_20px_0_rgba(212,175,55,0.15)] transition-all p-6 flex items-start gap-4">
            <div class="w-14 h-14 bg-[#d4af37]/20 rounded-xl flex items-center justify-center text-[#8f6d00] flex-shrink-0 group-hover:bg-[#d4af37] group-hover:text-white transition-colors">
                <span class="material-symbols-outlined text-3xl">analytics</span>
            </div>
            <div>
                <h2 class="text-lg font-bold text-on-surface mb-1">Diagnosa AI (MAE)</h2>
                <p class="text-on-surface-variant text-sm">Validasi akurasi sistem rekomendasi Collaborative Filtering.</p>
            </div>
        </div>
    </a>

</div>
@endsection
