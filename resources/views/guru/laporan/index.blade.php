@extends('layouts.guru')

@section('title', 'Laporan Progres Siswa - Guru')
@section('page_title', 'Laporan Progres Siswa')

@section('content')
<div class="mb-lg flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="font-headline-xl text-headline-xl text-on-surface">Laporan Progres Siswa</h2>
        <p class="font-body-lg text-body-lg text-on-surface-variant mt-2">Pantau rata-rata nilai kuis dan keaktifan siswa di kelas yang Anda ampu.</p>
    </div>
    <div class="flex gap-4">
        <button onclick="window.print()" class="flex items-center gap-2 bg-surface-container-lowest text-primary border border-primary px-4 py-2 rounded-lg font-bold hover:bg-surface-container-low transition-colors shadow-sm">
            <span class="material-symbols-outlined">print</span>
            Cetak Laporan
        </button>
    </div>
</div>

<div class="bg-surface-container-lowest rounded-xl shadow-[0_2px_10px_0_rgba(0,105,72,0.05)] border border-surface-container overflow-hidden">
    <div class="p-4 border-b border-surface-container bg-surface flex justify-between items-center">
        <h3 class="font-headline-md text-on-surface">Rekapitulasi Nilai & Aktivitas</h3>
        <span class="text-sm text-on-surface-variant">Tahun Ajaran Aktif</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low text-on-surface-variant font-label-lg text-label-lg uppercase tracking-wider text-xs border-b border-surface-container">
                    <th class="p-4 font-semibold">Nama Siswa</th>
                    <th class="p-4 font-semibold text-center">Kelas</th>
                    <th class="p-4 font-semibold text-center">Rata-rata Kuis</th>
                    <th class="p-4 font-semibold text-center">Total Aktivitas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container text-body-md">
                @forelse($siswa ?? [] as $s)
                    <tr class="hover:bg-surface/50 transition-colors">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($s->username) }}&background=006948&color=fff&rounded=true" class="w-8 h-8 rounded-full">
                                <span class="font-medium text-on-surface">{{ $s->name }}</span>
                            </div>
                        </td>
                        <td class="p-4 text-center text-on-surface-variant">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-container-high text-on-surface">{{ $s->kelas->nama_kelas ?? '-' }}</span>
                        </td>
                        <td class="p-4 text-center font-bold text-primary">
                            @php
                                $avg = $s->hasil_kuis->avg('nilai') ?? 0;
                            @endphp
                            {{ round($avg, 2) }}
                        </td>
                        <td class="p-4 text-center text-on-surface-variant">
                            <span class="flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-[18px]">local_fire_department</span>
                                {{ $s->log_aktivitas->count() }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-on-surface-variant py-8">
                            <span class="material-symbols-outlined text-4xl mb-2">groups</span>
                            <p>Belum ada data siswa di kelas yang Anda ajar.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
