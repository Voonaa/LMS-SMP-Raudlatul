@extends('layouts.guru')

@section('title', 'Edit Kuis - Guru')
@section('page_title', 'Edit Kuis')

@section('content')
<div class="mb-lg flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="font-headline-xl text-headline-xl text-on-surface">Edit Kuis</h2>
        <p class="font-body-lg text-body-lg text-on-surface-variant mt-2">Perbarui detail kuis.</p>
    </div>
    <div class="flex gap-4">
        <a href="{{ route('guru.kuis.index') }}" class="flex items-center gap-2 text-on-surface-variant hover:text-primary transition-colors font-bold mt-2">
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

<div class="bg-surface-container-lowest rounded-xl shadow-[0_2px_10px_0_rgba(0,105,72,0.05)] border border-surface-container p-6 max-w-2xl">
    <form action="{{ route('guru.kuis.update', $kuis->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-6">
            <label class="block text-sm font-semibold text-on-surface mb-2">Judul Kuis</label>
            <input type="text" name="judul" value="{{ old('judul', $kuis->judul) }}" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary focus:border-primary text-on-surface" placeholder="Masukkan judul kuis" required>
        </div>
        <div class="mb-6">
            <label class="block text-sm font-semibold text-on-surface mb-2">Deskripsi</label>
            <textarea name="deskripsi" rows="4" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary focus:border-primary text-on-surface" placeholder="Deskripsi singkat tentang kuis...">{{ old('deskripsi', $kuis->deskripsi) }}</textarea>
        </div>
        <div class="mb-6">
            <label class="block text-sm font-semibold text-on-surface mb-2">Materi Referensi (Opsional)</label>
            <select name="materi_id" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary focus:border-primary text-on-surface">
                <option value="">Pilih Materi</option>
                @foreach($materis ?? [] as $m)
                    <option value="{{ $m->id }}" {{ old('materi_id', $kuis->materi_id) == $m->id ? 'selected' : '' }}>{{ $m->judul }} ({{ $m->kelas->nama_kelas ?? '-' }})</option>
                @endforeach
            </select>
            <p class="text-xs text-outline mt-1">Jika dipilih, kelas dan mapel akan otomatis mengikuti materi tersebut.</p>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-primary text-on-primary px-8 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl hover:bg-primary-container transition-all flex items-center gap-2">
                <span class="material-symbols-outlined">save</span>
                Perbarui Kuis
            </button>
        </div>
    </form>
</div>
@endsection
