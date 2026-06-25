<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalUjian;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Role;
use App\Models\SoalUjian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;

class JadwalUjianController extends Controller
{
    public function index(Request $request)
    {
        $kelasList = Kelas::orderBy('tingkat')->orderBy('jurusan')->orderBy('nama')->get();
        $mapelList = MataPelajaran::where('aktif', true)->orderBy('nama')->get();
        $guruList  = User::whereHas('roles', fn($q) => $q->where('name', Role::TEACHER))
                         ->orderBy('name')->get();

        $query = JadwalUjian::with(['kelas', 'mataPelajaran', 'guru']);

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('aktif')) {
            $query->where('aktif', $request->aktif === '1');
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // Jika tidak ada filter tanggal maupun bulan, sembunyikan yang sudah lewat
        if (!$request->filled('bulan') && !$request->filled('tanggal')) {
            $query->whereDate('tanggal', '>=', now()->startOfDay());
        }

        $ujian = $query->orderBy('tanggal')->orderBy('jam_mulai')->get();

        // Group by tanggal untuk tampilan kalender/timeline
        $byTanggal = $ujian->groupBy(fn($u) => $u->tanggal->format('Y-m-d'));

        // Stats
        $stats = [
            'total'    => $ujian->count(),
            'upcoming' => $ujian->filter(fn($u) => ! $u->isSudahLewat())->count(),
            'lewat'    => $ujian->filter(fn($u) =>   $u->isSudahLewat())->count(),
            'aktif'    => $ujian->where('aktif', true)->count(),
        ];

        return view('admin.jadwal-ujian.index', compact(
            'ujian', 'byTanggal', 'kelasList', 'mapelList', 'guruList', 'stats'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'              => ['required', 'string', 'max:150'],
            'jenis'             => ['required', 'in:UTS,UAS,UKK,Sumatif,Lainnya'],
            'kelas_id'          => ['required', 'exists:kelas,id'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajaran,id'],
            'guru_id'           => ['nullable', 'exists:users,id'],
            'tanggal'           => ['required', 'date'],
            'jam_mulai'         => ['required', 'date_format:H:i'],
            'jam_selesai'       => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'ruangan'           => ['nullable', 'string', 'max:100'],
            'keterangan'        => ['nullable', 'string', 'max:500'],
            'aktif'             => ['nullable', 'boolean'],
        ], [
            'nama.required'              => 'Nama ujian wajib diisi.',
            'jenis.required'             => 'Jenis ujian wajib dipilih.',
            'kelas_id.required'          => 'Kelas wajib dipilih.',
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'tanggal.required'           => 'Tanggal ujian wajib diisi.',
            'jam_mulai.required'         => 'Jam mulai wajib diisi.',
            'jam_selesai.required'       => 'Jam selesai wajib diisi.',
            'jam_selesai.after'          => 'Jam selesai harus setelah jam mulai.',
        ]);

        $data['aktif'] = $request->boolean('aktif', true);

        $ujian = JadwalUjian::create($data);

        return redirect()
            ->route('admin.jadwal-ujian.index', $request->only('kelas_id', 'jenis'))
            ->with('success', "Jadwal ujian {$ujian->mataPelajaran->nama} ({$ujian->jenis}) berhasil ditambahkan.");
    }

    public function update(Request $request, JadwalUjian $jadwalUjian)
    {
        $data = $request->validate([
            'nama'              => ['required', 'string', 'max:150'],
            'jenis'             => ['required', 'in:UTS,UAS,UKK,Sumatif,Lainnya'],
            'kelas_id'          => ['required', 'exists:kelas,id'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajaran,id'],
            'guru_id'           => ['nullable', 'exists:users,id'],
            'tanggal'           => ['required', 'date'],
            'jam_mulai'         => ['required', 'date_format:H:i'],
            'jam_selesai'       => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'ruangan'           => ['nullable', 'string', 'max:100'],
            'keterangan'        => ['nullable', 'string', 'max:500'],
            'aktif'             => ['nullable', 'boolean'],
        ], [
            'nama.required'              => 'Nama ujian wajib diisi.',
            'jenis.required'             => 'Jenis ujian wajib dipilih.',
            'kelas_id.required'          => 'Kelas wajib dipilih.',
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'tanggal.required'           => 'Tanggal ujian wajib diisi.',
            'jam_mulai.required'         => 'Jam mulai wajib diisi.',
            'jam_selesai.required'       => 'Jam selesai wajib diisi.',
            'jam_selesai.after'          => 'Jam selesai harus setelah jam mulai.',
        ]);

        $data['aktif'] = $request->boolean('aktif', true);
        $jadwalUjian->update($data);

        return redirect()
            ->route('admin.jadwal-ujian.index', $request->only('kelas_id', 'jenis'))
            ->with('success', 'Jadwal ujian berhasil diperbarui.');
    }

    public function destroy(JadwalUjian $jadwalUjian)
    {
        $info = "{$jadwalUjian->mataPelajaran->nama} – {$jadwalUjian->jenis}";
        $jadwalUjian->delete();

        return redirect()->route('admin.jadwal-ujian.index')
                         ->with('success', "Jadwal ujian {$info} berhasil dihapus.");
    }

    public function toggleAktif(JadwalUjian $jadwalUjian)
    {
        $jadwalUjian->update(['aktif' => ! $jadwalUjian->aktif]);

        $status = $jadwalUjian->aktif ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Jadwal ujian berhasil {$status}.");
    }

    public function uploadSoal(Request $request, JadwalUjian $jadwalUjian)
    {
        $request->validate([
            'file_soal' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,xls,xlsx',
                'max:10240',
            ],
        ], [
            'file_soal.required' => 'File soal wajib dipilih.',
            'file_soal.file'     => 'Upload harus berupa file.',
            'file_soal.mimes'    => 'Format file harus PDF, DOC, DOCX, XLS, atau XLSX.',
            'file_soal.max'      => 'Ukuran file maksimal 10 MB.',
        ]);

        $file      = $request->file('file_soal');
        $extension = strtolower($file->getClientOriginalExtension());
        $mapelNama = $jadwalUjian->mataPelajaran->nama;

        // Hanya ekstrak soal dari DOCX
        if ($extension === 'docx') {
            try {
                $soalList = $this->parseDocxSoal($file->getRealPath());
            } catch (\Throwable $e) {
                return back()->withErrors(['file_soal' => 'Gagal membaca file: ' . $e->getMessage()]);
            }

            if (empty($soalList)) {
                return back()->withErrors([
                    'file_soal' => 'Tidak ada soal yang berhasil dibaca dari file. Pastikan format soal sesuai: '
                        . '"1. Pertanyaan", "A. Pilihan", "Jawaban: A".',
                ]);
            }

            DB::transaction(function () use ($jadwalUjian, $soalList, $file) {
                // Hapus soal lama (gunakan DB::table agar pasti terhapus sebelum insert)
                DB::table('soal_ujian')->where('jadwal_ujian_id', $jadwalUjian->id)->delete();

                // Simpan soal baru
                foreach ($soalList as $soal) {
                    SoalUjian::create([
                        'jadwal_ujian_id'     => $jadwalUjian->id,
                        'tipe'                => $soal['tipe'],
                        'nomor'               => $soal['nomor'],
                        'pertanyaan'          => $soal['pertanyaan'],
                        'pilihan_a'           => $soal['pilihan_a'] ?? null,
                        'pilihan_b'           => $soal['pilihan_b'] ?? null,
                        'pilihan_c'           => $soal['pilihan_c'] ?? null,
                        'pilihan_d'           => $soal['pilihan_d'] ?? null,
                        'kunci_jawaban'       => $soal['kunci_jawaban'] ?? null,
                        'kunci_jawaban_essay' => $soal['kunci_jawaban_essay'] ?? null,
                        'poin'                => $soal['poin'],
                    ]);
                }

                // Simpan file referensi
                if ($jadwalUjian->file_soal) {
                    Storage::disk('public')->delete($jadwalUjian->file_soal);
                }
                $path = $file->store('soal-ujian', 'public');
                $jadwalUjian->update(['file_soal' => $path]);
            });

            $jumlah = count($soalList);
            return back()->with('success', "Berhasil mengimpor {$jumlah} soal dari file {$mapelNama} ({$jadwalUjian->jenis}).");
        }

        // Untuk file non-DOCX (PDF, DOC, XLS, XLSX) — simpan file saja
        if ($jadwalUjian->file_soal) {
            Storage::disk('public')->delete($jadwalUjian->file_soal);
        }
        $path = $file->store('soal-ujian', 'public');
        $jadwalUjian->update(['file_soal' => $path]);

        return back()->with('success', "File soal {$mapelNama} ({$jadwalUjian->jenis}) berhasil diunggah. "
            . "Catatan: ekstrak soal otomatis hanya didukung untuk file DOCX.");
    }

    /**
     * Parse soal dari file DOCX.
     *
     * Format yang didukung:
     *   1. Teks pertanyaan
     *   A. Pilihan A
     *   B. Pilihan B
     *   C. Pilihan C
     *   D. Pilihan D
     *   Jawaban: A
     *
     * Atau soal essay (tanpa pilihan & jawaban):
     *   1. Teks pertanyaan
     *   (baris berikutnya bukan A./B./C./D. dan bukan "Jawaban:")
     */
    private function parseDocxSoal(string $filePath): array
    {
        $phpWord = IOFactory::load($filePath);
        $lines   = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                // Setiap elemen di-extract menjadi satu baris teks
                $text = trim($this->extractTextFromElement($element));

                // Element bisa mengandung newline (misal dari Table/ListItem gabungan)
                // pecah per baris agar parser tidak kelewatan
                foreach (explode("\n", $text) as $line) {
                    $line = trim($line);
                    if ($line !== '') {
                        $lines[] = $line;
                    }
                }
            }
        }

        return $this->parseLinesIntoSoal($lines);
    }

    /** Rekursif ambil teks dari element PhpWord */
    private function extractTextFromElement($element): string
    {
        // TextRun: kumpulan inline text/link
        if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
            $text = '';
            foreach ($element->getElements() as $child) {
                $text .= $this->extractTextFromElement($child);
            }
            return $text;
        }

        // Text: node daun teks biasa
        if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
            return $element->getText();
        }

        // Link: ambil teks anchor-nya
        if ($element instanceof \PhpOffice\PhpWord\Element\Link) {
            return $element->getText();
        }

        // Paragraph biasa
        if ($element instanceof \PhpOffice\PhpWord\Element\Paragraph) {
            $text = '';
            foreach ($element->getElements() as $child) {
                $text .= $this->extractTextFromElement($child);
            }
            return $text;
        }

        // ListItem (bullet/numbered list) — paling sering dipakai untuk pilihan A-D
        if ($element instanceof \PhpOffice\PhpWord\Element\ListItem) {
            return $this->extractTextFromElement($element->getTextObject());
        }

        // ListItemRun (list dengan mixed formatting)
        if ($element instanceof \PhpOffice\PhpWord\Element\ListItemRun) {
            $text = '';
            foreach ($element->getElements() as $child) {
                $text .= $this->extractTextFromElement($child);
            }
            return $text;
        }

        // Table
        if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
            $text = '';
            foreach ($element->getRows() as $row) {
                foreach ($row->getCells() as $cell) {
                    foreach ($cell->getElements() as $child) {
                        $cellText = trim($this->extractTextFromElement($child));
                        if ($cellText !== '') {
                            $text .= $cellText . "\n";
                        }
                    }
                }
            }
            return $text;
        }

        return '';
    }

    /** Ubah array baris teks menjadi array soal terstruktur */
    private function parseLinesIntoSoal(array $lines): array
    {
        $soalList    = [];
        $current     = null;
        $poinDefault = 5;

        // Regex patterns
        // Nomor soal: "1." / "1)" / "1 ." — minimal 1 digit, diikuti titik/kurung
        $rNomor = '/^(\d+)\s*[.\)]\s*(.+)/u';

        // Pilihan: "A." / "A)" / "(A)" / "[A]" / "A ." / "A :" / "A -"
        // Toleran spasi sebelum/sesudah tanda, dan NBSP
        $rPilihan = '/^[\(\[]?\s*([A-Da-d])\s*[\)\]]?\s*[.\)\-:]\s*(.+)/u';

        // Kunci jawaban: "Jawaban: A" / "Kunci: ..." / "Jawab: ..."
        $rJawaban = '/^(?:jawaban|jawab|kunci)\s*[:\-]\s*(.+)/ui';

        // Poin per soal: "Poin: 5"
        $rPoin = '/^poin\s*[:\-]\s*(\d+)/ui';

        foreach ($lines as $line) {
            // Normalisasi: ganti NBSP dan whitespace aneh jadi spasi biasa
            $line = preg_replace('/[\xA0\x{00A0}\x{2009}\x{202F}]/u', ' ', $line);
            $line = trim($line);
            if ($line === '') continue;

            // Lewati baris heading/section (tidak mengandung tanda baca soal)
            // Contoh: "Pilihan Ganda", "Essay dan Jawaban", "Soal Ujian dan Kunci Jawaban"
            if (preg_match('/^(pilihan\s+ganda|essay\s*(dan\s*jawaban)?|soal\s+ujian|kunci\s+jawaban\s*$)/ui', $line)) {
                continue;
            }

            // Cek pola nomor soal baru (harus SEBELUM pengecekan pilihan,
            // supaya "1. ..." tidak salah ditangkap sebagai baris lanjutan)
            if (preg_match($rNomor, $line, $m)) {
                // Simpan soal sebelumnya
                if ($current !== null) {
                    $soalList[] = $this->finalizeSoal($current);
                }
                $current = [
                    'nomor'               => (int) $m[1],
                    'pertanyaan'          => trim($m[2]),
                    'pilihan_a'           => null,
                    'pilihan_b'           => null,
                    'pilihan_c'           => null,
                    'pilihan_d'           => null,
                    'kunci_jawaban'       => null,
                    'kunci_jawaban_essay' => null,
                    'poin'                => $poinDefault,
                ];
                continue;
            }

            if ($current === null) continue;

            // Cek pilihan A-D
            if (preg_match($rPilihan, $line, $m)) {
                $key = 'pilihan_' . strtolower($m[1]);
                $current[$key] = trim($m[2]);
                continue;
            }

            // Cek kunci / jawaban
            if (preg_match($rJawaban, $line, $m)) {
                $nilaiJawaban = trim($m[1]);
                // Format: "A" / "A." / "A. teks penjelasan" / "A) teks" → kunci PG
                // Tangkap huruf pertama A–D yang diikuti titik, kurung, spasi, atau akhir string
                if (preg_match('/^([A-Da-d])\s*[.\):\s]/u', $nilaiJawaban, $mk)
                    || preg_match('/^([A-Da-d])$/u', $nilaiJawaban, $mk)) {
                    $current['kunci_jawaban'] = strtoupper($mk[1]);
                } else {
                    // Teks murni tanpa awalan huruf pilihan → kunci jawaban essay
                    $current['kunci_jawaban_essay'] = $nilaiJawaban;
                }
                continue;
            }

            // Cek poin
            if (preg_match($rPoin, $line, $m)) {
                $current['poin'] = (int) $m[1];
                continue;
            }

            // Baris lanjutan pertanyaan:
            // Hanya tambahkan ke pertanyaan kalau belum ada SATU PUN pilihan yang terisi.
            // Jika pilihan sudah mulai diisi, baris tak dikenal diabaikan
            // (mencegah kunci/catatan guru ikut masuk ke pertanyaan).
            $sudahAdaPilihan = $current['pilihan_a'] !== null
                            || $current['pilihan_b'] !== null
                            || $current['pilihan_c'] !== null
                            || $current['pilihan_d'] !== null;

            if (! $sudahAdaPilihan) {
                $current['pertanyaan'] .= ' ' . $line;
            }
        }

        // Simpan soal terakhir
        if ($current !== null) {
            $soalList[] = $this->finalizeSoal($current);
        }

        // Pisahkan PG dan Essay, lalu gabungkan: PG duluan, Essay di belakang
        // Ini menangani file yang punya dua section dengan nomor ulang dari 1
        $pg    = array_filter($soalList, fn($s) => $s['tipe'] === 'pilihan_ganda');
        $essay = array_filter($soalList, fn($s) => $s['tipe'] === 'essay');

        // Urutkan masing-masing berdasarkan nomor asli
        usort($pg,    fn($a, $b) => $a['nomor'] <=> $b['nomor']);
        usort($essay, fn($a, $b) => $a['nomor'] <=> $b['nomor']);

        // Gabungkan dan renomor berurutan
        $soalList = array_values(array_merge(array_values($pg), array_values($essay)));
        foreach ($soalList as $i => &$soal) {
            $soal['nomor'] = $i + 1;
        }
        unset($soal);

        return $soalList;
    }

    /** Tentukan tipe soal dan pastikan data lengkap */
    private function finalizeSoal(array $soal): array
    {
        // Soal dianggap pilihan ganda jika:
        // - minimal ada 2 pilihan (A dan B), ATAU
        // - ada kunci jawaban satu huruf A-D (meski pilihan tidak terparsing sempurna)
        $jumlahPilihan = collect(['pilihan_a','pilihan_b','pilihan_c','pilihan_d'])
            ->filter(fn($k) => $soal[$k] !== null)->count();

        $hasPilihanHuruf = isset($soal['kunci_jawaban'])
            && preg_match('/^[A-D]$/i', (string) $soal['kunci_jawaban']);

        $hasPilihan = $jumlahPilihan >= 2 || $hasPilihanHuruf;

        $soal['tipe'] = $hasPilihan ? 'pilihan_ganda' : 'essay';

        // Essay tidak butuh pilihan & kunci PG
        if ($soal['tipe'] === 'essay') {
            $soal['pilihan_a']   = null;
            $soal['pilihan_b']   = null;
            $soal['pilihan_c']   = null;
            $soal['pilihan_d']   = null;
            $soal['kunci_jawaban'] = null;
        } else {
            // PG tidak butuh kunci essay
            $soal['kunci_jawaban_essay'] = null;
        }

        return $soal;
    }

    public function hapusSoal(JadwalUjian $jadwalUjian)
    {
        if ($jadwalUjian->file_soal) {
            Storage::disk('public')->delete($jadwalUjian->file_soal);
            $jadwalUjian->update(['file_soal' => null]);
        }

        return back()->with('success', 'File soal berhasil dihapus.');
    }

    /**
     * Download template CSV kunci jawaban untuk ujian tertentu.
     * Berisi nomor soal, tipe, dan kolom kunci yang sudah terisi (jika ada).
     */
    public function downloadTemplateKunci(JadwalUjian $jadwalUjian)
    {
        $soalList = SoalUjian::where('jadwal_ujian_id', $jadwalUjian->id)
            ->orderBy('nomor')
            ->get(['nomor', 'tipe', 'kunci_jawaban', 'kunci_jawaban_essay']);

        $mapelNama = preg_replace('/[^A-Za-z0-9\-_]/', '_', $jadwalUjian->mataPelajaran->nama);
        $filename  = "template_kunci_{$mapelNama}_{$jadwalUjian->jenis}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-store, no-cache',
        ];

        $callback = function () use ($soalList) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 agar Excel membaca karakter Indonesia dengan benar
            fputs($handle, "\xEF\xBB\xBF");

            // Header baris
            fputcsv($handle, ['nomor', 'kunci', 'tipe_soal', 'keterangan']);

            if ($soalList->isEmpty()) {
                // Contoh baris jika belum ada soal
                fputcsv($handle, ['1', 'A', 'pilihan_ganda', 'Isi kolom kunci dengan A/B/C/D untuk PG atau teks untuk essay']);
                fputcsv($handle, ['2', 'B', 'pilihan_ganda', '']);
                fputcsv($handle, ['3', '', 'essay', 'Tulis jawaban model di kolom kunci untuk soal essay']);
            } else {
                foreach ($soalList as $soal) {
                    $kunci = $soal->tipe === 'pilihan_ganda'
                        ? ($soal->kunci_jawaban ?? '')
                        : ($soal->kunci_jawaban_essay ?? '');

                    $keterangan = $soal->tipe === 'pilihan_ganda'
                        ? 'Isi dengan A, B, C, atau D'
                        : 'Isi dengan teks jawaban model (untuk auto-grade essay)';

                    fputcsv($handle, [
                        $soal->nomor,
                        $kunci,
                        $soal->tipe,
                        $keterangan,
                    ]);
                }
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Upload kunci jawaban dari file CSV/Excel.
     * Format: kolom pertama = nomor soal, kolom kedua = kunci (A/B/C/D atau teks essay).
     * Baris pertama boleh header (nomor,kunci) — akan dilewati otomatis.
     */
    public function uploadKunci(Request $request, JadwalUjian $jadwalUjian)
    {
        $request->validate([
            'file_kunci' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:2048'],
        ], [
            'file_kunci.required' => 'File kunci jawaban wajib dipilih.',
            'file_kunci.mimes'    => 'Format file harus CSV, TXT, XLSX, atau XLS.',
            'file_kunci.max'      => 'Ukuran file maksimal 2 MB.',
        ]);

        $file = $request->file('file_kunci');
        $ext  = strtolower($file->getClientOriginalExtension());

        // Baca baris dari CSV/TXT
        $rows = [];
        if (in_array($ext, ['csv', 'txt'])) {
            $handle = fopen($file->getRealPath(), 'r');
            while (($cols = fgetcsv($handle, 1000, ',')) !== false) {
                $rows[] = $cols;
            }
            fclose($handle);
        } elseif (in_array($ext, ['xlsx', 'xls'])) {
            // Baca Excel dengan PhpSpreadsheet jika tersedia, fallback ke csv
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                foreach ($sheet->getRowIterator() as $row) {
                    $cells = [];
                    foreach ($row->getCellIterator() as $cell) {
                        $cells[] = $cell->getValue();
                    }
                    $rows[] = $cells;
                }
            } catch (\Throwable $e) {
                return back()->withErrors(['file_kunci' => 'Gagal membaca file Excel: ' . $e->getMessage()]);
            }
        }

        if (empty($rows)) {
            return back()->withErrors(['file_kunci' => 'File kosong atau tidak dapat dibaca.']);
        }

        // Deteksi apakah baris pertama adalah header
        $firstRow = array_map('strtolower', array_map('trim', $rows[0]));
        if (in_array($firstRow[0] ?? '', ['nomor', 'no', 'nomer', 'number', '#'])) {
            array_shift($rows); // buang header
        }

        $updated = 0;
        $skipped = 0;

        DB::transaction(function () use ($jadwalUjian, $rows, &$updated, &$skipped) {
            foreach ($rows as $row) {
                $nomor = isset($row[0]) ? (int) trim((string) $row[0]) : 0;
                $kunci = isset($row[1]) ? trim((string) $row[1]) : '';

                if ($nomor <= 0 || $kunci === '') {
                    $skipped++;
                    continue;
                }

                $soal = SoalUjian::where('jadwal_ujian_id', $jadwalUjian->id)
                    ->where('nomor', $nomor)
                    ->first();

                if (! $soal) {
                    $skipped++;
                    continue;
                }

                if ($soal->tipe === 'pilihan_ganda') {
                    $kunciUpper = strtoupper($kunci);
                    if (in_array($kunciUpper, ['A', 'B', 'C', 'D'], true)) {
                        $soal->update(['kunci_jawaban' => $kunciUpper]);
                        $updated++;
                    } else {
                        $skipped++;
                    }
                } else {
                    // Essay — simpan teks kunci
                    $soal->update(['kunci_jawaban_essay' => $kunci]);
                    $updated++;
                }
            }
        });

        $msg = "Kunci jawaban berhasil diperbarui: {$updated} soal.";
        if ($skipped > 0) {
            $msg .= " {$skipped} baris dilewati (nomor tidak ditemukan atau format tidak valid).";
        }

        return back()->with('success', $msg);
    }

    /**
     * Download template Word (.docx) untuk upload soal ujian.
     * Format: setiap soal diawali "SOAL X." dengan pilihan A/B/C/D dan baris "KUNCI: X".
     */
    public function downloadTemplateSoal(JadwalUjian $jadwalUjian)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        // Gaya dokumen
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);

        $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 14, 'name' => 'Times New Roman']);

        $section = $phpWord->addSection([
            'marginTop'    => 1134, // ~2cm
            'marginBottom' => 1134,
            'marginLeft'   => 1701, // ~3cm
            'marginRight'  => 1134,
        ]);

        // Header dokumen
        $section->addText(
            strtoupper('Template Soal Ujian'),
            ['bold' => true, 'size' => 14, 'name' => 'Times New Roman'],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );

        $mapel = $jadwalUjian->mataPelajaran->nama ?? 'Mata Pelajaran';
        $jenis = $jadwalUjian->jenis ?? '';
        $kelas = optional($jadwalUjian->kelas)->nama ?? '';

        $section->addText(
            "{$mapel} — {$jenis}" . ($kelas ? " | Kelas {$kelas}" : ''),
            ['size' => 11, 'name' => 'Times New Roman', 'color' => '555555'],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );

        $section->addTextBreak(1);

        // Petunjuk
        $section->addText('PETUNJUK PENGISIAN:', ['bold' => true, 'size' => 11, 'name' => 'Times New Roman']);
        $petunjuk = [
            'Tulis soal setelah teks "SOAL X." (ganti X dengan nomor urut).',
            'Untuk soal pilihan ganda, isi pilihan A, B, C, D di bawah soal.',
            'Tulis kunci jawaban di baris "KUNCI: X" (X = A/B/C/D untuk PG, atau jawaban teks untuk essay).',
            'Untuk soal essay, kosongkan pilihan A–D atau hapus baris tersebut.',
            'Jangan mengubah format penanda: SOAL X., A., B., C., D., KUNCI:',
        ];
        foreach ($petunjuk as $i => $p) {
            $section->addText(($i + 1) . '. ' . $p, ['size' => 10, 'name' => 'Times New Roman']);
        }

        $section->addTextBreak(1);
        $section->addText(str_repeat('─', 72), ['size' => 9, 'color' => 'AAAAAA', 'name' => 'Courier New']);
        $section->addTextBreak(1);

        // Contoh 3 soal pilihan ganda + 1 essay
        $contohSoal = [
            [
                'teks'    => 'Tuliskan pertanyaan soal pilihan ganda nomor 1 di sini.',
                'pilihan' => ['A. Pilihan jawaban A', 'B. Pilihan jawaban B', 'C. Pilihan jawaban C', 'D. Pilihan jawaban D'],
                'kunci'   => 'A',
            ],
            [
                'teks'    => 'Tuliskan pertanyaan soal pilihan ganda nomor 2 di sini.',
                'pilihan' => ['A. Pilihan jawaban A', 'B. Pilihan jawaban B', 'C. Pilihan jawaban C', 'D. Pilihan jawaban D'],
                'kunci'   => 'B',
            ],
            [
                'teks'    => 'Tuliskan pertanyaan soal pilihan ganda nomor 3 di sini.',
                'pilihan' => ['A. Pilihan jawaban A', 'B. Pilihan jawaban B', 'C. Pilihan jawaban C', 'D. Pilihan jawaban D'],
                'kunci'   => 'C',
            ],
            [
                'teks'    => 'Tuliskan pertanyaan soal essay nomor 4 di sini.',
                'pilihan' => [],
                'kunci'   => 'Tulis kunci jawaban essay di sini.',
            ],
        ];

        foreach ($contohSoal as $i => $soal) {
            $no = $i + 1;
            // Nomor & teks soal
            $section->addText("SOAL {$no}.", ['bold' => true, 'size' => 12, 'name' => 'Times New Roman']);
            $section->addText($soal['teks'], ['size' => 12, 'name' => 'Times New Roman']);

            // Pilihan (jika ada)
            foreach ($soal['pilihan'] as $pilihan) {
                $section->addText($pilihan, ['size' => 12, 'name' => 'Times New Roman'], ['indent' => 0.5]);
            }

            // Kunci
            $section->addText("KUNCI: {$soal['kunci']}", ['bold' => true, 'size' => 11, 'name' => 'Times New Roman', 'color' => '1E6E42']);
            $section->addTextBreak(1);
        }

        // Tambah 6 soal kosong ekstra (no 5–10) untuk diisi
        for ($i = 5; $i <= 10; $i++) {
            $section->addText("SOAL {$i}.", ['bold' => true, 'size' => 12, 'name' => 'Times New Roman']);
            $section->addText('Tulis pertanyaan di sini.', ['size' => 12, 'name' => 'Times New Roman', 'color' => 'BBBBBB']);
            $section->addText('A. ', ['size' => 12, 'name' => 'Times New Roman', 'color' => 'BBBBBB'], ['indent' => 0.5]);
            $section->addText('B. ', ['size' => 12, 'name' => 'Times New Roman', 'color' => 'BBBBBB'], ['indent' => 0.5]);
            $section->addText('C. ', ['size' => 12, 'name' => 'Times New Roman', 'color' => 'BBBBBB'], ['indent' => 0.5]);
            $section->addText('D. ', ['size' => 12, 'name' => 'Times New Roman', 'color' => 'BBBBBB'], ['indent' => 0.5]);
            $section->addText('KUNCI: ', ['bold' => true, 'size' => 11, 'name' => 'Times New Roman', 'color' => '1E6E42']);
            $section->addTextBreak(1);
        }

        $filename = 'template-soal-' . \Illuminate\Support\Str::slug($mapel . '-' . $jenis) . '.docx';

        $tmpPath = tempnam(sys_get_temp_dir(), 'soal_') . '.docx';
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tmpPath);

        return response()->download($tmpPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }
}
