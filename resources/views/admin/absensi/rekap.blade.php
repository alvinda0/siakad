@extends('admin.layouts.app')

@section('title', 'Rekap Absensi')
@section('breadcrumb', 'Rekap Absensi')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-800">Rekap Absensi</h1>
            <p class="text-sm text-slate-500">Ringkasan kehadiran per siswa dalam satu kelas.</p>
        </div>
        <a href="{{ route('admin.absensi.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
            ← Kembali ke Absensi
        </a>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-48">
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
            <div class="flex gap-2">
                <button type="submit"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 transition">
                    Tampilkan
                </button>
                <a href="{{ route('admin.absensi.rekap') }}"
                   class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel rekap --}}
    @if($selectedKelas && $rows->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100" style="background: var(--teal-light);">
            <p class="font-extrabold text-sm" style="color: var(--teal);">{{ $selectedKelas->nama }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 text-left">
                        <th class="px-5 py-3 font-semibold text-slate-500 w-10">#</th>
                        <th class="px-5 py-3 font-semibold text-slate-500">Nama Siswa</th>
                        <th class="px-5 py-3 font-semibold text-slate-500 text-center text-emerald-600">Hadir</th>
                        <th class="px-5 py-3 font-semibold text-slate-500 text-center text-blue-600">Sakit</th>
                        <th class="px-5 py-3 font-semibold text-slate-500 text-center text-amber-600">Izin</th>
                        <th class="px-5 py-3 font-semibold text-slate-500 text-center text-red-600">Alpha</th>
                        <th class="px-5 py-3 font-semibold text-slate-500 text-center">Total</th>
                        <th class="px-5 py-3 font-semibold text-slate-500 text-center">% Hadir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($rows as $i => $row)
                    @php $pct = $row['total'] > 0 ? round(($row['hadir'] / $row['total']) * 100) : 0; @endphp
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3 text-slate-400">{{ $i + 1 }}</td>
                        <td class="px-5 py-3 font-semibold text-slate-700">{{ $row['murid']->name }}</td>
                        <td class="px-5 py-3 text-center font-bold text-emerald-600">{{ $row['hadir'] }}</td>
                        <td class="px-5 py-3 text-center font-bold text-blue-600">{{ $row['sakit'] }}</td>
                        <td class="px-5 py-3 text-center font-bold text-amber-600">{{ $row['izin'] }}</td>
                        <td class="px-5 py-3 text-center font-bold text-red-600">{{ $row['alpha'] }}</td>
                        <td class="px-5 py-3 text-center text-slate-600">{{ $row['total'] }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold
                                {{ $pct >= 80 ? 'bg-emerald-50 text-emerald-700' : ($pct >= 60 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">
                                {{ $pct }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @elseif($selectedKelas && $rows->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
        <div class="text-6xl mb-4">📋</div>
        <h3 class="font-bold text-slate-600">Belum ada data absensi untuk kelas ini</h3>
    </div>
    @else
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
        <div class="text-6xl mb-4">📊</div>
        <h3 class="font-bold text-slate-600">Pilih kelas untuk melihat rekap</h3>
    </div>
    @endif

</div>
@endsection
