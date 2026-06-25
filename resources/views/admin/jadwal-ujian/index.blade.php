@extends('admin.layouts.app')

@section('title', 'Jadwal Ujian')
@section('breadcrumb', 'Jadwal Ujian')

@section('content')
<style>
    /* ── Badge jenis ── */
    .badge-jenis {
        display: inline-flex; align-items: center;
        padding: .2rem .65rem; border-radius: 9999px;
        font-size: .7rem; font-weight: 700; letter-spacing: .02em;
    }
    .badge-UTS     { background:#dbeafe; color:#1d4ed8; }
    .badge-UAS     { background:#ede9fe; color:#7c3aed; }
    .badge-UKK     { background:#ffe4e6; color:#be123c; }
    .badge-Sumatif { background:#fef3c7; color:#b45309; }
    .badge-Lainnya { background:#f1f5f9; color:#475569; }

    /* ── Date header ── */
    .date-header {
        display: flex; align-items: center; gap: .875rem;
        padding: .75rem 1.25rem;
        background: linear-gradient(to right, #f0f9ff, #e0f4f7);
        border-bottom: 1px solid #bae6ed;
        position: sticky; top: 0; z-index: 2;
    }
    .date-badge {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        min-width: 54px; height: 54px; border-radius: 12px;
        background: var(--teal); color: #fff;
        font-weight: 800; line-height: 1.1; flex-shrink: 0;
    }
    .date-badge.lewat { background: #94a3b8; }
    .date-badge .day-num  { font-size: 1.25rem; }
    .date-badge .day-mon  { font-size: .6rem; font-weight: 600; opacity: .85; }

    /* ── Exam row card ── */
    .exam-row {
        display: grid;
        grid-template-columns: 52px 1fr auto;
        gap: 0;
        border-bottom: 1px solid #f1f5f9;
        transition: background .12s;
    }
    .exam-row:hover { background: #f8fafc; }
    .exam-row.lewat { opacity: .58; }

    .exam-time {
        display: flex; flex-direction: column; align-items: center; justify-content: flex-start;
        padding: 1rem .5rem;
        border-right: 2px solid #e2e8f0;
        font-size: .68rem; font-weight: 700; color: var(--teal);
        gap: .15rem; text-align: center;
    }
    .exam-time .dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: var(--teal); margin: .3rem auto 0;
    }
    .exam-time.lewat .dot { background: #94a3b8; }
    .exam-time.lewat { color: #94a3b8; }

    .exam-body {
        padding: .875rem 1rem;
        min-width: 0;
    }
    .exam-title { font-weight: 700; font-size: .9rem; color: #1e293b; margin-bottom: .25rem; }
    .exam-sub   { font-size: .78rem; color: #64748b; margin-bottom: .5rem; }
    .exam-meta  { display: flex; flex-wrap: wrap; gap: .4rem .8rem; }
    .exam-meta-item {
        display: inline-flex; align-items: center; gap: .25rem;
        font-size: .72rem; color: #64748b;
    }
    .exam-meta-item svg { flex-shrink: 0; }

    .exam-actions {
        display: grid; grid-template-columns: 1fr 1fr; align-items: start;
        gap: .35rem; padding: .875rem 1rem; flex-shrink: 0; min-width: 220px;
    }
    .exam-actions .btn-action {
        justify-content: center; width: 100%;
    }
    .exam-actions form {
        display: contents;
    }

    /* ── Pill filter ── */
    .jenis-pill {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .3rem .8rem; border-radius: 9999px;
        font-size: .73rem; font-weight: 600; cursor: pointer;
        border: 1.5px solid transparent; transition: all .15s;
    }
    .jenis-pill:not(.active) { background: #f1f5f9; color: #475569; border-color: #e2e8f0; }
    .jenis-pill:not(.active):hover { background: #e2e8f0; }
    .jenis-pill.active { color: #fff; }
    .jenis-pill[data-jenis="semua"].active    { background:#475569; border-color:#475569; }
    .jenis-pill[data-jenis="UTS"].active      { background:#1d4ed8; border-color:#1d4ed8; }
    .jenis-pill[data-jenis="UAS"].active      { background:#7c3aed; border-color:#7c3aed; }
    .jenis-pill[data-jenis="UKK"].active      { background:#be123c; border-color:#be123c; }
    .jenis-pill[data-jenis="Sumatif"].active  { background:#b45309; border-color:#b45309; }
    .jenis-pill[data-jenis="Lainnya"].active  { background:#64748b; border-color:#64748b; }

    /* ── Stat card ── */
    .stat-card {
        background: #fff; border-radius: 16px; border: 1px solid #e2e8f0;
        padding: 1rem 1.25rem; display: flex; align-items: center; gap: .875rem;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .stat-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.35rem; flex-shrink: 0;
    }

    /* ── Action button ── */
    .btn-action {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .3rem .7rem; border-radius: 8px;
        font-size: .72rem; font-weight: 600; cursor: pointer;
        border: none; transition: all .13s; white-space: nowrap;
    }
</style>
<div class="space-y-5">

{{-- ── Header ── --}}
<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-extrabold text-slate-800">Jadwal Ujian</h1>
        <p class="text-sm text-slate-500 mt-0.5">
            <span class="font-semibold text-slate-700">{{ $stats['total'] }}</span> sesi ujian terdaftar
        </p>
    </div>
    <button type="button" onclick="openCreateModal()"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white shadow-sm transition hover:brightness-110 active:scale-95"
            style="background: var(--teal);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Jadwal Ujian
    </button>
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
        <div class="stat-icon bg-slate-50">✅</div>
        <div>
            <div class="text-2xl font-extrabold text-slate-800">{{ $stats['lewat'] }}</div>
            <div class="text-xs text-slate-500 font-medium">Sudah Lewat</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-emerald-50">🟢</div>
        <div>
            <div class="text-2xl font-extrabold text-slate-800">{{ $stats['aktif'] }}</div>
            <div class="text-xs text-slate-500 font-medium">Aktif</div>
        </div>
    </div>
</div>

{{-- ── Info upload soal ── --}}
<div class="flex items-start gap-3 bg-indigo-50 border border-indigo-200 text-indigo-800 text-sm px-4 py-3 rounded-xl">
    <svg class="w-4 h-4 shrink-0 mt-0.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span>
        Upload file <strong>DOCX</strong> untuk mengimpor soal secara otomatis ke sistem ujian online murid.
        Format soal di dalam file:
        <code class="bg-indigo-100 px-1 rounded text-xs font-mono">1. Pertanyaan</code>
        <code class="bg-indigo-100 px-1 rounded text-xs font-mono">A. Pilihan</code>
        <code class="bg-indigo-100 px-1 rounded text-xs font-mono">Jawaban: A</code>
        — soal essay cukup tulis nomor dan pertanyaan tanpa pilihan.
    </span>
</div>

{{-- ── Filter ── --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
    <form method="GET" id="form-filter" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Kelas</label>
            <select name="kelas_id" onchange="document.getElementById('form-filter').submit()"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $kelas)
                    <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Jenis Ujian</label>
            <select name="jenis" onchange="document.getElementById('form-filter').submit()"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                <option value="">Semua Jenis</option>
                @foreach(App\Models\JadwalUjian::$jenis as $val => $label)
                    <option value="{{ $val }}" {{ request('jenis') === $val ? 'selected' : '' }}>{{ $val }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Bulan</label>
            <select name="bulan" onchange="document.getElementById('form-filter').submit()"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
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
                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
        </div>
        <div class="flex items-end">
            <a href="{{ route('admin.jadwal-ujian.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Reset Filter
            </a>
        </div>
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

{{-- ── Jenis Pills ── --}}
@if($ujian->isNotEmpty())
<div class="flex flex-wrap gap-2 items-center">
    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide mr-1">Filter:</span>
    <button class="jenis-pill active" data-jenis="semua" onclick="filterJenis('semua',this)">
        Semua <span class="bg-white/30 text-[.65rem] px-1 rounded-full ml-0.5">{{ $stats['total'] }}</span>
    </button>
    @foreach(App\Models\JadwalUjian::$jenis as $jv => $_)
        @php $cnt = $ujian->where('jenis', $jv)->count(); @endphp
        @if($cnt)
        <button class="jenis-pill" data-jenis="{{ $jv }}" onclick="filterJenis('{{ $jv }}',this)">
            {{ $jv }} <span class="opacity-70 text-[.65rem] ml-0.5">{{ $cnt }}</span>
        </button>
        @endif
    @endforeach
</div>
@endif

{{-- ── Empty State ── --}}
@if($ujian->isEmpty())
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-16 text-center">
    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-4xl mx-auto mb-4">📝</div>
    <h3 class="font-bold text-slate-600 mb-1">Belum ada jadwal ujian</h3>
    <p class="text-sm text-slate-400 mb-4">Tambahkan jadwal ujian baru dengan tombol di atas.</p>
    <button type="button" onclick="openCreateModal()"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
            style="background: var(--teal);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Jadwal Ujian
    </button>
</div>
@else

{{-- ── Timeline ── --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden" id="timeline-container">

    @foreach($byTanggal as $tgl => $items)
    @php
        $dateObj = \Carbon\Carbon::parse($tgl);
        $hariId  = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu',
                    'Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'][$dateObj->format('l')] ?? '';
        $bulanId = ['January'=>'Januari','February'=>'Februari','March'=>'Maret','April'=>'April',
                    'May'=>'Mei','June'=>'Juni','July'=>'Juli','August'=>'Agustus',
                    'September'=>'September','October'=>'Oktober','November'=>'November',
                    'December'=>'Desember'][$dateObj->format('F')] ?? '';
        $bulanShort = ['January'=>'Jan','February'=>'Feb','March'=>'Mar','April'=>'Apr',
                       'May'=>'Mei','June'=>'Jun','July'=>'Jul','August'=>'Ags',
                       'September'=>'Sep','October'=>'Okt','November'=>'Nov',
                       'December'=>'Des'][$dateObj->format('F')] ?? '';
        $isLewat = $dateObj->startOfDay()->isPast() && !$dateObj->isToday();
        $isToday = $dateObj->isToday();
    @endphp

    <div class="tanggal-block" data-tanggal="{{ $tgl }}">

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
        <div class="exam-row {{ $isLewat ? 'lewat' : '' }}" data-jenis="{{ $u->jenis }}">

            {{-- Time column --}}
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
                    @if(!$u->aktif)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[.65rem] font-semibold text-slate-400 bg-slate-100">Nonaktif</span>
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
                    @if($u->guru)
                    <span class="exam-meta-item">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ $u->guru->name }}
                    </span>
                    @endif
                    @if($u->keterangan)
                    <span class="exam-meta-item">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $u->keterangan }}
                    </span>
                    @endif
                    {{-- Status soal --}}
                    @if($u->file_soal)
                    <a href="{{ Storage::url($u->file_soal) }}" target="_blank"
                       class="exam-meta-item font-semibold" style="color:#065f46;">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Soal tersedia
                    </a>
                    @else
                    <span class="exam-meta-item" style="color:#92400e;">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Soal belum diupload
                    </span>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="exam-actions">
                <button type="button"
                        data-id="{{ $u->id }}"
                        data-ujian='{!! json_encode($u->only(["nama","jenis","kelas_id","mata_pelajaran_id","guru_id","tanggal","jam_mulai","jam_selesai","ruangan","keterangan","aktif"]), JSON_UNESCAPED_UNICODE) !!}'
                        onclick="openEditModal(this.dataset.id, JSON.parse(this.dataset.ujian))"
                        class="btn-action text-slate-600 bg-slate-100 hover:bg-slate-200">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </button>
                <a href="{{ route('admin.soal-ujian.index', $u) }}"
                   class="btn-action text-indigo-700 bg-indigo-50 hover:bg-indigo-100">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Kelola Soal
                </a>
                <button type="button"
                        onclick="openUploadSoalModal({{ $u->id }}, '{{ addslashes($u->mataPelajaran->nama) }} – {{ $u->jenis }}', {{ $u->file_soal ? 'true' : 'false' }})"
                        class="btn-action {{ $u->file_soal ? 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100' : 'text-indigo-700 bg-indigo-50 hover:bg-indigo-100' }}">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    {{ $u->file_soal ? 'Ganti Soal' : 'Upload Soal' }}
                </button>

                <form method="POST" action="{{ route('admin.jadwal-ujian.toggle-aktif', $u) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="btn-action {{ $u->aktif ? 'text-amber-700 bg-amber-50 hover:bg-amber-100' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }}">
                        @if($u->aktif)
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        Nonaktifkan
                        @else
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Aktifkan
                        @endif
                    </button>
                </form>
                <button type="button"
                        onclick="confirmDelete('{{ route('admin.jadwal-ujian.destroy', $u) }}', '{{ addslashes($u->mataPelajaran->nama) }} – {{ $u->jenis }}')"
                        class="btn-action text-red-600 bg-red-50 hover:bg-red-100" style="grid-column: span 2">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Hapus
                </button>
            </div>

        </div>
        @endforeach

    </div>{{-- .tanggal-block --}}
    @endforeach

</div>{{-- #timeline-container --}}
@endif

</div>{{-- .space-y-5 --}}

{{-- ══════════════ MODAL TAMBAH ══════════════ --}}
<div id="modal-create" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden"
     onclick="if(event.target===this) closeCreateModal()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[92vh] overflow-y-auto">
        <div class="sticky top-0 bg-white flex items-center justify-between px-6 py-4 border-b border-slate-100 z-10">
            <div>
                <h2 class="text-base font-extrabold text-slate-800">Tambah Jadwal Ujian</h2>
                <p class="text-xs text-slate-400 mt-0.5">Isi detail sesi ujian baru</p>
            </div>
            <button type="button" onclick="closeCreateModal()" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        @if($errors->any() && old('_form') === 'create')
        <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
            <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif
        <form method="POST" action="{{ route('admin.jadwal-ujian.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="_form" value="create">

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Ujian <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Contoh: UTS Semester Ganjil 2026"
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300 focus:border-teal-300">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Jenis --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Ujian <span class="text-red-500">*</span></label>
                    <select name="jenis" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Jenis —</option>
                        @foreach(App\Models\JadwalUjian::$jenis as $v => $l)
                            <option value="{{ $v }}" {{ old('jenis') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Kelas --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kelas <span class="text-red-500">*</span></label>
                    <select name="kelas_id" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Kelas —</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ old('kelas_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Mapel --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Mata Pelajaran <span class="text-red-500">*</span></label>
                    <select name="mata_pelajaran_id" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Mapel —</option>
                        @foreach($mapelList as $mapel)
                            <option value="{{ $mapel->id }}" {{ old('mata_pelajaran_id') == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Guru --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Guru Pengawas</label>
                    <select name="guru_id" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Opsional —</option>
                        @foreach($guruList as $guru)
                            <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>{{ $guru->name }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Tanggal --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal') }}"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                {{-- Ruangan --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Ruangan</label>
                    <input type="text" name="ruangan" value="{{ old('ruangan') }}" placeholder="Contoh: R.101"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                {{-- Jam --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jam Mulai <span class="text-red-500">*</span></label>
                    <input type="time" name="jam_mulai" value="{{ old('jam_mulai') }}"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jam Selesai <span class="text-red-500">*</span></label>
                    <input type="time" name="jam_selesai" value="{{ old('jam_selesai') }}"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                {{-- Status --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status</label>
                    <select name="aktif" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="1" {{ old('aktif','1')==='1'?'selected':'' }}>✅ Aktif</option>
                        <option value="0" {{ old('aktif')==='0'?'selected':'' }}>⛔ Nonaktif</option>
                    </select>
                </div>
            </div>

            {{-- Keterangan --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Keterangan</label>
                <textarea name="keterangan" rows="2" placeholder="Catatan tambahan (opsional)"
                          class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300 resize-none">{{ old('keterangan') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeCreateModal()"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">Batal</button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 transition">
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════ MODAL EDIT ══════════════ --}}
<div id="modal-edit" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden"
     onclick="if(event.target===this) closeEditModal()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[92vh] overflow-y-auto">
        <div class="sticky top-0 bg-white flex items-center justify-between px-6 py-4 border-b border-slate-100 z-10">
            <div>
                <h2 class="text-base font-extrabold text-slate-800">Edit Jadwal Ujian</h2>
                <p class="text-xs text-slate-400 mt-0.5">Ubah detail sesi ujian</p>
            </div>
            <button type="button" onclick="closeEditModal()" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        @if($errors->any() && old('_form') === 'edit')
        <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
            <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif
        <form id="form-edit" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')
            <input type="hidden" name="_form" value="edit">
            <input type="hidden" name="edit_id" value="{{ old('edit_id') }}">

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Ujian <span class="text-red-500">*</span></label>
                <input type="text" id="edit-nama" name="nama" placeholder="Contoh: UTS Semester Ganjil 2026"
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Ujian <span class="text-red-500">*</span></label>
                    <select id="edit-jenis" name="jenis" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Jenis —</option>
                        @foreach(App\Models\JadwalUjian::$jenis as $v => $l)
                            <option value="{{ $v }}">{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kelas <span class="text-red-500">*</span></label>
                    <select id="edit-kelas_id" name="kelas_id" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Kelas —</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}">{{ $kelas->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Mata Pelajaran <span class="text-red-500">*</span></label>
                    <select id="edit-mata_pelajaran_id" name="mata_pelajaran_id" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Pilih Mapel —</option>
                        @foreach($mapelList as $mapel)
                            <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Guru Pengawas</label>
                    <select id="edit-guru_id" name="guru_id" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">— Opsional —</option>
                        @foreach($guruList as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" id="edit-tanggal" name="tanggal"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Ruangan</label>
                    <input type="text" id="edit-ruangan" name="ruangan" placeholder="Contoh: R.101"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jam Mulai <span class="text-red-500">*</span></label>
                    <input type="time" id="edit-jam_mulai" name="jam_mulai"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jam Selesai <span class="text-red-500">*</span></label>
                    <input type="time" id="edit-jam_selesai" name="jam_selesai"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status</label>
                    <select id="edit-aktif" name="aktif" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="1">✅ Aktif</option>
                        <option value="0">⛔ Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Keterangan</label>
                <textarea id="edit-keterangan" name="keterangan" rows="2" placeholder="Catatan tambahan (opsional)"
                          class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300 resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">Batal</button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════ MODAL UPLOAD SOAL ══════════════ --}}
<div id="modal-upload-soal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden"
     onclick="if(event.target===this) closeUploadSoalModal()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div>
                <h2 class="text-base font-extrabold text-slate-800" id="upload-soal-title">Upload Soal Ujian</h2>
                <p class="text-xs text-slate-400 mt-0.5" id="upload-soal-subtitle"></p>
            </div>
            <button type="button" onclick="closeUploadSoalModal()" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="form-upload-soal" method="POST" action="" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-sm font-semibold text-slate-700">
                        File Soal <span class="text-red-500">*</span>
                    </label>
                    <a id="btn-download-template-soal" href="#" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download Template Word
                    </a>
                </div>
                <div class="border-2 border-dashed border-slate-200 rounded-xl p-5 text-center hover:border-teal-300 transition cursor-pointer"
                     onclick="document.getElementById('input-file-soal').click()">
                    <div id="upload-placeholder">
                        <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-sm font-semibold text-slate-500">Klik untuk pilih file</p>
                        <p class="text-xs text-slate-400 mt-1">PDF, DOC, <strong class="text-indigo-500">DOCX</strong>, XLS, XLSX — maks. 10 MB</p>
                        <p class="text-xs text-teal-600 mt-1.5 font-medium">✦ Upload DOCX → soal diekstrak otomatis ke sistem ujian</p>
                    </div>
                    <div id="upload-file-info" class="hidden">
                        <svg class="w-8 h-8 text-teal-500 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm font-bold text-slate-700" id="upload-file-name"></p>
                        <p class="text-xs text-slate-400" id="upload-file-size"></p>
                    </div>
                </div>
                <input type="file" id="input-file-soal" name="file_soal"
                       accept=".pdf,.doc,.docx,.xls,.xlsx"
                       class="hidden"
                       onchange="previewFileSoal(this)">
            </div>

            {{-- Info ganti file --}}
            <div id="upload-replace-info" class="hidden items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 text-xs px-3 py-2.5 rounded-lg">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>File soal lama akan diganti dengan file baru yang diunggah.</span>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeUploadSoalModal()"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">Batal</button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Upload Soal
                </button>
            </div>
        </form>

        {{-- Hapus soal (jika sudah ada) --}}
        <div id="upload-hapus-section" class="hidden px-6 pb-5">
            <div class="border-t border-slate-100 pt-4 flex items-center justify-between">
                <span class="text-xs text-slate-500">Atau hapus file soal yang ada</span>
                <form id="form-hapus-soal" method="POST" action="">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus File Soal
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════ MODAL UPLOAD KUNCI JAWABAN ══════════════ --}}
<div id="modal-upload-kunci" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden"
     onclick="if(event.target===this) closeUploadKunciModal()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div>
                <h2 class="text-base font-extrabold text-slate-800">Upload Kunci Jawaban</h2>
                <p class="text-xs text-slate-400 mt-0.5" id="upload-kunci-subtitle"></p>
            </div>
            <button type="button" onclick="closeUploadKunciModal()" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="form-upload-kunci" method="POST" action="" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf

            {{-- Info format + tombol download template --}}
            <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 text-amber-800 text-xs px-4 py-3 rounded-xl">
                <svg class="w-4 h-4 shrink-0 mt-0.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <p class="font-semibold mb-1">Format file CSV/XLSX:</p>
                    <p>Kolom 1 = nomor soal, Kolom 2 = kunci jawaban (A/B/C/D untuk PG, teks untuk essay)</p>
                    <p class="mt-1 text-amber-600">Baris pertama boleh header: <code class="bg-amber-100 px-1 rounded font-mono">nomor,kunci</code></p>
                    <p class="mt-1">Contoh: <code class="bg-amber-100 px-1 rounded font-mono">1,A</code> &nbsp; <code class="bg-amber-100 px-1 rounded font-mono">2,C</code> &nbsp; <code class="bg-amber-100 px-1 rounded font-mono">3,B</code></p>
                    <div class="mt-2.5">
                        <a id="btn-download-template-kunci" href="#"
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
                     onclick="document.getElementById('input-file-kunci').click()">
                    <div id="kunci-placeholder">
                        <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        <p class="text-sm font-semibold text-slate-500">Klik untuk pilih file</p>
                        <p class="text-xs text-slate-400 mt-1">CSV, TXT, XLSX, XLS — maks. 2 MB</p>
                    </div>
                    <div id="kunci-file-info" class="hidden">
                        <svg class="w-8 h-8 text-amber-500 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm font-bold text-slate-700" id="kunci-file-name"></p>
                        <p class="text-xs text-slate-400">File siap diupload</p>
                    </div>
                </div>
                <input type="file" id="input-file-kunci" name="file_kunci"
                       accept=".csv,.txt,.xlsx,.xls"
                       class="hidden"
                       onchange="previewFileKunci(this)">
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

{{-- ══════════════ DELETE CONFIRM MODAL ══════════════ --}}
<div id="modal-delete" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden"
     onclick="if(event.target===this) closeDeleteModal()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6">
        <div class="flex flex-col items-center text-center gap-3">
            <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center">
                <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div>
                <h3 class="font-extrabold text-slate-800 text-base">Hapus Jadwal Ujian?</h3>
                <p class="text-sm text-slate-500 mt-1" id="delete-confirm-text">Data ujian akan dihapus permanen.</p>
            </div>
        </div>
        <div class="flex gap-3 mt-5">
            <button type="button" onclick="closeDeleteModal()"
                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">Batal</button>
            <form id="form-delete" method="POST" class="flex-1">
                @csrf @method('DELETE')
                <button type="submit"
                        class="w-full px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-red-500 hover:bg-red-600 transition">Hapus</button>
            </form>
        </div>
    </div>
</div>

<script>
const editUjianRoutes = @json($ujian->mapWithKeys(fn($u) => [$u->id => route('admin.jadwal-ujian.update', $u)]));
const uploadSoalRoutes = @json($ujian->mapWithKeys(fn($u) => [$u->id => route('admin.jadwal-ujian.upload-soal', $u)]));
const downloadTemplateSoalRoutes = @json($ujian->mapWithKeys(fn($u) => [$u->id => route('admin.jadwal-ujian.download-template-soal', $u)]));
const hapusSoalRoutes  = @json($ujian->mapWithKeys(fn($u) => [$u->id => route('admin.jadwal-ujian.hapus-soal', $u)]));
const uploadKunciRoutes = @json($ujian->mapWithKeys(fn($u) => [$u->id => route('admin.jadwal-ujian.upload-kunci', $u)]));
const downloadTemplateKunciRoutes = @json($ujian->mapWithKeys(fn($u) => [$u->id => route('admin.jadwal-ujian.download-template-kunci', $u)]));

// ── Jenis pill filter ──
function filterJenis(jenis, btn) {
    document.querySelectorAll('.jenis-pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.ujian-card, .exam-row').forEach(card => {
        const show = jenis === 'semua' || card.dataset.jenis === jenis;
        card.style.display = show ? '' : 'none';
    });
    document.querySelectorAll('.tanggal-block').forEach(block => {
        const visible = [...block.querySelectorAll('.exam-row')].some(c => c.style.display !== 'none');
        block.style.display = visible ? '' : 'none';
    });
}

// ── Create modal ──
function openCreateModal() {
    document.getElementById('modal-create').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeCreateModal() {
    document.getElementById('modal-create').classList.add('hidden');
    document.body.style.overflow = '';
}

// ── Edit modal ──
function openEditModal(id, data) {
    const form = document.getElementById('form-edit');
    form.action = editUjianRoutes[id] ?? '';
    form.querySelector('input[name="edit_id"]').value = id;
    if (data) {
        document.getElementById('edit-nama').value               = data.nama ?? '';
        document.getElementById('edit-jenis').value             = data.jenis ?? '';
        document.getElementById('edit-kelas_id').value          = data.kelas_id ?? '';
        document.getElementById('edit-mata_pelajaran_id').value = data.mata_pelajaran_id ?? '';
        document.getElementById('edit-guru_id').value           = data.guru_id ?? '';
        document.getElementById('edit-tanggal').value           = data.tanggal ?? '';
        document.getElementById('edit-jam_mulai').value         = (data.jam_mulai ?? '').substring(0, 5);
        document.getElementById('edit-jam_selesai').value       = (data.jam_selesai ?? '').substring(0, 5);
        document.getElementById('edit-ruangan').value           = data.ruangan ?? '';
        document.getElementById('edit-keterangan').value        = data.keterangan ?? '';
        document.getElementById('edit-aktif').value             = data.aktif ? '1' : '0';
    }
    document.getElementById('modal-edit').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeEditModal() {
    document.getElementById('modal-edit').classList.add('hidden');
    document.body.style.overflow = '';
}

// ── Upload Soal modal ──
function openUploadSoalModal(id, label, hasSoal) {
    document.getElementById('form-upload-soal').action = uploadSoalRoutes[id] ?? '';
    document.getElementById('form-hapus-soal').action  = hapusSoalRoutes[id]  ?? '';
    // Set href tombol download template
    const btnTemplate = document.getElementById('btn-download-template-soal');
    if (btnTemplate) btnTemplate.href = downloadTemplateSoalRoutes[id] ?? '#';
    document.getElementById('upload-soal-title').textContent    = hasSoal ? 'Ganti Soal Ujian' : 'Upload Soal Ujian';
    document.getElementById('upload-soal-subtitle').textContent = label;
    // reset file input
    document.getElementById('input-file-soal').value = '';
    document.getElementById('upload-placeholder').classList.remove('hidden');
    document.getElementById('upload-file-info').classList.add('hidden');
    // tampilkan/sembunyikan info ganti & tombol hapus
    const replaceInfo  = document.getElementById('upload-replace-info');
    const hapusSection = document.getElementById('upload-hapus-section');
    if (hasSoal) {
        replaceInfo.classList.remove('hidden');
        replaceInfo.classList.add('flex');
        hapusSection.classList.remove('hidden');
    } else {
        replaceInfo.classList.add('hidden');
        replaceInfo.classList.remove('flex');
        hapusSection.classList.add('hidden');
    }
    document.getElementById('modal-upload-soal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeUploadSoalModal() {
    document.getElementById('modal-upload-soal').classList.add('hidden');
    document.body.style.overflow = '';
}
function previewFileSoal(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    document.getElementById('upload-placeholder').classList.add('hidden');
    document.getElementById('upload-file-info').classList.remove('hidden');
    document.getElementById('upload-file-name').textContent = file.name;
    const kb = file.size / 1024;
    document.getElementById('upload-file-size').textContent = kb > 1024
        ? (kb / 1024).toFixed(1) + ' MB'
        : Math.round(kb) + ' KB';
}

// ── Upload Kunci modal ──
function openUploadKunciModal(id, label) {
    document.getElementById('form-upload-kunci').action = uploadKunciRoutes[id] ?? '';
    document.getElementById('upload-kunci-subtitle').textContent = label;
    document.getElementById('input-file-kunci').value = '';
    document.getElementById('kunci-file-info').classList.add('hidden');
    document.getElementById('kunci-placeholder').classList.remove('hidden');
    // Update link download template
    const downloadBtn = document.getElementById('btn-download-template-kunci');
    if (downloadBtn && downloadTemplateKunciRoutes[id]) {
        downloadBtn.href = downloadTemplateKunciRoutes[id];
    }
    document.getElementById('modal-upload-kunci').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeUploadKunciModal() {
    document.getElementById('modal-upload-kunci').classList.add('hidden');
    document.body.style.overflow = '';
}
function previewFileKunci(input) {
    if (!input.files || !input.files[0]) return;
    document.getElementById('kunci-placeholder').classList.add('hidden');
    document.getElementById('kunci-file-info').classList.remove('hidden');
    document.getElementById('kunci-file-name').textContent = input.files[0].name;
}

// ── Delete modal ──
function confirmDelete(url, label) {
    document.getElementById('form-delete').action = url;
    document.getElementById('delete-confirm-text').textContent = label + ' akan dihapus permanen.';
    document.getElementById('modal-delete').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeDeleteModal() {
    document.getElementById('modal-delete').classList.add('hidden');
    document.body.style.overflow = '';
}

// ── Auto-open on validation error ──
@if(old('_form') === 'create')
    document.addEventListener('DOMContentLoaded', openCreateModal);
@endif
@php
    $editItem = null;
    if (old('_form') === 'edit' && old('edit_id')) {
        $editItem = ['nama'=>old('nama'),'jenis'=>old('jenis'),'kelas_id'=>old('kelas_id'),
            'mata_pelajaran_id'=>old('mata_pelajaran_id'),'guru_id'=>old('guru_id'),
            'tanggal'=>old('tanggal'),'jam_mulai'=>old('jam_mulai'),'jam_selesai'=>old('jam_selesai'),
            'ruangan'=>old('ruangan'),'keterangan'=>old('keterangan'),'aktif'=>old('aktif','1')==='1'];
    }
@endphp
@if($editItem)
    document.addEventListener('DOMContentLoaded', function () {
        openEditModal({{ (int) old('edit_id') }}, @json($editItem));
    });
@endif
</script>

@endsection
