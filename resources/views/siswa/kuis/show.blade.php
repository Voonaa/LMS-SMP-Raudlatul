@extends('layouts.siswa')

@section('title', 'Kuis: ' . $kuis->judul . ' - Siswa')
@section('page_title', 'Mengerjakan Kuis')

@section('content')
<div class="p-4 lg:p-margin max-w-4xl mx-auto" x-data="kuisRunner()">
    <div class="mb-6 flex justify-between items-center bg-surface-container-lowest p-4 rounded-xl shadow-sm border border-surface-container sticky top-4 z-10">
        <div>
            <h1 class="text-xl font-bold text-on-surface">{{ $kuis->judul }}</h1>
            <p class="text-xs text-on-surface-variant">{{ $kuis->mata_pelajaran->nama_mapel ?? 'Umum' }}</p>
        </div>
        <div class="text-sm font-semibold text-on-surface-variant flex items-center gap-2">
            <span class="material-symbols-outlined text-tertiary-container">schedule</span>
            <span x-text="formatTime(durasi)"></span>
        </div>
    </div>

    <form action="{{ route('siswa.kuis.submit', $kuis->id) }}" method="POST" id="kuisForm">
        @csrf
        <input type="hidden" name="durasi" x-model="durasi">
        
        <div class="space-y-6">
            @foreach($kuis->soal as $index => $soal)
                <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container p-6">
                    <div class="flex gap-4 mb-4">
                        <span class="bg-primary-container text-on-primary-container w-8 h-8 rounded-full flex items-center justify-center font-bold flex-shrink-0">{{ $index + 1 }}</span>
                        <h3 class="text-lg font-semibold text-on-surface">{{ $soal->pertanyaan }}</h3>
                    </div>
                    
                    <div class="pl-12 space-y-3">
                        @foreach(['A' => $soal->opsi_a, 'B' => $soal->opsi_b, 'C' => $soal->opsi_c, 'D' => $soal->opsi_d] as $opsiKey => $opsiText)
                            <label class="flex items-start gap-3 p-3 rounded-lg border border-surface-container hover:bg-surface-container-low cursor-pointer transition-colors group">
                                <input type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $opsiKey }}" class="mt-1 text-primary focus:ring-primary" required>
                                <span class="font-medium text-on-surface-variant group-hover:text-on-surface transition-colors">{{ $opsiKey }}. {{ $opsiText }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="bg-primary text-on-primary px-8 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl hover:bg-primary-container hover:text-on-primary-container transition-all flex items-center gap-2">
                <span class="material-symbols-outlined">send</span> Kumpulkan Kuis
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('kuisRunner', () => ({
            durasi: 0,
            timer: null,
            init() {
                this.timer = setInterval(() => {
                    this.durasi++;
                }, 1000);
            },
            formatTime(seconds) {
                const m = Math.floor(seconds / 60).toString().padStart(2, '0');
                const s = (seconds % 60).toString().padStart(2, '0');
                return `${m}:${s}`;
            }
        }));
    });
</script>
@endsection
