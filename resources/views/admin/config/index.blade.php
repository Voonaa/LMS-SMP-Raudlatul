<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfigurasi Sistem - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
</head>
<body class="bg-[#f8f9ff] text-[#0b1c30] font-['Inter'] antialiased p-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-[#0b1c30]">Konfigurasi Sistem</h1>
            <a href="{{ route('admin.user.index') }}" class="text-[#006948] hover:underline font-semibold mt-2">Kembali ke Kelola Pengguna</a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 border-l-4 border-emerald-600 text-emerald-800 p-4 mb-6 rounded-r-lg" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form action="{{ route('admin.config.save') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Ajaran Aktif</label>
                    <input type="text" name="tahun_ajaran" value="2025/2026" class="w-full border border-gray-300 rounded-lg p-3">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Batas Bobot Leaderboard Matematika (Multiplier)</label>
                    <input type="number" step="0.1" name="math_multiplier" value="1.5" class="w-full border border-gray-300 rounded-lg p-3">
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="bg-[#0b1c30] text-white px-6 py-3 rounded-lg font-bold shadow hover:bg-gray-800 transition-colors">
                        Simpan Konfigurasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
