@extends('layouts.public')
@section('title', 'Fasilitas')

@push('styles')
<style>
    .sec-label { color:var(--teal); letter-spacing:.1em; font-size:.75rem; font-weight:700; text-transform:uppercase; }
    .sec-title  { color:var(--navy); font-weight:800; }
    .sec-divider { display:inline-block; width:48px; height:4px; border-radius:2px; background: linear-gradient(90deg, var(--teal), var(--gold)); margin-top:8px; margin-bottom:12px; }
    .card { background:#fff; border-radius:1.25rem; border: 1px solid rgba(27,122,138,0.08); transition: transform .25s, box-shadow .25s; }
    .card:hover { transform:translateY(-6px); box-shadow:0 20px 44px rgba(27,122,138,0.13); }
    .page-hero { background: linear-gradient(135deg, var(--teal-deeper) 0%, #0D4A57 100%); }
</style>
@endpush

@section('content')

<!-- Hero -->
<div class="page-hero text-white py-16 px-4 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background:radial-gradient(circle at 80% 50%, white, transparent 60%);"></div>
    <div class="max-w-7xl mx-auto relative z-10">
        <p class="text-xs font-bold uppercase tracking-widest text-white/70 mb-2">Sarana & Prasarana</p>
        <h1 class="text-4xl font-black mb-3">Fasilitas Lengkap</h1>
        <p class="text-white/80 max-w-lg text-sm leading-relaxed">Fasilitas modern untuk mendukung proses belajar dan pengembangan potensi siswa secara optimal.</p>
    </div>
    <div class="absolute bottom-0 left-0 w-full overflow-hidden" style="height:72px; line-height:0;">
        <svg viewBox="0 0 1440 72" preserveAspectRatio="none" class="w-full h-full" fill="white">
            <path d="M0,36 C480,72 960,0 1440,36 L1440,72 L0,72 Z"/>
        </svg>
    </div>
</div>

<!-- Content -->
<section class="py-20 px-4 bg-white">
    <div class="max-w-7xl mx-auto">

        <div class="text-center mb-12">
            <span class="sec-label">Standar Nasional</span>
            <h2 class="text-3xl font-extrabold sec-title mt-1">Fasilitas Unggulan</h2>
            <div class="sec-divider mx-auto"></div>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">Kami terus berinvestasi dalam fasilitas terbaik agar setiap siswa mendapatkan pengalaman belajar yang optimal.</p>
        </div>

        @if($fasilitas->isEmpty())
            <div class="text-center py-16 text-gray-400">
                <div class="text-6xl mb-4">🏫</div>
                <p class="font-semibold">Data fasilitas belum tersedia.</p>
            </div>
        @else
            <div class="grid md:grid-cols-3 gap-6 mb-16">
                @foreach($fasilitas as $item)
                <div class="card p-6 flex gap-5">
                    {{-- Image or placeholder --}}
                    <div class="shrink-0 mt-1">
                        @if($item->gambarUrl())
                            <img src="{{ $item->gambarUrl() }}" alt="{{ $item->nama }}"
                                 class="w-14 h-14 object-cover rounded-xl border border-slate-100 shadow-sm">
                        @else
                            <div class="w-14 h-14 flex items-center justify-center rounded-xl bg-teal-50">
                                <svg class="w-7 h-7 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold sec-title mb-1.5">{{ $item->nama }}</h3>
                        @if($item->deskripsi)
                            <p class="text-sm text-gray-500 leading-relaxed mb-3">{{ $item->deskripsi }}</p>
                        @endif
                        @if(count($item->fiturList()) > 0)
                        <ul class="space-y-1">
                            @foreach($item->fiturList() as $f)
                            <li class="flex items-center gap-2 text-xs text-gray-500">
                                <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background:var(--teal);"></span>
                                {{ $f }}
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @endif

        <!-- Stats -->
        <div class="rounded-2xl p-8 grid sm:grid-cols-2 md:grid-cols-4 gap-6 text-center"
             style="background:linear-gradient(135deg, var(--teal-deeper), var(--teal));">
            <div class="text-white">
                <span class="text-3xl">🏫</span>
                <p class="text-3xl font-black mt-1" style="color:#F9C940;">{{ $stats['total'] }}</p>
                <p class="text-sm text-white/75 mt-0.5">Total Fasilitas</p>
            </div>
            <div class="text-white">
                <span class="text-3xl">📚</span>
                <p class="text-3xl font-black mt-1" style="color:#F9C940;">{{ $stats['akademik'] }}</p>
                <p class="text-sm text-white/75 mt-0.5">Fasilitas Akademik</p>
            </div>
            <div class="text-white">
                <span class="text-3xl">🏅</span>
                <p class="text-3xl font-black mt-1" style="color:#F9C940;">{{ $stats['olahraga'] }}</p>
                <p class="text-sm text-white/75 mt-0.5">Fasilitas Olahraga</p>
            </div>
            <div class="text-white">
                <span class="text-3xl">🏥</span>
                <p class="text-3xl font-black mt-1" style="color:#F9C940;">{{ $stats['kesehatan'] }}</p>
                <p class="text-sm text-white/75 mt-0.5">Fasilitas Kesehatan</p>
            </div>
        </div>
    </div>
</section>

@endsection
