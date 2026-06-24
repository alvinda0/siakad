@extends('layouts.public')
@section('title', 'Prestasi')

@push('styles')
<style>
    .sec-label  { color:var(--teal); letter-spacing:.1em; font-size:.75rem; font-weight:700; text-transform:uppercase; }
    .sec-title  { color:var(--navy); font-weight:800; }
    .card-alt   { background:#FAFEFF; border-radius:1.25rem; border:1px solid rgba(27,122,138,0.10); transition:transform .25s,box-shadow .25s; }
    .card-alt:hover { transform:translateY(-6px); box-shadow:0 20px 44px rgba(27,122,138,0.13); }
    .cta-banner { background:linear-gradient(135deg,var(--teal-deeper) 0%,var(--teal) 60%,#2BA8BF 100%); position:relative; overflow:hidden; }
    .cta-banner::before { content:''; position:absolute; right:-60px; top:-60px; width:280px; height:280px; border-radius:50%; background:rgba(255,255,255,0.07); }
    .page-hero  { background:linear-gradient(135deg,var(--teal-deeper) 0%,#0D4A57 100%); }
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="page-hero text-white py-16 px-4 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background:radial-gradient(circle at 80% 50%, white, transparent 60%);"></div>
    <div class="max-w-7xl mx-auto relative z-10">
        <p class="text-xs font-bold uppercase tracking-widest text-white/70 mb-2">Kebanggaan Kita</p>
        <h1 class="text-4xl font-black mb-3">Prestasi Gemilang</h1>
        <p class="text-white/80 max-w-lg text-sm leading-relaxed">Penghargaan yang diraih siswa dan sekolah di tingkat kabupaten, provinsi, dan nasional.</p>
    </div>
    <div class="absolute bottom-0 left-0 w-full overflow-hidden" style="height:72px; line-height:0;">
        <svg viewBox="0 0 1440 72" preserveAspectRatio="none" class="w-full h-full" fill="white">
            <path d="M0,36 C480,72 960,0 1440,36 L1440,72 L0,72 Z"/>
        </svg>
    </div>
</div>

{{-- Stats Bar --}}
<div class="py-8 px-4 bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto grid grid-cols-3 md:grid-cols-6 gap-4 text-center">
        @php
        $statItems = [
            ['🏅', $stats['total'],     'Total'],
            ['🥇', $stats['nasional'],  'Nasional'],
            ['🥈', $stats['provinsi'],  'Provinsi'],
            ['🥉', $stats['kabupaten'], 'Kabupaten'],
            ['🏵️', $stats['kecamatan'], 'Kecamatan'],
            ['🌿', $stats['desa'],      'Desa'],
        ];
        @endphp
        @foreach($statItems as [$icon, $count, $label])
        <div>
            <span class="text-2xl">{{ $icon }}</span>
            <p class="text-2xl font-black mt-1" style="color:var(--gold);">{{ $count ?: '—' }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $label }}</p>
        </div>
        @endforeach
    </div>
</div>

{{-- Content --}}
<section class="py-20 px-4 bg-white">
    <div class="max-w-7xl mx-auto">

        {{-- Filter Tingkat --}}
        <div class="flex flex-wrap gap-2 mb-10 justify-center">
            @php $levels = ['Semua', 'Nasional', 'Provinsi', 'Kabupaten', 'Kecamatan', 'Desa', 'Lainnya']; @endphp
            @foreach($levels as $lv)
            <button onclick="filterLevel(this, '{{ $lv }}')"
                    class="lv-btn text-xs font-bold px-4 py-1.5 rounded-full border transition {{ $lv === 'Semua' ? 'text-white border-transparent' : 'border-slate-200 text-slate-500 bg-white' }}"
                    style="{{ $lv === 'Semua' ? 'background:var(--gold);' : '' }}">
                {{ $lv }}
            </button>
            @endforeach
        </div>

        @if($prestasi->isEmpty())
            {{-- Empty state --}}
            <div class="text-center py-24 text-gray-400">
                <div class="text-6xl mb-4">🏅</div>
                <h3 class="font-bold text-lg mb-1">Belum ada data prestasi</h3>
                <p class="text-sm">Data prestasi akan ditampilkan di sini setelah diinput oleh admin.</p>
            </div>
        @else
            @php
                $levelColor = [
                    'Nasional'  => ['text' => '#B91C1C', 'bg' => '#FEE2E2'],
                    'Provinsi'  => ['text' => '#1B7A8A', 'bg' => '#E0F4F7'],
                    'Kabupaten' => ['text' => '#E6920A', 'bg' => '#FEF3C7'],
                    'Kecamatan' => ['text' => '#7C3AED', 'bg' => '#EDE9FE'],
                    'Desa'      => ['text' => '#065F46', 'bg' => '#D1FAE5'],
                    'Lainnya'   => ['text' => '#64748B', 'bg' => '#F1F5F9'],
                ];
            @endphp

            <div id="prestasi-grid" class="grid md:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">
                @foreach($prestasi as $item)
                @php
                    $color = $levelColor[$item->tingkat] ?? ['text' => '#64748B', 'bg' => '#F1F5F9'];
                    $orientasi = $item->gambarOrientasi(); // 'portrait' | 'landscape' | null
                    $isPortrait = $orientasi === 'portrait';
                @endphp

                {{-- Portrait: gambar kiri, teks kanan → card span 2 kolom supaya cukup --}}
                {{-- Landscape: gambar atas, teks bawah → card normal --}}
                <div class="card-alt prestasi-item {{ $isPortrait ? 'lg:col-span-2 flex flex-row gap-4 p-4 text-left items-stretch' : 'p-5 text-center' }}"
                     data-level="{{ $item->tingkat }}">

                    @if($item->gambar)
                        {{-- Portrait: gambar di kiri, teks di kanan --}}
                        @if($isPortrait)
                            <div class="flex-shrink-0 self-stretch">
                                <img src="{{ $item->gambarUrl() }}" alt="{{ $item->judul }}"
                                     class="w-48 h-full rounded-2xl shadow-sm object-cover object-top">
                            </div>
                            <div class="flex flex-col justify-start min-w-0 flex-1 py-1">
                                <span class="inline-block text-xs font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full mb-2 self-start"
                                      style="color:{{ $color['text'] }}; background:{{ $color['bg'] }};">
                                    {{ $item->tingkat }}
                                </span>
                                <h3 class="font-bold text-sm leading-snug mb-2" style="color:var(--navy);">{{ $item->judul }}</h3>
                                <p class="text-xs text-gray-400">{{ $item->nama_peraih }} &middot; {{ $item->tahun }}</p>
                                @if($item->deskripsi)
                                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">{{ Str::limit($item->deskripsi, 200) }}</p>
                                @endif
                            </div>

                        {{-- Landscape: gambar atas, teks bawah --}}
                        @else
                            <div class="flex justify-center mb-3">
                                <img src="{{ $item->gambarUrl() }}" alt="{{ $item->judul }}"
                                     class="max-w-full max-h-72 w-auto h-auto rounded-2xl shadow-sm object-contain">
                            </div>
                            <span class="inline-block text-xs font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full mb-2"
                                  style="color:{{ $color['text'] }}; background:{{ $color['bg'] }};">
                                {{ $item->tingkat }}
                            </span>
                            <h3 class="font-bold text-sm leading-snug mb-2" style="color:var(--navy);">{{ $item->judul }}</h3>
                            <p class="text-xs text-gray-400">{{ $item->nama_peraih }} &middot; {{ $item->tahun }}</p>
                            @if($item->deskripsi)
                                <p class="text-xs text-slate-500 mt-2 leading-relaxed">{{ Str::limit($item->deskripsi, 80) }}</p>
                            @endif
                        @endif

                    @else
                        {{-- Tidak ada gambar → tampilan default dengan emoji medali --}}
                        <div class="text-4xl mb-3">{{ $item->medali }}</div>
                        <span class="inline-block text-xs font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full mb-2"
                              style="color:{{ $color['text'] }}; background:{{ $color['bg'] }};">
                            {{ $item->tingkat }}
                        </span>
                        <h3 class="font-bold text-sm leading-snug mb-2" style="color:var(--navy);">{{ $item->judul }}</h3>
                        <p class="text-xs text-gray-400">{{ $item->nama_peraih }} &middot; {{ $item->tahun }}</p>
                        @if($item->deskripsi)
                            <p class="text-xs text-slate-500 mt-2 leading-relaxed">{{ Str::limit($item->deskripsi, 80) }}</p>
                        @endif
                    @endif
                </div>
                @endforeach
            </div>

            <div id="empty-level" class="hidden text-center py-20 text-gray-400">
                <div class="text-5xl mb-3">🏅</div>
                <p class="font-medium">Tidak ada prestasi dalam kategori ini.</p>
            </div>
        @endif

        {{-- CTA --}}
        <div class="cta-banner rounded-2xl p-7 flex flex-col md:flex-row items-center justify-between gap-5 mt-4">
            <div class="relative z-10">
                <h3 class="text-xl font-bold text-white">🏆 {{ $stats['total'] > 0 ? 'Total '.$stats['total'].' Prestasi Diraih' : 'Raih Prestasi Bersama Kami' }}</h3>
                <p class="text-sm mt-1 text-white/75">Dari tingkat kabupaten hingga nasional — jadilah bagian dari kebanggaan ini</p>
            </div>
            <a href="{{ route('kandidat.create') }}"
               class="relative z-10 font-bold px-7 py-2.5 rounded-xl text-sm whitespace-nowrap text-white"
               style="background:var(--gold);">
                Bergabung Bersama Kami →
            </a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
function filterLevel(btn, level) {
    document.querySelectorAll('.lv-btn').forEach(b => {
        b.style.background = '';
        b.style.color = '';
        b.classList.add('border-slate-200', 'text-slate-500', 'bg-white');
        b.classList.remove('border-transparent', 'text-white');
    });
    btn.style.background = 'var(--gold)';
    btn.style.color = '#fff';
    btn.classList.remove('border-slate-200', 'text-slate-500', 'bg-white');
    btn.classList.add('border-transparent', 'text-white');

    let visible = 0;
    document.querySelectorAll('.prestasi-item').forEach(el => {
        const show = level === 'Semua' || el.dataset.level === level;
        el.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    const empty = document.getElementById('empty-level');
    if (empty) empty.classList.toggle('hidden', visible > 0);
}
</script>
@endpush
