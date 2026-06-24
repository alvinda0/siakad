@extends('admin.layouts.app')

@section('title', 'Ticker Info Berjalan')
@section('breadcrumb', 'Ticker')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-800">Ticker Info Berjalan</h1>
            <p class="text-sm text-slate-500">Total: <strong>{{ $tickers->total() }}</strong> item — tampil sebagai marquee di halaman beranda</p>
        </div>
        <button type="button" onclick="openCreateModal()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
                style="background: var(--teal);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Ticker
        </button>
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

    {{-- Preview ticker --}}
    @if($tickers->where('aktif', true)->count() > 0)
    <div class="rounded-xl overflow-hidden border border-teal-200">
        <div class="px-3 py-1.5 text-xs font-bold uppercase tracking-widest text-teal-700" style="background:#e0f2f1;">
            Preview Ticker Aktif
        </div>
        <div class="overflow-hidden py-2.5 px-4" style="background:var(--teal-dark, #0D5F6E);">
            <div class="flex items-center gap-4">
                <span class="text-xs font-extrabold px-2.5 py-1 rounded-full whitespace-nowrap shrink-0" style="background:#F9C940; color:#fff;">📢 INFO</span>
                <div class="overflow-hidden flex-1">
                    <p class="text-sm text-white whitespace-nowrap animate-marquee">
                        @foreach($tickers->where('aktif', true)->sortBy('urutan') as $t)
                            {{ $t->icon }} {{ $t->konten }}@if(!$loop->last) &nbsp;|&nbsp; @endif
                        @endforeach
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            @if(request('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif

            <div class="flex-1 min-w-36">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                <select name="aktif"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('aktif') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('aktif') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="flex-1 min-w-48">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Pencarian</label>
                <input type="search" name="q" value="{{ request('q') }}"
                       placeholder="Cari teks ticker..."
                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 transition">
                    Terapkan
                </button>
                <a href="{{ route('admin.ticker.index') }}"
                   class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if($tickers->isEmpty())
            <div class="p-12 text-center">
                <div class="text-6xl mb-4">📢</div>
                <h3 class="font-bold text-slate-600 mb-1">Belum ada ticker</h3>
                <p class="text-sm text-slate-400">Tambahkan item ticker dengan tombol di atas.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left">
                            <th class="px-5 py-3.5 font-semibold text-slate-500 w-10">#</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500 w-16">Icon</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Konten</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500 text-center w-20">Urutan</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500 w-24">Status</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($tickers as $i => $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3.5 text-slate-400">{{ $tickers->firstItem() + $i }}</td>
                            <td class="px-5 py-3.5 text-xl text-center">{{ $item->icon }}</td>
                            <td class="px-5 py-3.5 font-medium text-slate-700">{{ $item->konten }}</td>
                            <td class="px-5 py-3.5 text-center text-slate-600">{{ $item->urutan }}</td>
                            <td class="px-5 py-3.5">
                                @if($item->aktif)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-slate-100 text-slate-500">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex flex-wrap gap-2">
                                    <button type="button"
                                            data-id="{{ $item->id }}"
                                            data-info="{{ json_encode($item->only(['konten','icon','urutan','aktif']), JSON_HEX_QUOT | JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) }}"
                                            onclick="openEditModal(this.dataset.id, JSON.parse(this.dataset.info))"
                                            class="text-xs px-3 py-1.5 rounded-lg font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('admin.ticker.toggle-aktif', $item) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="text-xs px-3 py-1.5 rounded-lg font-semibold text-white {{ $item->aktif ? 'bg-slate-600 hover:bg-slate-700' : 'bg-emerald-600 hover:bg-emerald-700' }} transition">
                                            {{ $item->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <button type="button"
                                            onclick="confirmDeleteModal('{{ route('admin.ticker.destroy', $item) }}', '{{ addslashes(Str::limit($item->konten, 40)) }}')"
                                            class="text-xs px-3 py-1.5 rounded-lg font-semibold text-red-600 bg-red-50 hover:bg-red-100 transition">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-per-page :paginator="$tickers" />
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════
     MODAL TAMBAH
═══════════════════════════════════════ --}}
<div id="modal-create"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden"
     onclick="if(event.target===this) closeCreateModal()">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h2 class="text-base font-extrabold text-slate-800">Tambah Ticker</h2>
            <button type="button" onclick="closeCreateModal()"
                    class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        @if($errors->any() && old('_form') === 'create')
        <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.ticker.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="_form" value="create">

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Teks Ticker <span class="text-red-500">*</span></label>
                <textarea name="konten" rows="3" maxlength="500"
                          placeholder="Contoh: PPDB 2026/2027 telah dibuka — daftarkan segera"
                          class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">{{ old('konten') }}</textarea>
                <p class="text-xs text-slate-400 mt-1">Maksimal 500 karakter</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Icon <span class="text-slate-400 font-normal">(emoji)</span></label>
                    <input type="text" name="icon" value="{{ old('icon', '📢') }}" maxlength="10"
                           placeholder="📢"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Urutan</label>
                    <input type="number" name="urutan" value="{{ old('urutan', 0) }}" min="0"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                <select name="aktif"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="1" {{ old('aktif', '1') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('aktif') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeCreateModal()"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════
     MODAL EDIT
═══════════════════════════════════════ --}}
<div id="modal-edit"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden"
     onclick="if(event.target===this) closeEditModal()">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h2 class="text-base font-extrabold text-slate-800">Edit Ticker</h2>
            <button type="button" onclick="closeEditModal()"
                    class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        @if($errors->any() && old('_form') === 'edit')
        <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form id="form-edit" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="_form" value="edit">
            <input type="hidden" name="edit_id" value="{{ old('edit_id') }}">

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Teks Ticker <span class="text-red-500">*</span></label>
                <textarea id="edit-konten" name="konten" rows="3" maxlength="500"
                          class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300"></textarea>
                <p class="text-xs text-slate-400 mt-1">Maksimal 500 karakter</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Icon <span class="text-slate-400 font-normal">(emoji)</span></label>
                    <input type="text" id="edit-icon" name="icon" maxlength="10"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Urutan</label>
                    <input type="number" id="edit-urutan" name="urutan" min="0"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                <select id="edit-aktif" name="aktif"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    @keyframes marquee { 0%{transform:translateX(100%)} 100%{transform:translateX(-100%)} }
    .animate-marquee { display:inline-block; animation: marquee 25s linear infinite; }
</style>
@endpush

<script>
(function () {
    const editRoutes = @json(collect($tickers->items())->mapWithKeys(fn($item) => [$item->id => route('admin.ticker.update', $item)]));

    window.openCreateModal = function () {
        document.getElementById('modal-create').classList.remove('hidden');
    };

    window.closeCreateModal = function () {
        document.getElementById('modal-create').classList.add('hidden');
    };

    window.openEditModal = function (id, data) {
        const form = document.getElementById('form-edit');
        form.action = editRoutes[id] ?? form.action;
        form.querySelector('input[name="edit_id"]').value = id;

        if (data) {
            document.getElementById('edit-konten').value = data.konten ?? '';
            document.getElementById('edit-icon').value   = data.icon   ?? '📢';
            document.getElementById('edit-urutan').value = data.urutan ?? 0;
            document.getElementById('edit-aktif').value  = data.aktif ? '1' : '0';
        }

        document.getElementById('modal-edit').classList.remove('hidden');
    };

    window.closeEditModal = function () {
        document.getElementById('modal-edit').classList.add('hidden');
    };

    window.confirmDeleteModal = function (url, name) {
        if (!confirm('Hapus ticker "' + name + '"?\nTindakan ini tidak dapat dibatalkan.')) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
    };

    @if(old('_form') === 'create')
        openCreateModal();
    @endif

    @php
        $editItem = null;
        if (old('_form') === 'edit' && old('edit_id')) {
            $editItem = [
                'konten' => old('konten'),
                'icon'   => old('icon', '📢'),
                'urutan' => old('urutan', 0),
                'aktif'  => old('aktif', '1') === '1',
            ];
        }
    @endphp

    @if($editItem)
        openEditModal({{ (int) old('edit_id') }}, @json($editItem));
    @endif
})();
</script>

@endsection
