@extends('admin.layouts.app')

@section('title', 'Detail Kandidat — ' . $user->name)
@section('breadcrumb', 'Detail Kandidat')

@section('content')
<div class="space-y-5">

    {{-- Back + Aksi --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.kandidat.index') }}"
               class="p-2 rounded-lg text-slate-500 hover:bg-slate-200 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-slate-800">Detail Kandidat</h1>
                <p class="text-sm text-slate-400">Data pendaftaran PPDB</p>
            </div>
        </div>

        {{-- Aksi Terima / Tolak --}}
        @php
            $status = $profile?->status ?? 'draft';
            $badge = match($status) {
                'submitted' => 'bg-amber-100 text-amber-700',
                'diterima'  => 'bg-emerald-100 text-emerald-700',
                'ditolak'   => 'bg-red-100 text-red-700',
                default     => 'bg-slate-100 text-slate-500',
            };
            $label = match($status) {
                'submitted' => 'Menunggu',
                'diterima'  => 'Diterima',
                'ditolak'   => 'Ditolak',
                default     => 'Draft',
            };
        @endphp
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold {{ $badge }}">
                {{ $label }}
            </span>
            @if($status !== 'diterima')
            <form id="form-terima-show" method="POST" action="{{ route('admin.kandidat.accept', $user) }}">
                @csrf @method('PATCH')
                <button type="button"
                        onclick="openConfirmModal({ type: 'terima', name: '{{ addslashes($profile?->nama_lengkap ?? $user->name) }}', formId: 'form-terima-show' })"
                        class="px-4 py-2 rounded-xl text-sm font-bold text-white bg-emerald-500 hover:bg-emerald-600 transition">
                    ✓ Terima
                </button>
            </form>
            @endif
            @if($status !== 'ditolak' && $status !== 'diterima')
            <form id="form-tolak-show" method="POST" action="{{ route('admin.kandidat.reject', $user) }}">
                @csrf @method('PATCH')
                <button type="button"
                        onclick="openConfirmModal({ type: 'tolak', name: '{{ addslashes($profile?->nama_lengkap ?? $user->name) }}', formId: 'form-tolak-show' })"
                        class="px-4 py-2 rounded-xl text-sm font-bold text-white bg-red-500 hover:bg-red-600 transition">
                    ✗ Tolak
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
        <span>✅</span> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-medium">
        <span>❌</span> {{ session('error') }}
    </div>
    @endif

    {{-- Belum ada profil --}}
    @if(!$profile)
    <div class="flex items-center gap-3 px-4 py-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-sm font-medium">
        <span>⚠️</span> Kandidat ini belum mengisi data pendaftaran. Profil tidak tersedia.
    </div>
    @endif

    {{-- ══ HERO CARD ══ --}}
    @if($profile)
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
            @if($profile->foto)
                <img src="{{ Storage::url($profile->foto) }}"
                     class="w-24 h-24 rounded-2xl object-cover border border-slate-200 shrink-0" alt="">
            @else
                <div class="w-24 h-24 rounded-2xl flex items-center justify-center text-4xl font-black text-white shrink-0"
                     style="background:#7C3AED;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-8 gap-y-2 flex-1 text-sm">
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Nama Lengkap</p>
                    <p class="font-bold text-slate-800">{{ $profile->nama_lengkap ?? $user->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Email</p>
                    <p class="font-semibold text-slate-700">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">No. HP</p>
                    <p class="font-semibold text-slate-700">{{ $profile->no_hp ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Tanggal Daftar</p>
                    <p class="font-semibold text-slate-700">{{ $user->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Jurusan</p>
                    <p class="font-semibold text-slate-700">{{ $profile->jurusan ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Sistem Pendidikan</p>
                    <p class="font-semibold text-slate-700">{{ $profile->sistem_pendidikan ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Didaftarkan Oleh</p>
                    <p class="font-semibold text-slate-700">{{ $profile->didaftarkan_oleh ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Kelengkapan</p>
                    @php $pct = min(100, round(($profile->current_step / 8) * 100)); @endphp
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-slate-100 rounded-full h-2">
                            <div class="h-2 rounded-full bg-teal-500" style="width:{{ $pct }}%"></div>
                        </div>
                        <span class="text-xs font-bold text-slate-600">{{ $pct }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ 3-COLUMN GRID ══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ── Kol 1 ── --}}
        <div class="space-y-5">

            {{-- Data Diri --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100">
                    <h3 class="font-bold text-slate-700 text-sm">🪪 Data Diri</h3>
                </div>
                <div class="divide-y divide-slate-50 px-5">
                    @php
                        $rows = [
                            'NIK'               => $profile->nik,
                            'NISN'              => $profile->nisn,
                            'Nama Panggilan'    => $profile->nama_panggilan,
                            'Kewarganegaraan'   => $profile->kewarganegaraan,
                            'Tempat Lahir'      => $profile->tempat_lahir,
                            'Tanggal Lahir'     => $profile->tanggal_lahir?->format('d M Y'),
                            'Jenis Kelamin'     => $profile->jenis_kelamin === 'L' ? 'Laki-laki' : ($profile->jenis_kelamin === 'P' ? 'Perempuan' : null),
                            'Agama'             => $profile->agama,
                            'Status Keluarga'   => $profile->status_keluarga,
                            'Status dlm Kel.'   => $profile->status_dalam_keluarga,
                            'Anak Ke'           => $profile->anak_ke ? "{$profile->anak_ke} dari {$profile->dari_saudara}" : null,
                            'Saudara Kandung'   => $profile->total_saudara_kandung,
                            'Saudara Tiri'      => $profile->total_saudara_tiri,
                            'Saudara Angkat'    => $profile->total_saudara_angkat,
                            'Penerima KIP'      => $profile->penerima_kip ? 'Ya' : 'Tidak',
                            'Nomor KIP'         => $profile->nomor_kip,
                        ];
                    @endphp
                    @foreach($rows as $lbl => $val)
                        @if(!is_null($val) && $val !== '')
                        <div class="flex justify-between gap-3 py-2.5 text-sm">
                            <span class="text-slate-400 shrink-0">{{ $lbl }}</span>
                            <span class="text-slate-700 font-medium text-right">{{ $val }}</span>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Kesehatan --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100">
                    <h3 class="font-bold text-slate-700 text-sm">🏥 Kesehatan</h3>
                </div>
                <div class="divide-y divide-slate-50 px-5">
                    @php
                        $rows = [
                            'Tinggi Badan'      => $profile->tinggi_badan ? $profile->tinggi_badan . ' cm' : null,
                            'Berat Badan'       => $profile->berat_badan  ? $profile->berat_badan  . ' kg' : null,
                            'Disabilitas'       => $profile->disabilitas,
                            'Riwayat Kesehatan' => $profile->riwayat_kesehatan,
                        ];
                    @endphp
                    @foreach($rows as $lbl => $val)
                        @if(!is_null($val) && $val !== '')
                        <div class="flex justify-between gap-3 py-2.5 text-sm">
                            <span class="text-slate-400 shrink-0">{{ $lbl }}</span>
                            <span class="text-slate-700 font-medium text-right">{{ $val }}</span>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>

        </div>

        {{-- ── Kol 2 ── --}}
        <div class="space-y-5">

            {{-- Pendidikan --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100">
                    <h3 class="font-bold text-slate-700 text-sm">📚 Pendidikan</h3>
                </div>
                <div class="divide-y divide-slate-50 px-5">
                    @php
                        $rows = [
                            'Asal Sekolah'      => $profile->asal_sekolah,
                            'NPSN'              => $profile->npsn,
                            'Nomor Ijazah'      => $profile->nomor_ijazah,
                            'Tanggal Ijazah'    => $profile->tanggal_ijazah?->format('d M Y'),
                            'Lama Belajar'      => $profile->lama_belajar ? $profile->lama_belajar . ' tahun' : null,
                        ];
                    @endphp
                    @foreach($rows as $lbl => $val)
                        @if(!is_null($val) && $val !== '')
                        <div class="flex justify-between gap-3 py-2.5 text-sm">
                            <span class="text-slate-400 shrink-0">{{ $lbl }}</span>
                            <span class="text-slate-700 font-medium text-right">{{ $val }}</span>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Alamat & Transportasi --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100">
                    <h3 class="font-bold text-slate-700 text-sm">📍 Alamat & Transportasi</h3>
                </div>
                <div class="divide-y divide-slate-50 px-5">
                    @php
                        $rows = [
                            'Provinsi'          => $profile->provinsi,
                            'Kabupaten'         => $profile->kabupaten,
                            'Kecamatan'         => $profile->kecamatan,
                            'Desa'              => $profile->desa,
                            'RT / RW'           => ($profile->rt && $profile->rw) ? "{$profile->rt} / {$profile->rw}" : null,
                            'Alamat Lengkap'    => $profile->alamat_lengkap,
                            'Status Tinggal'    => $profile->status_tinggal,
                            'Bahasa Sehari-hari'=> $profile->bahasa_sehari_hari,
                            'Saudara di Sekolah'=> $profile->saudara_di_sekolah,
                            'Transportasi'      => $profile->moda_transportasi,
                            'Jarak ke Sekolah'  => $profile->jarak_sekolah_km  ? $profile->jarak_sekolah_km  . ' km' : null,
                            'Waktu Tempuh'      => $profile->waktu_tempuh_jam   ? $profile->waktu_tempuh_jam  . ' jam' : null,
                        ];
                    @endphp
                    @foreach($rows as $lbl => $val)
                        @if(!is_null($val) && $val !== '')
                        <div class="flex justify-between gap-3 py-2.5 text-sm">
                            <span class="text-slate-400 shrink-0">{{ $lbl }}</span>
                            <span class="text-slate-700 font-medium text-right">{{ $val }}</span>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Dokumen --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100">
                    <h3 class="font-bold text-slate-700 text-sm">📎 Dokumen</h3>
                </div>
                <div class="divide-y divide-slate-50 px-5">
                    @foreach(['dokumen_kk' => 'Kartu Keluarga', 'dokumen_ijazah' => 'Ijazah / SKL'] as $field => $lbl)
                    <div class="flex items-center justify-between py-2.5 text-sm">
                        <span class="text-slate-400">{{ $lbl }}</span>
                        @if($profile->$field)
                            <a href="{{ Storage::url($profile->$field) }}" target="_blank"
                               class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1">
                                Lihat <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        @else
                            <span class="text-xs text-slate-300">Belum diunggah</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Prestasi --}}
            @if($profile->prestasi && count($profile->prestasi))
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100">
                    <h3 class="font-bold text-slate-700 text-sm">🏆 Prestasi</h3>
                </div>
                <div class="divide-y divide-slate-50 px-5">
                    @foreach($profile->prestasi as $p)
                    <div class="flex items-center justify-between py-2.5 text-sm">
                        <span class="text-slate-700 font-medium">{{ $p['nama'] ?? '—' }}</span>
                        @if(!empty($p['dokumen']))
                            <a href="{{ Storage::url($p['dokumen']) }}" target="_blank"
                               class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1">
                                Lihat <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- ── Kol 3 ── --}}
        <div class="space-y-5">

            {{-- Ayah --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100">
                    <h3 class="font-bold text-slate-700 text-sm">👨 Data Ayah</h3>
                </div>
                <div class="divide-y divide-slate-50 px-5">
                    @php
                        $rows = [
                            'Nama'            => $profile->nama_ayah,
                            'NIK'             => $profile->nik_ayah,
                            'Tempat Lahir'    => $profile->tempat_lahir_ayah,
                            'Tanggal Lahir'   => $profile->tanggal_lahir_ayah?->format('d M Y'),
                            'Kewarganegaraan' => $profile->kewarganegaraan_ayah,
                            'Agama'           => $profile->agama_ayah,
                            'Pendidikan'      => $profile->pendidikan_ayah,
                            'Pekerjaan'       => $profile->pekerjaan_ayah,
                            'Status Nikah'    => $profile->status_pernikahan_ayah,
                            'No HP'           => $profile->no_hp_ayah,
                        ];
                    @endphp
                    @foreach($rows as $lbl => $val)
                        @if(!is_null($val) && $val !== '')
                        <div class="flex justify-between gap-3 py-2.5 text-sm">
                            <span class="text-slate-400 shrink-0">{{ $lbl }}</span>
                            <span class="text-slate-700 font-medium text-right">{{ $val }}</span>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Ibu --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100">
                    <h3 class="font-bold text-slate-700 text-sm">👩 Data Ibu</h3>
                </div>
                <div class="divide-y divide-slate-50 px-5">
                    @php
                        $rows = [
                            'Nama'            => $profile->nama_ibu,
                            'NIK'             => $profile->nik_ibu,
                            'Tempat Lahir'    => $profile->tempat_lahir_ibu,
                            'Tanggal Lahir'   => $profile->tanggal_lahir_ibu?->format('d M Y'),
                            'Kewarganegaraan' => $profile->kewarganegaraan_ibu,
                            'Agama'           => $profile->agama_ibu,
                            'Pendidikan'      => $profile->pendidikan_ibu,
                            'Pekerjaan'       => $profile->pekerjaan_ibu,
                            'Status Nikah'    => $profile->status_pernikahan_ibu,
                            'No HP'           => $profile->no_hp_ibu,
                        ];
                    @endphp
                    @foreach($rows as $lbl => $val)
                        @if(!is_null($val) && $val !== '')
                        <div class="flex justify-between gap-3 py-2.5 text-sm">
                            <span class="text-slate-400 shrink-0">{{ $lbl }}</span>
                            <span class="text-slate-700 font-medium text-right">{{ $val }}</span>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Penghasilan --}}
            @if($profile->penghasilan_ortu)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100">
                    <h3 class="font-bold text-slate-700 text-sm">💰 Penghasilan Orang Tua</h3>
                </div>
                <div class="px-5 py-4 text-sm font-semibold text-slate-700">
                    {{ $profile->penghasilan_ortu }}
                </div>
            </div>
            @endif

        </div>

    </div>{{-- end grid --}}
    @endif{{-- end @if($profile) --}}

</div>
@endsection
