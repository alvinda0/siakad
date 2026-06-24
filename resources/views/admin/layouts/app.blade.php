<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title id="page-title">@yield('title', 'Dashboard') — SIAKAD Admin</title>
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
            width: 260px;
            z-index: 40;
            display: flex;
            flex-direction: column;
            transition: width .25s ease;
            overflow: hidden;
        }
        #sidebar.collapsed { width: 68px; }
        #sidebar.collapsed .sidebar-label,
        #sidebar.collapsed .sidebar-group-label,
        #sidebar.collapsed .sidebar-badge { display: none; }
        #sidebar.collapsed .nav-item  { justify-content: center; padding-left: 0; padding-right: 0; }
        #sidebar.collapsed .brand-name { display: none; }

        /* ── Main wrapper offset ── */
        #main-wrapper {
            margin-left: 260px;
            transition: margin-left .25s ease;
            min-width: 0;
            flex: 1;
        }
        body.sidebar-collapsed #main-wrapper { margin-left: 68px; }

        @media (max-width: 767px) {
            #sidebar {
                transform: translateX(-100%);
                width: 260px !important;
                transition: transform .25s ease;
            }
            #sidebar.mobile-open { transform: translateX(0); }
            #main-wrapper { margin-left: 0 !important; }
        }

        /* ── Nav items ── */
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 14px; border-radius: 10px;
            font-size: .875rem; font-weight: 500; color: #94A3B8;
            transition: background .18s, color .18s;
            cursor: pointer; text-decoration: none; white-space: nowrap;
        }
        .nav-item:hover  { background: rgba(27,122,138,.10); color: #fff; }
        .nav-item.active { background: var(--teal); color: #fff; font-weight: 600; }
        .nav-item span.icon { font-size: 1.15rem; flex-shrink: 0; }

        /* ── Submenus ── */
        .submenu { overflow: hidden; transition: max-height .25s ease; max-height: 0; }
        .submenu.open { max-height: 300px; }
        .submenu-item {
            display: flex; align-items: center; gap: 8px;
            padding: 7px 14px 7px 40px; border-radius: 8px;
            font-size: .8125rem; font-weight: 500; color: #94A3B8;
            transition: background .15s, color .15s;
            text-decoration: none;
        }
        .submenu-item:hover  { background: rgba(27,122,138,.10); color: #CBD5E1; }
        .submenu-item.active { color: #fff; background: rgba(27,122,138,.25); }

        /* ── Page transition ── */
        #page-content { transition: opacity .15s ease, transform .15s ease; }
        #page-content.loading {
            opacity: 0;
            transform: translateY(6px);
            pointer-events: none;
        }

        /* ── Top progress bar ── */
        #nprogress {
            position: fixed; top: 0; left: 0; right: 0;
            height: 3px; z-index: 9999; pointer-events: none;
            background: linear-gradient(90deg, var(--teal), var(--gold));
            transform: scaleX(0); transform-origin: left;
            transition: transform .3s ease, opacity .3s ease;
            opacity: 0;
        }
        #nprogress.active { opacity: 1; }

        /* ── Scrollbar ── */
        #sidebar-nav::-webkit-scrollbar { width: 4px; }
        #sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        #sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 4px; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex">

<!-- Progress bar -->
<div id="nprogress"></div>

<!-- ══════════ SIDEBAR ══════════ -->
<aside id="sidebar" class="flex flex-col"
       style="background: linear-gradient(180deg, var(--navy) 0%, var(--teal-deeper) 100%);">

    <!-- Brand -->
    <div class="flex items-center gap-3 px-4 py-5 border-b border-white/10 shrink-0">
        <img src="{{ asset('image/smk.png') }}" alt="Logo" class="w-9 h-9 object-contain shrink-0">
        <div class="brand-name overflow-hidden">
            <p class="text-white font-extrabold text-sm leading-tight">SIAKAD Admin</p>
            <p class="text-white/50 text-xs truncate">SMK Muh. Sempor</p>
        </div>
    </div>

    <!-- Nav -->
    <nav id="sidebar-nav" class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}" data-spa
           class="nav-item" data-route="admin.dashboard">
            <span class="icon">🏠</span>
            <span class="sidebar-label">Dashboard</span>
        </a>

        <!-- ── Akademik ── -->
        <p class="sidebar-group-label text-xs font-bold uppercase tracking-widest text-white/30 px-3 pt-4 pb-1">
            Akademik
        </p>

        <!-- Guru -->
        <a href="{{ route('admin.guru.index') }}" data-spa
           class="nav-item" data-route="admin.guru.index">
            <span class="icon">👨‍🏫</span>
            <span class="sidebar-label">Guru</span>
        </a>

        <!-- Murid -->
        <a href="{{ route('admin.murid.index') }}" data-spa
           class="nav-item" data-route="admin.murid.index">
            <span class="icon">🎓</span>
            <span class="sidebar-label">Murid</span>
        </a>

        <!-- Kandidat PPDB -->
        <a href="{{ route('admin.kandidat.index') }}" data-spa
           class="nav-item">
            <span class="icon">📋</span>
            <span class="sidebar-label">Kandidat (PPDB)</span>
        </a>

        <!-- ── Manajemen ── -->
        <p class="sidebar-group-label text-xs font-bold uppercase tracking-widest text-white/30 px-3 pt-4 pb-1">
            Manajemen
        </p>

        <a href="{{ route('admin.kelas.index') }}" data-spa
           class="nav-item" data-route="admin.kelas.index">
            <span class="icon">🏫</span>
            <span class="sidebar-label">Kelas</span>
        </a>

        <a href="{{ route('admin.mapel.index') }}" data-spa
           class="nav-item" data-route="admin.mapel.index">
            <span class="icon">📚</span>
            <span class="sidebar-label">Mata Pelajaran</span>
        </a>

        <a href="{{ route('admin.jadwal.index') }}" data-spa
           class="nav-item" data-route="admin.jadwal.index">
            <span class="icon">🗓️</span>
            <span class="sidebar-label">Jadwal</span>
        </a>

        <a href="{{ route('admin.absensi.index') }}" data-spa
           class="nav-item" data-route="admin.absensi.index">
            <span class="icon">✅</span>
            <span class="sidebar-label">Absensi</span>
        </a>

        <a href="{{ route('admin.nilai.index') }}" data-spa
           class="nav-item" data-route="admin.nilai.index">
            <span class="icon">📊</span>
            <span class="sidebar-label">Nilai</span>
        </a>

        <a href="{{ route('admin.informasi.index') }}" data-spa
           class="nav-item" data-route="admin.informasi.index">
            <span class="icon">📢</span>
            <span class="sidebar-label">Informasi</span>
        </a>

        <a href="{{ route('admin.kegiatan-sekolah.index') }}" data-spa
           class="nav-item" data-route="admin.kegiatan-sekolah.index">
            <span class="icon">🎉</span>
            <span class="sidebar-label">Kegiatan Sekolah</span>
        </a>

        <!-- ── Sistem ── -->
        <p class="sidebar-group-label text-xs font-bold uppercase tracking-widest text-white/30 px-3 pt-4 pb-1">
            Sistem
        </p>

        <a href="{{ route('admin.users.index') }}" data-spa
           class="nav-item" data-route="admin.users.index">
            <span class="icon">👥</span>
            <span class="sidebar-label">Pengguna</span>
        </a>

        <a href="{{ route('admin.activity-logs.index') }}" data-spa
           class="nav-item" data-route="admin.activity-logs.index">
            <span class="icon">📋</span>
            <span class="sidebar-label">Log Aktivitas</span>
        </a>
    </nav>

    <!-- User info -->
    <div class="px-4 py-4 border-t border-white/10 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shrink-0 text-white"
                 style="background: var(--teal);">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="brand-name overflow-hidden">
                <p class="text-white text-sm font-semibold truncate">{{ Auth::user()->name }}</p>
                <p class="text-white/40 text-xs truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>
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
            <!-- Breadcrumb -->
            <div class="hidden sm:flex items-center gap-1 text-sm text-slate-400">
                <span>Admin</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span id="breadcrumb-text" class="font-semibold text-slate-700">@yield('breadcrumb', 'Dashboard')</span>
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

    <!-- Page content — only THIS area gets swapped -->
    <main class="flex-1 p-6">
        <div id="page-content">
            @yield('content')
        </div>
    </main>
</div>

<script>
/* ═══════════════════════════════════════════════
   SPA-like navigation — swap only #page-content
═══════════════════════════════════════════════ */
const pageContent  = document.getElementById('page-content');
const breadcrumb   = document.getElementById('breadcrumb-text');
const pageTitle    = document.getElementById('page-title');
const progressBar  = document.getElementById('nprogress');
let   progressTimer;

/* ── Progress bar helpers ── */
function progressStart() {
    clearTimeout(progressTimer);
    progressBar.style.transform = 'scaleX(0.3)';
    progressBar.classList.add('active');
    progressTimer = setTimeout(() => {
        progressBar.style.transform = 'scaleX(0.7)';
    }, 150);
}
function progressDone() {
    clearTimeout(progressTimer);
    progressBar.style.transform = 'scaleX(1)';
    setTimeout(() => {
        progressBar.style.opacity = '0';
        setTimeout(() => {
            progressBar.style.transform = 'scaleX(0)';
            progressBar.style.opacity   = '1';
            progressBar.classList.remove('active');
        }, 300);
    }, 200);
}

/* ── Re-run scripts after SPA swap ── */
function rerunScripts(container) {
    const scripts = Array.from(container.querySelectorAll('script'));
    return scripts.reduce((chain, old) => {
        return chain.then(() => new Promise((resolve) => {
            const s = document.createElement('script');
            Array.from(old.attributes).forEach(attr => s.setAttribute(attr.name, attr.value));
            if (old.src) {
                // External script — wait for load before continuing
                s.onload  = resolve;
                s.onerror = resolve; // Don't block chain on error
            } else {
                s.textContent = old.textContent;
            }
            old.replaceWith(s);
            if (!old.src) resolve(); // Inline scripts execute synchronously
        }));
    }, Promise.resolve());
}

/* ── Navigate ── */
async function navigateTo(url, pushState = true) {
    progressStart();
    pageContent.classList.add('loading');

    try {
        const res = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
            }
        });

        if (!res.ok) throw new Error(res.status);

        const text = await res.text();

        // Parse the fragment — server returns full page on AJAX too,
        // so extract only what's inside #page-content and data attrs.
        const parser  = new DOMParser();
        const doc     = parser.parseFromString(text, 'text/html');
        const newContent = doc.getElementById('page-content');
        const newTitle   = doc.getElementById('page-title');
        const newBread   = doc.getElementById('breadcrumb-text');

        if (newContent) {
            pageContent.innerHTML = newContent.innerHTML;
        }
        if (newTitle)   pageTitle.textContent   = newTitle.textContent;
        if (newBread)   breadcrumb.textContent  = newBread.textContent;

        if (pushState) {
            history.pushState({ url }, '', url);
        }

        // Clear any page-specific global functions from the previous page
        // to prevent stale closures from interfering with new page scripts
        ['openEditModal','closeEditModal','openCreateModal','closeCreateModal',
         'confirmDeleteModal','confirmDelete','openWaliModal','closeWaliModal',
         'openDeleteModal','closeDeleteModal'].forEach(fn => { delete window[fn]; });

        // Re-run any inline scripts inside the new content
        await rerunScripts(pageContent);

        updateActiveNav(url);

    } catch (e) {
        // Fallback: hard navigate
        window.location.href = url;
    } finally {
        pageContent.classList.remove('loading');
        progressDone();

        // Close mobile sidebar after navigation
        closeSidebar();
    }
}

/* ── Update active state in sidebar ── */
function updateActiveNav(url) {
    const path = new URL(url, location.origin).pathname;

    // Reset all nav items
    document.querySelectorAll('.nav-item[data-spa], .nav-item[data-route]').forEach(el => {
        el.classList.remove('active');
    });
    document.querySelectorAll('.submenu-item').forEach(el => {
        el.classList.remove('active');
    });

    // Find matching link
    document.querySelectorAll('[data-spa]').forEach(link => {
        const linkPath = new URL(link.href, location.origin).pathname;
        if (linkPath === path) {
            // Highlight the link
            if (link.classList.contains('nav-item')) {
                link.classList.add('active');
            } else if (link.classList.contains('submenu-item')) {
                link.classList.add('active');
                // Also open parent submenu and mark parent button
                const submenu = link.closest('.submenu');
                if (submenu) {
                    submenu.classList.add('open');
                    const parentBtn = submenu.previousElementSibling;
                    if (parentBtn) parentBtn.classList.add('active');
                    // Rotate arrow
                    const menuId  = submenu.id;
                    const arrowId = 'arrow-' + menuId.replace('menu-', '');
                    const arrow   = document.getElementById(arrowId);
                    if (arrow) arrow.classList.add('rotate-180');
                }
            }
        }
    });
}

/* ── Intercept all [data-spa] clicks ── */
document.addEventListener('click', e => {
    const link = e.target.closest('[data-spa]');
    if (!link) return;
    e.preventDefault();
    const url = link.href;
    if (url === location.href) return; // already here
    navigateTo(url);
});

/* ── Browser back / forward ── */
window.addEventListener('popstate', e => {
    if (e.state?.url) {
        navigateTo(e.state.url, false);
    }
});

/* ── Init active state on first load ── */
updateActiveNav(location.href);
history.replaceState({ url: location.href }, '', location.href);

/* ═══════════════════════
   Sidebar helpers
═══════════════════════ */
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
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    }
}

function closeSidebar() {
    sidebar.classList.remove('mobile-open');
    overlay.classList.add('hidden');
}

function toggleSubmenu(id) {
    const el    = document.getElementById(id);
    const arrow = document.getElementById('arrow-' + id.replace('menu-', ''));
    el.classList.toggle('open');
    if (arrow) arrow.classList.toggle('rotate-180');
}

// Restore sidebar collapsed state
if (!isMobile() && localStorage.getItem('sidebarCollapsed') === 'true') {
    sidebar.classList.add('collapsed');
    document.body.classList.add('sidebar-collapsed');
}
</script>

@stack('scripts')

{{-- Global Confirm Modal --}}
@include('components.confirm-modal')
</body>
</html>
