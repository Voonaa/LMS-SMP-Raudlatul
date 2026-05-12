@extends('layouts.guru')

@section('title', 'Detail Tugas: ' . $tugas->judul)
@section('page_title', 'Detail Pengumpulan')

@section('content')
<div class="mb-lg flex justify-between items-center">
    <div>
        <h2 class="font-headline-xl text-headline-xl text-on-surface">{{ $tugas->judul }}</h2>
        <p class="text-on-surface-variant mt-1">{{ $tugas->mata_pelajaran->nama_mapel ?? '' }} • Kelas {{ $tugas->kelas->nama_kelas ?? '' }}</p>
    </div>
    <a href="{{ route('guru.tugas.index') }}" class="flex items-center gap-2 text-on-surface-variant hover:text-primary font-bold">
        <span class="material-symbols-outlined">arrow_back</span> Kembali
    </a>
</div>

<div class="bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container p-6 mb-8">
    <h3 class="font-bold text-on-surface mb-2">Instruksi Tugas:</h3>
    <p class="text-on-surface-variant">{{ $tugas->deskripsi }}</p>
    @if($tugas->file_lampiran)
    <div class="mt-4">
        <a href="{{ Storage::url($tugas->file_lampiran) }}" target="_blank" class="inline-flex items-center gap-2 text-primary hover:underline font-bold text-sm">
            <span class="material-symbols-outlined text-sm">download</span> Unduh Lampiran
        </a>
    </div>
    @endif
</div>

<div class="bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container overflow-hidden">
    <div class="p-4 border-b border-surface-container bg-surface flex items-center justify-between">
        <h3 class="font-headline-md text-on-surface">Daftar Pengumpulan</h3>
        <span class="text-sm font-bold text-primary">{{ $pengumpulan->count() }} siswa telah mengumpulkan</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-surface-container-low border-b border-surface-container text-xs uppercase text-on-surface-variant">
                <tr>
                    <th class="p-4">Nama Siswa</th>
                    <th class="p-4">Waktu Kumpul</th>
                    <th class="p-4">File Jawaban</th>
                    <th class="p-4 text-center">Nilai</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container">
                @forelse($pengumpulan as $p)
                <tr class="hover:bg-surface/50 transition-colors">
                    <td class="p-4 font-medium text-on-surface">{{ $p->user->name }}</td>
                    <td class="p-4 text-on-surface-variant text-sm">{{ $p->created_at->format('d M Y, H:i') }}</td>
                    <td class="p-4">
                        <a href="{{ Storage::url($p->file_jawaban) }}" target="_blank" class="inline-flex items-center gap-1 text-primary hover:underline text-sm font-bold">
                            <span class="material-symbols-outlined text-sm">download</span> Unduh
                        </a>
                    </td>
                    <td class="p-4 text-center" x-data="{ openGrade: false, score: '{{ $p->nilai ?? '' }}', comment: '{{ $p->komentar ?? '' }}' }">
                        @if($p->nilai)
                            <button @click="openGrade = true" class="text-primary font-bold hover:underline">
                                {{ $p->nilai }}
                            </button>
                        @else
                            <button @click="openGrade = true" class="bg-surface-container text-on-surface-variant px-3 py-1 rounded-lg text-xs font-bold hover:bg-primary hover:text-on-primary transition-colors">
                                Beri Nilai
                            </button>
                        @endif

                        {{-- Grading Modal --}}
                        <div x-show="openGrade" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 text-left">
                            <div class="bg-surface rounded-xl shadow-lg w-full max-w-md p-6" @click.outside="openGrade = false">
                                <h3 class="font-headline-md text-primary mb-4 flex items-center gap-2">
                                    <span class="material-symbols-outlined">grade</span> Penilaian Tugas
                                </h3>
                                <p class="text-sm text-on-surface-variant mb-4 font-medium">Siswa: {{ $p->user->name }}</p>
                                
                                <form action="{{ route('guru.tugas.nilai', $p->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-sm font-semibold text-on-surface mb-2">Skor (0-100)</label>
                                        <input type="number" name="nilai" x-model="score" min="0" max="100" required 
                                               class="w-full border border-outline-variant bg-surface rounded-lg p-3 text-sm focus:ring-primary focus:border-primary outline-none">
                                    </div>
                                    <div class="mb-6">
                                        <label class="block text-sm font-semibold text-on-surface mb-2">Komentar Guru (opsional)</label>
                                        <textarea name="komentar" x-model="comment" rows="3" 
                                                  class="w-full border border-outline-variant bg-surface rounded-lg p-3 text-sm focus:ring-primary outline-none" 
                                                  placeholder="Bagus! Pertahankan kerjamu."></textarea>
                                    </div>
                                    <div class="flex gap-3">
                                        <button type="button" @click="openGrade = false" class="flex-1 py-3 bg-surface-container text-on-surface rounded-xl font-bold">Batal</button>
                                        <button type="submit" class="flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold shadow-md hover:shadow-lg transition-all">Simpan Nilai</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-4 text-center text-on-surface-variant py-8">Belum ada siswa yang mengumpulkan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
