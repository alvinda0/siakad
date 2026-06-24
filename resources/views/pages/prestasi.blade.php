@extends('layouts.public')
@section('title', 'Prestasi')

@push('styles')
<style>
    .sec-label { color:var(--teal); letter-spacing:.1em; font-size:.75rem; font-weight:700; text-transform:uppercase; }
    .sec-title  { color:var(--navy); font-weight:800; }
    .sec-divider { display:inline-block; width:48px; height:4px; border-radius:2px; background: linear-gradient(90deg, var(--teal), var(--gold)); margin-top:8px; margin-bottom:12px; }
    .card-alt { background:#FAFEFF; border-radius:1.25rem; border: 1px solid rgba(27,122,138,0.10); transition: transform .25s, box-shadow .25s; }
    .card-alt:hover { transform:translateY(-6px); box-shadow:0 20px 44px rgba(27,122,138,0.13); }
    .cta-banner { background: linear-gradient(135deg, var(--teal-deeper) 0%, var(--teal) 60%, #2BA8BF 100%); position:relative; overflow:hidden; }
    .cta-banner::before { content:''; position:absolute; right:-60px; top:-60px; width:280px; height:280px; border-radius:50%; background:rgba(255,255,255,0.07); }
    .page-hero { background: linear-gradient(135deg, var(--teal-deeper) 0%, #0D4A57 100%); }
</style>
@endpush

@section('content')

<!-- Hero -->
<div class="page-hero text-white py-16 px-4 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background:radial-gradient(circle at 80% 50%, white, transparent 60%);"></div>
    <div class="max-w-7xl mx-auto relative z-10">
        <p class="text-xs font-bold uppercase tracking-widest text-white/70 mb-2">Kebanggaan Kita</p>
        <h1 class="text-4xl font-black mb-3">Prestasi Gemilang</h1>
        <p class="text-white/80 max-w-lg text-sm leading-relaxed">Penghargaan yang diraih siswa dan sekolah di tingkat kabupaten, provinsi, dan nasional.</p>
    </div>
    <div class="absolute bottom-0 left-0 w-full overflow-hidden" style="height:72px; line-height:0;">
        <svg viewBox="0 0 1440 72" preserveAspectRatio="none" class="w-full h-full" fill="white">
            <path d="M0,36 C480,72 960,0 1440,36 L1440,72 L0,72 Z"/>
        </svg>
    </div>
</div>

<!-- Stats Bar -->
<div class="py-8 px-4 bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto grid grid-cols-3 gap-4 text-center">
        @php $stats = [['200+','Total Prestasi','🏅'],['15+','Prestasi Nasional','🥇'],['50+','Prestasi Provinsi','🥈']]; @endphp
        @foreach($stats as [$num,$label,$icon])
        <div>
            <span class="text-2xl">{{ $icon }}</span>
            <p class="text-2xl font-black mt-1" style="color:var(--gold);">{{ $num }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $label }}</p>
        </div>
        @endforeach
    </div>
</div>

<!-- Content -->
<section class="py-20 px-4 bg-white">
    <div class="max-w-7xl mx-auto">

        <!-- Filter Level -->
        <div class="flex flex-wrap gap-2 mb-10 justify-center">
            @php $levels = ['Semua', 'Nasional', 'Provinsi', 'Kabupaten']; @endphp
            @foreach($levels as $lv)
            <button onclick="filterLevel(this, '{{ $lv }}')"
                    class="lv-btn text-xs font-bold px-4 py-1.5 rounded-full border transition {{ $lv === 'Semua' ? 'text-white border-transparent' : 'border-slate-200 text-slate-500 bg-white' }}"
                    style="{{ $lv === 'Semua' ? 'background:var(--gold);' : '' }}">
                {{ $lv }}
            </button>
            @endforeach
        </div>

        <div id="prestasi-grid" class="grid md:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">
            @php
            $prestasi = [
                ['🥇','Juara 1 LKS Teknik Komputer','Nasional','2024','Ahmad Fauzi'],
                ['🥇','Juara 1 Karya Tulis Ilmiah','Provinsi','2024','Siti Rahayu'],
                ['🥈','Juara 2 Olimpiade Matematika','Nasional','2024','Budi Santoso'],
                ['🏆','Sekolah Adiwiyata Mandiri','Nasional','2024','Institusi'],
                ['🥇','Juara 1 Debat Bahasa Inggris','Provinsi','2025','Dewi Lestari'],
                ['🥈','Juara 2 Paskibraka','Kabupaten','2024','Rudi Hermawan'],
                ['🎖️','Finalis Lomba Robotika','Nasional','2025','Tim Robotika'],
                ['🥇','Juara 1 Futsal Pelajar','Kabupaten','2025','Tim Futsal'],
                ['🥇','Juara 1 Pidato Bahasa Inggris','Provinsi','2024','Manda Safitri'],
                ['🥈','Juara 2 Olimpiade Biologi','Provinsi','2024','Rizky Pratama'],
                ['🥉','Juara 3 Lomba Fotografi','Kabupaten','2025','Anisa Putri'],
                ['🏅','Peserta Terbaik Pramuka','Nasional','2024','Regu Garuda'],
            ];
            $levelColor = ['Nasional'=>'#B91C1C','Provinsi'=>'#1B7A8A','Kabupaten'=>'#E6920A'];
            @endphp
            @foreach($prestasi as [$medali,$judul,$level,$tahun,$nama])
            <div class="card-alt p-5 text-center prestasi-item" data-level="{{ $level }}">
                <div class="text-4xl mb-3">{{ $medali }}</div>
                <span class="inline-block text-xs font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full mb-2"
                      style="color:{{ $levelColor[$level] ?? '#1B7A8A' }}; background:{{ $levelColor[$level] ?? '#1B7A8A' }}18;">
                    {{ $level }}
                </span>
                <h3 class="font-bold text-sm leading-snug mb-2" style="color:var(--navy);">{{ $judul }}</h3>
                <p class="text-xs text-gray-400">{{ $nama }} &middot; {{ $tahun }}</p>
            </div>
            @endforeach
        </div>

        <div id="empty-level" class="hidden text-center py-20 text-gray-400">
            <div class="text-5xl mb-3">🏅</div>
            <p class="font-medium">Tidak ada prestasi dalam kategori ini.</p>
        </div>

        <!-- CTA -->
        <div class="cta-banner rounded-2xl p-7 flex flex-col md:flex-row items-center justify-between gap-5">
            <div class="relative z-10">
                <h3 class="text-xl font-bold text-white">🏆 Total 200+ Prestasi Diraih</h3>
                <p class="text-sm mt-1 text-white/75">Dari tingkat kabupaten hingga nasional dalam 5 tahun terakhir</p>
            </div>
            <a href="{{ route('kandidat.create') }}" class="relative z-10 font-bold px-7 py-2.5 rounded-xl text-sm whitespace-nowrap text-white"
               style="background:var(--gold);">
                Bergabung Bersama Kami →
            </a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
function filterLevel(btn, level) {
    document.querySelectorAll('.lv-btn').forEach(b => {
        b.style.background = '';
        b.classList.add('border-slate-200','text-slate-500','bg-white');
        b.classList.remove('border-transparent','text-white');
    });
    btn.style.background = 'var(--gold)';
    btn.style.color = '#fff';
    btn.classList.remove('border-slate-200','text-slate-500','bg-white');
    btn.classList.add('border-transparent','text-white');

    let visible = 0;
    document.querySelectorAll('.prestasi-item').forEach(el => {
        const show = level === 'Semua' || el.dataset.level === level;
        el.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('empty-level').classList.toggle('hidden', visible > 0);
}
</script>
@endpush
