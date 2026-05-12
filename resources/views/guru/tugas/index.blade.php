@extends('layouts.guru')

@section('title', 'Kelola Tugas - Guru')
@section('page_title', 'Kelola Tugas')

@section('content')
<div class="mb-lg flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="font-headline-xl text-headline-xl text-on-surface">Daftar Tugas</h2>
        <p class="font-body-lg text-body-lg text-on-surface-variant mt-2">Kelola tugas praktik dan esai untuk siswa Anda.</p>
    </div>
    <div class="flex gap-4">
        <a href="{{ route('guru.tugas.create') }}" class="flex items-center gap-2 bg-primary text-on-primary px-4 py-2 rounded-lg font-bold hover:bg-primary-container transition-colors shadow-sm">
            <span class="material-symbols-outlined">add</span>
            Buat Tugas Baru
        </a>
    </div>
</div>

@if(session('success'))
    <div class="bg-primary-container text-on-primary-container p-4 mb-6 rounded-xl flex items-center gap-3 shadow-sm">
        <span class="material-symbols-outlined">check_circle</span>
        <p class="font-medium">{{ session('success') }}</p>
    </div>
@endif

<div class="space-y-4">
    @forelse($tugas ?? [] as $t)
    <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container p-6 flex flex-col md:flex-row justify-between gap-4 hover:-translate-y-0.5 transition-all">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-lg bg-surface-container flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined">assignment</span>
                </div>
                <div>
                    <h3 class="font-headline-md text-on-surface font-bold">{{ $t->judul }}</h3>
                    <p class="text-xs text-on-surface-variant">{{ $t->mata_pelajaran->nama_mapel ?? '-' }} • Kelas {{ $t->kelas->nama_kelas ?? '-' }}</p>
                </div>
            </div>
            <p class="text-on-surface-variant text-sm ml-13 line-clamp-2">{{ $t->deskripsi }}</p>
        </div>
        <div class="flex flex-col md:items-end gap-3 min-w-40">
            @if($t->tenggat_waktu)
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold {{ now()->gt($t->tenggat_waktu) ? 'bg-error-container text-error' : 'bg-surface-container text-on-surface-variant' }}">
                    <span class="material-symbols-outlined text-[14px]">schedule</span>
                    {{ $t->tenggat_waktu->format('d M Y, H:i') }}
                </span>
            @endif
            <span class="text-sm font-bold text-primary">{{ $t->pengumpulan->count() }} Pengumpulan</span>
            <div class="flex gap-2">
                <a href="{{ route('guru.tugas.show', $t->id) }}" class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-surface-container rounded-md transition-colors" title="Lihat Pengumpulan">
                    <span class="material-symbols-outlined text-sm">visibility</span>
                </a>
                <a href="{{ route('guru.tugas.edit', $t->id) }}" class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-surface-container rounded-md transition-colors" title="Edit Tugas">
                    <span class="material-symbols-outlined text-sm">edit</span>
                </a>
                <form action="{{ route('guru.tugas.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus tugas ini?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container rounded-md transition-colors" title="Hapus">
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-12 bg-surface-container-lowest rounded-xl border border-surface-container">
        <span class="material-symbols-outlined text-5xl text-outline mb-3">assignment</span>
        <p class="text-on-surface-variant font-medium">Belum ada tugas. Buat tugas pertama Anda!</p>
        <a href="{{ route('guru.tugas.create') }}" class="mt-4 inline-flex items-center gap-2 bg-primary text-on-primary px-4 py-2 rounded-lg font-bold hover:bg-primary-container transition-colors text-sm">
            <span class="material-symbols-outlined text-sm">add</span> Buat Tugas Baru
        </a>
    </div>
    @endforelse
</div>
@endsection
