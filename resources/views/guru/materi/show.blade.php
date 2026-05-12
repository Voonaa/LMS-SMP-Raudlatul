@extends('layouts.guru')

@section('title', 'Detail Materi - Guru')
@section('page_title', 'Detail Materi')

@section('content')
<div class="p-4 lg:p-margin max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('guru.materi.index') }}" class="flex items-center gap-2 text-on-surface-variant hover:text-primary transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
            Kembali
        </a>
        <div class="flex gap-2">
            <a href="{{ route('guru.materi.edit', $materi->id) }}" class="flex items-center gap-2 bg-surface-container text-primary border border-primary px-4 py-2 rounded-lg font-bold hover:bg-surface-container-high transition-colors text-sm">
                <span class="material-symbols-outlined text-sm">edit</span>
                Edit
            </a>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-xl shadow-[0_2px_10px_0_rgba(0,105,72,0.05)] border border-surface-container p-8">
        <div class="mb-6 pb-6 border-b border-surface-container">
            <div class="flex flex-wrap gap-2 mb-4">
                <span class="bg-primary-container text-on-primary-container text-xs px-2.5 py-0.5 rounded-full font-medium">{{ $materi->mata_pelajaran->nama_mapel ?? 'Mapel' }}</span>
                <span class="bg-surface-container-high text-on-surface text-xs px-2.5 py-0.5 rounded-full font-medium">{{ $materi->kelas->nama_kelas ?? 'Kelas' }}</span>
            </div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface mb-2">{{ $materi->judul }}</h1>
            <p class="text-sm text-on-surface-variant">Dibuat pada {{ $materi->created_at->format('d M Y H:i') }}</p>
        </div>

        <div class="prose max-w-none text-on-surface">
            {!! $materi->konten !!}
        </div>
    </div>
</div>
@endsection
