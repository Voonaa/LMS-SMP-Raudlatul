@extends('layouts.guru')

@section('title', 'Edit Tugas: ' . $tugas->judul)
@section('page_title', 'Edit Tugas')

@section('content')
<div class="mb-lg flex justify-between items-center">
    <div>
        <h2 class="font-headline-xl text-headline-xl text-on-surface">Edit Tugas</h2>
        <p class="font-body-lg text-body-lg text-on-surface-variant mt-2">Perbarui informasi tugas untuk siswa.</p>
    </div>
    <a href="{{ route('guru.tugas.index') }}" class="flex items-center gap-2 text-on-surface-variant hover:text-primary transition-colors font-bold">
        <span class="material-symbols-outlined">arrow_back</span> Kembali
    </a>
</div>

@if($errors->any())
    <div class="bg-error-container text-error p-4 mb-6 rounded-xl border-l-4 border-error">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container p-8 max-w-4xl">
    <form action="{{ route('guru.tugas.update', $tugas->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Judul Tugas</label>
                <input type="text" name="judul" value="{{ old('judul', $tugas->judul) }}" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary focus:border-primary" placeholder="Cth: Tugas Esai: Proses Fotosintesis" required>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-2">Kelas</label>
                    <select name="kelas_id" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary" required>
                        <option value="">Pilih Kelas</option>
                        @foreach($kelasList ?? [] as $kelas)
                            <option value="{{ $kelas->id }}" {{ old('kelas_id', $tugas->kelas_id) == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-2">Mata Pelajaran</label>
                    <select name="mata_pelajaran_id" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary" required>
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach($mapelList ?? [] as $mapel)
                            <option value="{{ $mapel->id }}" {{ old('mata_pelajaran_id', $tugas->mata_pelajaran_id) == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Deskripsi / Instruksi Tugas</label>
                <textarea name="deskripsi" rows="6" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary" placeholder="Jelaskan instruksi tugas secara lengkap..." required>{{ old('deskripsi', $tugas->deskripsi) }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-2">Tenggat Waktu (opsional)</label>
                    <input type="datetime-local" name="tenggat_waktu" value="{{ old('tenggat_waktu', $tugas->tenggat_waktu ? $tugas->tenggat_waktu->format('Y-m-d\TH:i') : '') }}" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-2">Lampiran File (opsional, maks 10MB)</label>
                    @if($tugas->file_lampiran)
                        <p class="text-xs text-on-surface-variant mb-2">File saat ini: <a href="{{ Storage::url($tugas->file_lampiran) }}" target="_blank" class="text-primary hover:underline">Unduh Lampiran</a></p>
                    @endif
                    <input type="file" name="file_lampiran" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary text-sm">
                </div>
            </div>
        </div>
        <div class="flex justify-end mt-8">
            <button type="submit" class="bg-primary text-on-primary px-8 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all flex items-center gap-2">
                <span class="material-symbols-outlined">save</span>
                Perbarui Tugas
            </button>
        </div>
    </form>
</div>
@endsection
