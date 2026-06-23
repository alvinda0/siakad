<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KandidatProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MuridController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 15;

        $murid = User::whereHas('roles', fn($q) => $q->where('name', Role::STUDENT))
                     ->with(['roles', 'kandidatProfile.kelas'])
                     ->latest()
                     ->paginate($perPage)
                     ->withQueryString();

        return view('admin.murid.index', compact('murid'));
    }

    public function show(User $user)
    {
        abort_unless(
            $user->roles()->where('name', Role::STUDENT)->exists(),
            404
        );

        $user->load('kandidatProfile.kelas');
        $profile = $user->kandidatProfile;

        $kelasList = \App\Models\Kelas::orderByRaw("CASE tingkat WHEN 'X' THEN 1 WHEN 'XI' THEN 2 WHEN 'XII' THEN 3 ELSE 4 END")
                                      ->orderBy('jurusan')
                                      ->orderBy('nama')
                                      ->get();

        return view('admin.murid.show', compact('user', 'profile', 'kelasList'));
    }

    public function assignKelas(Request $request, User $user)
    {
        abort_unless(
            $user->roles()->where('name', Role::STUDENT)->exists(),
            404
        );

        $data = $request->validate([
            'kelas_id' => ['nullable', 'exists:kelas,id'],
        ]);

        $user->kandidatProfile?->update(['kelas_id' => $data['kelas_id'] ?: null]);

        return back()->with('success', 'Kelas siswa berhasil diperbarui.');
    }

    // ── Pendaftaran Multi-step (Admin) ─────────────────────────────────────

    /**
     * Tampilkan form pendaftaran murid multi-step.
     */
    public function create(Request $request)
    {
        $profile = null;
        $id = $request->session()->get('daftar_murid_profile_id');
        if ($id) {
            $profile = KandidatProfile::find($id);
        }

        $requestedStep = (int) $request->query('step', $profile ? ($profile->current_step ?? 2) : 1);

        if (!$profile && $requestedStep > 1) {
            $requestedStep = 1;
        }

        if ($profile && $requestedStep > ($profile->current_step ?? 1)) {
            $requestedStep = $profile->current_step ?? 1;
        }

        return view('admin.murid.create', compact('profile', 'requestedStep'));
    }

    /**
     * Simpan per-step.
     */
    public function store(Request $request)
    {
        $step = (int) $request->input('step', 0);

        return match ($step) {
            1 => $this->storeStep1($request),
            2 => $this->storeStep2($request),
            3 => $this->storeStep3($request),
            4 => $this->storeStep4($request),
            5 => $this->storeStep5($request),
            6 => $this->storeStep6($request),
            7 => $this->storeStep7($request),
            8 => $this->storeStep8($request),
            default => back()->withErrors(['step' => 'Langkah tidak valid.']),
        };
    }

    // ── STEP HANDLERS ─────────────────────────────────────────────────────

    /** Step 1: Buat akun + profil draft — role langsung student */
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

            // Langsung assign role student
            $user->assignRole(Role::STUDENT);

            $profile = KandidatProfile::create([
                'user_id'          => $user->id,
                'nama_lengkap'     => $data['nama_lengkap'],
                'didaftarkan_oleh' => '',
                'jurusan'          => '',
                'sistem_pendidikan'=> '',
                'current_step'     => 2,
                'status'           => 'draft',
            ]);

            $request->session()->put('daftar_murid_profile_id', $profile->id);
        });

        return redirect()->route('admin.murid.create', ['step' => 2])
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

        return redirect()->route('admin.murid.create', ['step' => 3]);
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

        return redirect()->route('admin.murid.create', ['step' => 4]);
    }

    /** Step 4: Data Diri */
    private function storeStep4(Request $request)
    {
        $profile = $this->getSessionProfile($request);

        $data = $request->validate([
            'nik'                   => ['required', 'digits:16'],
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
            'nik.required'   => 'NIK wajib diisi.',
            'nik.digits'     => 'NIK harus tepat 16 digit angka.',
            'no_hp.required' => 'No HP wajib diisi.',
            'no_hp.regex'    => 'No HP harus diawali 08 dan minimal 10 angka.',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('kandidat/foto', 'public');
        }

        if (isset($data['email_kontak'])) {
            $data['email'] = $data['email_kontak'];
            unset($data['email_kontak']);
        }

        $data['penerima_kip'] = $request->boolean('penerima_kip');
        $data['current_step'] = 5;

        $profile->update($data);

        return redirect()->route('admin.murid.create', ['step' => 5]);
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

        return redirect()->route('admin.murid.create', ['step' => 6]);
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

        return redirect()->route('admin.murid.create', ['step' => 7]);
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

        return redirect()->route('admin.murid.create', ['step' => 8]);
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

        $request->session()->forget('daftar_murid_profile_id');

        return redirect()->route('admin.murid.index')
                         ->with('success', "Murid {$profile->nama_lengkap} berhasil didaftarkan.");
    }

    // ── Helper ────────────────────────────────────────────────────────────

    private function getSessionProfile(Request $request): KandidatProfile
    {
        $id = $request->session()->get('daftar_murid_profile_id');

        abort_unless($id, 422, 'Sesi pendaftaran tidak ditemukan. Silakan mulai ulang.');

        return KandidatProfile::findOrFail($id);
    }
}
