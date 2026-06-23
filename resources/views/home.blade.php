<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SMK Muhammadiyah Sempor — Siakad</title>
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

        /* ── Mobile Menu ── */
        #mobile-menu { display:none; }
        #mobile-menu.open { display:block; }

        /* ── Buttons ── */
        .btn-teal {
            background: var(--teal); color: #fff;
            transition: background .2s, transform .15s, box-shadow .2s;
        }
        .btn-teal:hover { background: var(--teal-dark); transform:translateY(-1px); box-shadow:0 6px 16px rgba(27,122,138,0.3); }
        .btn-gold {
            background: var(--gold); color: #fff;
            transition: background .2s, transform .15s, box-shadow .2s;
        }
        .btn-gold:hover { background: #c97d08; transform:translateY(-1px); box-shadow:0 6px 16px rgba(230,146,10,0.35); }
        .btn-outline {
            border: 2px solid var(--teal); color: var(--teal);
            transition: all .2s;
        }
        .btn-outline:hover { background: var(--teal); color:#fff; transform:translateY(-1px); }
        .btn-ghost-white {
            border: 2px solid rgba(255,255,255,0.5); color:#fff;
            transition: all .2s;
        }
        .btn-ghost-white:hover { background:rgba(255,255,255,0.15); border-color:#fff; }

        /* ── Hero ── */
        .hero-bg {
            background: linear-gradient(135deg, var(--teal-deeper) 0%, var(--teal) 55%, #2BA8BF 100%);
            position: relative; overflow: hidden;
        }
        .hero-orb {
            position:absolute; border-radius:50%;
            background: radial-gradient(circle, rgba(255,255,255,0.12), transparent 70%);
            animation: float 8s ease-in-out infinite;
        }
        @keyframes float {
            0%,100%{ transform:translateY(0) scale(1); }
            50%{ transform:translateY(-20px) scale(1.04); }
        }
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(28px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .anim-1 { animation: fadeUp .7s ease both; }
        .anim-2 { animation: fadeUp .7s .15s ease both; }
        .anim-3 { animation: fadeUp .7s .3s ease both; }
        .anim-4 { animation: fadeUp .7s .45s ease both; }
        .anim-5 { animation: fadeUp .7s .6s ease both; }

        /* ── Stat cards ── */
        .stat-card {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            transition: transform .25s, background .25s;
        }
        .stat-card:hover { transform:translateY(-6px); background:rgba(255,255,255,0.18); }

        /* ── Ticker ── */
        @keyframes marquee { 0%{transform:translateX(100%)} 100%{transform:translateX(-100%)} }
        .animate-marquee { display:inline-block; animation: marquee 30s linear infinite; }

        /* ── Section headings ── */
        .sec-label { color:var(--teal); letter-spacing:.1em; font-size:.75rem; font-weight:700; text-transform:uppercase; }
        .sec-title  { color:var(--navy); font-weight:800; }
        .sec-divider {
            display:inline-block; width:48px; height:4px; border-radius:2px;
            background: linear-gradient(90deg, var(--teal), var(--gold));
            margin-top:8px; margin-bottom:12px;
        }

        /* ── Cards ── */
        .card {
            background:#fff; border-radius:1.25rem;
            border: 1px solid rgba(27,122,138,0.08);
            transition: transform .25s, box-shadow .25s;
        }
        .card:hover { transform:translateY(-6px); box-shadow:0 20px 44px rgba(27,122,138,0.13); }
        .card-alt {
            background:#FAFEFF; border-radius:1.25rem;
            border: 1px solid rgba(27,122,138,0.10);
            transition: transform .25s, box-shadow .25s;
        }
        .card-alt:hover { transform:translateY(-6px); box-shadow:0 20px 44px rgba(27,122,138,0.13); }

        /* ── Gradient cards (ekskul / galeri) ── */
        .grad-card {
            border-radius:1.25rem; overflow:hidden;
            transition: transform .25s, box-shadow .25s;
        }
        .grad-card:hover { transform:translateY(-6px) scale(1.02); box-shadow:0 20px 40px rgba(0,0,0,0.22); }

        /* ── News top bar ── */
        .news-bar { height:4px; border-radius:4px 4px 0 0; background:var(--teal); }

        /* ── Contact icon ring ── */
        .icon-ring {
            width:3rem; height:3rem; border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            flex-shrink:0;
        }

        /* ── CTA Banner ── */
        .cta-banner {
            background: linear-gradient(135deg, var(--teal-deeper) 0%, var(--teal) 60%, #2BA8BF 100%);
            position:relative; overflow:hidden;
        }
        .cta-banner::before {
            content:''; position:absolute; right:-60px; top:-60px;
            width:280px; height:280px; border-radius:50%;
            background:rgba(255,255,255,0.07);
        }

        /* ── Footer ── */
        footer a:hover { color:#fff; }
    </style>
</head>
<body>

<!-- Scroll progress bar -->
<div id="scroll-progress"></div>


<!-- ═══════════ NAVBAR ═══════════ -->
<nav class="navbar sticky top-0 z-50" id="navbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        <!-- Logo & Nama -->
        <a href="#beranda" class="flex items-center gap-3 shrink-0">
            <img src="{{ asset('image/smk.png') }}" alt="Logo SMK" class="w-10 h-10 object-contain">
            <div class="hidden sm:block">
                <p class="font-extrabold text-sm leading-tight" style="color:var(--teal-dark)">SMK Muhammadiyah Sempor</p>
                <p class="text-xs italic" style="color:var(--teal);">Excellent in Taqwa, Science & Professional</p>
            </div>
        </a>

        <!-- Desktop Menu -->
        <div class="hidden md:flex items-center gap-6">
            <a href="#beranda"   class="nav-link">Beranda</a>
            <a href="#kegiatan"  class="nav-link">Kegiatan</a>
            <a href="#prestasi"  class="nav-link">Prestasi</a>
            <a href="#ekskul"    class="nav-link">Ekstrakurikuler</a>
            <a href="#fasilitas" class="nav-link">Fasilitas</a>
            <a href="#kontak"    class="nav-link">Kontak</a>
        </div>

        <!-- Auth + Hamburger -->
        <div class="flex items-center gap-2">
            <a href="{{ route('kandidat.create') }}" class="btn-gold text-sm px-4 py-2 rounded-lg font-semibold hidden sm:inline-flex items-center gap-1.5">
                ✏️ Daftar Sekarang
            </a>
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-teal text-sm px-4 py-2 rounded-lg font-semibold">Dashboard</a>
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
            <a href="#beranda"   class="nav-link py-2 block" onclick="closeMobileMenu()">Beranda</a>
            <a href="#kegiatan"  class="nav-link py-2 block" onclick="closeMobileMenu()">Kegiatan</a>
            <a href="#prestasi"  class="nav-link py-2 block" onclick="closeMobileMenu()">Prestasi</a>
            <a href="#ekskul"    class="nav-link py-2 block" onclick="closeMobileMenu()">Ekstrakurikuler</a>
            <a href="#fasilitas" class="nav-link py-2 block" onclick="closeMobileMenu()">Fasilitas</a>
            <a href="#kontak"    class="nav-link py-2 block" onclick="closeMobileMenu()">Kontak</a>
            @if (Route::has('login'))
                @guest
                    <div class="pt-2 pb-1 flex flex-col gap-2">
                        <a href="{{ route('kandidat.create') }}" class="btn-gold text-sm px-4 py-2 rounded-lg font-semibold text-center" onclick="closeMobileMenu()">✏️ Daftar Sekarang</a>
                        <a href="{{ route('admin.login') }}" class="btn-outline text-sm px-4 py-2 rounded-lg font-semibold text-center" onclick="closeMobileMenu()">Masuk</a>
                    </div>
                @endguest
            @endif
        </div>
    </div>
</nav>


<!-- ═══════════ HERO ═══════════ -->
<section id="beranda" class="hero-bg text-white py-24 px-4 relative">
    <!-- Orbs -->
    <div class="hero-orb" style="width:520px;height:520px;right:-100px;top:-100px;animation-delay:0s;"></div>
    <div class="hero-orb" style="width:320px;height:320px;right:80px;top:60px;animation-delay:3s;"></div>
    <div class="hero-orb" style="width:200px;height:200px;left:30px;bottom:80px;animation-delay:5s;"></div>

    <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-14 items-center relative z-10">
        <!-- Copy -->
        <div>
            <span class="anim-1 inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-full mb-5"
                  style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25);">
                <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
                Sistem Informasi Akademik
            </span>
            <h1 class="anim-2 text-4xl md:text-5xl lg:text-6xl font-black leading-[1.1] mb-4">
                Selamat Datang di<br>
                <span style="color:#F9C940; text-shadow:0 0 40px rgba(249,201,64,0.4);">SMK Muhammadiyah<br>Sempor</span>
            </h1>
            <!-- Tagline badges -->
            <div class="anim-3 flex flex-wrap gap-2 mb-5">
                @foreach(['✦ Taqwa', '✦ Science', '✦ Professional'] as $tag)
                <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full"
                      style="color:#F9C940; background:rgba(249,201,64,0.15); border:1px solid rgba(249,201,64,0.3);">
                    {{ $tag }}
                </span>
                @endforeach
            </div>
            <p class="anim-4 text-base leading-relaxed mb-8 max-w-md" style="color:rgba(255,255,255,0.85);">
                Unggul dalam prestasi, berkarakter Islami, dan berwawasan teknologi — mencetak generasi cerdas dan berakhlak mulia.
            </p>
            <div class="anim-5 flex flex-wrap gap-3">
                <a href="#kegiatan" class="btn-gold font-bold px-7 py-3 rounded-xl text-sm">
                    Lihat Kegiatan
                </a>
                <a href="{{ Route::has('register') ? route('register') : '#' }}"
                   class="btn-ghost-white font-semibold px-7 py-3 rounded-xl text-sm">
                    Pendaftaran PPDB
                </a>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-2 gap-4 anim-3">
            @php $stats = [
                ['1.200+','Siswa Aktif','🎓'],
                ['85+','Tenaga Pendidik','👨‍🏫'],
                ['200+','Prestasi Diraih','🏆'],
                ['20+','Ekstrakurikuler','⚽'],
            ]; @endphp
            @foreach($stats as [$num,$label,$icon])
            <div class="stat-card rounded-2xl p-6 text-center">
                <div class="text-3xl mb-1">{{ $icon }}</div>
                <p class="text-3xl font-black" style="color:#F9C940;">{{ $num }}</p>
                <p class="text-sm mt-1" style="color:rgba(255,255,255,0.75);">{{ $label }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Wave bottom -->
    <div class="absolute bottom-0 left-0 w-full overflow-hidden" style="height:64px; line-height:0;">
        <svg viewBox="0 0 1440 64" preserveAspectRatio="none" class="w-full h-full" fill="#F0F7F9">
            <path d="M0,32 C360,64 1080,0 1440,32 L1440,64 L0,64 Z"/>
        </svg>
    </div>
</section>


<!-- ═══════════ TICKER ═══════════ -->
<div class="overflow-hidden py-2.5 px-4" style="background:var(--teal-dark);">
    <div class="max-w-7xl mx-auto flex items-center gap-4">
        <span class="text-xs font-extrabold px-2.5 py-1 rounded-full whitespace-nowrap shrink-0"
              style="background:var(--gold); color:#fff;">📢 INFO</span>
        <div class="overflow-hidden flex-1">
            <p class="text-sm text-white whitespace-nowrap animate-marquee">
                🎓 PPDB 2025/2026 telah dibuka — daftarkan segera &nbsp;|&nbsp;
                🏆 Tim Olimpiade Sains meraih Juara 1 Tingkat Provinsi &nbsp;|&nbsp;
                📚 Ujian Akhir Semester Genap: 10–20 Juni 2025 &nbsp;|&nbsp;
                🌿 SMK Muhammadiyah Sempor raih penghargaan Sekolah Adiwiyata &nbsp;|&nbsp;
                💻 Program Magang Industri Batch 3 dibuka — hubungi TU sekolah
            </p>
        </div>
    </div>
</div>

<!-- ═══════════ KEGIATAN ═══════════ -->
<section id="kegiatan" class="py-20 px-4" style="background:#F0F7F9;">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <span class="sec-label">Agenda Sekolah</span>
            <h2 class="text-3xl font-extrabold sec-title mt-1">Kegiatan Sekolah</h2>
            <div class="sec-divider mx-auto"></div>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">Berbagai kegiatan untuk mendukung perkembangan siswa secara akademik dan non-akademik.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            @php
            $kegiatan = [
                ['🎓','Upacara Hari Pendidikan Nasional','2 Mei 2025','Nasional',
                 'Peringatan Hari Pendidikan Nasional dengan upacara bendera dan pidato Mendikbud.'],
                ['🔬','Olimpiade Sains Tingkat Kabupaten','15 Mei 2025','Akademik',
                 'Kompetisi sains Matematika, Fisika, Kimia, dan Biologi antar sekolah se-Kabupaten Kebumen.'],
                ['🎭','Festival Seni & Budaya','22 Mei 2025','Seni',
                 'Pentas seni tahunan menampilkan tari tradisional, drama, musik, dan pameran karya siswa.'],
                ['⚽','Turnamen Olahraga Antar Kelas','28 Mei 2025','Olahraga',
                 'Pertandingan sepak bola, voli, dan basket antar kelas untuk mempererat kebersamaan.'],
                ['📚','Seminar Motivasi & Karir','5 Juni 2025','Akademik',
                 'Seminar bersama alumni sukses dan pakar karir untuk mempersiapkan siswa meraih masa depan.'],
                ['🌿','Gerakan Sekolah Hijau','10 Juni 2025','Lingkungan',
                 'Penanaman pohon dan kerja bakti membersihkan lingkungan sekolah bersama seluruh warga sekolah.'],
            ];
            @endphp
            @foreach($kegiatan as [$icon,$judul,$tanggal,$kat,$desk])
            <div class="card p-6 border-t-4" style="border-top-color:var(--teal);">
                <div class="text-4xl mb-4">{{ $icon }}</div>
                <span class="inline-block text-xs font-bold px-2.5 py-1 rounded-full mb-3"
                      style="background:var(--teal-light); color:var(--teal-dark);">{{ $kat }}</span>
                <h3 class="font-bold sec-title mb-1 leading-snug">{{ $judul }}</h3>
                <p class="text-xs text-gray-400 mb-3 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>{{ $tanggal }}
                </p>
                <p class="text-sm text-gray-500 leading-relaxed">{{ $desk }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>


<!-- ═══════════ PRESTASI ═══════════ -->
<section id="prestasi" class="py-20 px-4 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <span class="sec-label">Kebanggaan Kita</span>
            <h2 class="text-3xl font-extrabold sec-title mt-1">Prestasi Gemilang</h2>
            <div class="sec-divider mx-auto"></div>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">Penghargaan yang diraih siswa dan sekolah di tingkat kabupaten, provinsi, dan nasional.</p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            @php
            $prestasi = [
                ['🥇','Juara 1 LKS Teknik Komputer','Nasional','2024','Ahmad Fauzi'],
                ['🥇','Juara 1 Karya Tulis Ilmiah','Provinsi','2024','Siti Rahayu'],
                ['🥈','Juara 2 Olimpiade Matematika','Nasional','2024','Budi Santoso'],
                ['🏆','Sekolah Adiwiyata','Nasional','2024','Institusi'],
                ['🥇','Juara 1 Debat Bahasa Inggris','Provinsi','2025','Dewi Lestari'],
                ['🥈','Juara 2 Paskibraka','Kabupaten','2024','Rudi Hermawan'],
                ['🎖️','Finalis Lomba Robotika','Nasional','2025','Tim Robotika'],
                ['🥇','Juara 1 Futsal Pelajar','Kabupaten','2025','Tim Futsal'],
            ];
            $levelColor = ['Nasional'=>'#B91C1C','Provinsi'=>'#1B7A8A','Kabupaten'=>'#E6920A'];
            @endphp
            @foreach($prestasi as [$medali,$judul,$level,$tahun,$nama])
            <div class="card-alt p-5 text-center">
                <div class="text-4xl mb-3">{{ $medali }}</div>
                <span class="inline-block text-xs font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full mb-2"
                      style="color:{{ $levelColor[$level] ?? '#1B7A8A' }}; background:{{ $levelColor[$level] ?? '#1B7A8A' }}18;">
                    {{ $level }}
                </span>
                <h3 class="font-bold text-sm leading-snug sec-title mb-2">{{ $judul }}</h3>
                <p class="text-xs text-gray-400">{{ $nama }} &middot; {{ $tahun }}</p>
            </div>
            @endforeach
        </div>

        <!-- CTA Banner -->
        <div class="cta-banner rounded-2xl p-7 flex flex-col md:flex-row items-center justify-between gap-5">
            <div class="relative z-10">
                <h3 class="text-xl font-bold text-white">🏆 Total 200+ Prestasi Diraih</h3>
                <p class="text-sm mt-1 text-white/75">Dari tingkat kabupaten hingga nasional dalam 5 tahun terakhir</p>
            </div>
            <a href="#" class="relative z-10 btn-gold font-bold px-7 py-2.5 rounded-xl text-sm whitespace-nowrap">
                Lihat Semua Prestasi
            </a>
        </div>
    </div>
</section>


<!-- ═══════════ EKSTRAKURIKULER ═══════════ -->
<section id="ekskul" class="py-20 px-4" style="background:#F0F7F9;">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <span class="sec-label">Pengembangan Diri</span>
            <h2 class="text-3xl font-extrabold sec-title mt-1">Ekstrakurikuler</h2>
            <div class="sec-divider mx-auto"></div>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">Kembangkan bakat dan minatmu di luar kelas bersama teman-teman.</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @php
            $ekskul = [
                ['⚽','Futsal',48,'linear-gradient(135deg,#1B7A8A,#0D4A57)'],
                ['🏐','Bola Voli',36,'linear-gradient(135deg,#E6920A,#c97d08)'],
                ['🥋','Pencak Silat',30,'linear-gradient(135deg,#1B7A8A,#145F6E)'],
                ['🎭','Teater & Drama',25,'linear-gradient(135deg,#0D4A57,#1B7A8A)'],
                ['🎵','Paduan Suara',40,'linear-gradient(135deg,#145F6E,#2399AD)'],
                ['🤖','Robotika',22,'linear-gradient(135deg,#112D3E,#1B7A8A)'],
                ['📸','Fotografi',28,'linear-gradient(135deg,#E6920A,#1B7A8A)'],
                ['🌿','Pramuka',120,'linear-gradient(135deg,#1a5c2e,#26834a)'],
                ['🎨','Seni Lukis',20,'linear-gradient(135deg,#1B7A8A,#0D4A57)'],
                ['📰','Jurnalistik',18,'linear-gradient(135deg,#112D3E,#145F6E)'],
                ['🏹','Paskibraka',60,'linear-gradient(135deg,#B91C1C,#7f1d1d)'],
                ['💻','Coding & IT',35,'linear-gradient(135deg,#0D4A57,#2399AD)'],
            ];
            @endphp
            @foreach($ekskul as [$emoji,$nama,$anggota,$bg])
            <div class="grad-card p-5 text-white cursor-pointer" style="background:{{ $bg }};">
                <div class="text-3xl mb-2">{{ $emoji }}</div>
                <h3 class="font-bold text-sm mb-1">{{ $nama }}</h3>
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 bg-white/50 rounded-full"></span>
                    <p class="text-xs opacity-75">{{ $anggota }} anggota</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ═══════════ FASILITAS ═══════════ -->
<section id="fasilitas" class="py-20 px-4 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <span class="sec-label">Sarana & Prasarana</span>
            <h2 class="text-3xl font-extrabold sec-title mt-1">Fasilitas Lengkap</h2>
            <div class="sec-divider mx-auto"></div>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">Fasilitas modern pendukung proses belajar dan pengembangan potensi siswa.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            @php
            $fasilitas = [
                ['🔬','Lab Sains','Laboratorium Fisika, Kimia, dan Biologi dengan peralatan modern standar nasional.'],
                ['💻','Lab Komputer','2 ruang lab komputer dengan 40 unit PC terbaru dan koneksi internet cepat.'],
                ['📚','Perpustakaan','Koleksi 15.000+ buku fisik dan akses e-book serta jurnal ilmiah digital.'],
                ['🏟️','Lapangan Olahraga','Lapangan sepak bola, basket, dan voli berstandar untuk kegiatan olahraga.'],
                ['🎭','Aula & Auditorium','Aula serbaguna berkapasitas 800 orang untuk seminar dan pentas seni.'],
                ['🕌','Masjid Sekolah','Masjid sekolah untuk pembinaan akhlak dan kegiatan keagamaan siswa.'],
            ];
            @endphp
            @foreach($fasilitas as [$icon,$nama,$desk])
            <div class="card p-6 flex gap-5">
                <div class="text-5xl shrink-0 leading-none mt-1">{{ $icon }}</div>
                <div>
                    <h3 class="font-bold sec-title mb-1.5">{{ $nama }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $desk }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>


<!-- ═══════════ GALERI ═══════════ -->
<section class="py-20 px-4" style="background:#F0F7F9;">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <span class="sec-label">Momen Berharga</span>
            <h2 class="text-3xl font-extrabold sec-title mt-1">Galeri Sekolah</h2>
            <div class="sec-divider mx-auto"></div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @php
            $galeri = [
                ['linear-gradient(135deg,#1B7A8A,#0D4A57)','Upacara Bendera','🇮🇩'],
                ['linear-gradient(135deg,#E6920A,#c97d08)','Festival Budaya','🎭'],
                ['linear-gradient(135deg,#1a5c2e,#26834a)','Olahraga','⚽'],
                ['linear-gradient(135deg,#145F6E,#2399AD)','Pentas Seni','🎵'],
                ['linear-gradient(135deg,#112D3E,#1B7A8A)','Wisuda','🎓'],
                ['linear-gradient(135deg,#1B7A8A,#145F6E)','Pramuka','⛺'],
                ['linear-gradient(135deg,#0D4A57,#1B7A8A)','Olimpiade','🏆'],
                ['linear-gradient(135deg,#1a5c2e,#1B7A8A)','Penghijauan','🌱'],
            ];
            @endphp
            @foreach($galeri as [$bg,$label,$emoji])
            <div class="grad-card aspect-square flex flex-col items-center justify-center cursor-pointer group"
                 style="background:{{ $bg }};">
                <span class="text-5xl transition-transform group-hover:scale-110">{{ $emoji }}</span>
                <span class="text-white text-xs font-semibold mt-2 text-center px-2">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ═══════════ BERITA ═══════════ -->
<section class="py-20 px-4 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <span class="sec-label">Informasi</span>
            <h2 class="text-3xl font-extrabold sec-title mt-1">Berita Terkini</h2>
            <div class="sec-divider mx-auto"></div>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            @php
            $berita = [
                ['Akademik','20 Mei 2025',
                 'SMK Muhammadiyah Sempor Raih Kelulusan 100% di Tahun 2025',
                 'Seluruh siswa kelas XII berhasil lulus dengan nilai rata-rata melampaui standar nasional...'],
                ['Prestasi','18 Mei 2025',
                 'Tim LKS Teknik Komputer Lolos ke Babak Final Nasional',
                 'Tim LKS SMK Muhammadiyah Sempor berhasil melewati babak penyisihan dan siap bertarung di final...'],
                ['Kegiatan','15 Mei 2025',
                 'PPDB 2025/2026 Resmi Dibuka, Ini Jadwal dan Syaratnya',
                 'Penerimaan Peserta Didik Baru tahun ajaran 2025/2026 resmi dibuka. Pendaftaran dapat dilakukan secara online...'],
            ];
            @endphp
            @foreach($berita as [$kat,$tanggal,$judul,$desk])
            <div class="card overflow-hidden flex flex-col">
                <div class="news-bar"></div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full"
                              style="background:var(--teal-light); color:var(--teal-dark);">{{ $kat }}</span>
                        <span class="text-xs text-gray-400">{{ $tanggal }}</span>
                    </div>
                    <h3 class="font-bold sec-title mb-2 leading-snug text-base">{{ $judul }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed flex-1">{{ $desk }}</p>
                    <a href="#" class="inline-flex items-center gap-1 mt-5 text-sm font-semibold transition hover:gap-2"
                       style="color:var(--teal);">
                        Baca selengkapnya
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>


<!-- ═══════════ KONTAK ═══════════ -->
<section id="kontak" class="py-20 px-4" style="background:#F0F7F9;">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <span class="sec-label">Hubungi Kami</span>
            <h2 class="text-3xl font-extrabold sec-title mt-1">Kontak Sekolah</h2>
            <div class="sec-divider mx-auto"></div>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            <!-- Alamat -->
            <div class="card p-7 text-center flex flex-col items-center">
                <div class="icon-ring mb-4" style="background:var(--teal-light);">
                    <svg class="w-6 h-6" style="color:var(--teal);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="font-bold sec-title mb-2">Alamat</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Jl. Klampok-Gombong Km13, Ds. Sampang<br>Kec. Sempor, Kab. Kebumen</p>
                <a href="https://maps.google.com/?q=SMK+Muhammadiyah+Sempor" target="_blank"
                   class="mt-4 text-xs font-semibold transition hover:underline" style="color:var(--teal);">
                    Lihat di Peta →
                </a>
            </div>
            <!-- WhatsApp & Email -->
            <div class="card p-7 text-center flex flex-col items-center">
                <div class="icon-ring mb-4" style="background:var(--teal-light);">
                    <svg class="w-6 h-6" style="color:var(--teal);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <h3 class="font-bold sec-title mb-2">WhatsApp & Email</h3>
                <a href="https://wa.me/6281325540947" target="_blank"
                   class="text-sm text-gray-500 hover:text-green-600 transition">081325540947</a>
                <a href="mailto:smkmuhse@gmail.com"
                   class="text-sm text-gray-500 hover:text-blue-600 transition mt-1">smkmuhse@gmail.com</a>
            </div>
            <!-- Jam Operasional -->
            <div class="card p-7 text-center flex flex-col items-center">
                <div class="icon-ring mb-4" style="background:var(--gold-light);">
                    <svg class="w-6 h-6" style="color:var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="font-bold sec-title mb-2">Jam Operasional</h3>
                <p class="text-sm text-gray-500">Senin – Jumat<br><span class="font-semibold text-gray-700">07.00 – 16.00 WIB</span></p>
                <p class="text-sm text-gray-500 mt-2">Sabtu<br><span class="font-semibold text-gray-700">07.00 – 12.00 WIB</span></p>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════ CTA BANNER ═══════════ -->
<div class="py-12 px-4" style="background:#F0F7F9;">
    <div class="max-w-4xl mx-auto">
        <div class="cta-banner rounded-2xl p-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-5 relative z-10">
                <img src="{{ asset('image/smk.png') }}" alt="Logo" class="w-16 h-16 object-contain rounded-2xl bg-white/90 p-1.5 shrink-0">
                <div class="text-white">
                    <p class="text-sm font-semibold opacity-80 mb-0.5">Tahun Ajaran 2025/2026 — Kuota Terbatas!</p>
                    <h3 class="text-xl font-black">Daftar Sekarang di</h3>
                    <p class="text-lg font-extrabold" style="color:#F9C940;">SMK Muhammadiyah Sempor</p>
                </div>
            </div>
            <a href="{{ Route::has('register') ? route('register') : '#' }}"
               class="relative z-10 btn-gold font-black px-8 py-3.5 rounded-xl text-sm whitespace-nowrap uppercase tracking-wide">
                Daftar Sekarang
            </a>
        </div>
    </div>
</div>


<!-- ═══════════ FOOTER ═══════════ -->
<!-- Wave atas footer -->
<div style="background:#F0F7F9; line-height:0;">
    <svg viewBox="0 0 1440 56" preserveAspectRatio="none" class="w-full block" style="height:50px;" fill="#0B1E2D">
        <path d="M0,28 C480,56 960,0 1440,28 L1440,56 L0,56 Z"/>
    </svg>
</div>

<footer style="background:var(--navy-dark);" class="pt-10 pb-6 px-4">
    <div class="max-w-7xl mx-auto grid md:grid-cols-4 gap-8 pb-8 border-b border-white/10">
        <!-- Brand -->
        <div class="md:col-span-2">
            <div class="flex items-center gap-3 mb-4">
                <img src="{{ asset('image/smk.png') }}" alt="Logo" class="w-10 h-10 object-contain rounded-xl bg-white/90 p-0.5 shrink-0">
                <div>
                    <p class="font-bold text-white text-sm">SMK Muhammadiyah Sempor</p>
                    <p class="text-xs italic" style="color:rgba(255,255,255,0.5);">Excellent in Taqwa, Science & Professional</p>
                </div>
            </div>
            <p class="text-sm leading-relaxed" style="color:rgba(255,255,255,0.55);">
                Jl. Klampok-Gombong Km13, Ds. Sampang,<br>Kec. Sempor, Kab. Kebumen, Jawa Tengah.
            </p>
            <!-- Social -->
            <div class="flex gap-3 mt-4">
                <a href="#" aria-label="Instagram" class="w-8 h-8 rounded-lg flex items-center justify-center transition"
                   style="background:rgba(255,255,255,0.08);" onmouseover="this.style.background='var(--teal)'" onmouseout="this.style.background='rgba(255,255,255,0.08)'">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </a>
                <a href="#" aria-label="YouTube" class="w-8 h-8 rounded-lg flex items-center justify-center transition"
                   style="background:rgba(255,255,255,0.08);" onmouseover="this.style.background='#c00'" onmouseout="this.style.background='rgba(255,255,255,0.08)'">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/>
                    </svg>
                </a>
                <a href="https://wa.me/6281325540947" target="_blank" aria-label="WhatsApp"
                   class="w-8 h-8 rounded-lg flex items-center justify-center transition"
                   style="background:rgba(255,255,255,0.08);" onmouseover="this.style.background='#25D366'" onmouseout="this.style.background='rgba(255,255,255,0.08)'">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Navigasi -->
        <div>
            <h4 class="font-semibold text-white mb-4 text-sm">Navigasi</h4>
            <ul class="space-y-2 text-sm" style="color:rgba(255,255,255,0.55);">
                <li><a href="#beranda"   class="hover:text-white transition">Beranda</a></li>
                <li><a href="#kegiatan"  class="hover:text-white transition">Kegiatan</a></li>
                <li><a href="#prestasi"  class="hover:text-white transition">Prestasi</a></li>
                <li><a href="#ekskul"    class="hover:text-white transition">Ekstrakurikuler</a></li>
                <li><a href="#fasilitas" class="hover:text-white transition">Fasilitas</a></li>
                <li><a href="#kontak"    class="hover:text-white transition">Kontak</a></li>
            </ul>
        </div>

        <!-- Akun & Kontak -->
        <div>
            <h4 class="font-semibold text-white mb-4 text-sm">Akun Siakad</h4>
            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="btn-teal inline-block text-sm px-5 py-2 rounded-lg font-semibold mb-4">
                    Masuk ke Siakad
                </a>
            @endif
            <h4 class="font-semibold text-white mb-2 text-sm mt-2">Kontak</h4>
            <p class="text-sm" style="color:rgba(255,255,255,0.55);">
                <span class="block">Julaeha, S.Pd.</span>
                <a href="https://wa.me/6281325540947" target="_blank" class="hover:text-white transition">081325540947</a>
            </p>
            <a href="mailto:smkmuhse@gmail.com" class="text-sm hover:text-white transition mt-1 block"
               style="color:rgba(255,255,255,0.55);">smkmuhse@gmail.com</a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto pt-5 text-center text-xs" style="color:rgba(255,255,255,0.35);">
        &copy; {{ date('Y') }} SMK Muhammadiyah Sempor — Hak Cipta Dilindungi.
    </div>
</footer>


<!-- ═══════════ SCRIPTS ═══════════ -->
<script>
    // ── Scroll progress ──
    const bar = document.getElementById('scroll-progress');
    function updateProgress() {
        const total = document.documentElement.scrollHeight - window.innerHeight;
        bar.style.width = (window.scrollY / total * 100) + '%';
    }

    // ── Navbar scroll shadow ──
    const navbar = document.getElementById('navbar');
    function updateNavbar() {
        navbar.classList.toggle('scrolled', window.scrollY > 20);
    }

    window.addEventListener('scroll', () => { updateProgress(); updateNavbar(); }, { passive: true });

    // ── Mobile menu ──
    const menuBtn  = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const iconOpen = document.getElementById('icon-open');
    const iconClose = document.getElementById('icon-close');

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

    // ── Intersection observer for fade-in on scroll ──
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
        el.style.transition = 'opacity .5s ease, transform .5s ease, box-shadow .25s, border-color .25s';
        observer.observe(el);
    });
</script>
</body>
</html>
