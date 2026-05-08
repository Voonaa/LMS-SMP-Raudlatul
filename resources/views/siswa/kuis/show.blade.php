<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuis: {{ $kuis->judul }} - Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#f8f9ff] text-[#0b1c30] font-['Inter'] antialiased">
    <div class="max-w-4xl mx-auto p-6" x-data="kuisRunner()">
        <div class="mb-6 flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-200 sticky top-4 z-10">
            <div>
                <h1 class="text-xl font-bold text-[#006948]">{{ $kuis->judul }}</h1>
                <p class="text-xs text-gray-500">{{ $kuis->mata_pelajaran->nama_mapel ?? 'Umum' }}</p>
            </div>
            <div class="text-sm font-semibold text-gray-600 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#cba72f]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span x-text="formatTime(durasi)"></span>
            </div>
        </div>

        <form action="{{ route('siswa.kuis.submit', $kuis->id) }}" method="POST" id="kuisForm">
            @csrf
            <input type="hidden" name="durasi" x-model="durasi">
            
            <div class="space-y-6">
                @foreach($kuis->soal as $index => $soal)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex gap-4 mb-4">
                            <span class="bg-[#e5eeff] text-[#006948] w-8 h-8 rounded-full flex items-center justify-center font-bold flex-shrink-0">{{ $index + 1 }}</span>
                            <h3 class="text-lg font-semibold">{{ $soal->pertanyaan }}</h3>
                        </div>
                        
                        <div class="pl-12 space-y-3">
                            @foreach(['A' => $soal->opsi_a, 'B' => $soal->opsi_b, 'C' => $soal->opsi_c, 'D' => $soal->opsi_d] as $opsiKey => $opsiText)
                                <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors">
                                    <input type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $opsiKey }}" class="mt-1 text-[#006948] focus:ring-[#006948]" required>
                                    <span class="font-medium text-gray-700">{{ $opsiKey }}. {{ $opsiText }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-[#006948] text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl hover:bg-[#00855d] transition-all">
                    Kumpulkan Kuis
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('kuisRunner', () => ({
                durasi: 0,
                timer: null,
                init() {
                    this.timer = setInterval(() => {
                        this.durasi++;
                    }, 1000);
                },
                formatTime(seconds) {
                    const m = Math.floor(seconds / 60).toString().padStart(2, '0');
                    const s = (seconds % 60).toString().padStart(2, '0');
                    return `${m}:${s}`;
                }
            }));
        });
    </script>
</body>
</html>
