@extends('murid.layouts.app')

@section('title', 'Hasil Ujian')
@section('breadcrumb', 'Hasil Ujian')

@push('styles')
<style>
    .pilihan-label {
        display: flex; align-items: flex-start; gap: .75rem;
        padding: .75rem 1rem; border-radius: 10px;
        border: 1.5px solid #e2e8f0; background: #fff;
    }
    .pilihan-benar { border-color: #16a34a; background: #f0fdf4; }
    .pilihan-salah { border-color: #dc2626; background: #fef2f2; }
    .pilihan-kunci { border-color: #16a34a; background: #f0fdf4; }
    .pilihan-huruf {
        width: 28px; height: 28px; border-radius: 7px;
        display: flex; align-items: center; justify-content: center;
        font-size: .75rem; font-weight: 800;
        background: #f1f5f9; color: #475569; flex-shrink: 0;
    }
    .pilihan-benar .pilihan-huruf { background: #16a34a; color: #fff; }
    .pilihan-salah .pilihan-huruf { background: #dc2626; color: #fff; }
    .pilihan-kunci .pilihan-huruf { background: #16a34a; color: #fff; }

    .nilai-circle {
        width: 120px; height: 120px; border-radius: 50%;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        border: 6px solid var(--teal);
    }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 rounded-xl">
        <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Back --}}
    <a href="{{ route('murid.ujian.index') }}"
       class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-teal-600 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Daftar Ujian
    </a>

    {{-- ── Header hasil ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="h-2 w-full" style="background: var(--teal);"></div>
        <div class="p-6">
            <div class="flex flex-wrap items-center justify-between gap-6">
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">Rekap Hasil</p>
                    <h1 class="text-xl font-extrabold text-slate-800">{{ $jadwalUjian->mataPelajaran->nama }}</h1>
                    <p class="text-sm text-slate-500 mt-0.5">{{ $jadwalUjian->nama }} · {{ $jadwalUjian->jenis }}</p>
                    <div class="mt-3 flex flex-wrap gap-3 text-xs text-slate-500">
                        <span>📅 {{ $jadwalUjian->tanggalLabel() }}</span>
                        <span>⏰ {{ $jadwalUjian->jamLabel() }}</span>
                        @if($sesi->durasiMenit())
                        <span>⏱ Dikerjakan {{ $sesi->durasiMenit() }} menit</span>
                        @endif
                    </div>
                </div>

                {{-- Nilai circle: tampilkan HANYA jika semua soal pilihan ganda --}}
                @php $adaEssay = $soalList->where('tipe', 'essay')->count() > 0; @endphp
                @if(!$adaEssay)
                <div class="text-center">
                    <div class="nilai-circle mx-auto mb-2">
                        <span class="text-3xl font-extrabold text-teal-700">
                            {{ $sesi->nilai_total !== null ? number_format($sesi->nilai_total, 0) : '—' }}
                        </span>
                        <span class="text-xs text-slate-400 font-semibold">/ 100</span>
                    </div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Nilai</p>
                    @php
                        $predikat = match(true) {
                            ($sesi->nilai_total >= 90) => ['A', 'text-emerald-700', 'Sangat Baik'],
                            ($sesi->nilai_total >= 75) => ['B', 'text-teal-700', 'Baik'],
                            ($sesi->nilai_total >= 60) => ['C', 'text-amber-700', 'Cukup'],
                            default => ['D', 'text-red-700', 'Kurang'],
                        };
                    @endphp
                    @if($sesi->nilai_total !== null)
                    <span class="text-sm font-extrabold {{ $predikat[1] }}">{{ $predikat[0] }} — {{ $predikat[2] }}</span>
                    @endif
                </div>
                @endif
            </div>

            {{-- Catatan essay --}}
            @if($soalList->where('tipe', 'essay')->count())
            <div class="mt-4 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm text-amber-800">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 shrink-0 mt-0.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-semibold">Ujian telah diselesaikan</p>
                        <p class="text-xs mt-0.5 text-amber-700">Soal essay akan diperiksa oleh guru. Nilai akan diumumkan setelah guru selesai mengoreksi.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ── A. Pilihan Ganda ── --}}
    @php $soalPG = $soalList->where('tipe', 'pilihan_ganda'); @endphp
    @if($soalPG->count())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-blue-50 to-slate-50">
            @php
                $dijawabPG = $soalPG->filter(fn($s) => isset($jawabanMap[$s->id]) && ($jawabanMap[$s->id]->jawaban_pg ?? '') !== '')->count();
            @endphp
            <div class="flex items-center justify-between flex-wrap gap-2">
                <h2 class="font-extrabold text-slate-700 text-sm">A. Pilihan Ganda</h2>
                <div class="text-xs font-bold text-blue-700">
                    Dijawab: {{ $dijawabPG }}/{{ $soalPG->count() }}
                </div>
            </div>
        </div>

        <div class="p-5 space-y-8">
            @foreach($soalPG->sortBy('nomor') as $soal)
            @php
                $jwb     = $jawabanMap[$soal->id] ?? null;
                $dijawab = strtoupper($jwb->jawaban_pg ?? '');
                $kunci   = strtoupper($soal->kunci_jawaban ?? '');
                $isBenar = $dijawab !== '' && $dijawab === $kunci;
                $isSalah = $dijawab !== '' && $dijawab !== $kunci;
            @endphp
            <div>
                <div class="flex gap-3 mb-3">
                    <span class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-extrabold text-white bg-blue-500">
                        {{ $soal->nomor }}
                    </span>
                    <div class="flex-1">
                        <p class="text-sm text-slate-800 font-medium leading-relaxed">
                            {!! nl2br(e($soal->pertanyaan)) !!}
                        </p>
                        @if(!$jwb || !$dijawab)
                        <span class="inline-flex items-center mt-1 text-xs font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
                            Tidak dijawab
                        </span>
                        @endif
                    </div>
                </div>

                <div class="space-y-1.5 ml-11">
                    @foreach($soal->pilihanArray() as $huruf => $teks)
                    @php
                        $isDijawab = strtoupper($huruf) === $dijawab;
                        $extraClass = $isDijawab ? ($isBenar ? 'pilihan-benar' : 'pilihan-salah') : '';
                    @endphp
                    <div class="pilihan-label {{ $extraClass }}">
                        <span class="pilihan-huruf">{{ $huruf }}</span>
                        <span class="text-sm text-slate-700 leading-relaxed flex-1">{{ $teks }}</span>
                        @if($isDijawab)
                        <span class="text-xs font-bold shrink-0 {{ $isBenar ? 'text-emerald-700' : 'text-red-600' }}">
                            Jawaban Anda
                        </span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── B. Essay ── --}}
    @php $soalEssay = $soalList->where('tipe', 'essay'); @endphp
    @if($soalEssay->count())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-purple-50 to-slate-50">
            @php
                $dijawabEssay = $soalEssay->filter(fn($s) => isset($jawabanMap[$s->id]) && ($jawabanMap[$s->id]->jawaban_essay ?? '') !== '')->count();
            @endphp
            <div class="flex items-center justify-between flex-wrap gap-2">
                <h2 class="font-extrabold text-slate-700 text-sm">B. Essay</h2>
                <div class="text-xs font-bold text-purple-700">
                    Dijawab: {{ $dijawabEssay }}/{{ $soalEssay->count() }}
                </div>
            </div>
        </div>

        <div class="p-5 space-y-8">
            @foreach($soalEssay->sortBy('nomor') as $soal)
            @php
                $jwb        = $jawabanMap[$soal->id] ?? null;
                $nilaiEssay = $jwb->nilai_essay ?? null;
            @endphp
            <div>
                <div class="flex gap-3 mb-3">
                    <span class="flex-shrink-0 w-8 h-8 rounded-full bg-purple-500 flex items-center justify-center text-sm font-extrabold text-white">
                        {{ $soal->nomor }}
                    </span>
                    <div class="flex-1">
                        <p class="text-sm text-slate-800 font-medium leading-relaxed">
                            {!! nl2br(e($soal->pertanyaan)) !!}
                        </p>
                        <span class="text-xs text-slate-400">{{ $soal->poin }} poin</span>
                    </div>
                </div>
                <div class="ml-11 space-y-2">
                    {{-- Jawaban murid --}}
                    @if($jwb && $jwb->jawaban_essay)
                    <div class="bg-slate-50 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 whitespace-pre-wrap leading-relaxed">
                        {{ $jwb->jawaban_essay }}
                    </div>
                    @else
                    <div class="bg-slate-50 rounded-xl border border-dashed border-slate-300 px-4 py-3 text-sm text-slate-400 italic">
                        Tidak dijawab
                    </div>
                    @endif

                    {{-- Skor per soal essay tidak ditampilkan ke murid, dinilai guru --}}
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
