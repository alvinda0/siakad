@extends('layouts.public')
@section('title', 'Beranda')

@push('styles')
<style>
    .hero-bg { background: linear-gradient(135deg, var(--teal-deeper) 0%, var(--teal) 55%, #2BA8BF 100%); position: relative; overflow: hidden; }
    .hero-orb { position:absolute; border-radius:50%; background: radial-gradient(circle, rgba(255,255,255,0.12), transparent 70%); animation: float 8s ease-in-out infinite; }
    @keyframes float { 0%,100%{ transform:translateY(0) scale(1); } 50%{ transform:translateY(-20px) scale(1.04); } }
    @keyframes fadeUp { from { opacity:0; transform:translateY(28px); } to { opacity:1; transform:translateY(0); } }
    .anim-1 { animation: fadeUp .7s ease both; }
    .anim-2 { animation: fadeUp .7s .15s ease both; }
    .anim-3 { animation: fadeUp .7s .3s ease both; }
    .anim-4 { animation: fadeUp .7s .45s ease both; }
    .anim-5 { animation: fadeUp .7s .6s ease both; }
    .stat-card { background: rgba(255,255,255,0.12); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); transition: transform .3s cubic-bezier(.34,1.56,.64,1), background .25s, box-shadow .25s; }
    .stat-card:hover { transform:translateY(-8px) scale(1.03); background:rgba(255,255,255,0.20); box-shadow:0 16px 40px rgba(0,0,0,0.18); }
    @keyframes marquee { 0%{transform:translateX(0)} 100%{transform:translateX(-100%)} }
    .animate-marquee { display:inline-block; animation: marquee 25s linear infinite; }
    .ticker-track { display:flex; animation: ticker-scroll 25s linear infinite; }
    .ticker-item  { flex-shrink:0; }
    @keyframes ticker-scroll { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }
    .sec-label { color:var(--teal); letter-spacing:.1em; font-size:.75rem; font-weight:700; text-transform:uppercase; }
    .sec-title  { color:var(--navy); font-weight:800; }
    .sec-divider { display:inline-block; width:48px; height:4px; border-radius:2px; background: linear-gradient(90deg, var(--teal), var(--gold)); margin-top:8px; margin-bottom:12px; }
    .card { background:#fff; border-radius:1.25rem; border: 1px solid rgba(27,122,138,0.08); transition: transform .25s, box-shadow .25s; }
    .card:hover { transform:translateY(-6px); box-shadow:0 20px 44px rgba(27,122,138,0.13); }
    .news-bar { height:4px; border-radius:4px 4px 0 0; background:var(--teal); }
    .cta-banner { background: linear-gradient(135deg, var(--teal-deeper) 0%, var(--teal) 60%, #2BA8BF 100%); position:relative; overflow:hidden; }
    .cta-banner::before { content:''; position:absolute; right:-60px; top:-60px; width:280px; height:280px; border-radius:50%; background:rgba(255,255,255,0.07); }
</style>
@endpush

@section('content')

<!-- ═══════════ HERO ═══════════ -->
<section class="hero-bg text-white py-24 px-4 relative">
    <div class="hero-orb" style="width:520px;height:520px;right:-100px;top:-100px;animation-delay:0s;"></div>
    <div class="hero-orb" style="width:320px;height:320px;right:80px;top:60px;animation-delay:3s;"></div>
    <div class="hero-orb" style="width:200px;height:200px;left:30px;bottom:80px;animation-delay:5s;"></div>
    <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-14 items-center relative z-10">
        <div>
            <span class="anim-1 inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-full mb-5" style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25);">
                <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
                Sistem Informasi Akademik
            </span>
            <h1 class="anim-2 text-4xl md:text-5xl lg:text-6xl font-black leading-[1.1] mb-4">
                Selamat Datang di<br>
                <span style="color:#F9C940; text-shadow:0 0 40px rgba(249,201,64,0.4);">SMK Muhammadiyah<br>Sempor</span>
            </h1>
            <div class="anim-3 flex flex-wrap gap-2 mb-5">
                @foreach(['✦ Taqwa', '✦ Science', '✦ Professional'] as $tag)
                <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full" style="color:#F9C940; background:rgba(249,201,64,0.15); border:1px solid rgba(249,201,64,0.3);">{{ $tag }}</span>
                @endforeach
            </div>
            <p class="anim-4 text-base leading-relaxed mb-8 max-w-md" style="color:rgba(255,255,255,0.85);">
                Unggul dalam prestasi, berkarakter Islami, dan berwawasan teknologi — mencetak generasi cerdas dan berakhlak mulia.
            </p>
            <div class="anim-5 flex flex-wrap gap-3">
                <a href="{{ route('kegiatan') }}" class="btn-gold font-bold px-7 py-3 rounded-xl text-sm">Lihat Kegiatan</a>
                <a href="{{ route('kandidat.create') }}" class="btn-ghost-white font-semibold px-7 py-3 rounded-xl text-sm">Pendaftaran PPDB</a>
            </div>
        </div>
        <!-- STAT CARDS -->
        <div class="grid grid-cols-2 gap-4 anim-3">
            @php
            $statCards = [
                ['value' => $stats['siswa'],            'label' => 'Siswa Aktif',     'icon' => '🎓', 'delay' => '0s'],
                ['value' => $stats['guru'],              'label' => 'Tenaga Pendidik', 'icon' => '👨‍🏫', 'delay' => '.1s'],
                ['value' => $stats['prestasi'],          'label' => 'Prestasi Diraih', 'icon' => '🏆', 'delay' => '.2s'],
                ['value' => $stats['ekstrakurikuler'],   'label' => 'Ekstrakurikuler', 'icon' => '⚽', 'delay' => '.3s'],
            ];
            @endphp
            @foreach($statCards as $s)
            <div class="stat-card rounded-2xl p-6 text-center group" style="animation-delay:{{ $s['delay'] }};">
                <!-- icon with subtle pulse ring -->
                <div class="relative inline-flex items-center justify-center mb-2">
                    <span class="absolute inset-0 rounded-full bg-white opacity-10 scale-125 group-hover:scale-150 transition-transform duration-500"></span>
                    <span class="text-4xl">{{ $s['icon'] }}</span>
                </div>
                <!-- animated number -->
                <p class="text-3xl font-black leading-none mt-1 stat-counter"
                   data-target="{{ $s['value'] }}"
                   style="color:#F9C940;">0</p>
                <!-- thin accent line -->
                <div class="mx-auto my-2 rounded-full" style="width:32px;height:2px;background:rgba(249,201,64,0.5);"></div>
                <p class="text-sm font-semibold" style="color:rgba(255,255,255,0.80);">{{ $s['label'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
    <div class="absolute bottom-0 left-0 w-full overflow-hidden" style="height:80px; line-height:0;">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none" class="w-full h-full" fill="#F0F7F9">
            <path d="M0,40 C480,80 960,0 1440,40 L1440,80 L0,80 Z"/>
        </svg>
    </div>
</section>

<!-- ═══════════ TICKER ═══════════ -->
@if($tickers->isNotEmpty())
<div class="overflow-hidden py-2.5 px-4" style="background:var(--teal-dark);">
    <div class="max-w-7xl mx-auto flex items-center gap-4">
        <span class="text-xs font-extrabold px-2.5 py-1 rounded-full whitespace-nowrap shrink-0" style="background:var(--gold); color:#fff;">📢 INFO</span>
        <div class="overflow-hidden flex-1">
            {{-- Teks diduplikat agar loop terlihat seamless tanpa jeda --}}
            <div class="ticker-track flex whitespace-nowrap">
                @php $tickerText = $tickers->map(fn($t) => $t->icon . ' ' . $t->konten)->implode(' &nbsp;|&nbsp; '); @endphp
                <span class="ticker-item text-sm text-white pr-16">{!! $tickerText !!}</span>
                <span class="ticker-item text-sm text-white pr-16" aria-hidden="true">{!! $tickerText !!}</span>
            </div>
        </div>
    </div>
</div>
@endif

<!-- ═══════════ QUICK LINKS ═══════════ -->
<section class="py-16 px-4" style="background:#F0F7F9;">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-10">
            <span class="sec-label">Jelajahi</span>
            <h2 class="text-3xl font-extrabold sec-title mt-1">Halaman Sekolah</h2>
            <div class="sec-divider mx-auto"></div>
        </div>
        @php
        $pages = [
            ['route' => 'kegiatan',       'icon' => '🎓', 'title' => 'Kegiatan',        'desc' => 'Agenda dan kegiatan sekolah', 'bg' => 'var(--teal)'],
            ['route' => 'prestasi',       'icon' => '🏆', 'title' => 'Prestasi',         'desc' => 'Penghargaan dan pencapaian',  'bg' => 'var(--gold)'],
            ['route' => 'ekstrakurikuler','icon' => '⚽', 'title' => 'Ekstrakurikuler', 'desc' => 'Pengembangan bakat & minat',   'bg' => '#1a5c2e'],
            ['route' => 'fasilitas',      'icon' => '🏛️', 'title' => 'Fasilitas',        'desc' => 'Sarana dan prasarana modern', 'bg' => 'var(--navy)'],
            ['route' => 'pengumuman',     'icon' => '📋', 'title' => 'Pengumuman',       'desc' => 'Siswa diterima PPDB',         'bg' => '#B91C1C'],
            ['route' => 'informasi',      'icon' => '📢', 'title' => 'Informasi',        'desc' => 'Beasiswa & program strategis','bg' => '#0D4A57'],
            ['route' => 'kontak',         'icon' => '📞', 'title' => 'Kontak',           'desc' => 'Hubungi kami',                'bg' => '#145F6E'],
        ];
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-4">
            @foreach($pages as $p)
            <a href="{{ route($p['route']) }}" class="card p-5 text-center flex flex-col items-center gap-2 group cursor-pointer">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl mb-1 group-hover:scale-110 transition-transform" style="background:{{ $p['bg'] }}18;">{{ $p['icon'] }}</div>
                <p class="font-bold text-sm sec-title">{{ $p['title'] }}</p>
                <p class="text-xs text-gray-400 leading-tight text-center">{{ $p['desc'] }}</p>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- ═══════════ PENGUMUMAN PREVIEW ═══════════ -->
@if($diterima->count() > 0)
<section class="py-16 px-4 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-8 flex-wrap gap-3">
            <div>
                <span class="sec-label">PPDB {{ $tahunAjaran }}</span>
                <h2 class="text-2xl font-extrabold sec-title mt-1">Siswa Diterima</h2>
            </div>
            <a href="{{ route('pengumuman') }}" class="text-sm font-semibold px-5 py-2 rounded-xl transition"
               style="background:var(--teal-light); color:var(--teal-dark);">
                Lihat Semua →
            </a>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/60">
                            <th class="px-5 py-3 text-left font-semibold text-slate-500 uppercase tracking-wide text-xs">Nama</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-500 uppercase tracking-wide text-xs">Asal Sekolah</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-500 uppercase tracking-wide text-xs">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($diterima->take(5) as $siswa)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $siswa->nama_lengkap }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $siswa->asal_sekolah ?: '—' }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                    ✓ Diterima
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($diterima->total() > 5)
            <div class="px-5 py-3.5 border-t border-slate-100 text-sm text-slate-500">
                Menampilkan 5 dari <strong>{{ $diterima->total() }}</strong> siswa —
                <a href="{{ route('pengumuman') }}" class="font-semibold" style="color:var(--teal);">Lihat semua →</a>
            </div>
            @endif
        </div>
    </div>
</section>
@endif

@push('scripts')
<script>
(function () {
    /* ── Count-up animation triggered when cards enter the viewport ── */
    function countUp(el) {
        const target   = parseInt(el.dataset.target, 10);
        const duration = 1800; // ms
        const step     = 16;   // ~60 fps
        const increment = target / (duration / step);
        let current = 0;

        // Format with thousand-dot separator for 1000+
        function fmt(n) {
            return target >= 1000
                ? Math.floor(n).toLocaleString('id-ID')
                : Math.floor(n).toString();
        }

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            el.textContent = fmt(current);
        }, step);
    }

    const counters = document.querySelectorAll('.stat-counter');

    if ('IntersectionObserver' in window) {
        const obs = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    countUp(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.4 });

        counters.forEach(el => obs.observe(el));
    } else {
        // Fallback for old browsers
        counters.forEach(el => countUp(el));
    }
})();
</script>
@endpush

@endsection
