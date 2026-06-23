<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $tahun = (int) $request->query('tahun', date('Y'));

        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 20;

        $kelas = Kelas::when($tahun, fn($q) => $q->where('tahun_ajaran', $tahun))
                      ->with('waliKelas')
                      ->orderByRaw("CASE tingkat WHEN 'X' THEN 1 WHEN 'XI' THEN 2 WHEN 'XII' THEN 3 ELSE 4 END")
                      ->orderBy('jurusan')
                      ->orderBy('nama')
                      ->paginate($perPage)
                      ->withQueryString();

        $tahunList = Kelas::selectRaw('DISTINCT tahun_ajaran')
                          ->orderBy('tahun_ajaran', 'desc')
                          ->pluck('tahun_ajaran');

        $guruList = User::whereHas('roles', fn($q) => $q->where('name', Role::TEACHER))
                        ->orderBy('name')
                        ->get(['id', 'name']);

        return view('admin.kelas.index', compact('kelas', 'tahun', 'tahunList', 'guruList'));
    }

    public function create()
    {
        // Redirect to index — create is handled via modal
        return redirect()->route('admin.kelas.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'          => ['required', 'string', 'max:100'],
            'tingkat'       => ['required', 'in:X,XI,XII'],
            'jurusan'       => ['required', 'in:TKJ,TO'],
            'tahun_ajaran'  => ['required', 'integer', 'min:2000', 'max:2100'],
            'kapasitas'     => ['required', 'integer', 'min:1', 'max:100'],
            'wali_kelas_id' => ['nullable', 'exists:users,id'],
            'keterangan'    => ['nullable', 'string', 'max:500'],
        ], [
            'nama.required'         => 'Nama kelas wajib diisi.',
            'tingkat.required'      => 'Tingkat kelas wajib dipilih.',
            'tingkat.in'            => 'Tingkat tidak valid.',
            'jurusan.required'      => 'Jurusan wajib dipilih.',
            'jurusan.in'            => 'Jurusan tidak valid.',
            'tahun_ajaran.required' => 'Tahun ajaran wajib diisi.',
            'kapasitas.required'    => 'Kapasitas wajib diisi.',
        ]);

        // Sinkronkan kolom wali_kelas (nama) dari relasi
        if (!empty($data['wali_kelas_id'])) {
            $data['wali_kelas'] = User::find($data['wali_kelas_id'])?->name;
        } else {
            $data['wali_kelas'] = null;
        }

        // cek duplikat nama + tahun_ajaran
        $exists = Kelas::where('nama', $data['nama'])
                       ->where('tahun_ajaran', $data['tahun_ajaran'])
                       ->exists();

        if ($exists) {
            return back()->withInput()
                         ->withErrors(['nama' => 'Kelas dengan nama dan tahun ajaran ini sudah ada.']);
        }

        $kelas = Kelas::create($data);

        return redirect()->route('admin.kelas.index')
                         ->with('success', "Kelas {$kelas->nama} berhasil ditambahkan.");
    }

    public function show(Request $request, Kelas $kelas)
    {
        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 20;

        $siswa = $kelas->siswa()
                       ->with('user')
                       ->orderBy('nama_lengkap')
                       ->paginate($perPage)
                       ->withQueryString();

        return view('admin.kelas.show', compact('kelas', 'siswa'));
    }

    public function edit(Kelas $kelas)
    {
        // Edit is handled via modal on the index page
        return redirect()->route('admin.kelas.index');
    }

    public function update(Request $request, Kelas $kelas)
    {
        $data = $request->validate([
            'nama'          => ['required', 'string', 'max:100'],
            'tingkat'       => ['required', 'in:X,XI,XII'],
            'jurusan'       => ['required', 'in:TKJ,TO'],
            'tahun_ajaran'  => ['required', 'integer', 'min:2000', 'max:2100'],
            'kapasitas'     => ['required', 'integer', 'min:1', 'max:100'],
            'wali_kelas_id' => ['nullable', 'exists:users,id'],
            'keterangan'    => ['nullable', 'string', 'max:500'],
        ], [
            'nama.required'         => 'Nama kelas wajib diisi.',
            'tingkat.required'      => 'Tingkat kelas wajib dipilih.',
            'jurusan.required'      => 'Jurusan wajib dipilih.',
            'tahun_ajaran.required' => 'Tahun ajaran wajib diisi.',
            'kapasitas.required'    => 'Kapasitas wajib diisi.',
        ]);

        // Sinkronkan kolom wali_kelas (nama) dari relasi
        if (!empty($data['wali_kelas_id'])) {
            $data['wali_kelas'] = User::find($data['wali_kelas_id'])?->name;
        } else {
            $data['wali_kelas'] = null;
        }

        // cek duplikat, kecuali dirinya sendiri
        $exists = Kelas::where('nama', $data['nama'])
                       ->where('tahun_ajaran', $data['tahun_ajaran'])
                       ->where('id', '!=', $kelas->id)
                       ->exists();

        if ($exists) {
            return back()->withInput()
                         ->withErrors(['nama' => 'Kelas dengan nama dan tahun ajaran ini sudah ada.'])
                         ->with('_edit_id', $kelas->id);
        }

        $kelas->update($data);

        return redirect()->route('admin.kelas.index')
                         ->with('success', "Kelas {$kelas->nama} berhasil diperbarui.");
    }

    public function destroy(Kelas $kelas)
    {
        $nama = $kelas->nama;
        $kelas->delete();

        return redirect()->route('admin.kelas.index')
                         ->with('success', "Kelas {$nama} berhasil dihapus.");
    }
}
