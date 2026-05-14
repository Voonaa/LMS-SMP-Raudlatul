<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title', 'LMS SMP Islam Raudlatul Hikmah')</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              "colors": {
                      "inverse-primary": "#68dba9",
                      "surface-container-lowest": "#ffffff",
                      "surface-variant": "#d3e4fe",
                      "surface-container-highest": "#d3e4fe",
                      "on-tertiary-fixed-variant": "#574500",
                      "primary": "#006948",
                      "primary-container": "#00855d",
                      "on-secondary-container": "#616363",
                      "on-tertiary-container": "#4e3d00",
                      "secondary-container": "#dfe0e0",
                      "tertiary-fixed": "#ffe088",
                      "surface-container-high": "#dce9ff",
                      "outline": "#6d7a72",
                      "on-tertiary-fixed": "#241a00",
                      "on-tertiary": "#ffffff",
                      "on-error-container": "#93000a",
                      "surface-container-low": "#eff4ff",
                      "on-primary-fixed": "#002114",
                      "primary-fixed": "#85f8c4",
                      "tertiary-fixed-dim": "#e9c349",
                      "error": "#ba1a1a",
                      "on-surface": "#0b1c30",
                      "background": "#f8f9ff",
                      "secondary": "#5d5f5f",
                      "on-primary-fixed-variant": "#005137",
                      "on-surface-variant": "#3d4a42",
                      "surface-dim": "#cbdbf5",
                      "on-error": "#ffffff",
                      "outline-variant": "#bccac0",
                      "surface": "#f8f9ff",
                      "on-background": "#0b1c30",
                      "tertiary-container": "#cba72f",
                      "surface-container": "#e5eeff",
                      "on-secondary": "#ffffff",
                      "secondary-fixed-dim": "#c6c6c7",
                      "tertiary": "#735c00",
                      "surface-tint": "#006c4a",
                      "on-secondary-fixed-variant": "#454747",
                      "on-primary-container": "#f5fff7",
                      "on-primary": "#ffffff",
                      "error-container": "#ffdad6",
                      "primary-fixed-dim": "#68dba9",
                      "inverse-surface": "#213145",
                      "secondary-fixed": "#e2e2e2",
                      "on-secondary-fixed": "#1a1c1c",
                      "surface-bright": "#f8f9ff",
                      "inverse-on-surface": "#eaf1ff"
              },
              "borderRadius": {
                      "DEFAULT": "0.25rem",
                      "lg": "0.5rem",
                      "xl": "0.75rem",
                      "full": "9999px"
              },
              "spacing": {
                      "gutter": "24px",
                      "margin": "32px",
                      "base": "8px",
                      "sm": "12px",
                      "xs": "4px",
                      "lg": "40px",
                      "md": "24px",
                      "xl": "64px"
              },
              "fontFamily": {
                      "headline-md": ["Inter"],
                      "label-sm": ["Inter"],
                      "body-lg": ["Inter"],
                      "headline-lg": ["Inter"],
                      "headline-xl": ["Inter"],
                      "body-md": ["Inter"],
                      "label-lg": ["Inter"]
              }
            }
          }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            line-height: 1;
            display: inline-block;
            white-space: nowrap;
            direction: ltr;
            -webkit-font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
        }
        [data-weight="fill"] { font-variation-settings: 'FILL' 1; }
        .ambient-shadow { box-shadow: 0 4px 6px -1px rgba(5, 150, 105, 0.05), 0 2px 4px -1px rgba(5, 150, 105, 0.03); }
        .islamic-pattern-bg { background-image: url('data:image/svg+xml;utf8,<svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"><path d="M20 0l20 20-20 20L0 20z" fill="rgba(0, 105, 72, 0.02)" fill-rule="evenodd"/></svg>'); }
        [x-cloak] { display: none !important; }
        @yield('styles')
    </style>
</head>
<body class="bg-background text-on-background font-body-md" x-data="{ mobileMenuOpen: false }">
    <!-- Mobile Top Header -->
    <header class="lg:hidden flex justify-between items-center bg-surface-container-lowest px-4 py-3 shadow-sm sticky top-0 z-40">
        <div class="flex items-center gap-2">
            <span @click="mobileMenuOpen = !mobileMenuOpen" class="material-symbols-outlined text-primary cursor-pointer">menu</span>
            <span class="font-headline-md text-headline-md font-bold text-primary">Raudlatul Hikmah</span>
        </div>
        <img alt="Profile" class="w-8 h-8 rounded-full border border-primary object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->username) }}&background=0D8ABC&color=fff&rounded=true" />
    </header>

    <!-- Sidebar Navigation -->
    <aside :class="mobileMenuOpen ? 'translate-x-0 flex' : '-translate-x-full lg:translate-x-0'" class="w-64 h-screen fixed left-0 top-0 overflow-y-auto shadow-sm bg-surface-container-lowest hidden lg:flex flex-col border-r border-outline-variant z-50 transform transition-transform duration-300 ease-in-out">
        <div class="p-6 flex flex-col items-center border-b border-surface-container">
            <div class="w-16 h-16 rounded-full bg-surface-container flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-primary text-4xl">local_library</span>
            </div>
            <h1 class="font-headline-md text-headline-md font-bold text-primary text-center">Raudlatul Hikmah</h1>
            <p class="font-label-sm text-label-sm text-on-surface-variant">Portal Siswa</p>
        </div>
        
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('siswa.dashboard') ? 'text-primary bg-surface-container-low border-l-4 border-primary rounded-r-lg font-bold' : 'text-on-surface-variant hover:bg-surface-container rounded-lg' }} font-label-lg text-label-lg transition-colors" href="{{ route('siswa.dashboard') }}">
                <span class="material-symbols-outlined">dashboard</span>
                Dashboard
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('siswa.materi.*') ? 'text-primary bg-surface-container-low border-l-4 border-primary rounded-r-lg font-bold' : 'text-on-surface-variant hover:bg-surface-container rounded-lg' }} font-label-lg text-label-lg transition-colors" href="{{ route('siswa.materi.index') }}">
                <span class="material-symbols-outlined">menu_book</span>
                Materi
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('siswa.kuis.*') ? 'text-primary bg-surface-container-low border-l-4 border-primary rounded-r-lg font-bold' : 'text-on-surface-variant hover:bg-surface-container rounded-lg' }} font-label-lg text-label-lg transition-colors" href="{{ route('siswa.kuis.index') }}">
                <span class="material-symbols-outlined">assignment</span>
                Kuis
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('siswa.tugas.*') ? 'text-primary bg-surface-container-low border-l-4 border-primary rounded-r-lg font-bold' : 'text-on-surface-variant hover:bg-surface-container rounded-lg' }} font-label-lg text-label-lg transition-colors" href="{{ route('siswa.tugas.index') }}">
                <span class="material-symbols-outlined">task</span>
                Tugas
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('forum.*') ? 'text-primary bg-surface-container-low border-l-4 border-primary rounded-r-lg font-bold' : 'text-on-surface-variant hover:bg-surface-container rounded-lg' }} font-label-lg text-label-lg transition-colors" href="{{ route('forum.index') }}">
                <span class="material-symbols-outlined">forum</span>
                Forum
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('siswa.achievements') ? 'text-primary bg-surface-container-low border-l-4 border-primary rounded-r-lg font-bold' : 'text-on-surface-variant hover:bg-surface-container rounded-lg' }} font-label-lg text-label-lg transition-colors" href="{{ route('siswa.achievements') }}">
                <span class="material-symbols-outlined">workspace_premium</span>
                Pusat Pencapaian
            </a>
        </nav>
        
        <div class="p-4 border-t border-surface-container">
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('profile.settings') ? 'text-primary bg-surface-container-low border-l-4 border-primary rounded-r-lg font-bold' : 'text-on-surface-variant hover:bg-surface-container rounded-lg' }} font-label-lg text-label-lg transition-colors" href="{{ route('profile.settings') }}">
                <span class="material-symbols-outlined">settings</span>
                Settings
            </a>
            <form action="{{ route('logout') }}" method="POST" class="w-full mt-2">
                @csrf
                <button type="submit" class="w-full py-2 px-4 border border-outline text-primary font-label-lg rounded-lg hover:bg-surface-container-low transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">logout</span>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Overlay for mobile sidebar -->
    <div @click="mobileMenuOpen = false" class="fixed inset-0 bg-on-background/20 z-40 lg:hidden" style="display: none;" x-show="mobileMenuOpen"></div>

    <main class="lg:ml-64 min-h-screen pb-24 lg:pb-0">
        <!-- Desktop TopAppBar -->
        <header class="hidden lg:flex justify-between items-center w-full px-margin py-base sticky top-0 z-30 bg-surface shadow-sm">
            <div class="flex-1">
                <h2 class="font-headline-md text-headline-md font-extrabold text-primary">@yield('page_title', 'Dashboard')</h2>
            </div>
            <div class="flex items-center gap-6">
                <button class="text-on-surface-variant hover:text-primary transition-all relative">
                    <span class="material-symbols-outlined">notifications</span>
                </button>
                <div class="flex items-center gap-3 cursor-pointer pl-4 border-l border-surface-container">
                    <div class="text-right hidden md:block">
                        <p class="font-label-lg text-label-lg text-on-surface">{{ auth()->user()->name }}</p>
                        <p class="font-label-sm text-label-sm text-on-surface-variant">Siswa Kelas {{ auth()->user()->kelas->tingkat ?? '' }}</p>
                    </div>
                    <img alt="User Avatar" class="w-10 h-10 rounded-full border-2 border-primary object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->username) }}&background=0D8ABC&color=fff&rounded=true" />
                </div>
            </div>
        </header>

        <!-- Main Canvas -->
        @yield('content')
    </main>

    <!-- Mobile Bottom NavBar -->
    <nav class="lg:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 py-2 bg-surface-container-lowest shadow-lg border-t border-outline-variant rounded-t-xl">
        <a class="flex flex-col items-center justify-center {{ request()->routeIs('siswa.dashboard') ? 'text-primary' : 'text-on-surface-variant hover:text-primary' }} transition-colors py-1 w-16" href="{{ route('siswa.dashboard') }}">
            <span class="material-symbols-outlined mb-1" style="{{ request()->routeIs('siswa.dashboard') ? 'font-variation-settings:\'FILL\' 1;' : 'font-variation-settings:\'FILL\' 0;' }}">home</span>
            <span class="font-label-sm text-[10px]">Beranda</span>
        </a>
        <a class="flex flex-col items-center justify-center {{ request()->routeIs('siswa.materi.*') ? 'text-primary' : 'text-on-surface-variant hover:text-primary' }} transition-colors py-1 w-16" href="{{ route('siswa.materi.index') }}">
            <span class="material-symbols-outlined mb-1" style="{{ request()->routeIs('siswa.materi.*') ? 'font-variation-settings:\'FILL\' 1;' : 'font-variation-settings:\'FILL\' 0;' }}">book</span>
            <span class="font-label-sm text-[10px]">Materi</span>
        </a>
        <a class="flex flex-col items-center justify-center {{ request()->routeIs('siswa.kuis.*') ? 'text-primary' : 'text-on-surface-variant hover:text-primary' }} transition-colors py-1 w-16" href="{{ route('siswa.kuis.index') }}">
            <span class="material-symbols-outlined mb-1" style="{{ request()->routeIs('siswa.kuis.*') ? 'font-variation-settings:\'FILL\' 1;' : 'font-variation-settings:\'FILL\' 0;' }}">quiz</span>
            <span class="font-label-sm text-[10px]">Kuis</span>
        </a>
        <a class="flex flex-col items-center justify-center {{ request()->routeIs('siswa.tugas.*') ? 'text-primary' : 'text-on-surface-variant hover:text-primary' }} transition-colors py-1 w-16" href="{{ route('siswa.tugas.index') }}">
            <span class="material-symbols-outlined mb-1" style="{{ request()->routeIs('siswa.tugas.*') ? 'font-variation-settings:\'FILL\' 1;' : 'font-variation-settings:\'FILL\' 0;' }}">task</span>
            <span class="font-label-sm text-[10px]">Tugas</span>
        </a>
        <a class="flex flex-col items-center justify-center {{ request()->routeIs('forum.*') ? 'text-primary' : 'text-on-surface-variant hover:text-primary' }} transition-colors py-1 w-14" href="{{ route('forum.index') }}">
            <span class="material-symbols-outlined mb-1" style="{{ request()->routeIs('forum.*') ? 'font-variation-settings:\'FILL\' 1;' : 'font-variation-settings:\'FILL\' 0;' }}">groups</span>
            <span class="font-label-sm text-[9px]">Forum</span>
        </a>
        <a class="flex flex-col items-center justify-center {{ request()->routeIs('siswa.achievements') ? 'text-primary' : 'text-on-surface-variant hover:text-primary' }} transition-colors py-1 w-14" href="{{ route('siswa.achievements') }}">
            <span class="material-symbols-outlined mb-1" style="{{ request()->routeIs('siswa.achievements') ? 'font-variation-settings:\'FILL\' 1;' : 'font-variation-settings:\'FILL\' 0;' }}">workspace_premium</span>
            <span class="font-label-sm text-[9px]">Badge</span>
        </a>
    </nav>
    
    @yield('scripts')
</body>
</html>
