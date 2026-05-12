@extends('layouts.guru')

@section('title', 'Kelola Materi - Guru')
@section('page_title', 'Kelola Materi')

@section('content')
<div x-data="{ 
    open: false, 
    importModalOpen: false,
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
}">
    <div class="mb-lg flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="font-headline-xl text-headline-xl text-on-surface">Daftar Materi</h2>
            <p class="font-body-lg text-body-lg text-on-surface-variant mt-2">Daftar semua materi yang Anda ajarkan di kelas-kelas.</p>
        </div>
        <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto items-center">
            <form action="{{ route('guru.materi.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                <div class="relative flex-1 md:w-48">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline-variant">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" class="w-full border border-outline-variant bg-surface rounded-lg pl-10 pr-4 py-2 focus:ring-primary focus:border-primary text-on-surface text-sm" placeholder="Cari materi...">
                </div>
                <div class="md:w-40">
                    <select name="kelas_id" onchange="this.form.submit()" class="w-full border border-outline-variant bg-surface rounded-lg px-3 py-2 focus:ring-primary focus:border-primary text-on-surface text-sm">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList ?? [] as $kelas)
                            <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:w-40">
                    <select name="mapel_id" onchange="this.form.submit()" class="w-full border border-outline-variant bg-surface rounded-lg px-3 py-2 focus:ring-primary focus:border-primary text-on-surface text-sm">
                        <option value="">Semua Mapel</option>
                        @foreach($mapelList ?? [] as $mapel)
                            <option value="{{ $mapel->id }}" {{ request('mapel_id') == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
            <div class="flex gap-2">
                <button @click="open = true" class="flex items-center gap-2 bg-gradient-to-r from-[#d4af37] to-[#f3e5ab] text-on-tertiary-fixed border border-[#cba72f] px-4 py-2 rounded-lg font-bold hover:shadow-[0_4px_20px_0_rgba(212,175,55,0.3)] transition-all text-sm">
                    <span class="material-symbols-outlined text-sm">auto_awesome</span>
                    Generate AI
                </button>
                <button @click="importModalOpen = true" class="flex items-center gap-2 bg-surface-container text-primary border border-primary px-4 py-2 rounded-lg font-bold hover:bg-surface-container-high transition-colors text-sm">
                    <span class="material-symbols-outlined text-sm">upload_file</span>
                    Import CSV
                </button>
                <a href="{{ route('guru.materi.create') }}" class="flex items-center gap-2 bg-primary text-on-primary px-4 py-2 rounded-lg font-bold hover:bg-primary-container transition-colors shadow-sm whitespace-nowrap text-sm">
                    <span class="material-symbols-outlined text-sm">add</span>
                    Tambah
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-primary-container text-on-primary-container p-4 mb-6 rounded-xl flex items-center gap-3 shadow-sm">
            <span class="material-symbols-outlined">check_circle</span>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-surface-container-lowest rounded-xl shadow-[0_2px_10px_0_rgba(0,105,72,0.05)] border border-surface-container overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant font-label-lg text-label-lg uppercase tracking-wider text-xs border-b border-surface-container">
                        <th class="p-4 font-semibold">Judul Materi</th>
                        <th class="p-4 font-semibold">Kelas</th>
                        <th class="p-4 font-semibold">Mata Pelajaran</th>
                        <th class="p-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container text-body-md">
                    @forelse($materis ?? [] as $m)
                        <tr class="hover:bg-surface/50 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-primary-container flex items-center justify-center text-on-primary-container">
                                        <span class="material-symbols-outlined">menu_book</span>
                                    </div>
                                    <span class="font-medium text-on-surface">{{ $m->judul }}</span>
                                </div>
                            </td>
                            <td class="p-4 text-on-surface-variant">{{ $m->kelas->nama_kelas ?? '-' }}</td>
                            <td class="p-4 text-on-surface-variant">{{ $m->mata_pelajaran->nama_mapel ?? '-' }}</td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('guru.materi.show', $m->id) }}" class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-surface-container rounded-md transition-colors" title="Buka">
                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                    </a>
                                    <a href="{{ route('guru.materi.edit', $m->id) }}" class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-surface-container rounded-md transition-colors" title="Edit">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                    </a>
                                    <form action="{{ route('guru.materi.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus materi ini?');" class="inline">
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
                        <tr>
                            <td colspan="4" class="p-4 text-center text-on-surface-variant py-8">
                                <span class="material-symbols-outlined text-4xl mb-2">auto_stories</span>
                                <p>Belum ada materi. Silakan generate via AI atau tambah manual.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Import Materi Modal -->
    <div x-show="importModalOpen" x-cloak style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 text-left">
        <div @click.outside="importModalOpen = false" class="bg-surface rounded-xl shadow-xl w-full max-w-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-headline-md text-primary flex items-center gap-2"><span class="material-symbols-outlined text-tertiary-container">upload_file</span> Import Materi CSV</h3>
                <button @click="importModalOpen = false" class="text-on-surface-variant hover:text-error font-bold"><span class="material-symbols-outlined">close</span></button>
            </div>
            
            <form action="{{ route('guru.materi.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="bg-surface-container-low p-4 rounded-lg border border-outline-variant mb-4">
                    <p class="text-xs text-on-surface-variant mb-2">Gunakan template CSV berikut agar data materi terbaca dengan benar:</p>
                    <a href="{{ route('guru.materi.template') }}" class="inline-flex items-center gap-2 bg-secondary text-on-secondary px-3 py-2 rounded-lg text-xs font-bold hover:bg-secondary-container hover:text-on-secondary-container transition-all">
                        <span class="material-symbols-outlined text-sm">download</span> Download Template CSV
                    </a>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1">Pilih File CSV</label>
                    <input type="file" name="file_csv" class="w-full border border-outline-variant bg-surface rounded-lg p-2 focus:ring-primary focus:border-primary" required>
                    <p class="text-xs text-outline mt-1">Format: `Judul, Konten, Nama Kelas, Nama Mapel`</p>
                </div>
                
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="importModalOpen = false" class="px-4 py-2 bg-surface-container text-on-surface rounded-lg hover:bg-surface-container-high transition-colors text-sm font-bold">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg hover:bg-primary-container transition-colors text-sm font-bold">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>

    <!-- AI Generate Modal -->
    <div x-show="open" x-cloak style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 text-left">
        <div @click.outside="if(!loading) open = false" class="bg-surface rounded-xl shadow-lg w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-headline-md text-primary flex items-center gap-2"><span class="material-symbols-outlined text-tertiary-container">auto_awesome</span> Generate Materi AI</h3>
                <button @click="if(!loading) open = false" class="text-on-surface-variant hover:text-error font-bold"><span class="material-symbols-outlined">close</span></button>
            </div>
            
            <div x-show="!result" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1 text-on-surface">Topik Materi</label>
                    <input x-model="topik" type="text" class="w-full border border-outline-variant bg-surface rounded-lg p-2 focus:ring-primary focus:border-primary text-on-surface" placeholder="Cth: Sistem Tata Surya">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-on-surface">Kelas</label>
                    <select x-model="kelas" class="w-full border border-outline-variant bg-surface rounded-lg p-2 focus:ring-primary focus:border-primary text-on-surface">
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
                <div class="p-4 bg-surface-container-low rounded-lg border border-primary-container max-h-60 overflow-y-auto">
                    <h4 class="font-bold text-lg mb-2 text-on-surface" x-text="result?.judul"></h4>
                    <div class="text-sm prose max-w-none text-on-surface-variant" x-html="result?.konten_html"></div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1 text-on-surface">Pilih Mata Pelajaran (Wajib untuk menyimpan)</label>
                    <select x-model="mapel_id" class="w-full border border-outline-variant bg-surface rounded-lg p-2 focus:ring-primary focus:border-primary text-on-surface">
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
</div>
@endsection
