@extends('murid.layouts.app')

@section('title', 'Nilai Saya')
@section('breadcrumb', 'Nilai')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-800">Nilai Saya</h1>
            <p class="text-sm text-slate-500">Rekap nilai per mata pelajaran semester ini.</p>
        </div>
    </div>

    {{-- Filter semester & tahun --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Semester</label>
                <select name="semester"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                    @foreach(App\Models\Nilai::$semester as $val => $label)
                        <option value="{{ $val }}" {{ $semester == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tahun Ajaran</label>
                <input type="number" name="tahun_ajaran" value="{{ $tahunAjaran }}" min="2020" max="2099"
                       class="w-32 rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>
            <button type="submit"
                    class="px-5 py-2 rounded-xl text-sm font-semibold text-white hover:brightness-110 transition"
                    style="background:var(--teal)">
                Tampilkan
            </button>
        </form>
    </div>

    @if(!$kelasId)
    {{-- Belum punya kelas --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
        <div class="text-5xl mb-4">🎓</div>
        <h3 class="font-bold text-slate-600">Kamu belum terdaftar di kelas manapun</h3>
        <p class="text-sm text-slate-400 mt-1">Hubungi admin untuk assign kelas.</p>
    </div>

    @elseif($nilaiList->isEmpty())
    {{-- Belum ada nilai --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
        <div class="text-5xl mb-4">📝</div>
        <h3 class="font-bold text-slate-600">Belum ada nilai untuk semester ini</h3>
        <p class="text-sm text-slate-400 mt-1">
            Nilai akan muncul setelah guru menginput atau melakukan sync dari ujian online.
        </p>
    </div>

    @else
    {{-- Kartu rata-rata --}}
    @if($rata !== null)
    @php
        $rataPredikat = \App\Models\Nilai::predikatDari($rata);
        $rataColor = match($rataPredikat) {
            'A' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-700', 'badge' => 'bg-emerald-100 text-emerald-700'],
            'B' => ['bg' => 'bg-teal-50',    'border' => 'border-teal-200',    'text' => 'text-teal-700',    'badge' => 'bg-teal-100 text-teal-700'],
            'C' => ['bg' => 'bg-amber-50',   'border' => 'border-amber-200',   'text' => 'text-amber-700',   'badge' => 'bg-amber-100 text-amber-700'],
            default => ['bg' => 'bg-red-50', 'border' => 'border-red-200',     'text' => 'text-red-700',     'badge' => 'bg-red-100 text-red-700'],
        };
    @endphp
    <div class="{{ $rataColor['bg'] }} {{ $rataColor['border'] }} border rounded-2xl p-5 flex items-center gap-5">
        <div class="w-16 h-16 rounded-2xl bg-white shadow-sm flex items-center justify-center shrink-0">
            <span class="text-3xl font-extrabold {{ $rataColor['text'] }}">{{ $rata }}</span>
        </div>
        <div>
            <div class="text-sm font-semibold {{ $rataColor['text'] }}">Rata-rata Nilai Akhir</div>
            <div class="text-xs text-slate-500 mt-0.5">
                {{ App\Models\Nilai::$semester[$semester] }} / TA {{ $tahunAjaran }}/{{ $tahunAjaran + 1 }}
                &nbsp;·&nbsp; {{ $nilaiList->whereNotNull('nilai_akhir')->count() }} mata pelajaran
            </div>
            <span class="inline-flex items-center mt-2 px-2.5 py-0.5 rounded-full text-xs font-bold {{ $rataColor['badge'] }}">
                Predikat {{ $rataPredikat }}
            </span>
        </div>
    </div>
    @endif

    {{-- Tabel nilai --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between"
             style="background:var(--teal-light)">
            <p class="font-bold text-sm" style="color:var(--teal)">
                {{ App\Models\Nilai::$semester[$semester] }} / TA {{ $tahunAjaran }}/{{ $tahunAjaran + 1 }}
            </p>
            <span class="text-xs text-slate-500">{{ $nilaiList->count() }} mata pelajaran</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 text-left bg-slate-50">
                        <th class="px-5 py-3 font-semibold text-slate-500 w-10">#</th>
                        <th class="px-5 py-3 font-semibold text-slate-500">Mata Pelajaran</th>
                        <th class="px-4 py-3 font-semibold text-slate-500 text-center">
                            Tugas
                            <div class="font-normal text-slate-400 text-xs">(20%)</div>
                        </th>
                        <th class="px-4 py-3 font-semibold text-slate-500 text-center">
                            UTS
                            <div class="font-normal text-slate-400 text-xs">(30%)</div>
                        </th>
                        <th class="px-4 py-3 font-semibold text-slate-500 text-center">
                            UAS
                            <div class="font-normal text-slate-400 text-xs">(50%)</div>
                        </th>
                        <th class="px-4 py-3 font-semibold text-slate-500 text-center">Nilai Akhir</th>
                        <th class="px-4 py-3 font-semibold text-slate-500 text-center">Predikat</th>
                        <th class="px-5 py-3 font-semibold text-slate-500">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($nilaiList as $i => $n)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3 text-slate-400 text-xs">{{ $i + 1 }}</td>
                        <td class="px-5 py-3">
                            <div class="font-semibold text-slate-800">{{ $n->mataPelajaran?->nama ?? '—' }}</div>
                        </td>
                        {{-- Tugas --}}
                        <td class="px-4 py-3 text-center">
                            @if($n->nilai_tugas !== null)
                                <span class="font-semibold text-slate-700">{{ number_format($n->nilai_tugas, 0) }}</span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        {{-- UTS --}}
                        <td class="px-4 py-3 text-center">
                            @if($n->nilai_uts !== null)
                                <span class="font-semibold text-slate-700">{{ number_format($n->nilai_uts, 0) }}</span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        {{-- UAS --}}
                        <td class="px-4 py-3 text-center">
                            @if($n->nilai_uas !== null)
                                <span class="font-semibold text-slate-700">{{ number_format($n->nilai_uas, 0) }}</span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        {{-- Nilai Akhir --}}
                        <td class="px-4 py-3 text-center">
                            @if($n->nilai_akhir !== null)
                                @php
                                    $nc = match(\App\Models\Nilai::predikatDari((float)$n->nilai_akhir)) {
                                        'A' => 'text-emerald-700',
                                        'B' => 'text-teal-700',
                                        'C' => 'text-amber-600',
                                        default => 'text-red-600',
                                    };
                                @endphp
                                <span class="font-extrabold text-base {{ $nc }}">
                                    {{ number_format($n->nilai_akhir, 0) }}
                                </span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        {{-- Predikat --}}
                        <td class="px-4 py-3 text-center">
                            @if($n->predikat)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold {{ $n->predikatColorClass() }}">
                                    {{ $n->predikat }}
                                </span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        {{-- Catatan --}}
                        <td class="px-5 py-3 text-sm text-slate-500 max-w-xs">
                            {{ $n->catatan ?? '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Info bobot --}}
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50">
            <p class="text-xs text-slate-400">
                💡 Nilai Akhir = Tugas (20%) + UTS (30%) + UAS (50%)
                &nbsp;·&nbsp; Predikat: A ≥ 90 &nbsp;·&nbsp; B ≥ 75 &nbsp;·&nbsp; C ≥ 60 &nbsp;·&nbsp; D &lt; 60
            </p>
        </div>
    </div>
    @endif

</div>
@endsection
