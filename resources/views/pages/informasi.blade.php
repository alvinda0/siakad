@extends('layouts.public')
@section('title', 'Informasi')

@push('styles')
<style>
    .sec-label { color:var(--teal); letter-spacing:.1em; font-size:.75rem; font-weight:700; text-transform:uppercase; }
    .sec-title  { color:var(--navy); font-weight:800; }
    .sec-divider { display:inline-block; width:48px; height:4px; border-radius:2px; background: linear-gradient(90deg, var(--teal), var(--gold)); margin-top:8px; margin-bottom:12px; }
    .page-hero { background: linear-gradient(135deg, var(--teal-deeper) 0%, #0D4A57 100%); }
</style>
@endpush

@section('content')

<!-- Hero -->
<div class="page-hero text-white py-16 px-4 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background:radial-gradient(circle at 80% 50%, white, transparent 60%);"></div>
    <div class="max-w-7xl mx-auto relative z-10">
        <p class="text-xs font-bold uppercase tracking-widest text-white/70 mb-2">Program & Beasiswa</p>
        <h1 class="text-4xl font-black mb-3">Informasi</h1>
        <p class="text-white/80 max-w-lg text-sm leading-relaxed">Informasi beasiswa dan promo program strategis yang tersedia untuk calon siswa dan siswa aktif.</p>
    </div>
    <div class="absolute bottom-0 left-0 w-full overflow-hidden" style="height:72px; line-height:0;">
        <svg viewBox="0 0 1440 72" preserveAspectRatio="none" class="w-full h-full" fill="white">
            <path d="M0,36 C480,72 960,0 1440,36 L1440,72 L0,72 Z"/>
        </svg>
    </div>
</div>

<!-- Content -->
<section class="py-20 px-4 bg-white">
    <div class="max-w-7xl mx-auto space-y-12">

        <!-- Beasiswa -->
        <div>
            <h3 class="text-xl font-extrabold sec-title mb-6 flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-white text-base"
                      style="background:var(--teal);">🎓</span>
                Informasi Beasiswa
            </h3>
            @if($beasiswa->isEmpty())
                <div class="bg-slate-50 rounded-2xl border border-slate-100 p-10 text-center text-sm text-slate-400">
                    <div class="text-4xl mb-3">📭</div>
                    <p>Belum ada informasi beasiswa saat ini. Silakan cek kembali nanti.</p>
                </div>
            @else
                <div class="overflow-x-auto rounded-2xl border border-slate-100 shadow-sm">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="background:var(--teal);" class="text-white">
                                <th class="px-5 py-3.5 font-semibold text-left w-10">No</th>
                                <th class="px-5 py-3.5 font-semibold text-left">Jenis Beasiswa</th>
                                <th class="px-5 py-3.5 font-semibold text-left">Syarat</th>
                                <th class="px-5 py-3.5 font-semibold text-left">Benefit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 bg-white">
                            @foreach($beasiswa as $i => $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-5 py-4 text-slate-400 font-medium">{{ $i + 1 }}</td>
                                <td class="px-5 py-4 font-semibold text-slate-800">{{ $item->jenis }}</td>
                                <td class="px-5 py-4 text-slate-500 whitespace-pre-line leading-relaxed">{{ $item->syarat ?: '—' }}</td>
                                <td class="px-5 py-4 text-slate-500 whitespace-pre-line leading-relaxed">{{ $item->benefit ?: '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Promo Program -->
        <div>
            <h3 class="text-xl font-extrabold sec-title mb-6 flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-white text-base"
                      style="background:var(--gold);">⭐</span>
                Promo Program Strategis
            </h3>
            @if($promo->isEmpty())
                <div class="bg-slate-50 rounded-2xl border border-slate-100 p-10 text-center text-sm text-slate-400">
                    <div class="text-4xl mb-3">📭</div>
                    <p>Belum ada informasi promo program saat ini. Silakan cek kembali nanti.</p>
                </div>
            @else
                <div class="overflow-x-auto rounded-2xl border border-slate-100 shadow-sm">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="background:var(--gold);" class="text-white">
                                <th class="px-5 py-3.5 font-semibold text-left w-10">No</th>
                                <th class="px-5 py-3.5 font-semibold text-left">Jenis Program</th>
                                <th class="px-5 py-3.5 font-semibold text-left">Syarat</th>
                                <th class="px-5 py-3.5 font-semibold text-left">Benefit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 bg-white">
                            @foreach($promo as $i => $item)
                            <tr class="hover:bg-amber-50 transition">
                                <td class="px-5 py-4 text-slate-400 font-medium">{{ $i + 1 }}</td>
                                <td class="px-5 py-4 font-semibold text-slate-800">{{ $item->jenis }}</td>
                                <td class="px-5 py-4 text-slate-500 whitespace-pre-line leading-relaxed">{{ $item->syarat ?: '—' }}</td>
                                <td class="px-5 py-4 text-slate-500 whitespace-pre-line leading-relaxed">{{ $item->benefit ?: '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- CTA -->
        <div class="rounded-2xl p-7 flex flex-col md:flex-row items-center justify-between gap-5"
             style="background:linear-gradient(135deg, var(--teal-deeper), var(--teal));">
            <div class="text-white">
                <h3 class="text-lg font-bold">📝 Tertarik mendaftar beasiswa?</h3>
                <p class="text-sm text-white/75 mt-1">Hubungi TU sekolah untuk informasi lebih lengkap dan persyaratan pendaftaran.</p>
            </div>
            <a href="{{ route('kontak') }}" class="shrink-0 font-bold px-6 py-2.5 rounded-xl text-sm whitespace-nowrap"
               style="background:var(--gold); color:#fff;">
                Hubungi Sekolah →
            </a>
        </div>
    </div>
</section>

@endsection
