@extends('admin.layouts.app')

@section('title', 'Daftar Guru')
@section('breadcrumb', 'Guru')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-800">Daftar Guru</h1>
            <p class="text-sm text-slate-500">Total: <strong>{{ $guru->total() }}</strong> tenaga pendidik</p>
        </div>
        <a href="{{ route('admin.guru.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
           style="background: var(--teal);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Guru
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
        @if($guru->isEmpty())
            <div class="p-12 text-center">
                <div class="text-6xl mb-4">👨‍🏫</div>
                <h3 class="font-bold text-slate-600 mb-1">Belum ada data guru</h3>
                <p class="text-sm text-slate-400">Tambahkan guru pertama dengan klik tombol di atas.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left">
                            <th class="px-5 py-3.5 font-semibold text-slate-500 w-10">#</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Nama</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Email</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Wali Kelas</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Terdaftar</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($guru as $i => $user)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3.5 text-slate-400">
                                {{ $guru->firstItem() + $i }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white shrink-0"
                                         style="background: var(--teal);">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-slate-700">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $user->email }}</td>

                            {{-- Wali Kelas --}}
                            <td class="px-5 py-3.5">
                                @php
                                    $waliDi = $user->waliKelas->first();
                                @endphp
                                @if($waliDi)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-teal-50 text-teal-700">
                                        🏫 {{ $waliDi->nama }}
                                    </span>
                                @else
                                    <span class="text-slate-300 text-xs">—</span>
                                @endif
                            </td>

                            <td class="px-5 py-3.5 text-slate-400">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <button type="button"
                                        onclick="openWaliModal({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->waliKelas->first()?->id ?? 'null' }})"
                                        class="text-xs px-3 py-1.5 rounded-lg font-semibold text-white transition hover:brightness-110"
                                        style="background: var(--teal);">
                                    Edit Wali Kelas
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-per-page :paginator="$guru" />
        @endif
    </div>

</div>

{{-- ══ Modal Assign Wali Kelas ══ --}}
<div id="wali-modal"
     class="fixed inset-0 z-50 hidden flex items-center justify-center p-4"
     style="background: rgba(0,0,0,0.4);">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-5"
         onclick="event.stopPropagation()">

        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-extrabold text-slate-800 text-base">Assign Wali Kelas</h3>
                <p id="modal-guru-name" class="text-sm text-slate-500 mt-0.5"></p>
            </div>
            <button onclick="closeWaliModal()"
                    class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="wali-form" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Pilih Kelas
                </label>
                <select id="wali-kelas-select" name="kelas_id"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white text-slate-700
                               focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">— Tidak Menjadi Wali Kelas —</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}"
                                data-wali="{{ $k->wali_kelas_id }}"
                                data-label="{{ $k->waliKelas?->name }}">
                            {{ $k->nama }} (TA {{ $k->tahunAjaranLabel() }})
                            @if($k->wali_kelas_id)
                                — wali: {{ $k->waliKelas?->name }}
                            @endif
                        </option>
                    @endforeach
                </select>
                <p id="wali-warning" class="hidden mt-2 text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2"></p>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
                        style="background: var(--teal);">
                    Simpan
                </button>
                <button type="button" onclick="closeWaliModal()"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const routes = @json(
    collect($guru->items())->mapWithKeys(fn($u) => [
        $u->id => route('admin.guru.assign-wali-kelas', $u)
    ])
);

function openWaliModal(userId, userName, currentKelasId) {
    document.getElementById('modal-guru-name').textContent = userName;

    const actionUrl = routes[userId] ?? null;
    if (!actionUrl) {
        alert('Gagal memuat URL aksi. Silakan refresh halaman.');
        return;
    }
    document.getElementById('wali-form').action = actionUrl;

    const sel = document.getElementById('wali-kelas-select');
    sel.value = currentKelasId ?? '';
    checkWaliWarning();

    document.getElementById('wali-modal').classList.remove('hidden');
    document.getElementById('wali-modal').classList.add('flex');
}

function closeWaliModal() {
    document.getElementById('wali-modal').classList.add('hidden');
    document.getElementById('wali-modal').classList.remove('flex');
}

function checkWaliWarning() {
    const sel     = document.getElementById('wali-kelas-select');
    const opt     = sel.options[sel.selectedIndex];
    const warning = document.getElementById('wali-warning');
    const waliId  = opt?.dataset?.wali;
    const waliLabel = opt?.dataset?.label;

    if (sel.value && waliId && waliId !== 'null') {
        warning.textContent = `⚠️ Kelas ini saat ini dipegang oleh ${waliLabel}. Menyimpan akan mengganti wali kelasnya.`;
        warning.classList.remove('hidden');
    } else {
        warning.classList.add('hidden');
    }
}

document.getElementById('wali-kelas-select').addEventListener('change', checkWaliWarning);

// Tutup modal klik backdrop
document.getElementById('wali-modal').addEventListener('click', closeWaliModal);
</script>

@endsection
