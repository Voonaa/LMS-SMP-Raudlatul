@extends('layouts.admin')
@section('title', 'Detail Pengguna - Admin LMS')
@section('page_title', 'Detail Pengguna')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.user.index') }}" class="text-on-surface-variant hover:text-primary transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h2 class="text-xl font-bold text-on-surface">Detail Pengguna</h2>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl border border-surface-container shadow-sm overflow-hidden">
        {{-- Avatar Header --}}
        <div class="bg-gradient-to-r from-primary to-primary/80 p-8 flex flex-col items-center">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=ffffff&color=006948&size=96"
                 class="w-24 h-24 rounded-full border-4 border-white shadow-md" alt="{{ $user->name }}">
            <h3 class="text-xl font-bold text-on-primary mt-3">{{ $user->name }}</h3>
            <span class="mt-2 px-3 py-1 bg-white/20 text-on-primary rounded-full text-xs font-semibold uppercase">
                {{ $user->role }}
            </span>
        </div>

        {{-- Info --}}
        <div class="p-6 space-y-4">
            <div class="flex justify-between items-center py-3 border-b border-surface-container">
                <span class="text-sm font-semibold text-on-surface-variant flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">badge</span> Username / NISN
                </span>
                <span class="font-mono font-bold text-on-surface">{{ $user->username }}</span>
            </div>
            <div class="flex justify-between items-center py-3 border-b border-surface-container">
                <span class="text-sm font-semibold text-on-surface-variant flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">school</span> Kelas
                </span>
                <span class="font-semibold text-on-surface">{{ $user->kelas->nama_kelas ?? '-' }}</span>
            </div>
            <div class="flex justify-between items-center py-3 border-b border-surface-container">
                <span class="text-sm font-semibold text-on-surface-variant flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">calendar_today</span> Bergabung
                </span>
                <span class="font-semibold text-on-surface">{{ $user->created_at->format('d F Y') }}</span>
            </div>
        </div>

        {{-- Actions --}}
        <div class="px-6 pb-6 flex gap-3">
            <a href="{{ route('admin.user.edit', $user->id) }}"
               class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-primary text-on-primary rounded-xl font-semibold text-sm hover:bg-primary/90 transition-colors">
                <span class="material-symbols-outlined text-sm">edit</span> Edit
            </a>
            <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST"
                  onsubmit="return confirm('Yakin hapus user ini?')" class="flex-1">
                @csrf @method('DELETE')
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 py-2.5 bg-error-container text-on-error-container rounded-xl font-semibold text-sm hover:bg-error/20 transition-colors">
                    <span class="material-symbols-outlined text-sm">delete</span> Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
