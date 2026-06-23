@extends('admin.layouts.app')

@section('title', 'Kandidat PPDB')
@section('breadcrumb', 'Kandidat PPDB')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-extrabold text-slate-800">Kandidat PPDB</h1>
            <p class="text-sm text-slate-500 mt-0.5">
                Total: <strong>{{ $kandidat->total() }}</strong> calon peserta didik baru
            </p>
        </div>
        {{-- Filter Select --}}
        <form method="GET" action="{{ route('admin.kandidat.index') }}">
            @if(request('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif
            <select name="status" onchange="this.form.submit()"
                    class="text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white text-slate-700 font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400 cursor-pointer">
                <option value="" {{ request('status', '') === '' ? 'selected' : '' }}>Semua Status</option>
                <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Menunggu</option>
                <option value="diterima"  {{ request('status') === 'diterima'  ? 'selected' : '' }}>Diterima</option>
                <option value="ditolak"   {{ request('status') === 'ditolak'   ? 'selected' : '' }}>Ditolak</option>
                <option value="draft"     {{ request('status') === 'draft'     ? 'selected' : '' }}>Draft</option>
            </select>
        </form>
    </div>

    {{-- Flash message --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
        <span>✅</span> {{ session('success') }}
    </div>
    @endif

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if($kandidat->isEmpty())
            <div class="p-12 text-center">
                <div class="text-6xl mb-4">📋</div>
                <h3 class="font-bold text-slate-600 mb-1">Belum ada kandidat</h3>
                <p class="text-sm text-slate-400">Belum ada pendaftar PPDB saat ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left bg-slate-50/60">
                            <th class="px-5 py-3.5 font-semibold text-slate-500 w-10">#</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Nama</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Jurusan</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Asal Sekolah</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Tanggal Daftar</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Status</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($kandidat as $i => $user)
                        @php $profile = $user->kandidatProfile; @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3.5 text-slate-400">
                                {{ $kandidat->firstItem() + $i }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    {{-- Foto atau inisial --}}
                                    @if($profile?->foto)
                                        <img src="{{ Storage::url($profile->foto) }}"
                                             class="w-9 h-9 rounded-full object-cover shrink-0"
                                             alt="{{ $user->name }}">
                                    @else
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white shrink-0"
                                             style="background: #7C3AED;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-slate-700">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                @if($profile?->jurusan)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700">
                                        {{ $profile->jurusan }}
                                    </span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">
                                {{ $profile?->asal_sekolah ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-400">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5">
                                @php
                                    $status = $profile?->status ?? 'draft';
                                    $badge = match($status) {
                                        'submitted' => 'bg-amber-100 text-amber-700',
                                        'diterima'  => 'bg-emerald-100 text-emerald-700',
                                        'ditolak'   => 'bg-red-100 text-red-700',
                                        default     => 'bg-slate-100 text-slate-500',
                                    };
                                    $label = match($status) {
                                        'submitted' => 'Menunggu',
                                        'diterima'  => 'Diterima',
                                        'ditolak'   => 'Ditolak',
                                        default     => 'Draft',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $badge }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    {{-- Detail --}}
                                    <a href="{{ route('admin.kandidat.show', $user) }}"
                                       class="text-xs px-3 py-1.5 rounded-lg font-semibold text-white bg-indigo-500 hover:bg-indigo-600 transition">
                                        Detail
                                    </a>

                                    {{-- Terima --}}
                                    @if($status !== 'diterima')
                                    <form id="form-terima-{{ $user->id }}" method="POST"
                                          action="{{ route('admin.kandidat.accept', $user) }}">
                                        @csrf @method('PATCH')
                                        <button type="button"
                                                onclick="openConfirmModal({ type: 'terima', name: '{{ addslashes($user->name) }}', formId: 'form-terima-{{ $user->id }}' })"
                                                class="text-xs px-3 py-1.5 rounded-lg font-semibold text-white bg-emerald-500 hover:bg-emerald-600 transition">
                                            Terima
                                        </button>
                                    </form>
                                    @endif

                                    {{-- Tolak --}}
                                    @if($status !== 'ditolak' && $status !== 'diterima')
                                    <form id="form-tolak-{{ $user->id }}" method="POST"
                                          action="{{ route('admin.kandidat.reject', $user) }}">
                                        @csrf @method('PATCH')
                                        <button type="button"
                                                onclick="openConfirmModal({ type: 'tolak', name: '{{ addslashes($user->name) }}', formId: 'form-tolak-{{ $user->id }}' })"
                                                class="text-xs px-3 py-1.5 rounded-lg font-semibold text-white bg-red-500 hover:bg-red-600 transition">
                                            Tolak
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-per-page :paginator="$kandidat" />
        @endif
    </div>

</div>
@endsection
