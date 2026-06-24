<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SMK Muhammadiyah Sempor') — Siakad</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --teal:        #1B7A8A;
            --teal-dark:   #145F6E;
            --teal-deeper: #0D4A57;
            --teal-light:  #E0F4F7;
            --navy:        #112D3E;
            --navy-dark:   #0B1E2D;
            --gold:        #E6920A;
            --gold-light:  #FEF3DC;
            --text:        #1E293B;
        }
        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; color: var(--text); background: #F0F7F9; overflow-x: hidden; }

        /* ── Scroll Progress ── */
        #scroll-progress {
            position: fixed; top: 0; left: 0; height: 3px; width: 0%;
            background: linear-gradient(90deg, var(--teal), var(--gold));
            z-index: 9999; transition: width .1s linear;
        }

        /* ── Navbar ── */
        .navbar {
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(27,122,138,0.12);
            transition: box-shadow .3s;
        }
        .navbar.scrolled { box-shadow: 0 4px 24px rgba(13,74,87,0.10); }
        .nav-link {
            position: relative; padding-bottom: 2px;
            color: #475569; font-size:.875rem; font-weight:500;
            text-decoration: none; transition: color .2s;
        }
        .nav-link::after {
            content:''; position:absolute; left:0; bottom:-2px;
            width:0; height:2px; background:var(--teal); border-radius:2px;
            transition: width .25s ease;
        }
        .nav-link:hover { color: var(--teal); }
        .nav-link:hover::after { width:100%; }
        .nav-link.active { color: var(--teal); }
        .nav-link.active::after { width:100%; }

        /* ── Mobile Menu ── */
        #mobile-menu { display:none; }
        #mobile-menu.open { display:block; }

        /* ── Buttons ── */
        .btn-teal { background: var(--teal); color: #fff; transition: background .2s, transform .15s, box-shadow .2s; }
        .btn-teal:hover { background: var(--teal-dark); transform:translateY(-1px); box-shadow:0 6px 16px rgba(27,122,138,0.3); }
        .btn-gold { background: var(--gold); color: #fff; transition: background .2s, transform .15s, box-shadow .2s; }
        .btn-gold:hover { background: #c97d08; transform:translateY(-1px); box-shadow:0 6px 16px rgba(230,146,10,0.35); }
        .btn-outline { border: 2px solid var(--teal); color: var(--teal); transition: all .2s; }
        .btn-outline:hover { background: var(--teal); color:#fff; transform:translateY(-1px); }
        .btn-ghost-white { border: 2px solid rgba(255,255,255,0.5); color:#fff; transition: all .2s; }
        .btn-ghost-white:hover { background:rgba(255,255,255,0.15); border-color:#fff; }
    </style>
    @stack('styles')
</head>
<body>

<!-- Scroll progress bar -->
<div id="scroll-progress"></div>

<!-- ═══════════ NAVBAR ═══════════ -->
<nav class="navbar sticky top-0 z-50" id="navbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        <!-- Logo & Nama -->
        <a href="{{ route('beranda') }}" class="flex items-center gap-3 shrink-0">
            <img src="{{ asset('image/smk.png') }}" alt="Logo SMK" class="w-10 h-10 object-contain">
            <div class="hidden sm:block">
                <p class="font-extrabold text-sm leading-tight" style="color:var(--teal-dark)">SMK Muhammadiyah Sempor</p>
                <p class="text-xs italic" style="color:var(--teal);">Excellent in Taqwa, Science &amp; Professional</p>
            </div>
        </a>

        <!-- Desktop Menu -->
        <div class="hidden md:flex items-center gap-6">
            <a href="{{ route('beranda') }}"        class="nav-link {{ request()->routeIs('beranda')        ? 'active' : '' }}">Beranda</a>
            <a href="{{ route('kegiatan') }}"       class="nav-link {{ request()->routeIs('kegiatan')       ? 'active' : '' }}">Kegiatan</a>
            <a href="{{ route('prestasi') }}"       class="nav-link {{ request()->routeIs('prestasi')       ? 'active' : '' }}">Prestasi</a>
            <a href="{{ route('ekstrakurikuler') }}" class="nav-link {{ request()->routeIs('ekstrakurikuler') ? 'active' : '' }}">Ekstrakurikuler</a>
            <a href="{{ route('fasilitas') }}"      class="nav-link {{ request()->routeIs('fasilitas')      ? 'active' : '' }}">Fasilitas</a>
            <a href="{{ route('pengumuman') }}"     class="nav-link {{ request()->routeIs('pengumuman', 'informasi') ? 'active' : '' }}">Informasi</a>
            <a href="{{ route('kontak') }}"         class="nav-link {{ request()->routeIs('kontak')         ? 'active' : '' }}">Kontak</a>
        </div>

        <!-- Auth + Hamburger -->
        <div class="flex items-center gap-2">
            <a href="{{ route('kandidat.create') }}" class="btn-gold text-sm px-4 py-2 rounded-lg font-semibold hidden sm:inline-flex items-center gap-1.5">
                ✏️ Daftar Sekarang
            </a>
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/admin/dashboard') }}" class="btn-teal text-sm px-4 py-2 rounded-lg font-semibold">Dashboard</a>
                @else
                    <a href="{{ route('admin.login') }}" class="btn-outline text-sm px-4 py-2 rounded-lg font-semibold hidden sm:inline-flex">Masuk</a>
                @endauth
            @endif
            <!-- Hamburger -->
            <button id="menu-btn" class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition" aria-label="Menu">
                <svg id="icon-open"  class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg id="icon-close" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="md:hidden border-t border-gray-100 bg-white">
        <div class="px-4 py-3 flex flex-col gap-1">
            <a href="{{ route('beranda') }}"         class="nav-link py-2 block" onclick="closeMobileMenu()">Beranda</a>
            <a href="{{ route('kegiatan') }}"        class="nav-link py-2 block" onclick="closeMobileMenu()">Kegiatan</a>
            <a href="{{ route('prestasi') }}"        class="nav-link py-2 block" onclick="closeMobileMenu()">Prestasi</a>
            <a href="{{ route('ekstrakurikuler') }}" class="nav-link py-2 block" onclick="closeMobileMenu()">Ekstrakurikuler</a>
            <a href="{{ route('fasilitas') }}"       class="nav-link py-2 block" onclick="closeMobileMenu()">Fasilitas</a>
            <a href="{{ route('pengumuman') }}"      class="nav-link py-2 block" onclick="closeMobileMenu()">Informasi</a>
            <a href="{{ route('kontak') }}"          class="nav-link py-2 block" onclick="closeMobileMenu()">Kontak</a>
            @if (Route::has('login'))
                @guest
                    <div class="pt-2 pb-1 flex flex-col gap-2">
                        <a href="{{ route('kandidat.create') }}" class="btn-gold text-sm px-4 py-2 rounded-lg font-semibold text-center" onclick="closeMobileMenu()">✏️ Daftar Sekarang</a>
                        <a href="{{ route('admin.login') }}"     class="btn-outline text-sm px-4 py-2 rounded-lg font-semibold text-center" onclick="closeMobileMenu()">Masuk</a>
                    </div>
                @endguest
            @endif
        </div>
    </div>
</nav>

<!-- ═══════════ PAGE CONTENT ═══════════ -->
@yield('content')

<!-- ═══════════ FOOTER ═══════════ -->
<div style="background:#F0F7F9; line-height:0;">
    <svg viewBox="0 0 1440 56" preserveAspectRatio="none" class="w-full block" style="height:50px;" fill="#0B1E2D">
        <path d="M0,28 C480,56 960,0 1440,28 L1440,56 L0,56 Z"/>
    </svg>
</div>

<footer style="background:var(--navy-dark);" class="pt-10 pb-6 px-4">
    <div class="max-w-7xl mx-auto grid md:grid-cols-4 gap-8 pb-8 border-b border-white/10">
        <div class="md:col-span-2">
            <div class="flex items-center gap-3 mb-4">
                <img src="{{ asset('image/smk.png') }}" alt="Logo" class="w-10 h-10 object-contain rounded-xl bg-white/90 p-0.5 shrink-0">
                <div>
                    <p class="font-bold text-white text-sm">SMK Muhammadiyah Sempor</p>
                    <p class="text-xs italic" style="color:rgba(255,255,255,0.5);">Excellent in Taqwa, Science &amp; Professional</p>
                </div>
            </div>
            <p class="text-sm leading-relaxed" style="color:rgba(255,255,255,0.55);">
                Jl. Klampok-Gombong Km13, Ds. Sampang,<br>Kec. Sempor, Kab. Kebumen, Jawa Tengah.
            </p>
            <div class="flex gap-3 mt-4">
                <a href="#" aria-label="Instagram" class="w-8 h-8 rounded-lg flex items-center justify-center transition" style="background:rgba(255,255,255,0.08);" onmouseover="this.style.background='var(--teal)'" onmouseout="this.style.background='rgba(255,255,255,0.08)'">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                </a>
                <a href="#" aria-label="YouTube" class="w-8 h-8 rounded-lg flex items-center justify-center transition" style="background:rgba(255,255,255,0.08);" onmouseover="this.style.background='#c00'" onmouseout="this.style.background='rgba(255,255,255,0.08)'">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg>
                </a>
                <a href="https://wa.me/6281325540947" target="_blank" aria-label="WhatsApp" class="w-8 h-8 rounded-lg flex items-center justify-center transition" style="background:rgba(255,255,255,0.08);" onmouseover="this.style.background='#25D366'" onmouseout="this.style.background='rgba(255,255,255,0.08)'">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </a>
            </div>
        </div>
        <div>
            <h4 class="font-semibold text-white mb-4 text-sm">Navigasi</h4>
            <ul class="space-y-2 text-sm" style="color:rgba(255,255,255,0.55);">
                <li><a href="{{ route('beranda') }}"         class="hover:text-white transition">Beranda</a></li>
                <li><a href="{{ route('kegiatan') }}"        class="hover:text-white transition">Kegiatan</a></li>
                <li><a href="{{ route('prestasi') }}"        class="hover:text-white transition">Prestasi</a></li>
                <li><a href="{{ route('ekstrakurikuler') }}" class="hover:text-white transition">Ekstrakurikuler</a></li>
                <li><a href="{{ route('fasilitas') }}"       class="hover:text-white transition">Fasilitas</a></li>
                <li><a href="{{ route('pengumuman') }}"      class="hover:text-white transition">Informasi</a></li>
                <li><a href="{{ route('kontak') }}"          class="hover:text-white transition">Kontak</a></li>
            </ul>
        </div>
        <div>
            <h4 class="font-semibold text-white mb-4 text-sm">Akun Siakad</h4>
            <a href="{{ route('admin.login') }}" class="btn-teal inline-block text-sm px-5 py-2 rounded-lg font-semibold mb-4">Masuk ke Siakad</a>
            <h4 class="font-semibold text-white mb-2 text-sm mt-2">Kontak</h4>
            <p class="text-sm" style="color:rgba(255,255,255,0.55);">
                <span class="block">Julaeha, S.Pd.</span>
                <a href="https://wa.me/6281325540947" target="_blank" class="hover:text-white transition">081325540947</a>
            </p>
            <a href="mailto:smkmuhse@gmail.com" class="text-sm hover:text-white transition mt-1 block" style="color:rgba(255,255,255,0.55);">smkmuhse@gmail.com</a>
        </div>
    </div>
    <div class="max-w-7xl mx-auto pt-5 text-center text-xs" style="color:rgba(255,255,255,0.35);">
        &copy; {{ date('Y') }} SMK Muhammadiyah Sempor — Hak Cipta Dilindungi.
    </div>
</footer>

<script>
    const bar = document.getElementById('scroll-progress');
    const navbar = document.getElementById('navbar');
    function updateProgress() {
        const total = document.documentElement.scrollHeight - window.innerHeight;
        if (total > 0) bar.style.width = (window.scrollY / total * 100) + '%';
    }
    function updateNavbar() { navbar.classList.toggle('scrolled', window.scrollY > 20); }
    window.addEventListener('scroll', () => { updateProgress(); updateNavbar(); }, { passive: true });

    const menuBtn    = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const iconOpen   = document.getElementById('icon-open');
    const iconClose  = document.getElementById('icon-close');
    menuBtn.addEventListener('click', () => {
        const isOpen = mobileMenu.classList.toggle('open');
        iconOpen.classList.toggle('hidden', isOpen);
        iconClose.classList.toggle('hidden', !isOpen);
    });
    function closeMobileMenu() {
        mobileMenu.classList.remove('open');
        iconOpen.classList.remove('hidden');
        iconClose.classList.add('hidden');
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.style.opacity = '1';
                e.target.style.transform = 'translateY(0)';
                observer.unobserve(e.target);
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.card, .card-alt, .grad-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity .5s ease, transform .5s ease, box-shadow .25s';
        observer.observe(el);
    });
</script>
@stack('scripts')
</body>
</html>
