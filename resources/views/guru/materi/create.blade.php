<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Materi - Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
</head>
<body class="bg-[#f8f9ff] text-[#0b1c30] font-['Inter'] antialiased p-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-[#006948]">Tambah Materi Manual</h1>
            <a href="{{ route('guru.materi.index') }}" class="text-[#006948] hover:underline font-semibold mt-2">Kembali</a>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form action="{{ route('guru.materi.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Materi</label>
                    <input type="text" name="judul" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-[#006948] focus:border-[#006948]" required>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kelas</label>
                        <select name="kelas_id" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-[#006948]" required>
                            <option value="">Pilih Kelas</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Mata Pelajaran</label>
                        <select name="mata_pelajaran_id" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-[#006948]" required>
                            <option value="">Pilih Mata Pelajaran</option>
                            @foreach($mapelList as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Konten (Mendukung HTML)</label>
                    <textarea name="konten" rows="8" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-[#006948] focus:border-[#006948]" required></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-[#006948] text-white px-6 py-3 rounded-lg font-bold shadow hover:bg-[#00855d] transition-colors">
                        Simpan Materi
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
