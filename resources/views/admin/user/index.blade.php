@extends('layouts.admin')
@section('title', 'Kelola Pengguna - Admin LMS')
@section('page_title', 'Kelola Pengguna')

@section('content')
<div x-data="{ importModalOpen: false }">

    {{-- ===== HEADER ===== --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-on-surface">Kelola Pengguna</h2>
            <p class="text-on-surface-variant mt-1">Manajemen akun guru, siswa, dan admin sistem.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.user.template') }}" class="flex items-center gap-2 px-4 py-2 bg-surface-container border border-outline-variant text-on-surface rounded-lg text-sm hover:bg-surface-container-high transition-colors">
                <span class="material-symbols-outlined text-sm">download</span> Template CSV
            </a>
            <button @click="importModalOpen = true" class="flex items-center gap-2 px-4 py-2 bg-surface-container border border-primary text-primary rounded-lg text-sm hover:bg-primary/5 transition-colors font-semibold">
                <span class="material-symbols-outlined text-sm">upload_file</span> Import Siswa
            </button>
            <a href="{{ route('admin.user.create') }}" class="flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-lg text-sm hover:bg-primary/90 transition-colors font-semibold shadow-sm">
                <span class="material-symbols-outlined text-sm">person_add</span> Tambah User
            </a>
        </div>
    </div>

    {{-- ===== ALERT ===== --}}
    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 p-4 bg-primary-container text-on-primary-container rounded-xl">
            <span class="material-symbols-outlined">check_circle</span>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('warning'))
        <div class="mb-4 flex items-center gap-3 p-4 bg-tertiary-container text-on-tertiary-container rounded-xl">
            <span class="material-symbols-outlined">warning</span>
            <p class="font-medium">{{ session('warning') }}</p>
        </div>
    @endif

    {{-- ===== STATS CARDS ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-surface-container-lowest rounded-xl p-5 border border-surface-container flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 bg-primary-container rounded-xl flex items-center justify-center text-on-primary-container">
                <span class="material-symbols-outlined text-2xl">school</span>
            </div>
            <div>
                <p class="text-xs text-on-surface-variant font-semibold uppercase tracking-wide">Total Siswa</p>
                <p class="text-3xl font-black text-on-surface">{{ $totalSiswa }}</p>
            </div>
        </div>
        <div class="bg-surface-container-lowest rounded-xl p-5 border border-surface-container flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 bg-tertiary-container rounded-xl flex items-center justify-center text-on-tertiary-container">
                <span class="material-symbols-outlined text-2xl">assignment_ind</span>
            </div>
            <div>
                <p class="text-xs text-on-surface-variant font-semibold uppercase tracking-wide">Total Guru</p>
                <p class="text-3xl font-black text-on-surface">{{ $totalGuru }}</p>
            </div>
        </div>
        <div class="bg-surface-container-lowest rounded-xl p-5 border border-surface-container flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 bg-[#d4af37]/20 rounded-xl flex items-center justify-center text-[#8f6d00]">
                <span class="material-symbols-outlined text-2xl">manage_accounts</span>
            </div>
            <div>
                <p class="text-xs text-on-surface-variant font-semibold uppercase tracking-wide">Total Admin</p>
                <p class="text-3xl font-black text-on-surface">{{ $totalAdmin }}</p>
            </div>
        </div>
    </div>

    {{-- ===== SEARCH & FILTER ===== --}}
    <form method="GET" action="{{ route('admin.user.index') }}" class="bg-surface-container-lowest rounded-xl border border-surface-container p-4 mb-4 flex flex-col sm:flex-row gap-3 items-center shadow-sm">
        <div class="relative flex-1 w-full">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline-variant text-sm">search</span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="w-full pl-10 pr-4 py-2 border border-outline-variant bg-surface rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary"
                   placeholder="Cari nama atau username...">
        </div>
        <select name="role" class="border border-outline-variant bg-surface rounded-lg px-3 py-2 text-sm focus:ring-primary focus:border-primary text-on-surface min-w-[130px]">
            <option value="">Semua Role</option>
            <option value="siswa"  {{ request('role') == 'siswa'  ? 'selected' : '' }}>Siswa</option>
            <option value="guru"   {{ request('role') == 'guru'   ? 'selected' : '' }}>Guru</option>
            <option value="admin"  {{ request('role') == 'admin'  ? 'selected' : '' }}>Admin</option>
        </select>
        <select name="kelas_id" class="border border-outline-variant bg-surface rounded-lg px-3 py-2 text-sm focus:ring-primary focus:border-primary text-on-surface min-w-[150px]">
            <option value="">Semua Kelas</option>
            @foreach($kelasList as $k)
                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg text-sm font-semibold hover:bg-primary/90 transition-colors whitespace-nowrap">
            Filter
        </button>
        <a href="{{ route('admin.user.index') }}" class="px-4 py-2 bg-surface-container text-on-surface rounded-lg text-sm hover:bg-surface-container-high transition-colors whitespace-nowrap">
            Reset
        </a>
    </form>

    {{-- ===== TABLE ===== --}}
    <div class="bg-surface-container-lowest rounded-xl border border-surface-container overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant uppercase text-xs tracking-wider border-b border-outline-variant">
                        <th class="px-4 py-3 font-semibold">#</th>
                        <th class="px-4 py-3 font-semibold">Nama</th>
                        <th class="px-4 py-3 font-semibold">Username / NISN</th>
                        <th class="px-4 py-3 font-semibold">Role</th>
                        <th class="px-4 py-3 font-semibold">Kelas</th>
                        <th class="px-4 py-3 font-semibold">Bergabung</th>
                        <th class="px-4 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container">
                    @forelse($users as $user)
                    <tr class="hover:bg-surface/40 transition-colors">
                        <td class="px-4 py-3 text-on-surface-variant">{{ $users->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=006948&color=fff&size=36"
                                     class="w-9 h-9 rounded-full flex-shrink-0" alt="{{ $user->name }}">
                                <span class="font-semibold text-on-surface">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-on-surface-variant font-mono text-xs">{{ $user->username }}</td>
                        <td class="px-4 py-3">
                            @php
                                $roleStyle = match($user->role) {
                                    'admin'  => 'bg-[#d4af37]/20 text-[#8f6d00]',
                                    'guru'   => 'bg-tertiary-container text-on-tertiary-container',
                                    default  => 'bg-primary-container text-on-primary-container',
                                };
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $roleStyle }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-on-surface-variant">{{ $user->kelas->nama_kelas ?? '-' }}</td>
                        <td class="px-4 py-3 text-on-surface-variant">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.user.show', $user->id) }}"
                                   class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary/10 rounded-md transition-colors" title="Lihat Detail">
                                    <span class="material-symbols-outlined text-sm">visibility</span>
                                </a>
                                <a href="{{ route('admin.user.edit', $user->id) }}"
                                   class="p-1.5 text-on-surface-variant hover:text-tertiary hover:bg-tertiary/10 rounded-md transition-colors" title="Edit">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                </a>
                                <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST"
                                      onsubmit="return confirm('Yakin hapus user {{ addslashes($user->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error/10 rounded-md transition-colors" title="Hapus">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl mb-2 block">person_search</span>
                            Tidak ada pengguna yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="px-4 py-3 border-t border-surface-container">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- ===== IMPORT MODAL ===== --}}
    <div x-show="importModalOpen" x-cloak style="display:none"
         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div @click.outside="importModalOpen = false" class="bg-surface rounded-xl shadow-xl w-full max-w-lg p-6">
            <div class="flex justify-between items-center mb-5">
                <h3 class="font-bold text-lg text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined">upload_file</span> Import Siswa via Excel/CSV
                </h3>
                <button @click="importModalOpen = false" class="text-on-surface-variant hover:text-error">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="bg-surface-container-low rounded-lg p-4 border border-outline-variant mb-4">
                <p class="text-xs text-on-surface-variant mb-2">Gunakan template berikut agar format sesuai:</p>
                <a href="{{ route('admin.user.template') }}" class="inline-flex items-center gap-1.5 text-xs font-bold bg-primary text-on-primary px-3 py-1.5 rounded-lg hover:bg-primary/90">
                    <span class="material-symbols-outlined text-sm">download</span> Download Template CSV
                </a>
            </div>

            <form action="{{ route('admin.user.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1">Pilih File Excel / CSV</label>
                    <input type="file" name="file_excel" required accept=".xlsx,.xls,.csv"
                           class="w-full border border-outline-variant bg-surface rounded-lg p-2 text-sm">
                    <p class="text-xs text-outline mt-1">Kolom: no, username, email, password, nama, id_kelas</p>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="importModalOpen = false"
                            class="px-4 py-2 bg-surface-container text-on-surface rounded-lg text-sm font-semibold hover:bg-surface-container-high">Batal</button>
                    <button type="submit"
                            class="px-4 py-2 bg-primary text-on-primary rounded-lg text-sm font-semibold hover:bg-primary/90">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
