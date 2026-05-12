@extends('layouts.siswa')
@section('title', 'Kuis Diagnostik - LMS Raudlatul Hikmah')
@section('page_title', 'Kuis Diagnostik')

@section('content')
<div
    x-data="{
        currentSection: 0,
        totalSections: 5,
        submitting: false,
        done: false,
        result: null,
        jawaban: {},
        mapelColors: {
            1: 'bg-emerald-100 text-emerald-800 border-emerald-300',
            2: 'bg-sky-100 text-sky-800 border-sky-300',
            3: 'bg-amber-100 text-amber-800 border-amber-300',
            4: 'bg-purple-100 text-purple-800 border-purple-300',
            5: 'bg-rose-100 text-rose-800 border-rose-300',
        },
        get progress() { return Math.round((Object.keys(this.jawaban).length / {{ $kuis->soal->count() }}) * 100); },
        selectJawaban(soalId, opsi) {
            this.jawaban[soalId] = opsi;
        },
        async submitKuis() {
            if (Object.keys(this.jawaban).length < {{ $kuis->soal->count() }}) {
                alert('Harap jawab semua soal sebelum mengumpulkan!');
                return;
            }
            this.submitting = true;
            const response = await fetch('{{ route('siswa.diagnostik.submit') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ jawaban: this.jawaban })
            });
            const data = await response.json();
            this.submitting = false;
            if (data.success) {
                this.result = data;
                this.done = true;
            } else {
                alert(data.message || 'Terjadi kesalahan.');
            }
        },
        goToDashboard() {
            window.location.href = '{{ route('siswa.dashboard') }}';
        }
    }"
    class="max-w-3xl mx-auto"
>

{{-- ===== HEADER ===== --}}
<div class="mb-6">
    <div class="flex items-start gap-4">
        <div class="w-14 h-14 bg-primary rounded-xl flex items-center justify-center text-on-primary flex-shrink-0">
            <span class="material-symbols-outlined text-3xl">quiz</span>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-on-surface">{{ $kuis->judul }}</h2>
            <p class="text-on-surface-variant mt-1 text-sm">{{ $kuis->deskripsi }}</p>
        </div>
    </div>

    {{-- Info Bar --}}
    <div class="mt-4 grid grid-cols-3 gap-3 text-center">
        <div class="bg-surface-container-lowest border border-surface-container rounded-xl p-3">
            <p class="text-2xl font-black text-primary">{{ $kuis->soal->count() }}</p>
            <p class="text-xs text-on-surface-variant">Total Soal</p>
        </div>
        <div class="bg-surface-container-lowest border border-surface-container rounded-xl p-3">
            <p class="text-2xl font-black text-tertiary">5</p>
            <p class="text-xs text-on-surface-variant">Mata Pelajaran</p>
        </div>
        <div class="bg-surface-container-lowest border border-surface-container rounded-xl p-3">
            <p class="text-2xl font-black text-on-surface">~15</p>
            <p class="text-xs text-on-surface-variant">Menit</p>
        </div>
    </div>
</div>

{{-- ===== PROGRESS BAR ===== --}}
<div class="mb-6 bg-surface-container-lowest rounded-xl border border-surface-container p-4">
    <div class="flex justify-between items-center mb-2">
        <span class="text-xs font-semibold text-on-surface-variant">Progress Jawaban</span>
        <span class="text-xs font-bold text-primary" x-text="Object.keys(jawaban).length + '/{{ $kuis->soal->count() }} soal'"></span>
    </div>
    <div class="w-full h-2.5 bg-surface-container rounded-full overflow-hidden">
        <div class="h-full bg-primary rounded-full transition-all duration-300"
             :style="`width: ${progress}%`"></div>
    </div>
</div>

{{-- ===== SOAL LIST ===== --}}
<template x-if="!done">
<div class="space-y-5">
    @php
        $mapelInfo = [
            1 => ['nama' => 'Matematika',      'icon' => 'calculate',  'color' => 'text-emerald-700 bg-emerald-50 border-emerald-200'],
            2 => ['nama' => 'IPA',             'icon' => 'science',    'color' => 'text-sky-700 bg-sky-50 border-sky-200'],
            3 => ['nama' => 'IPS',             'icon' => 'public',     'color' => 'text-amber-700 bg-amber-50 border-amber-200'],
            4 => ['nama' => 'Bahasa Indonesia','icon' => 'menu_book',  'color' => 'text-purple-700 bg-purple-50 border-purple-200'],
            5 => ['nama' => 'Bahasa Inggris',  'icon' => 'translate',  'color' => 'text-rose-700 bg-rose-50 border-rose-200'],
        ];
        $currentMapel = 0;
        $nomor = 0;
    @endphp

    @foreach($kuis->soal as $soal)
        @php
            $mapelId = $soal->mata_pelajaran_id ?? 1;
            $info    = $mapelInfo[$mapelId] ?? $mapelInfo[1];
            $nomor++;
            $showHeader = $currentMapel !== $mapelId;
            $currentMapel = $mapelId;
        @endphp

        {{-- Seksi header mapel baru --}}
        @if($showHeader)
        <div class="flex items-center gap-3 mt-6 mb-3 {{ $nomor > 1 ? 'pt-2 border-t border-surface-container' : '' }}">
            <div class="w-9 h-9 rounded-xl {{ $info['color'] }} border flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-lg">{{ $info['icon'] }}</span>
            </div>
            <h3 class="font-bold text-on-surface text-sm uppercase tracking-wider">{{ $info['nama'] }}</h3>
        </div>
        @endif

        {{-- Card Soal --}}
        <div class="bg-surface-container-lowest rounded-xl border border-surface-container p-5 shadow-sm transition-all"
             :class="jawaban[{{ $soal->id }}] ? 'border-primary/40 bg-primary-container/5' : ''"
        >
            <p class="text-sm font-semibold text-on-surface mb-4">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-surface-container text-xs font-black mr-2">{{ $nomor }}</span>
                {{ $soal->pertanyaan }}
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach(['A' => $soal->opsi_a, 'B' => $soal->opsi_b, 'C' => $soal->opsi_c, 'D' => $soal->opsi_d] as $opsi => $teks)
                <button
                    type="button"
                    @click="selectJawaban({{ $soal->id }}, '{{ $opsi }}')"
                    :class="jawaban[{{ $soal->id }}] === '{{ $opsi }}'
                        ? 'border-primary bg-primary text-on-primary shadow-md'
                        : 'border-outline-variant bg-surface hover:border-primary hover:bg-primary-container/10 text-on-surface'"
                    class="flex items-center gap-3 text-left px-4 py-3 rounded-xl border font-medium text-sm transition-all"
                >
                    <span class="flex-shrink-0 w-7 h-7 rounded-full border-2 flex items-center justify-center text-xs font-black transition-all"
                          :class="jawaban[{{ $soal->id }}] === '{{ $opsi }}'
                              ? 'border-on-primary text-on-primary'
                              : 'border-outline text-on-surface-variant'">
                        {{ $opsi }}
                    </span>
                    <span>{{ $teks }}</span>
                </button>
                @endforeach
            </div>
        </div>
    @endforeach

    {{-- Tombol Submit --}}
    <div class="sticky bottom-4 z-10">
        <button
            @click="submitKuis()"
            :disabled="submitting || Object.keys(jawaban).length < {{ $kuis->soal->count() }}"
            :class="Object.keys(jawaban).length < {{ $kuis->soal->count() }}
                ? 'bg-surface-container-high text-on-surface-variant cursor-not-allowed'
                : 'bg-primary hover:bg-primary/90 text-on-primary shadow-[0_4px_20px_0_rgba(0,105,72,0.35)] cursor-pointer'"
            class="w-full py-4 rounded-2xl font-black text-lg transition-all flex items-center justify-center gap-2"
        >
            <template x-if="submitting">
                <span class="flex items-center gap-2">
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Memproses...
                </span>
            </template>
            <template x-if="!submitting">
                <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined">send</span>
                    Kumpulkan Jawaban
                    <span class="text-sm font-normal opacity-75"
                          x-show="Object.keys(jawaban).length < {{ $kuis->soal->count() }}"
                          x-text="`(${Object.keys(jawaban).length}/{{ $kuis->soal->count() }} dijawab)`"></span>
                </span>
            </template>
        </button>
    </div>
</div>
</template>

{{-- ===== HASIL / SUCCESS STATE ===== --}}
<template x-if="done && result">
<div class="text-center py-8">
    <div class="w-24 h-24 bg-primary-container rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
        <span class="material-symbols-outlined text-5xl text-on-primary-container">emoji_events</span>
    </div>

    <h3 class="text-2xl font-black text-on-surface mb-2">Kuis Selesai! 🎉</h3>
    <p class="text-on-surface-variant mb-6 text-sm" x-text="result.message"></p>

    {{-- Skor Total --}}
    <div class="inline-block bg-primary text-on-primary px-8 py-4 rounded-2xl shadow-lg mb-6">
        <p class="text-xs font-semibold opacity-80 uppercase tracking-wider">Nilai Total</p>
        <p class="text-5xl font-black" x-text="result.nilai_total + '/100'"></p>
    </div>

    {{-- Nilai per Mapel --}}
    <div class="grid grid-cols-5 gap-3 mb-6">
        @php
            $mapelBadge = [1 => ['Mtk','#006948'], 2 => ['IPA','#0277bd'], 3 => ['IPS','#e65100'], 4 => ['B.Ind','#6a1b9a'], 5 => ['B.Ing','#c62828']];
        @endphp
        @foreach($mapelBadge as $mid => $info)
        <div class="bg-surface-container-lowest rounded-xl border border-surface-container p-3 text-center">
            <div class="w-8 h-8 rounded-full mx-auto mb-1 flex items-center justify-center text-white text-xs font-bold"
                 style="background-color: {{ $info[1] }}">{{ $info[0] }}</div>
            <p class="text-lg font-black text-on-surface" x-text="(result.nilai_per_mapel[{{ $mid }}] ?? 0) + ''"></p>
            <p class="text-[10px] text-on-surface-variant">/ 100</p>
        </div>
        @endforeach
    </div>

    {{-- Rekomendasi --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-8 text-left">
        <p class="text-sm font-bold text-amber-800 flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-500">auto_awesome</span>
            Fokus Rekomendasi AI Kamu
        </p>
        <p class="text-sm text-amber-700 mt-1">
            Berdasarkan hasil diagnostik, sistem akan memprioritaskan materi
            <strong x-text="result.weakest_mapel"></strong>
            untuk rekomendasi awal kamu (nilai: <span x-text="result.nilai_terendah"></span>/100).
        </p>
    </div>

    <button @click="goToDashboard()"
        class="w-full py-4 bg-primary text-on-primary rounded-2xl font-black text-lg shadow-lg hover:bg-primary/90 transition-all flex items-center justify-center gap-2">
        <span class="material-symbols-outlined">rocket_launch</span>
        Mulai Belajar Sekarang!
    </button>
</div>
</template>

</div>
@endsection
