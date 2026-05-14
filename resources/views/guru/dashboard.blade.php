@extends('layouts.guru')

@section('title', 'Dashboard Guru - Pengelolaan Materi')
@section('page_title', 'Pengelolaan Materi')

@section('content')
<div class="mb-lg">
    <h2 class="font-headline-xl text-headline-xl text-on-surface">Dashboard Analitik</h2>
    <p class="font-body-lg text-body-lg text-on-surface-variant mt-2">Pantau perkembangan siswa, aktivitas pembelajaran, dan tren diskusi.</p>
</div>

<!-- 1. Statistik Ringkas -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-[0_4px_20px_0_rgba(0,105,72,0.05)] border border-surface-container flex items-center gap-4">
        <div class="w-14 h-14 rounded-xl bg-primary-container flex items-center justify-center text-on-primary-container">
            <span class="material-symbols-outlined text-3xl">groups</span>
        </div>
        <div>
            <p class="text-sm font-semibold text-on-surface-variant mb-1">Total Siswa</p>
            <h3 class="text-3xl font-bold text-on-surface">{{ $totalSiswa ?? 0 }}</h3>
        </div>
    </div>
    
    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-[0_4px_20px_0_rgba(0,105,72,0.05)] border border-surface-container flex items-center gap-4">
        <div class="w-14 h-14 rounded-xl bg-tertiary-container flex items-center justify-center text-on-tertiary-container">
            <span class="material-symbols-outlined text-3xl">quiz</span>
        </div>
        <div>
            <p class="text-sm font-semibold text-on-surface-variant mb-1">Rata-rata Kuis</p>
            <h3 class="text-3xl font-bold text-on-surface">{{ number_format($rataRataKuis ?? 0, 1) }}</h3>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-[0_4px_20px_0_rgba(0,105,72,0.05)] border border-surface-container flex items-center gap-4">
        <div class="w-14 h-14 rounded-xl bg-[#d4af37]/20 flex items-center justify-center text-[#8f6d00]">
            <span class="material-symbols-outlined text-3xl">menu_book</span>
        </div>
        <div>
            <p class="text-sm font-semibold text-on-surface-variant mb-1">Penyelesaian Materi</p>
            <h3 class="text-3xl font-bold text-on-surface">{{ $persentasePenyelesaian ?? 0 }}%</h3>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- 2. Student at Risk -->
    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-[0_4px_20px_0_rgba(0,105,72,0.05)] border border-surface-container">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-headline-md text-lg font-bold text-error flex items-center gap-2">
                <span class="material-symbols-outlined">warning</span> Student at Risk
            </h3>
            <span class="text-xs text-on-surface-variant">Butuh perhatian ekstra</span>
        </div>
        
        <div class="space-y-4">
            @forelse($studentsAtRisk ?? [] as $student)
                <div class="flex items-center justify-between p-3 bg-error-container/20 rounded-xl border border-error/10">
                    <div class="flex items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=B3261E&color=fff" class="w-10 h-10 rounded-full">
                        <div>
                            <p class="font-semibold text-on-surface">{{ $student->name }}</p>
                            <p class="text-xs text-on-surface-variant">Kelas {{ $student->kelas->nama_kelas ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-error">Avg Skor: {{ number_format($student->avg_skor ?? 0, 1) }}</p>
                        <p class="text-xs text-on-surface-variant">{{ $student->log_aktivitas_count }} Aktivitas</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-on-surface-variant">
                    <span class="material-symbols-outlined text-4xl mb-2 text-primary">sentiment_satisfied</span>
                    <p>Semua siswa dalam kondisi baik!</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- 3. Grafik Aktivitas -->
    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-[0_4px_20px_0_rgba(0,105,72,0.05)] border border-surface-container flex flex-col">
        <h3 class="font-headline-md text-lg font-bold text-on-surface mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">insights</span> Aktivitas 7 Hari Terakhir
        </h3>
        
        <div class="flex-1 h-48">
            <canvas id="aktivitasChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- 4. Monitoring Forum -->
    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-[0_4px_20px_0_rgba(0,105,72,0.05)] border border-surface-container">
        <h3 class="font-headline-md text-lg font-bold text-on-surface mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined text-tertiary">forum</span> Topik Diskusi Terhangat
        </h3>
        
        <div class="space-y-4">
            @forelse($topForums ?? [] as $forum)
                <a href="{{ route('forum.index') }}" class="block p-4 bg-surface-container-low hover:bg-surface-container transition-colors rounded-xl border border-surface-container">
                    <h4 class="font-semibold text-on-surface line-clamp-1">{{ $forum->judul }}</h4>
                    <div class="flex justify-between mt-2 text-xs text-on-surface-variant">
                        <span>Oleh: {{ $forum->user->name ?? 'Anonim' }}</span>
                        <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">chat_bubble</span> {{ $forum->replies_count }} Balasan</span>
                    </div>
                </a>
            @empty
                <div class="text-center py-6 text-on-surface-variant">
                    <span class="material-symbols-outlined text-4xl mb-2">speaker_notes_off</span>
                    <p>Belum ada diskusi di forum.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- 5. Rekomendasi Terpopuler (Item-Based CF Preview) -->
    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-[0_4px_20px_0_rgba(0,105,72,0.05)] border border-surface-container">
        <h3 class="font-headline-md text-lg font-bold text-on-surface mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined text-[#d4af37]">hotel_class</span> Materi Paling Diminati
        </h3>
        
        <div class="space-y-4">
            @forelse($topMateri ?? [] as $index => $materi)
                <div class="flex items-center justify-between p-3 bg-surface-container-low rounded-xl border border-surface-container">
                    <div class="flex items-center gap-4">
                        <span class="font-black text-xl text-outline-variant w-4">{{ $index + 1 }}</span>
                        <p class="font-semibold text-on-surface line-clamp-1">{{ $materi['judul'] }}</p>
                    </div>
                    <span class="text-xs font-bold bg-primary-container text-on-primary-container px-2 py-1 rounded-full whitespace-nowrap">
                        {{ $materi['total'] }} View
                    </span>
                </div>
            @empty
                <div class="text-center py-6 text-on-surface-variant">
                    <span class="material-symbols-outlined text-4xl mb-2">library_books</span>
                    <p>Belum ada aktivitas membaca materi.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('aktivitasChart').getContext('2d');
        const labels = {!! json_encode(array_map(function($date) { return \Carbon\Carbon::parse($date)->format('d M'); }, array_keys($aktivitas7Hari))) !!};
        const data = {!! json_encode(array_values($aktivitas7Hari)) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Aktivitas',
                    data: data,
                    borderColor: '#006948', // Primary color
                    backgroundColor: 'rgba(0, 105, 72, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#006948',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#fff',
                        titleColor: '#1c1b1f',
                        bodyColor: '#1c1b1f',
                        borderColor: '#e0e0e0',
                        borderWidth: 1,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#49454f'
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#49454f'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endsection