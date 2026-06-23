@extends('admin.layouts.app')

@section('title', 'Daftar Murid')
@section('breadcrumb', 'Murid')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-800">Daftar Murid</h1>
            <p class="text-sm text-slate-500">Total: <strong>{{ $murid->total() }}</strong> peserta didik</p>
        </div>
        <a href="{{ route('admin.murid.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
           style="background: var(--teal);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Murid
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

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if($murid->isEmpty())
            <div class="p-12 text-center">
                <div class="text-6xl mb-4">🎓</div>
                <h3 class="font-bold text-slate-600 mb-1">Belum ada data murid</h3>
                <p class="text-sm text-slate-400">Tambahkan murid pertama dengan klik tombol di atas.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left">
                            <th class="px-5 py-3.5 font-semibold text-slate-500 w-10">#</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Nama</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Email</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Jurusan</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Kelas</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Terdaftar</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($murid as $i => $user)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3.5 text-slate-400">
                                {{ $murid->firstItem() + $i }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white shrink-0"
                                         style="background: #E6920A;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-slate-700">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $user->email }}</td>
                            <td class="px-5 py-3.5">
                                @if($user->kandidatProfile?->jurusan)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700">
                                        {{ $user->kandidatProfile->jurusan }}
                                    </span>
                                @else
                                    <span class="text-slate-300 text-xs">—</span>
                                @endif
                            </td>

                            {{-- Kolom Kelas — view only --}}
                            <td class="px-5 py-3.5">
                                @if($user->kandidatProfile?->kelas)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-teal-50 text-teal-700">
                                        🏫 {{ $user->kandidatProfile->kelas->nama }}
                                    </span>
                                @else
                                    <span class="text-slate-300 text-xs">—</span>
                                @endif
                            </td>

                            <td class="px-5 py-3.5 text-slate-400">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <a href="{{ route('admin.murid.show', $user) }}"
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

            <x-per-page :paginator="$murid" />
        @endif
    </div>

</div>
@endsection
