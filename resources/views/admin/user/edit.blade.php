@extends('layouts.admin')
@section('title', 'Edit Pengguna - Admin LMS')
@section('page_title', 'Edit Pengguna')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.user.index') }}" class="text-on-surface-variant hover:text-primary transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h2 class="text-xl font-bold text-on-surface">Edit Pengguna</h2>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl border border-surface-container shadow-sm p-6">
        @if($errors->any())
            <div class="mb-4 p-4 bg-error-container text-on-error-container rounded-xl">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.user.update', $user->id) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-on-surface mb-1.5">Nama Lengkap <span class="text-error">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full border border-outline-variant bg-surface rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
            </div>

            <div>
                <label class="block text-sm font-semibold text-on-surface mb-1.5">Username / NISN <span class="text-error">*</span></label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                       class="w-full border border-outline-variant bg-surface rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary font-mono">
            </div>

            <div>
                <label class="block text-sm font-semibold text-on-surface mb-1.5">Role <span class="text-error">*</span></label>
                <select name="role" required
                        class="w-full border border-outline-variant bg-surface rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="siswa" {{ old('role', $user->role) == 'siswa' ? 'selected' : '' }}>Siswa</option>
                    <option value="guru"  {{ old('role', $user->role) == 'guru'  ? 'selected' : '' }}>Guru</option>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-on-surface mb-1.5">Kelas</label>
                <select name="kelas_id"
                        class="w-full border border-outline-variant bg-surface rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">-- Tidak ada / Admin --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ old('kelas_id', $user->kelas_id) == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-on-surface mb-1.5">Password Baru</label>
                <input type="password" name="password" placeholder="Kosongkan jika tidak diubah"
                       class="w-full border border-outline-variant bg-surface rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                <p class="text-xs text-on-surface-variant mt-1">Minimal 6 karakter. Kosongkan jika tidak ingin mengubah password.</p>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.user.index') }}"
                   class="flex-1 flex items-center justify-center py-2.5 bg-surface-container text-on-surface rounded-xl font-semibold text-sm hover:bg-surface-container-high transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-primary text-on-primary rounded-xl font-semibold text-sm hover:bg-primary/90 transition-colors">
                    <span class="material-symbols-outlined text-sm">save</span> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
