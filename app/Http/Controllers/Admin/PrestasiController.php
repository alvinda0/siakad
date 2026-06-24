<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prestasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrestasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Prestasi::query();

        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        if ($request->filled('aktif')) {
            $query->where('aktif', $request->aktif === '1');
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('nama_peraih', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%");
            });
        }

        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 10;

        $prestasi = $query
            ->orderBy('urutan')
            ->orderByDesc('tahun')
            ->orderBy('judul')
            ->paginate($perPage)
            ->withQueryString();

        // Daftar tahun yang ada di DB untuk filter
        $tahunList = Prestasi::selectRaw('DISTINCT tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('admin.prestasi.index', compact('prestasi', 'tahunList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'       => ['required', 'string', 'max:255'],
            'nama_peraih' => ['required', 'string', 'max:255'],
            'tingkat'     => ['required', 'in:' . implode(',', array_keys(Prestasi::$tingkat))],
            'medali'      => ['required', 'string', 'max:20'],
            'tahun'       => ['required', 'string', 'max:20'],
            'deskripsi'   => ['nullable', 'string', 'max:2000'],
            'urutan'      => ['nullable', 'integer', 'min:0'],
            'aktif'       => ['nullable', 'boolean'],
            'gambar'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'judul.required'       => 'Judul prestasi wajib diisi.',
            'nama_peraih.required' => 'Nama peraih wajib diisi.',
            'tingkat.required'     => 'Tingkat wajib dipilih.',
            'tingkat.in'           => 'Tingkat tidak valid.',
            'medali.required'      => 'Medali wajib dipilih.',
            'tahun.required'       => 'Tahun wajib diisi.',
            'gambar.image'         => 'File harus berupa gambar.',
            'gambar.mimes'         => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'gambar.max'           => 'Ukuran gambar maksimal 2MB.',
        ]);

        $data['aktif']  = $request->boolean('aktif', true);
        $data['urutan'] = (int) ($data['urutan'] ?? 0);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('prestasi', 'public');
        } else {
            unset($data['gambar']);
        }

        $item = Prestasi::create($data);

        $filters = $this->activeFilters($request);

        return redirect()
            ->route('admin.prestasi.index', $filters)
            ->with('success', "Prestasi \"{$item->judul}\" berhasil ditambahkan.");
    }

    public function update(Request $request, Prestasi $prestasi)
    {
        $data = $request->validate([
            'judul'        => ['required', 'string', 'max:255'],
            'nama_peraih'  => ['required', 'string', 'max:255'],
            'tingkat'      => ['required', 'in:' . implode(',', array_keys(Prestasi::$tingkat))],
            'medali'       => ['required', 'string', 'max:20'],
            'tahun'        => ['required', 'string', 'max:20'],
            'deskripsi'    => ['nullable', 'string', 'max:2000'],
            'urutan'       => ['nullable', 'integer', 'min:0'],
            'aktif'        => ['nullable', 'boolean'],
            'gambar'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'hapus_gambar' => ['nullable', 'boolean'],
        ], [
            'judul.required'       => 'Judul prestasi wajib diisi.',
            'nama_peraih.required' => 'Nama peraih wajib diisi.',
            'tingkat.required'     => 'Tingkat wajib dipilih.',
            'tingkat.in'           => 'Tingkat tidak valid.',
            'medali.required'      => 'Medali wajib dipilih.',
            'tahun.required'       => 'Tahun wajib diisi.',
            'gambar.image'         => 'File harus berupa gambar.',
            'gambar.mimes'         => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'gambar.max'           => 'Ukuran gambar maksimal 2MB.',
        ]);

        $data['aktif']  = $request->boolean('aktif', true);
        $data['urutan'] = (int) ($data['urutan'] ?? 0);

        if ($request->hasFile('gambar')) {
            if ($prestasi->gambar) {
                Storage::disk('public')->delete($prestasi->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('prestasi', 'public');
        } elseif ($request->boolean('hapus_gambar')) {
            if ($prestasi->gambar) {
                Storage::disk('public')->delete($prestasi->gambar);
            }
            $data['gambar'] = null;
        } else {
            unset($data['gambar']);
        }

        unset($data['hapus_gambar']);

        $prestasi->update($data);

        $filters = $this->activeFilters($request);

        return redirect()
            ->route('admin.prestasi.index', $filters)
            ->with('success', "Prestasi \"{$prestasi->judul}\" berhasil diperbarui.");
    }

    public function destroy(Prestasi $prestasi)
    {
        if ($prestasi->gambar) {
            Storage::disk('public')->delete($prestasi->gambar);
        }

        $judul = $prestasi->judul;
        $prestasi->delete();

        return redirect()
            ->route('admin.prestasi.index')
            ->with('success', "Prestasi \"{$judul}\" berhasil dihapus.");
    }

    public function toggleAktif(Prestasi $prestasi)
    {
        $prestasi->update(['aktif' => ! $prestasi->aktif]);

        $status = $prestasi->aktif ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Prestasi \"{$prestasi->judul}\" berhasil {$status}.");
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function activeFilters(Request $request): array
    {
        return array_filter([
            'tingkat' => $request->query('tingkat'),
            'aktif'   => $request->query('aktif'),
            'tahun'   => $request->query('tahun'),
            'q'       => $request->query('q'),
        ], fn($v) => $v !== null && $v !== '');
    }
}
