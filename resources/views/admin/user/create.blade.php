<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#f8f9ff] text-[#0b1c30] font-['Inter'] antialiased p-6">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-[#0b1c30]">Tambah Pengguna Baru</h1>
            <a href="{{ route('admin.user.index') }}" class="text-[#006948] hover:underline font-semibold mt-2">Kembali</a>
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

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" x-data="{ role: 'siswa' }">
            <form action="{{ route('admin.user.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full border border-gray-300 rounded-lg p-3" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Username (NISN/NIP tanpa spasi)</label>
                    <input type="text" name="username" class="w-full border border-gray-300 rounded-lg p-3" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" class="w-full border border-gray-300 rounded-lg p-3" required>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                        <select name="role" x-model="role" class="w-full border border-gray-300 rounded-lg p-3" required>
                            <option value="siswa">Siswa</option>
                            <option value="guru">Guru</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div x-show="role !== 'admin'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kelas (Wajib untuk Siswa/Guru)</label>
                        <select name="kelas_id" class="w-full border border-gray-300 rounded-lg p-3" :required="role !== 'admin'">
                            <option value="">Pilih Kelas</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="bg-[#0b1c30] text-white px-6 py-3 rounded-lg font-bold shadow hover:bg-gray-800 transition-colors">
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
