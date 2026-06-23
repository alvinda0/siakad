<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KandidatProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class KandidatController extends Controller
{
    // ── Admin: daftar kandidat ────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = User::whereHas('roles', fn($q) => $q->where('name', Role::CANDIDATE))
                     ->with(['roles', 'kandidatProfile'])
                     ->latest();

        // Filter status
        if ($request->filled('status')) {
            $status = $request->status;
            $query->whereHas('kandidatProfile', fn($q) => $q->where('status', $status));
        }

        $perPage  = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 15;
        $kandidat = $query->paginate($perPage)->withQueryString();

        return view('admin.kandidat.index', compact('kandidat'));
    }

    public function show(User $user)
    {
        $user->load('kandidatProfile');
        $profile = $user->kandidatProfile; // bisa null, view harus handle

        return view('admin.kandidat.show', compact('user', 'profile'));
    }

    public function accept(User $user)
    {
        if (!$user->kandidatProfile) {
            return back()->with('error', 'Kandidat belum memiliki data profil.');
        }

        DB::transaction(function () use ($user) {
            $user->kandidatProfile->update(['status' => 'diterima']);

            // Cabut role kandidat, upgrade ke siswa
            $user->removeRole(Role::CANDIDATE);
            $user->assignRole(Role::STUDENT);
        });

        return back()->with('success', "Kandidat {$user->name} berhasil diterima sebagai siswa.");
    }

    public function reject(User $user)
    {
        if (!$user->kandidatProfile) {
            return back()->with('error', 'Kandidat belum memiliki data profil.');
        }

        $user->kandidatProfile->update(['status' => 'ditolak']);

        return back()->with('success', "Kandidat {$user->name} telah ditolak.");
    }

    // ── Publik: form pendaftaran multi-step ───────────────────────────────────

    /**
     * Step awal — pilih "Didaftarkan Oleh".
     * Jika sesi pendaftaran sudah ada (draft), lanjutkan dari step terakhir.
     */
    public function create(Request $request)
    {
        $profile = null;
        $id = $request->session()->get('daftar_profile_id');
        if ($id) {
            $profile = KandidatProfile::with('user')->find($id);
        }

        // Tentukan step yang diminta
        $requestedStep = (int) $request->query('step', $profile ? ($profile->current_step ?? 2) : 1);

        // Jika belum ada profil, paksa ke step 1
        if (!$profile && $requestedStep > 1) {
            $requestedStep = 1;
        }

        // Boleh navigasi mundur ke step yang sudah selesai,
        // tapi tidak boleh lompat maju melewati current_step
        if ($profile && $requestedStep > ($profile->current_step ?? 1)) {
            $requestedStep = $profile->current_step ?? 1;
        }

        return view('kandidat.create', compact('profile', 'requestedStep'));
    }

    /**
     * Simpan per-step via AJAX / form POST.
     * Body wajib memiliki field `step` (integer 0–6).
     * Pada step 0, akun user dibuat jika belum ada; token sesi disimpan.
     */
    public function store(Request $request)
    {
        $step = (int) $request->input('step', 0);

        return match ($step) {
            1 => $this->storeStep1($request),  // Akun
            2 => $this->storeStep2($request),  // Didaftarkan Oleh
            3 => $this->storeStep3($request),  // Pendidikan
            4 => $this->storeStep4($request),  // Data Diri
            5 => $this->storeStep5($request),  // Kesehatan
            6 => $this->storeStep6($request),  // Dokumen
            7 => $this->storeStep7($request),  // Info Tambahan
            8 => $this->storeStep8($request),  // Orang Tua
            default => back()->withErrors(['step' => 'Langkah tidak valid.']),
        };
    }

    // ──────────────────────────────────────────────────────────────────────────
    // STEP HANDLERS
    // ──────────────────────────────────────────────────────────────────────────

    /** Step 1: Buat akun + profil draft */
    private function storeStep1(Request $request)
    {
        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:users,email'],
            'password'     => ['required', 'confirmed', 'min:8'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required'        => 'Email wajib diisi.',
            'email.unique'          => 'Email sudah terdaftar.',
            'password.required'     => 'Kata sandi wajib diisi.',
            'password.confirmed'    => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        DB::transaction(function () use ($data, $request) {
            $user = User::create([
                'name'     => $data['nama_lengkap'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
            $user->assignRole(Role::CANDIDATE);

            $profile = KandidatProfile::create([
                'user_id'          => $user->id,
                'nama_lengkap'     => $data['nama_lengkap'],
                'didaftarkan_oleh' => '',
                'jurusan'          => '',
                'sistem_pendidikan'=> '',
                'current_step'     => 2,
                'status'           => 'draft',
            ]);

            $request->session()->put('daftar_profile_id', $profile->id);
        });

        return redirect()->route('kandidat.create', ['step' => 2])
                         ->with('success', 'Akun berhasil dibuat. Lanjutkan pendaftaran.');
    }

    /** Step 2: Didaftarkan Oleh */
    private function storeStep2(Request $request)
    {
        $profile = $this->getSessionProfile($request);

        $data = $request->validate([
            'didaftarkan_oleh' => ['required', 'string'],
        ], [
            'didaftarkan_oleh.required' => 'Pilih siapa yang mendaftarkan.',
        ]);

        $profile->update(array_merge($data, ['current_step' => 3]));

        return redirect()->route('kandidat.create', ['step' => 3]);
    }

    /** Step 3: Pendidikan */
    private function storeStep3(Request $request)
    {
        $profile = $this->getSessionProfile($request);

        $data = $request->validate([
            'jurusan'          => ['required', 'string'],
            'sistem_pendidikan'=> ['required', 'string'],
        ]);

        $profile->update(array_merge($data, ['current_step' => 4]));

        return redirect()->route('kandidat.create', ['step' => 4]);
    }

    /** Step 4: Data Diri Calon Siswa */
    private function storeStep4(Request $request)
    {
        $profile = $this->getSessionProfile($request);

        $data = $request->validate([
            'nik'                   => ['required', 'digits:16', 'unique:kandidat_profiles,nik,' . $profile->id],
            'nisn'                  => ['required', 'string', 'max:20'],
            'nama_lengkap'          => ['required', 'string', 'max:255'],
            'nama_panggilan'        => ['nullable', 'string', 'max:100'],
            'kewarganegaraan'       => ['required', 'string', 'max:50'],
            'tempat_lahir'          => ['required', 'string', 'max:100'],
            'tanggal_lahir'         => ['required', 'date'],
            'jenis_kelamin'         => ['required', 'in:L,P'],
            'agama'                 => ['required', 'string'],
            'status_keluarga'       => ['required', 'in:Yatim,Piatu,Yatim Piatu,Keluarga Lengkap'],
            'status_dalam_keluarga' => ['nullable', 'string'],
            'anak_ke'               => ['required', 'integer', 'min:1'],
            'dari_saudara'          => ['required', 'integer', 'min:1'],
            'total_saudara_kandung' => ['required', 'integer', 'min:0'],
            'total_saudara_tiri'    => ['nullable', 'integer', 'min:0'],
            'total_saudara_angkat'  => ['nullable', 'integer', 'min:0'],
            'asal_sekolah'          => ['required', 'string', 'max:255'],
            'lama_belajar'          => ['required', 'integer', 'min:1'],
            'nomor_ijazah'          => ['required', 'string', 'max:100'],
            'tanggal_ijazah'        => ['required', 'date'],
            'npsn'                  => ['required', 'string', 'max:20'],
            'penerima_kip'          => ['nullable'],
            'nomor_kip'             => ['nullable', 'string', 'max:50'],
            'status_tinggal'        => ['required', 'string'],
            'bahasa_sehari_hari'    => ['required', 'string', 'max:100'],
            'saudara_di_sekolah'    => ['nullable', 'string', 'max:255'],
            'moda_transportasi'     => ['required', 'string'],
            'jarak_sekolah_km'      => ['required', 'numeric', 'min:0'],
            'waktu_tempuh_jam'      => ['required', 'numeric', 'min:0'],
            'foto'                  => [$profile->foto ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png', 'max:10240'],
            'no_hp'                 => ['required', 'string', 'regex:/^08\d{8,13}$/'],
            'email_kontak'          => ['nullable', 'email', 'max:255'],
            'provinsi'              => ['required', 'string', 'max:100'],
            'kabupaten'             => ['required', 'string', 'max:100'],
            'kecamatan'             => ['required', 'string', 'max:100'],
            'desa'                  => ['required', 'string', 'max:100'],
            'rt'                    => ['required', 'string', 'max:5'],
            'rw'                    => ['required', 'string', 'max:5'],
            'alamat_lengkap'        => ['required', 'string', 'max:500'],
        ], [
            'nik.required'    => 'NIK wajib diisi.',
            'nik.digits'      => 'NIK harus tepat 16 digit angka.',
            'nik.unique'      => 'NIK sudah terdaftar.',
            'no_hp.required'  => 'No HP wajib diisi.',
            'no_hp.regex'     => 'No HP harus diawali 08 dan minimal 10 angka.',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('kandidat/foto', 'public');
        }

        // Simpan email kontak ke kolom 'email' di profil (bukan user email)
        if (!empty($data['email_kontak'])) {
            $data['email'] = $data['email_kontak'];
        } else {
            // Fallback ke email akun user jika email kontak dikosongkan
            $data['email'] = $profile->user->email ?? $profile->email;
        }
        unset($data['email_kontak']);

        $data['penerima_kip'] = $request->boolean('penerima_kip');
        $data['current_step'] = 5;

        $profile->update($data);

        return redirect()->route('kandidat.create', ['step' => 5]);
    }

    /** Step 5: Kesehatan */
    private function storeStep5(Request $request)
    {
        $profile = $this->getSessionProfile($request);

        $data = $request->validate([
            'riwayat_kesehatan' => ['nullable', 'string', 'max:1000'],
            'disabilitas'       => ['nullable', 'string'],
            'tinggi_badan'      => ['nullable', 'numeric', 'min:0'],
            'berat_badan'       => ['nullable', 'numeric', 'min:0'],
        ]);

        $profile->update(array_merge($data, ['current_step' => 6]));

        return redirect()->route('kandidat.create', ['step' => 6]);
    }

    /** Step 6: Dokumen */
    private function storeStep6(Request $request)
    {
        $profile = $this->getSessionProfile($request);

        $request->validate([
            'dokumen_kk'     => [$profile->dokumen_kk ? 'nullable' : 'required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'dokumen_ijazah' => [$profile->dokumen_ijazah ? 'nullable' : 'required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [
            'dokumen_kk.required'     => 'Kartu Keluarga wajib diunggah.',
            'dokumen_ijazah.required' => 'Ijazah / Keterangan Lulus wajib diunggah.',
        ]);

        $update = ['current_step' => 7];

        if ($request->hasFile('dokumen_kk')) {
            $update['dokumen_kk'] = $request->file('dokumen_kk')->store('kandidat/dokumen', 'public');
        }
        if ($request->hasFile('dokumen_ijazah')) {
            $update['dokumen_ijazah'] = $request->file('dokumen_ijazah')->store('kandidat/dokumen', 'public');
        }

        $profile->update($update);

        return redirect()->route('kandidat.create', ['step' => 7]);
    }

    /** Step 7: Informasi Tambahan (prestasi) */
    private function storeStep7(Request $request)
    {
        $profile = $this->getSessionProfile($request);

        $request->validate([
            'prestasi'           => ['nullable', 'array'],
            'prestasi.*.nama'    => ['nullable', 'string', 'max:255'],
            'prestasi.*.dokumen' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $prestasi = [];
        foreach ($request->input('prestasi', []) as $i => $item) {
            $dokPath = null;
            if ($request->hasFile("prestasi.{$i}.dokumen")) {
                $dokPath = $request->file("prestasi.{$i}.dokumen")
                                   ->store('kandidat/prestasi', 'public');
            }
            if (!empty($item['nama'])) {
                $prestasi[] = ['nama' => $item['nama'], 'dokumen' => $dokPath];
            }
        }

        $profile->update([
            'prestasi'     => $prestasi ?: null,
            'current_step' => 8,
        ]);

        return redirect()->route('kandidat.create', ['step' => 8]);
    }

    /** Step 8: Data Orang Tua — selesai */
    private function storeStep8(Request $request)
    {
        $profile = $this->getSessionProfile($request);

        $data = $request->validate([
            'nama_ayah'              => ['required', 'string', 'max:255'],
            'nik_ayah'               => ['required', 'digits:16'],
            'pendidikan_ayah'        => ['required', 'string'],
            'pekerjaan_ayah'         => ['required', 'string'],
            'status_pernikahan_ayah' => ['required', 'string'],
            'no_hp_ayah'             => ['required', 'string', 'regex:/^08\d{8,13}$/'],
            'tempat_lahir_ayah'      => ['required', 'string', 'max:100'],
            'tanggal_lahir_ayah'     => ['required', 'date'],
            'kewarganegaraan_ayah'   => ['required', 'string', 'max:50'],
            'agama_ayah'             => ['required', 'string'],
            'nama_ibu'               => ['required', 'string', 'max:255'],
            'nik_ibu'                => ['required', 'digits:16'],
            'pendidikan_ibu'         => ['required', 'string'],
            'pekerjaan_ibu'          => ['required', 'string'],
            'status_pernikahan_ibu'  => ['required', 'string'],
            'no_hp_ibu'              => ['required', 'string', 'regex:/^08\d{8,13}$/'],
            'tempat_lahir_ibu'       => ['required', 'string', 'max:100'],
            'tanggal_lahir_ibu'      => ['required', 'date'],
            'kewarganegaraan_ibu'    => ['required', 'string', 'max:50'],
            'agama_ibu'              => ['required', 'string'],
            'penghasilan_ortu'       => ['required', 'string'],
        ], [
            'nama_ayah.required'              => 'Nama ayah wajib diisi.',
            'nik_ayah.required'               => 'NIK ayah wajib diisi.',
            'nik_ayah.digits'                 => 'NIK ayah harus tepat 16 digit angka.',
            'pendidikan_ayah.required'        => 'Pendidikan ayah wajib dipilih.',
            'pekerjaan_ayah.required'         => 'Pekerjaan ayah wajib dipilih.',
            'status_pernikahan_ayah.required' => 'Status pernikahan ayah wajib dipilih.',
            'no_hp_ayah.required'             => 'No HP ayah wajib diisi.',
            'no_hp_ayah.regex'                => 'No HP ayah harus diawali 08 dan minimal 10 angka.',
            'tempat_lahir_ayah.required'      => 'Tempat lahir ayah wajib diisi.',
            'tanggal_lahir_ayah.required'     => 'Tanggal lahir ayah wajib diisi.',
            'kewarganegaraan_ayah.required'   => 'Kewarganegaraan ayah wajib diisi.',
            'agama_ayah.required'             => 'Agama ayah wajib dipilih.',
            'nama_ibu.required'               => 'Nama ibu wajib diisi.',
            'nik_ibu.required'                => 'NIK ibu wajib diisi.',
            'nik_ibu.digits'                  => 'NIK ibu harus tepat 16 digit angka.',
            'pendidikan_ibu.required'         => 'Pendidikan ibu wajib dipilih.',
            'pekerjaan_ibu.required'          => 'Pekerjaan ibu wajib dipilih.',
            'status_pernikahan_ibu.required'  => 'Status pernikahan ibu wajib dipilih.',
            'no_hp_ibu.required'              => 'No HP ibu wajib diisi.',
            'no_hp_ibu.regex'                 => 'No HP ibu harus diawali 08 dan minimal 10 angka.',
            'tempat_lahir_ibu.required'       => 'Tempat lahir ibu wajib diisi.',
            'tanggal_lahir_ibu.required'      => 'Tanggal lahir ibu wajib diisi.',
            'kewarganegaraan_ibu.required'    => 'Kewarganegaraan ibu wajib diisi.',
            'agama_ibu.required'              => 'Agama ibu wajib dipilih.',
            'penghasilan_ortu.required'       => 'Penghasilan orang tua wajib dipilih.',
        ]);

        $profile->update(array_merge($data, [
            'current_step' => 9,
            'status'       => 'submitted',
        ]));

        $request->session()->forget('daftar_profile_id');

        return redirect()->route('kandidat.selesai');
    }

    /** Halaman selesai */
    public function selesai()
    {
        return view('kandidat.selesai');
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function getSessionProfile(Request $request): KandidatProfile
    {
        $id = $request->session()->get('daftar_profile_id');

        abort_unless($id, 422, 'Sesi pendaftaran tidak ditemukan. Silakan mulai ulang.');

        return KandidatProfile::findOrFail($id);
    }
}
