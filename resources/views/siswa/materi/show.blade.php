@extends('layouts.siswa')

@section('title', $materi->judul . ' - Siswa')
@section('page_title', 'Membaca Materi')

@section('content')
<div class="p-4 lg:p-margin max-w-4xl mx-auto" x-data="materiReader()">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('siswa.materi.index') }}" class="text-primary hover:underline font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Kembali ke Daftar Materi
        </a>
        <div class="text-sm font-semibold text-on-surface-variant bg-surface-container-high px-3 py-1 rounded-full flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">timer</span>
            <span x-text="formatTime(durasi)"></span>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-surface-container p-8 md:p-12">
        <div class="mb-8 border-b border-surface-container pb-6">
            <span class="bg-primary-container text-on-primary-container px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-4 inline-block">
                {{ $materi->mata_pelajaran->nama_mapel ?? 'Umum' }}
            </span>
            <h1 class="text-4xl font-extrabold text-on-surface mb-4">{{ $materi->judul }}</h1>
            <p class="text-on-surface-variant text-sm font-medium">Pengajar: {{ $materi->guru->name ?? 'Anonim' }} • Diperbarui pada {{ $materi->updated_at->format('d M Y') }}</p>
        </div>

        <div class="prose max-w-none text-on-surface leading-relaxed mb-12">
            {!! $materi->konten !!}
        </div>

        <div class="mt-8 pt-8 border-t border-surface-container flex justify-center">
            <button @click="selesaiMembaca()" x-bind:disabled="loading || isDone" class="bg-primary text-on-primary px-8 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all disabled:opacity-50 flex items-center gap-2">
                <span x-show="!loading && !isDone" class="flex items-center gap-2"><span class="material-symbols-outlined">check_circle</span> Selesai Membaca & Dapatkan Poin</span>
                <span x-show="loading" class="flex items-center gap-2"><span class="material-symbols-outlined animate-spin">sync</span> Menyimpan progres...</span>
                <span x-show="isDone" class="flex items-center gap-2"><span class="material-symbols-outlined">verified</span> Tersimpan! Poin ditambahkan.</span>
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('materiReader', () => ({
            durasi: 0,
            timer: null,
            loading: false,
            isDone: false,
            init() {
                this.timer = setInterval(() => {
                    if(!this.isDone) {
                        this.durasi++;
                    }
                }, 1000);
            },
            formatTime(seconds) {
                const m = Math.floor(seconds / 60).toString().padStart(2, '0');
                const s = (seconds % 60).toString().padStart(2, '0');
                return `${m}:${s}`;
            },
            async selesaiMembaca() {
                this.loading = true;
                try {
                    const response = await fetch('{{ route('siswa.materi.log', $materi->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ durasi: this.durasi })
                    });
                    const data = await response.json();
                    if(data.success) {
                        this.isDone = true;
                        clearInterval(this.timer);
                    }
                } catch(e) {
                    alert('Gagal menyimpan progres');
                } finally {
                    this.loading = false;
                }
            }
        }));
    });
</script>
@endsection
