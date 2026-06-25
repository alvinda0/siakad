@php $suffix = ($mode === 'edit') ? '_edit' : ''; @endphp

{{-- Tipe --}}
<div>
    <label class="block text-sm font-semibold text-slate-700 mb-2">Tipe Soal <span class="text-red-500">*</span></label>
    <div class="flex gap-3">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="tipe" id="tipe{{ $suffix }}"
                   value="pilihan_ganda"
                   {{ old('tipe', 'pilihan_ganda') === 'pilihan_ganda' ? 'checked' : '' }}
                   onchange="toggleTipe{{ $mode === 'edit' ? 'Edit' : 'Create' }}('pilihan_ganda')">
            <span class="text-sm font-medium text-slate-700">Pilihan Ganda</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="tipe" id="tipe_essay{{ $suffix }}"
                   value="essay"
                   {{ old('tipe') === 'essay' ? 'checked' : '' }}
                   onchange="toggleTipe{{ $mode === 'edit' ? 'Edit' : 'Create' }}('essay')">
            <span class="text-sm font-medium text-slate-700">Essay</span>
        </label>
    </div>
</div>

{{-- Nomor & Poin --}}
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor Soal <span class="text-red-500">*</span></label>
        <input type="number" name="nomor" id="nomor{{ $suffix }}" min="1" max="200"
               value="{{ old('nomor') }}"
               placeholder="Contoh: 1"
               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
    </div>
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Poin <span class="text-red-500">*</span></label>
        <input type="number" name="poin" id="poin{{ $suffix }}" min="1" max="100"
               value="{{ old('poin', 1) }}"
               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
    </div>
</div>

{{-- Pertanyaan --}}
<div>
    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pertanyaan <span class="text-red-500">*</span></label>
    <textarea name="pertanyaan" id="pertanyaan{{ $suffix }}" rows="4"
              placeholder="Tulis pertanyaan di sini..."
              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300 resize-y">{{ old('pertanyaan') }}</textarea>
</div>

{{-- Pilihan Ganda fields --}}
<div id="pg-fields{{ $mode === 'edit' ? '-edit' : '-create' }}" class="{{ old('tipe') === 'essay' ? 'hidden' : '' }}">
    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3">Pilihan Jawaban</p>
    <div class="space-y-3">
        @foreach(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'] as $key => $label)
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-extrabold bg-slate-100 text-slate-600 shrink-0">{{ $label }}</span>
            <input type="text" name="pilihan_{{ $key }}" id="pilihan_{{ $key }}{{ $suffix }}"
                   value="{{ old('pilihan_'.$key) }}"
                   placeholder="Pilihan {{ $label }}"
                   class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
        </div>
        @endforeach
    </div>
    <div class="mt-4">
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kunci Jawaban <span class="text-red-500">*</span></label>
        <select name="kunci_jawaban" id="kunci_jawaban{{ $suffix }}"
                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300">
            <option value="">— Pilih Kunci —</option>
            @foreach(['A', 'B', 'C', 'D'] as $opt)
            <option value="{{ $opt }}" {{ old('kunci_jawaban') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
    </div>
</div>

{{-- Essay fields --}}
<div id="essay-fields{{ $mode === 'edit' ? '-edit' : '-create' }}" class="{{ old('tipe') !== 'essay' ? 'hidden' : '' }}">
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
            Kunci Jawaban Essay
            <span class="ml-1 text-xs font-normal text-slate-400">(untuk penilaian otomatis — opsional)</span>
        </label>
        <textarea name="kunci_jawaban_essay" id="kunci_jawaban_essay{{ $suffix }}" rows="4"
                  placeholder="Tulis jawaban model di sini. Sistem akan mencocokkan kemiripan kata dengan jawaban siswa secara otomatis..."
                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-300 resize-y">{{ old('kunci_jawaban_essay') }}</textarea>
        <p class="mt-1 text-xs text-slate-400">
            💡 Jika diisi, nilai essay dihitung otomatis saat siswa submit. Jawaban siswa yang mengandung kata-kata serupa akan mendapat skor proporsional.
        </p>
    </div>
</div>