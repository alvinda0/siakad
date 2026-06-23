@extends('admin.layouts.app')

@section('title', 'Mata Pelajaran')
@section('breadcrumb', 'Mata Pelajaran')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-800">Mata Pelajaran</h1>
            <p class="text-sm text-slate-500">Total: <strong>{{ $mapel->total() }}</strong> mata pelajaran</p>
        </div>
        <button type="button" onclick="openCreateModal()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
                style="background: var(--teal);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Mata Pelajaran
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
            {{-- Pertahankan per_page saat filter berubah --}}
            @if(request('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif
            <div class="flex-1 min-w-36">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Jurusan</label>
                <select name="jurusan"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">Semua Jurusan</option>
                    @foreach(App\Models\MataPelajaran::$jurusan as $val => $label)
                        <option value="{{ $val }}" {{ request('jurusan') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-32">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tingkat</label>
                <select name="tingkat"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">Semua Tingkat</option>
                    @foreach(App\Models\MataPelajaran::$tingkat as $val => $label)
                        <option value="{{ $val }}" {{ request('tingkat') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-32">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                <select name="aktif"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('aktif') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('aktif') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="flex-1 min-w-40">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Pencarian</label>
                <input type="search" name="q" value="{{ request('q') }}"
                       placeholder="Cari nama atau kode"
                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 transition">
                    Terapkan
                </button>
                <a href="{{ route('admin.mapel.index') }}"
                   class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if($mapel->isEmpty())
            <div class="p-12 text-center">
                <div class="text-6xl mb-4">📚</div>
                <h3 class="font-bold text-slate-600 mb-1">Belum ada data mata pelajaran</h3>
                <p class="text-sm text-slate-400">Tambahkan mata pelajaran baru dengan tombol di atas.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left">
                            <th class="px-5 py-3.5 font-semibold text-slate-500 w-10">#</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Kode</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Nama</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Jurusan</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Tingkat</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Guru</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Status</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($mapel as $i => $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3.5 text-slate-400">{{ $mapel->firstItem() + $i }}</td>
                            <td class="px-5 py-3.5 font-semibold text-slate-700">{{ $item->kode }}</td>
                            <td class="px-5 py-3.5 text-slate-700">{{ $item->nama }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-teal-50 text-teal-700">
                                    {{ $item->jurusanLabel() }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700">
                                    {{ $item->tingkatLabel() }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-700">{{ $item->guru?->name ?? '-' }}</td>
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
                                            data-mapel='{!! json_encode($item->only(["kode", "nama", "guru_id", "jurusan", "tingkat", "deskripsi", "aktif"]), JSON_UNESCAPED_UNICODE) !!}'
                                            onclick="openEditModal(this.dataset.id, JSON.parse(this.dataset.mapel))"
                                            class="text-xs px-3 py-1.5 rounded-lg font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('admin.mapel.toggle-aktif', $item) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="text-xs px-3 py-1.5 rounded-lg font-semibold text-white {{ $item->aktif ? 'bg-slate-600 hover:bg-slate-700' : 'bg-emerald-600 hover:bg-emerald-700' }} transition">
                                            {{ $item->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <button type="button"
                                            onclick="confirmDeleteModal('{{ route('admin.mapel.destroy', $item) }}', '{{ addslashes($item->nama) }}')"
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

            <x-per-page :paginator="$mapel" />
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════
     MODAL TAMBAH MATA PELAJARAN
═══════════════════════════════════════ --}}
<div id="modal-create"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden"
     onclick="if(event.target===this) closeCreateModal()">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h2 class="text-base font-extrabold text-slate-800">Tambah Mata Pelajaran</h2>
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

        <form method="POST" action="{{ route('admin.mapel.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="_form" value="create">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Kode</label>
                    <input type="text" name="kode" value="{{ old('kode') }}"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama</label>
                    <input type="text" name="nama" value="{{ old('nama') }}"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Guru</label>
                    <select name="guru_id"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Guru —</option>
                        @foreach($guruList as $guru)
                            <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>{{ $guru->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Jurusan</label>
                    <select name="jurusan"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Jurusan —</option>
                        @foreach(App\Models\MataPelajaran::$jurusan as $val => $label)
                            <option value="{{ $val }}" {{ old('jurusan') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tingkat</label>
                    <select name="tingkat"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Tingkat —</option>
                        @foreach(App\Models\MataPelajaran::$tingkat as $val => $label)
                            <option value="{{ $val }}" {{ old('tingkat') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                    <select name="aktif"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="1" {{ old('aktif', '1') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('aktif') === '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Deskripsi</label>
                <textarea name="deskripsi" rows="4"
                          class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">{{ old('deskripsi') }}</textarea>
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
     MODAL EDIT MATA PELAJARAN
═══════════════════════════════════════ --}}
<div id="modal-edit"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden"
     onclick="if(event.target===this) closeEditModal()">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h2 id="form-edit-title" class="text-base font-extrabold text-slate-800">Edit Mata Pelajaran</h2>
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
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Kode</label>
                    <input type="text" id="edit-kode" name="kode" value="{{ old('kode') }}"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama</label>
                    <input type="text" id="edit-nama" name="nama" value="{{ old('nama') }}"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Guru</label>
                    <select id="edit-guru_id" name="guru_id"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Guru —</option>
                        @foreach($guruList as $guru)
                            <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>{{ $guru->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Jurusan</label>
                    <select id="edit-jurusan" name="jurusan"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Jurusan —</option>
                        @foreach(App\Models\MataPelajaran::$jurusan as $val => $label)
                            <option value="{{ $val }}" {{ old('jurusan') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tingkat</label>
                    <select id="edit-tingkat" name="tingkat"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Tingkat —</option>
                        @foreach(App\Models\MataPelajaran::$tingkat as $val => $label)
                            <option value="{{ $val }}" {{ old('tingkat') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                    <select id="edit-aktif" name="aktif"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="1" {{ old('aktif', '1') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('aktif') === '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Deskripsi</label>
                <textarea id="edit-deskripsi" name="deskripsi" rows="4"
                          class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </button>
                <button id="form-edit-submit" type="submit"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const editRoutes = @json(collect($mapel->items())->mapWithKeys(fn($item) => [$item->id => route('admin.mapel.update', $item)]));

function openCreateModal() {
    document.getElementById('modal-create').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('modal-create').classList.add('hidden');
}

function openEditModal(id, data) {
    const form = document.getElementById('form-edit');
    form.action = editRoutes[id] ?? form.action;
    form.querySelector('input[name="edit_id"]').value = id;

    if (data) {
        document.getElementById('edit-kode').value = data.kode ?? '';
        document.getElementById('edit-nama').value = data.nama ?? '';
        document.getElementById('edit-guru_id').value = data.guru_id ?? '';
        document.getElementById('edit-jurusan').value = data.jurusan ?? '';
        document.getElementById('edit-tingkat').value = data.tingkat ?? '';
        document.getElementById('edit-aktif').value = data.aktif ? '1' : '0';
        document.getElementById('edit-deskripsi').value = data.deskripsi ?? '';
    }

    document.getElementById('modal-edit').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('modal-edit').classList.add('hidden');
}

function confirmDelete(url, message) {
    if (!confirm(message)) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    form.innerHTML = `@csrf @method('DELETE')`;
    document.body.appendChild(form);
    form.submit();
}@if(old('_form') === 'create')
    document.addEventListener('DOMContentLoaded', openCreateModal);
@endif

@php
    $editItem = null;
    if (old('_form') === 'edit' && old('edit_id')) {
        $editItem = [
            'kode' => old('kode'),
            'nama' => old('nama'),
            'guru_id' => old('guru_id'),
            'jurusan' => old('jurusan'),
            'tingkat' => old('tingkat'),
            'aktif' => old('aktif', '1') === '1',
            'deskripsi' => old('deskripsi'),
        ];
    }
@endphp

@if($editItem)
    document.addEventListener('DOMContentLoaded', function () {
        openEditModal({{ (int) old('edit_id') }}, @json($editItem));
    });
@endif
</script>
@endpush
@endsection
