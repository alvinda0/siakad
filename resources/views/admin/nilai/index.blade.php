@extends('admin.layouts.app')

@section('title', 'Input Nilai')
@section('breadcrumb', 'Nilai')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-800">Input Nilai Siswa</h1>
            <p class="text-sm text-slate-500">Pilih kelas, mata pelajaran, dan semester untuk mengisi nilai.</p>
        </div>
        <a href="{{ route('admin.nilai.rekap') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
           style="background: var(--teal);">
            📊 Rekap Nilai
        </a>
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
        <form method="GET" id="filter-nilai" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Kelas</label>
                <select name="kelas_id"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">— Pilih Kelas —</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Mata Pelajaran</label>
                <select name="mata_pelajaran_id"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">— Pilih Mapel —</option>
                    @foreach($mapelList as $mapel)
                        <option value="{{ $mapel->id }}" {{ request('mata_pelajaran_id') == $mapel->id ? 'selected' : '' }}>
                            {{ $mapel->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Semester</label>
                <select name="semester"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                    @foreach(App\Models\Nilai::$semester as $val => $label)
                        <option value="{{ $val }}" {{ $semester == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tahun Ajaran</label>
                <input type="number" name="tahun_ajaran" value="{{ $tahunAjaran }}" min="2020" max="2099"
                       class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="flex-1 inline-flex justify-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 transition">
                    Tampilkan
                </button>
                <a href="{{ route('admin.nilai.index') }}"
                   class="flex-1 inline-flex justify-center px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Info bobot --}}
    @if($selectedKelas && $selectedMapel)
    <div class="bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-3 text-sm text-indigo-700">
        💡 <strong>Bobot nilai:</strong> Tugas 20% + UTS 30% + UAS 50% = Nilai Akhir (dihitung otomatis)
    </div>
    @endif

    {{-- Tabel input nilai --}}
    @if($selectedKelas && $selectedMapel && $nilaiRows->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100" style="background: var(--teal-light);">
            <p class="font-extrabold text-sm" style="color: var(--teal);">
                {{ $selectedMapel->nama }} — {{ $selectedKelas->nama }}
            </p>
            <p class="text-xs text-slate-500">
                {{ App\Models\Nilai::$semester[$semester] }} / TA {{ $tahunAjaran }}/{{ $tahunAjaran + 1 }}
            </p>
        </div>

        <form method="POST" action="{{ route('admin.nilai.simpan') }}">
            @csrf
            <input type="hidden" name="kelas_id"          value="{{ $selectedKelas->id }}">
            <input type="hidden" name="mata_pelajaran_id" value="{{ $selectedMapel->id }}">
            <input type="hidden" name="semester"          value="{{ $semester }}">
            <input type="hidden" name="tahun_ajaran"      value="{{ $tahunAjaran }}">

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left">
                            <th class="px-5 py-3 font-semibold text-slate-500 w-10">#</th>
                            <th class="px-5 py-3 font-semibold text-slate-500">Nama Siswa</th>
                            <th class="px-4 py-3 font-semibold text-slate-500 text-center">Tugas<br><span class="font-normal text-slate-400 text-xs">(20%)</span></th>
                            <th class="px-4 py-3 font-semibold text-slate-500 text-center">UTS<br><span class="font-normal text-slate-400 text-xs">(30%)</span></th>
                            <th class="px-4 py-3 font-semibold text-slate-500 text-center">UAS<br><span class="font-normal text-slate-400 text-xs">(50%)</span></th>
                            <th class="px-4 py-3 font-semibold text-slate-500 text-center">Nilai Akhir</th>
                            <th class="px-4 py-3 font-semibold text-slate-500 text-center">Predikat</th>
                            <th class="px-5 py-3 font-semibold text-slate-500">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50" id="nilai-tbody">
                        @foreach($nilaiRows as $i => $row)
                        @php $n = $row['nilai']; @endphp
                        <tr class="hover:bg-slate-50 transition" data-row="{{ $i }}">
                            <td class="px-5 py-3 text-slate-400">{{ $i + 1 }}</td>
                            <td class="px-5 py-3 font-semibold text-slate-700">{{ $row['murid']->name }}</td>
                            <td class="px-4 py-3">
                                <input type="number" name="nilai[{{ $row['murid']->id }}][tugas]"
                                       value="{{ $n?->nilai_tugas }}"
                                       min="0" max="100" step="0.01"
                                       placeholder="—"
                                       class="nilai-input w-20 text-center rounded-lg border border-slate-200 px-2 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300"
                                       data-type="tugas" data-row="{{ $i }}">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" name="nilai[{{ $row['murid']->id }}][uts]"
                                       value="{{ $n?->nilai_uts }}"
                                       min="0" max="100" step="0.01"
                                       placeholder="—"
                                       class="nilai-input w-20 text-center rounded-lg border border-slate-200 px-2 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300"
                                       data-type="uts" data-row="{{ $i }}">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" name="nilai[{{ $row['murid']->id }}][uas]"
                                       value="{{ $n?->nilai_uas }}"
                                       min="0" max="100" step="0.01"
                                       placeholder="—"
                                       class="nilai-input w-20 text-center rounded-lg border border-slate-200 px-2 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300"
                                       data-type="uas" data-row="{{ $i }}">
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span id="akhir-{{ $i }}" class="font-bold text-slate-700">
                                    {{ $n?->nilai_akhir ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span id="predikat-{{ $i }}"
                                      class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold {{ $n?->predikatColorClass() ?? 'bg-slate-50 text-slate-400' }}">
                                    {{ $n?->predikat ?? '—' }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <input type="text" name="nilai[{{ $row['murid']->id }}][catatan]"
                                       value="{{ $n?->catatan }}"
                                       placeholder="Opsional"
                                       class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex justify-end">
                <button type="submit"
                        class="px-5 py-2 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 transition">
                    💾 Simpan Semua Nilai
                </button>
            </div>
        </form>
    </div>
    @elseif(request('kelas_id') && request('mata_pelajaran_id') && $nilaiRows->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
        <div class="text-6xl mb-4">🎓</div>
        <h3 class="font-bold text-slate-600">Tidak ada murid di kelas ini</h3>
    </div>
    @else
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
        <div class="text-6xl mb-4">📝</div>
        <h3 class="font-bold text-slate-600 mb-1">Pilih kelas dan mata pelajaran untuk mengisi nilai</h3>
        <p class="text-sm text-slate-400">Gunakan filter di atas.</p>
    </div>
    @endif

</div>

@push('scripts')
<script>
// Live preview nilai akhir & predikat
function hitungAkhir(row) {
    const get = (type) => {
        const el = document.querySelector(`.nilai-input[data-row="${row}"][data-type="${type}"]`);
        return el && el.value !== '' ? parseFloat(el.value) : null;
    };

    const tugas = get('tugas');
    const uts   = get('uts');
    const uas   = get('uas');

    const akhirEl    = document.getElementById('akhir-' + row);
    const predikatEl = document.getElementById('predikat-' + row);

    if (tugas === null && uts === null && uas === null) {
        akhirEl.textContent    = '—';
        predikatEl.textContent = '—';
        predikatEl.className   = 'inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-slate-50 text-slate-400';
        return;
    }

    const akhir = ((tugas ?? 0) * 0.20 + (uts ?? 0) * 0.30 + (uas ?? 0) * 0.50).toFixed(2);
    akhirEl.textContent = akhir;

    let predikat = 'D';
    let cls      = 'bg-red-50 text-red-700';
    if (akhir >= 90)      { predikat = 'A'; cls = 'bg-emerald-50 text-emerald-700'; }
    else if (akhir >= 75) { predikat = 'B'; cls = 'bg-teal-50 text-teal-700'; }
    else if (akhir >= 60) { predikat = 'C'; cls = 'bg-amber-50 text-amber-700'; }

    predikatEl.textContent = predikat;
    predikatEl.className   = `inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold ${cls}`;
}

document.addEventListener('input', function (e) {
    if (e.target.classList.contains('nilai-input')) {
        hitungAkhir(e.target.dataset.row);
    }
});
</script>
@endpush
@endsection
