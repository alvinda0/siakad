<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JadwalUjian;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class JadwalUjianGuruController extends Controller
{
    /**
     * Tampilkan daftar jadwal ujian untuk mata pelajaran yang diampu guru yang login.
     */
    public function index(Request $request)
    {
        $guru = Auth::user();

        // Ambil ID mata pelajaran yang diampu guru ini
        $mapelIds = MataPelajaran::where('guru_id', $guru->id)->pluck('id');

        $query = JadwalUjian::with(['kelas', 'mataPelajaran', 'guru'])
            ->whereIn('mata_pelajaran_id', $mapelIds);

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        $ujian = $query->orderBy('tanggal')->orderBy('jam_mulai')->get();

        $byTanggal = $ujian->groupBy(fn($u) => $u->tanggal->format('Y-m-d'));

        $stats = [
            'total'    => $ujian->count(),
            'upcoming' => $ujian->filter(fn($u) => ! $u->isSudahLewat())->count(),
            'sudah_upload' => $ujian->whereNotNull('file_soal')->count(),
            'belum_upload' => $ujian->whereNull('file_soal')->count(),
        ];

        return view('guru.jadwal-ujian.index', compact(
            'ujian', 'byTanggal', 'stats'
        ));
    }

    /**
     * Upload atau ganti file soal untuk jadwal ujian tertentu.
     * Hanya boleh dilakukan guru yang mengampu mata pelajaran ujian tersebut.
     */
    public function uploadSoal(Request $request, JadwalUjian $jadwalUjian)
    {
        $guru = Auth::user();

        // Pastikan guru ini mengampu mata pelajaran dari ujian tersebut
        $isAmpu = MataPelajaran::where('id', $jadwalUjian->mata_pelajaran_id)
            ->where('guru_id', $guru->id)
            ->exists();

        abort_unless($isAmpu, 403, 'Anda tidak memiliki akses untuk mengunggah soal ujian ini.');

        $request->validate([
            'file_soal' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,xls,xlsx',
                'max:10240', // max 10 MB
            ],
        ], [
            'file_soal.required' => 'File soal wajib dipilih.',
            'file_soal.file'     => 'Upload harus berupa file.',
            'file_soal.mimes'    => 'Format file harus PDF, DOC, DOCX, XLS, atau XLSX.',
            'file_soal.max'      => 'Ukuran file maksimal 10 MB.',
        ]);

        // Hapus file lama jika ada
        if ($jadwalUjian->file_soal) {
            Storage::disk('public')->delete($jadwalUjian->file_soal);
        }

        $path = $request->file('file_soal')->store('soal-ujian', 'public');

        $jadwalUjian->update(['file_soal' => $path]);

        $mapelNama = $jadwalUjian->mataPelajaran->nama;

        return back()->with('success', "File soal untuk {$mapelNama} ({$jadwalUjian->jenis}) berhasil diunggah.");
    }

    /**
     * Hapus file soal dari jadwal ujian.
     */
    public function hapusSoal(JadwalUjian $jadwalUjian)
    {
        $guru = Auth::user();

        $isAmpu = MataPelajaran::where('id', $jadwalUjian->mata_pelajaran_id)
            ->where('guru_id', $guru->id)
            ->exists();

        abort_unless($isAmpu, 403, 'Anda tidak memiliki akses untuk menghapus soal ujian ini.');

        if ($jadwalUjian->file_soal) {
            Storage::disk('public')->delete($jadwalUjian->file_soal);
            $jadwalUjian->update(['file_soal' => null]);
        }

        return back()->with('success', 'File soal berhasil dihapus.');
    }
}
