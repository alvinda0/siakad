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
        <p class="text-white/80 max-w-lg text-sm leading-relaxed">Kembangkan bakat dan minatmu di luar kelas bersama teman-teman. Pilih ekstrakurikuler yang sesuai passionmu.</p>
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
            <span class="sec-label">20+ Pilihan</span>
            <h2 class="text-3xl font-extrabold sec-title mt-1">Temukan Passionmu</h2>
            <div class="sec-divider mx-auto"></div>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">Setiap siswa dapat mengikuti minimal 1 ekstrakurikuler wajib dan 1 pilihan sesuai minat.</p>
        </div>

        <!-- Wajib -->
        <h3 class="text-lg font-extrabold sec-title mb-5 flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-white text-xs font-black" style="background:var(--teal);">W</span>
            Ekstrakurikuler Wajib
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-12">
            @php
            $wajib = [
                ['🌿','Pramuka',120,'linear-gradient(135deg,#1a5c2e,#26834a)'],
                ['🏹','Paskibraka',60,'linear-gradient(135deg,#B91C1C,#7f1d1d)'],
            ];
            @endphp
            @foreach($wajib as [$emoji,$nama,$anggota,$bg])
            <div class="grad-card p-6 text-white" style="background:{{ $bg }}; min-height:140px;">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-3xl">{{ $emoji }}</span>
                    <span class="text-xs font-black px-2 py-0.5 rounded-full bg-white/20">WAJIB</span>
                </div>
                <h3 class="font-bold text-sm mb-1">{{ $nama }}</h3>
                <p class="text-xs opacity-75">{{ $anggota }} anggota</p>
            </div>
            @endforeach
        </div>

        <!-- Pilihan -->
        <h3 class="text-lg font-extrabold sec-title mb-5 flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-white text-xs font-black" style="background:var(--gold);">P</span>
            Ekstrakurikuler Pilihan
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @php
            $ekskul = [
                ['⚽','Futsal',48,'linear-gradient(135deg,#1B7A8A,#0D4A57)'],
                ['🏐','Bola Voli',36,'linear-gradient(135deg,#E6920A,#c97d08)'],
                ['🥋','Pencak Silat',30,'linear-gradient(135deg,#1B7A8A,#145F6E)'],
                ['🎭','Teater & Drama',25,'linear-gradient(135deg,#0D4A57,#1B7A8A)'],
                ['🎵','Paduan Suara',40,'linear-gradient(135deg,#145F6E,#2399AD)'],
                ['🤖','Robotika',22,'linear-gradient(135deg,#112D3E,#1B7A8A)'],
                ['📸','Fotografi',28,'linear-gradient(135deg,#E6920A,#1B7A8A)'],
                ['🎨','Seni Lukis',20,'linear-gradient(135deg,#1B7A8A,#0D4A57)'],
                ['📰','Jurnalistik',18,'linear-gradient(135deg,#112D3E,#145F6E)'],
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
