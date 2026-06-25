<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalUjian;
use App\Models\SesiUjian;
use App\Models\SoalUjian;
use Illuminate\Http\Request;

class SoalUjianController extends Controller
{
    /**
     * Halaman manajemen soal untuk satu jadwal ujian.
     */
    public function index(JadwalUjian $jadwalUjian)
    {
        $soalList = $jadwalUjian->soalList()->get();

        // Statistik pengerjaan
        $totalMurid  = $jadwalUjian->kelas->siswa()->count();
        $totalSelesai = SesiUjian::where('jadwal_ujian_id', $jadwalUjian->id)
                                  ->where('status', 'selesai')
                                  ->count();
        $totalSedang  = SesiUjian::where('jadwal_ujian_id', $jadwalUjian->id)
                                  ->where('status', 'sedang')
                                  ->count();

        $jadwalUjian->load(['kelas', 'mataPelajaran', 'guru']);

        return view('admin.soal-ujian.index', compact(
            'jadwalUjian', 'soalList', 'totalMurid', 'totalSelesai', 'totalSedang'
        ));
    }

    /**
     * Tambah soal baru.
     */
    public function store(Request $request, JadwalUjian $jadwalUjian)
    {
        $data = $request->validate([
            'tipe'                 => ['required', 'in:pilihan_ganda,essay'],
            'nomor'                => ['required', 'integer', 'min:1', 'max:200'],
            'pertanyaan'           => ['required', 'string', 'max:5000'],
            'pilihan_a'            => ['nullable', 'required_if:tipe,pilihan_ganda', 'string', 'max:1000'],
            'pilihan_b'            => ['nullable', 'required_if:tipe,pilihan_ganda', 'string', 'max:1000'],
            'pilihan_c'            => ['nullable', 'required_if:tipe,pilihan_ganda', 'string', 'max:1000'],
            'pilihan_d'            => ['nullable', 'required_if:tipe,pilihan_ganda', 'string', 'max:1000'],
            'kunci_jawaban'        => ['nullable', 'required_if:tipe,pilihan_ganda', 'in:A,B,C,D'],
            'kunci_jawaban_essay'  => ['nullable', 'string', 'max:5000'],
            'poin'                 => ['required', 'integer', 'min:1', 'max:100'],
        ], [
            'tipe.required'            => 'Tipe soal wajib dipilih.',
            'nomor.required'           => 'Nomor soal wajib diisi.',
            'pertanyaan.required'      => 'Pertanyaan wajib diisi.',
            'pilihan_a.required_if'    => 'Pilihan A wajib diisi untuk soal pilihan ganda.',
            'pilihan_b.required_if'    => 'Pilihan B wajib diisi untuk soal pilihan ganda.',
            'pilihan_c.required_if'    => 'Pilihan C wajib diisi untuk soal pilihan ganda.',
            'pilihan_d.required_if'    => 'Pilihan D wajib diisi untuk soal pilihan ganda.',
            'kunci_jawaban.required_if'=> 'Kunci jawaban wajib dipilih untuk soal pilihan ganda.',
            'kunci_jawaban.in'         => 'Kunci jawaban harus A, B, C, atau D.',
            'poin.required'            => 'Poin wajib diisi.',
        ]);

        // Cek nomor soal unik
        $exists = SoalUjian::where('jadwal_ujian_id', $jadwalUjian->id)
                            ->where('nomor', $data['nomor'])
                            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['nomor' => "Nomor soal {$data['nomor']} sudah ada."])
                ->withInput()
                ->with('open_modal_create', true);
        }

        $data['jadwal_ujian_id'] = $jadwalUjian->id;

        // Essay tidak butuh pilihan dan kunci PG
        if ($data['tipe'] === 'essay') {
            $data['pilihan_a'] = $data['pilihan_b'] = $data['pilihan_c'] = $data['pilihan_d'] = null;
            $data['kunci_jawaban'] = null;
        } else {
            // PG tidak butuh kunci essay
            $data['kunci_jawaban_essay'] = null;
        }

        SoalUjian::create($data);

        return back()->with('success', "Soal nomor {$data['nomor']} berhasil ditambahkan.");
    }

    /**
     * Update soal.
     */
    public function update(Request $request, JadwalUjian $jadwalUjian, SoalUjian $soal)
    {
        abort_unless($soal->jadwal_ujian_id === $jadwalUjian->id, 404);

        $data = $request->validate([
            'tipe'                 => ['required', 'in:pilihan_ganda,essay'],
            'nomor'                => ['required', 'integer', 'min:1', 'max:200'],
            'pertanyaan'           => ['required', 'string', 'max:5000'],
            'pilihan_a'            => ['nullable', 'required_if:tipe,pilihan_ganda', 'string', 'max:1000'],
            'pilihan_b'            => ['nullable', 'required_if:tipe,pilihan_ganda', 'string', 'max:1000'],
            'pilihan_c'            => ['nullable', 'required_if:tipe,pilihan_ganda', 'string', 'max:1000'],
            'pilihan_d'            => ['nullable', 'required_if:tipe,pilihan_ganda', 'string', 'max:1000'],
            'kunci_jawaban'        => ['nullable', 'required_if:tipe,pilihan_ganda', 'in:A,B,C,D'],
            'kunci_jawaban_essay'  => ['nullable', 'string', 'max:5000'],
            'poin'                 => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        // Cek nomor unik (kecuali dirinya sendiri)
        $exists = SoalUjian::where('jadwal_ujian_id', $jadwalUjian->id)
                            ->where('nomor', $data['nomor'])
                            ->where('id', '!=', $soal->id)
                            ->exists();

        if ($exists) {
            return back()->withErrors(['nomor' => "Nomor soal {$data['nomor']} sudah ada."]);
        }

        if ($data['tipe'] === 'essay') {
            $data['pilihan_a'] = $data['pilihan_b'] = $data['pilihan_c'] = $data['pilihan_d'] = null;
            $data['kunci_jawaban'] = null;
        } else {
            $data['kunci_jawaban_essay'] = null;
        }

        $soal->update($data);

        return back()->with('success', "Soal nomor {$soal->nomor} berhasil diperbarui.");
    }

    /**
     * Hapus soal.
     */
    public function destroy(JadwalUjian $jadwalUjian, SoalUjian $soal)
    {
        abort_unless($soal->jadwal_ujian_id === $jadwalUjian->id, 404);

        $nomor = $soal->nomor;
        $soal->delete();

        return back()->with('success', "Soal nomor {$nomor} berhasil dihapus.");
    }

    /**
     * Halaman rekap hasil pengerjaan oleh semua murid.
     */
    public function rekap(JadwalUjian $jadwalUjian)
    {
        $sesiList = SesiUjian::with('murid')
                             ->where('jadwal_ujian_id', $jadwalUjian->id)
                             ->orderBy('nilai_total', 'desc')
                             ->get();

        $jadwalUjian->load(['kelas', 'mataPelajaran']);

        $stats = [
            'total_murid'  => $jadwalUjian->kelas->siswa()->count(),
            'selesai'      => $sesiList->where('status', 'selesai')->count(),
            'sedang'       => $sesiList->where('status', 'sedang')->count(),
            'belum'        => $sesiList->where('status', 'belum')->count(),
            'rata2'        => $sesiList->where('status', 'selesai')->avg('nilai_total'),
            'tertinggi'    => $sesiList->where('status', 'selesai')->max('nilai_total'),
            'terendah'     => $sesiList->where('status', 'selesai')->min('nilai_total'),
        ];

        return view('admin.soal-ujian.rekap', compact('jadwalUjian', 'sesiList', 'stats'));
    }
}
