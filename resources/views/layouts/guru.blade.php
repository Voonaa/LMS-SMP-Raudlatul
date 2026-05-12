<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title', 'Guru Portal - LMS')</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
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
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                    fontFamily: { "headline-md": ["Inter"], "label-sm": ["Inter"], "body-lg": ["Inter"], "headline-lg": ["Inter"], "headline-xl": ["Inter"], "body-md": ["Inter"], "label-lg": ["Inter"] }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined { font-family: 'Material Symbols Outlined'; font-weight: normal; font-style: normal; font-size: 24px; line-height: 1; display: inline-block; white-space: nowrap; direction: ltr; -webkit-font-feature-settings: 'liga'; -webkit-font-smoothing: antialiased; }
        [x-cloak] { display: none !important; }
        @yield('styles')
    </style>
</head>
<body class="bg-surface text-on-surface flex min-h-screen" x-data="{ sidebarOpen: false }">
    <!-- SideNavBar -->
    <aside :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" class="hidden lg:flex flex-col border-r border-outline-variant shadow-sm bg-surface-container-lowest w-64 h-screen fixed left-0 top-0 overflow-y-auto z-40 transition-transform transform lg:translate-x-0">
        <div class="px-6 py-6 border-b border-surface-container flex flex-col items-center justify-center space-y-2">
            <div class="w-16 h-16 rounded-full bg-surface-container flex items-center justify-center overflow-hidden border-2 border-primary/20">
                <span class="material-symbols-outlined text-primary text-4xl">local_library</span>
            </div>
            <div class="text-center">
                <h2 class="font-headline-md text-headline-md font-bold text-primary leading-tight">Raudlatul Hikmah</h2>
                <p class="font-label-sm text-label-sm text-on-surface-variant mt-1">Portal Guru</p>
            </div>
        </div>
        
        <!-- Navigation Links -->
        <nav class="flex-1 py-4 flex flex-col gap-1">
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('guru.dashboard') ? 'text-primary bg-surface-container-low border-l-4 border-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container transition-colors' }}" href="{{ route('guru.dashboard') }}">
                <span class="material-symbols-outlined" style="{{ request()->routeIs('guru.dashboard') ? 'font-variation-settings: \'FILL\' 1;' : 'font-variation-settings: \'FILL\' 0;' }}">dashboard</span>
                <span class="font-label-lg text-label-lg">Dashboard</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('guru.materi.*') ? 'text-primary bg-surface-container-low border-l-4 border-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container transition-colors' }}" href="{{ route('guru.materi.index') }}">
                <span class="material-symbols-outlined" style="{{ request()->routeIs('guru.materi.*') ? 'font-variation-settings: \'FILL\' 1;' : 'font-variation-settings: \'FILL\' 0;' }}">menu_book</span>
                <span class="font-label-lg text-label-lg">Materi</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('guru.kuis.*') ? 'text-primary bg-surface-container-low border-l-4 border-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container transition-colors' }}" href="{{ route('guru.kuis.index') }}">
                <span class="material-symbols-outlined" style="{{ request()->routeIs('guru.kuis.*') ? 'font-variation-settings: \'FILL\' 1;' : 'font-variation-settings: \'FILL\' 0;' }}">assignment</span>
                <span class="font-label-lg text-label-lg">Kuis</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('guru.tugas.*') ? 'text-primary bg-surface-container-low border-l-4 border-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container transition-colors' }}" href="{{ route('guru.tugas.index') }}">
                <span class="material-symbols-outlined" style="{{ request()->routeIs('guru.tugas.*') ? 'font-variation-settings: \'FILL\' 1;' : 'font-variation-settings: \'FILL\' 0;' }}">task</span>
                <span class="font-label-lg text-label-lg">Tugas</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('guru.laporan.*') ? 'text-primary bg-surface-container-low border-l-4 border-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container transition-colors' }}" href="{{ route('guru.laporan.index') }}">
                <span class="material-symbols-outlined" style="{{ request()->routeIs('guru.laporan.*') ? 'font-variation-settings: \'FILL\' 1;' : 'font-variation-settings: \'FILL\' 0;' }}">analytics</span>
                <span class="font-label-lg text-label-lg">Laporan</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('forum.*') ? 'text-primary bg-surface-container-low border-l-4 border-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container transition-colors' }}" href="{{ route('forum.index') }}">
                <span class="material-symbols-outlined" style="{{ request()->routeIs('forum.*') ? 'font-variation-settings: \'FILL\' 1;' : 'font-variation-settings: \'FILL\' 0;' }}">forum</span>
                <span class="font-label-lg text-label-lg">Forum</span>
            </a>
        </nav>
        
        <div class="p-4 border-t border-surface-container flex flex-col gap-1">
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('profile.settings') ? 'text-primary bg-surface-container-low border-l-4 border-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container transition-colors' }}" href="{{ route('profile.settings') }}">
                <span class="material-symbols-outlined">settings</span>
                <span class="font-label-lg text-label-lg">Settings</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="w-full mt-2">
                @csrf
                <button type="submit" class="w-full py-2 flex items-center justify-center gap-2 text-error border border-error rounded-lg hover:bg-error-container transition-colors font-label-lg text-label-lg">
                    <span class="material-symbols-outlined">logout</span>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Overlay -->
    <div @click="sidebarOpen = false" class="fixed inset-0 bg-black/20 z-30 lg:hidden" x-show="sidebarOpen" style="display: none;"></div>

    <div class="flex-1 lg:ml-64 flex flex-col min-h-screen overflow-x-hidden">
        <header class="flex justify-between items-center w-full px-6 py-3 sticky top-0 z-30 bg-surface shadow-sm">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 text-on-surface-variant rounded-md hover:bg-surface-container focus:outline-none">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <div class="flex items-center gap-4">
                <h1 class="font-headline-md text-headline-md font-extrabold text-primary hidden sm:block">@yield('page_title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-4">
                <button class="w-10 h-10 rounded-full overflow-hidden border-2 border-surface-container hover:border-primary transition-colors focus:outline-none">
                    <img alt="User Avatar" class="w-full h-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->username) }}&background=0D8ABC&color=fff&rounded=true" />
                </button>
            </div>
        </header>

        <main class="flex-1 p-6 bg-background">
            @yield('content')
        </main>
    </div>

    <!-- Mobile Bottom NavBar -->
    <nav class="lg:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 py-2 bg-surface-container-lowest shadow-[0_-4px_10px_rgba(0,0,0,0.05)] border-t border-outline-variant rounded-t-xl">
        <a class="flex flex-col items-center justify-center {{ request()->routeIs('guru.dashboard') ? 'text-primary' : 'text-on-surface-variant hover:text-primary' }} transition-colors py-1 w-16" href="{{ route('guru.dashboard') }}">
            <span class="material-symbols-outlined mb-1" style="{{ request()->routeIs('guru.dashboard') ? 'font-variation-settings: \'FILL\' 1;' : 'font-variation-settings: \'FILL\' 0;' }}">home</span>
            <span class="font-label-sm text-[10px]">Beranda</span>
        </a>
        <a class="flex flex-col items-center justify-center {{ request()->routeIs('guru.materi.*') ? 'text-primary' : 'text-on-surface-variant hover:text-primary' }} py-1 w-16" href="{{ route('guru.materi.index') }}">
            <span class="material-symbols-outlined mb-1" style="{{ request()->routeIs('guru.materi.*') ? 'font-variation-settings: \'FILL\' 1;' : 'font-variation-settings: \'FILL\' 0;' }}">book</span>
            <span class="font-label-sm text-[10px]">Materi</span>
        </a>
        <a class="flex flex-col items-center justify-center {{ request()->routeIs('guru.kuis.*') ? 'text-primary' : 'text-on-surface-variant hover:text-primary' }} py-1 w-16" href="{{ route('guru.kuis.index') }}">
            <span class="material-symbols-outlined mb-1" style="{{ request()->routeIs('guru.kuis.*') ? 'font-variation-settings: \'FILL\' 1;' : 'font-variation-settings: \'FILL\' 0;' }}">task</span>
            <span class="font-label-sm text-[10px]">Tugas</span>
        </a>
        <a class="flex flex-col items-center justify-center {{ request()->routeIs('forum.*') ? 'text-primary' : 'text-on-surface-variant hover:text-primary' }} py-1 w-16" href="{{ route('forum.index') }}">
            <span class="material-symbols-outlined mb-1" style="{{ request()->routeIs('forum.*') ? 'font-variation-settings: \'FILL\' 1;' : 'font-variation-settings: \'FILL\' 0;' }}">groups</span>
            <span class="font-label-sm text-[10px]">Forum</span>
        </a>
    </nav>
    
    @yield('scripts')
</body>
</html>
