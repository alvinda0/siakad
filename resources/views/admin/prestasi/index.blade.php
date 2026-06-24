@extends('admin.layouts.app')

@section('title', 'Prestasi')
@section('breadcrumb', 'Prestasi')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-800">Prestasi</h1>
            <p class="text-sm text-slate-500">Total: <strong>{{ $prestasi->total() }}</strong> prestasi</p>
        </div>
        <button type="button" onclick="openCreateModal()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
                style="background: var(--teal);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Prestasi
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
            <div class="flex-1 min-w-36">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tingkat</label>
                <select name="tingkat"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">Semua Tingkat</option>
                    @foreach(App\Models\Prestasi::$tingkat as $val => $label)
                        <option value="{{ $val }}" {{ request('tingkat') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-32">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tahun</label>
                <select name="tahun"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunList as $thn)
                        <option value="{{ $thn }}" {{ request('tahun') === $thn ? 'selected' : '' }}>{{ $thn }}</option>
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
                       placeholder="Cari judul atau nama peraih"
                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 transition">
                    Terapkan
                </button>
                <a href="{{ route('admin.prestasi.index') }}"
                   class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if($prestasi->isEmpty())
            <div class="p-12 text-center">
                <div class="text-6xl mb-4">🏆</div>
                <h3 class="font-bold text-slate-600 mb-1">Belum ada data prestasi</h3>
                <p class="text-sm text-slate-400">Tambahkan prestasi baru dengan tombol di atas.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left">
                            <th class="px-4 py-3.5 font-semibold text-slate-500 w-10">#</th>
                            <th class="px-4 py-3.5 font-semibold text-slate-500 w-12">Medali</th>
                            <th class="px-4 py-3.5 font-semibold text-slate-500">Judul</th>
                            <th class="px-4 py-3.5 font-semibold text-slate-500">Nama Peraih</th>
                            <th class="px-4 py-3.5 font-semibold text-slate-500">Tingkat</th>
                            <th class="px-4 py-3.5 font-semibold text-slate-500">Tahun</th>
                            <th class="px-4 py-3.5 font-semibold text-slate-500 hidden lg:table-cell">Deskripsi</th>
                            <th class="px-4 py-3.5 font-semibold text-slate-500 text-center">Urutan</th>
                            <th class="px-4 py-3.5 font-semibold text-slate-500">Status</th>
                            <th class="px-4 py-3.5 font-semibold text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($prestasi as $i => $item)
                        @php $color = $item->tingkatColor(); @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-slate-400">{{ $prestasi->firstItem() + $i }}</td>
                            <td class="px-4 py-3 text-2xl text-center">{{ $item->medali }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-700 max-w-xs">{{ $item->judul }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $item->nama_peraih }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold"
                                      style="color:{{ $color['text'] }}; background:{{ $color['bg'] }};">
                                    {{ $item->tingkat }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-500 whitespace-nowrap">{{ $item->tahun }}</td>
                            <td class="px-4 py-3 text-slate-500 max-w-xs hidden lg:table-cell">
                                <span title="{{ $item->deskripsi }}">{{ Str::limit($item->deskripsi, 60) ?: '-' }}</span>
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
                                            data-prestasi="{{ json_encode($item->only(['judul','nama_peraih','tingkat','medali','tahun','deskripsi','urutan','aktif','gambar']), JSON_HEX_QUOT | JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) }}"
                                            data-gambar-url="{{ $item->gambarUrl() ?? '' }}"
                                            data-has-gambar="{{ $item->gambar ? '1' : '0' }}"
                                            onclick="openEditModal(this.dataset.id, JSON.parse(this.dataset.prestasi), this.dataset.gambarUrl, this.dataset.hasGambar)"
                                            class="text-xs px-3 py-1.5 rounded-lg font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.prestasi.toggle-aktif', $item) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="text-xs px-3 py-1.5 rounded-lg font-semibold text-white {{ $item->aktif ? 'bg-slate-600 hover:bg-slate-700' : 'bg-emerald-600 hover:bg-emerald-700' }} transition">
                                            {{ $item->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    <button type="button"
                                            onclick="confirmDeleteModal('{{ route('admin.prestasi.destroy', $item) }}', '{{ addslashes($item->judul) }}')"
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
            <x-per-page :paginator="$prestasi" />
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
            <h2 class="text-base font-extrabold text-slate-800">Tambah Prestasi</h2>
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
        <form method="POST" action="{{ route('admin.prestasi.store') }}"
              enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="_form" value="create">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Judul Prestasi <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" value="{{ old('judul') }}"
                           placeholder="Contoh: Juara 1 LKS Teknik Komputer"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Peraih <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_peraih" value="{{ old('nama_peraih') }}"
                           placeholder="Nama siswa, tim, atau institusi"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tahun <span class="text-red-500">*</span></label>
                    <input type="text" name="tahun" value="{{ old('tahun', date('Y')) }}"
                           placeholder="Contoh: 2025"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tingkat <span class="text-red-500">*</span></label>
                    <select name="tingkat"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Tingkat —</option>
                        @foreach(App\Models\Prestasi::$tingkat as $val => $label)
                            <option value="{{ $val }}" {{ old('tingkat') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Medali <span class="text-red-500">*</span></label>
                    <select name="medali"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        @foreach(App\Models\Prestasi::$medaliOptions as $val => $label)
                            <option value="{{ $val }}" {{ old('medali', '🏅') === $val ? 'selected' : '' }}>{{ $label }}</option>
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
                <textarea name="deskripsi" rows="3" placeholder="Keterangan tambahan (opsional)..."
                          class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">{{ old('deskripsi') }}</textarea>
            </div>

            {{-- Upload Gambar --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Gambar <span class="text-slate-400 font-normal">(opsional)</span></label>
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
            <h2 class="text-base font-extrabold text-slate-800">Edit Prestasi</h2>
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
            <input type="hidden" name="edit_id" value="{{ old('edit_id') }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Judul Prestasi <span class="text-red-500">*</span></label>
                    <input type="text" id="edit-judul" name="judul"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Peraih <span class="text-red-500">*</span></label>
                    <input type="text" id="edit-nama-peraih" name="nama_peraih"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tahun <span class="text-red-500">*</span></label>
                    <input type="text" id="edit-tahun" name="tahun"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tingkat <span class="text-red-500">*</span></label>
                    <select id="edit-tingkat" name="tingkat"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Tingkat —</option>
                        @foreach(App\Models\Prestasi::$tingkat as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Medali <span class="text-red-500">*</span></label>
                    <select id="edit-medali" name="medali"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        @foreach(App\Models\Prestasi::$medaliOptions as $val => $label)
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
                <textarea id="edit-deskripsi" name="deskripsi" rows="3"
                          class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300"></textarea>
            </div>

            {{-- Edit Gambar --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Gambar <span class="text-slate-400 font-normal">(opsional)</span></label>
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
    const editRoutes = @json(collect($prestasi->items())->mapWithKeys(fn($item) => [$item->id => route('admin.prestasi.update', $item)]));

    /* ── Preview gambar ── */
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

    window.openEditModal = function (id, data, gambarUrl, hasGambar) {
        const form = document.getElementById('form-edit');
        form.action = editRoutes[id] ?? form.action;
        form.querySelector('input[name="edit_id"]').value = id;

        // Reset file input & preview
        document.getElementById('edit-gambar-input').value = '';
        document.getElementById('edit-preview-wrap').classList.add('hidden');
        document.getElementById('edit-upload-hint').classList.remove('hidden');
        const hapusCheck = document.getElementById('edit-hapus-gambar');
        if (hapusCheck) hapusCheck.checked = false;

        if (data) {
            document.getElementById('edit-judul').value        = data.judul        ?? '';
            document.getElementById('edit-nama-peraih').value  = data.nama_peraih  ?? '';
            document.getElementById('edit-tingkat').value      = data.tingkat      ?? '';
            document.getElementById('edit-medali').value       = data.medali       ?? '🏅';
            document.getElementById('edit-tahun').value        = data.tahun        ?? '';
            document.getElementById('edit-aktif').value        = data.aktif ? '1' : '0';
            document.getElementById('edit-urutan').value       = data.urutan       ?? 0;
            document.getElementById('edit-deskripsi').value    = data.deskripsi    ?? '';
        }

        const currentWrap = document.getElementById('edit-current-gambar');
        const currentImg  = document.getElementById('edit-current-img');
        if (hasGambar === '1' && gambarUrl) {
            currentImg.src = gambarUrl;
            currentWrap.classList.remove('hidden');
        } else {
            currentWrap.classList.add('hidden');
        }

        document.getElementById('modal-edit').classList.remove('hidden');
    };

    window.closeEditModal = function () {
        document.getElementById('modal-edit').classList.add('hidden');
    };

    window.confirmDeleteModal = function (url, name) {
        if (!confirm('Hapus prestasi "' + name + '"?\nTindakan ini tidak dapat dibatalkan.')) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <input type="hidden" name="_method" value="DELETE">`;
        document.body.appendChild(form);
        form.submit();
    };

    // Re-open modals on validation error
    @if(old('_form') === 'create') openCreateModal(); @endif

    @php
        $editItem = null;
        if (old('_form') === 'edit' && old('edit_id')) {
            $editItem = [
                'judul'       => old('judul'),
                'nama_peraih' => old('nama_peraih'),
                'tingkat'     => old('tingkat'),
                'medali'      => old('medali', '🏅'),
                'tahun'       => old('tahun'),
                'aktif'       => old('aktif', '1') === '1',
                'urutan'      => old('urutan', 0),
                'deskripsi'   => old('deskripsi'),
                'gambar'      => null,
            ];
        }
    @endphp
    @if($editItem)
        openEditModal({{ (int) old('edit_id') }}, @json($editItem), '', '0');
    @endif
})();
</script>

@endsection
