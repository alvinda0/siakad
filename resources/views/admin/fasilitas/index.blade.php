@extends('admin.layouts.app')

@section('title', 'Fasilitas')
@section('breadcrumb', 'Fasilitas')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-800">Fasilitas Sekolah</h1>
            <p class="text-sm text-slate-500">Total: <strong>{{ $fasilitas->total() }}</strong> fasilitas</p>
        </div>
        <button type="button" onclick="openCreateModal()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
                style="background: var(--teal);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Fasilitas
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

    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            @if(request('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif
            <div class="flex-1 min-w-36">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Kategori</label>
                <select name="kategori"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">Semua Kategori</option>
                    @foreach(App\Models\Fasilitas::$kategori as $val => $label)
                        <option value="{{ $val }}" {{ request('kategori') === $val ? 'selected' : '' }}>{{ $label }}</option>
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
            <div class="flex-1 min-w-48">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Pencarian</label>
                <input type="search" name="q" value="{{ request('q') }}"
                       placeholder="Cari nama atau deskripsi"
                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 transition">
                    Terapkan
                </button>
                <a href="{{ route('admin.fasilitas.index') }}"
                   class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if($fasilitas->isEmpty())
            <div class="p-12 text-center">
                <div class="text-6xl mb-4">🏫</div>
                <h3 class="font-bold text-slate-600 mb-1">Belum ada data fasilitas</h3>
                <p class="text-sm text-slate-400">Tambahkan fasilitas sekolah dengan tombol di atas.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left">
                            <th class="px-4 py-3.5 font-semibold text-slate-500 w-10">#</th>
                            <th class="px-4 py-3.5 font-semibold text-slate-500 w-20">Gambar</th>
                            <th class="px-4 py-3.5 font-semibold text-slate-500">Nama</th>
                            <th class="px-4 py-3.5 font-semibold text-slate-500 hidden md:table-cell">Kategori</th>
                            <th class="px-4 py-3.5 font-semibold text-slate-500 hidden lg:table-cell">Deskripsi</th>
                            <th class="px-4 py-3.5 font-semibold text-slate-500 text-center hidden lg:table-cell">Fitur</th>
                            <th class="px-4 py-3.5 font-semibold text-slate-500 text-center">Urutan</th>
                            <th class="px-4 py-3.5 font-semibold text-slate-500">Status</th>
                            <th class="px-4 py-3.5 font-semibold text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($fasilitas as $i => $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-slate-400">{{ $fasilitas->firstItem() + $i }}</td>
                            <td class="px-4 py-3">
                                @if($item->gambarUrl())
                                    <img src="{{ $item->gambarUrl() }}" alt="{{ $item->nama }}"
                                         class="w-16 h-10 object-cover rounded-lg border border-slate-100">
                                @else
                                    <div class="w-16 h-10 flex items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-700">{{ $item->nama }}</td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-teal-50 text-teal-700">
                                    {{ $item->kategori }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-500 hidden lg:table-cell max-w-xs truncate">
                                {{ $item->deskripsi ?: '-' }}
                            </td>
                            <td class="px-4 py-3 text-center text-slate-600 hidden lg:table-cell">
                                {{ count($item->fiturList()) }}
                            </td>
                            <td class="px-4 py-3 text-center text-slate-600">{{ $item->urutan }}</td>
                            <td class="px-4 py-3">
                                @if($item->aktif)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-slate-100 text-slate-500">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <button type="button"
                                            data-id="{{ $item->id }}"
                                            data-fasilitas="{{ json_encode($item->only(['nama','deskripsi','kategori','urutan','aktif','gambar']), JSON_HEX_QUOT | JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) }}"
                                            data-fitur="{{ json_encode($item->fiturList(), JSON_UNESCAPED_UNICODE) }}"
                                            data-gambar-url="{{ $item->gambarUrl() ?? '' }}"
                                            data-has-gambar="{{ $item->gambar ? '1' : '0' }}"
                                            onclick="openEditModal(this.dataset.id, JSON.parse(this.dataset.fasilitas), JSON.parse(this.dataset.fitur), this.dataset.gambarUrl, this.dataset.hasGambar)"
                                            class="text-xs px-3 py-1.5 rounded-lg font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.fasilitas.toggle-aktif', $item) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="text-xs px-3 py-1.5 rounded-lg font-semibold text-white {{ $item->aktif ? 'bg-slate-600 hover:bg-slate-700' : 'bg-emerald-600 hover:bg-emerald-700' }} transition">
                                            {{ $item->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    <button type="button"
                                            onclick="confirmDeleteModal('{{ route('admin.fasilitas.destroy', $item) }}', '{{ addslashes($item->nama) }}')"
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
            <x-per-page :paginator="$fasilitas" />
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
            <h2 class="text-base font-extrabold text-slate-800">Tambah Fasilitas</h2>
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
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
        @endif
        <form method="POST" action="{{ route('admin.fasilitas.store') }}"
              enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="_form" value="create">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Fasilitas <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}"
                           placeholder="Nama fasilitas"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                    <select name="kategori"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Kategori —</option>
                        @foreach(App\Models\Fasilitas::$kategori as $val => $label)
                            <option value="{{ $val }}" {{ old('kategori') === $val ? 'selected' : '' }}>{{ $label }}</option>
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
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Urutan</label>
                    <input type="number" name="urutan" value="{{ old('urutan', 0) }}" min="0"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Deskripsi</label>
                <textarea name="deskripsi" rows="2" placeholder="Deskripsi singkat fasilitas..."
                          class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">{{ old('deskripsi') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Fitur / Keunggulan</label>
                <textarea name="fitur" rows="4" placeholder="Tulis satu fitur per baris, contoh:&#10;Ruang ber-AC&#10;40 unit PC&#10;Internet 100 Mbps"
                          class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">{{ old('fitur') }}</textarea>
                <p class="text-xs text-slate-400 mt-1">Tulis satu fitur per baris.</p>
            </div>
            {{-- Upload Gambar --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Gambar</label>
                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-4 text-center hover:border-teal-300 transition cursor-pointer"
                     onclick="document.getElementById('create-gambar-input').click()">
                    <div id="create-preview-wrap" class="hidden mb-3">
                        <img id="create-preview-img" src="" alt="Preview"
                             class="mx-auto max-h-40 rounded-xl object-cover shadow">
                    </div>
                    <div id="create-upload-hint">
                        <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm text-slate-400">Klik untuk pilih gambar</p>
                        <p class="text-xs text-slate-300 mt-1">JPG, PNG, WEBP — Maks. 2MB</p>
                    </div>
                </div>
                <input id="create-gambar-input" type="file" name="gambar" accept="image/*" class="hidden"
                       onchange="previewImage(this, 'create-preview-img', 'create-preview-wrap', 'create-upload-hint')">
                <p class="text-xs text-slate-400 mt-1">Kosongkan jika tidak ingin menampilkan gambar.</p>
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
            <h2 class="text-base font-extrabold text-slate-800">Edit Fasilitas</h2>
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
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
        @endif
        <form id="form-edit" method="POST" action="" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="_form" value="edit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Fasilitas <span class="text-red-500">*</span></label>
                    <input type="text" id="edit-nama" name="nama"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                    <select id="edit-kategori" name="kategori"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Kategori —</option>
                        @foreach(App\Models\Fasilitas::$kategori as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
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
                <label class="block text-sm font-semibold text-slate-700 mb-2">Deskripsi</label>
                <textarea id="edit-deskripsi" name="deskripsi" rows="2"
                          class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300"></textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Fitur / Keunggulan</label>
                <textarea id="edit-fitur" name="fitur" rows="4"
                          placeholder="Tulis satu fitur per baris"
                          class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300"></textarea>
                <p class="text-xs text-slate-400 mt-1">Tulis satu fitur per baris.</p>
            </div>
            {{-- Edit Gambar --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Gambar</label>
                <div id="edit-current-gambar" class="hidden mb-3">
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <img id="edit-current-img" src="" alt="Gambar saat ini"
                             class="w-24 h-16 object-cover rounded-lg shadow-sm">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-slate-600 mb-1">Gambar saat ini</p>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" id="edit-hapus-gambar" name="hapus_gambar" value="1"
                                       class="rounded border-slate-300 text-red-500">
                                <span class="text-xs text-red-600 font-medium">Hapus gambar ini</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-4 text-center hover:border-teal-300 transition cursor-pointer"
                     onclick="document.getElementById('edit-gambar-input').click()">
                    <div id="edit-preview-wrap" class="hidden mb-3">
                        <img id="edit-preview-img" src="" alt="Preview"
                             class="mx-auto max-h-40 rounded-xl object-cover shadow">
                    </div>
                    <div id="edit-upload-hint">
                        <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm text-slate-400">Klik untuk ganti gambar</p>
                        <p class="text-xs text-slate-300 mt-1">JPG, PNG, WEBP — Maks. 2MB</p>
                    </div>
                </div>
                <input id="edit-gambar-input" type="file" name="gambar" accept="image/*" class="hidden"
                       onchange="previewImage(this, 'edit-preview-img', 'edit-preview-wrap', 'edit-upload-hint')">
                <p class="text-xs text-slate-400 mt-1">Kosongkan untuk mempertahankan gambar yang ada.</p>
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

@push('scripts')
<script>
(function () {
    const editRoutes = @json(collect($fasilitas->items())->mapWithKeys(fn($item) => [$item->id => route('admin.fasilitas.update', $item)]));

    window.previewImage = function (input, imgId, wrapId, hintId) {
        const file = input.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById(imgId).src = e.target.result;
            document.getElementById(wrapId).classList.remove('hidden');
            document.getElementById(hintId).classList.add('hidden');
        };
        reader.readAsDataURL(file);
    };

    window.openCreateModal = function () {
        document.getElementById('modal-create').classList.remove('hidden');
    };
    window.closeCreateModal = function () {
        document.getElementById('modal-create').classList.add('hidden');
    };

    window.openEditModal = function (id, data, fiturArr, gambarUrl, hasGambar) {
        const form = document.getElementById('form-edit');
        form.action = editRoutes[id] ?? form.action;

        // Reset file inputs & previews
        document.getElementById('edit-gambar-input').value = '';
        document.getElementById('edit-preview-wrap').classList.add('hidden');
        document.getElementById('edit-upload-hint').classList.remove('hidden');
        const hapusCheck = document.getElementById('edit-hapus-gambar');
        if (hapusCheck) hapusCheck.checked = false;

        if (data) {
            document.getElementById('edit-nama').value      = data.nama      ?? '';
            document.getElementById('edit-deskripsi').value = data.deskripsi ?? '';
            document.getElementById('edit-urutan').value    = data.urutan    ?? 0;

            const kategoriSel = document.getElementById('edit-kategori');
            if (kategoriSel) {
                Array.from(kategoriSel.options).forEach(opt => {
                    opt.selected = opt.value === (data.kategori ?? '');
                });
            }

            const aktifSel = document.getElementById('edit-aktif');
            if (aktifSel) {
                aktifSel.value = data.aktif ? '1' : '0';
            }
        }

        // Populate fitur textarea
        const fiturTA = document.getElementById('edit-fitur');
        if (fiturTA) {
            fiturTA.value = Array.isArray(fiturArr) ? fiturArr.join('\n') : '';
        }

        // Gambar saat ini
        const currentGambarWrap = document.getElementById('edit-current-gambar');
        const currentImg        = document.getElementById('edit-current-img');
        if (hasGambar === '1' && gambarUrl) {
            currentImg.src = gambarUrl;
            currentGambarWrap.classList.remove('hidden');
        } else {
            currentGambarWrap.classList.add('hidden');
        }

        document.getElementById('modal-edit').classList.remove('hidden');
    };

    window.closeEditModal = function () {
        document.getElementById('modal-edit').classList.add('hidden');
    };

    // Auto-open edit modal on validation error
    @if($errors->any() && old('_form') === 'create')
        openCreateModal();
    @elseif($errors->any() && old('_form') === 'edit')
        // Re-open edit modal when validation fails
        (function () {
            const id       = '{{ old('edit_id') }}';
            const dataRaw  = @json(old());
            const fiturRaw = (dataRaw.fitur ?? '').split('\n').map(s => s.trim()).filter(Boolean);
            if (id && editRoutes[id]) {
                const form = document.getElementById('form-edit');
                form.action = editRoutes[id];
                document.getElementById('edit-nama').value      = dataRaw.nama      ?? '';
                document.getElementById('edit-deskripsi').value = dataRaw.deskripsi ?? '';
                document.getElementById('edit-urutan').value    = dataRaw.urutan    ?? 0;
                document.getElementById('edit-fitur').value     = dataRaw.fitur     ?? '';
                const kategoriSel = document.getElementById('edit-kategori');
                if (kategoriSel) {
                    Array.from(kategoriSel.options).forEach(opt => {
                        opt.selected = opt.value === (dataRaw.kategori ?? '');
                    });
                }
                const aktifSel = document.getElementById('edit-aktif');
                if (aktifSel) aktifSel.value = dataRaw.aktif ?? '1';
                document.getElementById('modal-edit').classList.remove('hidden');
            }
        })();
    @endif
})();
</script>
@endpush

@endsection
