@extends('layouts.admin')

@section('title', 'Kelola Pengguna - Admin')
@section('page_title', 'Kelola Pengguna')

@section('content')
<div class="mb-lg flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="font-headline-xl text-headline-xl text-on-surface">Daftar Pengguna</h2>
        <p class="font-body-lg text-body-lg text-on-surface-variant mt-2">Kelola data siswa, guru, dan admin dalam sistem.</p>
    </div>
    <div class="flex gap-4">
        <a href="{{ route('admin.user.create') }}" class="flex items-center gap-2 bg-primary text-on-primary px-4 py-2 rounded-lg font-bold hover:bg-primary-container transition-colors shadow-sm">
            <span class="material-symbols-outlined">add</span>
            Tambah User
        </a>
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
                    <th class="p-4 font-semibold">Nama</th>
                    <th class="p-4 font-semibold">Username</th>
                    <th class="p-4 font-semibold">Role</th>
                    <th class="p-4 font-semibold">Kelas</th>
                    <th class="p-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container text-body-md">
                @forelse($users ?? [] as $u)
                    <tr class="hover:bg-surface/50 transition-colors">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($u->username) }}&background=006948&color=fff&rounded=true" class="w-8 h-8 rounded-full">
                                <span class="font-medium text-on-surface">{{ $u->name }}</span>
                            </div>
                        </td>
                        <td class="p-4 text-on-surface-variant">{{ $u->username }}</td>
                        <td class="p-4 text-on-surface-variant">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $u->role === 'admin' ? 'bg-error-container text-error' : ($u->role === 'guru' ? 'bg-tertiary-container text-on-tertiary-container' : 'bg-surface-container-high text-on-surface') }} capitalize">
                                {{ $u->role }}
                            </span>
                        </td>
                        <td class="p-4 text-on-surface-variant">{{ $u->kelas->nama_kelas ?? '-' }}</td>
                        <td class="p-4 text-right">
                            @if($u->id !== auth()->id())
                            <form action="{{ route('admin.user.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container rounded-md transition-colors" title="Hapus">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-on-surface-variant py-8">
                            <span class="material-symbols-outlined text-4xl mb-2">group_off</span>
                            <p>Belum ada data pengguna.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
