@extends('admin.layouts.app')

@section('title', 'Daftar Murid')
@section('breadcrumb', 'Murid')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-800">Daftar Murid</h1>
            <p class="text-sm text-slate-500">
                Total: <strong>{{ $murid->total() }}</strong> peserta didik
                @if($belumKelasCount > 0)
                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-amber-100 text-amber-700">
                        ⚠️ {{ $belumKelasCount }} belum punya kelas
                    </span>
                @endif
            </p>
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

    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Cari Nama / Email</label>
                <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Ketik nama atau email..."
                       class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>
            <div class="min-w-[180px]">
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Filter Kelas</label>
                <select name="kelas_id"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">Semua Kelas</option>
                    <option value="belum" {{ request('kelas_id') === 'belum' ? 'selected' : '' }}>
                        ⚠️ Belum Punya Kelas
                    </option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }} (TA {{ $k->tahunAjaranLabel() }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 transition">
                    Cari
                </button>
                <a href="{{ route('admin.murid.index') }}"
                   class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Bulk Assign Kelas --}}
    <div id="bulk-bar"
         class="hidden items-center gap-3 flex-wrap bg-indigo-50 border border-indigo-200 rounded-xl px-4 py-3">
        <span class="text-sm font-semibold text-indigo-700">
            <span id="bulk-count">0</span> murid dipilih
        </span>
        <form method="POST" action="{{ route('admin.murid.bulk-assign-kelas') }}"
              id="bulk-form" class="flex items-center gap-2 flex-wrap">
            @csrf
            <div id="bulk-hidden-ids"></div>
            <select name="kelas_id" required
                    class="rounded-xl border border-indigo-200 bg-white px-3 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300 min-w-[200px]">
                <option value="">— Pilih Kelas Tujuan —</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k->id }}">{{ $k->nama }} (TA {{ $k->tahunAjaranLabel() }})</option>
                @endforeach
            </select>
            <button type="submit"
                    class="px-4 py-1.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition">
                Assign Kelas
            </button>
            <button type="button" onclick="clearSelection()"
                    class="px-3 py-1.5 rounded-xl text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition">
                Batal
            </button>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if($murid->isEmpty())
            <div class="p-12 text-center">
                <div class="text-6xl mb-4">🎓</div>
                <h3 class="font-bold text-slate-600 mb-1">Tidak ada data murid</h3>
                <p class="text-sm text-slate-400">Coba ubah filter atau tambahkan murid baru.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left">
                            <th class="px-4 py-3.5 w-10">
                                <input type="checkbox" id="check-all"
                                       class="rounded border-slate-300 text-teal-600 focus:ring-teal-400"
                                       onchange="toggleAll(this)">
                            </th>
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
                        <tr class="hover:bg-slate-50 transition" id="row-{{ $user->id }}">
                            <td class="px-4 py-3.5">
                                <input type="checkbox" name="murid_ids[]" value="{{ $user->id }}"
                                       class="row-check rounded border-slate-300 text-teal-600 focus:ring-teal-400"
                                       onchange="updateBulkBar()">
                            </td>
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

                            {{-- Quick Assign Kelas --}}
                            <td class="px-5 py-3.5">
                                @if($user->kandidatProfile)
                                <form method="POST" action="{{ route('admin.murid.assign-kelas', $user) }}"
                                      class="flex items-center gap-1.5">
                                    @csrf
                                    @method('PATCH')
                                    <select name="kelas_id"
                                            onchange="this.form.submit()"
                                            class="text-xs rounded-lg border {{ $user->kandidatProfile->kelas_id ? 'border-teal-200 bg-teal-50 text-teal-700' : 'border-amber-200 bg-amber-50 text-amber-700' }} px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-teal-300 max-w-[160px]">
                                        <option value="">— Pilih Kelas —</option>
                                        @foreach($kelasList as $k)
                                            <option value="{{ $k->id }}" {{ $user->kandidatProfile->kelas_id == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                                @else
                                    <span class="text-xs text-slate-300 italic">Belum ada profil</span>
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

<script>
function updateBulkBar() {
    const checked = document.querySelectorAll('.row-check:checked');
    const bar     = document.getElementById('bulk-bar');
    const counter = document.getElementById('bulk-count');
    const hiddenContainer = document.getElementById('bulk-hidden-ids');

    counter.textContent = checked.length;

    // Rebuild hidden inputs
    hiddenContainer.innerHTML = '';
    checked.forEach(cb => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'murid_ids[]';
        input.value = cb.value;
        hiddenContainer.appendChild(input);
    });

    if (checked.length > 0) {
        bar.classList.remove('hidden');
        bar.classList.add('flex');
    } else {
        bar.classList.add('hidden');
        bar.classList.remove('flex');
    }
}

function toggleAll(master) {
    document.querySelectorAll('.row-check').forEach(cb => {
        cb.checked = master.checked;
    });
    updateBulkBar();
}

function clearSelection() {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = false);
    const master = document.getElementById('check-all');
    if (master) master.checked = false;
    updateBulkBar();
}
</script>
@endsection
