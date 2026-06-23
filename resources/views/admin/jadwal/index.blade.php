@extends('admin.layouts.app')

@section('title', 'Jadwal Pelajaran')
@section('breadcrumb', 'Jadwal')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-800">Jadwal Pelajaran</h1>
            <p class="text-sm text-slate-500">Total: <strong>{{ $jadwal->count() }}</strong> sesi terdaftar</p>
        </div>
        <button type="button" onclick="openCreateModal()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
                style="background: var(--teal);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Jadwal
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
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Kelas</label>
                <select name="kelas_id"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Hari</label>
                <select name="hari"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">Semua Hari</option>
                    @foreach(App\Models\Jadwal::$hari as $val => $label)
                        <option value="{{ $val }}" {{ request('hari') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                <select name="aktif"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('aktif') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('aktif') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="flex-1 inline-flex justify-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 transition">
                    Terapkan
                </button>
                <a href="{{ route('admin.jadwal.index') }}"
                   class="flex-1 inline-flex justify-center px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel per hari --}}
    @if($jadwal->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
        <div class="text-6xl mb-4">🗓️</div>
        <h3 class="font-bold text-slate-600 mb-1">Belum ada jadwal</h3>
        <p class="text-sm text-slate-400">Tambahkan jadwal baru dengan tombol di atas.</p>
    </div>
    @else
        @foreach(App\Models\Jadwal::$hari as $hari => $_)
            @if(isset($byHari[$hari]) && $byHari[$hari]->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 flex items-center gap-2"
                     style="background: var(--teal-light);">
                    <span class="text-base font-extrabold" style="color: var(--teal);">{{ $hari }}</span>
                    <span class="text-xs font-semibold text-slate-500">({{ $byHari[$hari]->count() }} sesi)</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 text-left">
                                <th class="px-5 py-3 font-semibold text-slate-500">Jam</th>
                                <th class="px-5 py-3 font-semibold text-slate-500">Mata Pelajaran</th>
                                <th class="px-5 py-3 font-semibold text-slate-500">Kelas</th>
                                <th class="px-5 py-3 font-semibold text-slate-500">Guru</th>
                                <th class="px-5 py-3 font-semibold text-slate-500">Ruangan</th>
                                <th class="px-5 py-3 font-semibold text-slate-500">Status</th>
                                <th class="px-5 py-3 font-semibold text-slate-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($byHari[$hari] as $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-5 py-3 font-mono text-slate-700 text-xs whitespace-nowrap">
                                    {{ $item->jamLabel() }}
                                </td>
                                <td class="px-5 py-3 font-semibold text-slate-700">
                                    {{ $item->mataPelajaran->nama }}
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-teal-50 text-teal-700">
                                        {{ $item->kelas->nama }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-slate-600">{{ $item->guru?->name ?? '-' }}</td>
                                <td class="px-5 py-3 text-slate-500">{{ $item->ruangan ?? '-' }}</td>
                                <td class="px-5 py-3">
                                    @if($item->aktif)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-slate-100 text-slate-500">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button"
                                                data-id="{{ $item->id }}"
                                                data-jadwal='{!! json_encode($item->only(["kelas_id","mata_pelajaran_id","guru_id","hari","jam_mulai","jam_selesai","ruangan","aktif"]), JSON_UNESCAPED_UNICODE) !!}'
                                                onclick="openEditModal(this.dataset.id, JSON.parse(this.dataset.jadwal))"
                                                class="text-xs px-3 py-1.5 rounded-lg font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                                            Edit
                                        </button>
                                        <button type="button"
                                                onclick="confirmDelete('{{ route('admin.jadwal.destroy', $item) }}', 'Hapus jadwal {{ addslashes($item->mataPelajaran->nama) }} – {{ $item->hari }}?')"
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
            </div>
            @endif
        @endforeach
    @endif

</div>

{{-- ═══════════════════════════ MODAL TAMBAH ═══════════════════════════ --}}
<div id="modal-create"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden"
     onclick="if(event.target===this) closeCreateModal()">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h2 class="text-base font-extrabold text-slate-800">Tambah Jadwal</h2>
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

        <form method="POST" action="{{ route('admin.jadwal.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="_form" value="create">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Kelas</label>
                    <select name="kelas_id" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Kelas —</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ old('kelas_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Mata Pelajaran</label>
                    <select name="mata_pelajaran_id" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Mapel —</option>
                        @foreach($mapelList as $mapel)
                            <option value="{{ $mapel->id }}" {{ old('mata_pelajaran_id') == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Guru</label>
                    <select name="guru_id" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Guru (opsional) —</option>
                        @foreach($guruList as $guru)
                            <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>{{ $guru->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Hari</label>
                    <select name="hari" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Hari —</option>
                        @foreach(App\Models\Jadwal::$hari as $val => $label)
                            <option value="{{ $val }}" {{ old('hari') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Jam Mulai</label>
                    <input type="time" name="jam_mulai" value="{{ old('jam_mulai') }}"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Jam Selesai</label>
                    <input type="time" name="jam_selesai" value="{{ old('jam_selesai') }}"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Ruangan</label>
                    <input type="text" name="ruangan" value="{{ old('ruangan') }}" placeholder="Contoh: R.101"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                    <select name="aktif" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="1" {{ old('aktif', '1') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('aktif') === '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
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

{{-- ═══════════════════════════ MODAL EDIT ═══════════════════════════ --}}
<div id="modal-edit"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden"
     onclick="if(event.target===this) closeEditModal()">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h2 class="text-base font-extrabold text-slate-800">Edit Jadwal</h2>
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

        <form id="form-edit" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="_form" value="edit">
            <input type="hidden" name="edit_id" value="{{ old('edit_id') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Kelas</label>
                    <select id="edit-kelas_id" name="kelas_id" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Kelas —</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}">{{ $kelas->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Mata Pelajaran</label>
                    <select id="edit-mata_pelajaran_id" name="mata_pelajaran_id" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Mapel —</option>
                        @foreach($mapelList as $mapel)
                            <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Guru</label>
                    <select id="edit-guru_id" name="guru_id" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Guru (opsional) —</option>
                        @foreach($guruList as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Hari</label>
                    <select id="edit-hari" name="hari" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Hari —</option>
                        @foreach(App\Models\Jadwal::$hari as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Jam Mulai</label>
                    <input type="time" id="edit-jam_mulai" name="jam_mulai"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Jam Selesai</label>
                    <input type="time" id="edit-jam_selesai" name="jam_selesai"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Ruangan</label>
                    <input type="text" id="edit-ruangan" name="ruangan" placeholder="Contoh: R.101"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                    <select id="edit-aktif" name="aktif" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
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
const editJadwalRoutes = @json($jadwal->mapWithKeys(fn($item) => [$item->id => route('admin.jadwal.update', $item)]));

function openCreateModal() {
    document.getElementById('modal-create').classList.remove('hidden');
}
function closeCreateModal() {
    document.getElementById('modal-create').classList.add('hidden');
}
function openEditModal(id, data) {
    const form = document.getElementById('form-edit');
    form.action = editJadwalRoutes[id] ?? '';
    form.querySelector('input[name="edit_id"]').value = id;
    if (data) {
        document.getElementById('edit-kelas_id').value          = data.kelas_id ?? '';
        document.getElementById('edit-mata_pelajaran_id').value = data.mata_pelajaran_id ?? '';
        document.getElementById('edit-guru_id').value           = data.guru_id ?? '';
        document.getElementById('edit-hari').value              = data.hari ?? '';
        // Time values: trim seconds if present
        document.getElementById('edit-jam_mulai').value   = (data.jam_mulai   ?? '').substring(0, 5);
        document.getElementById('edit-jam_selesai').value = (data.jam_selesai ?? '').substring(0, 5);
        document.getElementById('edit-ruangan').value     = data.ruangan ?? '';
        document.getElementById('edit-aktif').value       = data.aktif ? '1' : '0';
    }
    document.getElementById('modal-edit').classList.remove('hidden');
}
function closeEditModal() {
    document.getElementById('modal-edit').classList.add('hidden');
}
function confirmDelete(url, message) {
    if (!confirm(message)) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    form.innerHTML = `@csrf @method('DELETE')`;
    document.body.appendChild(form);
    form.submit();
}

@if(old('_form') === 'create')
    document.addEventListener('DOMContentLoaded', openCreateModal);
@endif

@php
    $editItem = null;
    if (old('_form') === 'edit' && old('edit_id')) {
        $editItem = [
            'kelas_id'          => old('kelas_id'),
            'mata_pelajaran_id' => old('mata_pelajaran_id'),
            'guru_id'           => old('guru_id'),
            'hari'              => old('hari'),
            'jam_mulai'         => old('jam_mulai'),
            'jam_selesai'       => old('jam_selesai'),
            'ruangan'           => old('ruangan'),
            'aktif'             => old('aktif', '1') === '1',
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
