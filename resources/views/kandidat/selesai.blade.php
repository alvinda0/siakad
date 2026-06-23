<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendaftaran Berhasil — SMK Muhammadiyah Sempor</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --teal: #1B7A8A; --teal-dark: #145F6E; --navy: #112D3E; --gold: #E6920A; }
        body { font-family: 'Inter', sans-serif; background: #F0F7F9; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center px-4">

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-10 max-w-md w-full text-center">
        <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-5"
             style="background: #D1FAE5;">
            <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="text-2xl font-extrabold mb-2" style="color: var(--navy);">Pendaftaran Berhasil!</h1>
        <p class="text-slate-500 text-sm leading-relaxed mb-6">
            Terima kasih telah mendaftar di <strong>SMK Muhammadiyah Sempor</strong>.
            Data Anda sedang dalam proses verifikasi. Kami akan menghubungi Anda segera.
        </p>

        <div class="bg-teal-50 border border-teal-100 rounded-xl p-4 mb-6 text-left">
            <p class="text-xs font-bold text-teal-700 uppercase tracking-widest mb-2">Langkah Selanjutnya</p>
            <ul class="space-y-1.5 text-sm text-slate-600">
                <li class="flex gap-2"><span class="text-teal-500 font-bold">1.</span> Tunggu konfirmasi dari pihak sekolah</li>
                <li class="flex gap-2"><span class="text-teal-500 font-bold">2.</span> Pantau email atau nomor HP yang didaftarkan</li>
                <li class="flex gap-2"><span class="text-teal-500 font-bold">3.</span> Siapkan dokumen asli untuk verifikasi</li>
            </ul>
        </div>

        <div class="flex flex-col gap-2">
            <a href="{{ url('/') }}"
               class="block text-sm font-semibold px-6 py-3 rounded-xl text-white transition hover:brightness-110"
               style="background: var(--teal);">
                ← Kembali ke Beranda
            </a>
            <a href="https://wa.me/6281325540947" target="_blank"
               class="block text-sm font-semibold px-6 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                Hubungi Sekolah via WhatsApp
            </a>
        </div>
    </div>

    <p class="text-xs text-slate-400 mt-6">&copy; {{ date('Y') }} SMK Muhammadiyah Sempor</p>
</body>
</html>
