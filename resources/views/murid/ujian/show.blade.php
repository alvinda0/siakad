@extends('murid.layouts.app')

@section('title', 'Detail Ujian')
@section('breadcrumb', 'Detail Ujian')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    {{-- Back --}}
    <a href="{{ route('murid.ujian.index') }}"
       class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-teal-600 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Daftar Ujian
    </a>

    {{-- Card info ujian --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

        {{-- Color stripe --}}
        <div class="h-2 w-full"
             style="background: {{ ['UTS'=>'#1d4ed8','UAS'=>'#7c3aed','UKK'=>'#be123c','Sumatif'=>'#b45309','Lainnya'=>'#94a3b8'][$jadwalUjian->jenis] ?? '#94a3b8' }}">
        </div>

        <div class="p-6">
            {{-- Badge --}}
            <div class="mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold"
                      style="background: {{ ['UTS'=>'#dbeafe','UAS'=>'#ede9fe','UKK'=>'#ffe4e6','Sumatif'=>'#fef3c7','Lainnya'=>'#f1f5f9'][$jadwalUjian->jenis] ?? '#f1f5f9' }};
                             color: {{ ['UTS'=>'#1d4ed8','UAS'=>'#7c3aed','UKK'=>'#be123c','Sumatif'=>'#b45309','Lainnya'=>'#475569'][$jadwalUjian->jenis] ?? '#475569' }}">
                    {{ $jadwalUjian->jenis }}
                </span>
            </div>

            <h1 class="text-2xl font-extrabold text-slate-800 mb-1">{{ $jadwalUjian->mataPelajaran->nama }}</h1>
            <p class="text-slate-500 mb-6">{{ $jadwalUjian->nama }}</p>

            {{-- Info grid --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Tanggal</p>
                    <p class="text-sm font-bold text-slate-700">{{ $jadwalUjian->tanggalLabel() }}</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Waktu</p>
                    <p class="text-sm font-bold text-slate-700">{{ $jadwalUjian->jamLabel() }}</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Kelas</p>
                    <p class="text-sm font-bold text-slate-700">{{ $jadwalUjian->kelas->nama }}</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Jumlah Soal</p>
                    <p class="text-sm font-bold text-slate-700">{{ $soalCount }} soal</p>
                </div>
                @if($jadwalUjian->ruangan)
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Ruangan</p>
                    <p class="text-sm font-bold text-slate-700">{{ $jadwalUjian->ruangan }}</p>
                </div>
                @endif
                @if($jadwalUjian->guru)
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Guru Pengawas</p>
                    <p class="text-sm font-bold text-slate-700">{{ $jadwalUjian->guru->name }}</p>
                </div>
                @endif
            </div>

            @if($jadwalUjian->keterangan)
            <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 mb-6">
                <p class="text-xs font-semibold text-blue-600 mb-1">Keterangan</p>
                <p class="text-sm text-blue-800">{{ $jadwalUjian->keterangan }}</p>
            </div>
            @endif

            {{-- Status sesi --}}
            @if($sesi->isBelum())
            <div class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-4 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-bold text-amber-800">Perhatian!</p>
                        <ul class="text-xs text-amber-700 mt-1.5 space-y-1 list-disc list-inside">
                            <li>Setelah klik Mulai Ujian, sesi akan langsung dimulai.</li>
                            <li>Jawaban akan tersimpan otomatis saat Anda memilih/mengetik.</li>
                            <li>Pastikan koneksi internet Anda stabil.</li>
                            <li>Klik tombol <strong>Selesaikan Ujian</strong> setelah menjawab semua soal.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('murid.ujian.submit', $jadwalUjian) }}"
                  id="form-mulai" onsubmit="return false;">
                @csrf
                <button type="button" onclick="konfirmasiMulai()"
                        class="w-full flex items-center justify-center gap-2 py-3.5 rounded-xl text-base font-bold text-white transition hover:brightness-110 active:scale-95"
                        style="background: var(--teal);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                    Mulai Ujian Sekarang
                </button>
            </form>

            @elseif($sesi->isSedang())
            <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4 mb-6">
                <p class="text-sm font-bold text-blue-800">Ujian sedang berlangsung</p>
                <p class="text-xs text-blue-700 mt-0.5">Anda sudah memulai ujian ini. Klik tombol di bawah untuk melanjutkan.</p>
            </div>
            <a href="{{ route('murid.ujian.kerjakan', $jadwalUjian) }}"
               class="flex items-center justify-center gap-2 w-full py-3.5 rounded-xl text-base font-bold text-white transition hover:brightness-110"
               style="background: var(--teal);">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
                Lanjut Kerjakan Ujian
            </a>

            @else
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-5 py-4 mb-6">
                <p class="text-sm font-bold text-emerald-800">✅ Ujian sudah selesai</p>
                <p class="text-xs text-emerald-700 mt-0.5">Anda telah menyelesaikan ujian ini.</p>
            </div>
            <a href="{{ route('murid.ujian.hasil', $jadwalUjian) }}"
               class="flex items-center justify-center gap-2 w-full py-3.5 rounded-xl text-base font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition border border-emerald-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Lihat Hasil Ujian
            </a>
            @endif
        </div>
    </div>

</div>

{{-- Konfirmasi modal --}}
<div id="modal-konfirmasi" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 text-center">
        <div class="w-16 h-16 bg-teal-50 rounded-full flex items-center justify-center text-4xl mx-auto mb-4">📝</div>
        <h2 class="text-lg font-extrabold text-slate-800 mb-2">Siap Mulai Ujian?</h2>
        <p class="text-sm text-slate-500 mb-6">
            Anda akan mulai ujian <strong>{{ $jadwalUjian->mataPelajaran->nama }}</strong>.<br>
            Sesi akan langsung dimulai dan dicatat.
        </p>
        <div class="flex gap-3">
            <button type="button" onclick="document.getElementById('modal-konfirmasi').classList.add('hidden')"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                Batal
            </button>
            <a href="{{ route('murid.ujian.kerjakan', $jadwalUjian) }}"
               class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white transition hover:brightness-110 text-center"
               style="background: var(--teal);">
                Ya, Mulai!
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
function konfirmasiMulai() {
    document.getElementById('modal-konfirmasi').classList.remove('hidden');
}
</script>
@endpush
@endsection
