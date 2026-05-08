<!DOCTYPE html>

<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Forum Diskusi - SMP Islam Raudlatul Hikmah</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script defer="" src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                    },
                    "fontSize": {
                        "headline-md": ["24px", { "lineHeight": "32px", "fontWeight": "600" }],
                        "label-sm": ["12px", { "lineHeight": "16px", "fontWeight": "500" }],
                        "body-lg": ["18px", { "lineHeight": "28px", "fontWeight": "400" }],
                        "headline-lg": ["32px", { "lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
                        "headline-xl": ["40px", { "lineHeight": "48px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "body-md": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
                        "label-lg": ["14px", { "lineHeight": "20px", "letterSpacing": "0.02em", "fontWeight": "600" }]
                    }
                }
            }
        }
    </script>
<style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9ff; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .material-symbols-outlined.fill { font-variation-settings: 'FILL' 1; }
        .ambient-shadow { box-shadow: 0 4px 20px rgba(5, 150, 105, 0.05); }
    </style>
</head>
<body class="bg-background text-on-background antialiased flex flex-col md:flex-row min-h-screen">
<!-- SideNavBar (Desktop) -->
<nav class="hidden lg:flex flex-col border-r border-outline-variant dark:border-outline shadow-sm bg-surface-container-lowest dark:bg-inverse-surface w-64 h-screen fixed left-0 top-0 overflow-y-auto z-40">
<div class="p-6">
<div class="flex items-center gap-3">
<img alt="SMP Islam Raudlatul Hikmah Logo" class="w-10 h-10 rounded-full object-cover" data-alt="A high-quality, professional logo for an Islamic school, featuring a stylized book and a growing tree or leaf motif in rich emerald green and gold colors. The logo is set against a clean white background, exuding a sense of growth, academic excellence, and Islamic tradition. Bright, modern lighting highlights the metallic gold accents." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCGpthRWNDhTb1rcpNLIL_LkDFnlaNKJO5ZN6NzwV1LvuNVjQYnIrYizPQ8_8MrJz6qQZWkexds3TsHerHS_tPum7TNWMxUpq1AgaBT_X4aEAsfRNeUrhDDYI9nPiKe-tC_gOIJ9mS1nGwDbKOEpK-KKkt6W2N5joe5ShdX-TQWS7ddg9WAMRflKpBK_EJt4n7wLEsdeCqxGPzxm_umNHfranCRZx7RTMosdVEbaMM5ZgZ8yozsZ0cL7QgVru1SvsGEq2OD47x4fxX1"/>
<div>
<h1 class="font-headline-md text-headline-md font-bold text-primary dark:text-inverse-primary text-sm">Raudlatul Hikmah</h1>
<p class="font-label-sm text-label-sm text-on-surface-variant">LMS Portal</p>
</div>
</div>
</div>
<div class="flex-1 px-4 py-2 space-y-2">
<a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant dark:text-surface-variant hover:bg-surface-container dark:hover:bg-secondary-container transition-colors rounded-lg font-label-lg text-label-lg" href="#">
<span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
                Dashboard
            </a>
<a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant dark:text-surface-variant hover:bg-surface-container dark:hover:bg-secondary-container transition-colors rounded-lg font-label-lg text-label-lg" href="#">
<span class="material-symbols-outlined" data-icon="menu_book">menu_book</span>
                Materi
            </a>
<a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant dark:text-surface-variant hover:bg-surface-container dark:hover:bg-secondary-container transition-colors rounded-lg font-label-lg text-label-lg" href="#">
<span class="material-symbols-outlined" data-icon="assignment">assignment</span>
                Tugas
            </a>
<a class="flex items-center gap-3 px-4 py-3 text-primary dark:text-inverse-primary bg-surface-container-low dark:bg-secondary border-l-4 border-primary dark:border-inverse-primary rounded-r-lg font-label-lg text-label-lg" href="#">
<span class="material-symbols-outlined" data-icon="forum">forum</span>
                Forum
            </a>
</div>
<div class="p-4 border-t border-outline-variant">
<a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container transition-colors rounded-lg font-label-lg text-label-lg" href="#">
<span class="material-symbols-outlined" data-icon="settings">settings</span>
                Settings
            </a>
<a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container transition-colors rounded-lg font-label-lg text-label-lg" href="#">
<span class="material-symbols-outlined" data-icon="help">help</span>
                Bantuan
            </a>
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="w-full mt-4">
    @csrf
    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 text-error hover:bg-error-container transition-colors rounded-lg font-label-lg text-label-lg font-medium">
        Keluar
    </button>
</form>
</div>
</nav>
<!-- Main Content Area -->
<div class="flex-1 lg:ml-64 flex flex-col min-h-screen">
<!-- TopAppBar -->
<header class="flex justify-between items-center w-full px-margin py-base sticky top-0 z-30 bg-surface dark:bg-on-background shadow-sm">
<div class="flex items-center gap-4 lg:hidden">
<img alt="SMP Islam Raudlatul Hikmah Logo" class="w-8 h-8 rounded-full object-cover" data-alt="A high-quality, professional logo for an Islamic school, featuring a stylized book and a growing tree or leaf motif in rich emerald green and gold colors. The logo is set against a clean white background, exuding a sense of growth, academic excellence, and Islamic tradition. Bright, modern lighting highlights the metallic gold accents." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDe-qZXHG0pwWrVDl02j3pMTC0VaOHnJ82M6MLrviouOCWm0Ygx-sDr1dztx-l6hLdZ9tLGSB5UGO485FX5jptBSoxqCWfi1h-OgKD0X8f4HbrE7HzjDSCtp-Y9jXYCB-cMGw4VIpOJg0w_pHDFERjUZZ1bYW4Hrq9L8h--yQaJcTYEsUruuLt1y4L46jk3RnIQyutt4yfoPGIR0upFUss9noKn9o3OKjDpPs66aCv3z4-YLWh9_0rQ0eIJAni5N28xpkS4qnxMw1wV"/>
<span class="font-headline-md text-headline-md font-extrabold text-primary dark:text-inverse-primary text-lg">SMP Islam Raudlatul Hikmah</span>
</div>
<div class="hidden lg:block">
<span class="font-headline-md text-headline-md font-extrabold text-primary dark:text-inverse-primary">SMP Islam Raudlatul Hikmah</span>
</div>
<div class="flex items-center gap-4">
<button class="text-on-surface-variant hover:text-primary transition-all p-2 rounded-full hover:bg-surface-container">
<span class="material-symbols-outlined" data-icon="notifications">notifications</span>
</button>
<button class="text-on-surface-variant hover:text-primary transition-all p-1 rounded-full hover:bg-surface-container font-bold px-3">
{{ explode(' ', $user->name)[0] }}
</button>
</div>
</header>
<!-- Canvas / Content -->
<main class="flex-1 px-4 md:px-margin py-lg max-w-5xl mx-auto w-full pb-24 lg:pb-lg">
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
<div>
<h2 class="font-headline-lg text-headline-lg text-on-surface">Forum Diskusi</h2>
<p class="font-body-md text-body-md text-on-surface-variant mt-1">Ruang diskusi asinkron untuk siswa dan guru.</p>
</div>
<button class="bg-primary text-on-primary px-6 py-2 rounded-full font-label-lg text-label-lg hover:bg-primary-container transition-colors flex items-center gap-2 ambient-shadow">
<span class="material-symbols-outlined" data-icon="add">add</span>
                    Buat Topik Baru
                </button>
</div>
<!-- Forum List -->
<div class="space-y-4">
@forelse($threads as $thread)
<div class="bg-surface-container-lowest rounded-xl p-6 ambient-shadow border border-outline-variant/30 flex gap-4" x-data="{ replyOpen: false }">
<div class="flex flex-col items-center gap-1 min-w-[40px]">
<form action="{{ route('forum.like', $thread->id) }}" method="POST">
@csrf
<button type="submit" class="p-1 rounded-full hover:bg-surface-container transition-colors text-on-surface-variant">
<span class="material-symbols-outlined fill">thumb_up</span>
</button>
</form>
</div>
<div class="flex-1">
<div class="flex items-center gap-2 mb-2">
<span class="font-label-sm text-label-sm text-on-surface font-semibold">{{ $thread->user->name }}</span>
<span class="text-on-surface-variant text-xs">•</span>
<span class="font-label-sm text-label-sm text-on-surface-variant font-normal">{{ $thread->created_at->diffForHumans() }}</span>
<span class="bg-primary-container/20 text-primary-container px-2 py-0.5 rounded-full text-xs font-semibold ml-2">{{ $thread->mata_pelajaran->nama_mapel ?? 'Umum' }}</span>
</div>
<h3 class="font-headline-md text-headline-md text-lg mb-2 text-on-surface">{{ $thread->judul }}</h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-4">{{ $thread->konten }}</p>
<div class="flex items-center gap-4">
<button @click="replyOpen = !replyOpen" class="flex items-center gap-1.5 text-on-surface-variant hover:text-primary transition-colors font-label-sm text-label-sm">
<span class="material-symbols-outlined text-[18px]">chat_bubble</span> {{ $thread->replies->count() }} Balasan
</button>
</div>
<div class="mt-4 pt-4 border-t border-outline-variant/30" x-show="replyOpen" x-transition="" style="display: none;">
@foreach($thread->replies as $reply)
<div class="mb-3 p-3 bg-surface-container-low rounded-lg">
    <div class="flex items-center gap-2 mb-1">
        <span class="font-label-sm text-label-sm text-on-surface font-semibold">{{ $reply->user->name }}</span>
        <span class="text-on-surface-variant text-xs">•</span>
        <span class="font-label-sm text-label-sm text-on-surface-variant font-normal">{{ $reply->created_at->diffForHumans() }}</span>
    </div>
    <p class="font-body-md text-body-md text-on-surface-variant text-sm">{{ $reply->konten }}</p>
</div>
@endforeach
<form action="{{ route('forum.reply', $thread->id) }}" method="POST" class="mt-4">
@csrf
<div class="flex gap-3 mb-4">
<textarea name="konten" required class="w-full border border-outline-variant rounded-lg p-2 font-body-md text-body-md focus:border-primary focus:ring-1 focus:ring-primary bg-surface" placeholder="Tulis balasan Anda..." rows="2"></textarea>
</div>
<div class="flex justify-end">
<button type="button" @click="replyOpen = false" class="bg-surface-container text-on-surface px-4 py-1.5 rounded-full font-label-sm text-label-sm hover:bg-surface-container-high transition-colors mr-2">Batal</button>
<button type="submit" class="bg-primary text-on-primary px-4 py-1.5 rounded-full font-label-sm text-label-sm hover:bg-primary-container transition-colors">Kirim</button>
</div>
</form>
</div>
</div>
</div>
@empty
<div class="bg-surface-container-lowest rounded-xl p-6 ambient-shadow text-center">Belum ada diskusi</div>
@endforelse
</div>
</main>
</div>
<!-- BottomNavBar (Mobile) -->
<nav class="lg:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 py-2 pb-safe shadow-lg bg-surface-container-lowest dark:bg-inverse-surface border-t border-outline-variant rounded-t-xl">
<a class="flex flex-col items-center justify-center text-on-surface-variant dark:text-surface-variant hover:text-primary transition-colors py-1 px-4" href="#">
<span class="material-symbols-outlined" data-icon="home">home</span>
<span class="font-label-sm text-label-sm mt-1">Beranda</span>
</a>
<a class="flex flex-col items-center justify-center text-on-surface-variant dark:text-surface-variant hover:text-primary transition-colors py-1 px-4" href="#">
<span class="material-symbols-outlined" data-icon="book">book</span>
<span class="font-label-sm text-label-sm mt-1">Materi</span>
</a>
<a class="flex flex-col items-center justify-center text-on-surface-variant dark:text-surface-variant hover:text-primary transition-colors py-1 px-4" href="#">
<span class="material-symbols-outlined" data-icon="task">task</span>
<span class="font-label-sm text-label-sm mt-1">Tugas</span>
</a>
<a class="flex flex-col items-center justify-center bg-primary-container dark:bg-primary text-on-primary-container dark:text-on-primary rounded-full px-6 py-1 active:scale-90 duration-200" href="#">
<span class="material-symbols-outlined" data-icon="groups">groups</span>
<span class="font-label-sm text-label-sm mt-1">Forum</span>
</a>
</nav>
</body></html>