@extends('layouts.siswa')

@section('title', 'Pusat Pencapaian - Siswa')
@section('page_title', 'Pusat Pencapaian')

@section('content')
<div x-data="{ activeTab: 'badges' }" class="space-y-6 max-w-5xl mx-auto pb-12">
    
    <!-- Hero Section: Ringkasan Poin -->
    <div class="bg-gradient-to-br from-primary to-[#00553A] rounded-2xl p-8 text-on-primary shadow-lg relative overflow-hidden">
        <!-- Dekorasi Background -->
        <div class="absolute top-0 right-0 opacity-10">
            <span class="material-symbols-outlined text-[150px] transform rotate-12 translate-x-4 -translate-y-4">workspace_premium</span>
        </div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="text-center md:text-left">
                <h2 class="text-3xl font-bold mb-2">Halo, {{ $user->name }}!</h2>
                <p class="text-primary-container/80 text-lg">Terus tingkatkan prestasimu dan raih lencana tertinggi.</p>
                <div class="mt-4 flex items-center justify-center md:justify-start gap-4 text-sm font-medium">
                    <span class="bg-black/20 px-3 py-1.5 rounded-full flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">local_fire_department</span>
                        {{ $streak }} Hari Streak
                    </span>
                    <span class="bg-black/20 px-3 py-1.5 rounded-full flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">leaderboard</span>
                        Peringkat {{ $myRank }} di Kelas
                    </span>
                </div>
            </div>
            
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 text-center border border-white/20 min-w-[200px]">
                <p class="text-sm text-primary-container font-semibold uppercase tracking-wider mb-1">Total Poin</p>
                <div class="text-5xl font-black text-[#F3E5AB] drop-shadow-md flex items-center justify-center gap-2">
                    {{ $poin }}
                    <span class="material-symbols-outlined text-4xl text-[#D4AF37]">stars</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js Tabs Navigation -->
    <div class="flex border-b border-outline-variant/30 mt-8">
        <button @click="activeTab = 'badges'" 
                :class="activeTab === 'badges' ? 'border-primary text-primary font-bold' : 'border-transparent text-on-surface-variant hover:text-on-surface hover:border-outline-variant'"
                class="px-6 py-3 border-b-2 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">military_tech</span>
            Koleksi Badges
        </button>
        <button @click="activeTab = 'leaderboard'" 
                :class="activeTab === 'leaderboard' ? 'border-primary text-primary font-bold' : 'border-transparent text-on-surface-variant hover:text-on-surface hover:border-outline-variant'"
                class="px-6 py-3 border-b-2 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">format_list_numbered</span>
            Peringkat Kelas
        </button>
    </div>

    <!-- Tab Content: Koleksi Badges -->
    <div x-show="activeTab === 'badges'" x-transition.opacity class="pt-4">
        <div class="mb-6">
            <h3 class="text-xl font-bold text-on-surface">Lencana Pencapaian</h3>
            <p class="text-on-surface-variant text-sm mt-1">Selesaikan misi untuk membuka lencana baru.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($badges as $badge)
                <div class="bg-surface-container-lowest border {{ $badge['terbuka'] ? 'border-primary/30 shadow-md shadow-primary/5' : 'border-outline-variant/30' }} rounded-xl p-5 flex flex-col items-center text-center transition-all hover:scale-[1.02]">
                    <!-- Ikon Badge dengan filter Grayscale jika belum didapat -->
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-3 {{ $badge['terbuka'] ? 'bg-gradient-to-br from-[#D4AF37] to-[#F3E5AB] text-[#6B570A]' : 'bg-surface-container-high text-outline grayscale opacity-50' }}">
                        <span class="material-symbols-outlined text-3xl">{{ $badge['ikon'] }}</span>
                    </div>
                    
                    <h4 class="font-bold text-lg {{ $badge['terbuka'] ? 'text-on-surface' : 'text-on-surface-variant grayscale opacity-60' }}">
                        {{ $badge['nama'] }}
                    </h4>
                    
                    <p class="text-sm mt-2 {{ $badge['terbuka'] ? 'text-on-surface-variant' : 'text-outline opacity-70' }}">
                        {{ $badge['deskripsi'] }}
                    </p>

                    @if(!$badge['terbuka'])
                        <div class="mt-4 pt-4 border-t border-outline-variant/30 w-full">
                            <p class="text-xs font-semibold text-error/80 flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">lock</span>
                                Terkunci
                            </p>
                            <p class="text-[11px] text-outline mt-1">
                                Butuh: 
                                @if($badge['syarat_poin'] > 0) {{ $badge['syarat_poin'] }} Poin @endif
                                @if($badge['syarat_poin'] > 0 && $badge['syarat_streak'] > 0) & @endif
                                @if($badge['syarat_streak'] > 0) {{ $badge['syarat_streak'] }} Hari Streak @endif
                            </p>
                        </div>
                    @else
                        <div class="mt-4 pt-4 border-t border-primary/10 w-full">
                            <p class="text-xs font-bold text-primary flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                Tercapai
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Tab Content: Leaderboard -->
    <div x-show="activeTab === 'leaderboard'" x-transition.opacity style="display: none;" class="pt-4">
        <div class="mb-6 flex justify-between items-end">
            <div>
                <h3 class="text-xl font-bold text-on-surface">Papan Peringkat Kelas</h3>
                <p class="text-on-surface-variant text-sm mt-1">Top 10 siswa dengan poin tertinggi di kelas {{ $user->kelas->nama_kelas ?? '' }}.</p>
            </div>
            <div class="hidden sm:block">
                <span class="bg-surface-container-high text-on-surface text-xs font-bold px-3 py-1.5 rounded-lg border border-outline-variant">
                    Peringkatmu: #{{ $myRank }}
                </span>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low text-on-surface-variant text-sm uppercase tracking-wider border-b border-surface-container">
                            <th class="p-4 font-semibold text-center w-16">Peringkat</th>
                            <th class="p-4 font-semibold">Nama Siswa</th>
                            <th class="p-4 font-semibold text-right">Total Poin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container text-body-md">
                        @foreach($leaderboard as $index => $siswa)
                            @php
                                $isCurrentUser = $siswa['user_id'] === $user->id;
                                $rank = $siswa['rank'];
                            @endphp
                            
                            <!-- Highlight baris pengguna saat ini -->
                            <tr class="transition-colors {{ $isCurrentUser ? 'bg-primary/5 border-l-4 border-l-primary' : 'hover:bg-surface/50 border-l-4 border-l-transparent' }}">
                                <td class="p-4 text-center">
                                    @if($rank === 1)
                                        <div class="w-8 h-8 mx-auto rounded-full bg-[#D4AF37] text-white flex items-center justify-center font-bold shadow-md">1</div>
                                    @elseif($rank === 2)
                                        <div class="w-8 h-8 mx-auto rounded-full bg-[#C0C0C0] text-white flex items-center justify-center font-bold shadow-md">2</div>
                                    @elseif($rank === 3)
                                        <div class="w-8 h-8 mx-auto rounded-full bg-[#CD7F32] text-white flex items-center justify-center font-bold shadow-md">3</div>
                                    @else
                                        <div class="w-8 h-8 mx-auto flex items-center justify-center font-bold text-on-surface-variant">{{ $rank }}</div>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $isCurrentUser ? 'bg-primary text-on-primary' : 'bg-surface-container-high text-on-surface' }} font-bold text-sm">
                                            {{ strtoupper(substr($siswa['name'], 0, 1)) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-semibold {{ $isCurrentUser ? 'text-primary' : 'text-on-surface' }}">
                                                {{ $siswa['name'] }}
                                                @if($isCurrentUser) <span class="text-xs bg-primary text-white px-2 py-0.5 rounded-full ml-2">Kamu</span> @endif
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5 font-bold {{ $isCurrentUser ? 'text-primary' : 'text-on-surface' }}">
                                        {{ $siswa['points'] }}
                                        <span class="material-symbols-outlined text-[18px] {{ $isCurrentUser ? 'text-[#D4AF37]' : 'text-[#D4AF37]' }}">stars</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        
                        @if(count($leaderboard) === 0)
                            <tr>
                                <td colspan="3" class="p-8 text-center text-on-surface-variant">
                                    Belum ada data peringkat untuk kelas ini.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
