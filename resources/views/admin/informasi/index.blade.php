@extends('admin.layouts.app')

@section('title', 'Informasi')
@section('breadcrumb', 'Informasi')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-800">Informasi</h1>
            <p class="text-sm text-slate-500">Total: <strong>{{ $informasi->total() }}</strong> informasi</p>
        </div>
        <button type="button" onclick="openCreateModal()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
                style="background: var(--teal);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Informasi
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

    {{-- Filter & Search --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            @if(request('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif

            <div class="flex-1 min-w-40">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tipe</label>
                <select name="tipe"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">Semua Tipe</option>
                    @foreach(App\Models\Informasi::$tipe as $val => $label)
                        <option value="{{ $val }}" {{ request('tipe') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

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
                       placeholder="Cari jenis, syarat, benefit"
                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 transition">
                    Terapkan
                </button>
                <a href="{{ route('admin.informasi.index') }}"
                   class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if($informasi->isEmpty())
            <div class="p-12 text-center">
                <div class="text-6xl mb-4">📋</div>
                <h3 class="font-bold text-slate-600 mb-1">Belum ada data informasi</h3>
                <p class="text-sm text-slate-400">Tambahkan informasi baru dengan tombol di atas.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left">
                            <th class="px-5 py-3.5 font-semibold text-slate-500 w-10">#</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Tipe</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Jenis</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Syarat</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Benefit</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500 text-center">Urutan</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Status</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($informasi as $i => $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3.5 text-slate-400">{{ $informasi->firstItem() + $i }}</td>
                            <td class="px-5 py-3.5">
                                @if($item->tipe === 'beasiswa')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-blue-50 text-blue-700">
                                        🎓 Beasiswa
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-amber-50 text-amber-700">
                                        ⭐ Promo Program
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-slate-700 max-w-xs">{{ $item->jenis }}</td>
                            <td class="px-5 py-3.5 text-slate-500 max-w-xs">
                                <span title="{{ $item->syarat }}">
                                    {{ Str::limit($item->syarat, 60) ?: '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500 max-w-xs">
                                <span title="{{ $item->benefit }}">
                                    {{ Str::limit($item->benefit, 60) ?: '-' }}
                                </span>
                            </td>
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
                                            data-info="{{ json_encode($item->only(['tipe','jenis','syarat','benefit','urutan','aktif']), JSON_HEX_QUOT | JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) }}"
                                            onclick="openEditModal(this.dataset.id, JSON.parse(this.dataset.info))"
                                            class="text-xs px-3 py-1.5 rounded-lg font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('admin.informasi.toggle-aktif', $item) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="text-xs px-3 py-1.5 rounded-lg font-semibold text-white {{ $item->aktif ? 'bg-slate-600 hover:bg-slate-700' : 'bg-emerald-600 hover:bg-emerald-700' }} transition">
                                            {{ $item->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <button type="button"
                                            onclick="confirmDeleteModal('{{ route('admin.informasi.destroy', $item) }}', '{{ addslashes($item->jenis) }}')"
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

            <x-per-page :paginator="$informasi" />
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════
     MODAL TAMBAH
═══════════════════════════════════════ --}}
<div id="modal-create"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden"
     onclick="if(event.target===this) closeCreateModal()">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h2 class="text-base font-extrabold text-slate-800">Tambah Informasi</h2>
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

        <form method="POST" action="{{ route('admin.informasi.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="_form" value="create">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tipe <span class="text-red-500">*</span></label>
                    <select name="tipe"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Tipe —</option>
                        @foreach(App\Models\Informasi::$tipe as $val => $label)
                            <option value="{{ $val }}" {{ old('tipe') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis <span class="text-red-500">*</span></label>
                    <input type="text" name="jenis" value="{{ old('jenis') }}"
                           placeholder="Nama jenis beasiswa / program"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                    <select name="aktif"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="1" {{ old('aktif', '1') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('aktif') === '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Urutan</label>
                    <input type="number" name="urutan" value="{{ old('urutan', 0) }}" min="0"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Syarat</label>
                <textarea name="syarat" rows="4" placeholder="Tuliskan syarat..."
                          class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">{{ old('syarat') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Benefit</label>
                <textarea name="benefit" rows="4" placeholder="Tuliskan benefit..."
                          class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">{{ old('benefit') }}</textarea>
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
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h2 class="text-base font-extrabold text-slate-800">Edit Informasi</h2>
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tipe <span class="text-red-500">*</span></label>
                    <select id="edit-tipe" name="tipe"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Tipe —</option>
                        @foreach(App\Models\Informasi::$tipe as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis <span class="text-red-500">*</span></label>
                    <input type="text" id="edit-jenis" name="jenis"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                    <select id="edit-aktif" name="aktif"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Urutan</label>
                    <input type="number" id="edit-urutan" name="urutan" min="0"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Syarat</label>
                <textarea id="edit-syarat" name="syarat" rows="4"
                          class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300"></textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Benefit</label>
                <textarea id="edit-benefit" name="benefit" rows="4"
                          class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300"></textarea>
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

<script>
(function () {
    const editRoutes = @json(collect($informasi->items())->mapWithKeys(fn($item) => [$item->id => route('admin.informasi.update', $item)]));

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
            document.getElementById('edit-tipe').value    = data.tipe    ?? '';
            document.getElementById('edit-jenis').value   = data.jenis   ?? '';
            document.getElementById('edit-aktif').value   = data.aktif   ? '1' : '0';
            document.getElementById('edit-urutan').value  = data.urutan  ?? 0;
            document.getElementById('edit-syarat').value  = data.syarat  ?? '';
            document.getElementById('edit-benefit').value = data.benefit ?? '';
        }

        document.getElementById('modal-edit').classList.remove('hidden');
    };

    window.closeEditModal = function () {
        document.getElementById('modal-edit').classList.add('hidden');
    };

    window.confirmDeleteModal = function (url, name) {
        if (!confirm('Hapus informasi "' + name + '"?\nTindakan ini tidak dapat dibatalkan.')) {
            return;
        }
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

    // Re-open create modal on validation error
    @if(old('_form') === 'create')
        openCreateModal();
    @endif

    // Re-open edit modal on validation error
    @php
        $editItem = null;
        if (old('_form') === 'edit' && old('edit_id')) {
            $editItem = [
                'tipe'    => old('tipe'),
                'jenis'   => old('jenis'),
                'aktif'   => old('aktif', '1') === '1',
                'urutan'  => old('urutan', 0),
                'syarat'  => old('syarat'),
                'benefit' => old('benefit'),
            ];
        }
    @endphp

    @if($editItem)
        openEditModal({{ (int) old('edit_id') }}, @json($editItem));
    @endif
})();
</script>

@endsection
