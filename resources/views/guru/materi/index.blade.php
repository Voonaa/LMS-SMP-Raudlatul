<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Materi - Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
</head>
<body class="bg-[#f8f9ff] text-[#0b1c30] font-['Inter'] antialiased p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-[#006948]">Kelola Materi</h1>
            <div class="flex gap-4">
                <a href="{{ route('guru.dashboard') }}" class="text-[#006948] hover:underline font-semibold mt-2">Kembali ke Dashboard</a>
                <a href="{{ route('guru.materi.create') }}" class="bg-[#006948] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-[#00855d] transition-colors">Tambah Materi Manual</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 border-l-4 border-[#006948] text-[#006948] p-4 mb-6 rounded-r-lg" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="p-4 font-semibold text-gray-600">Judul Materi</th>
                        <th class="p-4 font-semibold text-gray-600">Kelas</th>
                        <th class="p-4 font-semibold text-gray-600">Mata Pelajaran</th>
                        <th class="p-4 font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($materis as $m)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 font-medium text-[#0b1c30]">{{ $m->judul }}</td>
                            <td class="p-4 text-gray-600">{{ $m->kelas->nama_kelas ?? '-' }}</td>
                            <td class="p-4 text-gray-600">{{ $m->mata_pelajaran->nama_mapel ?? '-' }}</td>
                            <td class="p-4">
                                <form action="{{ route('guru.materi.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus materi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500">Belum ada materi. Silakan generate via AI di Dashboard atau tambah manual.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
