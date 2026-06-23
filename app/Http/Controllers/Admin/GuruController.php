<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 15;

        $guru = User::whereHas('roles', fn($q) => $q->where('name', Role::TEACHER))
                    ->with(['roles', 'waliKelas'])
                    ->latest()
                    ->paginate($perPage)
                    ->withQueryString();

        $kelasList = Kelas::with('waliKelas')
                          ->orderByRaw("CASE tingkat WHEN 'X' THEN 1 WHEN 'XI' THEN 2 WHEN 'XII' THEN 3 ELSE 4 END")
                          ->orderBy('jurusan')
                          ->orderBy('nama')
                          ->get();

        return view('admin.guru.index', compact('guru', 'kelasList'));
    }

    public function create()
    {
        return view('admin.guru.create');
    }

    /**
     * Assign atau lepas wali kelas dari seorang guru.
     * - Menghapus assignment lama dari kelas yang sebelumnya dia ampu (jika ada).
     * - Menset wali_kelas_id & wali_kelas (nama) ke kelas yang dipilih.
     */
    public function assignWaliKelas(Request $request, User $user)
    {
        abort_unless(
            $user->roles()->where('name', Role::TEACHER)->exists(),
            404
        );

        $data = $request->validate([
            'kelas_id' => ['nullable', 'exists:kelas,id'],
        ]);

        $kelasId = $data['kelas_id'] ?: null;

        // Lepas kelas lama yang dipegangnya (jika berbeda)
        Kelas::where('wali_kelas_id', $user->id)
             ->where('id', '!=', $kelasId ?? 0)
             ->update(['wali_kelas_id' => null, 'wali_kelas' => null]);

        if ($kelasId) {
            $kelas = Kelas::findOrFail($kelasId);
            $kelas->update([
                'wali_kelas_id' => $user->id,
                'wali_kelas'    => $user->name,
            ]);
        }

        return back()->with('success', "Wali kelas {$user->name} berhasil diperbarui.");
    }
}
