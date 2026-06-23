<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendaftaran PPDB — SMK Muhammadiyah Sempor</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --teal: #1B7A8A; --teal-dark: #145F6E; --teal-deeper: #0D4A57;
            --teal-light: #E0F4F7; --navy: #112D3E; --gold: #E6920A;
        }
        body { font-family: 'Inter', sans-serif; background: #F0F7F9; }

        /* Steps sidebar */
        .step-num {
            width: 2rem; height: 2rem; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem; font-weight: 700; flex-shrink: 0;
            border: 2px solid #CBD5E1; color: #94A3B8; background: #fff;
            transition: all .2s;
        }
        .step-num.done   { background: var(--teal); border-color: var(--teal); color: #fff; }
        .step-num.active { background: var(--teal); border-color: var(--teal); color: #fff; }

        /* Panels */
        .step-panel { display: none; }
        .step-panel.active { display: block; }

        /* Inputs */
        input[type="text"], input[type="email"], input[type="password"],
        input[type="number"], input[type="date"], select, textarea {
            width: 100%; border: 1px solid #CBD5E1; border-radius: .5rem;
            padding: .5rem .75rem; font-size: .875rem; outline: none;
            transition: border-color .15s, box-shadow .15s; background: #fff;
        }
        input:focus, select:focus, textarea:focus {
            border-color: var(--teal); box-shadow: 0 0 0 3px rgba(27,122,138,.12);
        }
        label { font-size: .8rem; font-weight: 600; color: #374151; margin-bottom: .3rem; display: block; }
        .field-group { margin-bottom: 1rem; }
        .required::after { content: ' *'; color: #EF4444; }

        /* Buttons */
        .btn-next {
            background: var(--teal); color: #fff; font-weight: 700;
            padding: .65rem 2rem; border-radius: .75rem; border: none;
            cursor: pointer; transition: background .15s, transform .1s;
            display: inline-flex; align-items: center; gap: .5rem; font-size: .875rem;
        }
        .btn-next:hover { background: var(--teal-dark); transform: translateY(-1px); }

        .btn-back {
            background: #fff; color: #374151; font-weight: 700;
            padding: .65rem 2rem; border-radius: .75rem; border: 1.5px solid #CBD5E1;
            cursor: pointer; transition: background .15s, border-color .15s;
            display: inline-flex; align-items: center; gap: .5rem; font-size: .875rem;
            text-decoration: none;
        }
        .btn-back:hover { background: #F8FAFC; border-color: #94A3B8; }

        .section-title { font-size: 1.15rem; font-weight: 800; color: var(--navy); margin-bottom: .2rem; }
        .section-sub   { font-size: .8rem; color: #64748B; margin-bottom: 1.25rem; }
        .divider { border: none; border-top: 1px solid #E2E8F0; margin: 1.25rem 0; }
        .file-hint { font-size: .75rem; color: #94A3B8; margin-top: .25rem; }
        .error-msg { font-size: .75rem; color: #EF4444; margin-top: .25rem; }
        .group-label { font-size: .7rem; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: .08em; margin-bottom: .75rem; }

        /* ── Choices.js overrides ── */
        .choices { margin-bottom: 0; }
        .choices__inner {
            border: 1px solid #CBD5E1 !important;
            border-radius: .5rem !important;
            background: #fff !important;
            padding: .4rem .6rem !important;
            font-size: .875rem !important;
            min-height: unset !important;
        }
        .choices.is-focused .choices__inner,
        .choices.is-open    .choices__inner {
            border-color: var(--teal) !important;
            box-shadow: 0 0 0 3px rgba(27,122,138,.12) !important;
        }
        .choices__list--dropdown,
        .choices__list[aria-expanded] {
            border: 1px solid #CBD5E1 !important;
            border-radius: .5rem !important;
            box-shadow: 0 8px 24px rgba(17,45,62,.10) !important;
            z-index: 9999 !important;
        }
        .choices__list--dropdown .choices__item--selectable.is-highlighted {
            background: var(--teal-light) !important;
            color: var(--teal-dark) !important;
        }
        .choices__list--single { padding: 0 !important; }
        .choices__placeholder { color: #94A3B8 !important; }
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
</head>
<body>

{{-- ══ Header ══ --}}
<div style="background: linear-gradient(135deg, var(--teal-deeper), var(--teal));" class="py-4 px-4 shadow">
    <div class="max-w-5xl mx-auto flex items-center gap-3">
        <img src="{{ asset('image/smk.png') }}" alt="Logo" class="w-10 h-10 object-contain bg-white/90 rounded-xl p-0.5">
        <div>
            <p class="text-white font-extrabold text-sm leading-tight">SPMB SMK MUHAMMADIYAH SEMPOR</p>
            <p class="text-white/70 text-xs">Sistem Penerimaan Murid Baru</p>
        </div>
        <a href="{{ url('/') }}" class="ml-auto text-xs text-white/70 hover:text-white transition">← Beranda</a>
    </div>
</div>

{{-- ══ Alerts ══ --}}
<div class="max-w-5xl mx-auto px-4 mt-4">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl px-4 py-3 mb-3 flex gap-2">
            <span>✅</span> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-3">
            <p class="font-semibold mb-1">Terdapat kesalahan:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif
</div>

@php
    $profile ??= null;
    // Gunakan $requestedStep yang sudah divalidasi controller,
    // fallback ke request('step') untuk backward compat
    $currentStep = isset($requestedStep)
        ? (int) $requestedStep
        : (int) request('step', $profile ? ($profile->current_step ?? 2) : 1);
    $steps = [
        [1, 'Pendaftaran',           'Buat akun pendaftaran'],
        [2, 'Didaftarkan Oleh',      'Siapa yang mendaftarkan'],
        [3, 'Pendidikan',            'Jurusan & sistem pendidikan'],
        [4, 'Data Diri Calon Siswa', 'Lengkapi data diri'],
        [5, 'Kesehatan',             'Informasi kesehatan'],
        [6, 'Dokumen',               'Unggah dokumen'],
        [7, 'Informasi Tambahan',    'Prestasi & lainnya'],
        [8, 'Data Orang Tua',        'Data ayah & ibu'],
        [9, 'Selesai',               'Pendaftaran berhasil'],
    ];
@endphp

<div class="max-w-5xl mx-auto px-4 pb-12 flex flex-col lg:flex-row gap-6 mt-5">

    {{-- ══ Sidebar ══ --}}
    <aside class="lg:w-64 shrink-0">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sticky top-4">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Langkah</p>
            <div class="space-y-1">
                @foreach($steps as [$num, $label, $sub])
                @php
                    $isDone   = $currentStep > $num;
                    $isActive = $currentStep === $num;
                    $canVisit = $profile && $num <= ($profile->current_step ?? 1);
                @endphp
                <div class="flex gap-3 items-start px-2 py-2 rounded-lg {{ $isActive ? 'bg-teal-50' : '' }}">
                    @if($canVisit && !$isActive)
                    <a href="{{ route('kandidat.create', ['step' => $num]) }}"
                       class="step-num {{ $isDone ? 'done' : '' }} hover:ring-2 hover:ring-teal-300 transition shrink-0"
                       title="Kembali ke {{ $label }}">
                        @if($isDone) ✓ @else {{ $num }} @endif
                    </a>
                    @else
                    <div class="step-num {{ $isDone ? 'done' : ($isActive ? 'active' : '') }}">
                        @if($isDone) ✓ @else {{ $num }} @endif
                    </div>
                    @endif
                    <div class="min-w-0">
                        @if($canVisit && !$isActive)
                        <a href="{{ route('kandidat.create', ['step' => $num]) }}"
                           class="text-xs font-semibold {{ $isActive ? 'text-teal-700' : 'text-slate-500' }} hover:text-teal-700 leading-tight block">{{ $label }}</a>
                        @else
                        <p class="text-xs font-semibold {{ $isActive ? 'text-teal-700' : 'text-slate-500' }} leading-tight">{{ $label }}</p>
                        @endif
                        <p class="text-xs text-slate-400 truncate hidden lg:block">{{ $sub }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </aside>

    {{-- ══ Main Card ══ --}}
    <main class="flex-1 min-w-0">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:p-8">

{{-- ════════════════════════════════════════
     STEP 1 — Pendaftaran (Akun)
════════════════════════════════════════ --}}
<div class="step-panel {{ $currentStep === 1 ? 'active' : '' }}">
    <p class="section-title">Pendaftaran</p>
    <p class="section-sub">Buat akun untuk melanjutkan proses pendaftaran</p>

    @if($profile)
    {{-- Akun sudah dibuat, tampilkan info saja --}}
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl px-4 py-3 mb-5 flex gap-2">
        <span>✅</span>
        <div>
            <p class="font-semibold">Akun sudah dibuat</p>
            <p class="mt-0.5">Akun atas nama <strong>{{ $profile->nama_lengkap }}</strong> sudah berhasil dibuat sebelumnya.</p>
        </div>
    </div>
    <div class="flex justify-end mt-6">
        <a href="{{ route('kandidat.create', ['step' => 2]) }}" class="btn-next">Selanjutnya →</a>
    </div>
    @else
    <form method="POST" action="{{ route('kandidat.store') }}">
        @csrf
        <input type="hidden" name="step" value="1">

        <div class="field-group">
            <label class="required">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                   placeholder="contoh: Ahmad Fauzi" required>
            @error('nama_lengkap')<p class="error-msg">{{ $message }}</p>@enderror
        </div>

        <div class="field-group">
            <label class="required">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   placeholder="contoh: ahmad@example.com" required>
            @error('email')<p class="error-msg">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="field-group">
                <label class="required">Kata Sandi</label>
                <div class="relative">
                    <input type="password" name="password" id="password" placeholder="Min. 8 karakter" required style="padding-right:2.5rem;">
                    <button type="button" onclick="togglePassword('password','eye-pass')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition">
                        <svg id="eye-pass" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('password')<p class="error-msg">{{ $message }}</p>@enderror
            </div>
            <div class="field-group">
                <label class="required">Konfirmasi Kata Sandi</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi kata sandi" required style="padding-right:2.5rem;">
                    <button type="button" onclick="togglePassword('password_confirmation','eye-confirm')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition">
                        <svg id="eye-confirm" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="flex justify-end mt-6">
            <button type="submit" class="btn-next">Selanjutnya →</button>
        </div>
    </form>
    @endif
</div>

{{-- ════════════════════════════════════════
     STEP 2 — Didaftarkan Oleh
════════════════════════════════════════ --}}
<div class="step-panel {{ $currentStep === 2 ? 'active' : '' }}">
    <p class="section-title">Didaftarkan Oleh</p>
    <p class="section-sub">Pilih siapa yang melakukan pendaftaran ini</p>

    <form method="POST" action="{{ route('kandidat.store') }}">
        @csrf
        <input type="hidden" name="step" value="2">

        <div class="field-group">
            <label class="required">Didaftarkan Oleh</label>
            <select name="didaftarkan_oleh" required>
                <option value="">-- Pilih --</option>
                @foreach(\App\Models\KandidatProfile::$didaftarkanOleh as $val => $label)
                    <option value="{{ $val }}" {{ old('didaftarkan_oleh', $profile?->didaftarkan_oleh) === $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('didaftarkan_oleh')<p class="error-msg">{{ $message }}</p>@enderror
        </div>

        <div class="flex justify-between mt-6">
            <a href="{{ route('kandidat.create', ['step' => 1]) }}" class="btn-back">← Kembali</a>
            <button type="submit" class="btn-next">Selanjutnya →</button>
        </div>
    </form>
</div>

{{-- ════════════════════════════════════════
     STEP 3 — Pendidikan
════════════════════════════════════════ --}}
<div class="step-panel {{ $currentStep === 3 ? 'active' : '' }}">
    <p class="section-title">Pendidikan</p>
    <p class="section-sub">Pilih jurusan, program atau sistem pendidikan</p>

    <form method="POST" action="{{ route('kandidat.store') }}">
        @csrf
        <input type="hidden" name="step" value="3">

        <div class="field-group">
            <label class="required">Jurusan</label>
            <select name="jurusan" required>
                <option value="">Pilih Jurusan</option>
                @foreach(\App\Models\KandidatProfile::$jurusan as $val => $label)
                    <option value="{{ $val }}" {{ old('jurusan', $profile?->jurusan) === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('jurusan')<p class="error-msg">{{ $message }}</p>@enderror
        </div>

        <div class="field-group">
            <label class="required">Sistem Pendidikan</label>
            <select name="sistem_pendidikan" required>
                <option value="">Pilih Sistem Pendidikan</option>
                @foreach(\App\Models\KandidatProfile::$sistemPendidikan as $val => $label)
                    <option value="{{ $val }}" {{ old('sistem_pendidikan', $profile?->sistem_pendidikan) === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('sistem_pendidikan')<p class="error-msg">{{ $message }}</p>@enderror
        </div>

        <div class="flex justify-between mt-6">
            <a href="{{ route('kandidat.create', ['step' => 2]) }}" class="btn-back">← Kembali</a>
            <button type="submit" class="btn-next">Selanjutnya →</button>
        </div>
    </form>
</div>

{{-- ════════════════════════════════════════
     STEP 4 — Data Diri Calon Siswa
════════════════════════════════════════ --}}
<div class="step-panel {{ $currentStep === 4 ? 'active' : '' }}">
    <p class="section-title">Data Diri Calon Siswa</p>
    <p class="section-sub">Lengkapi data diri calon siswa</p>

    <form method="POST" action="{{ route('kandidat.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="step" value="4">

        {{-- Identitas --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="field-group">
                <label class="required">NIK / KIA (Sesuai KK)</label>
                <input type="text" name="nik" value="{{ old('nik', $profile?->nik) }}"
                       placeholder="contoh: 3305xxxxxxxxxx16"
                       minlength="16" maxlength="16" pattern="\d{16}"
                       title="NIK harus tepat 16 digit angka" required>
                <p class="file-hint">Harus tepat 16 digit angka</p>
                @error('nik')<p class="error-msg">{{ $message }}</p>@enderror
            </div>
            <div class="field-group">
                <label class="required">NISN</label>
                <input type="text" name="nisn" value="{{ old('nisn', $profile?->nisn) }}" placeholder="contoh: 0012345678" required>
            </div>
            <div class="field-group">
                <label class="required">Nama Lengkap (Sesuai Ijazah)</label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $profile?->nama_lengkap) }}" placeholder="contoh: Ahmad Fauzi" required>
            </div>
            <div class="field-group">
                <label>Nama Panggilan</label>
                <input type="text" name="nama_panggilan" value="{{ old('nama_panggilan', $profile?->nama_panggilan) }}" placeholder="contoh: Fauzi">
            </div>
            <div class="field-group">
                <label class="required">Kewarganegaraan</label>
                <input type="text" name="kewarganegaraan" value="{{ old('kewarganegaraan', $profile?->kewarganegaraan ?? 'Indonesia') }}" required>
            </div>
            <div class="field-group">
                <label class="required">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $profile?->tempat_lahir) }}" placeholder="contoh: Kebumen" required>
            </div>
            <div class="field-group">
                <label class="required">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $profile?->tanggal_lahir?->format('Y-m-d')) }}" required>
            </div>
            <div class="field-group">
                <label class="required">Jenis Kelamin</label>
                <select name="jenis_kelamin" required>
                    <option value="">Pilih</option>
                    @foreach(\App\Models\KandidatProfile::$jenisKelamin as $v => $l)
                        <option value="{{ $v }}" {{ old('jenis_kelamin', $profile?->jenis_kelamin) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field-group">
                <label class="required">Agama</label>
                <select name="agama" required>
                    <option value="">Pilih</option>
                    @foreach(\App\Models\KandidatProfile::$agama as $v => $l)
                        <option value="{{ $v }}" {{ old('agama', $profile?->agama) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field-group">
                <label class="required">Status Keluarga</label>
                <select name="status_keluarga" required>
                    <option value="">Pilih</option>
                    @foreach(\App\Models\KandidatProfile::$statusKeluarga as $v => $l)
                        <option value="{{ $v }}" {{ old('status_keluarga', $profile?->status_keluarga) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Saudara --}}
        <hr class="divider">
        <p class="group-label">Status Dalam Keluarga</p>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div class="field-group">
                <label class="required">Anak Ke</label>
                <input type="number" name="anak_ke" value="{{ old('anak_ke', $profile?->anak_ke) }}" min="1" placeholder="contoh: 2" required>
            </div>
            <div class="field-group">
                <label class="required">Dari Saudara</label>
                <input type="number" name="dari_saudara" value="{{ old('dari_saudara', $profile?->dari_saudara) }}" min="1" placeholder="contoh: 3" required>
            </div>
            <div class="field-group">
                <label class="required">Total Saudara (Kandung)</label>
                <input type="number" name="total_saudara_kandung" value="{{ old('total_saudara_kandung', $profile?->total_saudara_kandung) }}" min="0" placeholder="contoh: 3" required>
            </div>
            <div class="field-group">
                <label>Total Saudara (Tiri)</label>
                <input type="number" name="total_saudara_tiri" value="{{ old('total_saudara_tiri', $profile?->total_saudara_tiri) }}" min="0" placeholder="contoh: 0">
            </div>
            <div class="field-group">
                <label>Total Saudara (Angkat)</label>
                <input type="number" name="total_saudara_angkat" value="{{ old('total_saudara_angkat', $profile?->total_saudara_angkat) }}" min="0" placeholder="contoh: 0">
            </div>
        </div>

        {{-- Asal Sekolah --}}
        <hr class="divider">
        <p class="group-label">Asal Sekolah</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="field-group">
                <label class="required">Asal Sekolah</label>
                <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah', $profile?->asal_sekolah) }}" placeholder="contoh: SMP N 1 Sempor" required>
            </div>
            <div class="field-group">
                <label class="required">Lama Belajar (Tahun)</label>
                <input type="number" name="lama_belajar" value="{{ old('lama_belajar', $profile?->lama_belajar) }}" min="1" placeholder="contoh: 3" required>
            </div>
            <div class="field-group">
                <label class="required">Nomor Ijazah</label>
                <input type="text" name="nomor_ijazah" value="{{ old('nomor_ijazah', $profile?->nomor_ijazah) }}" placeholder="contoh: FWK1290ds2" required>
            </div>
            <div class="field-group">
                <label class="required">Tanggal Ijazah</label>
                <input type="date" name="tanggal_ijazah" value="{{ old('tanggal_ijazah', $profile?->tanggal_ijazah?->format('Y-m-d')) }}" required>
            </div>
            <div class="field-group">
                <label class="required">NPSN</label>
                <input type="text" name="npsn" value="{{ old('npsn', $profile?->npsn) }}" placeholder="contoh: 21322345" required>
            </div>
        </div>

        {{-- KIP --}}
        <hr class="divider">
        <p class="group-label">KIP</p>
        @php $penerimaKip = old('penerima_kip', $profile?->penerima_kip ? '1' : '0') @endphp
        <div class="field-group">
            <label>Penerima KIP</label>
            <div class="flex gap-5 mt-1">
                <label class="flex items-center gap-2 font-normal text-sm text-slate-600 cursor-pointer">
                    <input type="radio" name="penerima_kip" value="1" class="w-auto" {{ $penerimaKip === '1' ? 'checked' : '' }}> Ya
                </label>
                <label class="flex items-center gap-2 font-normal text-sm text-slate-600 cursor-pointer">
                    <input type="radio" name="penerima_kip" value="0" class="w-auto" {{ $penerimaKip !== '1' ? 'checked' : '' }}> Tidak
                </label>
            </div>
        </div>
        <div class="field-group" id="nomor-kip-wrap" style="{{ $penerimaKip === '1' ? '' : 'display:none' }}">
            <label>Nomor KIP</label>
            <input type="text" name="nomor_kip" value="{{ old('nomor_kip', $profile?->nomor_kip) }}" placeholder="contoh: 12345678">
        </div>

        {{-- Lainnya --}}
        <hr class="divider">
        <p class="group-label">Informasi Lainnya</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="field-group">
                <label class="required">Status Tinggal</label>
                <select name="status_tinggal" required>
                    <option value="">Pilih</option>
                    @foreach(\App\Models\KandidatProfile::$statusTinggal as $v => $l)
                        <option value="{{ $v }}" {{ old('status_tinggal', $profile?->status_tinggal) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field-group">
                <label class="required">Bahasa Sehari-hari di Rumah</label>
                <input type="text" name="bahasa_sehari_hari" value="{{ old('bahasa_sehari_hari', $profile?->bahasa_sehari_hari) }}" placeholder="contoh: Bahasa Indonesia" required>
            </div>
            <div class="field-group">
                <label>Saudara Kandung yang Sekolah di Sini</label>
                <input type="text" name="saudara_di_sekolah" value="{{ old('saudara_di_sekolah', $profile?->saudara_di_sekolah) }}" placeholder="contoh: John Doe Kelas 2A">
            </div>
            <div class="field-group">
                <label class="required">Moda Transportasi</label>
                <select name="moda_transportasi" required>
                    <option value="">Pilih Moda Transportasi</option>
                    @foreach(\App\Models\KandidatProfile::$modaTransportasi as $v => $l)
                        <option value="{{ $v }}" {{ old('moda_transportasi', $profile?->moda_transportasi) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field-group">
                <label class="required">Jarak Menuju Sekolah (KM)</label>
                <input type="number" name="jarak_sekolah_km" value="{{ old('jarak_sekolah_km', $profile?->jarak_sekolah_km) }}" step="0.1" min="0" placeholder="contoh: 10" required>
            </div>
            <div class="field-group">
                <label class="required">Waktu Tempuh (Jam)</label>
                <input type="number" name="waktu_tempuh_jam" value="{{ old('waktu_tempuh_jam', $profile?->waktu_tempuh_jam) }}" step="0.1" min="0" placeholder="contoh: 0.5" required>
            </div>
        </div>

        <div class="field-group">
            <label class="{{ $profile?->foto ? '' : 'required' }}">Pas Foto 3×4</label>
            @if($profile?->foto)
                <div class="mb-2">
                    <img id="foto-preview" src="{{ asset('storage/' . $profile->foto) }}"
                         alt="Pas Foto" class="w-24 h-32 object-cover rounded-lg border border-slate-200 shadow-sm">
                </div>
                <p class="text-xs text-teal-700 mb-1">✓ Foto sudah diunggah. Pilih file baru untuk mengganti.</p>
            @else
                <div class="mb-2 hidden" id="foto-preview-wrap">
                    <img id="foto-preview" src="" alt="Preview Foto"
                         class="w-24 h-32 object-cover rounded-lg border border-slate-200 shadow-sm">
                </div>
            @endif
            <input type="file" name="foto" id="foto-input" accept=".jpg,.jpeg,.png" {{ $profile?->foto ? '' : 'required' }}
                   onchange="previewFoto(this)"
                   class="file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
            <p class="file-hint">Maks. 10MB, format: JPG, JPEG, PNG</p>
        </div>

        {{-- Kontak --}}
        <hr class="divider">
        <p class="group-label">Kontak</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="field-group"><label class="required">No HP</label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $profile?->no_hp) }}"
                       placeholder="contoh: 081234567890"
                       pattern="08\d{8,13}" minlength="10" maxlength="15"
                       title="No HP harus diawali 08, minimal 10 angka" required>
                <p class="file-hint">Diawali 08, minimal 10 angka</p>
                @error('no_hp')<p class="error-msg">{{ $message }}</p>@enderror
            </div>
            <div class="field-group">
                <label>Alamat Email</label>
                <input type="email" name="email_kontak" value="{{ old('email_kontak', $profile?->email ?? $profile?->user?->email) }}" placeholder="contoh: siswa@example.com">
            </div>
        </div>

        {{-- Alamat --}}
        <hr class="divider">
        <p class="group-label">Alamat</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="field-group">
                <label class="required">Provinsi</label>
                <input type="text" name="provinsi" value="{{ old('provinsi', $profile?->provinsi) }}" placeholder="contoh: Jawa Tengah" required>
            </div>
            <div class="field-group">
                <label class="required">Kabupaten / Kota</label>
                <input type="text" name="kabupaten" value="{{ old('kabupaten', $profile?->kabupaten) }}" placeholder="contoh: Kebumen" required>
            </div>
            <div class="field-group">
                <label class="required">Kecamatan</label>
                <input type="text" name="kecamatan" value="{{ old('kecamatan', $profile?->kecamatan) }}" placeholder="contoh: Sempor" required>
            </div>
            <div class="field-group">
                <label class="required">Desa / Kelurahan</label>
                <input type="text" name="desa" value="{{ old('desa', $profile?->desa) }}" placeholder="contoh: Sampang" required>
            </div>
            <div class="field-group">
                <label class="required">RT</label>
                <input type="text" name="rt" value="{{ old('rt', $profile?->rt) }}" placeholder="contoh: 02" required>
            </div>
            <div class="field-group">
                <label class="required">RW</label>
                <input type="text" name="rw" value="{{ old('rw', $profile?->rw) }}" placeholder="contoh: 03" required>
            </div>
        </div>
        <div class="field-group">
            <label class="required">Alamat Lengkap</label>
            <textarea name="alamat_lengkap" rows="3" placeholder="contoh: Jl. Mawar No.5, RT 02/03" required>{{ old('alamat_lengkap', $profile?->alamat_lengkap) }}</textarea>
        </div>

        <div class="flex justify-between mt-6">
            <a href="{{ route('kandidat.create', ['step' => 3]) }}" class="btn-back">← Kembali</a>
            <button type="submit" class="btn-next">Selanjutnya →</button>
        </div>
    </form>
</div>

{{-- ════════════════════════════════════════
     STEP 5 — Kesehatan
════════════════════════════════════════ --}}
<div class="step-panel {{ $currentStep === 5 ? 'active' : '' }}">
    <p class="section-title">Kesehatan</p>
    <p class="section-sub">Lengkapi informasi kesehatan anda (opsional)</p>

    <form method="POST" action="{{ route('kandidat.store') }}">
        @csrf
        <input type="hidden" name="step" value="5">

        <div class="field-group">
            <label>Riwayat Kesehatan</label>
            <textarea name="riwayat_kesehatan" rows="3" placeholder="contoh: Pernah dirawat karena sakit asma">{{ old('riwayat_kesehatan', $profile?->riwayat_kesehatan) }}</textarea>
        </div>
        <div class="field-group">
            <label>Disabilitas</label>
            <select name="disabilitas">
                <option value="">Pilih</option>
                @foreach(\App\Models\KandidatProfile::$disabilitas as $v => $l)
                    <option value="{{ $v }}" {{ old('disabilitas', $profile?->disabilitas) === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div class="field-group">
                <label>Tinggi Badan (cm)</label>
                <input type="number" name="tinggi_badan" value="{{ old('tinggi_badan', $profile?->tinggi_badan) }}" step="0.1" min="0" placeholder="contoh: 160">
            </div>
            <div class="field-group">
                <label>Berat Badan (kg)</label>
                <input type="number" name="berat_badan" value="{{ old('berat_badan', $profile?->berat_badan) }}" step="0.1" min="0" placeholder="contoh: 55">
            </div>
        </div>

        <div class="flex justify-between mt-6">
            <a href="{{ route('kandidat.create', ['step' => 4]) }}" class="btn-back">← Kembali</a>
            <button type="submit" class="btn-next">Selanjutnya →</button>
        </div>
    </form>
</div>

{{-- ════════════════════════════════════════
     STEP 6 — Dokumen
════════════════════════════════════════ --}}
<div class="step-panel {{ $currentStep === 6 ? 'active' : '' }}">
    <p class="section-title">Dokumen</p>
    <p class="section-sub">Lengkapi dokumen anda</p>

    <form method="POST" action="{{ route('kandidat.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="step" value="6">

        <div class="field-group">
            <label class="{{ $profile?->dokumen_kk ? '' : 'required' }}">Kartu Keluarga</label>
            @if($profile?->dokumen_kk)
                <p class="text-xs text-teal-700 mb-1">✓ KK sudah diunggah. Biarkan kosong jika tidak ingin mengganti.</p>
            @endif
            <input type="file" name="dokumen_kk" accept=".pdf,.jpg,.jpeg,.png"
                   {{ $profile?->dokumen_kk ? '' : 'required' }}
                   class="file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
            <p class="file-hint">Maks. 10MB, format: PDF, JPG, JPEG, PNG</p>
            @error('dokumen_kk')<p class="error-msg">{{ $message }}</p>@enderror
        </div>

        <div class="field-group">
            <label class="{{ $profile?->dokumen_ijazah ? '' : 'required' }}">Ijazah / Keterangan Lulus</label>
            @if($profile?->dokumen_ijazah)
                <p class="text-xs text-teal-700 mb-1">✓ Ijazah sudah diunggah. Biarkan kosong jika tidak ingin mengganti.</p>
            @endif
            <input type="file" name="dokumen_ijazah" accept=".pdf,.jpg,.jpeg,.png"
                   {{ $profile?->dokumen_ijazah ? '' : 'required' }}
                   class="file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
            <p class="file-hint">Maks. 10MB, format: PDF, JPG, JPEG, PNG</p>
            @error('dokumen_ijazah')<p class="error-msg">{{ $message }}</p>@enderror
        </div>

        <div class="flex justify-between mt-6">
            <a href="{{ route('kandidat.create', ['step' => 5]) }}" class="btn-back">← Kembali</a>
            <button type="submit" class="btn-next">Selanjutnya →</button>
        </div>
    </form>
</div>

{{-- ════════════════════════════════════════
     STEP 7 — Informasi Tambahan
════════════════════════════════════════ --}}
<div class="step-panel {{ $currentStep === 7 ? 'active' : '' }}">
    <p class="section-title">Informasi Tambahan</p>
    <p class="section-sub">Lengkapi informasi tambahan anda (opsional)</p>

    <form method="POST" action="{{ route('kandidat.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="step" value="7">

        <div id="prestasi-list">
            <div class="prestasi-item border border-slate-200 rounded-xl p-4 mb-3 bg-slate-50/70">
                <div class="field-group">
                    <label>Prestasi</label>
                    <input type="text" name="prestasi[0][nama]" placeholder="contoh: Juara 1 Olimpiade Matematika">
                </div>
                <div class="field-group mb-0">
                    <label>Dokumen Prestasi</label>
                    <input type="file" name="prestasi[0][dokumen]" accept=".pdf,.jpg,.jpeg,.png"
                           class="file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                    <p class="file-hint">Maks. 10MB. Wajib diisi jika memiliki prestasi. Maks panjang nama file kurang dari 50 karakter.</p>
                </div>
            </div>
        </div>

        <button type="button" onclick="tambahPrestasi()"
                class="text-sm font-semibold px-4 py-2 rounded-lg border border-teal-300 text-teal-700 bg-teal-50 hover:bg-teal-100 transition mb-2">
            + Tambah Prestasi
        </button>

        <div class="flex justify-between mt-4">
            <a href="{{ route('kandidat.create', ['step' => 6]) }}" class="btn-back">← Kembali</a>
            <button type="submit" class="btn-next">Selanjutnya →</button>
        </div>
    </form>
</div>

{{-- ════════════════════════════════════════
     STEP 8 — Data Orang Tua
════════════════════════════════════════ --}}
<div class="step-panel {{ $currentStep === 8 ? 'active' : '' }}">
    <p class="section-title">Data Orang Tua</p>
    <p class="section-sub">Lengkapi data orang tua anda</p>

    <form method="POST" action="{{ route('kandidat.store') }}">
        @csrf
        <input type="hidden" name="step" value="8">

        {{-- Ayah --}}
        <p class="group-label">Data Ayah</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="field-group"><label class="required">Nama Ayah</label>
                <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $profile?->nama_ayah) }}" placeholder="contoh: Lesmana" required></div>
            <div class="field-group"><label class="required">NIK Ayah</label>
                <input type="text" name="nik_ayah" value="{{ old('nik_ayah', $profile?->nik_ayah) }}"
                       placeholder="contoh: 3305xxxxxxxxxx16"
                       minlength="16" maxlength="16" pattern="\d{16}"
                       title="NIK harus tepat 16 digit angka" required>
                <p class="file-hint">Harus tepat 16 digit angka</p>
                @error('nik_ayah')<p class="error-msg">{{ $message }}</p>@enderror
            </div>
            <div class="field-group"><label class="required">Pendidikan Ayah</label>
                <select name="pendidikan_ayah" required><option value="">Pilih Pendidikan Ayah</option>
                    @foreach(\App\Models\KandidatProfile::$pendidikanOrtu as $v => $l)
                        <option value="{{ $v }}" {{ old('pendidikan_ayah', $profile?->pendidikan_ayah) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select></div>
            <div class="field-group"><label class="required">Pekerjaan Ayah</label>
                <select name="pekerjaan_ayah" required><option value="">Pilih Pekerjaan Ayah</option>
                    @foreach(\App\Models\KandidatProfile::$pekerjaanOrtu as $v => $l)
                        <option value="{{ $v }}" {{ old('pekerjaan_ayah', $profile?->pekerjaan_ayah) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select></div>
            <div class="field-group"><label class="required">Status Pernikahan Ayah</label>
                <select name="status_pernikahan_ayah" required><option value="">Pilih Status Pernikahan</option>
                    @foreach(\App\Models\KandidatProfile::$statusPernikahan as $v => $l)
                        <option value="{{ $v }}" {{ old('status_pernikahan_ayah', $profile?->status_pernikahan_ayah) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select></div>
            <div class="field-group"><label class="required">No HP / WhatsApp Ayah</label>
                <input type="text" name="no_hp_ayah" value="{{ old('no_hp_ayah', $profile?->no_hp_ayah) }}"
                       placeholder="contoh: 081234567890"
                       pattern="08\d{8,13}" minlength="10" maxlength="15"
                       title="No HP harus diawali 08, minimal 10 angka" required>
                <p class="file-hint">Diawali 08, minimal 10 angka</p>
                @error('no_hp_ayah')<p class="error-msg">{{ $message }}</p>@enderror
            </div>
            <div class="field-group"><label class="required">Tempat Lahir</label>
                <input type="text" name="tempat_lahir_ayah" value="{{ old('tempat_lahir_ayah', $profile?->tempat_lahir_ayah) }}" placeholder="contoh: Jakarta" required></div>
            <div class="field-group"><label class="required">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir_ayah" value="{{ old('tanggal_lahir_ayah', $profile?->tanggal_lahir_ayah?->format('Y-m-d')) }}" required></div>
            <div class="field-group"><label class="required">Kewarganegaraan Ayah</label>
                <input type="text" name="kewarganegaraan_ayah" value="{{ old('kewarganegaraan_ayah', $profile?->kewarganegaraan_ayah ?? 'Indonesia') }}" required></div>
            <div class="field-group"><label class="required">Agama Ayah</label>
                <select name="agama_ayah" required><option value="">Pilih Agama</option>
                    @foreach(\App\Models\KandidatProfile::$agama as $v => $l)
                        <option value="{{ $v }}" {{ old('agama_ayah', $profile?->agama_ayah) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select></div>
        </div>

        <hr class="divider">

        {{-- Ibu --}}
        <p class="group-label">Data Ibu</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="field-group"><label class="required">Nama Ibu</label>
                <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $profile?->nama_ibu) }}" placeholder="contoh: Yuni" required></div>
            <div class="field-group"><label class="required">NIK Ibu</label>
                <input type="text" name="nik_ibu" value="{{ old('nik_ibu', $profile?->nik_ibu) }}"
                       placeholder="contoh: 3305xxxxxxxxxx16"
                       minlength="16" maxlength="16" pattern="\d{16}"
                       title="NIK harus tepat 16 digit angka" required>
                <p class="file-hint">Harus tepat 16 digit angka</p>
                @error('nik_ibu')<p class="error-msg">{{ $message }}</p>@enderror
            </div>
            <div class="field-group"><label class="required">Pendidikan Ibu</label>
                <select name="pendidikan_ibu" required><option value="">Pilih Pendidikan Ibu</option>
                    @foreach(\App\Models\KandidatProfile::$pendidikanOrtu as $v => $l)
                        <option value="{{ $v }}" {{ old('pendidikan_ibu', $profile?->pendidikan_ibu) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select></div>
            <div class="field-group"><label class="required">Pekerjaan Ibu</label>
                <select name="pekerjaan_ibu" required><option value="">Pilih Pekerjaan Ibu</option>
                    @foreach(\App\Models\KandidatProfile::$pekerjaanOrtu as $v => $l)
                        <option value="{{ $v }}" {{ old('pekerjaan_ibu', $profile?->pekerjaan_ibu) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select></div>
            <div class="field-group"><label class="required">Status Pernikahan Ibu</label>
                <select name="status_pernikahan_ibu" required><option value="">Pilih Status Pernikahan</option>
                    @foreach(\App\Models\KandidatProfile::$statusPernikahan as $v => $l)
                        <option value="{{ $v }}" {{ old('status_pernikahan_ibu', $profile?->status_pernikahan_ibu) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select></div>
            <div class="field-group"><label class="required">No HP / WhatsApp Ibu</label>
                <input type="text" name="no_hp_ibu" value="{{ old('no_hp_ibu', $profile?->no_hp_ibu) }}"
                       placeholder="contoh: 081234567890"
                       pattern="08\d{8,13}" minlength="10" maxlength="15"
                       title="No HP harus diawali 08, minimal 10 angka" required>
                <p class="file-hint">Diawali 08, minimal 10 angka</p>
                @error('no_hp_ibu')<p class="error-msg">{{ $message }}</p>@enderror
            </div>
            <div class="field-group"><label class="required">Tempat Lahir</label>
                <input type="text" name="tempat_lahir_ibu" value="{{ old('tempat_lahir_ibu', $profile?->tempat_lahir_ibu) }}" placeholder="contoh: Jakarta" required></div>
            <div class="field-group"><label class="required">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir_ibu" value="{{ old('tanggal_lahir_ibu', $profile?->tanggal_lahir_ibu?->format('Y-m-d')) }}" required></div>
            <div class="field-group"><label class="required">Kewarganegaraan Ibu</label>
                <input type="text" name="kewarganegaraan_ibu" value="{{ old('kewarganegaraan_ibu', $profile?->kewarganegaraan_ibu ?? 'Indonesia') }}" required></div>
            <div class="field-group"><label class="required">Agama Ibu</label>
                <select name="agama_ibu" required><option value="">Pilih Agama</option>
                    @foreach(\App\Models\KandidatProfile::$agama as $v => $l)
                        <option value="{{ $v }}" {{ old('agama_ibu', $profile?->agama_ibu) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select></div>
        </div>

        <hr class="divider">
        <p class="group-label">Informasi</p>
        <div class="field-group">
            <label class="required">Penghasilan Orang Tua</label>
            <select name="penghasilan_ortu" required>
                <option value="">Pilih Penghasilan Orang Tua</option>
                @foreach(\App\Models\KandidatProfile::$penghasilanOrtu as $v => $l)
                    <option value="{{ $v }}" {{ old('penghasilan_ortu', $profile?->penghasilan_ortu) === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex justify-between mt-6">
            <a href="{{ route('kandidat.create', ['step' => 7]) }}" class="btn-back">← Kembali</a>
            <button type="submit" class="btn-next" style="background:#10B981;">
                ✓ Kirim Pendaftaran
            </button>
        </div>
    </form>
</div>

        </div>{{-- /card --}}
    </main>
</div>{{-- /flex --}}

<script>
// Preview foto sebelum upload
function previewFoto(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (e) {
        let img = document.getElementById('foto-preview');
        const wrap = document.getElementById('foto-preview-wrap');
        if (wrap) wrap.classList.remove('hidden');
        if (img) img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

// Toggle show/hide password
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    const isHidden = input.type === 'password';

    input.type = isHidden ? 'text' : 'password';

    // Mata terbuka = sedang menampilkan sandi, mata coret = sedang menyembunyikan
    if (isHidden) {
        // tampilkan: mata coret
        icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 012.058-3.368m2.905-2.606A9.96 9.96 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-1.358 2.64M15 12a3 3 0 11-4.243-4.243M3 3l18 18"/>`;
    } else {
        // sembunyikan: mata terbuka
        icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
    }
}

// Toggle nomor KIP
document.querySelectorAll('input[name="penerima_kip"]').forEach(r => {
    r.addEventListener('change', function () {
        const wrap = document.getElementById('nomor-kip-wrap');
        if (wrap) wrap.style.display = this.value === '1' ? 'block' : 'none';
    });
});

// Tambah baris prestasi
let prestasiCount = 1;
function tambahPrestasi() {
    const i = prestasiCount++;
    const div = document.createElement('div');
    div.className = 'prestasi-item border border-slate-200 rounded-xl p-4 mb-3 bg-slate-50/70';
    div.innerHTML = `
        <div class="flex justify-between items-center mb-2">
            <span class="text-xs font-semibold text-slate-500">Prestasi ${i + 1}</span>
            <button type="button" onclick="this.closest('.prestasi-item').remove()"
                class="text-xs text-red-400 hover:text-red-600 font-semibold transition">✕ Hapus</button>
        </div>
        <div class="field-group">
            <label>Prestasi</label>
            <input type="text" name="prestasi[${i}][nama]" placeholder="contoh: Juara 1 Olimpiade"
                   style="width:100%;border:1px solid #CBD5E1;border-radius:.5rem;padding:.5rem .75rem;font-size:.875rem;outline:none;background:#fff;">
        </div>
        <div class="field-group mb-0">
            <label>Dokumen Prestasi</label>
            <input type="file" name="prestasi[${i}][dokumen]" accept=".pdf,.jpg,.jpeg,.png"
                   class="file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700">
            <p class="file-hint">Maks. 10MB. Wajib diisi jika memiliki prestasi.</p>
        </div>
    `;
    document.getElementById('prestasi-list').appendChild(div);
}

// ── Choices.js — inisialisasi semua <select> ──────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const choicesConfig = {
        searchEnabled: true,
        searchPlaceholderValue: 'Cari...',
        itemSelectText: '',
        shouldSort: false,
        allowHTML: false,
    };

    document.querySelectorAll('select').forEach(function (el) {
        // Jangan inisialisasi ulang jika sudah dibungkus Choices
        if (el.closest('.choices')) return;
        new Choices(el, choicesConfig);
    });
});
</script>
</body>
</html>
