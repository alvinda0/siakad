@extends('admin.layouts.app')

@section('title', 'Rekap Nilai')
@section('breadcrumb', 'Rekap Nilai')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-800">Rekap Nilai</h1>
            <p class="text-sm text-slate-500">Ringkasan nilai seluruh mata pelajaran per siswa dalam satu kelas.</p>
        </div>
        <a href="{{ route('admin.nilai.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
            ← Kembali ke Input Nilai
        </a>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-40">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Kelas</label>
                <select name="kelas_id"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">— Pilih Kelas —</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Semester</label>
                <select name="semester"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
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
            <div class="flex gap-2">
                <button type="submit"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 transition">
                    Tampilkan
                </button>
                <a href="{{ route('admin.nilai.rekap') }}"
                   class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel rekap --}}
    @if($selectedKelas && $muridRows->isNotEmpty() && $mapelHeaders->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100" style="background: var(--teal-light);">
            <p class="font-extrabold text-sm" style="color: var(--teal);">
                {{ $selectedKelas->nama }}
                — {{ App\Models\Nilai::$semester[$semester] }}
                — TA {{ $tahunAjaran }}/{{ $tahunAjaran + 1 }}
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 text-left">
                        <th class="px-5 py-3 font-semibold text-slate-500 w-10">#</th>
                        <th class="px-5 py-3 font-semibold text-slate-500 min-w-40">Nama Siswa</th>
                        @foreach($mapelHeaders as $mapel)
                        <th class="px-3 py-3 font-semibold text-slate-500 text-center text-xs whitespace-nowrap max-w-24">
                            {{ Str::limit($mapel->nama, 16) }}
                        </th>
                        @endforeach
                        <th class="px-5 py-3 font-semibold text-slate-500 text-center">Rata-rata</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($muridRows as $i => $row)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3 text-slate-400">{{ $i + 1 }}</td>
                        <td class="px-5 py-3 font-semibold text-slate-700">{{ $row['murid']->name }}</td>
                        @foreach($mapelHeaders as $mapel)
                        @php $n = $row['nilaiMap'][$mapel->id] ?? null; @endphp
                        <td class="px-3 py-3 text-center">
                            @if($n && $n->nilai_akhir !== null)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold {{ $n->predikatColorClass() }}">
                                    {{ number_format($n->nilai_akhir, 1) }}
                                </span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        @endforeach
                        <td class="px-5 py-3 text-center">
                            @if($row['rata'] !== null)
                                @php
                                    $rataPredikat = \App\Models\Nilai::predikatDari($row['rata']);
                                    $rataColor = match($rataPredikat) {
                                        'A' => 'bg-emerald-50 text-emerald-700',
                                        'B' => 'bg-teal-50 text-teal-700',
                                        'C' => 'bg-amber-50 text-amber-700',
                                        default => 'bg-red-50 text-red-700',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold {{ $rataColor }}">
                                    {{ number_format($row['rata'], 2) }}
                                </span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @elseif($selectedKelas && $muridRows->isNotEmpty() && $mapelHeaders->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
        <div class="text-6xl mb-4">📝</div>
        <h3 class="font-bold text-slate-600">Belum ada nilai yang diinput untuk kelas ini</h3>
        <p class="text-sm text-slate-400 mt-1">Silakan input nilai terlebih dahulu.</p>
    </div>
    @elseif($selectedKelas && $muridRows->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
        <div class="text-6xl mb-4">🎓</div>
        <h3 class="font-bold text-slate-600">Tidak ada murid di kelas ini</h3>
    </div>
    @else
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
        <div class="text-6xl mb-4">📊</div>
        <h3 class="font-bold text-slate-600">Pilih kelas untuk melihat rekap nilai</h3>
    </div>
    @endif

</div>
@endsection
