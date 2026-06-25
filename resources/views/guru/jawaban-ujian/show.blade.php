@extends('guru.layouts.app')

@section('title', 'Jawaban: '.$jadwalUjian->nama)
@section('breadcrumb', 'Jawaban Ujian Siswa')

@push('styles')
<style>
    .pilihan-item { padding:.3rem .7rem; border-radius:8px; font-size:.8rem; font-weight:600; }
    .pilihan-kunci   { background:#d1fae5; color:#065f46; }
    .pilihan-dipilih { background:#dbeafe; color:#1d4ed8; }
    .pilihan-salah   { background:#fee2e2; color:#991b1b; }
    .pilihan-default { background:#f8fafc; color:#475569; }
    .modal-overlay { position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:50;display:flex;align-items:center;justify-content:center;padding:1rem; }
    .modal-box { background:#fff;border-radius:1.25rem;padding:1.5rem;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.2); }
</style>
@endpush

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a href="{{ route('guru.jawaban-ujian.index') }}"
               class="text-xs text-slate-400 hover:text-teal-600 transition inline-flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Daftar Ujian
            </a>
            <h1 class="text-xl font-extrabold text-slate-800 mt-1">Jawaban: {{ $jadwalUjian->nama }}</h1>
            <p class="text-sm text-slate-500">
                {{ $jadwalUjian->mataPelajaran?->nama }} &middot; {{ $jadwalUjian->kelas?->nama }}
                &middot; {{ $jadwalUjian->tanggal?->format('d M Y') }}
            </p>
        </div>
        @php $colors=['UTS'=>'blue','UAS'=>'purple','UKK'=>'rose','Sumatif'=>'amber','Lainnya'=>'slate'];$c=$colors[$jadwalUjian->jenis]??'slate'; @endphp
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold shrink-0
            bg-{{ $c }}-50 text-{{ $c }}-700 border border-{{ $c }}-200">
            {{ $jadwalUjian->jenis }}
        </span>
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

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([['📋','Total Murid',$stats['total_murid'],'bg-slate-50'],['✅','Selesai',$stats['selesai'],'bg-emerald-50'],['🔵','Sedang',$stats['sedang'],'bg-blue-50'],['⏳','Belum Mulai',$stats['belum'],'bg-amber-50']] as [$ico,$lbl,$val,$bg])
        <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-11 h-11 {{ $bg }} rounded-xl flex items-center justify-center text-2xl shrink-0">{{ $ico }}</div>
            <div>
                <div class="text-2xl font-extrabold text-slate-800">{{ $val }}</div>
                <div class="text-xs text-slate-500">{{ $lbl }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Info soal + hint --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex flex-wrap gap-4 items-center text-sm">
        <div class="flex items-center gap-2">
            <span>📝</span>
            <div><div class="font-bold text-slate-700">{{ $stats['total_soal'] }}</div><div class="text-xs text-slate-400">Total Soal</div></div>
        </div>
        <div class="flex items-center gap-2">
            <span>🔤</span>
            <div><div class="font-bold text-slate-700">{{ $stats['total_soal_pg'] }}</div><div class="text-xs text-slate-400">Pilihan Ganda</div></div>
        </div>
        <div class="flex items-center gap-2">
            <span>✍️</span>
            <div><div class="font-bold text-slate-700">{{ $stats['total_soal_essay'] }}</div><div class="text-xs text-slate-400">Essay</div></div>
        </div>
        <div class="flex items-center gap-2 px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-xl ml-auto">
            <span>🔑</span>
            <span class="text-xs font-semibold text-amber-700">Klik 🔑 untuk edit kunci jawaban · Pilih siswa lalu edit jawaban PG &amp; nilai essay</span>
        </div>
    </div>

    {{-- Grid 2 kolom --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Kiri: Daftar Siswa --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between" style="background:var(--teal-light)">
                <h2 class="font-bold text-sm" style="color:var(--teal)">Siswa yang Mengerjakan</h2>
                <span class="text-xs text-slate-500">{{ $sesiList->count() }} siswa</span>
            </div>
            @if($sesiList->isEmpty())
            <div class="p-8 text-center"><div class="text-4xl mb-2">😶</div><p class="text-sm text-slate-400">Belum ada siswa yang mengerjakan.</p></div>
            @else
            <div class="divide-y divide-slate-100 max-h-[540px] overflow-y-auto">
                @foreach($sesiList as $sesi)
                @php
                    $isActive = $muridDipilih && $muridDipilih->id === $sesi->murid_id;
                    $badgeCls = match($sesi->status){'selesai'=>'bg-emerald-50 text-emerald-700 border-emerald-200','sedang'=>'bg-blue-50 text-blue-700 border-blue-200',default=>'bg-slate-50 text-slate-500 border-slate-200'};
                @endphp
                <a href="{{ route('guru.jawaban-ujian.show',$jadwalUjian) }}?murid_id={{ $sesi->murid_id }}"
                   class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 transition {{ $isActive ? 'bg-teal-50 border-l-4 border-teal-500' : '' }}">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0 text-white" style="background:var(--teal)">
                        {{ strtoupper(substr($sesi->murid?->name??'?',0,1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-sm text-slate-800 truncate">{{ $sesi->murid?->name??'Siswa Dihapus' }}</div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[.65rem] font-bold border rounded-full px-1.5 py-0.5 {{ $badgeCls }}">
                                {{ App\Models\SesiUjian::$status[$sesi->status]??$sesi->status }}
                            </span>
                            @if($sesi->nilai_total !== null)
                            @php $nc=$sesi->nilai_total>=75?'text-emerald-700':($sesi->nilai_total>=60?'text-amber-600':'text-red-600'); @endphp
                            <span class="text-xs font-bold {{ $nc }}">{{ number_format($sesi->nilai_total,0) }}</span>
                            @endif
                        </div>
                    </div>
                    @if($isActive)<svg class="w-4 h-4 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>@endif
                </a>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Kanan: Detail Jawaban --}}
        <div class="lg:col-span-2 space-y-4">

            @if(! $muridDipilih)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-14 text-center">
                <div class="text-5xl mb-4">👈</div>
                <h3 class="font-bold text-slate-600 mb-1">Pilih Siswa</h3>
                <p class="text-sm text-slate-400">Klik nama siswa di sebelah kiri untuk melihat jawaban ujiannya.</p>
            </div>
            @else

            {{-- Info siswa + nilai --}}
            @php $sesiMurid = $sesiList->firstWhere('murid_id', $muridDipilih->id); @endphp
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                <div class="flex flex-wrap items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white shrink-0" style="background:var(--teal)">
                        {{ strtoupper(substr($muridDipilih->name,0,1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-slate-800">{{ $muridDipilih->name }}</div>
                        <div class="text-xs text-slate-400">{{ $muridDipilih->email }}</div>
                    </div>
                    @if($sesiMurid)
                    <div class="flex flex-wrap gap-4 text-center">
                        <div><div class="text-xs text-slate-400">Nilai PG</div><div class="font-bold text-slate-700">{{ $sesiMurid->nilai_pg!==null?number_format($sesiMurid->nilai_pg,0):'—' }}</div></div>
                        <div><div class="text-xs text-slate-400">Nilai Essay</div><div class="font-bold text-slate-700">{{ $sesiMurid->nilai_essay!==null?number_format($sesiMurid->nilai_essay,0):'—' }}</div></div>
                        <div>
                            <div class="text-xs text-slate-400">Total</div>
                            @php $nc=$sesiMurid->nilai_total!==null?($sesiMurid->nilai_total>=75?'text-emerald-700':($sesiMurid->nilai_total>=60?'text-amber-600':'text-red-600')):'text-slate-400'; @endphp
                            <div class="font-extrabold text-lg {{ $nc }}">{{ $sesiMurid->nilai_total!==null?number_format($sesiMurid->nilai_total,0):'—' }}</div>
                        </div>
                        <div><div class="text-xs text-slate-400">Durasi</div><div class="font-bold text-slate-700">{{ $sesiMurid->durasiMenit()?$sesiMurid->durasiMenit().' mnt':'—' }}</div></div>
                    </div>
                    @endif
                </div>

                {{-- Tombol aksi edit nilai — selalu tampil setelah pilih siswa --}}
                @if($sesiMurid)
                <div class="mt-3 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2">
                    <p class="text-xs text-slate-400 flex-1">Koreksi jawaban PG &amp; nilai essay langsung di bawah tiap soal.</p>
                    <a href="#soal-1"
                       onclick="document.getElementById('form-nilai').scrollIntoView({behavior:'smooth',block:'start'});return false;"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:brightness-110 transition"
                       style="background:var(--teal)">
                        ✏️ Edit Jawaban &amp; Nilai
                    </a>
                    <button form="form-nilai" type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:brightness-110 transition bg-emerald-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Perubahan
                    </button>
                </div>
                @endif
            </div>

            {{-- Soal + form nilai essay --}}
            @if($soalList->isNotEmpty())
            @php $adaEssay = $soalList->where('tipe','essay')->count() > 0; @endphp
            <form id="form-nilai" method="POST"
                  action="{{ route('guru.jawaban-ujian.update-nilai', $jadwalUjian) }}">                @csrf
                <input type="hidden" name="murid_id" value="{{ $muridDipilih->id }}">

                @foreach($soalList as $soal)
                @php $jawaban = $jawabanList[$soal->id] ?? null; @endphp
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-4">

                    {{-- Header soal --}}
                    <div class="flex items-center gap-3 px-5 py-3 border-b border-slate-100 bg-slate-50">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-extrabold text-white shrink-0" style="background:var(--teal)">{{ $soal->nomor }}</span>
                        <div class="flex-1 font-semibold text-slate-700 text-sm">{{ $soal->pertanyaan }}</div>

                        {{-- Badge status jawaban --}}
                        @if($soal->tipe === 'pilihan_ganda')
                            @if($jawaban)
                                @if($jawaban->isBenar())
                                <span class="text-[.65rem] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 shrink-0">✓ Benar (+{{ $soal->poin }})</span>
                                @else
                                <span class="text-[.65rem] font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-700 shrink-0">✗ Salah (+0)</span>
                                @endif
                            @else
                            <span class="text-[.65rem] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 shrink-0">Tidak dijawab</span>
                            @endif
                        @else
                        <span class="text-[.65rem] font-bold px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 shrink-0">Essay · maks {{ $soal->poin }} poin</span>
                        @endif

                        {{-- Tombol edit kunci jawaban --}}
                        <button type="button"
                                onclick="openKunciModal({{ $soal->id }}, '{{ $soal->tipe }}', '{{ addslashes($soal->kunci_jawaban ?? '') }}', {{ json_encode($soal->kunci_jawaban_essay ?? '') }}, {{ $muridDipilih->id }})"
                                class="ml-1 p-1.5 rounded-lg text-amber-500 hover:bg-amber-50 transition shrink-0"
                                title="Edit kunci jawaban soal ini">
                            🔑
                        </button>
                    </div>

                    <div class="px-5 py-4 space-y-3">
                        @if($soal->tipe === 'pilihan_ganda')
                        {{-- Pilihan: tampilkan mana yang dipilih vs kunci --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach(['A'=>$soal->pilihan_a,'B'=>$soal->pilihan_b,'C'=>$soal->pilihan_c,'D'=>$soal->pilihan_d] as $huruf=>$teks)
                            @if($teks)
                            @php
                                $isKunci   = $soal->kunci_jawaban === $huruf;
                                $isDipilih = $jawaban && strtoupper($jawaban->jawaban_pg??'') === $huruf;
                                $cls = 'pilihan-default';
                                if ($isKunci && $isDipilih) $cls = 'pilihan-kunci';
                                elseif ($isKunci)           $cls = 'pilihan-kunci';
                                elseif ($isDipilih)         $cls = 'pilihan-salah';
                            @endphp
                            <div class="pilihan-item {{ $cls }} flex items-center gap-2">
                                <span class="font-extrabold shrink-0">{{ $huruf }}.</span>
                                <span>{{ $teks }}</span>
                                @if($isKunci)<span class="ml-auto text-[.6rem] font-bold shrink-0">✓ Kunci</span>
                                @elseif($isDipilih)<span class="ml-auto text-[.6rem] font-bold shrink-0">← Dipilih</span>@endif
                            </div>
                            @endif
                            @endforeach
                        </div>
                        @if(!$jawaban)<p class="text-xs text-slate-400 italic">Siswa tidak menjawab soal ini.</p>@endif

                        {{-- Edit jawaban PG --}}
                        @if($sesiMurid)
                        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                            <label class="text-xs font-semibold text-slate-600 shrink-0">✏️ Koreksi Jawaban</label>
                            <div class="flex gap-1.5">
                                @foreach(['A','B','C','D'] as $opt)
                                @php
                                    $isSelected  = $jawaban && strtoupper($jawaban->jawaban_pg??'') === $opt;
                                    $isKunciOpt  = $soal->kunci_jawaban === $opt;
                                @endphp
                                <label class="cursor-pointer">
                                    <input type="radio"
                                           name="jawaban_pg[{{ $soal->id }}]"
                                           value="{{ $opt }}"
                                           class="peer sr-only"
                                           {{ $isSelected ? 'checked' : '' }}>
                                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 font-bold text-sm transition
                                        {{ $isKunciOpt ? 'border-emerald-300 text-emerald-700' : 'border-slate-200 text-slate-500' }}
                                        peer-checked:border-teal-500 peer-checked:bg-teal-50 peer-checked:text-teal-700
                                        hover:border-slate-300">
                                        {{ $opt }}
                                    </span>
                                </label>
                                @endforeach
                                {{-- Opsi kosongkan jawaban --}}
                                <label class="cursor-pointer" title="Kosongkan jawaban">
                                    <input type="radio"
                                           name="jawaban_pg[{{ $soal->id }}]"
                                           value=""
                                           class="peer sr-only"
                                           {{ (!$jawaban || !$jawaban->jawaban_pg) ? 'checked' : '' }}>
                                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 font-bold text-xs transition
                                        border-slate-200 text-slate-400
                                        peer-checked:border-red-300 peer-checked:bg-red-50 peer-checked:text-red-500
                                        hover:border-slate-300">
                                        —
                                    </span>
                                </label>
                            </div>
                            <span class="text-xs text-slate-400">
                                Kunci: <strong class="text-emerald-600">{{ $soal->kunci_jawaban ?? '?' }}</strong>
                            </span>
                        </div>
                        @endif

                        @else
                        {{-- Essay: tampil jawaban + input nilai --}}
                        <div>
                            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Jawaban Siswa</div>
                            @if($jawaban && $jawaban->jawaban_essay)
                            <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 leading-relaxed">{{ $jawaban->jawaban_essay }}</div>
                            @else
                            <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm text-slate-400 italic">Tidak dijawab</div>
                            @endif
                        </div>
                        @if($soal->kunci_jawaban_essay)
                        <div>
                            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Kunci Jawaban</div>
                            <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 text-sm text-emerald-800 leading-relaxed">{{ $soal->kunci_jawaban_essay }}</div>
                        </div>
                        @endif
                        {{-- Input nilai essay --}}
                        <div class="flex items-center gap-3 pt-1 border-t border-slate-100">
                            <label class="text-xs font-semibold text-slate-600 shrink-0">✏️ Nilai Essay <span class="font-normal text-slate-400">(0–{{ $soal->poin }})</span></label>
                            <input type="number" name="nilai_essay[{{ $soal->id }}]"
                                   value="{{ $jawaban?->nilai_essay ?? 0 }}"
                                   min="0" max="{{ $soal->poin }}" step="1"
                                   class="w-24 text-center rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                            <span class="text-xs text-slate-400">/ {{ $soal->poin }} poin</span>
                            @if($jawaban?->nilai_essay !== null)
                            @php $bc=$jawaban->nilai_essay>=$soal->poin*0.75?'bg-emerald-100 text-emerald-700':($jawaban->nilai_essay>=$soal->poin*0.5?'bg-amber-100 text-amber-700':'bg-red-100 text-red-700'); @endphp
                            <span class="text-[.65rem] font-bold px-2 py-0.5 rounded-full {{ $bc }}">Saat ini: {{ $jawaban->nilai_essay }}</span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach

                @if($adaEssay || $stats['total_soal_pg'] > 0)
                <div class="sticky bottom-4 flex justify-end">
                    @if($sesiMurid)
                    <button form="form-nilai" type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white shadow-lg hover:brightness-110 transition bg-emerald-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Semua Perubahan
                    </button>
                    @else
                    <span class="text-xs text-slate-400 italic bg-white px-4 py-2 rounded-xl border border-slate-100 shadow-sm">Siswa belum mengerjakan ujian</span>
                    @endif
                </div>
                @endif
            </form>
            @else
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 text-center">
                <div class="text-4xl mb-2">📭</div>
                <p class="text-sm text-slate-400">Belum ada soal untuk ujian ini.</p>
            </div>
            @endif

            @endif {{-- end muridDipilih --}}
        </div>
    </div>{{-- end grid --}}

    {{-- ═══════ MODAL EDIT KUNCI JAWABAN ═══════ --}}
    <div id="modal-kunci" class="modal-overlay hidden" onclick="closeKunciModal(event)">
        <div class="modal-box" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-extrabold text-slate-800 text-base">🔑 Edit Kunci Jawaban</h3>
                <button type="button" onclick="closeKunciModal()" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <p class="text-xs text-slate-500 mb-4">
                Mengubah kunci jawaban akan <strong class="text-slate-700">menghitung ulang nilai semua siswa</strong> yang sudah menyelesaikan ujian ini.
            </p>

            {{-- Form PG --}}
            <form id="form-kunci-pg" method="POST" class="hidden">
                @csrf
                <input type="hidden" id="kunci-pg-murid" name="murid_id">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Kunci Jawaban Pilihan Ganda</label>
                <div class="grid grid-cols-4 gap-2 mb-5">
                    @foreach(['A','B','C','D'] as $h)
                    <label class="cursor-pointer">
                        <input type="radio" name="kunci_jawaban" value="{{ $h }}" class="peer sr-only">
                        <div class="text-center py-2.5 rounded-xl border-2 border-slate-200 font-bold text-slate-500
                                    peer-checked:border-teal-500 peer-checked:bg-teal-50 peer-checked:text-teal-700
                                    hover:border-slate-300 transition text-sm">
                            {{ $h }}
                        </div>
                    </label>
                    @endforeach
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="closeKunciModal()" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-sm font-semibold text-white hover:brightness-110 transition" style="background:var(--teal)">Simpan & Hitung Ulang</button>
                </div>
            </form>

            {{-- Form Essay --}}
            <form id="form-kunci-essay" method="POST" class="hidden">
                @csrf
                <input type="hidden" id="kunci-essay-murid" name="murid_id">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Kunci Jawaban Essay</label>
                <textarea name="kunci_jawaban_essay" id="kunci-essay-input" rows="5"
                          class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300 mb-4"
                          placeholder="Tulis kunci jawaban essay..."></textarea>
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="closeKunciModal()" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-sm font-semibold text-white hover:brightness-110 transition" style="background:var(--teal)">Simpan & Hitung Ulang</button>
                </div>
            </form>
        </div>
    </div>

</div>{{-- end content --}}
@endsection

@push('scripts')
<script>
function openKunciModal(soalId, tipe, kunciPg, kunciEssay, muridId) {
    const modal    = document.getElementById('modal-kunci');
    const formPg   = document.getElementById('form-kunci-pg');
    const formEssay= document.getElementById('form-kunci-essay');

    // Set action URL (dengan murid_id sebagai query param agar redirect kembali ke siswa ini)
    const baseUrl = '{{ route("guru.jawaban-ujian.update-kunci", [$jadwalUjian, "__SOAL__"]) }}'
        .replace('__SOAL__', soalId);
    const actionUrl = baseUrl + '?murid_id=' + muridId;

    if (tipe === 'pilihan_ganda') {
        formEssay.classList.add('hidden');
        formPg.classList.remove('hidden');
        formPg.action = actionUrl;
        document.getElementById('kunci-pg-murid').value = muridId;
        // Centang radio sesuai kunci saat ini
        formPg.querySelectorAll('input[type=radio]').forEach(r => {
            r.checked = r.value === kunciPg.toUpperCase();
        });
    } else {
        formPg.classList.add('hidden');
        formEssay.classList.remove('hidden');
        formEssay.action = actionUrl;
        document.getElementById('kunci-essay-murid').value = muridId;
        document.getElementById('kunci-essay-input').value = kunciEssay || '';
    }

    modal.classList.remove('hidden');
}

function closeKunciModal(e) {
    if (e && e.target !== document.getElementById('modal-kunci')) return;
    document.getElementById('modal-kunci').classList.add('hidden');
}
</script>
@endpush
