@extends(auth()->user()->role == 'guru' ? 'layouts.guru' : (auth()->user()->role == 'admin' ? 'layouts.admin' : 'layouts.siswa'))

@section('title', 'Pengaturan Profil - LMS')
@section('page_title', 'Pengaturan Profil')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-on-surface">Pengaturan Akun</h2>
    <p class="text-on-surface-variant mt-1">Perbarui informasi profil dan kata sandi Anda.</p>
</div>

{{-- ============================================================
     BANNER FORCE PASSWORD CHANGE (hanya muncul jika belum ganti)
============================================================ --}}
@if(auth()->user()->role === 'siswa' && !auth()->user()->is_password_changed)
<div class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-400 rounded-xl flex items-start gap-3 shadow-sm">
    <span class="material-symbols-outlined text-amber-500 text-2xl flex-shrink-0 mt-0.5">security</span>
    <div>
        <p class="font-bold text-amber-800 text-sm">Langkah Keamanan Wajib</p>
        <p class="text-amber-700 text-sm mt-0.5">
            Demi keamanan, silakan ganti password bawaan Anda sebelum mulai belajar.
            Anda tidak dapat mengakses fitur LMS lainnya sampai password diperbarui.
        </p>
    </div>
</div>
@endif

{{-- Flash from force redirect --}}
@if(session('force_password_change'))
<div class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-400 rounded-xl flex items-start gap-3 shadow-sm">
    <span class="material-symbols-outlined text-amber-500 text-2xl flex-shrink-0 mt-0.5">lock</span>
    <div>
        <p class="font-bold text-amber-800 text-sm">Akses Terkunci</p>
        <p class="text-amber-700 text-sm mt-0.5">
            Demi keamanan, silakan ganti password bawaan Anda sebelum mulai belajar.
        </p>
    </div>
</div>
@endif

@if(session('success'))
<div class="mb-6 bg-primary-container text-on-primary-container p-4 rounded-xl flex items-center gap-3 shadow-sm">
    <span class="material-symbols-outlined">check_circle</span>
    <p class="font-medium">{{ session('success') }}</p>
</div>
@endif

@if($errors->any())
<div class="mb-6 bg-error-container text-error p-4 rounded-xl border-l-4 border-error shadow-sm">
    <ul class="list-disc list-inside text-sm space-y-1">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- ============================================================
         FORM 1: Ubah Nama (dikunci jika belum ganti password)
    ============================================================ --}}
    <div class="bg-surface-container-lowest rounded-xl border border-surface-container p-6 shadow-sm
                {{ auth()->user()->role === 'siswa' && !auth()->user()->is_password_changed ? 'opacity-50 pointer-events-none select-none' : '' }}">
        <h3 class="text-lg font-bold text-on-surface mb-5 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">person</span>
            Informasi Profil
        </h3>

        @if(auth()->user()->role === 'siswa' && !auth()->user()->is_password_changed)
        <div class="mb-4 flex items-center gap-2 text-xs text-amber-600 bg-amber-50 p-2 rounded-lg">
            <span class="material-symbols-outlined text-sm">lock</span>
            Ubah nama setelah password diperbarui.
        </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-on-surface mb-2">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-2 focus:ring-primary focus:border-primary text-on-surface transition" required>
            </div>
            <div class="mb-5">
                <label class="block text-sm font-semibold text-on-surface mb-2">Username</label>
                <input type="text" value="{{ $user->username }}"
                    class="w-full border border-outline-variant bg-surface-container-low rounded-lg p-3 text-on-surface-variant cursor-not-allowed" disabled>
                <p class="text-xs text-outline mt-1">Username tidak dapat diubah.</p>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-primary text-on-primary rounded-lg font-bold hover:bg-primary/90 transition shadow-sm">
                    Simpan Nama
                </button>
            </div>
        </form>
    </div>

    {{-- ============================================================
         FORM 2: Ubah Password (selalu aktif, ini yang wajib diisi)
    ============================================================ --}}
    <div class="bg-surface-container-lowest rounded-xl border shadow-sm p-6
                {{ auth()->user()->role === 'siswa' && !auth()->user()->is_password_changed
                    ? 'border-amber-300 ring-2 ring-amber-200'
                    : 'border-surface-container' }}">
        <h3 class="text-lg font-bold text-on-surface mb-5 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">lock_reset</span>
            Ubah Kata Sandi
            @if(auth()->user()->role === 'siswa' && !auth()->user()->is_password_changed)
                <span class="ml-auto text-[10px] font-bold px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full uppercase tracking-wider">Wajib</span>
            @endif
        </h3>

        <form action="{{ route('profile.password.update') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-on-surface mb-2">Kata Sandi Saat Ini</label>
                <input type="password" name="current_password" id="current_password"
                    class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-2 focus:ring-primary focus:border-primary text-on-surface transition"
                    placeholder="Masukkan kata sandi saat ini" required>
                @error('current_password')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-on-surface mb-2">Kata Sandi Baru</label>
                <input type="password" name="new_password" id="new_password"
                    class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-2 focus:ring-primary focus:border-primary text-on-surface transition"
                    placeholder="Minimal 6 karakter" required>
            </div>
            <div class="mb-5">
                <label class="block text-sm font-semibold text-on-surface mb-2">Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                    class="w-full border border-outline-variant bg-surface rounded-lg p-3 focus:ring-2 focus:ring-primary focus:border-primary text-on-surface transition"
                    placeholder="Ulangi kata sandi baru" required>
            </div>
            <div class="flex justify-end">
                <button type="submit"
                    class="w-full px-6 py-2.5 font-bold rounded-lg transition shadow-sm
                        {{ auth()->user()->role === 'siswa' && !auth()->user()->is_password_changed
                            ? 'bg-amber-500 hover:bg-amber-600 text-white'
                            : 'bg-primary hover:bg-primary/90 text-on-primary' }}">
                    <span class="flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">key</span>
                        @if(auth()->user()->role === 'siswa' && !auth()->user()->is_password_changed)
                            Ganti Password & Mulai Belajar
                        @else
                            Perbarui Kata Sandi
                        @endif
                    </span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
