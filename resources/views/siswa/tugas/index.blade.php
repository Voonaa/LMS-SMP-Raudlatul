@extends('layouts.siswa')

@section('title', 'Tugas - Siswa')
@section('page_title', 'Tugas & Pengumpulan')

@section('content')
<div class="p-4 lg:p-margin max-w-7xl mx-auto" x-data="{ activeId: null, loading: false }">
    <div class="mb-lg">
        <h2 class="font-headline-xl text-headline-xl text-on-surface">Tugas Aktif</h2>
        <p class="font-body-lg text-body-lg text-on-surface-variant mt-2">Kumpulkan tugas dari guru sebelum tenggat waktu.</p>
    </div>

    @if(session('success'))
        <div class="bg-primary-container text-on-primary-container p-4 mb-6 rounded-xl flex items-center gap-3">
            <span class="material-symbols-outlined">check_circle</span>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-error-container text-error p-4 mb-6 rounded-xl flex items-center gap-3">
            <span class="material-symbols-outlined">error</span>
            <p class="font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <div class="space-y-4">
        @forelse($tugas ?? [] as $t)
        @php $sudahKumpul = $t->pengumpulan->first(); @endphp
        <div class="bg-surface-container-lowest rounded-xl shadow-sm border {{ $sudahKumpul ? 'border-primary/30' : 'border-surface-container' }} p-6">
            <div class="flex flex-col md:flex-row justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-lg {{ $sudahKumpul ? 'bg-primary-container text-on-primary-container' : 'bg-surface-container text-on-surface-variant' }} flex items-center justify-center">
                            <span class="material-symbols-outlined" style="{{ $sudahKumpul ? 'font-variation-settings: \'FILL\' 1;' : '' }}">{{ $sudahKumpul ? 'task_alt' : 'assignment' }}</span>
                        </div>
                        <div>
                            <h3 class="font-headline-md text-on-surface font-bold">{{ $t->judul }}</h3>
                            <p class="text-xs text-on-surface-variant">{{ $t->mata_pelajaran->nama_mapel ?? '-' }} • Oleh: {{ $t->guru->name ?? '-' }}</p>
                        </div>
                    </div>
                    <p class="text-on-surface-variant text-sm line-clamp-2 ml-13">{{ $t->deskripsi }}</p>
                    @if($t->tenggat_waktu)
                    <p class="text-xs mt-2 ml-13 flex items-center gap-1 {{ now()->gt($t->tenggat_waktu) ? 'text-error' : 'text-on-surface-variant' }}">
                        <span class="material-symbols-outlined text-[14px]">schedule</span>
                        Tenggat: {{ $t->tenggat_waktu->format('d M Y, H:i') }}
                    </p>
                    @endif
                </div>
                <div class="flex flex-col items-start md:items-end gap-3 min-w-40">
                    @if($sudahKumpul)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-primary-container text-on-primary-container">
                            <span class="material-symbols-outlined text-[14px]">check_circle</span> Sudah Dikumpulkan
                        </span>
                    @else
                        <button @click="activeId = {{ $t->id }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg text-sm font-bold bg-primary text-on-primary hover:bg-primary-container transition-all">
                            <span class="material-symbols-outlined text-sm">upload</span> Kumpulkan
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Upload Modal per Tugas --}}
        @if(!$sudahKumpul)
        <div x-show="activeId == {{ $t->id }}" 
             x-cloak
             class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            
            <div class="bg-surface rounded-xl shadow-lg w-full max-w-md p-6" @click.outside="if(!loading) activeId = null">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-headline-md text-primary flex items-center gap-2">
                        <span class="material-symbols-outlined">upload</span> Kumpulkan Tugas
                    </h3>
                    <button type="button" @click="activeId = null" :disabled="loading" class="text-on-surface-variant hover:text-error">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <p class="text-sm text-on-surface-variant mb-6">Mata Pelajaran: <b>{{ $t->mata_pelajaran->nama_mapel ?? '-' }}</b><br>Tugas: <b>{{ $t->judul }}</b></p>
                
                <form action="{{ route('siswa.tugas.kumpulkan', $t->id) }}" method="POST" enctype="multipart/form-data" @submit="loading = true">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-on-surface mb-2">File Jawaban (PDF, DOCX, JPG, PNG, atau ZIP - Maks 20MB)</label>
                        <div class="relative">
                            <input type="file" name="file_jawaban" required 
                                   class="w-full border border-outline-variant bg-surface rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all" 
                                   accept=".pdf,.doc,.docx,.jpg,.png,.zip">
                        </div>
                    </div>
                    
                    <div class="flex gap-3 mt-8">
                        <button type="button" @click="activeId = null" :disabled="loading" class="flex-1 py-3 bg-surface-container text-on-surface rounded-xl font-bold hover:bg-surface-container-high transition-colors disabled:opacity-50">
                            Batal
                        </button>
                        <button type="submit" :disabled="loading" class="flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold hover:bg-primary-container transition-all flex items-center justify-center gap-2 disabled:opacity-50 shadow-md">
                            <span x-show="!loading" class="material-symbols-outlined text-sm">send</span>
                            <span x-show="loading" class="material-symbols-outlined animate-spin text-sm">sync</span>
                            <span x-text="loading ? 'Mengirim...' : 'Kumpulkan Sekarang'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
        @empty
        <div class="text-center py-12 bg-surface-container-lowest rounded-xl border border-surface-container">
            <span class="material-symbols-outlined text-5xl text-outline mb-3">assignment</span>
            <p class="text-on-surface-variant font-medium">Belum ada tugas aktif dari guru.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
