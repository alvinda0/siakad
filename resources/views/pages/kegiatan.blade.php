@extends('layouts.public')
@section('title', 'Kegiatan')

@push('styles')
<style>
    .sec-label { color:var(--teal); letter-spacing:.1em; font-size:.75rem; font-weight:700; text-transform:uppercase; }
    .page-hero { background: linear-gradient(135deg, var(--teal-deeper) 0%, #0D4A57 100%); }
    .kat-card {
        background:#fff;
        border-radius:1.25rem;
        border:1px solid rgba(27,122,138,0.08);
        overflow:hidden;
        transition: transform .25s, box-shadow .25s;
    }
    .kat-card:hover { transform:translateY(-6px); box-shadow:0 20px 44px rgba(27,122,138,0.13); }
    .kat-card img { width:100%; height:200px; object-fit:cover; display:block; }
</style>
@endpush

@section('content')

<!-- Hero -->
<div class="page-hero text-white py-16 px-4 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background:radial-gradient(circle at 80% 50%, white, transparent 60%);"></div>
    <div class="max-w-7xl mx-auto relative z-10">
        <p class="sec-label text-white/70 mb-2">Agenda Sekolah</p>
        <h1 class="text-4xl font-black mb-3">Kegiatan Sekolah</h1>
        <p class="text-white/80 max-w-lg text-sm leading-relaxed">Berbagai kegiatan untuk mendukung perkembangan siswa secara akademik dan non-akademik.</p>
    </div>
    <div class="absolute bottom-0 left-0 w-full overflow-hidden" style="height:72px; line-height:0;">
        <svg viewBox="0 0 1440 72" preserveAspectRatio="none" class="w-full h-full" fill="#F0F7F9">
            <path d="M0,36 C480,72 960,0 1440,36 L1440,72 L0,72 Z"/>
        </svg>
    </div>
</div>

<!-- Content -->
<section class="py-20 px-4" style="background:#F0F7F9;">
    <div class="max-w-7xl mx-auto">

        @if($kegiatan->isEmpty())
            <div class="text-center py-20 text-gray-400">
                <div class="text-5xl mb-3">📭</div>
                <p class="font-medium">Belum ada kegiatan yang tersedia saat ini.</p>
            </div>
        @else

        <!-- Filter Kategori -->
        <div class="flex flex-wrap gap-2 mb-10 justify-center">
            <button onclick="filterKat(this, 'Semua')"
                    class="kat-btn text-xs font-bold px-4 py-1.5 rounded-full border transition text-white border-transparent"
                    style="background:var(--teal);">
                Semua
            </button>
            @foreach($kategori as $val => $label)
                @if($kegiatan->where('kategori', $val)->count())
                <button onclick="filterKat(this, '{{ $val }}')"
                        class="kat-btn text-xs font-bold px-4 py-1.5 rounded-full border transition border-slate-200 text-slate-500 bg-white">
                    {{ $label }}
                </button>
                @endif
            @endforeach
        </div>

        <!-- Grid -->
        <div id="kegiatan-grid" class="grid md:grid-cols-3 gap-6">
            @foreach($kegiatan as $item)
            <div class="kat-card kegiatan-item" data-kat="{{ $item->kategori }}">
                {{-- Gambar --}}
                <div class="relative overflow-hidden" style="height:200px;">
                    <img src="{{ $item->gambarUrl() }}"
                         alt="{{ $item->judul }}"
                         class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                         loading="lazy"
                         onerror="this.src='{{ asset('image/kegiatan/lainnya.svg') }}'">
                    {{-- Badge kategori overlay --}}
                    <span class="absolute top-3 left-3 text-xs font-bold px-2.5 py-1 rounded-full shadow"
                          style="background:rgba(27,122,138,0.9); color:#fff; backdrop-filter:blur(4px);">
                        {{ $item->kategori }}
                    </span>
                </div>
                {{-- Konten --}}
                <div class="p-5">
                    <h3 class="font-bold text-base leading-snug mb-2" style="color:var(--navy);">{{ $item->judul }}</h3>
                    <p class="text-xs text-gray-400 mb-3 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $item->tanggal_kegiatan }}
                    </p>
                    @if($item->deskripsi)
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $item->deskripsi }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- Empty state (filter) -->
        <div id="empty-kat" class="hidden text-center py-20 text-gray-400">
            <div class="text-5xl mb-3">📭</div>
            <p class="font-medium">Tidak ada kegiatan dalam kategori ini.</p>
        </div>

        @endif
    </div>
</section>

@endsection

@push('scripts')
<script>
function filterKat(btn, kat) {
    document.querySelectorAll('.kat-btn').forEach(b => {
        b.style.background = '';
        b.style.color = '';
        b.classList.add('border-slate-200','text-slate-500','bg-white');
        b.classList.remove('border-transparent','text-white');
    });
    btn.style.background = 'var(--teal)';
    btn.style.color = '#fff';
    btn.classList.remove('border-slate-200','text-slate-500','bg-white');
    btn.classList.add('border-transparent','text-white');

    let visible = 0;
    document.querySelectorAll('.kegiatan-item').forEach(el => {
        const show = kat === 'Semua' || el.dataset.kat === kat;
        el.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('empty-kat').classList.toggle('hidden', visible > 0);
}
</script>
@endpush
