@extends('layouts.admin')
@section('title', 'Evaluasi Algoritma - Admin LMS')
@section('page_title', 'Evaluasi Collaborative Filtering')

@section('content')

{{-- ============================================================
     HEADER
============================================================ --}}
<div class="mb-8">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-on-surface">Evaluasi Algoritma Collaborative Filtering</h2>
            <p class="text-on-surface-variant mt-1">
                Metode: <span class="font-semibold text-primary">Hybrid CF (User-Based, Item-Based, SVD)</span> ·
                Validasi: <span class="font-semibold text-primary">Leave-One-Out Cross Validation (LOO-CV)</span>
            </p>
        </div>
        <span class="hidden md:flex items-center gap-1.5 px-3 py-1.5 bg-primary-container text-on-primary-container rounded-full text-xs font-bold">
            <span class="material-symbols-outlined text-sm">science</span>
            Dokumen Skripsi – Bab 4
        </span>
    </div>
</div>

{{-- ============================================================
     CARDS RINGKASAN SKOR
============================================================ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 mb-8">

    {{-- MAE --}}
    <div class="lg:col-span-1 bg-gradient-to-br from-primary to-primary/80 rounded-2xl p-5 text-on-primary shadow-[0_4px_20px_0_rgba(0,105,72,0.25)] flex flex-col items-center text-center">
        <span class="material-symbols-outlined text-3xl mb-1 opacity-80">analytics</span>
        <p class="text-3xl font-black">{{ number_format($mae, 4) }}</p>
        <p class="text-xs font-semibold uppercase tracking-wider mt-1 opacity-80">Mean Absolute Error (MAE)</p>
        <p class="text-[10px] opacity-60 mt-1">Semakin kecil = semakin akurat</p>
    </div>

    {{-- Precision --}}
    <div class="lg:col-span-1 bg-surface-container-lowest rounded-2xl p-5 border border-surface-container shadow-sm flex flex-col items-center text-center">
        <span class="material-symbols-outlined text-3xl text-tertiary mb-1">verified</span>
        <p class="text-3xl font-black text-on-surface">{{ number_format($precision * 100, 1) }}<span class="text-lg">%</span></p>
        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider mt-1">Precision@10</p>
        <p class="text-[10px] text-outline mt-1">TP / (TP + FP)</p>
    </div>

    {{-- Recall --}}
    <div class="lg:col-span-1 bg-surface-container-lowest rounded-2xl p-5 border border-surface-container shadow-sm flex flex-col items-center text-center">
        <span class="material-symbols-outlined text-3xl text-[#d4af37] mb-1">target</span>
        <p class="text-3xl font-black text-on-surface">{{ number_format($recall * 100, 1) }}<span class="text-lg">%</span></p>
        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider mt-1">Recall@10</p>
        <p class="text-[10px] text-outline mt-1">TP / (TP + FN)</p>
    </div>

    {{-- F1-Score --}}
    <div class="lg:col-span-1 bg-surface-container-lowest rounded-2xl p-5 border border-surface-container shadow-sm flex flex-col items-center text-center">
        <span class="material-symbols-outlined text-3xl text-blue-500 mb-1">balance</span>
        <p class="text-3xl font-black text-on-surface">{{ number_format($f1Score * 100, 1) }}<span class="text-lg">%</span></p>
        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider mt-1">F1-Score@10</p>
        <p class="text-[10px] text-outline mt-1">Harmonic Mean P & R</p>
    </div>

    {{-- Total Data --}}
    <div class="lg:col-span-1 bg-surface-container-lowest rounded-2xl p-5 border border-surface-container shadow-sm flex flex-col items-center text-center">
        <span class="material-symbols-outlined text-3xl text-on-surface-variant mb-1">dataset</span>
        <p class="text-3xl font-black text-on-surface">{{ number_format($n) }}</p>
        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider mt-1">Total Data (N)</p>
        <p class="text-[10px] text-outline mt-1">Pasangan aktual–prediksi</p>
    </div>

    {{-- Total User --}}
    <div class="lg:col-span-1 bg-surface-container-lowest rounded-2xl p-5 border border-surface-container shadow-sm flex flex-col items-center text-center">
        <span class="material-symbols-outlined text-3xl text-on-surface-variant mb-1">groups</span>
        <p class="text-3xl font-black text-on-surface">{{ number_format($totalUsers) }}</p>
        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider mt-1">User Dievaluasi</p>
        <p class="text-[10px] text-outline mt-1">Memiliki ≥ 2 interaksi</p>
    </div>
</div>

{{-- ============================================================
     GRAFIK VISUALISASI (Chart.js)
============================================================ --}}
<div class="bg-surface-container-lowest rounded-2xl border border-surface-container shadow-sm p-6 mb-8">
    <div class="flex items-start justify-between mb-5 gap-4">
        <div>
            <h3 class="text-lg font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">bar_chart</span>
                Grafik Perbandingan Rating Aktual vs. Prediksi
            </h3>
            <p class="text-xs text-on-surface-variant mt-0.5">
                Menampilkan sampel 10 siswa representatif (aktif, sedang, pasif) · Rata-rata per siswa
            </p>
        </div>
        <div class="flex gap-4 text-xs text-on-surface-variant flex-shrink-0">
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-[#006948] inline-block"></span> Aktual
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-[#d4af37] inline-block"></span> Prediksi
            </span>
        </div>
    </div>
    <div class="relative" style="height:320px;">
        <canvas id="cfChart"></canvas>
    </div>
</div>

{{-- ============================================================
     TABEL SAMPEL REPRESENTATIF
============================================================ --}}
<div class="bg-surface-container-lowest rounded-2xl border border-surface-container shadow-sm overflow-hidden mb-6">

    <div class="px-6 py-4 border-b border-surface-container flex items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">table_view</span>
                Tabel Sampel Representatif
            </h3>
            <p class="text-xs text-on-surface-variant mt-0.5">
                10 siswa dipilih secara purposif mewakili kelompok <span class="text-error font-semibold">Aktif Tinggi</span>,
                <span class="text-tertiary font-semibold">Aktif Sedang</span>, dan
                <span class="text-on-surface-variant font-semibold">Pasif</span>
            </p>
        </div>
        <span class="flex-shrink-0 text-xs px-3 py-1 bg-primary-container text-on-primary-container rounded-full font-semibold">
            N sampel = {{ $sampleData->count() }}
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="bg-surface-container-low text-on-surface-variant uppercase text-[11px] tracking-wider font-semibold border-b border-outline-variant">
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Nama Siswa</th>
                    <th class="px-4 py-3 text-center">Total Interaksi</th>
                    <th class="px-4 py-3 text-center">Kategori</th>
                    <th class="px-4 py-3 text-center">Avg. Rating Aktual</th>
                    <th class="px-4 py-3 text-center">Avg. Prediksi Sistem</th>
                    <th class="px-4 py-3 text-center">Selisih (Error)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container">
                @php
                    $sortedSample = $sampleData->sortByDesc('total_interaksi')->values();
                    $maxInteraksi = $sortedSample->max('total_interaksi') ?: 1;
                    $minInteraksi = $sortedSample->min('total_interaksi') ?: 0;
                    $midThreshHigh = $minInteraksi + ($maxInteraksi - $minInteraksi) * 0.6;
                    $midThreshLow  = $minInteraksi + ($maxInteraksi - $minInteraksi) * 0.3;
                @endphp

                @foreach($sortedSample as $i => $row)
                @php
                    $interaksi = $row['total_interaksi'];
                    if ($interaksi >= $midThreshHigh) {
                        $kategori = 'Aktif Tinggi';
                        $katStyle = 'bg-error-container text-on-error-container';
                    } elseif ($interaksi >= $midThreshLow) {
                        $kategori = 'Aktif Sedang';
                        $katStyle = 'bg-tertiary-container text-on-tertiary-container';
                    } else {
                        $kategori = 'Pasif';
                        $katStyle = 'bg-surface-container-high text-on-surface-variant';
                    }
                    $error = $row['avg_error'];
                    $errorStyle = $error < 0.5 ? 'text-primary font-bold' : ($error < 1.5 ? 'text-tertiary font-semibold' : 'text-error font-semibold');
                @endphp
                <tr class="hover:bg-surface/40 transition-colors">
                    <td class="px-4 py-3 text-on-surface-variant font-mono">{{ $i + 1 }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($row['name']) }}&background=006948&color=fff&size=28"
                                 class="w-7 h-7 rounded-full flex-shrink-0" alt="">
                            <div>
                                <p class="font-semibold text-on-surface">{{ $row['name'] }}</p>
                                <p class="text-[11px] text-on-surface-variant font-mono">{{ $row['username'] }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="font-bold text-on-surface">{{ $interaksi }}</span>
                        <div class="mt-1 mx-auto w-16 h-1.5 bg-surface-container rounded-full overflow-hidden">
                            <div class="h-full bg-primary rounded-full" style="width: {{ min(100, ($interaksi / $maxInteraksi) * 100) }}%"></div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold {{ $katStyle }}">{{ $kategori }}</span>
                    </td>
                    <td class="px-4 py-3 text-center font-mono font-semibold text-on-surface">
                        {{ number_format($row['avg_actual'], 4) }}
                    </td>
                    <td class="px-4 py-3 text-center font-mono font-semibold text-on-surface">
                        {{ number_format($row['avg_predicted'], 4) }}
                    </td>
                    <td class="px-4 py-3 text-center font-mono {{ $errorStyle }}">
                        {{ number_format($error, 4) }}
                    </td>
                </tr>
                @endforeach

                @if($sampleData->isEmpty())
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-on-surface-variant">
                        <span class="material-symbols-outlined text-4xl block mb-2">sentiment_dissatisfied</span>
                        Data tidak cukup untuk evaluasi. Pastikan SyntheticDataSeeder sudah dijalankan.
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Footer Note --}}
    <div class="px-6 py-4 bg-surface-container-low border-t border-surface-container flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <p class="text-xs text-on-surface-variant flex items-start gap-1.5">
            <span class="material-symbols-outlined text-[15px] flex-shrink-0 mt-0.5 text-primary">info</span>
            Data lengkap <strong>{{ $totalUsers }} siswa</strong> yang dievaluasi dapat dilihat pada lampiran skripsi.
            Sampel di atas dipilih secara purposif untuk keterwakilan kategori keaktifan.
        </p>
        <a href="{{ route('admin.testing.mae.export') }}" 
           class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 bg-primary text-on-primary rounded-lg text-xs font-bold hover:bg-primary/90 transition-colors">
            <span class="material-symbols-outlined text-sm">download</span>
            Unduh Data Lengkap (CSV)
        </a>
    </div>
</div>

{{-- ============================================================
     KETERANGAN METODOLOGI
============================================================ --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-surface-container-lowest rounded-xl border border-surface-container p-4 shadow-sm">
        <h4 class="font-bold text-sm text-on-surface mb-2 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-primary text-base">calculate</span> Formula MAE
        </h4>
        <p class="text-xs text-on-surface-variant font-mono bg-surface-container p-2 rounded-lg">
            MAE = (1/N) &Sigma; |r<sub>ui</sub> &ndash; r&#770;<sub>ui</sub>|
        </p>
        <p class="text-[11px] text-on-surface-variant mt-2">
            r<sub>ui</sub> = rating aktual, r&#770;<sub>ui</sub> = rating prediksi sistem.
        </p>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-surface-container p-4 shadow-sm">
        <h4 class="font-bold text-sm text-on-surface mb-2 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-primary text-base">model_training</span> Metode Similaritas
        </h4>
        <p class="text-xs text-on-surface-variant font-mono bg-surface-container p-2 rounded-lg">
            sim(i,j) = (A&middot;B) / (&#8214;A&#8214;&middot;&#8214;B&#8214;)
        </p>
        <p class="text-[11px] text-on-surface-variant mt-2">
            Cosine Similarity antara vektor rating item i dan item j.
        </p>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-surface-container p-4 shadow-sm">
        <h4 class="font-bold text-sm text-on-surface mb-2 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-primary text-base">rule</span> Threshold Relevansi
        </h4>
        <p class="text-xs text-on-surface-variant font-mono bg-surface-container p-2 rounded-lg">
            &theta; = &mu; + 0.5&sigma; = {{ $threshold }}
        </p>
        <p class="text-[11px] text-on-surface-variant mt-2">
            Threshold <em>dinamis</em>: mean ({{ $meanRating }}) + &frac12; std. deviasi. Item relevan jika rating &ge; {{ $threshold }}.
        </p>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-surface-container p-4 shadow-sm">
        <h4 class="font-bold text-sm text-on-surface mb-2 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-primary text-base">science</span> Pembobotan Hybrid
        </h4>
        <p class="text-xs text-on-surface-variant font-mono bg-surface-container p-2 rounded-lg">
            &#375; = 0.4&sdot;UB + 0.4&sdot;IB + 0.2&sdot;SVD
        </p>
        <p class="text-[11px] text-on-surface-variant mt-2">
            Agregasi: 40% User-Based CF, 40% Item-Based CF, 20% Funk SVD.
        </p>
    </div>
</div>

{{-- ============================================================
     CHART.JS SCRIPT
============================================================ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    const labels    = {!! $chartLabels !!};
    const actual    = {!! $chartActual !!};
    const predicted = {!! $chartPredicted !!};

    const ctx = document.getElementById('cfChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Rating Aktual',
                    data: actual,
                    borderColor: '#006948',
                    backgroundColor: 'rgba(0,105,72,0.12)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#006948',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.35,
                    fill: true,
                },
                {
                    label: 'Prediksi Sistem (CF)',
                    data: predicted,
                    borderColor: '#d4af37',
                    backgroundColor: 'rgba(212,175,55,0.08)',
                    borderWidth: 2.5,
                    borderDash: [6, 3],
                    pointBackgroundColor: '#d4af37',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.35,
                    fill: true,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: { size: 12, family: 'Inter, sans-serif', weight: '600' },
                        usePointStyle: true,
                        pointStyleWidth: 12,
                        padding: 20,
                    }
                },
                tooltip: {
                    backgroundColor: '#1C1B1F',
                    titleFont: { size: 12, weight: 'bold' },
                    bodyFont: { size: 12 },
                    padding: 12,
                    callbacks: {
                        label: function(ctx) {
                            return ` ${ctx.dataset.label}: ${ctx.parsed.y.toFixed(4)}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: {
                        font: { size: 11, family: 'Inter, sans-serif' },
                        maxRotation: 30,
                    }
                },
                y: {
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: {
                        font: { size: 11 },
                        callback: (v) => v.toFixed(2)
                    },
                    title: {
                        display: true,
                        text: 'Avg. Implicit Rating',
                        font: { size: 11, weight: '600' },
                        color: '#6B7280'
                    }
                }
            }
        }
    });
})();
</script>

@endsection
