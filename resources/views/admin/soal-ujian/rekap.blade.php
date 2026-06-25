@extends('admin.layouts.app')

@section('title', 'Rekap Pengerjaan Ujian')
@section('breadcrumb', 'Rekap Ujian')

@section('content')
<div class="space-y-5">

{{-- Header --}}
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="{{ route('admin.soal-ujian.index', $jadwalUjian) }}"
           class="text-xs text-slate-400 hover:text-teal-600 transition">← Soal Ujian</a>
        <h1 class="text-xl font-extrabold text-slate-800 mt-0.5">Rekap: {{ $jadwalUjian->mataPelajaran->nama }}</h1>
        <p class="text-sm text-slate-500">{{ $jadwalUjian->nama }} &middot; {{ $jadwalUjian->kelas->nama }}</p>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    @php $items = [
        ['📋','Total Murid',$stats['total_murid'],'bg-slate-50'],
        ['✅','Selesai',$stats['selesai'],'bg-emerald-50'],
        ['🔵','Sedang',$stats['sedang'],'bg-blue-50'],
        ['⏳','Belum',$stats['belum'],'bg-amber-50'],
    ]; @endphp
    @foreach($items as [$ico,$lbl,$val,$bg])
    <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center gap-3">
        <div class="w-11 h-11 {{ $bg }} rounded-xl flex items-center justify-center text-2xl">{{ $ico }}</div>
        <div>
            <div class="text-2xl font-extrabold text-slate-800">{{ $val }}</div>
            <div class="text-xs text-slate-500">{{ $lbl }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Rata-rata --}}
@if($stats['rata2'] !== null)
<div class="bg-teal-50 border border-teal-200 rounded-xl px-5 py-3 text-sm text-teal-800">
    📊 Rata-rata nilai: <strong>{{ number_format($stats['rata2'], 1) }}</strong>
    &nbsp;·&nbsp; Tertinggi: <strong>{{ number_format($stats['tertinggi'], 0) }}</strong>
    &nbsp;·&nbsp; Terendah: <strong>{{ number_format($stats['terendah'], 0) }}</strong>
</div>
@endif

{{-- Tabel rekap --}}
@if($sesiList->isEmpty())
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-16 text-center">
    <div class="text-4xl mb-3">📝</div>
    <p class="text-sm text-slate-400">Belum ada murid yang mengerjakan ujian ini.</p>
</div>
@else
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50 text-xs font-bold text-slate-500 uppercase tracking-wide">
                    <th class="px-5 py-3 text-left">No</th>
                    <th class="px-5 py-3 text-left">Murid</th>
                    <th class="px-5 py-3 text-center">Status</th>
                    <th class="px-5 py-3 text-center">Nilai PG</th>
                    <th class="px-5 py-3 text-center">Nilai Essay</th>
                    <th class="px-5 py-3 text-center">Nilai Total</th>
                    <th class="px-5 py-3 text-center">Durasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($sesiList as $i => $sesi)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-5 py-3 text-slate-400 font-medium">{{ $i + 1 }}</td>
                    <td class="px-5 py-3">
                        <div class="font-semibold text-slate-800">{{ $sesi->murid?->name ?? '-' }}</div>
                        <div class="text-xs text-slate-400">{{ $sesi->murid?->email }}</div>
                    </td>
                    <td class="px-5 py-3 text-center">
                        @php $cls = match($sesi->status){
                            'selesai'=>'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'sedang' =>'bg-blue-50 text-blue-700 border-blue-200',
                            default  =>'bg-slate-50 text-slate-500 border-slate-200',
                        }; @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $cls }}">
                            {{ App\Models\SesiUjian::$status[$sesi->status] }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center font-bold text-slate-700">
                        {{ $sesi->nilai_pg !== null ? number_format($sesi->nilai_pg, 0) : '—' }}
                    </td>
                    <td class="px-5 py-3 text-center font-bold text-slate-700">
                        {{ $sesi->nilai_essay !== null ? number_format($sesi->nilai_essay, 0) : '—' }}
                    </td>
                    <td class="px-5 py-3 text-center">
                        @if($sesi->nilai_total !== null)
                        @php $color = $sesi->nilai_total >= 75 ? 'text-emerald-700' : ($sesi->nilai_total >= 60 ? 'text-amber-700' : 'text-red-600'); @endphp
                        <span class="font-extrabold text-lg {{ $color }}">{{ number_format($sesi->nilai_total, 0) }}</span>
                        @else
                        <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-center text-slate-500">
                        {{ $sesi->durasiMenit() ? $sesi->durasiMenit().' mnt' : '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

</div>
@endsection
