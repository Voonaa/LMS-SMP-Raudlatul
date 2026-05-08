<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $materi->judul }} - Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#f8f9ff] text-[#0b1c30] font-['Inter'] antialiased">
    <div class="max-w-4xl mx-auto p-6" x-data="materiReader()">
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('siswa.materi.index') }}" class="text-[#006948] hover:underline font-semibold flex items-center gap-2">
                &larr; Kembali ke Daftar Materi
            </a>
            <div class="text-sm font-semibold text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                Waktu membaca: <span x-text="formatTime(durasi)"></span>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 md:p-12">
            <div class="mb-8 border-b border-gray-100 pb-6">
                <span class="bg-[#e5eeff] text-[#006948] px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-4 inline-block">
                    {{ $materi->mata_pelajaran->nama_mapel ?? 'Umum' }}
                </span>
                <h1 class="text-4xl font-extrabold text-[#0b1c30] mb-4">{{ $materi->judul }}</h1>
                <p class="text-gray-500 text-sm font-medium">Pengajar: {{ $materi->guru->name ?? 'Anonim' }} • Diperbarui pada {{ $materi->updated_at->format('d M Y') }}</p>
            </div>

            <div class="prose max-w-none text-gray-800 leading-relaxed mb-12">
                {!! $materi->konten !!}
            </div>

            <div class="mt-8 pt-8 border-t border-gray-100 flex justify-center">
                <button @click="selesaiMembaca()" x-bind:disabled="loading || isDone" class="bg-gradient-to-r from-[#006948] to-[#00855d] text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all disabled:opacity-50 flex items-center gap-2">
                    <span x-show="!loading && !isDone">Selesai Membaca & Dapatkan Poin</span>
                    <span x-show="loading">Menyimpan progres...</span>
                    <span x-show="isDone">Tersimpan! Poin ditambahkan.</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('materiReader', () => ({
                durasi: 0,
                timer: null,
                loading: false,
                isDone: false,
                init() {
                    this.timer = setInterval(() => {
                        if(!this.isDone) {
                            this.durasi++;
                        }
                    }, 1000);
                },
                formatTime(seconds) {
                    const m = Math.floor(seconds / 60).toString().padStart(2, '0');
                    const s = (seconds % 60).toString().padStart(2, '0');
                    return `${m}:${s}`;
                },
                async selesaiMembaca() {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route('siswa.materi.log', $materi->id) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ durasi: this.durasi })
                        });
                        const data = await response.json();
                        if(data.success) {
                            this.isDone = true;
                            clearInterval(this.timer);
                        }
                    } catch(e) {
                        alert('Gagal menyimpan progres');
                    } finally {
                        this.loading = false;
                    }
                }
            }));
        });
    </script>
</body>
</html>
