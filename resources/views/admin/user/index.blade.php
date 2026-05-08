<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
</head>
<body class="bg-[#f8f9ff] text-[#0b1c30] font-['Inter'] antialiased p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-[#0b1c30]">Kelola Pengguna</h1>
            <div class="flex gap-4">
                <a href="{{ route('admin.testing.mae') }}" class="text-[#006948] hover:underline font-semibold mt-2">Testing MAE CF</a>
                <a href="{{ route('admin.config.index') }}" class="text-[#006948] hover:underline font-semibold mt-2">Konfigurasi</a>
                <a href="{{ route('admin.user.create') }}" class="bg-[#0b1c30] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-800 transition-colors">Tambah User</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-700 transition-colors">Logout</button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 border-l-4 border-emerald-600 text-emerald-800 p-4 mb-6 rounded-r-lg" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="p-4 font-semibold text-gray-600">Nama</th>
                        <th class="p-4 font-semibold text-gray-600">Username</th>
                        <th class="p-4 font-semibold text-gray-600">Role</th>
                        <th class="p-4 font-semibold text-gray-600">Kelas</th>
                        <th class="p-4 font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($users as $u)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 font-medium text-[#0b1c30]">{{ $u->name }}</td>
                            <td class="p-4 text-gray-600">{{ $u->username }}</td>
                            <td class="p-4 text-gray-600 capitalize">{{ $u->role }}</td>
                            <td class="p-4 text-gray-600">{{ $u->kelas->nama_kelas ?? '-' }}</td>
                            <td class="p-4">
                                <form action="{{ route('admin.user.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
