@extends('layouts.public')
@section('title', 'Ekstrakurikuler')

@push('styles')
<style>
    .sec-label { color:var(--teal); letter-spacing:.1em; font-size:.75rem; font-weight:700; text-transform:uppercase; }
    .sec-title  { color:var(--navy); font-weight:800; }
    .sec-divider { display:inline-block; width:48px; height:4px; border-radius:2px; background: linear-gradient(90deg, var(--teal), var(--gold)); margin-top:8px; margin-bottom:12px; }
    .grad-card { border-radius:1.25rem; overflow:hidden; transition: transform .25s, box-shadow .25s; }
    .grad-card:hover { transform:translateY(-6px) scale(1.02); box-shadow:0 20px 40px rgba(0,0,0,0.22); }
    .page-hero { background: linear-gradient(135deg, var(--teal-deeper) 0%, #0D4A57 100%); }
</style>
@endpush

@section('content')

<!-- Hero -->
<div class="page-hero text-white py-16 px-4 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background:radial-gradient(circle at 80% 50%, white, transparent 60%);"></div>
    <div class="max-w-7xl mx-auto relative z-10">
        <p class="text-xs font-bold uppercase tracking-widest text-white/70 mb-2">Pengembangan Diri</p>
        <h1 class="text-4xl font-black mb-3">Ekstrakurikuler</h1>
        <p class="text-white/80 max-w-lg text-sm leading-relaxed">
            Kembangkan bakat dan minatmu di luar kelas bersama teman-teman.
            Total <strong>{{ $wajib->count() + $pilihan->count() }}</strong> ekstrakurikuler tersedia.
        </p>
    </div>
    <div class="absolute bottom-0 left-0 w-full overflow-hidden" style="height:72px; line-height:0;">
        <svg viewBox="0 0 1440 72" preserveAspectRatio="none" class="w-full h-full" fill="#F0F7F9">
            <path d="M0,36 C480,72 960,0 1440,36 L1440,72 L0,72 Z"/>
        </svg>
    </div>
</div>

<!-- Content -->
<section class="py-20 px-4" style="background:#F0F7F9;">
    <div class="max-w-7xl mx-auto">

        <div class="text-center mb-12">
            <span class="sec-label">{{ $wajib->count() + $pilihan->count() }} Pilihan</span>
            <h2 class="text-3xl font-extrabold sec-title mt-1">Temukan Passionmu</h2>
            <div class="sec-divider mx-auto"></div>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">Setiap siswa dapat mengikuti minimal 1 ekstrakurikuler wajib dan 1 pilihan sesuai minat.</p>
        </div>

        @if($wajib->isNotEmpty())
        <!-- Wajib -->
        <h3 class="text-lg font-extrabold sec-title mb-5 flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-white text-xs font-black" style="background:var(--teal);">W</span>
            Ekstrakurikuler Wajib
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-12">
            @php
            $wajibGradients = [
                'linear-gradient(135deg,#1a5c2e,#26834a)',
                'linear-gradient(135deg,#B91C1C,#7f1d1d)',
                'linear-gradient(135deg,#1B7A8A,#0D4A57)',
                'linear-gradient(135deg,#112D3E,#145F6E)',
                'linear-gradient(135deg,#E6920A,#c97d08)',
            ];
            @endphp
            @foreach($wajib as $idx => $item)
            @php $bg = $wajibGradients[$idx % count($wajibGradients)]; @endphp
            <div class="grad-card text-white flex flex-col" style="background:{{ $bg }};">
                @if($item->gambarUrl())
                    <img src="{{ $item->gambarUrl() }}" alt="{{ $item->nama }}"
                         class="w-full h-44 object-cover flex-shrink-0">
                @else
                    <div class="w-full h-44 flex-shrink-0" style="background:rgba(0,0,0,0.15);"></div>
                @endif
                <div class="p-4">
                    <span class="inline-block text-xs font-black px-2 py-0.5 rounded-full bg-white/20 mb-2">WAJIB</span>
                    <h3 class="font-bold text-sm mb-1">{{ $item->nama }}</h3>
                    @if($item->jumlah_anggota > 0)
                        <p class="text-xs opacity-75">{{ $item->jumlah_anggota }} anggota</p>
                    @endif
                    @if($item->jadwal)
                        <p class="text-xs opacity-60 mt-1">🕐 {{ $item->jadwal }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @if($pilihan->isNotEmpty())
        <!-- Pilihan -->
        <h3 class="text-lg font-extrabold sec-title mb-5 flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-white text-xs font-black" style="background:var(--gold);">P</span>
            Ekstrakurikuler Pilihan
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @php
            $pilihanGradients = [
                'linear-gradient(135deg,#1B7A8A,#0D4A57)',
                'linear-gradient(135deg,#E6920A,#c97d08)',
                'linear-gradient(135deg,#1B7A8A,#145F6E)',
                'linear-gradient(135deg,#0D4A57,#1B7A8A)',
                'linear-gradient(135deg,#145F6E,#2399AD)',
                'linear-gradient(135deg,#112D3E,#1B7A8A)',
                'linear-gradient(135deg,#E6920A,#1B7A8A)',
                'linear-gradient(135deg,#1B7A8A,#0D4A57)',
                'linear-gradient(135deg,#112D3E,#145F6E)',
                'linear-gradient(135deg,#0D4A57,#2399AD)',
            ];
            @endphp
            @foreach($pilihan as $idx => $item)
            @php $bg = $pilihanGradients[$idx % count($pilihanGradients)]; @endphp
            <div class="grad-card text-white cursor-pointer flex flex-col" style="background:{{ $bg }};">
                @if($item->gambarUrl())
                    <img src="{{ $item->gambarUrl() }}" alt="{{ $item->nama }}"
                         class="w-full h-44 object-cover flex-shrink-0">
                @else
                    <div class="w-full h-44 flex-shrink-0" style="background:rgba(0,0,0,0.15);"></div>
                @endif
                <div class="p-4">
                    <h3 class="font-bold text-sm mb-1">{{ $item->nama }}</h3>
                    @if($item->jumlah_anggota > 0)
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 bg-white/50 rounded-full"></span>
                        <p class="text-xs opacity-75">{{ $item->jumlah_anggota }} anggota</p>
                    </div>
                    @endif
                    @if($item->jadwal)
                        <p class="text-xs opacity-60 mt-1">🕐 {{ $item->jadwal }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @if($wajib->isEmpty() && $pilihan->isEmpty())
        <div class="text-center py-16">
            <div class="text-6xl mb-4">🏅</div>
            <h3 class="font-bold text-slate-600 mb-1">Data ekstrakurikuler belum tersedia</h3>
            <p class="text-sm text-slate-400">Segera hadir. Hubungi sekolah untuk informasi lebih lanjut.</p>
        </div>
        @endif

        <!-- Info Pendaftaran -->
        <div class="mt-14 rounded-2xl p-7 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-5"
             style="background:linear-gradient(135deg, var(--teal-deeper), var(--teal));">
            <div>
                <h3 class="text-lg font-bold mb-1">📝 Cara Mendaftar Ekstrakurikuler</h3>
                <ul class="text-sm text-white/80 space-y-1 mt-2">
                    <li>1. Hubungi pembina ekskul yang diminati</li>
                    <li>2. Isi formulir pendaftaran di TU sekolah</li>
                    <li>3. Ikuti sesi perkenalan / tryout</li>
                    <li>4. Konfirmasi keikutsertaan kepada wali kelas</li>
                </ul>
            </div>
            <a href="{{ route('kontak') }}" class="shrink-0 font-bold px-6 py-2.5 rounded-xl text-sm whitespace-nowrap"
               style="background:var(--gold); color:#fff;">
                Hubungi Sekolah →
            </a>
        </div>
    </div>
</section>

@endsection
