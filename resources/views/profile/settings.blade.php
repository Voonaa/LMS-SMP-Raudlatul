@extends(auth()->user()->role == 'guru' ? 'layouts.guru' : (auth()->user()->role == 'admin' ? 'layouts.admin' : 'layouts.siswa'))

@section('title', 'Pengaturan Profil - LMS')
@section('page_title', 'Pengaturan Profil')

@section('content')
<div class="mb-lg">
    <h2 class="font-headline-xl text-headline-xl text-on-surface">Pengaturan Akun</h2>
    <p class="font-body-lg text-body-lg text-on-surface-variant mt-2">Perbarui informasi profil dan kata sandi Anda.</p>
</div>

@if(session('success'))
    <div class="bg-primary-container text-on-primary-container p-4 mb-6 rounded-xl flex items-center gap-3 shadow-sm">
        <span class="material-symbols-outlined">check_circle</span>
        <p class="font-medium">{{ session('success') }}</p>
    </div>
@endif

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
    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        <div class="mb-6">
            <label class="block text-sm font-semibold text-on-surface mb-2">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary focus:border-primary text-on-surface" required>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-semibold text-on-surface mb-2">Username</label>
            <input type="text" value="{{ $user->username }}" class="w-full border border-outline-variant bg-surface-container-low rounded-lg p-3 text-on-surface-variant cursor-not-allowed" disabled>
            <p class="text-xs text-outline mt-1">Username tidak dapat diubah.</p>
        </div>

        <div class="border-t border-surface-container my-6 pt-6">
            <h3 class="text-lg font-bold text-on-surface mb-4">Ubah Kata Sandi</h3>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-on-surface mb-2">Kata Sandi Saat Ini</label>
                <input type="password" name="current_password" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary focus:border-primary text-on-surface" placeholder="Masukkan kata sandi saat ini">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-2">Kata Sandi Baru</label>
                    <input type="password" name="new_password" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary focus:border-primary text-on-surface" placeholder="Minimal 6 karakter">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-2">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="new_password_confirmation" class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-primary focus:border-primary text-on-surface" placeholder="Ulangi kata sandi baru">
                </div>
            </div>
            <p class="text-xs text-outline mt-2">Kosongkan jika tidak ingin mengubah kata sandi.</p>
        </div>
        
        <div class="flex justify-end gap-3 mt-8">
            <button type="submit" class="px-6 py-3 bg-primary text-on-primary rounded-lg font-bold hover:bg-primary-container transition-colors shadow-sm">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
