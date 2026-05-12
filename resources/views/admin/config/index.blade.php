@extends('layouts.admin')

@section('title', 'Konfigurasi Sistem - Admin')
@section('page_title', 'Konfigurasi Sistem')

@section('content')
<div class="mb-lg">
    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-primary hover:text-primary-container font-medium mb-4">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Kembali ke Dashboard
    </a>
    <h2 class="font-headline-xl text-headline-xl text-on-surface">Pengaturan Sistem</h2>
    <p class="font-body-lg text-body-lg text-on-surface-variant mt-2">Atur parameter global untuk sistem LMS.</p>
</div>

@if(session('success'))
    <div class="bg-primary-container text-on-primary-container p-4 mb-6 rounded-xl flex items-center gap-3 shadow-sm">
        <span class="material-symbols-outlined">check_circle</span>
        <p class="font-medium">{{ session('success') }}</p>
    </div>
@endif

<div class="bg-surface-container-lowest rounded-xl shadow-[0_2px_10px_0_rgba(0,105,72,0.05)] border border-surface-container p-6 max-w-2xl">
    <form action="{{ route('admin.config.save') }}" method="POST">
        @csrf
        <div class="mb-6">
            <label class="block text-sm font-semibold text-on-surface mb-2">Tahun Ajaran Aktif</label>
            <input type="text" name="tahun_ajaran" value="2025/2026" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary focus:border-primary text-on-surface" placeholder="Contoh: 2025/2026">
        </div>
        <div class="mb-6">
            <label class="block text-sm font-semibold text-on-surface mb-2">Batas Bobot Leaderboard Matematika (Multiplier)</label>
            <input type="number" step="0.1" name="math_multiplier" value="1.5" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary focus:border-primary text-on-surface" placeholder="Contoh: 1.5">
        </div>
        
        <div class="flex justify-end gap-3 mt-8">
            <button type="submit" class="px-6 py-3 bg-primary text-on-primary rounded-lg font-bold hover:bg-primary-container transition-colors shadow-sm">
                Simpan Konfigurasi
            </button>
        </div>
    </form>
</div>
@endsection
