@extends('layouts.admin')

@section('title', 'Dashboard Admin - LMS Raudlatul Hikmah')
@section('page_title', 'Dashboard Admin')

@section('content')
<div class="mb-lg">
    <h2 class="font-headline-xl text-headline-xl text-on-surface">Selamat Datang, Admin</h2>
    <p class="font-body-lg text-body-lg text-on-surface-variant mt-2">Pusat kendali sistem LMS SMP Islam Raudlatul Hikmah.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <a href="{{ route('admin.user.index') }}" class="block group">
        <div class="bg-surface-container-lowest rounded-2xl shadow-[0_2px_10px_0_rgba(0,105,72,0.05)] border border-surface-container group-hover:border-primary transition-all p-6 flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-surface-container-low text-primary rounded-full flex items-center justify-center mb-4 group-hover:bg-primary-container/20">
                <span class="material-symbols-outlined text-3xl">manage_accounts</span>
            </div>
            <h2 class="text-xl font-bold text-on-surface mb-2">Kelola Pengguna</h2>
            <p class="text-on-surface-variant text-sm">Tambah, edit, dan hapus pengguna (Siswa & Guru) dalam sistem.</p>
        </div>
    </a>

    <a href="{{ route('admin.config.index') }}" class="block group">
        <div class="bg-surface-container-lowest rounded-2xl shadow-[0_2px_10px_0_rgba(0,105,72,0.05)] border border-surface-container group-hover:border-primary transition-all p-6 flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-surface-container-low text-primary rounded-full flex items-center justify-center mb-4 group-hover:bg-primary-container/20">
                <span class="material-symbols-outlined text-3xl">settings</span>
            </div>
            <h2 class="text-xl font-bold text-on-surface mb-2">Konfigurasi Sistem</h2>
            <p class="text-on-surface-variant text-sm">Atur parameter global seperti tahun ajaran dan bobot leaderboard.</p>
        </div>
    </a>

    <a href="{{ route('admin.testing.mae') }}" class="block group">
        <div class="bg-surface-container-lowest rounded-2xl shadow-[0_2px_10px_0_rgba(0,105,72,0.05)] border border-surface-container group-hover:border-primary transition-all p-6 flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-surface-container-low text-primary rounded-full flex items-center justify-center mb-4 group-hover:bg-primary-container/20">
                <span class="material-symbols-outlined text-3xl">analytics</span>
            </div>
            <h2 class="text-xl font-bold text-on-surface mb-2">Diagnosa AI (MAE)</h2>
            <p class="text-on-surface-variant text-sm">Validasi akurasi sistem rekomendasi Collaborative Filtering.</p>
        </div>
    </a>
</div>
@endsection
