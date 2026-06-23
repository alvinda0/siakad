@extends('admin.layouts.app')

@section('title', 'Tambah Kelas')
@section('breadcrumb', 'Kelas / Tambah')

@section('content')
<div class="max-w-2xl space-y-5">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.kelas.index') }}"
           class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-extrabold text-slate-800">Tambah Kelas</h1>
            <p class="text-sm text-slate-500">Isi data kelas baru di bawah ini.</p>
        </div>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('admin.kelas.store') }}"
          class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-5">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Nama Kelas --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Nama Kelas <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama" value="{{ old('nama') }}"
                       placeholder="Contoh: X TKJ 1"
                       class="w-full px-3 py-2.5 rounded-xl border text-sm
                              {{ $errors->has('nama') ? 'border-red-400 bg-red-50' : 'border-slate-200' }}
                              focus:outline-none focus:ring-2 focus:ring-teal-300">
                @error('nama')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tingkat --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Tingkat <span class="text-red-500">*</span>
                </label>
                <select name="tingkat"
                        class="w-full px-3 py-2.5 rounded-xl border text-sm
                               {{ $errors->has('tingkat') ? 'border-red-400 bg-red-50' : 'border-slate-200' }}
                               focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">— Pilih Tingkat —</option>
                    @foreach(\App\Models\Kelas::$tingkat as $val => $label)
                        <option value="{{ $val }}" {{ old('tingkat') === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('tingkat')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jurusan --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Jurusan <span class="text-red-500">*</span>
                </label>
                <select name="jurusan"
                        class="w-full px-3 py-2.5 rounded-xl border text-sm
                               {{ $errors->has('jurusan') ? 'border-red-400 bg-red-50' : 'border-slate-200' }}
                               focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="">— Pilih Jurusan —</option>
                    @foreach(\App\Models\Kelas::$jurusan as $val => $label)
                        <option value="{{ $val }}" {{ old('jurusan') === $val ? 'selected' : '' }}>
                            {{ $val }} — {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('jurusan')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tahun Ajaran --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Tahun Ajaran <span class="text-red-500">*</span>
                </label>
                <input type="number" name="tahun_ajaran"
                       value="{{ old('tahun_ajaran', date('Y')) }}"
                       min="2000" max="2100"
                       placeholder="{{ date('Y') }}"
                       class="w-full px-3 py-2.5 rounded-xl border text-sm
                              {{ $errors->has('tahun_ajaran') ? 'border-red-400 bg-red-50' : 'border-slate-200' }}
                              focus:outline-none focus:ring-2 focus:ring-teal-300">
                <p class="mt-1 text-xs text-slate-400">Isi tahun mulai, misal 2026 untuk TA 2026/2027</p>
                @error('tahun_ajaran')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kapasitas --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Kapasitas <span class="text-red-500">*</span>
                </label>
                <input type="number" name="kapasitas"
                       value="{{ old('kapasitas', 32) }}"
                       min="1" max="100"
                       class="w-full px-3 py-2.5 rounded-xl border text-sm
                              {{ $errors->has('kapasitas') ? 'border-red-400 bg-red-50' : 'border-slate-200' }}
                              focus:outline-none focus:ring-2 focus:ring-teal-300">
                @error('kapasitas')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Wali Kelas --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Wali Kelas</label>
                <input type="text" name="wali_kelas" value="{{ old('wali_kelas') }}"
                       placeholder="Nama wali kelas (opsional)"
                       class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm
                              focus:outline-none focus:ring-2 focus:ring-teal-300">
                @error('wali_kelas')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Keterangan --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Keterangan</label>
                <textarea name="keterangan" rows="3"
                          placeholder="Keterangan tambahan (opsional)"
                          class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm resize-none
                                 focus:outline-none focus:ring-2 focus:ring-teal-300">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
                    style="background: var(--teal);">
                Simpan Kelas
            </button>
            <a href="{{ route('admin.kelas.index') }}"
               class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                Batal
            </a>
        </div>
    </form>

</div>
@endsection
