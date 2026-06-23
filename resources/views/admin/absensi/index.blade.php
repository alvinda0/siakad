@extends('admin.layouts.app')

@section('title', 'Absensi')
@section('breadcrumb', 'Absensi')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-800">Absensi Siswa</h1>
            <p class="text-sm text-slate-500">Pilih kelas, jadwal, dan tanggal untuk mencatat kehadiran.</p>
        </div>
        <a href="{{ route('admin.absensi.rekap') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
           style="background: var(--teal);">
            📊 Rekap Absensi
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 rounded-xl">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <form method="GET" id="filter-form" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Kelas</label>
                <select name="kelas_id" id="sel-kelas"
                        onchange="document.getElementById('filter-form').submit()"
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
                <label class="block text-sm font-semibold text-slate-700 mb-2">Jadwal / Mata Pelajaran</label>
                <select name="jadwal_id"
                        onchange="document.getElementById('filter-form').submit()"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300"
                        {{ $jadwalList->isEmpty() ? 'disabled' : '' }}>
                    <option value="">— Pilih Jadwal —</option>
                    @foreach($jadwalList as $j)
                        <option value="{{ $j->id }}" {{ request('jadwal_id') == $j->id ? 'selected' : '' }}>
                            {{ $j->mataPelajaran->nama }} ({{ $j->hari }}, {{ $j->jamLabel() }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal->format('Y-m-d') }}"
                       onchange="document.getElementById('filter-form').submit()"
                       class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>
            <div class="flex items-end">
                <a href="{{ route('admin.absensi.index') }}"
                   class="w-full inline-flex justify-center px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Summary cards --}}
    @if($rekap)
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([['Hadir','emerald','✅'], ['Sakit','blue','🤒'], ['Izin','amber','📋'], ['Alpha','red','❌']] as [$s, $c, $icon])
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-3">
            <div class="text-2xl">{{ $icon }}</div>
            <div>
                <p class="text-xs font-semibold text-slate-500">{{ $s }}</p>
                <p class="text-2xl font-extrabold text-{{ $c }}-600">{{ $rekap[$s] }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Form Absensi --}}
    @if($selectedJadwal && $absensiRows->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between"
             style="background: var(--teal-light);">
            <div>
                <p class="font-extrabold text-sm" style="color: var(--teal);">
                    {{ $selectedJadwal->mataPelajaran->nama }}
                    — {{ $selectedKelas->nama }}
                </p>
                <p class="text-xs text-slate-500">
                    {{ $selectedJadwal->hari }}, {{ $selectedJadwal->jamLabel() }}
                    &bull; {{ $tanggal->translatedFormat('d F Y') }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.absensi.simpan') }}">
            @csrf
            <input type="hidden" name="jadwal_id" value="{{ $selectedJadwal->id }}">
            <input type="hidden" name="tanggal"   value="{{ $tanggal->format('Y-m-d') }}">

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left">
                            <th class="px-5 py-3 font-semibold text-slate-500 w-10">#</th>
                            <th class="px-5 py-3 font-semibold text-slate-500">Nama Siswa</th>
                            @foreach(App\Models\Absensi::$status as $s => $_)
                            <th class="px-4 py-3 font-semibold text-slate-500 text-center">{{ $s }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($absensiRows as $i => $row)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3 text-slate-400">{{ $i + 1 }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                                         style="background: var(--gold);">
                                        {{ strtoupper(substr($row['murid']->name, 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-slate-700">{{ $row['murid']->name }}</span>
                                </div>
                            </td>
                            @foreach(App\Models\Absensi::$status as $s => $_)
                            <td class="px-4 py-3 text-center">
                                <input type="radio"
                                       name="absensi[{{ $row['murid']->id }}]"
                                       value="{{ $s }}"
                                       class="w-4 h-4 accent-teal-600"
                                       {{ $row['status'] === $s ? 'checked' : '' }}
                                       {{ $row['status'] === null && $s === 'Hadir' ? 'checked' : '' }}>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex justify-end gap-3">
                <button type="submit"
                        class="px-5 py-2 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 transition">
                    💾 Simpan Absensi
                </button>
            </div>
        </form>
    </div>
    @elseif(request('kelas_id') && request('jadwal_id') && $absensiRows->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
        <div class="text-6xl mb-4">🎓</div>
        <h3 class="font-bold text-slate-600 mb-1">Tidak ada murid di kelas ini</h3>
        <p class="text-sm text-slate-400">Assign murid ke kelas terlebih dahulu.</p>
    </div>
    @elseif(!request('kelas_id'))
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
        <div class="text-6xl mb-4">✅</div>
        <h3 class="font-bold text-slate-600 mb-1">Pilih kelas dan jadwal untuk memulai absensi</h3>
        <p class="text-sm text-slate-400">Gunakan filter di atas untuk memilih sesi absensi.</p>
    </div>
    @endif

</div>
@endsection
