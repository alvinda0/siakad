<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $kelasList = Kelas::orderBy('tingkat')->orderBy('jurusan')->orderBy('nama')->get();
        $mapelList = MataPelajaran::where('aktif', true)->orderBy('nama')->get();
        $guruList  = User::whereHas('roles', fn($q) => $q->where('name', Role::TEACHER))
                         ->orderBy('name')->get();

        $query = Jadwal::with(['kelas', 'mataPelajaran', 'guru']);

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }
        if ($request->filled('aktif')) {
            $query->where('aktif', $request->aktif === '1');
        }

        // Order by hari (custom) then jam_mulai
        $jadwal = $query->get()->sortBy(function ($j) {
            return [Jadwal::$hariOrder[$j->hari] ?? 99, $j->jam_mulai];
        })->values();

        // Group by hari for timetable display
        $byHari = $jadwal->groupBy('hari');

        $selectedKelas = $request->filled('kelas_id')
            ? Kelas::find($request->kelas_id)
            : null;

        return view('admin.jadwal.index', compact(
            'jadwal', 'byHari', 'kelasList', 'mapelList', 'guruList', 'selectedKelas'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kelas_id'          => ['required', 'exists:kelas,id'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajaran,id'],
            'guru_id'           => ['nullable', 'exists:users,id'],
            'hari'              => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
            'jam_mulai'         => ['required', 'date_format:H:i'],
            'jam_selesai'       => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'ruangan'           => ['nullable', 'string', 'max:50'],
            'aktif'             => ['nullable', 'boolean'],
        ], [
            'kelas_id.required'          => 'Kelas wajib dipilih.',
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'hari.required'              => 'Hari wajib dipilih.',
            'jam_mulai.required'         => 'Jam mulai wajib diisi.',
            'jam_selesai.required'       => 'Jam selesai wajib diisi.',
            'jam_selesai.after'          => 'Jam selesai harus setelah jam mulai.',
        ]);

        $data['aktif'] = $request->boolean('aktif', true);

        $jadwal = Jadwal::create($data);

        return redirect()
            ->route('admin.jadwal.index', $request->only('kelas_id', 'hari'))
            ->with('success', "Jadwal {$jadwal->mataPelajaran->nama} ({$jadwal->hari}) berhasil ditambahkan.");
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $data = $request->validate([
            'kelas_id'          => ['required', 'exists:kelas,id'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajaran,id'],
            'guru_id'           => ['nullable', 'exists:users,id'],
            'hari'              => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
            'jam_mulai'         => ['required', 'date_format:H:i'],
            'jam_selesai'       => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'ruangan'           => ['nullable', 'string', 'max:50'],
            'aktif'             => ['nullable', 'boolean'],
        ], [
            'kelas_id.required'          => 'Kelas wajib dipilih.',
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'hari.required'              => 'Hari wajib dipilih.',
            'jam_mulai.required'         => 'Jam mulai wajib diisi.',
            'jam_selesai.required'       => 'Jam selesai wajib diisi.',
            'jam_selesai.after'          => 'Jam selesai harus setelah jam mulai.',
        ]);

        $data['aktif'] = $request->boolean('aktif', true);
        $jadwal->update($data);

        return redirect()
            ->route('admin.jadwal.index', $request->only('kelas_id', 'hari'))
            ->with('success', "Jadwal berhasil diperbarui.");
    }

    public function destroy(Jadwal $jadwal)
    {
        $info = "{$jadwal->mataPelajaran->nama} – {$jadwal->hari}";
        $jadwal->delete();

        return redirect()->route('admin.jadwal.index')
                         ->with('success', "Jadwal {$info} berhasil dihapus.");
    }
}
