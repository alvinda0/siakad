@extends('layouts.public')
@section('title', 'Informasi')

@push('styles')
<style>
    .sec-title { color:var(--navy); font-weight:800; }
    .page-hero { background: linear-gradient(135deg, var(--teal-deeper) 0%, #0D4A57 100%); }
</style>
@endpush

@section('content')

<!-- Hero -->
<div class="page-hero text-white py-16 px-4 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background:radial-gradient(circle at 80% 50%, white, transparent 60%);"></div>
    <div class="max-w-7xl mx-auto relative z-10">
        <p class="text-xs font-bold uppercase tracking-widest text-white/70 mb-2">PPDB {{ $tahunAjaran }}</p>
        <h1 class="text-4xl font-black mb-3">Informasi</h1>
        <p class="text-white/80 max-w-lg text-sm leading-relaxed">Pengumuman penerimaan siswa baru, beasiswa, dan promo program strategis yang tersedia.</p>
    </div>
    <div class="absolute bottom-0 left-0 w-full overflow-hidden" style="height:72px; line-height:0;">
        <svg viewBox="0 0 1440 72" preserveAspectRatio="none" class="w-full h-full" fill="#F0F7F9">
            <path d="M0,36 C480,72 960,0 1440,36 L1440,72 L0,72 Z"/>
        </svg>
    </div>
</div>

<!-- Content -->
<section class="py-16 px-4" style="background:#F0F7F9;">
    <div class="max-w-7xl mx-auto space-y-16">

        {{-- ══════════════ BEASISWA ══════════════ --}}
        <div>
            <h2 class="text-2xl font-extrabold sec-title mb-2 flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-white text-base"
                      style="background:var(--teal);">🎓</span>
                Informasi Beasiswa
            </h2>
            <p class="text-sm text-slate-500 mb-6">Daftar beasiswa yang tersedia untuk calon siswa dan siswa aktif.</p>

            @if($beasiswa->isEmpty())
                <div class="bg-white rounded-2xl border border-slate-100 p-10 text-center text-sm text-slate-400 shadow-sm">
                    <div class="text-4xl mb-3">📭</div>
                    <p>Belum ada informasi beasiswa saat ini. Silakan cek kembali nanti.</p>
                </div>
            @else
                <div class="overflow-x-auto rounded-2xl border border-slate-100 shadow-sm">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="background:var(--teal);" class="text-white">
                                <th class="px-5 py-3.5 font-semibold text-left w-10">No</th>
                                <th class="px-5 py-3.5 font-semibold text-left">Jenis Beasiswa</th>
                                <th class="px-5 py-3.5 font-semibold text-left">Syarat</th>
                                <th class="px-5 py-3.5 font-semibold text-left">Benefit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 bg-white">
                            @foreach($beasiswa as $i => $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-5 py-4 text-slate-400 font-medium">{{ $i + 1 }}</td>
                                <td class="px-5 py-4 font-semibold text-slate-800">{{ $item->jenis }}</td>
                                <td class="px-5 py-4 text-slate-500 whitespace-pre-line leading-relaxed">{{ $item->syarat ?: '—' }}</td>
                                <td class="px-5 py-4 text-slate-500 whitespace-pre-line leading-relaxed">{{ $item->benefit ?: '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- ══════════════ PROMO PROGRAM ══════════════ --}}
        <div>
            <h2 class="text-2xl font-extrabold sec-title mb-2 flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-white text-base"
                      style="background:var(--gold);">⭐</span>
                Promo Program Strategis
            </h2>
            <p class="text-sm text-slate-500 mb-6">Program strategis dengan promo khusus untuk pendaftar baru.</p>

            @if($promo->isEmpty())
                <div class="bg-white rounded-2xl border border-slate-100 p-10 text-center text-sm text-slate-400 shadow-sm">
                    <div class="text-4xl mb-3">📭</div>
                    <p>Belum ada informasi promo program saat ini. Silakan cek kembali nanti.</p>
                </div>
            @else
                <div class="overflow-x-auto rounded-2xl border border-slate-100 shadow-sm">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="background:var(--gold);" class="text-white">
                                <th class="px-5 py-3.5 font-semibold text-left w-10">No</th>
                                <th class="px-5 py-3.5 font-semibold text-left">Jenis Program</th>
                                <th class="px-5 py-3.5 font-semibold text-left">Syarat</th>
                                <th class="px-5 py-3.5 font-semibold text-left">Benefit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 bg-white">
                            @foreach($promo as $i => $item)
                            <tr class="hover:bg-amber-50 transition">
                                <td class="px-5 py-4 text-slate-400 font-medium">{{ $i + 1 }}</td>
                                <td class="px-5 py-4 font-semibold text-slate-800">{{ $item->jenis }}</td>
                                <td class="px-5 py-4 text-slate-500 whitespace-pre-line leading-relaxed">{{ $item->syarat ?: '—' }}</td>
                                <td class="px-5 py-4 text-slate-500 whitespace-pre-line leading-relaxed">{{ $item->benefit ?: '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- ══════════════ PENGUMUMAN PPDB ══════════════ --}}
        <div>
            <h2 class="text-2xl font-extrabold sec-title mb-2">Pengumuman Siswa Diterima</h2>
            <p class="text-sm text-slate-500 mb-6">Bagi pendaftar yang diterima, harap segera melakukan daftar ulang sebelum batas waktu.</p>

            <!-- Toolbar -->
            <form method="GET" action="{{ route('pengumuman') }}"
                  class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <label class="text-sm text-slate-500 font-medium">Tampilkan:</label>
                    <select name="per_page" onchange="this.form.submit()"
                            class="text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white text-slate-700 font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400 cursor-pointer">
                        @foreach([25, 50, 100, 1000] as $n)
                            <option value="{{ $n }}" {{ request('per_page', 25) == $n ? 'selected' : '' }}>{{ $n }} data</option>
                        @endforeach
                    </select>
                    @if(request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif
                </div>

                <div class="relative w-full sm:w-72">
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Cari nama / NISN…"
                           class="w-full text-sm border border-slate-200 rounded-xl pl-9 pr-4 py-2 bg-white text-slate-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                    <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    @if(request('per_page'))
                        <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                    @endif
                    @if(request('q'))
                        <a href="{{ route('pengumuman') }}" class="absolute right-3 top-2.5 text-slate-300 hover:text-slate-500 transition">✕</a>
                    @endif
                </div>
            </form>

            <!-- Tabel -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                @if($diterima->isEmpty())
                    <div class="p-16 text-center">
                        <div class="text-5xl mb-4">🎓</div>
                        @if(request('q'))
                            <p class="text-slate-500 font-medium">Tidak ada hasil untuk "<strong>{{ request('q') }}</strong>".</p>
                            <a href="{{ route('pengumuman') }}" class="mt-3 inline-block text-sm font-semibold"
                               style="color:var(--teal);">Tampilkan semua →</a>
                        @else
                            <p class="text-slate-500 font-medium">Belum ada pengumuman siswa diterima.</p>
                            <p class="text-sm text-slate-400 mt-1">Silakan cek kembali nanti.</p>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50/60">
                                    <th class="px-5 py-3.5 text-left font-semibold text-slate-500 uppercase tracking-wide text-xs w-10">No</th>
                                    <th class="px-5 py-3.5 text-left font-semibold text-slate-500 uppercase tracking-wide text-xs">Nama</th>
                                    <th class="px-5 py-3.5 text-left font-semibold text-slate-500 uppercase tracking-wide text-xs">No. Pendaftaran</th>
                                    <th class="px-5 py-3.5 text-left font-semibold text-slate-500 uppercase tracking-wide text-xs">Asal Sekolah</th>
                                    <th class="px-5 py-3.5 text-left font-semibold text-slate-500 uppercase tracking-wide text-xs">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($diterima as $i => $siswa)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-5 py-3.5 text-slate-400 text-xs">{{ $diterima->firstItem() + $i }}</td>
                                    <td class="px-5 py-3.5 font-medium text-slate-800">{{ $siswa->nama_lengkap }}</td>
                                    <td class="px-5 py-3.5 text-slate-500 font-mono tracking-wide">
                                        @php
                                            $nisn = $siswa->nisn ?? '';
                                            $masked = strlen($nisn) > 7
                                                ? '****' . substr($nisn, -7)
                                                : str_repeat('*', max(0, strlen($nisn) - 3)) . substr($nisn, -3);
                                        @endphp
                                        {{ $masked ?: '—' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-600">{{ $siswa->asal_sekolah ?: '—' }}</td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                            ✓ Diterima
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($diterima->hasPages())
                    <div class="px-5 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-slate-500">
                        <span>
                            Menampilkan {{ $diterima->firstItem() }}–{{ $diterima->lastItem() }}
                            dari <strong>{{ $diterima->total() }}</strong> siswa diterima
                        </span>
                        <div class="flex items-center gap-1">
                            @if($diterima->onFirstPage())
                                <span class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-300 cursor-not-allowed select-none">‹</span>
                            @else
                                <a href="{{ $diterima->previousPageUrl() }}"
                                   class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-teal-50 text-slate-600 hover:text-teal-700 transition font-semibold">‹</a>
                            @endif

                            @foreach($diterima->getUrlRange(max(1, $diterima->currentPage()-2), min($diterima->lastPage(), $diterima->currentPage()+2)) as $page => $url)
                                @if($page == $diterima->currentPage())
                                    <span class="px-3 py-1.5 rounded-lg text-white font-bold text-xs"
                                          style="background:var(--teal);">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}"
                                       class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-teal-50 text-slate-600 hover:text-teal-700 transition text-xs font-semibold">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if($diterima->hasMorePages())
                                <a href="{{ $diterima->nextPageUrl() }}"
                                   class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-teal-50 text-slate-600 hover:text-teal-700 transition font-semibold">›</a>
                            @else
                                <span class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-300 cursor-not-allowed select-none">›</span>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="px-5 py-4 border-t border-slate-100 text-sm text-slate-500">
                        Total <strong>{{ $diterima->total() }}</strong> siswa diterima
                    </div>
                    @endif
                @endif
            </div>

            <!-- Info daftar ulang -->
            <div class="mt-5 p-5 rounded-2xl border flex gap-4 items-start"
                 style="background:#FEF3DC; border-color:#E6920A44;">
                <span class="text-2xl shrink-0">⚠️</span>
                <div>
                    <p class="font-bold text-sm" style="color:var(--gold);">Informasi Daftar Ulang</p>
                    <p class="text-sm text-gray-600 mt-1">Siswa yang diterima wajib melakukan daftar ulang paling lambat <strong>7 hari</strong> setelah pengumuman. Hubungi TU sekolah untuk informasi lebih lanjut.</p>
                    <a href="{{ route('kontak') }}" class="inline-block mt-2 text-sm font-semibold" style="color:var(--gold);">Hubungi Sekolah →</a>
                </div>
            </div>
        </div>

        <!-- CTA -->
        <div class="rounded-2xl p-7 flex flex-col md:flex-row items-center justify-between gap-5"
             style="background:linear-gradient(135deg, var(--teal-deeper), var(--teal));">
            <div class="text-white">
                <h3 class="text-lg font-bold">📝 Tertarik mendaftar beasiswa?</h3>
                <p class="text-sm text-white/75 mt-1">Hubungi TU sekolah untuk informasi lebih lengkap dan persyaratan pendaftaran.</p>
            </div>
            <a href="{{ route('kontak') }}" class="shrink-0 font-bold px-6 py-2.5 rounded-xl text-sm whitespace-nowrap"
               style="background:var(--gold); color:#fff;">
                Hubungi Sekolah →
            </a>
        </div>

    </div>
</section>

@endsection
