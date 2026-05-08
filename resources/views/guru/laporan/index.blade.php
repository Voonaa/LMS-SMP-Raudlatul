<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Progres Siswa - Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
</head>
<body class="bg-[#f8f9ff] text-[#0b1c30] font-['Inter'] antialiased p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-[#006948]">Laporan Progres Siswa</h1>
            <a href="{{ route('guru.dashboard') }}" class="text-[#006948] hover:underline font-semibold mt-2">Kembali ke Dashboard</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="p-4 font-semibold text-gray-600">Nama Siswa</th>
                        <th class="p-4 font-semibold text-gray-600">Kelas</th>
                        <th class="p-4 font-semibold text-gray-600">Rata-rata Kuis</th>
                        <th class="p-4 font-semibold text-gray-600">Total Aktivitas (Materi & Forum)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($siswa as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 font-medium text-[#0b1c30]">{{ $s->name }}</td>
                            <td class="p-4 text-gray-600">{{ $s->kelas->nama_kelas ?? '-' }}</td>
                            <td class="p-4 font-bold text-[#006948]">
                                @php
                                    $avg = $s->hasil_kuis->avg('nilai') ?? 0;
                                @endphp
                                {{ round($avg, 2) }}
                            </td>
                            <td class="p-4 text-gray-600">
                                {{ $s->log_aktivitas->count() }} Aktivitas
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500">Belum ada data siswa di kelas yang Anda ajar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
