@extends('layouts.public')
@section('title', 'Kontak')

@push('styles')
<style>
    .sec-label { color:var(--teal); letter-spacing:.1em; font-size:.75rem; font-weight:700; text-transform:uppercase; }
    .sec-title  { color:var(--navy); font-weight:800; }
    .sec-divider { display:inline-block; width:48px; height:4px; border-radius:2px; background: linear-gradient(90deg, var(--teal), var(--gold)); margin-top:8px; margin-bottom:12px; }
    .card { background:#fff; border-radius:1.25rem; border: 1px solid rgba(27,122,138,0.08); transition: transform .25s, box-shadow .25s; }
    .card:hover { transform:translateY(-6px); box-shadow:0 20px 44px rgba(27,122,138,0.13); }
    .page-hero { background: linear-gradient(135deg, var(--teal-deeper) 0%, #0D4A57 100%); }
    .icon-ring { width:3.25rem; height:3.25rem; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .map-embed { border-radius:1.25rem; overflow:hidden; border: 1px solid rgba(27,122,138,0.12); }
    input:focus, textarea:focus { outline:none; box-shadow: 0 0 0 3px rgba(27,122,138,0.15); }
</style>
@endpush

@section('content')

<!-- Hero -->
<div class="page-hero text-white py-16 px-4 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background:radial-gradient(circle at 80% 50%, white, transparent 60%);"></div>
    <div class="max-w-7xl mx-auto relative z-10">
        <p class="text-xs font-bold uppercase tracking-widest text-white/70 mb-2">Hubungi Kami</p>
        <h1 class="text-4xl font-black mb-3">Kontak Sekolah</h1>
        <p class="text-white/80 max-w-lg text-sm leading-relaxed">Kami siap membantu menjawab pertanyaan Anda seputar PPDB, akademik, maupun informasi umum sekolah.</p>
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

        <!-- Info Cards -->
        <div class="grid md:grid-cols-3 gap-6 mb-16">
            <!-- Alamat -->
            <div class="card p-7 text-center flex flex-col items-center">
                <div class="icon-ring mb-4" style="background:var(--teal-light);">
                    <svg class="w-6 h-6" style="color:var(--teal);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="font-bold sec-title mb-2">Alamat</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Jl. Klampok-Gombong Km13,<br>Ds. Sampang, Kec. Sempor,<br>Kab. Kebumen, Jawa Tengah</p>
                <a href="https://maps.app.goo.gl/pLxdCxj2gSsvCFgr6" target="_blank" rel="noopener"
                   class="mt-4 text-xs font-semibold transition hover:underline" style="color:var(--teal);">
                    📍 Lihat di Google Maps →
                </a>
            </div>

            <!-- WhatsApp & Email -->
            <div class="card p-7 text-center flex flex-col items-center">
                <div class="icon-ring mb-4" style="background:var(--teal-light);">
                    <svg class="w-6 h-6" style="color:var(--teal);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <h3 class="font-bold sec-title mb-3">WhatsApp & Email</h3>
                <a href="https://wa.me/6281325540947" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 text-sm font-semibold px-4 py-2 rounded-xl mb-2 transition"
                   style="background:#25D366; color:#fff;">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    081325540947
                </a>
                <a href="mailto:smkmuhse@gmail.com"
                   class="text-sm text-gray-500 hover:text-blue-600 transition">smkmuhse@gmail.com</a>
                <p class="text-xs text-gray-400 mt-3">Kontak Tata Usaha:<br>Julaeha, S.Pd.</p>
            </div>

            <!-- Jam Operasional -->
            <div class="card p-7 text-center flex flex-col items-center">
                <div class="icon-ring mb-4" style="background:#FEF3DC;">
                    <svg class="w-6 h-6" style="color:var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="font-bold sec-title mb-4">Jam Operasional</h3>
                <div class="space-y-2 text-sm text-gray-600 w-full">
                    <div class="flex justify-between border-b border-slate-100 pb-2">
                        <span class="font-medium">Senin – Jumat</span>
                        <span class="font-bold text-gray-800">07.00 – 16.00</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100 pb-2">
                        <span class="font-medium">Sabtu</span>
                        <span class="font-bold text-gray-800">07.00 – 12.00</span>
                    </div>
                    <div class="flex justify-between pt-1">
                        <span class="font-medium text-red-400">Minggu</span>
                        <span class="font-bold text-red-400">Libur</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map -->
        <div class="mb-12">
            <h3 class="text-lg font-extrabold sec-title mb-4">📍 Lokasi Sekolah</h3>
            <div class="map-embed bg-slate-100 w-full" style="height:360px;">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.4604216344555!2d109.4703072!3d-7.524653700000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e654e097c11ebe1%3A0x2e00f6ff044613ef!2sSMK%20Muhammadiyah%20Sempor!5e0!3m2!1sid!2sid!4v1782276716516!5m2!1sid!2sid"
                    width="100%" height="360" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="strict-origin-when-cross-origin" title="Peta lokasi SMK Muhammadiyah Sempor">
                </iframe>
            </div>
        </div>


    </div>
</section>

@endsection
