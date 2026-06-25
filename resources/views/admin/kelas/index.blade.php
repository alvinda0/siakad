@extends('admin.layouts.app')

@section('title', 'Daftar Kelas')
@section('breadcrumb', 'Kelas')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<style>
    /* ── Choices.js overrides untuk modal kelas ── */
    .choices { margin-bottom: 0; }
    .choices__inner {
        background: #fff !important;
        border: 1px solid #CBD5E1 !important;
        border-radius: .75rem !important;
        padding: .5rem .75rem !important;
        font-size: .875rem !important;
        min-height: unset !important;
    }
    .choices.is-focused .choices__inner,
    .choices.is-open    .choices__inner {
        border-color: #1B7A8A !important;
        box-shadow: 0 0 0 3px rgba(27,122,138,.12) !important;
    }
    .choices__list--dropdown,
    .choices__list[aria-expanded] {
        border: 1px solid #CBD5E1 !important;
        border-radius: .5rem !important;
        z-index: 9999 !important;
        font-size: .875rem !important;
    }
    .choices__list--dropdown .choices__item--selectable.is-highlighted {
        background: #E0F4F7 !important;
        color: #145F6E !important;
    }
    .choices__list--single { padding: 0 !important; }
    .choices__placeholder { color: #94A3B8 !important; opacity: 1 !important; }
    .choices[data-type*=select-one] .choices__button { display: none; }
    .choices__input {
        font-size: .875rem !important;
        background: transparent !important;
        margin-bottom: 0 !important;
    }
    .choices__list--dropdown .choices__input {
        border-bottom: 1px solid #E2E8F0 !important;
        padding: .4rem .75rem !important;
    }
</style>
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-extrabold text-slate-800">Daftar Kelas</h1>
            <p class="text-sm text-slate-500">Total: <strong>{{ $kelas->total() }}</strong> kelas</p>
        </div>
        <button type="button" onclick="openCreateModal()"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
           style="background: var(--teal);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kelas
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

    {{-- Filter Tahun Ajaran --}}
    @if($tahunList->isNotEmpty())
    <form method="GET" class="flex items-center gap-2">
        @if(request('per_page'))
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
        @endif
        <label class="text-sm font-medium text-slate-600">Tahun Ajaran:</label>
        <select name="tahun" onchange="this.form.submit()"
                class="text-sm border border-slate-200 rounded-lg px-3 py-1.5 bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
            @foreach($tahunList as $t)
                <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>
                    {{ $t }}/{{ $t + 1 }}
                </option>
            @endforeach
        </select>
    </form>
    @endif

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if($kelas->isEmpty())
            <div class="p-12 text-center">
                <div class="text-6xl mb-4">🏫</div>
                <h3 class="font-bold text-slate-600 mb-1">Belum ada data kelas</h3>
                <p class="text-sm text-slate-400">Tambahkan kelas pertama dengan klik tombol di atas.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left">
                            <th class="px-5 py-3.5 font-semibold text-slate-500 w-10">#</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Nama Kelas</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Tingkat</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Jurusan</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Tahun Ajaran</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Kapasitas</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Wali Kelas</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($kelas as $i => $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3.5 text-slate-400">{{ $kelas->firstItem() + $i }}</td>
                            <td class="px-5 py-3.5 font-semibold text-slate-700">{{ $item->nama }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700">
                                    {{ $item->tingkat }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-teal-50 text-teal-700">
                                    {{ $item->jurusan }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $item->tahunAjaranLabel() }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $item->kapasitas }} siswa</td>
                            <td class="px-5 py-3.5 text-slate-500">
                                {{ $item->waliKelas?->name ?? $item->wali_kelas ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.kelas.show', $item) }}" data-spa
                                       class="text-xs px-3 py-1.5 rounded-lg font-semibold text-white transition hover:brightness-110"
                                       style="background: var(--teal);">
                                        Detail
                                    </a>
                                    <button type="button"
                                            data-id="{{ $item->id }}"
                                            data-kelas="{{ json_encode($item->only(['nama','tingkat','jurusan','tahun_ajaran','kapasitas','wali_kelas_id','keterangan'])) }}"
                                            onclick="openEditModal(this.dataset.id, JSON.parse(this.dataset.kelas))"
                                            class="text-xs px-3 py-1.5 rounded-lg font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                                        Edit
                                    </button>
                                    <button type="button"
                                            onclick="confirmDelete('{{ route('admin.kelas.destroy', $item) }}', 'Hapus kelas {{ addslashes($item->nama) }}?')"
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

            <x-per-page :paginator="$kelas" />
        @endif
    </div>

</div>

{{-- ═══════════════════════════════════════
     MODAL TAMBAH KELAS
═══════════════════════════════════════ --}}
<div id="modal-create"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden"
     onclick="if(event.target===this) closeCreateModal()">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl mx-4 max-h-[90vh] overflow-y-auto">

        {{-- Modal header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h2 class="text-base font-extrabold text-slate-800">Tambah Kelas</h2>
            <button type="button" onclick="closeCreateModal()"
                    class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Validation errors (create) --}}
        @if($errors->any() && old('_form') === 'create')
        <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.kelas.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="_form" value="create">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Nama Kelas --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Nama Kelas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama"
                           value="{{ old('_form') === 'create' ? old('nama') : '' }}"
                           placeholder="Contoh: X TKJ 1"
                           class="w-full px-3 py-2.5 rounded-xl border text-sm
                                  {{ $errors->has('nama') && old('_form') === 'create' ? 'border-red-400 bg-red-50' : 'border-slate-200' }}
                                  focus:outline-none focus:ring-2 focus:ring-teal-300">
                    @if($errors->has('nama') && old('_form') === 'create')
                        <p class="mt-1 text-xs text-red-500">{{ $errors->first('nama') }}</p>
                    @endif
                </div>

                {{-- Tingkat --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Tingkat <span class="text-red-500">*</span>
                    </label>
                    <select name="tingkat"
                            class="w-full px-3 py-2.5 rounded-xl border text-sm
                                   {{ $errors->has('tingkat') && old('_form') === 'create' ? 'border-red-400 bg-red-50' : 'border-slate-200' }}
                                   focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Tingkat —</option>
                        @foreach(\App\Models\Kelas::$tingkat as $val => $label)
                            <option value="{{ $val }}" {{ old('_form') === 'create' && old('tingkat') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @if($errors->has('tingkat') && old('_form') === 'create')
                        <p class="mt-1 text-xs text-red-500">{{ $errors->first('tingkat') }}</p>
                    @endif
                </div>

                {{-- Jurusan --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Jurusan <span class="text-red-500">*</span>
                    </label>
                    <select name="jurusan"
                            class="w-full px-3 py-2.5 rounded-xl border text-sm
                                   {{ $errors->has('jurusan') && old('_form') === 'create' ? 'border-red-400 bg-red-50' : 'border-slate-200' }}
                                   focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Jurusan —</option>
                        @foreach(\App\Models\Kelas::$jurusan as $val => $label)
                            <option value="{{ $val }}" {{ old('_form') === 'create' && old('jurusan') === $val ? 'selected' : '' }}>
                                {{ $val }} — {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @if($errors->has('jurusan') && old('_form') === 'create')
                        <p class="mt-1 text-xs text-red-500">{{ $errors->first('jurusan') }}</p>
                    @endif
                </div>

                {{-- Tahun Ajaran --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Tahun Ajaran <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="tahun_ajaran"
                           value="{{ old('_form') === 'create' ? old('tahun_ajaran', date('Y')) : date('Y') }}"
                           min="2000" max="2100"
                           class="w-full px-3 py-2.5 rounded-xl border text-sm
                                  {{ $errors->has('tahun_ajaran') && old('_form') === 'create' ? 'border-red-400 bg-red-50' : 'border-slate-200' }}
                                  focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <p class="mt-1 text-xs text-slate-400">Tahun mulai, misal 2026 untuk TA 2026/2027</p>
                </div>

                {{-- Kapasitas --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Kapasitas <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="kapasitas"
                           value="{{ old('_form') === 'create' ? old('kapasitas', 32) : 32 }}"
                           min="1" max="100"
                           class="w-full px-3 py-2.5 rounded-xl border text-sm
                                  {{ $errors->has('kapasitas') && old('_form') === 'create' ? 'border-red-400 bg-red-50' : 'border-slate-200' }}
                                  focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>

                {{-- Wali Kelas --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Wali Kelas</label>
                    <select id="create-wali_kelas_id" name="wali_kelas_id"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Tidak ada —</option>
                        @foreach($guruList as $guru)
                            <option value="{{ $guru->id }}"
                                {{ (old('_form') === 'create' && old('wali_kelas_id') == $guru->id) ? 'selected' : '' }}>
                                {{ $guru->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Keterangan --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Keterangan</label>
                    <textarea name="keterangan" rows="2"
                              placeholder="Keterangan tambahan (opsional)"
                              class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm resize-none
                                     focus:outline-none focus:ring-2 focus:ring-teal-300">{{ old('_form') === 'create' ? old('keterangan') : '' }}</textarea>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-1">
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
                        style="background: var(--teal);">
                    Simpan Kelas
                </button>
                <button type="button" onclick="closeCreateModal()"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════
     MODAL EDIT KELAS
═══════════════════════════════════════ --}}
<div id="modal-edit"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden"
     onclick="if(event.target===this) closeEditModal()">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl mx-4 max-h-[90vh] overflow-y-auto">

        {{-- Modal header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h2 class="text-base font-extrabold text-slate-800">Edit Kelas</h2>
            <button type="button" onclick="closeEditModal()"
                    class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Validation errors (edit) --}}
        @if($errors->any() && old('_form') === 'edit')
        <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Form (action updated by JS) --}}
        <form id="form-edit" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="_form" value="edit">
            <input type="hidden" id="edit-id-field" name="_edit_id" value="">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Nama Kelas --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Nama Kelas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="edit-nama" name="nama"
                           placeholder="Contoh: X TKJ 1"
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-teal-300
                                  {{ $errors->has('nama') && old('_form') === 'edit' ? '!border-red-400 bg-red-50' : '' }}">
                    @if($errors->has('nama') && old('_form') === 'edit')
                        <p class="mt-1 text-xs text-red-500">{{ $errors->first('nama') }}</p>
                    @endif
                </div>

                {{-- Tingkat --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Tingkat <span class="text-red-500">*</span>
                    </label>
                    <select id="edit-tingkat" name="tingkat"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Tingkat —</option>
                        @foreach(\App\Models\Kelas::$tingkat as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Jurusan --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Jurusan <span class="text-red-500">*</span>
                    </label>
                    <select id="edit-jurusan" name="jurusan"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Jurusan —</option>
                        @foreach(\App\Models\Kelas::$jurusan as $val => $label)
                            <option value="{{ $val }}">{{ $val }} — {{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tahun Ajaran --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Tahun Ajaran <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="edit-tahun_ajaran" name="tahun_ajaran"
                           min="2000" max="2100"
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <p class="mt-1 text-xs text-slate-400">Tahun mulai, misal 2026 untuk TA 2026/2027</p>
                </div>

                {{-- Kapasitas --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Kapasitas <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="edit-kapasitas" name="kapasitas"
                           min="1" max="100"
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>

                {{-- Wali Kelas --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Wali Kelas</label>
                    <select id="edit-wali_kelas_id" name="wali_kelas_id"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Tidak ada —</option>
                        @foreach($guruList as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Keterangan --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Keterangan</label>
                    <textarea id="edit-keterangan" name="keterangan" rows="2"
                              placeholder="Keterangan tambahan (opsional)"
                              class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm resize-none
                                     focus:outline-none focus:ring-2 focus:ring-teal-300"></textarea>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-1">
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
                        style="background: var(--teal);">
                    Perbarui Kelas
                </button>
                <button type="button" onclick="closeEditModal()"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
/* ── Choices.js instances ── */
const choicesConfig = {
    searchEnabled: true,
    searchPlaceholderValue: 'Cari guru...',
    itemSelectText: '',
    noResultsText: 'Guru tidak ditemukan',
    shouldSort: false,
    allowHTML: false,
};

let choicesCreateWali = null;
let choicesEditWali   = null;

document.addEventListener('DOMContentLoaded', function () {
    const elCreate = document.getElementById('create-wali_kelas_id');
    const elEdit   = document.getElementById('edit-wali_kelas_id');
    if (elCreate) choicesCreateWali = new Choices(elCreate, choicesConfig);
    if (elEdit)   choicesEditWali   = new Choices(elEdit,   choicesConfig);
});

/* ── Route base for edit action ── */
const kelasRouteBase = "{{ url('admin/kelas') }}";

/* ── Create Modal ── */
function openCreateModal() {
    document.getElementById('modal-create').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeCreateModal() {
    document.getElementById('modal-create').classList.add('hidden');
    document.body.style.overflow = '';
}

/* ── Edit Modal ── */
function openEditModal(id, data) {
    // Set form action
    document.getElementById('form-edit').action = kelasRouteBase + '/' + id;
    document.getElementById('edit-id-field').value = id;

    // Populate fields
    document.getElementById('edit-nama').value         = data.nama         ?? '';
    document.getElementById('edit-tahun_ajaran').value = data.tahun_ajaran ?? '';
    document.getElementById('edit-kapasitas').value    = data.kapasitas    ?? '';
    document.getElementById('edit-keterangan').value   = data.keterangan   ?? '';

    // Select tingkat
    document.getElementById('edit-tingkat').value = data.tingkat ?? '';

    // Select jurusan
    document.getElementById('edit-jurusan').value = data.jurusan ?? '';

    // Select wali kelas via Choices instance
    if (choicesEditWali) {
        choicesEditWali.setChoiceByValue(data.wali_kelas_id ? String(data.wali_kelas_id) : '');
    }

    document.getElementById('modal-edit').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeEditModal() {
    document.getElementById('modal-edit').classList.add('hidden');
    document.body.style.overflow = '';
}

/* ── Re-open modals on validation error ── */
@if($errors->any() && old('_form') === 'create')
    openCreateModal();
@endif
@if($errors->any() && old('_form') === 'edit')
    // Restore edit form values from old() via hidden inputs
    document.getElementById('edit-nama').value         = @json(old('nama', ''));
    document.getElementById('edit-tingkat').value      = @json(old('tingkat', ''));
    document.getElementById('edit-jurusan').value      = @json(old('jurusan', ''));
    document.getElementById('edit-tahun_ajaran').value = @json(old('tahun_ajaran', ''));
    document.getElementById('edit-kapasitas').value    = @json(old('kapasitas', ''));
    document.getElementById('edit-wali_kelas_id').value = @json(old('wali_kelas_id', ''));
    if (choicesEditWali) choicesEditWali.setChoiceByValue(@json(old('wali_kelas_id', '')) + '');
    document.getElementById('edit-keterangan').value   = @json(old('keterangan', ''));
    // Restore action from hidden field
    const editId = @json(old('_edit_id', ''));
    if (editId) {
        // Jangan panggil openEditModal(editId, {}) karena akan menimpa nilai yang sudah diisi
        document.getElementById('form-edit').action = kelasRouteBase + '/' + editId;
        document.getElementById('modal-edit').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
@endif

/* ── Delete confirm ── */
function confirmDelete(url, message) {
    if (confirm(message + '\nTindakan ini tidak dapat dibatalkan.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

/* ── ESC key closes modals ── */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeCreateModal();
        closeEditModal();
    }
});
</script>

@endsection
