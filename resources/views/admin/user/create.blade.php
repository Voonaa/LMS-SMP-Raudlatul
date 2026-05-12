@extends('layouts.admin')

@section('title', 'Tambah Pengguna - Admin')
@section('page_title', 'Tambah Pengguna')

@section('content')
<div class="mb-lg">
    <a href="{{ route('admin.user.index') }}" class="inline-flex items-center text-primary hover:text-primary-container font-medium mb-4">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Kembali ke Daftar
    </a>
    <h2 class="font-headline-xl text-headline-xl text-on-surface">Tambah Pengguna Baru</h2>
    <p class="font-body-lg text-body-lg text-on-surface-variant mt-2">Buat akun baru untuk siswa, guru, atau admin.</p>
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

<div class="bg-surface-container-lowest rounded-xl shadow-[0_2px_10px_0_rgba(0,105,72,0.05)] border border-surface-container p-6 max-w-2xl" x-data="{ role: 'siswa' }">
    <form action="{{ route('admin.user.store') }}" method="POST">
        @csrf
        <div class="mb-6">
            <label class="block text-sm font-semibold text-on-surface mb-2">Nama Lengkap</label>
            <input type="text" name="name" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary focus:border-primary text-on-surface" placeholder="Masukkan nama lengkap" required>
        </div>
        <div class="mb-6">
            <label class="block text-sm font-semibold text-on-surface mb-2">Username (NISN/NIP tanpa spasi)</label>
            <input type="text" name="username" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary focus:border-primary text-on-surface" placeholder="Masukkan username" required>
        </div>
        <div class="mb-6">
            <label class="block text-sm font-semibold text-on-surface mb-2">Password</label>
            <input type="password" name="password" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary focus:border-primary text-on-surface" placeholder="Masukkan password" required>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Role</label>
                <select name="role" x-model="role" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary focus:border-primary text-on-surface" required>
                    <option value="siswa">Siswa</option>
                    <option value="guru">Guru</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div x-show="role !== 'admin'">
                <label class="block text-sm font-semibold text-on-surface mb-2">Kelas (Wajib untuk Siswa/Guru)</label>
                <select name="kelas_id" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary focus:border-primary text-on-surface" :required="role !== 'admin'">
                    <option value="">Pilih Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="flex justify-end gap-3 mt-8">
            <a href="{{ route('admin.user.index') }}" class="px-6 py-3 bg-surface-container text-on-surface rounded-lg font-medium hover:bg-surface-container-high transition-colors">Batal</a>
            <button type="submit" class="px-6 py-3 bg-primary text-on-primary rounded-lg font-bold hover:bg-primary-container transition-colors shadow-sm">
                Simpan Pengguna
            </button>
        </div>
    </form>
</div>
@endsection
