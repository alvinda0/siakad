@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Welcome banner -->
    <div class="rounded-2xl p-6 text-white relative overflow-hidden"
         style="background: linear-gradient(135deg, #0D4A57 0%, #1B7A8A 60%, #2BA8BF 100%);">
        <div class="relative z-10">
            <h1 class="text-2xl font-extrabold mb-1">Selamat datang, {{ Auth::user()->name }}! 👋</h1>
            <p class="text-white/70 text-sm">Panel Administrasi SIAKAD — SMK Muhammadiyah Sempor</p>
        </div>
        <div class="absolute right-6 top-1/2 -translate-y-1/2 text-8xl opacity-10 pointer-events-none">🏫</div>
    </div>

    <!-- Stat cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $stats = [
            ['Guru',       '48',   '👨‍🏫', '#1B7A8A', 'admin.guru.index'],
            ['Murid',      '1.240','🎓',  '#E6920A', 'admin.murid.index'],
            ['Kandidat',   '87',   '📋',  '#7C3AED', 'admin.kandidat.index'],
            ['Kelas Aktif','24',   '🏫',  '#059669', 'admin.kelas.index'],
        ];
        @endphp
        @foreach($stats as [$label, $val, $icon, $color, $route])
        <a href="{{ route($route) }}"
           class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm hover:shadow-md transition hover:-translate-y-0.5 block">
            <div class="flex items-center justify-between mb-3">
                <span class="text-2xl">{{ $icon }}</span>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full text-white"
                      style="background: {{ $color }};">Lihat</span>
            </div>
            <p class="text-2xl font-extrabold" style="color: {{ $color }};">{{ $val }}</p>
            <p class="text-sm text-slate-500 mt-0.5">{{ $label }}</p>
        </a>
        @endforeach
    </div>

    <!-- Quick links -->
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
        <h2 class="font-bold text-slate-700 mb-4">Akses Cepat</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            @php
            $links = [
                ['Tambah Guru',    '👨‍🏫', 'admin.guru.create'],
                ['Tambah Murid',   '🎓',  'admin.murid.create'],
                ['Data Kandidat',  '📋',  'admin.kandidat.index'],
                ['Kelola Kelas',   '🏫',  'admin.kelas.index'],
                ['Mata Pelajaran', '📚',  'admin.mapel.index'],
                ['Jadwal',         '🗓️',  'admin.jadwal.index'],
                ['Absensi',        '✅',  'admin.absensi.index'],
                ['Nilai',          '📊',  'admin.nilai.index'],
            ];
            @endphp
            @foreach($links as [$label, $icon, $route])
            <a href="{{ route($route) }}"
               class="flex flex-col items-center gap-2 p-4 rounded-xl border border-slate-100 hover:border-teal-300 hover:bg-teal-50 transition text-center group">
                <span class="text-2xl group-hover:scale-110 transition-transform">{{ $icon }}</span>
                <span class="text-xs font-semibold text-slate-600 group-hover:text-teal-700">{{ $label }}</span>
            </a>
            @endforeach
        </div>
    </div>

</div>
@endsection
