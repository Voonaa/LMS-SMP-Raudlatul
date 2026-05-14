@extends('layouts.admin')
@section('title', 'Dashboard Admin - LMS Raudlatul Hikmah')
@section('page_title', 'Dashboard Admin')

@section('styles')
<style>
    .islamic-pattern-bg { background-image: url('data:image/svg+xml;utf8,<svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"><path d="M20 0l20 20-20 20L0 20z" fill="rgba(255, 255, 255, 0.05)" fill-rule="evenodd"/></svg>'); }
    .glass-card { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
</style>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-8 pb-12">

    {{-- ===== HERO BANNER ===== --}}
    <div class="bg-gradient-to-br from-primary via-[#00553A] to-[#003B28] rounded-3xl p-8 text-on-primary shadow-[0_8px_30px_rgb(0,105,72,0.2)] relative overflow-hidden islamic-pattern-bg">
        <!-- Dekorasi Background -->
        <div class="absolute top-0 right-0 opacity-20 transform translate-x-1/4 -translate-y-1/4 pointer-events-none">
            <span class="material-symbols-outlined text-[250px]">admin_panel_settings</span>
        </div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 text-xs font-semibold tracking-wide mb-4">
                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                    Sistem Beroperasi Normal
                </div>
                <h2 class="text-3xl md:text-4xl font-extrabold mb-2 text-white drop-shadow-md">Selamat Datang, Admin 👋</h2>
                <p class="text-primary-container/90 text-lg max-w-xl">
                    Pusat kendali utama LMS SMP Islam Raudlatul Hikmah. Pantau metrik, kelola pengguna, dan atur konfigurasi sistem dari sini.
                </p>
            </div>
            
            <div class="hidden md:flex flex-col gap-3">
                <div class="glass-card rounded-2xl p-4 flex items-center gap-4 min-w-[220px]">
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-2xl">event</span>
                    </div>
                    <div>
                        <p class="text-xs text-white/70 font-semibold uppercase">Hari Ini</p>
                        <p class="text-lg font-bold text-white">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== STATS CARDS ===== --}}
    <div>
        <h3 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">monitoring</span>
            Ringkasan Sistem
        </h3>
        
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
            <!-- Card 1: Siswa -->
            <div class="bg-surface-container-lowest rounded-2xl border-l-4 border-l-primary p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110">
                    <span class="material-symbols-outlined text-[100px] text-primary">school</span>
                </div>
                <div class="relative z-10 flex flex-col gap-1">
                    <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-2">
                        <span class="material-symbols-outlined text-xl">school</span>
                    </div>
                    <p class="text-3xl font-black text-on-surface">{{ $totalSiswa ?? 0 }}</p>
                    <p class="text-sm font-semibold text-on-surface-variant">Total Siswa</p>
                </div>
            </div>

            <!-- Card 2: Guru -->
            <div class="bg-surface-container-lowest rounded-2xl border-l-4 border-l-[#D4AF37] p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110">
                    <span class="material-symbols-outlined text-[100px] text-[#D4AF37]">assignment_ind</span>
                </div>
                <div class="relative z-10 flex flex-col gap-1">
                    <div class="w-10 h-10 bg-[#D4AF37]/10 rounded-xl flex items-center justify-center text-[#8f6d00] mb-2">
                        <span class="material-symbols-outlined text-xl">assignment_ind</span>
                    </div>
                    <p class="text-3xl font-black text-on-surface">{{ $totalGuru ?? 0 }}</p>
                    <p class="text-sm font-semibold text-on-surface-variant">Total Guru</p>
                </div>
            </div>

            <!-- Card 3: Kelas -->
            <div class="bg-surface-container-lowest rounded-2xl border-l-4 border-l-blue-500 p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110">
                    <span class="material-symbols-outlined text-[100px] text-blue-500">class</span>
                </div>
                <div class="relative z-10 flex flex-col gap-1">
                    <div class="w-10 h-10 bg-blue-500/10 rounded-xl flex items-center justify-center text-blue-600 mb-2">
                        <span class="material-symbols-outlined text-xl">class</span>
                    </div>
                    <p class="text-3xl font-black text-on-surface">{{ $totalKelas ?? 0 }}</p>
                    <p class="text-sm font-semibold text-on-surface-variant">Total Kelas</p>
                </div>
            </div>

            <!-- Card 4: Materi -->
            <div class="bg-surface-container-lowest rounded-2xl border-l-4 border-l-emerald-500 p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110">
                    <span class="material-symbols-outlined text-[100px] text-emerald-500">menu_book</span>
                </div>
                <div class="relative z-10 flex flex-col gap-1">
                    <div class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-600 mb-2">
                        <span class="material-symbols-outlined text-xl">menu_book</span>
                    </div>
                    <p class="text-3xl font-black text-on-surface">{{ $totalMateri ?? 0 }}</p>
                    <p class="text-sm font-semibold text-on-surface-variant">Materi Edukasi</p>
                </div>
            </div>

            <!-- Card 5: Kuis -->
            <div class="bg-surface-container-lowest rounded-2xl border-l-4 border-l-orange-500 p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110">
                    <span class="material-symbols-outlined text-[100px] text-orange-500">quiz</span>
                </div>
                <div class="relative z-10 flex flex-col gap-1">
                    <div class="w-10 h-10 bg-orange-500/10 rounded-xl flex items-center justify-center text-orange-600 mb-2">
                        <span class="material-symbols-outlined text-xl">quiz</span>
                    </div>
                    <p class="text-3xl font-black text-on-surface">{{ $totalKuis ?? 0 }}</p>
                    <p class="text-sm font-semibold text-on-surface-variant">Kuis Aktif</p>
                </div>
            </div>

            <!-- Card 6: Log Aktivitas -->
            <div class="bg-surface-container-lowest rounded-2xl border-l-4 border-l-purple-500 p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110">
                    <span class="material-symbols-outlined text-[100px] text-purple-500">history</span>
                </div>
                <div class="relative z-10 flex flex-col gap-1">
                    <div class="w-10 h-10 bg-purple-500/10 rounded-xl flex items-center justify-center text-purple-600 mb-2">
                        <span class="material-symbols-outlined text-xl">history</span>
                    </div>
                    <p class="text-3xl font-black text-on-surface">{{ $totalAktivitas ?? 0 }}</p>
                    <p class="text-sm font-semibold text-on-surface-variant">Log Aktivitas</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== MENU CEPAT (QUICK ACTIONS) ===== --}}
    <div>
        <h3 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[#D4AF37]">bolt</span>
            Akses Cepat
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Action 1 -->
            <a href="{{ route('admin.user.index') }}" class="group relative bg-surface-container-lowest rounded-2xl border border-surface-container overflow-hidden hover:border-primary hover:shadow-[0_8px_30px_rgb(0,105,72,0.12)] transition-all duration-300 transform hover:-translate-y-1">
                <div class="absolute top-0 left-0 w-1 h-full bg-primary transform origin-bottom scale-y-0 group-hover:scale-y-100 transition-transform duration-300"></div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-3xl">manage_accounts</span>
                        </div>
                        <span class="material-symbols-outlined text-outline group-hover:text-primary transition-colors">arrow_forward</span>
                    </div>
                    <h2 class="text-xl font-bold text-on-surface mb-2 group-hover:text-primary transition-colors">Kelola Pengguna</h2>
                    <p class="text-on-surface-variant text-sm leading-relaxed">Pusat manajemen akun. Tambah, edit, hapus, atau *import* massal data Guru dan Siswa via CSV.</p>
                </div>
            </a>

            <!-- Action 2 -->
            <a href="{{ route('admin.config.index') }}" class="group relative bg-surface-container-lowest rounded-2xl border border-surface-container overflow-hidden hover:border-[#D4AF37] hover:shadow-[0_8px_30px_rgba(212,175,55,0.15)] transition-all duration-300 transform hover:-translate-y-1">
                <div class="absolute top-0 left-0 w-1 h-full bg-[#D4AF37] transform origin-bottom scale-y-0 group-hover:scale-y-100 transition-transform duration-300"></div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-[#D4AF37]/10 rounded-2xl flex items-center justify-center text-[#8f6d00] group-hover:bg-[#D4AF37] group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-3xl">settings</span>
                        </div>
                        <span class="material-symbols-outlined text-outline group-hover:text-[#D4AF37] transition-colors">arrow_forward</span>
                    </div>
                    <h2 class="text-xl font-bold text-on-surface mb-2 group-hover:text-[#D4AF37] transition-colors">Konfigurasi Sistem</h2>
                    <p class="text-on-surface-variant text-sm leading-relaxed">Atur variabel global LMS seperti data referensi Master Mata Pelajaran dan Kelas.</p>
                </div>
            </a>

            <!-- Action 3 -->
            <a href="{{ route('admin.testing.mae') }}" class="group relative bg-surface-container-lowest rounded-2xl border border-surface-container overflow-hidden hover:border-blue-500 hover:shadow-[0_8px_30px_rgba(59,130,246,0.15)] transition-all duration-300 transform hover:-translate-y-1">
                <div class="absolute top-0 left-0 w-1 h-full bg-blue-500 transform origin-bottom scale-y-0 group-hover:scale-y-100 transition-transform duration-300"></div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-600 group-hover:bg-blue-500 group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-3xl">analytics</span>
                        </div>
                        <span class="material-symbols-outlined text-outline group-hover:text-blue-500 transition-colors">arrow_forward</span>
                    </div>
                    <h2 class="text-xl font-bold text-on-surface mb-2 group-hover:text-blue-500 transition-colors">Diagnosa Algoritma</h2>
                    <p class="text-on-surface-variant text-sm leading-relaxed">Pengujian akurasi sistem rekomendasi Collaborative Filtering. Penting untuk validasi sistem.</p>
                </div>
            </a>

        </div>
    </div>

</div>
@endsection
