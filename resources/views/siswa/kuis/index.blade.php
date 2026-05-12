@extends('layouts.siswa')

@section('title', 'Daftar Kuis - Siswa')
@section('page_title', 'Tugas & Evaluasi')

@section('content')
<div class="p-4 lg:p-margin max-w-7xl mx-auto">
    <div class="mb-lg flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="font-headline-xl text-headline-xl text-on-surface">Daftar Kuis & Tugas</h2>
            <p class="font-body-lg text-body-lg text-on-surface-variant mt-2">Selesaikan kuis untuk mengukur pemahaman Anda dan dapatkan poin tambahan.</p>
        </div>
        
        <form action="{{ route('siswa.kuis.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
            <div class="relative flex-1 md:w-64">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline-variant">search</span>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full border border-outline-variant bg-surface rounded-lg pl-10 pr-4 py-2 focus:ring-primary focus:border-primary text-on-surface" placeholder="Cari kuis...">
            </div>
            <div class="md:w-48">
                <select name="mapel_id" onchange="this.form.submit()" class="w-full border border-outline-variant bg-surface rounded-lg px-3 py-2 focus:ring-primary focus:border-primary text-on-surface">
                    <option value="">Semua Mapel</option>
                    @foreach($mapelList ?? [] as $mapel)
                        <option value="{{ $mapel->id }}" {{ request('mapel_id') == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-primary text-on-primary px-4 py-2 rounded-lg font-bold hover:bg-primary-container transition-colors">Cari</button>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-primary-container text-on-primary-container p-4 mb-6 rounded-xl flex items-center gap-3 shadow-sm">
            <span class="material-symbols-outlined">check_circle</span>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-error-container text-error p-4 mb-6 rounded-xl flex items-center gap-3 shadow-sm">
            <span class="material-symbols-outlined">error</span>
            <p class="font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($kuis ?? [] as $k)
            <div class="bg-surface-container-lowest rounded-xl shadow-[0_2px_10px_0_rgba(0,105,72,0.05)] border border-surface-container p-6 flex flex-col hover:-translate-y-1 hover:shadow-md transition-all group">
                <div class="mb-4 flex justify-between items-center">
                    <span class="bg-primary-container text-on-primary-container px-3 py-1 rounded-full text-xs font-semibold">{{ $k->mata_pelajaran->nama_mapel ?? 'Umum' }}</span>
                    <span class="material-symbols-outlined text-outline group-hover:text-primary transition-colors">assignment</span>
                </div>
                <h2 class="text-xl font-bold text-on-surface mb-2 group-hover:text-primary transition-colors">{{ $k->judul }}</h2>
                <p class="text-on-surface-variant text-sm mb-4">{{ $k->deskripsi }}</p>
                
                @php
                    $hasil = \App\Models\HasilKuis::where('kuis_id', $k->id)->where('user_id', auth()->id())->first();
                @endphp

                <div class="mt-auto flex justify-between items-center pt-4 border-t border-surface-container">
                    @if($hasil)
                        <span class="text-sm font-bold text-primary flex items-center gap-1"><span class="material-symbols-outlined text-sm">stars</span> Nilai: {{ $hasil->nilai }}</span>
                        <span class="bg-surface-variant text-on-surface-variant px-3 py-1 rounded-lg text-sm font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-sm">check</span> Selesai</span>
                    @else
                        <span class="text-xs text-outline font-medium">Belum Dikerjakan</span>
                        <a href="{{ route('siswa.kuis.show', $k->id) }}" class="bg-gradient-to-r from-[#d4af37] to-[#f3e5ab] text-on-tertiary-fixed border border-[#cba72f] px-4 py-2 rounded-lg text-sm font-bold hover:shadow-md transition-all">Mulai Kuis</a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container">
                <span class="material-symbols-outlined text-4xl text-outline mb-3">task</span>
                <p class="text-on-surface-variant font-medium">Belum ada kuis tersedia di kelas Anda.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
