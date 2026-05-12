@extends('layouts.siswa')

@section('title', 'Dashboard Siswa - SMP Islam Raudlatul Hikmah')
@section('page_title', 'Dashboard')

@section('content')
<div class="p-4 lg:p-margin max-w-7xl mx-auto space-y-gutter">
    <!-- Welcome Banner (Bento Style top) -->
    <section class="bg-primary text-on-primary rounded-xl p-8 relative overflow-hidden shadow-md flex items-center islamic-pattern-bg">
        <div class="absolute right-0 top-0 opacity-10 transform translate-x-1/4 -translate-y-1/4">
            <span class="material-symbols-outlined text-[200px]" data-weight="fill">local_library</span>
        </div>
        <div class="relative z-10 w-full md:w-2/3">
            <h2 class="font-headline-lg text-headline-lg mb-2">Assalamu'alaikum, {{ explode(' ', auth()->user()->name)[0] }}!</h2>
            <p class="font-body-lg text-body-lg text-on-primary-container mb-6">Selamat datang di LMS SMP Islam Raudlatul Hikmah. Mari kita lanjutkan pembelajaran hari ini dengan semangat dan ikhlas.</p>
            <a href="{{ route('siswa.materi.index') }}" class="bg-tertiary-container text-on-tertiary-container font-label-lg px-6 py-2 rounded-full hover:opacity-90 transition-opacity inline-flex items-center gap-2">
                <span class="material-symbols-outlined">play_circle</span>
                Lanjut Belajar
            </a>
        </div>
    </section>

    <!-- Grid Layout for Widgets -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter">
        <!-- AI Recommendation Widget -->
        <div class="lg:col-span-2 bg-surface-container-lowest rounded-xl p-6 ambient-shadow border-t-4 border-primary">
            <div class="flex items-center gap-2 mb-6">
                <span class="material-symbols-outlined text-tertiary-container">auto_awesome</span>
                <h3 class="font-headline-md text-headline-md text-on-surface">Rekomendasi Materi Untukmu</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($rekomendasi ?? [] as $materi)
                <a href="{{ route('siswa.materi.show', $materi->id) }}" class="bg-surface-container-low rounded-lg p-4 flex flex-col justify-between border border-transparent hover:border-primary-fixed transition-colors group">
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <span class="bg-primary-container text-on-primary-container text-xs px-2 py-1 rounded font-label-sm">{{ $materi->mata_pelajaran->nama_mapel ?? 'Mapel' }}</span>
                            <span class="material-symbols-outlined text-outline group-hover:text-primary transition-colors">arrow_forward</span>
                        </div>
                        <h4 class="font-label-lg text-label-lg text-on-surface mb-1">{{ $materi->judul }}</h4>
                        <p class="text-sm text-on-surface-variant line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($materi->konten), 60) }}</p>
                    </div>
                    <div class="mt-4 text-right">
                        <p class="text-xs text-on-surface-variant mt-1">Rekomendasi AI</p>
                    </div>
                </a>
                @empty
                <p class="text-sm text-on-surface-variant col-span-2">Belum ada rekomendasi materi saat ini.</p>
                @endforelse
            </div>
        </div>

        <!-- Gamification Widget: Leaderboard & Daily Streak -->
        <div class="flex flex-col gap-gutter">
            <!-- Daily Streak -->
            <div class="bg-surface-container-lowest rounded-xl p-6 ambient-shadow border-t-4 border-orange-500">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-orange-500" data-weight="fill">local_fire_department</span>
                    <h3 class="font-headline-md text-headline-md text-on-surface">Daily Streak</h3>
                </div>
                <div class="flex items-end gap-2">
                    <span class="text-4xl font-extrabold text-orange-600">{{ $streak ?? 0 }}</span>
                    <span class="text-on-surface-variant font-medium mb-1">Hari Berturut-turut</span>
                </div>
                <p class="text-xs text-on-surface-variant mt-2">Terus pertahankan semangat belajarmu!</p>
            </div>

            <!-- Leaderboard -->
            <div class="bg-surface-container-lowest rounded-xl p-6 ambient-shadow border-t-4 border-tertiary-container flex-1" x-data="{ leaderboard: [], init() { fetch('/api/leaderboard/{{ auth()->user()->kelas_id }}').then(res => res.json()).then(data => this.leaderboard = data) } }">
                <div class="flex items-center gap-2 mb-4 pb-2 border-b border-surface-container">
                    <span class="material-symbols-outlined text-tertiary-container" data-weight="fill">emoji_events</span>
                    <h3 class="font-headline-md text-headline-md text-on-surface">Peringkat Kelas Anda</h3>
                </div>
                <ul class="space-y-3">
                    <template x-for="siswa in leaderboard" :key="siswa.user_id">
                        <li class="flex items-center justify-between p-2 rounded transition-colors" :class="siswa.user_id == {{ auth()->id() }} ? 'bg-primary-container text-on-primary-container shadow-sm border border-primary' : 'hover:bg-surface-container-low'">
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-on-surface-variant w-4 text-center" x-text="siswa.rank"></span>
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm" :class="siswa.user_id == {{ auth()->id() }} ? 'bg-primary text-on-primary' : 'bg-surface-variant text-on-surface-variant'" x-text="siswa.name.substring(0,2).toUpperCase()"></div>
                                <span class="font-label-lg" :class="siswa.user_id == {{ auth()->id() }} ? '' : 'text-on-surface'" x-text="siswa.user_id == {{ auth()->id() }} ? 'Anda' : siswa.name"></span>
                            </div>
                            <span class="font-label-sm font-bold" :class="siswa.user_id == {{ auth()->id() }} ? '' : 'text-primary'" x-text="siswa.points + ' pts'"></span>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection