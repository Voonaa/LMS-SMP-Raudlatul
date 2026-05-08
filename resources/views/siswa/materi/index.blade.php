<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Materi - Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
</head>
<body class="bg-[#f8f9ff] text-[#0b1c30] font-['Inter'] antialiased">
    <div class="max-w-5xl mx-auto p-6">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-[#006948]">Daftar Materi</h1>
            <a href="{{ route('siswa.dashboard') }}" class="text-[#006948] hover:underline font-semibold">Kembali ke Dashboard</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($materis as $materi)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col">
                    <div class="mb-4">
                        <span class="bg-[#e5eeff] text-[#006948] px-3 py-1 rounded-full text-xs font-semibold">{{ $materi->mata_pelajaran->nama_mapel ?? 'Umum' }}</span>
                    </div>
                    <h2 class="text-xl font-bold text-[#0b1c30] mb-2">{{ $materi->judul }}</h2>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ strip_tags($materi->konten) }}</p>
                    <div class="mt-auto flex justify-between items-center">
                        <span class="text-xs text-gray-500">Oleh: {{ $materi->guru->name ?? 'Anonim' }}</span>
                        <a href="{{ route('siswa.materi.show', $materi->id) }}" class="bg-[#006948] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-[#00855d] transition-colors">Baca Materi</a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-white rounded-xl shadow-sm border border-gray-200">
                    <p class="text-gray-500">Belum ada materi tersedia di kelas Anda.</p>
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>
