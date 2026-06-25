@extends('murid.layouts.app')

@section('title', 'Mengerjakan Ujian')
@section('breadcrumb', $jadwalUjian->mataPelajaran->nama)

@push('styles')
<style>
    body { background: #F0F7F9; }

    .soal-nav-btn {
        width: 36px; height: 36px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: .78rem; font-weight: 700;
        cursor: pointer; border: 1.5px solid #e2e8f0;
        background: #fff; color: #64748b;
        transition: all .12s;
        flex-shrink: 0;
    }
    .soal-nav-btn:hover        { border-color: var(--teal); color: var(--teal); }
    .soal-nav-btn.dijawab      { background: var(--teal); color: #fff; border-color: var(--teal); }
    .soal-nav-btn.aktif        { outline: 3px solid #fbbf24; outline-offset: 2px; }

    .pilihan-label {
        display: flex; align-items: flex-start; gap: .75rem;
        padding: .875rem 1rem; border-radius: 12px;
        border: 1.5px solid #e2e8f0; cursor: pointer;
        transition: all .13s; background: #fff;
    }
    .pilihan-label:hover { border-color: var(--teal); background: #f0fdf9; }
    .pilihan-label input[type="radio"] { display: none; }
    .pilihan-label.selected { border-color: var(--teal); background: #e0f4f7; }
    .pilihan-huruf {
        width: 30px; height: 30px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: .78rem; font-weight: 800;
        background: #f1f5f9; color: #475569;
        flex-shrink: 0;
        transition: all .12s;
    }
    .pilihan-label.selected .pilihan-huruf {
        background: var(--teal); color: #fff;
    }

    .floating-panel {
        position: sticky; top: 80px;
        background: #fff; border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 6px rgba(0,0,0,.06);
    }

    /* Nomor soal panel scrollable */
    .nav-grid { display: flex; flex-wrap: wrap; gap: 6px; }

    .progress-bar-bg { background: #e2e8f0; border-radius: 999px; height: 6px; }
    .progress-bar-fill {
        background: var(--teal); border-radius: 999px; height: 6px;
        transition: width .3s ease;
    }

    @media (max-width: 1023px) {
        .floating-panel { position: static; }
    }
</style>
@endpush

@section('content')
@php
    $totalSoal  = $soalList->count();
    $soalPG     = $soalList->where('tipe', 'pilihan_ganda');
    $soalEssay  = $soalList->where('tipe', 'essay');
    $totalDijawab = $jawabanMap->count();
@endphp

<div class="space-y-4">

{{-- ── Top bar ujian ── --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-3 flex flex-wrap items-center justify-between gap-3">
    <div>
        <p class="text-xs text-slate-400 font-medium">Ujian Sedang Berlangsung</p>
        <h1 class="text-base font-extrabold text-slate-800">
            {{ $jadwalUjian->mataPelajaran->nama }}
            <span class="ml-2 text-xs font-bold px-2 py-0.5 rounded-full bg-teal-50 text-teal-700">{{ $jadwalUjian->jenis }}</span>
        </h1>
    </div>
    <div class="flex items-center gap-3">
        {{-- Timer display --}}
        <div class="text-center bg-slate-50 rounded-xl px-4 py-2 border border-slate-200">
            <p class="text-xs text-slate-400 font-medium">Waktu</p>
            <p id="timer-display" class="text-lg font-extrabold text-slate-800 font-mono tabular-nums">
                {{ $jadwalUjian->jamLabel() }}
            </p>
        </div>
        {{-- Progress --}}
        <div class="text-center bg-teal-50 rounded-xl px-4 py-2 border border-teal-200">
            <p class="text-xs text-teal-500 font-medium">Dijawab</p>
            <p class="text-lg font-extrabold text-teal-700">
                <span id="count-dijawab">{{ $totalDijawab }}</span>/<span>{{ $totalSoal }}</span>
            </p>
        </div>
    </div>
</div>

<div class="flex gap-5 items-start">

    {{-- ── Kolom kiri: Soal ── --}}
    <div class="flex-1 min-w-0 space-y-6" id="soal-container">

        {{-- A. PILIHAN GANDA --}}
        @if($soalPG->count())
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-blue-50 to-slate-50">
                <h2 class="font-extrabold text-slate-700 text-sm">A. Pilihan Ganda</h2>
                <p class="text-xs text-slate-400 mt-0.5">{{ $soalPG->count() }} soal — pilih satu jawaban yang paling tepat</p>
            </div>

            <div class="p-5 space-y-8">
                @foreach($soalPG->sortBy('nomor') as $soal)
                @php $jwb = $jawabanMap[$soal->id] ?? null; @endphp
                <div class="soal-item" id="soal-{{ $soal->id }}" data-nomor="{{ $soal->nomor }}">
                    {{-- Nomor + pertanyaan --}}
                    <div class="flex gap-3 mb-4">
                        <span class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-extrabold text-white"
                              style="background: var(--teal);">{{ $soal->nomor }}</span>
                        <p class="text-sm text-slate-800 font-medium leading-relaxed pt-1">
                            {!! nl2br(e($soal->pertanyaan)) !!}
                        </p>
                    </div>

                    {{-- Pilihan --}}
                    <div class="space-y-2 ml-11" role="radiogroup">
                        @foreach($soal->pilihanArray() as $huruf => $teks)
                        <label class="pilihan-label {{ ($jwb && $jwb->jawaban_pg === $huruf) ? 'selected' : '' }}"
                               id="label-{{ $soal->id }}-{{ $huruf }}"
                               for="pg-{{ $soal->id }}-{{ $huruf }}">
                            <input type="radio"
                                   id="pg-{{ $soal->id }}-{{ $huruf }}"
                                   name="pg_{{ $soal->id }}"
                                   value="{{ $huruf }}"
                                   {{ ($jwb && $jwb->jawaban_pg === $huruf) ? 'checked' : '' }}
                                   onchange="pilihJawaban({{ $soal->id }}, 'pg', '{{ $huruf }}')">
                            <span class="pilihan-huruf">{{ $huruf }}</span>
                            <span class="text-sm text-slate-700 leading-relaxed">{{ $teks }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- B. ESSAY --}}
        @if($soalEssay->count())
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-purple-50 to-slate-50">
                <h2 class="font-extrabold text-slate-700 text-sm">B. Essay</h2>
                <p class="text-xs text-slate-400 mt-0.5">{{ $soalEssay->count() }} soal — jawab dengan kalimat Anda sendiri</p>
            </div>

            <div class="p-5 space-y-8">
                @foreach($soalEssay->sortBy('nomor') as $soal)
                @php $jwb = $jawabanMap[$soal->id] ?? null; @endphp
                <div class="soal-item" id="soal-{{ $soal->id }}" data-nomor="{{ $soal->nomor }}">
                    <div class="flex gap-3 mb-3">
                        <span class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-extrabold text-white bg-purple-500">
                            {{ $soal->nomor }}
                        </span>
                        <p class="text-sm text-slate-800 font-medium leading-relaxed pt-1">
                            {!! nl2br(e($soal->pertanyaan)) !!}
                        </p>
                    </div>
                    <div class="ml-11">
                        <textarea
                            id="essay-{{ $soal->id }}"
                            rows="5"
                            placeholder="Tulis jawaban Anda di sini..."
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300 focus:border-teal-300 resize-y"
                            oninput="autoSaveEssay({{ $soal->id }}, this.value)">{{ $jwb ? $jwb->jawaban_essay : '' }}</textarea>
                        <p class="text-xs text-slate-400 mt-1 text-right" id="save-status-{{ $soal->id }}">
                            {{ $jwb ? '✓ Tersimpan' : '' }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Submit button --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div>
                    <p class="text-sm font-semibold text-slate-700">Selesai menjawab?</p>
                    <p class="text-xs text-slate-400 mt-0.5">
                        <span id="info-dijawab">{{ $totalDijawab }}</span> dari {{ $totalSoal }} soal dijawab.
                        Soal yang belum dijawab akan dianggap kosong.
                    </p>
                </div>
                <button type="button" onclick="konfirmasiSelesai()"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold text-white transition hover:brightness-110 active:scale-95"
                        style="background: var(--teal);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Selesaikan Ujian
                </button>
            </div>

            {{-- Progress bar --}}
            <div class="mt-4">
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" id="progress-bar"
                         style="width: {{ $totalSoal > 0 ? round(($totalDijawab/$totalSoal)*100) : 0 }}%"></div>
                </div>
                <p class="text-xs text-slate-400 mt-1 text-right">
                    <span id="progress-pct">{{ $totalSoal > 0 ? round(($totalDijawab/$totalSoal)*100) : 0 }}</span>% selesai
                </p>
            </div>
        </div>

    </div>

    {{-- ── Kolom kanan: Navigasi soal ── --}}
    <div class="w-56 shrink-0 hidden lg:block">
        <div class="floating-panel p-4">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3">Navigasi Soal</p>
            <div class="nav-grid" id="nav-grid">
                @foreach($soalList->sortBy('nomor') as $soal)
                @php $jwb = $jawabanMap[$soal->id] ?? null; @endphp
                <button type="button"
                        class="soal-nav-btn {{ $jwb ? 'dijawab' : '' }}"
                        id="nav-btn-{{ $soal->id }}"
                        data-soal-id="{{ $soal->id }}"
                        onclick="scrollToSoal({{ $soal->id }})"
                        title="Soal {{ $soal->nomor }}">
                    {{ $soal->nomor }}
                </button>
                @endforeach
            </div>

            <div class="mt-4 space-y-2 text-xs text-slate-500">
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded soal-nav-btn dijawab" style="pointer-events:none;min-width:20px;height:20px;font-size:.6rem;padding:0;"></div>
                    <span>Sudah dijawab</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded border border-slate-200 bg-white" style="min-width:20px;height:20px;"></div>
                    <span>Belum dijawab</span>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100">
                <button type="button" onclick="konfirmasiSelesai()"
                        class="w-full py-2.5 rounded-xl text-sm font-bold text-white transition hover:brightness-110"
                        style="background: var(--teal);">
                    Selesaikan
                </button>
            </div>
        </div>
    </div>

</div>
</div>

{{-- Modal konfirmasi selesai --}}
<div id="modal-selesai" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 text-center">
        <div class="w-16 h-16 bg-teal-50 rounded-full flex items-center justify-center text-4xl mx-auto mb-4">🎓</div>
        <h2 class="text-lg font-extrabold text-slate-800 mb-2">Selesaikan Ujian?</h2>
        <p class="text-sm text-slate-500 mb-2">
            Anda menjawab <strong id="modal-dijawab">0</strong> dari <strong>{{ $totalSoal }}</strong> soal.
        </p>
        <p class="text-xs text-amber-600 bg-amber-50 rounded-lg px-3 py-2 mb-6">
            Setelah diselesaikan, jawaban tidak dapat diubah lagi.
        </p>
        <div class="flex gap-3">
            <button type="button"
                    onclick="document.getElementById('modal-selesai').classList.add('hidden')"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                Lanjut Kerjakan
            </button>
            <form method="POST" action="{{ route('murid.ujian.submit', $jadwalUjian) }}" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full py-2.5 rounded-xl text-sm font-bold text-white transition hover:brightness-110"
                        style="background: var(--teal);">
                    Ya, Selesaikan!
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const SAVE_URL  = "{{ route('murid.ujian.jawaban', $jadwalUjian) }}";
const CSRF      = document.querySelector('meta[name="csrf-token"]').content;
const TOTAL     = {{ $totalSoal }};
let   dijawabSet = new Set(@json($jawabanMap->keys()));

/* ── Pilihan ganda ── */
function pilihJawaban(soalId, tipe, nilai) {
    // Update UI pilihan
    document.querySelectorAll(`label[id^="label-${soalId}-"]`)
            .forEach(l => l.classList.remove('selected'));
    const target = document.getElementById(`label-${soalId}-${nilai}`);
    if (target) target.classList.add('selected');

    // Kirim ke server
    simpanJawaban(soalId, 'jawaban_pg', nilai);
}

/* ── Essay auto-save (debounce 800ms) ── */
const essayTimers = {};
function autoSaveEssay(soalId, teks) {
    const status = document.getElementById(`save-status-${soalId}`);
    if (status) status.textContent = 'Menyimpan...';
    clearTimeout(essayTimers[soalId]);
    essayTimers[soalId] = setTimeout(() => {
        simpanJawaban(soalId, 'jawaban_essay', teks);
    }, 800);
}

/* ── Kirim jawaban ── */
async function simpanJawaban(soalId, field, nilai) {
    try {
        const body = new FormData();
        body.append('_token', CSRF);
        body.append('soal_id', soalId);
        body.append(field, nilai);

        const res = await fetch(SAVE_URL, { method: 'POST', body });
        if (res.ok) {
            // Update nav button
            dijawabSet.add(String(soalId));
            const btn = document.getElementById(`nav-btn-${soalId}`);
            if (btn) btn.classList.add('dijawab');

            // Update counter
            updateCounter();

            // Update essay status
            if (field === 'jawaban_essay') {
                const status = document.getElementById(`save-status-${soalId}`);
                if (status) status.textContent = '✓ Tersimpan';
            }
        }
    } catch (e) {
        // Silent fail — user can still answer, will be saved on next change
    }
}

function updateCounter() {
    const count = dijawabSet.size;
    document.getElementById('count-dijawab').textContent = count;
    document.getElementById('info-dijawab').textContent  = count;
    const pct = TOTAL > 0 ? Math.round((count / TOTAL) * 100) : 0;
    document.getElementById('progress-bar').style.width  = pct + '%';
    document.getElementById('progress-pct').textContent  = pct;
}

/* ── Scroll to soal ── */
function scrollToSoal(soalId) {
    const el = document.getElementById(`soal-${soalId}`);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        // Highlight aktif
        document.querySelectorAll('.soal-nav-btn').forEach(b => b.classList.remove('aktif'));
        const btn = document.getElementById(`nav-btn-${soalId}`);
        if (btn) btn.classList.add('aktif');
    }
}

/* ── Konfirmasi selesai ── */
function konfirmasiSelesai() {
    document.getElementById('modal-dijawab').textContent = dijawabSet.size;
    document.getElementById('modal-selesai').classList.remove('hidden');
}

/* Tutup modal klik backdrop */
document.getElementById('modal-selesai').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
</script>
@endpush
@endsection
