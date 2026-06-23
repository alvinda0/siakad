@extends('admin.layouts.app')

@section('title', 'Detail Kelas — ' . $kelas->nama)
@section('breadcrumb', 'Kelas / Detail')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.kelas.index') }}"
               class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-slate-800">{{ $kelas->nama }}</h1>
                <p class="text-sm text-slate-500">{{ $kelas->tingkatJurusanLabel() }} — TA {{ $kelas->tahunAjaranLabel() }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.kelas.edit', $kelas) }}"
               class="text-sm px-4 py-2 rounded-xl font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                Edit Kelas
            </a>
            <form method="POST" action="{{ route('admin.kelas.destroy', $kelas) }}"
                  onsubmit="return confirm('Hapus kelas {{ addslashes($kelas->nama) }}?\nTindakan ini tidak dapat dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="text-sm px-4 py-2 rounded-xl font-semibold text-red-600 bg-red-50 hover:bg-red-100 transition">
                    Hapus Kelas
                </button>
            </form>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0"
                 style="background: var(--teal-light);">
                🏫
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-extrabold text-slate-800 text-lg">{{ $kelas->nama }}</p>
                <p class="text-sm text-slate-500">TA {{ $kelas->tahunAjaranLabel() }}</p>
            </div>
            {{-- Stat --}}
            <div class="text-right shrink-0">
                <p class="text-2xl font-extrabold text-slate-800">{{ $siswa->total() }}</p>
                <p class="text-xs text-slate-400">dari {{ $kelas->kapasitas }} kursi</p>
            </div>
        </div>

        <dl class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-slate-50">
            <div class="px-5 py-4">
                <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Tingkat</dt>
                <dd>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700">
                        {{ $kelas->tingkat }}
                    </span>
                </dd>
            </div>
            <div class="px-5 py-4">
                <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Jurusan</dt>
                <dd>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-teal-50 text-teal-700">
                        {{ $kelas->jurusan }}
                    </span>
                </dd>
            </div>
            <div class="px-5 py-4">
                <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Kapasitas</dt>
                <dd class="text-sm font-semibold text-slate-700">{{ $kelas->kapasitas }} siswa</dd>
            </div>
            <div class="px-5 py-4">
                <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Wali Kelas</dt>
                <dd class="text-sm font-semibold text-slate-700">
                    {{ $kelas->waliKelas?->name ?? $kelas->wali_kelas ?? '—' }}
                </dd>
            </div>
        </dl>
    </div>

    {{-- Daftar Siswa --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-slate-700">
                Daftar Siswa
                <span class="ml-2 text-xs font-semibold px-2 py-0.5 rounded-full bg-teal-50 text-teal-700">
                    {{ $siswa->total() }}
                </span>
            </h2>
        </div>

        @if($siswa->isEmpty())
            <div class="p-12 text-center">
                <div class="text-5xl mb-3">🎓</div>
                <h3 class="font-bold text-slate-600 mb-1">Belum ada siswa di kelas ini</h3>
                <p class="text-sm text-slate-400">Siswa bisa ditugaskan ke kelas ini dari halaman detail murid.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left">
                            <th class="px-5 py-3.5 font-semibold text-slate-500 w-10">#</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Nama Siswa</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">NISN</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">NIK</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Jenis Kelamin</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">No HP</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($siswa as $i => $profile)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3.5 text-slate-400">{{ $siswa->firstItem() + $i }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    @if($profile->foto)
                                        <img src="{{ Storage::url($profile->foto) }}" alt=""
                                             class="w-9 h-9 rounded-full object-cover shrink-0">
                                    @else
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white shrink-0"
                                             style="background: #E6920A;">
                                            {{ strtoupper(substr($profile->nama_lengkap, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-slate-700">{{ $profile->nama_lengkap }}</p>
                                        @if($profile->nama_panggilan)
                                            <p class="text-xs text-slate-400">"{{ $profile->nama_panggilan }}"</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500 font-mono text-xs">
                                {{ $profile->nisn ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-500 font-mono text-xs">
                                {{ $profile->nik ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if($profile->jenis_kelamin === 'L')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-blue-50 text-blue-700">
                                        Laki-laki
                                    </span>
                                @elseif($profile->jenis_kelamin === 'P')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-pink-50 text-pink-700">
                                        Perempuan
                                    </span>
                                @else
                                    <span class="text-slate-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-500 text-xs">
                                {{ $profile->no_hp ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <a href="{{ route('admin.murid.show', $profile->user_id) }}"
                                   class="text-xs px-3 py-1.5 rounded-lg font-semibold text-white transition hover:brightness-110"
                                   style="background: var(--teal);">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-per-page :paginator="$siswa" />
        @endif
    </div>

</div>
@endsection
