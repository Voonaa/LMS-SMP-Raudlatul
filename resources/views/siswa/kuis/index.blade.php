<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kuis - Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
</head>
<body class="bg-[#f8f9ff] text-[#0b1c30] font-['Inter'] antialiased">
    <div class="max-w-5xl mx-auto p-6">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-[#006948]">Daftar Kuis</h1>
            <a href="{{ route('siswa.dashboard') }}" class="text-[#006948] hover:underline font-semibold">Kembali ke Dashboard</a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 border-l-4 border-[#006948] text-[#006948] p-4 mb-6 rounded-r-lg" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($kuis as $k)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col">
                    <div class="mb-4">
                        <span class="bg-[#e5eeff] text-[#006948] px-3 py-1 rounded-full text-xs font-semibold">{{ $k->mata_pelajaran->nama_mapel ?? 'Umum' }}</span>
                    </div>
                    <h2 class="text-xl font-bold text-[#0b1c30] mb-2">{{ $k->judul }}</h2>
                    <p class="text-gray-600 text-sm mb-4">{{ $k->deskripsi }}</p>
                    
                    @php
                        $hasil = \App\Models\HasilKuis::where('kuis_id', $k->id)->where('user_id', auth()->id())->first();
                    @endphp

                    <div class="mt-auto flex justify-between items-center">
                        @if($hasil)
                            <span class="text-sm font-bold text-[#006948]">Nilai: {{ $hasil->nilai }}</span>
                            <span class="bg-gray-100 text-gray-500 px-3 py-1.5 rounded-lg text-sm font-semibold">Selesai</span>
                        @else
                            <span class="text-xs text-gray-500">Belum Dikerjakan</span>
                            <a href="{{ route('siswa.kuis.show', $k->id) }}" class="bg-[#cba72f] text-[#241a00] px-4 py-2 rounded-lg text-sm font-bold hover:bg-[#d4af37] transition-colors shadow-sm">Mulai Kuis</a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-white rounded-xl shadow-sm border border-gray-200">
                    <p class="text-gray-500">Belum ada kuis tersedia di kelas Anda.</p>
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>
