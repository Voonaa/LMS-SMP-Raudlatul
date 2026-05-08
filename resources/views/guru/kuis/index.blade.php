<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kuis - Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#f8f9ff] text-[#0b1c30] font-['Inter'] antialiased p-6">
    <div class="max-w-6xl mx-auto" x-data="{ aiModalOpen: false, loading: false, materiId: '', jumlahSoal: 5, 
        async generate() {
            if(!this.materiId || !this.jumlahSoal) return;
            this.loading = true;
            try {
                const response = await fetch('{{ route('guru.kuis.generate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ materi_id: this.materiId, jumlah_soal: this.jumlahSoal })
                });
                const data = await response.json();
                if(data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message || 'Error occurred');
                }
            } catch(e) {
                alert('Gagal menghubungi server.');
            } finally {
                this.loading = false;
            }
        } 
    }">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-[#006948]">Kelola Kuis</h1>
            <div class="flex gap-4">
                <a href="{{ route('guru.dashboard') }}" class="text-[#006948] hover:underline font-semibold mt-2">Kembali ke Dashboard</a>
                <button @click="aiModalOpen = true" class="bg-gradient-to-r from-[#d4af37] to-[#f3e5ab] text-[#241a00] px-4 py-2 rounded-lg font-bold hover:shadow-lg transition-all flex items-center gap-2">
                    ✨ Generate via AI Gemini
                </button>
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
                        <th class="p-4 font-semibold text-gray-600">Judul Kuis</th>
                        <th class="p-4 font-semibold text-gray-600">Materi</th>
                        <th class="p-4 font-semibold text-gray-600">Jml Soal</th>
                        <th class="p-4 font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($kuis as $k)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 font-medium text-[#0b1c30]">{{ $k->judul }}</td>
                            <td class="p-4 text-gray-600">{{ $k->materi->judul ?? '-' }}</td>
                            <td class="p-4 text-gray-600">{{ $k->soal->count() }} Soal</td>
                            <td class="p-4">
                                <form action="{{ route('guru.kuis.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kuis ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500">Belum ada kuis. Silakan klik "Generate via AI Gemini".</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- AI Modal -->
        <div x-show="aiModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div @click.outside="if(!loading) aiModalOpen = false" class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-[#006948] flex items-center gap-2">✨ Generate Kuis AI</h3>
                    <button @click="if(!loading) aiModalOpen = false" class="text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Materi Referensi</label>
                        <select x-model="materiId" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-[#006948]">
                            <option value="">Pilih Materi</option>
                            @foreach($materis as $m)
                                <option value="{{ $m->id }}">{{ $m->judul }} ({{ $m->kelas->nama_kelas ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Soal</label>
                        <input x-model="jumlahSoal" type="number" min="1" max="20" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-[#006948]">
                    </div>
                    
                    <button @click="generate()" :disabled="loading || !materiId || !jumlahSoal" class="w-full mt-4 bg-gradient-to-r from-[#d4af37] to-[#f3e5ab] text-[#241a00] font-bold py-3 rounded-lg flex items-center justify-center gap-2 disabled:opacity-50 transition-all">
                        <span x-text="loading ? '⏳ Generating (Bisa memakan waktu > 10 detik)...' : '✨ Generate Kuis'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
