<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portal Murid') — SIAKAD</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --teal:       #1B7A8A;
            --teal-dark:  #145F6E;
            --teal-deeper:#0D4A57;
            --teal-light: #E0F4F7;
            --navy:       #112D3E;
            --gold:       #E6920A;
        }
        body { font-family: 'Inter', sans-serif; background: #F0F7F9; }

        /* ── Sidebar ── */
        #sidebar {
            position: fixed;
            left: 0; top: 0;
            height: 100vh;
            width: 240px;
            z-index: 40;
            display: flex;
            flex-direction: column;
            transition: width .25s ease;
            overflow: hidden;
        }
        #sidebar.collapsed { width: 64px; }
        #sidebar.collapsed .sidebar-label,
        #sidebar.collapsed .sidebar-group-label { display: none; }
        #sidebar.collapsed .nav-item  { justify-content: center; padding-left: 0; padding-right: 0; }
        #sidebar.collapsed .brand-name { display: none; }

        #main-wrapper {
            margin-left: 240px;
            transition: margin-left .25s ease;
            min-width: 0;
            flex: 1;
        }
        body.sidebar-collapsed #main-wrapper { margin-left: 64px; }

        @media (max-width: 767px) {
            #sidebar {
                transform: translateX(-100%);
                width: 240px !important;
                transition: transform .25s ease;
            }
            #sidebar.mobile-open { transform: translateX(0); }
            #main-wrapper { margin-left: 0 !important; }
        }

        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 14px; border-radius: 10px;
            font-size: .875rem; font-weight: 500; color: #94A3B8;
            transition: background .18s, color .18s;
            cursor: pointer; text-decoration: none; white-space: nowrap;
        }
        .nav-item:hover  { background: rgba(27,122,138,.10); color: #fff; }
        .nav-item.active { background: var(--teal); color: #fff; font-weight: 600; }
        .nav-item span.icon { font-size: 1.1rem; flex-shrink: 0; }

        #sidebar-nav::-webkit-scrollbar { width: 4px; }
        #sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        #sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 4px; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex">

<!-- ══════════ SIDEBAR ══════════ -->
<aside id="sidebar" class="flex flex-col"
       style="background: linear-gradient(180deg, var(--navy) 0%, var(--teal-deeper) 100%);">

    <!-- Brand -->
    <div class="flex items-center gap-3 px-4 py-5 border-b border-white/10 shrink-0">
        <img src="{{ asset('image/smk.png') }}" alt="Logo" class="w-9 h-9 object-contain shrink-0">
        <div class="brand-name overflow-hidden">
            <p class="text-white font-extrabold text-sm leading-tight">Portal Murid</p>
            <p class="text-white/50 text-xs truncate">SMK Muh. Sempor</p>
        </div>
    </div>

    <!-- Nav -->
    <nav id="sidebar-nav" class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

        <p class="sidebar-group-label text-xs font-bold uppercase tracking-widest text-white/30 px-3 pb-1">
            Akademik
        </p>

        <a href="{{ route('murid.ujian.index') }}"
           class="nav-item {{ request()->routeIs('murid.ujian.*') ? 'active' : '' }}">
            <span class="icon">📝</span>
            <span class="sidebar-label">Ujian Online</span>
        </a>

        <a href="{{ route('murid.nilai.index') }}"
           class="nav-item {{ request()->routeIs('murid.nilai.*') ? 'active' : '' }}">
            <span class="icon">📊</span>
            <span class="sidebar-label">Nilai Saya</span>
        </a>

    </nav>

    <!-- User info + logout -->
    <div class="px-4 py-4 border-t border-white/10 shrink-0 space-y-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shrink-0 text-white"
                 style="background: var(--teal);">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="brand-name overflow-hidden">
                <p class="text-white text-sm font-semibold truncate">{{ Auth::user()->name }}</p>
                <p class="text-white/40 text-xs truncate">Murid</p>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}" class="brand-name">
            @csrf
            <button type="submit"
                    class="w-full text-xs font-semibold px-3 py-2 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition text-left">
                🚪 Logout
            </button>
        </form>
    </div>
</aside>

<!-- Mobile overlay -->
<div id="overlay" class="hidden fixed inset-0 bg-black/50 z-30 md:hidden" onclick="closeSidebar()"></div>

<!-- ══════════ MAIN ══════════ -->
<div id="main-wrapper" class="flex flex-col min-h-screen">

    <!-- Topbar -->
    <header class="sticky top-0 z-20 bg-white border-b border-slate-200 shadow-sm px-4 py-3 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()"
                    class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 transition"
                    aria-label="Toggle sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                </svg>
            </button>
            <div class="hidden sm:flex items-center gap-1 text-sm text-slate-400">
                <span>Murid</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="font-semibold text-slate-700">@yield('breadcrumb', 'Dashboard')</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-slate-500 hidden sm:block">
                Halo, <strong class="text-slate-700">{{ Auth::user()->name }}</strong>
            </span>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit"
                        class="text-sm font-semibold px-4 py-2 rounded-lg text-white transition hover:brightness-110"
                        style="background: var(--teal);">
                    Logout
                </button>
            </form>
        </div>
    </header>

    <!-- Page content -->
    <main class="flex-1 p-6">
        @yield('content')
    </main>
</div>

<script>
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');
const isMobile = () => window.innerWidth < 768;

function toggleSidebar() {
    if (isMobile()) {
        sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('hidden');
    } else {
        sidebar.classList.toggle('collapsed');
        document.body.classList.toggle('sidebar-collapsed', sidebar.classList.contains('collapsed'));
        localStorage.setItem('muridSidebarCollapsed', sidebar.classList.contains('collapsed'));
    }
}

function closeSidebar() {
    sidebar.classList.remove('mobile-open');
    overlay.classList.add('hidden');
}

if (!isMobile() && localStorage.getItem('muridSidebarCollapsed') === 'true') {
    sidebar.classList.add('collapsed');
    document.body.classList.add('sidebar-collapsed');
}
</script>

@stack('scripts')
</body>
</html>
