@extends('layouts.guru')

@section('title', 'Detail Kuis - Guru')
@section('page_title', 'Detail Kuis')

@section('content')
<div class="p-4 lg:p-margin max-w-5xl mx-auto" x-data="{ addSoalModal: false }">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('guru.kuis.index') }}" class="flex items-center gap-2 text-on-surface-variant hover:text-primary transition-colors font-bold">
            <span class="material-symbols-outlined">arrow_back</span>
            Kembali
        </a>
        <div class="flex gap-2">
            <button @click="addSoalModal = true" class="flex items-center gap-2 bg-primary text-on-primary px-4 py-2 rounded-lg font-bold hover:bg-primary-container transition-colors text-sm shadow-sm">
                <span class="material-symbols-outlined text-sm">add</span>
                Tambah Soal
            </button>
            <a href="{{ route('guru.kuis.edit', $kuis->id) }}" class="flex items-center gap-2 bg-surface-container text-primary border border-primary px-4 py-2 rounded-lg font-bold hover:bg-surface-container-high transition-colors text-sm">
                <span class="material-symbols-outlined text-sm">edit</span>
                Edit Kuis
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-primary-container text-on-primary-container p-4 mb-6 rounded-xl flex items-center gap-3 shadow-sm">
            <span class="material-symbols-outlined">check_circle</span>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-surface-container-lowest rounded-xl shadow-[0_2px_10px_0_rgba(0,105,72,0.05)] border border-surface-container p-6 mb-6">
        <div class="flex flex-wrap gap-2 mb-4">
            <span class="bg-tertiary-container text-on-tertiary-container text-xs px-2.5 py-0.5 rounded-full font-medium">Kuis</span>
            @if($kuis->materi)
                <span class="bg-primary-container text-on-primary-container text-xs px-2.5 py-0.5 rounded-full font-medium">Materi: {{ $kuis->materi->judul }}</span>
            @endif
        </div>
        <h1 class="font-headline-lg text-headline-lg text-on-surface mb-2">{{ $kuis->judul }}</h1>
        <p class="text-on-surface-variant mb-4">{{ $kuis->deskripsi }}</p>
        
        <div class="flex gap-4 text-sm text-outline">
            <span>Jumlah Soal: <strong>{{ $kuis->soal->count() }}</strong></span>
        </div>
    </div>

    <!-- Daftar Soal -->
    <div class="space-y-4">
        <h3 class="font-headline-md text-on-surface mb-4">Daftar Soal</h3>
        @forelse($kuis->soal as $index => $soal)
            <div class="bg-surface-container-lowest rounded-xl shadow-[0_2px_10px_0_rgba(0,105,72,0.05)] border border-surface-container p-6">
                <div class="flex justify-between items-start mb-4">
                    <span class="font-bold text-primary">Soal #{{ $index + 1 }}</span>
                </div>
                <p class="text-on-surface mb-4">{{ $soal->pertanyaan }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div class="p-3 rounded-lg {{ $soal->jawaban_benar == 'A' ? 'bg-primary-container text-on-primary-container font-bold' : 'bg-surface-container-low text-on-surface' }}">A. {{ $soal->opsi_a }}</div>
                    <div class="p-3 rounded-lg {{ $soal->jawaban_benar == 'B' ? 'bg-primary-container text-on-primary-container font-bold' : 'bg-surface-container-low text-on-surface' }}">B. {{ $soal->opsi_b }}</div>
                    <div class="p-3 rounded-lg {{ $soal->jawaban_benar == 'C' ? 'bg-primary-container text-on-primary-container font-bold' : 'bg-surface-container-low text-on-surface' }}">C. {{ $soal->opsi_c }}</div>
                    <div class="p-3 rounded-lg {{ $soal->jawaban_benar == 'D' ? 'bg-primary-container text-on-primary-container font-bold' : 'bg-surface-container-low text-on-surface' }}">D. {{ $soal->opsi_d }}</div>
                </div>
            </div>
        @empty
            <div class="text-center py-8 bg-surface-container-lowest rounded-xl border border-surface-container text-on-surface-variant">
                <span class="material-symbols-outlined text-4xl mb-2">assignment</span>
                <p>Belum ada soal untuk kuis ini.</p>
            </div>
        @endforelse
    </div>

    <!-- Modal Tambah Soal -->
    <div x-show="addSoalModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div @click.outside="addSoalModal = false" class="bg-surface rounded-xl shadow-xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-headline-md text-primary flex items-center gap-2"><span class="material-symbols-outlined text-tertiary-container">add</span> Tambah Soal Manual</h3>
                <button @click="addSoalModal = false" class="text-on-surface-variant hover:text-error font-bold"><span class="material-symbols-outlined">close</span></button>
            </div>
            
            <form action="{{ route('guru.kuis.soal.store', $kuis->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1">Pertanyaan</label>
                    <textarea name="pertanyaan" rows="3" class="w-full border border-outline-variant bg-surface rounded-lg p-2 focus:ring-primary focus:border-primary" required></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1">Opsi A</label>
                        <input type="text" name="opsi_a" class="w-full border border-outline-variant bg-surface rounded-lg p-2 focus:ring-primary focus:border-primary" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1">Opsi B</label>
                        <input type="text" name="opsi_b" class="w-full border border-outline-variant bg-surface rounded-lg p-2 focus:ring-primary focus:border-primary" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1">Opsi C</label>
                        <input type="text" name="opsi_c" class="w-full border border-outline-variant bg-surface rounded-lg p-2 focus:ring-primary focus:border-primary" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1">Opsi D</label>
                        <input type="text" name="opsi_d" class="w-full border border-outline-variant bg-surface rounded-lg p-2 focus:ring-primary focus:border-primary" required>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1">Jawaban Benar</label>
                    <select name="jawaban_benar" class="w-full border border-outline-variant bg-surface rounded-lg p-2 focus:ring-primary focus:border-primary" required>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                </div>
                
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="addSoalModal = false" class="px-4 py-2 bg-surface-container text-on-surface rounded-lg hover:bg-surface-container-high transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg hover:bg-primary-container transition-colors">Simpan Soal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
