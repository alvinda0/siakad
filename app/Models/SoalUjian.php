<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SoalUjian extends Model
{
    protected $table = 'soal_ujian';

    protected $fillable = [
        'jadwal_ujian_id',
        'tipe',
        'nomor',
        'pertanyaan',
        'pilihan_a',
        'pilihan_b',
        'pilihan_c',
        'pilihan_d',
        'kunci_jawaban',
        'kunci_jawaban_essay',
        'poin',
    ];

    protected $casts = [
        'nomor' => 'integer',
        'poin'  => 'integer',
    ];

    // ── Options ──────────────────────────────────────────────────────────────

    public static array $tipe = [
        'pilihan_ganda' => 'Pilihan Ganda',
        'essay'         => 'Essay',
    ];

    public static array $pilihanLabel = [
        'A' => 'A',
        'B' => 'B',
        'C' => 'C',
        'D' => 'D',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function jadwalUjian(): BelongsTo
    {
        return $this->belongsTo(JadwalUjian::class, 'jadwal_ujian_id');
    }

    public function jawaban(): HasMany
    {
        return $this->hasMany(JawabanUjian::class, 'soal_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isPilihanGanda(): bool
    {
        return $this->tipe === 'pilihan_ganda';
    }

    public function isEssay(): bool
    {
        return $this->tipe === 'essay';
    }

    /**
     * Pilihan dalam format array [A => teks, B => teks, ...]
     */
    public function pilihanArray(): array
    {
        return array_filter([
            'A' => $this->pilihan_a,
            'B' => $this->pilihan_b,
            'C' => $this->pilihan_c,
            'D' => $this->pilihan_d,
        ]);
    }

    /**
     * Hitung skor essay (0–poin).
     *
     * Strategi:
     * 1. Hitung coverage: berapa % kata penting dari kunci jawaban ada di jawaban murid
     *    (setelah normalisasi stem sederhana)
     * 2. Skor = coverage × poin, min 0, max poin
     * 3. Threshold minimum 15% untuk dapat nilai > 0
     *
     * @param  string  $jawabanMurid
     * @return int  Skor 0 s/d $this->poin
     */
    public function hitungNilaiEssay(string $jawabanMurid): int
    {
        if (! $this->kunci_jawaban_essay || trim($jawabanMurid) === '') {
            return 0;
        }

        $similarity = $this->hitungKemiripan(
            $this->kunci_jawaban_essay,
            $jawabanMurid
        );

        if ($similarity < 0.15) {
            return 0;
        }

        return min((int) round($similarity * $this->poin), $this->poin);
    }

    /**
     * Hitung koefisien kemiripan (0.0–1.0) antara kunci dan jawaban murid.
     *
     * Menggunakan coverage-based scoring:
     * - Berapa % token kunci tercakup di jawaban murid (token = potongan 4 karakter pertama)
     * - Digabung dengan Jaccard untuk kasus jawaban singkat
     * - Ambil nilai tertinggi dari keduanya
     */
    public function hitungKemiripan(string $teksA, string $teksB): float
    {
        $tokensKunci   = $this->normalize($teksA);
        $tokensJawaban = $this->normalize($teksB);

        if (empty($tokensKunci) || empty($tokensJawaban)) {
            return 0.0;
        }

        // Coverage: berapa % token kunci muncul di jawaban
        $matched = 0;
        foreach ($tokensKunci as $token) {
            // 1. Exact match
            if (in_array($token, $tokensJawaban, true)) {
                $matched++;
                continue;
            }
            // 2. Prefix match (4 char): token kunci berawalan sama dengan token jawaban
            if (mb_strlen($token) >= 4) {
                $p4 = mb_substr($token, 0, 4);
                foreach ($tokensJawaban as $tj) {
                    if (mb_strpos($tj, $p4) === 0) {
                        $matched++;
                        continue 2;
                    }
                }
            }
            // 3. Contained match: token kunci ada di dalam token jawaban (atau sebaliknya)
            //    Menangani nasal assimilation: kunci 'simp' cocok dengan jawaban 'yimp'
            //    karena keduanya berasal dari 'simpan'. Cek apakah token kunci ≥3 char
            //    adalah substring dari original jawaban (sebelum stem), atau vice versa.
            //    Pragmatisnya: bandingkan token yang >= 3 char dengan semua token jawaban,
            //    jika salah satu adalah substring dari yang lain → match.
            if (mb_strlen($token) >= 3) {
                foreach ($tokensJawaban as $tj) {
                    if (mb_strlen($tj) >= 3
                        && (mb_strpos($tj, $token) !== false
                            || mb_strpos($token, $tj) !== false)) {
                        $matched++;
                        continue 2;
                    }
                }
            }
        }

        $coverage = $matched / count($tokensKunci);

        // Jaccard sebagai fallback (mencegah over-score pada jawaban sangat panjang)
        $intersection = 0;
        foreach ($tokensKunci as $token) {
            if (in_array($token, $tokensJawaban, true)) {
                $intersection++;
            }
        }
        $union   = count(array_unique(array_merge($tokensKunci, $tokensJawaban)));
        $jaccard = $union > 0 ? $intersection / $union : 0.0;

        // Ambil yang lebih menguntungkan murid, tapi batasi 1.0
        return min(max($coverage, $jaccard), 1.0);
    }

    /**
     * Normalisasi teks: lowercase, hapus tanda baca, strip stopword,
     * kembalikan array token unik (sudah distem dengan prefix 4 char).
     */
    private function normalize(string $teks): array
    {
        $teks = mb_strtolower($teks);

        // Ganti NBSP dan tanda baca dengan spasi
        $teks = preg_replace('/[\xA0\x{00A0}]/u', ' ', $teks);
        $teks = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $teks);

        $kata = preg_split('/\s+/', trim($teks), -1, PREG_SPLIT_NO_EMPTY);

        $stopwords = [
            'dan','atau','yang','di','ke','dari','pada','untuk','dengan','adalah',
            'ini','itu','juga','sudah','belum','akan','bisa','dapat','harus','ada',
            'tidak','dalam','oleh','serta','bahwa','karena','sehingga','namun',
            'seperti','yaitu','yakni','antara','setiap','semua','salah','satu',
            'dua','tiga','suatu','sebuah','agar','supaya','ketika','saat','setelah',
            'sebelum','lebih','sangat','amat','paling','jadi','maka','bila','jika',
            'the','a','an','is','are','was','were','of','to','in','for','on',
            'with','as','by','at','be','has','had','have','this','that','it',
            'and','or','but','not','also','so','than','can','may','will',
        ];

        $tokens = [];
        foreach ($kata as $k) {
            if (mb_strlen($k) <= 2 || in_array($k, $stopwords)) {
                continue;
            }
            $root = $this->getRoot($k);
            // Buang token hasil stem yang terlalu pendek (< 3 char) — artefak stemmer
            if (mb_strlen($root) >= 3) {
                $tokens[] = $root;
            }
        }

        return array_values(array_unique($tokens));
    }

    /**
     * Ambil root kata: strip awalan umum Bahasa Indonesia,
     * lalu ambil 5 karakter pertama sebagai fingerprint.
     * Ini menangani: analisis = menganalisis, deploy = deployment,
     * integrasi = mengintegrasikan, dsb.
     */
    private function getRoot(string $kata): string
    {
        // Strip akhiran -kan, -an, -i, -nya, -ment, -tion dulu
        $suffixes = ['ment','tion','kan','an','nya','lah','kah','ing'];
        foreach ($suffixes as $sfx) {
            $len = mb_strlen($sfx);
            if (mb_strlen($kata) > $len + 3
                && mb_substr($kata, -$len) === $sfx) {
                $kata = mb_substr($kata, 0, -$len);
                break;
            }
        }

        // Strip awalan — urutan: panjang ke pendek, lebih spesifik ke umum
        // Mencakup nasal assimilation Bahasa Indonesia:
        //   meny+ipm → menyimpan, memu+dah → memudahkan, menu+rang → mengurangi
        $prefixes = [
            // 6 karakter
            'mempel','memper','pember','penyer','mempro',
            // 5 karakter
            'mengg','menge','menye','menca','menci','menco','mencu',
            'menda','mende','mendi','mendo','mendu',
            'menja','menje','menji','menjo','menju',
            'mensa','menta','menyu',
            'penge',
            // nasal: memu + konsonan (memudahkan, memupuk, memulas)
            // menu + konsonan (menular, menurun, menutupi → ter-strip jadi 'tupi')
            'memu','menu',
            // 4 karakter
            'meng','meny','memb','memp','peng','peny','pemb','pemp',
            // 3 karakter
            'mem','men','me',
            'pem','pen','pe',
            'ber','ter','per',
            // 2 karakter
            'ke','se','di',
        ];

        foreach ($prefixes as $pfx) {
            $len = mb_strlen($pfx);
            if (mb_strlen($kata) > $len + 3
                && mb_substr($kata, 0, $len) === $pfx) {
                $kata = mb_substr($kata, $len);
                break;
            }
        }

        // Ambil 5 karakter pertama dari root sebagai token fingerprint
        return mb_strlen($kata) > 5 ? mb_substr($kata, 0, 5) : $kata;
    }
}
