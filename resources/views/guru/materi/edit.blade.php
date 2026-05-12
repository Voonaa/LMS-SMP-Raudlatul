@extends('layouts.guru')

@section('title', 'Edit Materi - Guru')
@section('page_title', 'Edit Materi')

@section('content')
<div class="mb-lg flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="font-headline-xl text-headline-xl text-on-surface">Edit Materi</h2>
        <p class="font-body-lg text-body-lg text-on-surface-variant mt-2">Perbarui materi pembelajaran.</p>
    </div>
    <div class="flex gap-4">
        <a href="{{ route('guru.materi.index') }}" class="flex items-center gap-2 text-on-surface-variant hover:text-primary transition-colors font-bold mt-2">
            <span class="material-symbols-outlined">arrow_back</span>
            Kembali
        </a>
    </div>
</div>

@if($errors->any())
    <div class="bg-error-container text-error p-4 mb-6 rounded-xl border-l-4 border-error shadow-sm">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-surface-container-lowest rounded-xl shadow-[0_2px_10px_0_rgba(0,105,72,0.05)] border border-surface-container p-6 max-w-4xl">
    <form action="{{ route('guru.materi.update', $materi->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-6">
            <label class="block text-sm font-semibold text-on-surface mb-2">Judul Materi</label>
            <input type="text" name="judul" value="{{ old('judul', $materi->judul) }}" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary focus:border-primary text-on-surface" placeholder="Masukkan judul materi" required>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Kelas</label>
                <select name="kelas_id" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary focus:border-primary text-on-surface" required>
                    <option value="">Pilih Kelas</option>
                    @foreach($kelasList ?? [] as $kelas)
                        <option value="{{ $kelas->id }}" {{ old('kelas_id', $materi->kelas_id) == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Mata Pelajaran</label>
                <select name="mata_pelajaran_id" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary focus:border-primary text-on-surface" required>
                    <option value="">Pilih Mata Pelajaran</option>
                    @foreach($mapelList ?? [] as $mapel)
                        <option value="{{ $mapel->id }}" {{ old('mata_pelajaran_id', $materi->mata_pelajaran_id) == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mb-8">
            <label class="block text-sm font-semibold text-on-surface mb-2">Konten (Mendukung HTML)</label>
            <textarea name="konten" rows="12" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary focus:border-primary text-on-surface font-mono text-sm" placeholder="<p>Tuliskan materi di sini...</p>" required>{{ old('konten', $materi->konten) }}</textarea>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-primary text-on-primary px-8 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl hover:bg-primary-container transition-all flex items-center gap-2">
                <span class="material-symbols-outlined">save</span>
                Perbarui Materi
            </button>
        </div>
    </form>
</div>
@endsection
