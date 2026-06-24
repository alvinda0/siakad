@extends('layouts.public')
@section('title', 'Fasilitas')

@push('styles')
<style>
    .sec-label { color:var(--teal); letter-spacing:.1em; font-size:.75rem; font-weight:700; text-transform:uppercase; }
    .sec-title  { color:var(--navy); font-weight:800; }
    .sec-divider { display:inline-block; width:48px; height:4px; border-radius:2px; background: linear-gradient(90deg, var(--teal), var(--gold)); margin-top:8px; margin-bottom:12px; }
    .card { background:#fff; border-radius:1.25rem; border: 1px solid rgba(27,122,138,0.08); transition: transform .25s, box-shadow .25s; }
    .card:hover { transform:translateY(-6px); box-shadow:0 20px 44px rgba(27,122,138,0.13); }
    .page-hero { background: linear-gradient(135deg, var(--teal-deeper) 0%, #0D4A57 100%); }
</style>
@endpush

@section('content')

<!-- Hero -->
<div class="page-hero text-white py-16 px-4 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background:radial-gradient(circle at 80% 50%, white, transparent 60%);"></div>
    <div class="max-w-7xl mx-auto relative z-10">
        <p class="text-xs font-bold uppercase tracking-widest text-white/70 mb-2">Sarana & Prasarana</p>
        <h1 class="text-4xl font-black mb-3">Fasilitas Lengkap</h1>
        <p class="text-white/80 max-w-lg text-sm leading-relaxed">Fasilitas modern untuk mendukung proses belajar dan pengembangan potensi siswa secara optimal.</p>
    </div>
    <div class="absolute bottom-0 left-0 w-full overflow-hidden" style="height:72px; line-height:0;">
        <svg viewBox="0 0 1440 72" preserveAspectRatio="none" class="w-full h-full" fill="white">
            <path d="M0,36 C480,72 960,0 1440,36 L1440,72 L0,72 Z"/>
        </svg>
    </div>
</div>

<!-- Content -->
<section class="py-20 px-4 bg-white">
    <div class="max-w-7xl mx-auto">

        <div class="text-center mb-12">
            <span class="sec-label">Standar Nasional</span>
            <h2 class="text-3xl font-extrabold sec-title mt-1">Fasilitas Unggulan</h2>
            <div class="sec-divider mx-auto"></div>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">Kami terus berinvestasi dalam fasilitas terbaik agar setiap siswa mendapatkan pengalaman belajar yang optimal.</p>
        </div>

        <!-- Fasilitas Utama -->
        <div class="grid md:grid-cols-3 gap-6 mb-16">
            @php
            $fasilitas = [
                ['🔬','Lab Sains','Laboratorium Fisika, Kimia, dan Biologi dengan peralatan modern standar nasional.',['Mikroskop digital','Alat ukur presisi','Bahan kimia lengkap','Ruang ber-AC']],
                ['💻','Lab Komputer','2 ruang lab komputer dengan 40 unit PC terbaru dan koneksi internet 100 Mbps.',['40 unit PC i5 Gen 12','Internet 100 Mbps','Software terkini','AC & proyektor']],
                ['📚','Perpustakaan','Koleksi 15.000+ buku fisik dan akses e-book serta jurnal ilmiah digital.',['15.000+ koleksi buku','Akses e-library','Ruang baca nyaman','Komputer katalog']],
                ['🏟️','Lapangan Olahraga','Lapangan sepak bola, basket, dan voli berstandar untuk kegiatan olahraga rutin.',['Lapangan sepak bola','Lapangan basket','Lapangan voli','Lintasan lari']],
                ['🎭','Aula & Auditorium','Aula serbaguna berkapasitas 800 orang untuk seminar, wisuda, dan pentas seni.',['Kapasitas 800 orang','Sound system modern','AC central','Backstage & lighting']],
                ['🕌','Masjid Sekolah','Masjid sekolah yang luas untuk pembinaan akhlak dan kegiatan keagamaan siswa.',['Kapasitas 500 jamaah','Perpustakaan Al-Quran','Ruang wudhu bersih','Tempat wanita terpisah']],
                ['🍽️','Kantin Sehat','Kantin dengan menu bergizi yang higienis, terjangkau, dan bervariasi setiap hari.',['Menu bergizi bervariasi','Harga terjangkau','Pengawasan kebersihan','Area makan luas']],
                ['🚌','Akses Transportasi','Lokasi strategis dilalui angkot dan tersedia area parkir yang luas dan aman.',['Jalur angkot tersedia','Parkir luas & aman','Pos keamanan 24 jam','Dekat jalan utama']],
                ['🏥','UKS Modern','Unit Kesehatan Siswa dengan peralatan lengkap dan perawat tetap setiap hari sekolah.',['Perawat tetap','Obat-obatan lengkap','Ruang istirahat','Kerjasama Puskesmas']],
            ];
            @endphp
            @foreach($fasilitas as [$icon,$nama,$desk,$fitur])
            <div class="card p-6 flex gap-5">
                <div class="text-5xl shrink-0 leading-none mt-1">{{ $icon }}</div>
                <div class="flex-1">
                    <h3 class="font-bold sec-title mb-1.5">{{ $nama }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed mb-3">{{ $desk }}</p>
                    <ul class="space-y-1">
                        @foreach($fitur as $f)
                        <li class="flex items-center gap-2 text-xs text-gray-500">
                            <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background:var(--teal);"></span>
                            {{ $f }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Stats -->
        <div class="rounded-2xl p-8 grid sm:grid-cols-2 md:grid-cols-4 gap-6 text-center"
             style="background:linear-gradient(135deg, var(--teal-deeper), var(--teal));">
            @php
            $fstats = [['24','Ruang Kelas','🏫'],['3','Lab Komputer','💻'],['2','Lab Sains','🔬'],['1','Perpustakaan Digital','📚']];
            @endphp
            @foreach($fstats as [$num,$label,$icon])
            <div class="text-white">
                <span class="text-3xl">{{ $icon }}</span>
                <p class="text-3xl font-black mt-1" style="color:#F9C940;">{{ $num }}</p>
                <p class="text-sm text-white/75 mt-0.5">{{ $label }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
