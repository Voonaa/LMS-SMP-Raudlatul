@extends('layouts.guru')

@section('title', 'Dashboard Guru - Pengelolaan Materi')
@section('page_title', 'Pengelolaan Materi')

@section('content')
<div class="mb-lg flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="font-headline-xl text-headline-xl text-on-surface">Pengelolaan Materi</h2>
        <p class="font-body-lg text-body-lg text-on-surface-variant mt-2">Kelola dan atur modul pembelajaran, kuis, dan bahan ajar untuk kelas Anda.</p>
    </div>
</div>

@if(session('success'))
    <div class="bg-primary-container text-on-primary-container p-4 mb-6 rounded-xl flex items-center gap-3 shadow-sm">
        <span class="material-symbols-outlined">check_circle</span>
        <p class="font-medium">{{ session('success') }}</p>
    </div>
@endif

<!-- Action Bar -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-lg">
    <!-- Action 1: Add Manual -->
    <a href="{{ route('guru.materi.create') }}" class="flex items-center justify-center gap-2 py-4 px-6 bg-primary text-on-primary rounded-xl shadow-[0_4px_14px_0_rgba(0,105,72,0.2)] hover:bg-primary-container transition-all hover:-translate-y-0.5 font-label-lg text-label-lg group relative overflow-hidden">
        <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity"></div>
        <span class="material-symbols-outlined">add</span>
        Tambah Manual
    </a>

    <!-- Action 2: Import Excel -->
    <form action="{{ route('guru.materi.import') }}" method="POST" enctype="multipart/form-data" class="hidden" id="importForm">
        @csrf
        <input type="file" name="file_csv" id="fileCsv" onchange="document.getElementById('importForm').submit()">
    </form>
    <button onclick="document.getElementById('fileCsv').click()" class="flex items-center justify-center gap-2 py-4 px-6 bg-surface-container-lowest text-primary border border-primary rounded-xl hover:bg-surface-container-low transition-all font-label-lg text-label-lg shadow-sm">
        <span class="material-symbols-outlined text-primary">upload_file</span>
        Import Excel/CSV
    </button>

    <!-- Action 3: AI Generate -->
    <button @click="$dispatch('open-ai-modal')" class="flex items-center justify-center gap-2 py-4 px-6 bg-gradient-to-r from-[#d4af37] to-[#f3e5ab] text-on-tertiary-fixed border border-[#cba72f] rounded-xl hover:shadow-[0_4px_20px_0_rgba(212,175,55,0.3)] transition-all hover:-translate-y-0.5 font-label-lg text-label-lg font-bold">
        <span class="material-symbols-outlined">auto_awesome</span>
        Generate via AI Gemini
    </button>
</div>

<!-- Content Area: Filter & Table Container -->
<div class="bg-surface-container-lowest rounded-xl shadow-[0_2px_10px_0_rgba(0,105,72,0.05)] border border-surface-container overflow-hidden">
    <div class="p-6 border-b border-surface-container flex flex-col sm:flex-row justify-between items-center gap-4 bg-white">
        <div class="flex items-center gap-4 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-80">
                <span class="material-symbols-outlined absolute left-3 top-2.5 text-outline text-sm">search</span>
                <input class="w-full pl-10 pr-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm bg-surface" placeholder="Cari judul materi..." type="text"/>
            </div>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <select class="border border-outline-variant rounded-lg px-4 py-2 text-sm bg-surface focus:ring-2 focus:ring-primary text-on-surface">
                <option>Semua Kelas</option>
                @foreach($kelasList ?? [] as $k)
                    <option value="{{ $k->id }}">Kelas {{ $k->tingkat }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low text-on-surface-variant font-label-lg text-label-lg uppercase tracking-wider text-xs border-b border-outline-variant">
                    <th class="p-4 font-semibold">Judul Materi</th>
                    <th class="p-4 font-semibold">Mata Pelajaran</th>
                    <th class="p-4 font-semibold">Kelas</th>
                    <th class="p-4 font-semibold">Tgl Dibuat</th>
                    <th class="p-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container text-body-md">
                @forelse($materiList ?? [] as $materi)
                <tr class="hover:bg-surface/50 transition-colors group">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-primary-container flex items-center justify-center text-on-primary-container">
                                <span class="material-symbols-outlined">menu_book</span>
                            </div>
                            <div>
                                <p class="font-medium text-on-surface group-hover:text-primary transition-colors">{{ $materi->judul }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 text-on-surface-variant">{{ $materi->mata_pelajaran->nama_mapel }}</td>
                    <td class="p-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-container-high text-on-surface">Kelas {{ $materi->kelas->tingkat }}</span>
                    </td>
                    <td class="p-4 text-on-surface-variant text-sm">{{ $materi->created_at->format('d M Y') }}</td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('guru.materi.show', $materi->id) }}" class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-surface-container rounded-md transition-colors" title="Buka">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                            </a>
                            <a href="{{ route('guru.materi.edit', $materi->id) }}" class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-surface-container rounded-md transition-colors" title="Edit">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </a>
                            <form action="{{ route('guru.materi.destroy', $materi->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container rounded-md transition-colors" title="Hapus">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-4 text-center text-on-surface-variant">Belum ada materi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- AI Generate Modal -->
<div x-data="{ 
    open: false, 
    loading: false, 
    topik: '', 
    kelas: '', 
    mapel_id: '',
    result: null,
    async generate() {
        if(!this.topik || !this.kelas) return;
        this.loading = true;
        this.result = null;
        try {
            const response = await fetch('{{ route('guru.materi.generate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ topik: this.topik, kelas_id: this.kelas })
            });
            const data = await response.json();
            if(data.success) {
                this.result = data;
            } else {
                alert(data.message || 'Error occurred');
            }
        } catch(e) {
            alert('Error occurred');
        } finally {
            this.loading = false;
        }
    },
    async saveMateri() {
        if(!this.result || !this.mapel_id) {
            alert('Silakan pilih Mata Pelajaran terlebih dahulu!');
            return;
        }
        this.loading = true;
        try {
            const response = await fetch('{{ route('guru.materi.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    judul: this.result.judul, 
                    konten: this.result.konten_html,
                    kelas_id: this.kelas,
                    mata_pelajaran_id: this.mapel_id
                })
            });
            const data = await response.json();
            if(data.success) {
                alert('Materi berhasil disimpan!');
                this.open = false;
                this.result = null;
                window.location.reload();
            } else {
                alert(data.message || 'Error occurred');
            }
        } catch(e) {
            alert('Terjadi kesalahan saat menyimpan materi');
        } finally {
            this.loading = false;
        }
    }
}" @open-ai-modal.window="open = true" x-show="open" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div @click.outside="if(!loading) open = false" class="bg-surface rounded-xl shadow-lg w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-headline-md text-primary flex items-center gap-2"><span class="material-symbols-outlined text-tertiary-container">auto_awesome</span> Generate Materi AI</h3>
            <button @click="if(!loading) open = false" class="text-on-surface-variant hover:text-error"><span class="material-symbols-outlined">close</span></button>
        </div>
        
        <div x-show="!result" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Topik Materi</label>
                <input x-model="topik" type="text" class="w-full border-outline-variant rounded-lg p-2 focus:ring-primary focus:border-primary" placeholder="Cth: Sistem Tata Surya">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Kelas</label>
                <select x-model="kelas" class="w-full border-outline-variant rounded-lg p-2 focus:ring-primary focus:border-primary">
                    <option value="">Pilih Kelas</option>
                    @foreach($kelasList ?? [] as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }} (Tingkat {{ $k->tingkat }})</option>
                    @endforeach
                </select>
            </div>
            <button @click="generate()" :disabled="loading || !topik || !kelas" class="w-full bg-gradient-to-r from-[#d4af37] to-[#f3e5ab] text-on-tertiary-fixed font-bold py-2 rounded-lg flex items-center justify-center gap-2 disabled:opacity-50">
                <span x-show="!loading" class="material-symbols-outlined">auto_awesome</span>
                <span x-show="loading" class="material-symbols-outlined animate-spin">sync</span>
                <span x-text="loading ? 'Generating...' : 'Generate'"></span>
            </button>
        </div>

        <div x-show="result" class="space-y-4">
            <div class="p-4 bg-surface-container-low rounded-lg border border-primary-container">
                <h4 class="font-bold text-lg mb-2" x-text="result?.judul"></h4>
                <div class="text-sm prose max-w-none" x-html="result?.konten_html"></div>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Pilih Mata Pelajaran (Wajib untuk menyimpan)</label>
                <select x-model="mapel_id" class="w-full border-outline-variant rounded-lg p-2 focus:ring-primary focus:border-primary">
                    <option value="">Pilih Mapel</option>
                    @foreach($mapelList ?? [] as $m)
                        <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex gap-2">
                <button @click="open = false; result = null; topik = ''; kelas = ''; mapel_id = ''" class="flex-1 py-2 bg-surface-container text-on-surface rounded-lg hover:bg-surface-container-high transition-colors">Tutup</button>
                <button @click="saveMateri()" :disabled="loading || !mapel_id" class="flex-1 py-2 bg-primary text-on-primary rounded-lg hover:bg-primary-container transition-colors disabled:opacity-50 flex items-center justify-center gap-2">
                    <span x-show="loading" class="material-symbols-outlined animate-spin text-sm">sync</span>
                    Simpan Materi
                </button>
            </div>
        </div>
    </div>
</div>
@endsection