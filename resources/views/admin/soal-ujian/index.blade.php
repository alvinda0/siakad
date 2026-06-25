@extends('admin.layouts.app')

@section('title', 'Manajemen Soal Ujian')
@section('breadcrumb', 'Soal Ujian')

@section('content')
<style>
    .soal-card { background:#fff;border-radius:14px;border:1px solid #e2e8f0;overflow:hidden;transition:box-shadow .13s; }
    .soal-card:hover { box-shadow:0 2px 10px rgba(0,0,0,.06); }
    .tipe-badge-pg  { background:#dbeafe;color:#1d4ed8; }
    .tipe-badge-essay { background:#ede9fe;color:#7c3aed; }
    .tipe-badge { display:inline-flex;align-items:center;padding:.2rem .6rem;border-radius:9999px;font-size:.68rem;font-weight:700; }
    .pilihan-row { display:flex;align-items:flex-start;gap:.6rem;padding:.5rem .75rem;border-radius:8px;border:1px solid #f1f5f9; }
    .pilihan-row.kunci { border-color:#bbf7d0;background:#f0fdf4; }
    .pilihan-huruf-sm { width:24px;height:24px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:800;background:#f1f5f9;color:#475569;flex-shrink:0; }
    .pilihan-row.kunci .pilihan-huruf-sm { background:#16a34a;color:#fff; }
    .stat-mini { background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:.75rem 1rem;display:flex;align-items:center;gap:.75rem; }
</style>
<div class="space-y-5">

{{-- ── Header ── --}}
<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('admin.jadwal-ujian.index') }}"
               class="text-xs text-slate-400 hover:text-teal-600 transition">← Jadwal Ujian</a>
        </div>
        <h1 class="text-xl font-extrabold text-slate-800">Soal: {{ $jadwalUjian->mataPelajaran->nama }}</h1>
        <p class="text-sm text-slate-500 mt-0.5">
            {{ $jadwalUjian->nama }} &middot; {{ $jadwalUjian->jenis }} &middot; {{ $jadwalUjian->kelas->nama }}
        </p>
    </div>
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('admin.soal-ujian.rekap', $jadwalUjian) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
            📊 Rekap Pengerjaan
        </a>
        <a href="{{ route('admin.jadwal-ujian.download-template-kunci', $jadwalUjian) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Template Kunci
        </a>
        @php $soalKunciNull = $soalList->where('tipe','pilihan_ganda')->whereNull('kunci_jawaban')->count(); @endphp
        @if($soalKunciNull > 0)
        <button type="button" onclick="openUploadKunciModal()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-amber-700 bg-amber-50 border border-amber-200 hover:bg-amber-100 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            Upload Kunci ({{ $soalKunciNull }} soal belum)
        </button>
        @else
        <button type="button" onclick="openUploadKunciModal()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            Upload Kunci
        </button>
        @endif
        <button type="button" onclick="openCreateModal()"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white shadow-sm transition hover:brightness-110"
                style="background: var(--teal);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Soal
        </button>
    </div>
</div>

{{-- ── Flash ── --}}
@if(session('success'))
<div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 rounded-xl">
    <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
@endif
@if($errors->any())
<div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
    <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

{{-- ── Stats ── --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="stat-mini"><span class="text-2xl">📝</span>
        <div><div class="text-xl font-extrabold text-slate-800">{{ $soalList->count() }}</div>
        <div class="text-xs text-slate-500">Total Soal</div></div></div>
    <div class="stat-mini"><span class="text-2xl">🅰️</span>
        <div><div class="text-xl font-extrabold text-slate-800">{{ $soalList->where('tipe','pilihan_ganda')->count() }}</div>
        <div class="text-xs text-slate-500">Pilihan Ganda</div></div></div>
    <div class="stat-mini"><span class="text-2xl">✏️</span>
        <div><div class="text-xl font-extrabold text-slate-800">{{ $soalList->where('tipe','essay')->count() }}</div>
        <div class="text-xs text-slate-500">Essay</div></div></div>
    @php $belumKunci = $soalList->where('tipe','pilihan_ganda')->whereNull('kunci_jawaban')->count(); @endphp
    <div class="stat-mini {{ $belumKunci > 0 ? 'border-red-200 bg-red-50' : '' }}">
        <span class="text-2xl">{{ $belumKunci > 0 ? '⚠️' : '✅' }}</span>
        <div>
            <div class="text-xl font-extrabold {{ $belumKunci > 0 ? 'text-red-600' : 'text-slate-800' }}">
                {{ $belumKunci > 0 ? $belumKunci.' soal' : $totalSelesai.'/'.$totalMurid }}
            </div>
            <div class="text-xs {{ $belumKunci > 0 ? 'text-red-500' : 'text-slate-500' }}">
                {{ $belumKunci > 0 ? 'Kunci Belum Diset' : 'Murid Selesai' }}
            </div>
        </div>
    </div>
</div>

{{-- ── Daftar soal ── --}}
@if($soalList->isEmpty())
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-16 text-center">
    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-4xl mx-auto mb-4">📝</div>
    <h3 class="font-bold text-slate-600 mb-1">Belum ada soal</h3>
    <p class="text-sm text-slate-400 mb-4">Tambahkan soal pilihan ganda atau essay.</p>
    <button type="button" onclick="openCreateModal()"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
            style="background: var(--teal);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Soal Pertama
    </button>
</div>
@else
<div class="space-y-3">
    @foreach($soalList->sortBy('nomor') as $soal)
    <div class="soal-card">
        <div class="px-5 py-4">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-3 flex-1 min-w-0">
                    <span class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center text-sm font-extrabold text-white"
                          style="background: {{ $soal->isPilihanGanda() ? 'var(--teal)' : '#7c3aed' }}">
                        {{ $soal->nomor }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="tipe-badge {{ $soal->isPilihanGanda() ? 'tipe-badge-pg' : 'tipe-badge-essay' }}">
                                {{ $soal->isPilihanGanda() ? 'Pilihan Ganda' : 'Essay' }}
                            </span>
                            <span class="text-xs text-slate-400">{{ $soal->poin }} poin</span>
                        </div>
                        <p class="text-sm text-slate-800 font-medium leading-relaxed">
                            {!! nl2br(e($soal->pertanyaan)) !!}
                        </p>
                        @if($soal->isPilihanGanda())
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                            @foreach($soal->pilihanArray() as $huruf => $teks)
                            <div class="pilihan-row {{ strtoupper($huruf) === strtoupper($soal->kunci_jawaban ?? '') ? 'kunci' : '' }}">
                                <span class="pilihan-huruf-sm">{{ $huruf }}</span>
                                <span class="text-xs text-slate-700 leading-relaxed">{{ $teks }}</span>
                                @if(strtoupper($huruf) === strtoupper($soal->kunci_jawaban ?? ''))
                                <span class="text-[.65rem] font-bold text-emerald-700 ml-auto shrink-0">✓ Kunci</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @if(! $soal->kunci_jawaban)
                        <div class="mt-2 flex flex-wrap items-center gap-1.5">
                            <span class="text-[.65rem] font-medium text-red-600 bg-red-50 border border-red-100 px-2 py-0.5 rounded-full">
                                ⚠ Kunci belum diset
                            </span>
                            @foreach(['A','B','C','D'] as $k)
                            @if(isset($soal->pilihanArray()[$k]))
                            <form method="POST" action="{{ route('admin.soal-ujian.update', [$jadwalUjian, $soal]) }}" class="inline">
                                @csrf @method('PUT')
                                <input type="hidden" name="tipe" value="{{ $soal->tipe }}">
                                <input type="hidden" name="nomor" value="{{ $soal->nomor }}">
                                <input type="hidden" name="pertanyaan" value="{{ $soal->pertanyaan }}">
                                <input type="hidden" name="pilihan_a" value="{{ $soal->pilihan_a }}">
                                <input type="hidden" name="pilihan_b" value="{{ $soal->pilihan_b }}">
                                <input type="hidden" name="pilihan_c" value="{{ $soal->pilihan_c }}">
                                <input type="hidden" name="pilihan_d" value="{{ $soal->pilihan_d }}">
                                <input type="hidden" name="kunci_jawaban" value="{{ $k }}">
                                <input type="hidden" name="poin" value="{{ $soal->poin }}">
                                <button type="submit"
                                        class="text-[.65rem] font-bold px-2 py-0.5 rounded-full bg-slate-100 hover:bg-emerald-100 hover:text-emerald-700 border border-slate-200 transition">
                                    Kunci {{ $k }}
                                </button>
                            </form>
                            @endif
                            @endforeach
                        </div>
                        @endif
                        @elseif($soal->isEssay() && $soal->kunci_jawaban_essay)
                        <div class="mt-3 bg-emerald-50 border border-emerald-100 rounded-lg px-3 py-2">
                            <p class="text-[.65rem] font-bold text-emerald-700 mb-0.5">💡 Kunci Essay (Auto-grade):</p>
                            <p class="text-xs text-emerald-800 leading-relaxed line-clamp-2">{{ $soal->kunci_jawaban_essay }}</p>
                        </div>
                        @elseif($soal->isEssay())
                        <div class="mt-3">
                            <span class="text-[.65rem] font-medium text-amber-600 bg-amber-50 border border-amber-100 px-2 py-0.5 rounded-full">
                                ⚠ Belum ada kunci — dinilai manual guru
                            </span>
                        </div>
                        @endif                    </div>
                </div>
                {{-- Aksi --}}
                <div class="flex flex-col gap-1.5 shrink-0">
                    <button type="button"
                            data-soal='{!! json_encode($soal->only(["id","tipe","nomor","pertanyaan","pilihan_a","pilihan_b","pilihan_c","pilihan_d","kunci_jawaban","kunci_jawaban_essay","poin"]), JSON_UNESCAPED_UNICODE) !!}'
                            onclick="openEditModal(JSON.parse(this.dataset.soal))"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                        ✏️ Edit
                    </button>
                    <form method="POST" action="{{ route('admin.soal-ujian.destroy', [$jadwalUjian, $soal]) }}"
                          onsubmit="return confirm('Hapus soal nomor {{ $soal->nomor }}?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="w-full inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 transition">
                            🗑 Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

</div>{{-- .space-y-5 --}}

{{-- ════════ MODAL TAMBAH SOAL ════════ --}}
<div id="modal-create" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden"
     onclick="if(event.target===this) closeCreateModal()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[92vh] overflow-y-auto">
        <div class="sticky top-0 bg-white flex items-center justify-between px-6 py-4 border-b border-slate-100 z-10">
            <h2 class="text-base font-extrabold text-slate-800">Tambah Soal</h2>
            <button type="button" onclick="closeCreateModal()" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.soal-ujian.store', $jadwalUjian) }}" class="px-6 py-5 space-y-4">
            @csrf
            @include('admin.soal-ujian._form', ['mode' => 'create', 'soal' => null])
            <div class="flex justify-end gap-3 pt-1 border-t border-slate-100">
                <button type="button" onclick="closeCreateModal()"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
                        style="background: var(--teal);">
                    Simpan Soal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════ MODAL EDIT SOAL ════════ --}}
<div id="modal-edit" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden"
     onclick="if(event.target===this) closeEditModal()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[92vh] overflow-y-auto">
        <div class="sticky top-0 bg-white flex items-center justify-between px-6 py-4 border-b border-slate-100 z-10">
            <h2 class="text-base font-extrabold text-slate-800">Edit Soal</h2>
            <button type="button" onclick="closeEditModal()" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" id="form-edit" action="" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')
            @include('admin.soal-ujian._form', ['mode' => 'edit', 'soal' => null])
            <div class="flex justify-end gap-3 pt-1 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
                        style="background: var(--teal);">
                    Perbarui Soal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════ MODAL UPLOAD KUNCI JAWABAN ════════ --}}
<div id="modal-upload-kunci" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden"
     onclick="if(event.target===this) closeUploadKunciModal()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div>
                <h2 class="text-base font-extrabold text-slate-800">Upload Kunci Jawaban</h2>
                <p class="text-xs text-slate-400 mt-0.5">{{ $jadwalUjian->mataPelajaran->nama }} — {{ $jadwalUjian->jenis }}</p>
            </div>
            <button type="button" onclick="closeUploadKunciModal()" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.jadwal-ujian.upload-kunci', $jadwalUjian) }}" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf
            {{-- Info format --}}
            <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 text-amber-800 text-xs px-4 py-3 rounded-xl">
                <svg class="w-4 h-4 shrink-0 mt-0.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <p class="font-semibold mb-1">Format file CSV/XLSX:</p>
                    <p>Kolom 1 = nomor soal, Kolom 2 = kunci jawaban (A/B/C/D untuk PG, teks untuk essay)</p>
                    <p class="mt-1">Contoh isi file: <code class="bg-amber-100 px-1 rounded font-mono">1,A</code> &nbsp; <code class="bg-amber-100 px-1 rounded font-mono">2,C</code> &nbsp; <code class="bg-amber-100 px-1 rounded font-mono">3,B</code></p>
                    <p class="mt-1 text-amber-600">Baris pertama boleh header: <code class="bg-amber-100 px-1 rounded font-mono">nomor,kunci</code></p>
                    <div class="mt-2.5">
                        <a href="{{ route('admin.jadwal-ujian.download-template-kunci', $jadwalUjian) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download Template CSV
                        </a>
                    </div>
                </div>
            </div>
            {{-- Drop zone --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    File Kunci Jawaban <span class="text-red-500">*</span>
                </label>
                <div class="border-2 border-dashed border-slate-200 rounded-xl p-5 text-center hover:border-amber-300 transition cursor-pointer"
                     onclick="document.getElementById('input-file-kunci-soal').click()">
                    <div id="kunci-placeholder-soal">
                        <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        <p class="text-sm font-semibold text-slate-500">Klik untuk pilih file</p>
                        <p class="text-xs text-slate-400 mt-1">CSV, TXT, XLSX, XLS — maks. 2 MB</p>
                    </div>
                    <div id="kunci-file-info-soal" class="hidden">
                        <svg class="w-8 h-8 text-amber-500 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm font-bold text-slate-700" id="kunci-file-name-soal"></p>
                        <p class="text-xs text-slate-400">File siap diupload</p>
                    </div>
                </div>
                <input type="file" id="input-file-kunci-soal" name="file_kunci"
                       accept=".csv,.txt,.xlsx,.xls"
                       class="hidden"
                       onchange="previewFileKunciSoal(this)">
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeUploadKunciModal()"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">Batal</button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Upload Kunci
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const BASE_EDIT_URL = "{{ url('admin/jadwal-ujian/'.$jadwalUjian->id.'/soal') }}/";

function openCreateModal() {
    document.getElementById('modal-create').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    // reset tipe toggle
    toggleTipeCreate('pilihan_ganda');
}
function closeCreateModal() {
    document.getElementById('modal-create').classList.add('hidden');
    document.body.style.overflow = '';
}

function openEditModal(soal) {
    document.getElementById('modal-edit').classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    document.getElementById('form-edit').action = BASE_EDIT_URL + soal.id;

    // Fill fields
    setEditField('tipe_edit', soal.tipe);
    setEditField('nomor_edit', soal.nomor);
    setEditField('pertanyaan_edit', soal.pertanyaan);
    setEditField('pilihan_a_edit', soal.pilihan_a ?? '');
    setEditField('pilihan_b_edit', soal.pilihan_b ?? '');
    setEditField('pilihan_c_edit', soal.pilihan_c ?? '');
    setEditField('pilihan_d_edit', soal.pilihan_d ?? '');
    setEditField('kunci_jawaban_edit', soal.kunci_jawaban ?? '');
    setEditField('kunci_jawaban_essay_edit', soal.kunci_jawaban_essay ?? '');
    setEditField('poin_edit', soal.poin);
    toggleTipeEdit(soal.tipe);
}
function closeEditModal() {
    document.getElementById('modal-edit').classList.add('hidden');
    document.body.style.overflow = '';
}

function setEditField(id, val) {
    const el = document.getElementById(id);
    if (el) el.value = val;
}

function toggleTipeCreate(tipe) {
    const pg    = document.getElementById('pg-fields-create');
    const essay = document.getElementById('essay-fields-create');
    if (pg)    pg.classList.toggle('hidden', tipe !== 'pilihan_ganda');
    if (essay) essay.classList.toggle('hidden', tipe !== 'essay');
}
function toggleTipeEdit(tipe) {
    const pg    = document.getElementById('pg-fields-edit');
    const essay = document.getElementById('essay-fields-edit');
    if (pg)    pg.classList.toggle('hidden', tipe !== 'pilihan_ganda');
    if (essay) essay.classList.toggle('hidden', tipe !== 'essay');
}

// ── Upload Kunci modal ──
function openUploadKunciModal() {
    document.getElementById('input-file-kunci-soal').value = '';
    document.getElementById('kunci-placeholder-soal').classList.remove('hidden');
    document.getElementById('kunci-file-info-soal').classList.add('hidden');
    document.getElementById('modal-upload-kunci').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeUploadKunciModal() {
    document.getElementById('modal-upload-kunci').classList.add('hidden');
    document.body.style.overflow = '';
}
function previewFileKunciSoal(input) {
    if (!input.files || !input.files[0]) return;
    document.getElementById('kunci-placeholder-soal').classList.add('hidden');
    document.getElementById('kunci-file-info-soal').classList.remove('hidden');
    document.getElementById('kunci-file-name-soal').textContent = input.files[0].name;
}
</script>
@endsection
