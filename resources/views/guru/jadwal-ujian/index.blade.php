@extends('guru.layouts.app')

@section('title', 'Jadwal Ujian Saya')
@section('breadcrumb', 'Jadwal Ujian')

@push('styles')
<style>
    .badge-jenis {
        display:inline-flex;align-items:center;
        padding:.2rem .65rem;border-radius:9999px;
        font-size:.7rem;font-weight:700;letter-spacing:.02em;
    }
    .badge-UTS     { background:#dbeafe;color:#1d4ed8; }
    .badge-UAS     { background:#ede9fe;color:#7c3aed; }
    .badge-UKK     { background:#ffe4e6;color:#be123c; }
    .badge-Sumatif { background:#fef3c7;color:#b45309; }
    .badge-Lainnya { background:#f1f5f9;color:#475569; }

    .date-header {
        display:flex;align-items:center;gap:.875rem;
        padding:.75rem 1.25rem;
        background:linear-gradient(to right,#f0f9ff,#e0f4f7);
        border-bottom:1px solid #bae6ed;
    }
    .date-badge {
        display:flex;flex-direction:column;align-items:center;justify-content:center;
        min-width:52px;height:52px;border-radius:12px;
        background:var(--teal);color:#fff;
        font-weight:800;line-height:1.1;flex-shrink:0;
    }
    .date-badge.lewat { background:#94a3b8; }
    .date-badge .day-num { font-size:1.2rem; }
    .date-badge .day-mon { font-size:.58rem;font-weight:600;opacity:.85; }

    .exam-row {
        display:grid;grid-template-columns:52px 1fr auto;
        border-bottom:1px solid #f1f5f9;transition:background .12s;
    }
    .exam-row:hover { background:#f8fafc; }
    .exam-row.lewat { opacity:.55; }

    .exam-time {
        display:flex;flex-direction:column;align-items:center;justify-content:flex-start;
        padding:1rem .5rem;border-right:2px solid #e2e8f0;
        font-size:.67rem;font-weight:700;color:var(--teal);
        gap:.15rem;text-align:center;
    }
    .exam-time .dot { width:8px;height:8px;border-radius:50%;background:var(--teal);margin:.3rem auto 0; }
    .exam-time.lewat { color:#94a3b8; }
    .exam-time.lewat .dot { background:#94a3b8; }

    .exam-body { padding:.875rem 1rem;min-width:0; }
    .exam-title { font-weight:700;font-size:.9rem;color:#1e293b;margin-bottom:.25rem; }
    .exam-sub   { font-size:.78rem;color:#64748b;margin-bottom:.5rem; }
    .exam-meta  { display:flex;flex-wrap:wrap;gap:.4rem .8rem; }
    .exam-meta-item {
        display:inline-flex;align-items:center;gap:.25rem;
        font-size:.72rem;color:#64748b;
    }

    .exam-actions {
        display:flex;flex-direction:column;align-items:flex-end;justify-content:center;
        gap:.4rem;padding:.875rem 1rem;flex-shrink:0;min-width:160px;
    }

    .stat-card {
        background:#fff;border-radius:16px;border:1px solid #e2e8f0;
        padding:1rem 1.25rem;display:flex;align-items:center;gap:.875rem;
        box-shadow:0 1px 3px rgba(0,0,0,.04);
    }
    .stat-icon {
        width:44px;height:44px;border-radius:12px;
        display:flex;align-items:center;justify-content:center;
        font-size:1.3rem;flex-shrink:0;
    }

    .btn-upload {
        display:inline-flex;align-items:center;gap:.3rem;
        padding:.35rem .75rem;border-radius:8px;
        font-size:.72rem;font-weight:600;cursor:pointer;
        border:none;transition:all .13s;white-space:nowrap;
    }

    /* Upload status badge */
    .soal-badge-ok  { display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .65rem;border-radius:8px;font-size:.7rem;font-weight:600;background:#d1fae5;color:#065f46; }
    .soal-badge-no  { display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .65rem;border-radius:8px;font-size:.7rem;font-weight:600;background:#fef3c7;color:#92400e; }

    /* Drag-over highlight */
    .drop-zone.dragover { border-color:var(--teal) !important; background:#f0fdf9; }
</style>
@endpush

@section('content')
<div class="space-y-5">

{{-- ── Header ── --}}
<div>
    <h1 class="text-xl font-extrabold text-slate-800">Jadwal Ujian Saya</h1>
    <p class="text-sm text-slate-500 mt-0.5">
        Kelola dan unggah soal ujian untuk mata pelajaran yang Anda ampu.
    </p>
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
@if(session('error'))
<div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-xl">
    <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    {{ session('error') }}
</div>
@endif

{{-- ── Stats ── --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="stat-card">
        <div class="stat-icon bg-teal-50">📋</div>
        <div>
            <div class="text-2xl font-extrabold text-slate-800">{{ $stats['total'] }}</div>
            <div class="text-xs text-slate-500 font-medium">Total Ujian</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-blue-50">⏳</div>
        <div>
            <div class="text-2xl font-extrabold text-slate-800">{{ $stats['upcoming'] }}</div>
            <div class="text-xs text-slate-500 font-medium">Akan Datang</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-emerald-50">✅</div>
        <div>
            <div class="text-2xl font-extrabold text-slate-800">{{ $stats['sudah_upload'] }}</div>
            <div class="text-xs text-slate-500 font-medium">Soal Terunggah</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-amber-50">⚠️</div>
        <div>
            <div class="text-2xl font-extrabold text-slate-800">{{ $stats['belum_upload'] }}</div>
            <div class="text-xs text-slate-500 font-medium">Belum Upload</div>
        </div>
    </div>
</div>

{{-- ── Filter ── --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
    <form method="GET" id="form-filter" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Jenis Ujian</label>
            <select name="jenis" onchange="document.getElementById('form-filter').submit()"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                <option value="">Semua Jenis</option>
                @foreach(App\Models\JadwalUjian::$jenis as $val => $label)
                    <option value="{{ $val }}" {{ request('jenis') === $val ? 'selected' : '' }}>{{ $val }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Bulan</label>
            <select name="bulan" onchange="document.getElementById('form-filter').submit()"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                <option value="">Semua Bulan</option>
                @foreach(['1'=>'Januari','2'=>'Februari','3'=>'Maret','4'=>'April','5'=>'Mei','6'=>'Juni','7'=>'Juli','8'=>'Agustus','9'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $num => $bln)
                    <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>{{ $bln }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Tanggal</label>
            <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                   onchange="document.getElementById('form-filter').submit()"
                   class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
        </div>
        <a href="{{ route('guru.jadwal-ujian.index') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Reset
        </a>
    </form>
    @if(!request()->filled('bulan') && !request()->filled('tanggal'))
    <p class="text-xs text-slate-400 mt-3 flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Hanya menampilkan jadwal ujian yang belum lewat. Pilih <strong>Bulan</strong> atau <strong>Tanggal</strong> untuk melihat ujian yang sudah lewat.
    </p>
    @endif
</div>

{{-- ── Empty State ── --}}
@if($ujian->isEmpty())
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-16 text-center">
    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-4xl mx-auto mb-4">📝</div>
    <h3 class="font-bold text-slate-600 mb-1">Tidak ada jadwal ujian</h3>
    <p class="text-sm text-slate-400">Belum ada jadwal ujian yang tercatat untuk mata pelajaran Anda.</p>
</div>
@else

{{-- ── Timeline ── --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

    @foreach($byTanggal as $tgl => $items)
    @php
        $dateObj    = \Carbon\Carbon::parse($tgl);
        $hariId     = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu',
                       'Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'][$dateObj->format('l')] ?? '';
        $bulanId    = ['January'=>'Januari','February'=>'Februari','March'=>'Maret','April'=>'April',
                       'May'=>'Mei','June'=>'Juni','July'=>'Juli','August'=>'Agustus',
                       'September'=>'September','October'=>'Oktober','November'=>'November',
                       'December'=>'Desember'][$dateObj->format('F')] ?? '';
        $bulanShort = ['January'=>'Jan','February'=>'Feb','March'=>'Mar','April'=>'Apr',
                       'May'=>'Mei','June'=>'Jun','July'=>'Jul','August'=>'Ags',
                       'September'=>'Sep','October'=>'Okt','November'=>'Nov','December'=>'Des'][$dateObj->format('F')] ?? '';
        $isLewat = $dateObj->startOfDay()->isPast() && !$dateObj->isToday();
        $isToday = $dateObj->isToday();
    @endphp

    <div>
        {{-- Date header --}}
        <div class="date-header">
            <div class="date-badge {{ $isLewat ? 'lewat' : '' }}">
                <span class="day-num">{{ $dateObj->format('d') }}</span>
                <span class="day-mon">{{ $bulanShort }} {{ $dateObj->format('Y') }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-bold text-sm text-slate-800">
                    {{ $hariId }}, {{ $dateObj->format('d') }} {{ $bulanId }} {{ $dateObj->format('Y') }}
                    @if($isToday)
                    <span class="ml-2 text-xs font-bold px-2 py-0.5 rounded-full bg-teal-500 text-white">Hari Ini</span>
                    @endif
                </div>
                <div class="text-xs text-slate-500 mt-0.5">{{ $items->count() }} sesi ujian</div>
            </div>
            @if($isLewat)
            <span class="text-xs font-semibold text-slate-400 bg-slate-100 px-3 py-1 rounded-full">Sudah lewat</span>
            @elseif($isToday)
            <span class="text-xs font-semibold text-teal-700 bg-teal-50 px-3 py-1 rounded-full border border-teal-200">Hari ini</span>
            @else
            <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-3 py-1 rounded-full">Akan datang</span>
            @endif
        </div>

        {{-- Exam rows --}}
        @foreach($items->sortBy('jam_mulai') as $u)
        <div class="exam-row {{ $isLewat ? 'lewat' : '' }}">

            {{-- Time --}}
            <div class="exam-time {{ $isLewat ? 'lewat' : '' }}">
                <span>{{ substr($u->jam_mulai,0,5) }}</span>
                <span class="text-slate-300 text-[.6rem]">|</span>
                <span>{{ substr($u->jam_selesai,0,5) }}</span>
                <div class="dot"></div>
            </div>

            {{-- Body --}}
            <div class="exam-body">
                <div class="flex flex-wrap items-center gap-1.5 mb-1.5">
                    <span class="badge-jenis badge-{{ $u->jenis }}">{{ $u->jenis }}</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[.7rem] font-bold bg-teal-50 text-teal-700 border border-teal-100">
                        {{ $u->kelas->nama }}
                    </span>
                    @if($u->file_soal)
                        <span class="soal-badge-ok">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Soal Terunggah
                        </span>
                    @else
                        <span class="soal-badge-no">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Belum Upload
                        </span>
                    @endif
                </div>
                <div class="exam-title">{{ $u->mataPelajaran->nama }}</div>
                <div class="exam-sub">{{ $u->nama }}</div>
                <div class="exam-meta">
                    <span class="exam-meta-item">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ substr($u->jam_mulai,0,5) }} – {{ substr($u->jam_selesai,0,5) }}
                    </span>
                    @if($u->ruangan)
                    <span class="exam-meta-item">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $u->ruangan }}
                    </span>
                    @endif
                    @if($u->keterangan)
                    <span class="exam-meta-item">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $u->keterangan }}
                    </span>
                    @endif
                    @if($u->file_soal)
                    <a href="{{ Storage::url($u->file_soal) }}" target="_blank"
                       class="exam-meta-item text-teal-600 hover:underline font-semibold">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Lihat / Unduh Soal
                    </a>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="exam-actions">
                <button type="button"
                        onclick="openUploadModal({{ $u->id }}, '{{ addslashes($u->mataPelajaran->nama) }}', '{{ $u->jenis }}', {{ $u->file_soal ? 'true' : 'false' }})"
                        class="btn-upload text-white"
                        style="background: var(--teal);">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    {{ $u->file_soal ? 'Ganti Soal' : 'Upload Soal' }}
                </button>

                @if($u->file_soal)
                <form method="POST" action="{{ route('guru.jadwal-ujian.hapus-soal', $u) }}"
                      onsubmit="return confirm('Hapus file soal ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-upload text-red-600 bg-red-50 hover:bg-red-100">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus Soal
                    </button>
                </form>
                @endif
            </div>

        </div>
        @endforeach
    </div>
    @endforeach

</div>
@endif

</div>{{-- .space-y-5 --}}

{{-- ══════════════ MODAL UPLOAD SOAL ══════════════ --}}
<div id="modal-upload" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden"
     onclick="if(event.target===this) closeUploadModal()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div>
                <h2 class="text-base font-extrabold text-slate-800" id="modal-title">Upload Soal Ujian</h2>
                <p class="text-xs text-slate-400 mt-0.5" id="modal-subtitle">—</p>
            </div>
            <button type="button" onclick="closeUploadModal()"
                    class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="form-upload" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf

            {{-- Drop zone --}}
            <div id="drop-zone"
                 class="drop-zone border-2 border-dashed border-slate-300 rounded-2xl p-8 text-center cursor-pointer transition hover:border-teal-400 hover:bg-teal-50/30"
                 onclick="document.getElementById('file-input').click()"
                 ondragover="handleDragOver(event)"
                 ondragleave="handleDragLeave(event)"
                 ondrop="handleDrop(event)">
                <div id="drop-icon" class="text-4xl mb-3">📄</div>
                <p id="drop-label" class="text-sm font-semibold text-slate-600">
                    Klik atau seret file ke sini
                </p>
                <p class="text-xs text-slate-400 mt-1">PDF, DOC, DOCX, XLS, XLSX — maks. 10 MB</p>
                <input type="file" id="file-input" name="file_soal" accept=".pdf,.doc,.docx,.xls,.xlsx" class="hidden"
                       onchange="handleFileSelect(this)">
            </div>

            {{-- Preview file terpilih --}}
            <div id="file-preview" class="hidden flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">
                <div class="text-2xl" id="file-icon">📄</div>
                <div class="flex-1 min-w-0">
                    <p id="file-name" class="text-sm font-semibold text-slate-700 truncate"></p>
                    <p id="file-size" class="text-xs text-slate-400"></p>
                </div>
                <button type="button" onclick="clearFile()"
                        class="p-1 rounded-lg text-slate-400 hover:bg-slate-200 hover:text-red-500 transition flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Validasi error --}}
            @error('file_soal')
            <p class="text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">{{ $message }}</p>
            @enderror

            {{-- Info ganti soal --}}
            <div id="replace-warning" class="hidden flex items-start gap-2 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-xs text-amber-800">
                <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>File soal sebelumnya akan diganti. File lama akan dihapus permanen.</span>
            </div>

            <div class="flex justify-end gap-3 pt-1 border-t border-slate-100">
                <button type="button" onclick="closeUploadModal()"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </button>
                <button type="submit" id="btn-submit-upload"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:brightness-110 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        style="background: var(--teal);" disabled>
                    <img id="btn-loading-img" src="{{ asset('image/smk.png') }}" alt="Loading" class="w-4 h-4 object-contain animate-spin hidden">
                    <span id="btn-label-upload">Upload Soal</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const uploadRoutes = @json(
    $ujian->mapWithKeys(fn($u) => [$u->id => route('guru.jadwal-ujian.upload-soal', $u)])
);

function openUploadModal(id, mapel, jenis, hasFile) {
    document.getElementById('modal-title').textContent = hasFile ? 'Ganti Soal Ujian' : 'Upload Soal Ujian';
    document.getElementById('modal-subtitle').textContent = mapel + ' · ' + jenis;
    document.getElementById('form-upload').action = uploadRoutes[id] ?? '';
    document.getElementById('replace-warning').classList.toggle('hidden', !hasFile);
    clearFile();
    document.getElementById('modal-upload').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeUploadModal() {
    document.getElementById('modal-upload').classList.add('hidden');
    document.body.style.overflow = '';
    clearFile();
}

/* ── File select / drag-drop ── */
const extIcons = { pdf:'📕', doc:'📘', docx:'📘', xls:'📗', xlsx:'📗' };

function handleFileSelect(input) {
    if (input.files && input.files[0]) showFilePreview(input.files[0]);
}

function handleDragOver(e) {
    e.preventDefault();
    document.getElementById('drop-zone').classList.add('dragover');
}
function handleDragLeave(e) {
    document.getElementById('drop-zone').classList.remove('dragover');
}
function handleDrop(e) {
    e.preventDefault();
    document.getElementById('drop-zone').classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (!file) return;
    // Assign to input
    const dt  = new DataTransfer();
    dt.items.add(file);
    const inp = document.getElementById('file-input');
    inp.files = dt.files;
    showFilePreview(file);
}

function showFilePreview(file) {
    const ext  = file.name.split('.').pop().toLowerCase();
    const icon = extIcons[ext] ?? '📄';
    const size = file.size > 1048576
        ? (file.size / 1048576).toFixed(1) + ' MB'
        : Math.round(file.size / 1024) + ' KB';

    document.getElementById('file-icon').textContent  = icon;
    document.getElementById('file-name').textContent  = file.name;
    document.getElementById('file-size').textContent  = size;
    document.getElementById('file-preview').classList.remove('hidden');
    document.getElementById('drop-zone').classList.add('hidden');
    document.getElementById('btn-submit-upload').disabled = false;
    document.getElementById('btn-label-upload').textContent = 'Upload Soal';
}

function clearFile() {
    document.getElementById('file-input').value = '';
    document.getElementById('file-preview').classList.add('hidden');
    document.getElementById('drop-zone').classList.remove('hidden');
    document.getElementById('btn-submit-upload').disabled = true;
}

/* ── Loading state on submit ── */
document.getElementById('form-upload').addEventListener('submit', function () {
    const btn = document.getElementById('btn-submit-upload');
    btn.disabled = true;
    document.getElementById('btn-label-upload').textContent = 'Mengunggah…';
    document.getElementById('btn-loading-img').classList.remove('hidden');
});
</script>
@endpush
@endsection
