<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $query = MataPelajaran::query();

        // Filter jurusan
        if ($request->filled('jurusan')) {
            $query->where('jurusan', $request->jurusan);
        }

        // Filter tingkat
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        // Filter status aktif
        if ($request->filled('aktif')) {
            $query->where('aktif', $request->aktif === '1');
        }

        // Search nama / kode
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('kode', 'like', "%{$q}%");
            });
        }

        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 10;
        $mapel = $query->with('guru')->orderBy('jurusan')->orderBy('tingkat')->orderBy('nama')->paginate($perPage)->withQueryString();

        $guruList = User::whereHas('roles', fn($q) => $q->where('name', Role::TEACHER))
                        ->orderBy('name')
                        ->get();

        return view('admin.mapel.index', compact('mapel', 'guruList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode'      => ['required', 'string', 'max:20', 'unique:mata_pelajaran,kode'],
            'nama'      => ['required', 'string', 'max:255'],
            'guru_id'   => ['nullable', 'exists:users,id'],
            'jurusan'   => ['required', 'in:TKJ,TO,Semua'],
            'tingkat'   => ['required', 'in:X,XI,XII,Semua'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'aktif'     => ['nullable', 'boolean'],
        ], [
            'kode.required'  => 'Kode mata pelajaran wajib diisi.',
            'kode.unique'    => 'Kode ini sudah digunakan.',
            'nama.required'  => 'Nama mata pelajaran wajib diisi.',
            'guru_id.exists' => 'Guru tidak valid.',
            'jurusan.required'  => 'Jurusan wajib dipilih.',
            'tingkat.required'  => 'Tingkat wajib dipilih.',
        ]);

        if (! empty($data['guru_id'])) {
            $validGuru = User::where('id', $data['guru_id'])
                             ->whereHas('roles', fn($q) => $q->where('name', Role::TEACHER))
                             ->exists();

            if (! $validGuru) {
                return back()->withErrors(['guru_id' => 'Guru harus berstatus guru.'])->withInput();
            }
        }

        $data['aktif'] = $request->boolean('aktif', true);

        $mapel = MataPelajaran::create($data);

        return redirect()->route('admin.mapel.index', $request->only('jurusan', 'tingkat', 'aktif', 'q'))
                         ->with('success', "Mata pelajaran {$mapel->nama} berhasil ditambahkan.");
    }

    public function update(Request $request, MataPelajaran $mapel)
    {
        $data = $request->validate([
            'kode'      => ['required', 'string', 'max:20', 'unique:mata_pelajaran,kode,' . $mapel->id],
            'nama'      => ['required', 'string', 'max:255'],
            'guru_id'   => ['nullable', 'exists:users,id'],
            'jurusan'   => ['required', 'in:TKJ,TO,Semua'],
            'tingkat'   => ['required', 'in:X,XI,XII,Semua'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'aktif'     => ['nullable', 'boolean'],
        ], [
            'kode.required'  => 'Kode mata pelajaran wajib diisi.',
            'kode.unique'    => 'Kode ini sudah digunakan.',
            'nama.required'  => 'Nama mata pelajaran wajib diisi.',
            'guru_id.exists' => 'Guru tidak valid.',
            'jurusan.required'  => 'Jurusan wajib dipilih.',
            'tingkat.required'  => 'Tingkat wajib dipilih.',
        ]);

        if (! empty($data['guru_id'])) {
            $validGuru = User::where('id', $data['guru_id'])
                             ->whereHas('roles', fn($q) => $q->where('name', Role::TEACHER))
                             ->exists();

            if (! $validGuru) {
                return back()->withErrors(['guru_id' => 'Guru harus berstatus guru.'])->withInput();
            }
        }

        $data['aktif'] = $request->boolean('aktif', true);

        $mapel->update($data);

        return redirect()->route('admin.mapel.index', $request->only('jurusan', 'tingkat', 'aktif', 'q'))
                         ->with('success', "Mata pelajaran {$mapel->nama} berhasil diperbarui.");
    }

    public function destroy(MataPelajaran $mapel)
    {
        $nama = $mapel->nama;
        $mapel->delete();

        return redirect()->route('admin.mapel.index')
                         ->with('success', "Mata pelajaran {$nama} berhasil dihapus.");
    }

    /**
     * Toggle status aktif/nonaktif.
     */
    public function toggleAktif(MataPelajaran $mapel)
    {
        $mapel->update(['aktif' => ! $mapel->aktif]);

        $status = $mapel->aktif ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Mata pelajaran {$mapel->nama} berhasil {$status}.");
    }
}
