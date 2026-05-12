@extends('layouts.admin')

@section('title', 'Evaluasi MAE - Admin')
@section('page_title', 'Evaluasi MAE')

@section('content')
<div class="mb-lg">
    <h2 class="font-headline-xl text-headline-xl text-on-surface">Evaluasi MAE (Mean Absolute Error)</h2>
    <p class="font-body-lg text-body-lg text-on-surface-variant mt-2">Pengujian Akurasi Algoritma Item-Based Collaborative Filtering.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-surface-container-low border border-surface-container p-6 rounded-xl text-center shadow-sm">
        <p class="text-sm text-on-surface-variant font-semibold uppercase tracking-wider mb-1">Target User</p>
        <p class="text-xl font-bold text-primary">{{ $userTest->name ?? 'N/A' }}</p>
    </div>
    <div class="bg-surface-container-low border border-surface-container p-6 rounded-xl text-center shadow-sm">
        <p class="text-sm text-on-surface-variant font-semibold uppercase tracking-wider mb-1">Total Data (N)</p>
        <p class="text-3xl font-bold text-primary">{{ $n }}</p>
    </div>
    <div class="bg-surface-container-low border border-surface-container p-6 rounded-xl text-center shadow-sm">
        <p class="text-sm text-on-surface-variant font-semibold uppercase tracking-wider mb-1">Nilai MAE</p>
        <p class="text-3xl font-bold text-error">{{ number_format($mae, 4) }}</p>
    </div>
</div>

<div class="bg-surface-container-lowest rounded-xl shadow-[0_2px_10px_0_rgba(0,105,72,0.05)] border border-surface-container overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low text-on-surface-variant font-label-lg text-label-lg uppercase tracking-wider text-xs border-b border-surface-container">
                    <th class="p-4 font-semibold">Item ID</th>
                    <th class="p-4 font-semibold">Skor Aktual</th>
                    <th class="p-4 font-semibold">Skor Prediksi</th>
                    <th class="p-4 font-semibold text-right">Error Absolut</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container text-body-md">
                @forelse($actualScores ?? [] as $itemId => $actual)
                    <tr class="hover:bg-surface/50 transition-colors">
                        <td class="p-4 font-medium text-on-surface">{{ $itemId }}</td>
                        <td class="p-4 text-on-surface-variant">{{ $actual }}</td>
                        <td class="p-4 text-on-surface-variant">{{ number_format($predictedScores[$itemId] ?? 0, 4) }}</td>
                        <td class="p-4 text-right font-semibold text-error">{{ number_format(abs($actual - ($predictedScores[$itemId] ?? 0)), 4) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-on-surface-variant py-8">
                            <span class="material-symbols-outlined text-4xl mb-2">error</span>
                            <p>Belum ada data interaksi untuk user ini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
