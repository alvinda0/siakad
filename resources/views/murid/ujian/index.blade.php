@extends('murid.layouts.app')

@section('title', 'Ujian Online')
@section('breadcrumb', 'Ujian Online')

@push('styles')
<style>
    .badge-jenis {
        display:inline-flex;align-items:center;
        padding:.2rem .65rem;border-radius:9999px;
        font-size:.7rem;font-weight:700;letter-spacing:.02em;
    }
    .badge-UTS     { background:#dbeafe;color:#1d4ed8; }
    .badge-UAS     { background:#ede9fe;color:#7c3aed; }
    .badge-UKK     { background:#ffe4e6;color:#be123c; }
    .badge-Sumatif { background:#fef3c7;color:#b45309; }
    .badge-Lainnya { background:#f1f5f9;color:#475569; }

    .ujian-card {
        background:#fff;border-radius:16px;border:1px solid #e2e8f0;
        transition:box-shadow .15s,transform .15s;
        overflow:hidden;
    }
    .ujian-card:hover { box-shadow:0 4px 20px rgba(0,0,0,.08);transform:translateY(-1px); }

    .status-badge {
        display:inline-flex;align-items:center;gap:.3rem;
        padding:.25rem .75rem;border-radius:9999px;
        font-size:.7rem;font-weight:700;border:1px solid transparent;
    }
    .status-belum   { background:#f8fafc;color:#64748b;border-color:#e2e8f0; }
    .status-sedang  { background:#eff6ff;color:#2563eb;border-color:#bfdbfe; }
    .status-selesai { background:#f0fdf4;color:#16a34a;border-color:#bbf7d0; }
    .status-locked  { background:#fef9c3;color:#a16207;border-color:#fde68a; }
</style>
@endpush

@section('content')
<div class="space-y-5">

{{-- ── Header ── --}}
<div>
    <h1 class="text-xl font-extrabold text-slate-800">Ujian Online</h1>
    <p class="text-sm text-slate-500 mt-0.5">
        Daftar ujian yang tersedia untuk kelas Anda.
    </p>
</div>

{{-- ── Flash ── --}}
@if(session('success'))
<div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 rounded-xl">
    <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
@endif

@if(! $profil || ! $profil->kelas_id)
{{-- Belum terdaftar di kelas --}}
<div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm px-5 py-4 rounded-xl flex items-start gap-3">
    <svg class="w-5 h-5 mt-0.5 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div>
        <p class="font-semibold">Belum terdaftar di kelas</p>
        <p class="text-xs mt-0.5 text-amber-700">Akun Anda belum ditempatkan ke kelas. Hubungi admin sekolah.</p>
    </div>
</div>

@elseif($ujianList->isEmpty())
{{-- Empty state --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-16 text-center">
    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-4xl mx-auto mb-4">📝</div>
    <h3 class="font-bold text-slate-600 mb-1">Belum ada ujian</h3>
    <p class="text-sm text-slate-400">Tidak ada jadwal ujian aktif untuk kelas Anda saat ini.</p>
</div>

@else

{{-- ── Filter ── --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
    <form method="GET" id="form-filter" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Jenis Ujian</label>
            <select name="jenis" onchange="document.getElementById('form-filter').submit()"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                <option value="">Semua Jenis</option>
                @foreach(App\Models\JadwalUjian::$jenis as $val => $label)
                    <option value="{{ $val }}" {{ request('jenis') === $val ? 'selected' : '' }}>{{ $val }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Bulan</label>
            <select name="bulan" onchange="document.getElementById('form-filter').submit()"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                <option value="">Semua Bulan</option>
                @foreach(['1'=>'Januari','2'=>'Februari','3'=>'Maret','4'=>'April','5'=>'Mei','6'=>'Juni','7'=>'Juli','8'=>'Agustus','9'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $num => $bln)
                    <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>{{ $bln }}</option>
                @endforeach
            </select>
        </div>
        @if(request()->hasAny(['jenis','bulan']))
        <a href="{{ route('murid.ujian.index') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Reset
        </a>
        @endif
    </form>
</div>

{{-- ── Grid ujian ── --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @foreach($ujianList as $ujian)
    @php
        $sesi      = $sesiMap[$ujian->id] ?? null;
        $jumlahSoal = $soalCount[$ujian->id] ?? 0;
        $adaEssay  = ($essayCount[$ujian->id] ?? 0) > 0;
        $isLewat   = $ujian->tanggal->isPast() && !$ujian->tanggal->isToday();
        $isToday   = $ujian->tanggal->isToday();
        $sudahAda  = $jumlahSoal > 0;
    @endphp

    <div class="ujian-card">
        {{-- Color stripe --}}
        <div class="h-1.5 w-full"
             style="background: {{ ['UTS'=>'#1d4ed8','UAS'=>'#7c3aed','UKK'=>'#be123c','Sumatif'=>'#b45309','Lainnya'=>'#94a3b8'][$ujian->jenis] ?? '#94a3b8' }}">
        </div>

        <div class="p-5">
            {{-- Badges row --}}
            <div class="flex flex-wrap items-center gap-2 mb-3">
                <span class="badge-jenis badge-{{ $ujian->jenis }}">{{ $ujian->jenis }}</span>
                @if($sesi)
                    <span class="status-badge status-{{ $sesi->status }}">
                        @if($sesi->isBelum()) ⏳ Belum Dimulai
                        @elseif($sesi->isSedang()) 🔵 Sedang Berlangsung
                        @else ✅ Selesai
                        @endif
                    </span>
                @elseif(!$sudahAda)
                    <span class="status-badge status-locked">🔒 Soal Belum Tersedia</span>
                @else
                    <span class="status-badge status-belum">⏳ Belum Dimulai</span>
                @endif
            </div>

            {{-- Nama & mapel --}}
            <h3 class="font-bold text-slate-800 text-base leading-snug mb-1">{{ $ujian->mataPelajaran->nama }}</h3>
            <p class="text-sm text-slate-500 mb-3">{{ $ujian->nama }}</p>

            {{-- Info --}}
            <div class="space-y-1.5 text-xs text-slate-500 mb-4">
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-medium text-slate-700">{{ $ujian->tanggalLabel() }}</span>
                    @if($isToday)
                    <span class="text-[.65rem] font-bold px-1.5 py-0.5 rounded-full bg-teal-500 text-white">Hari Ini</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $ujian->jamLabel() }}
                </div>
                @if($ujian->ruangan)
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                    {{ $ujian->ruangan }}
                </div>
                @endif
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    {{ $jumlahSoal > 0 ? $jumlahSoal.' soal' : 'Belum ada soal' }}
                </div>
                @if($sesi && $sesi->isSelesai() && $sesi->nilai_total !== null && !$adaEssay)
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    <span class="font-semibold text-emerald-700">Nilai: {{ number_format($sesi->nilai_total, 0) }}</span>
                </div>
                @elseif($sesi && $sesi->isSelesai() && $adaEssay)
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold text-amber-600">Menunggu koreksi guru</span>
                </div>
                @endif
            </div>

            {{-- CTA Button --}}
            @if(!$sudahAda)
                <div class="text-xs text-center text-slate-400 py-2 rounded-xl bg-slate-50 border border-dashed border-slate-200">
                    Menunggu soal dari guru...
                </div>
            @elseif($sesi && $sesi->isSelesai())
                <a href="{{ route('murid.ujian.hasil', $ujian) }}"
                   class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition border border-emerald-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Lihat Hasil
                </a>
            @elseif($sesi && $sesi->isSedang())
                <a href="{{ route('murid.ujian.kerjakan', $ujian) }}"
                   class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
                   style="background: var(--teal);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Lanjut Kerjakan
                </a>
            @else
                <a href="{{ route('murid.ujian.show', $ujian) }}"
                   class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
                   style="background: var(--teal);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                    Mulai Ujian
                </a>
            @endif
        </div>
    </div>
    @endforeach
</div>

@endif

</div>
@endsection
