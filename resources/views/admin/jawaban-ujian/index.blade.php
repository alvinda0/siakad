@extends('admin.layouts.app')

@section('title', 'Jawaban Ujian')
@section('breadcrumb', 'Jawaban Ujian')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div>
        <h1 class="text-xl font-extrabold text-slate-800">Jawaban Ujian Siswa</h1>
        <p class="text-sm text-slate-500 mt-0.5">Lihat dan periksa jawaban ujian yang telah dikerjakan oleh siswa.</p>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
        <form method="GET" id="form-filter" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Kelas</label>
                <select name="kelas_id" onchange="document.getElementById('form-filter').submit()"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
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
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Cari</label>
                <input type="text" name="cari" value="{{ request('cari') }}"
                       placeholder="Cari nama ujian..."
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>
            <button type="submit"
                    class="px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
                    style="background: var(--teal);">
                Cari
            </button>
            <a href="{{ route('admin.jawaban-ujian.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Reset
            </a>
        </form>
    </div>

    {{-- Empty state --}}
    @if($ujianList->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-16 text-center">
        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-4xl mx-auto mb-4">📋</div>
        <h3 class="font-bold text-slate-600 mb-1">Tidak ada data ujian</h3>
        <p class="text-sm text-slate-400">Belum ada jadwal ujian yang tercatat.</p>
    </div>
    @else

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50 text-xs font-bold text-slate-500 uppercase tracking-wide">
                        <th class="px-5 py-3 text-left">Ujian</th>
                        <th class="px-5 py-3 text-left">Kelas</th>
                        <th class="px-5 py-3 text-left">Guru</th>
                        <th class="px-5 py-3 text-center">Tanggal</th>
                        <th class="px-5 py-3 text-center">Jenis</th>
                        <th class="px-5 py-3 text-center">Selesai</th>
                        <th class="px-5 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($ujianList as $ujian)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3">
                            <div class="font-semibold text-slate-800">{{ $ujian->nama }}</div>
                            <div class="text-xs text-slate-400">{{ $ujian->mataPelajaran?->nama }}</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-teal-50 text-teal-700 border border-teal-100">
                                {{ $ujian->kelas?->nama }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate-600 text-xs">{{ $ujian->guru?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-center text-xs text-slate-500">
                            {{ $ujian->tanggal?->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            @php
                                $colors = ['UTS'=>'blue','UAS'=>'purple','UKK'=>'rose','Sumatif'=>'amber','Lainnya'=>'slate'];
                                $c = $colors[$ujian->jenis] ?? 'slate';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                                bg-{{ $c }}-50 text-{{ $c }}-700 border border-{{ $c }}-200">
                                {{ $ujian->jenis }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex items-center gap-1 font-bold text-emerald-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $ujian->total_selesai }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <a href="{{ route('admin.jawaban-ujian.show', $ujian) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition hover:brightness-110"
                               style="background: var(--teal);">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Lihat Jawaban
                            </a>
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
