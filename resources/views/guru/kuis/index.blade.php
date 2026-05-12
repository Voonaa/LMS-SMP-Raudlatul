@extends('layouts.guru')

@section('title', 'Kelola Kuis - Guru')
@section('page_title', 'Kelola Kuis')

@section('content')
<div x-data="{ aiModalOpen: false, importModalOpen: false, loading: false, materiId: '', jumlahSoal: 5, activeKuisId: null,
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
    <div class="mb-lg flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="font-headline-xl text-headline-xl text-on-surface">Daftar Kuis</h2>
            <p class="font-body-lg text-body-lg text-on-surface-variant mt-2">Kelola kuis dan evaluasi untuk mengukur pemahaman siswa.</p>
        </div>
        <div class="flex gap-2 items-center">
            <a href="{{ route('guru.kuis.template') }}" class="flex items-center gap-2 bg-surface-container text-primary border border-primary px-4 py-2 rounded-lg font-bold hover:bg-surface-container-high transition-colors text-sm">
                <span class="material-symbols-outlined text-sm">download</span>
                Template CSV
            </a>
            <button @click="aiModalOpen = true" class="bg-gradient-to-r from-[#d4af37] to-[#f3e5ab] text-on-tertiary-fixed border border-[#cba72f] px-4 py-2 rounded-lg font-bold hover:shadow-[0_4px_20px_0_rgba(212,175,55,0.3)] transition-all flex items-center gap-2 text-sm">
                <span class="material-symbols-outlined text-sm">auto_awesome</span> Generate AI
            </button>
            <a href="{{ route('guru.kuis.create') }}" class="flex items-center gap-2 bg-primary text-on-primary px-4 py-2 rounded-lg font-bold hover:bg-primary-container transition-colors shadow-sm whitespace-nowrap text-sm">
                <span class="material-symbols-outlined text-sm">add</span>
                Buat Kuis
            </a>
        </div>

        <!-- AI Modal -->
        <div x-show="aiModalOpen" x-cloak style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 text-left">
            <div @click.outside="if(!loading) aiModalOpen = false" class="bg-surface rounded-xl shadow-xl w-full max-w-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-headline-md text-primary flex items-center gap-2"><span class="material-symbols-outlined text-tertiary-container">auto_awesome</span> Generate Kuis AI</h3>
                    <button @click="if(!loading) aiModalOpen = false" class="text-on-surface-variant hover:text-error font-bold"><span class="material-symbols-outlined">close</span></button>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1">Pilih Materi Referensi</label>
                        <select x-model="materiId" class="w-full border border-outline-variant bg-surface rounded-lg p-2 focus:ring-primary focus:border-primary">
                            <option value="">Pilih Materi</option>
                            @foreach($materis ?? [] as $m)
                                <option value="{{ $m->id }}">{{ $m->judul }} ({{ $m->kelas->nama_kelas ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1">Jumlah Soal</label>
                        <input x-model="jumlahSoal" type="number" min="1" max="20" class="w-full border border-outline-variant bg-surface rounded-lg p-2 focus:ring-primary focus:border-primary">
                    </div>
                    
                    <button @click="generate()" :disabled="loading || !materiId || !jumlahSoal" class="w-full mt-4 bg-gradient-to-r from-[#d4af37] to-[#f3e5ab] text-on-tertiary-fixed font-bold py-3 rounded-lg flex items-center justify-center gap-2 disabled:opacity-50 transition-all">
                        <span x-show="!loading" class="material-symbols-outlined">auto_awesome</span>
                        <span x-show="loading" class="material-symbols-outlined animate-spin">sync</span>
                        <span x-text="loading ? 'Generating...' : 'Generate Kuis'"></span>
                    </button>
                </div>
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
                        <th class="p-4 font-semibold">Judul Kuis</th>
                        <th class="p-4 font-semibold">Materi Referensi</th>
                        <th class="p-4 font-semibold text-center">Jml Soal</th>
                        <th class="p-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container text-body-md">
                    @forelse($kuis ?? [] as $k)
                        <tr class="hover:bg-surface/50 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-tertiary-container flex items-center justify-center text-on-tertiary-container">
                                        <span class="material-symbols-outlined">assignment</span>
                                    </div>
                                    <span class="font-medium text-on-surface">{{ $k->judul }}</span>
                                </div>
                            </td>
                            <td class="p-4 text-on-surface-variant">{{ $k->materi->judul ?? '-' }}</td>
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-container-high text-on-surface">{{ $k->soal->count() }} Soal</span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('guru.kuis.show', $k->id) }}" class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-surface-container rounded-md transition-colors" title="Buka">
                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                    </a>
                                    <a href="{{ route('guru.kuis.edit', $k->id) }}" class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-surface-container rounded-md transition-colors" title="Edit">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                    </a>
                                    <button @click="importModalOpen = true; activeKuisId = {{ $k->id }}" class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-surface-container rounded-md transition-colors" title="Import Soal">
                                        <span class="material-symbols-outlined text-sm">upload_file</span>
                                    </button>
                                    <form action="{{ route('guru.kuis.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kuis ini?');" class="inline">
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
                                <span class="material-symbols-outlined text-4xl mb-2">task</span>
                                <p>Belum ada kuis. Silakan klik "Generate via AI Gemini".</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Import Soal Modal -->
    <div x-show="importModalOpen" x-cloak style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 text-left">
        <div @click.outside="importModalOpen = false" class="bg-surface rounded-xl shadow-xl w-full max-w-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-headline-md text-primary flex items-center gap-2"><span class="material-symbols-outlined text-tertiary-container">upload_file</span> Import Soal Kuis</h3>
                <button @click="importModalOpen = false" class="text-on-surface-variant hover:text-error font-bold"><span class="material-symbols-outlined">close</span></button>
            </div>
            
            <form :action="'/guru/kuis/' + activeKuisId + '/import'" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="bg-surface-container-low p-4 rounded-lg border border-outline-variant mb-4">
                    <p class="text-xs text-on-surface-variant mb-2">Gunakan template CSV berikut agar data soal terbaca dengan benar:</p>
                    <a href="{{ route('guru.kuis.template') }}" class="inline-flex items-center gap-2 bg-secondary text-on-secondary px-3 py-2 rounded-lg text-xs font-bold hover:bg-secondary-container hover:text-on-secondary-container transition-all">
                        <span class="material-symbols-outlined text-sm">download</span> Download Template CSV
                    </a>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1">Pilih File CSV</label>
                    <input type="file" name="file_csv" class="w-full border border-outline-variant bg-surface rounded-lg p-2 focus:ring-primary focus:border-primary" required>
                    <p class="text-xs text-outline mt-1">Format: `Pertanyaan, Opsi A, B, C, D, Jawaban (A/B/C/D)`</p>
                </div>
                
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="importModalOpen = false" class="px-4 py-2 bg-surface-container text-on-surface rounded-lg hover:bg-surface-container-high transition-colors text-sm font-bold">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg hover:bg-primary-container transition-colors text-sm font-bold">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
