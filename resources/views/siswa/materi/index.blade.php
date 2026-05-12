@extends('layouts.siswa')

@section('title', 'Daftar Materi - Siswa')
@section('page_title', 'Materi Pelajaran')

@section('content')
<div class="p-4 lg:p-margin max-w-7xl mx-auto">
    <div class="mb-lg flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="font-headline-xl text-headline-xl text-on-surface">Daftar Materi</h2>
            <p class="font-body-lg text-body-lg text-on-surface-variant mt-2">Jelajahi dan pelajari materi yang telah disiapkan oleh guru-guru Anda.</p>
        </div>
        
        <form action="{{ route('siswa.materi.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
            <div class="relative flex-1 md:w-64">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline-variant">search</span>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full border border-outline-variant bg-surface rounded-lg pl-10 pr-4 py-2 focus:ring-primary focus:border-primary text-on-surface" placeholder="Cari materi...">
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

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($materis ?? [] as $materi)
            <div class="bg-surface-container-lowest rounded-xl shadow-[0_2px_10px_0_rgba(0,105,72,0.05)] border border-surface-container p-6 flex flex-col hover:-translate-y-1 hover:shadow-md transition-all group">
                <div class="mb-4 flex justify-between items-center">
                    <span class="bg-primary-container text-on-primary-container px-3 py-1 rounded-full text-xs font-semibold">{{ $materi->mata_pelajaran->nama_mapel ?? 'Umum' }}</span>
                    <span class="material-symbols-outlined text-outline group-hover:text-primary transition-colors">menu_book</span>
                </div>
                <h2 class="text-xl font-bold text-on-surface mb-2 group-hover:text-primary transition-colors">{{ $materi->judul }}</h2>
                <p class="text-on-surface-variant text-sm mb-4 line-clamp-2">{{ strip_tags($materi->konten) }}</p>
                <div class="mt-auto flex justify-between items-center pt-4 border-t border-surface-container">
                    <span class="text-xs text-outline font-medium">Oleh: {{ $materi->guru->name ?? 'Anonim' }}</span>
                    <a href="{{ route('siswa.materi.show', $materi->id) }}" class="text-primary font-bold text-sm hover:underline flex items-center gap-1">
                        Baca 
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container">
                <span class="material-symbols-outlined text-4xl text-outline mb-3">auto_stories</span>
                <p class="text-on-surface-variant font-medium">Belum ada materi tersedia di kelas Anda.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
