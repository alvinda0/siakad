<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KegiatanSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KegiatanSekolahController extends Controller
{
    public function index(Request $request)
    {
        $query = KegiatanSekolah::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('aktif')) {
            $query->where('aktif', $request->aktif === '1');
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%")
                    ->orWhere('tanggal_kegiatan', 'like', "%{$q}%");
            });
        }

        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 10;

        $kegiatan = $query
            ->orderBy('urutan')
            ->orderBy('judul')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.kegiatan-sekolah.index', compact('kegiatan'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'            => ['required', 'string', 'max:255'],
            'kategori'         => ['required', 'in:' . implode(',', array_keys(KegiatanSekolah::$kategori))],
            'tanggal_kegiatan' => ['required', 'string', 'max:100'],
            'deskripsi'        => ['nullable', 'string', 'max:2000'],
            'urutan'           => ['nullable', 'integer', 'min:0'],
            'aktif'            => ['nullable', 'boolean'],
            'gambar'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'judul.required'            => 'Judul kegiatan wajib diisi.',
            'kategori.required'         => 'Kategori wajib dipilih.',
            'kategori.in'               => 'Kategori tidak valid.',
            'tanggal_kegiatan.required' => 'Tanggal kegiatan wajib diisi.',
            'gambar.image'              => 'File harus berupa gambar.',
            'gambar.mimes'              => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'gambar.max'                => 'Ukuran gambar maksimal 2MB.',
        ]);

        $data['aktif']  = $request->boolean('aktif', true);
        $data['urutan'] = (int) ($data['urutan'] ?? 0);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('kegiatan', 'public');
        } else {
            unset($data['gambar']);
        }

        $item = KegiatanSekolah::create($data);

        $filters = array_filter([
            'kategori' => $request->query('kategori'),
            'aktif'    => $request->query('aktif'),
            'q'        => $request->query('q'),
        ], fn($v) => $v !== null && $v !== '');

        return redirect()
            ->route('admin.kegiatan-sekolah.index', $filters)
            ->with('success', "Kegiatan \"{$item->judul}\" berhasil ditambahkan.");
    }

    public function update(Request $request, KegiatanSekolah $kegiatanSekolah)
    {
        $data = $request->validate([
            'judul'            => ['required', 'string', 'max:255'],
            'kategori'         => ['required', 'in:' . implode(',', array_keys(KegiatanSekolah::$kategori))],
            'tanggal_kegiatan' => ['required', 'string', 'max:100'],
            'deskripsi'        => ['nullable', 'string', 'max:2000'],
            'urutan'           => ['nullable', 'integer', 'min:0'],
            'aktif'            => ['nullable', 'boolean'],
            'gambar'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'hapus_gambar'     => ['nullable', 'boolean'],
        ], [
            'judul.required'            => 'Judul kegiatan wajib diisi.',
            'kategori.required'         => 'Kategori wajib dipilih.',
            'kategori.in'               => 'Kategori tidak valid.',
            'tanggal_kegiatan.required' => 'Tanggal kegiatan wajib diisi.',
            'gambar.image'              => 'File harus berupa gambar.',
            'gambar.mimes'              => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'gambar.max'                => 'Ukuran gambar maksimal 2MB.',
        ]);

        $data['aktif']  = $request->boolean('aktif', true);
        $data['urutan'] = (int) ($data['urutan'] ?? 0);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama
            if ($kegiatanSekolah->gambar) {
                Storage::disk('public')->delete($kegiatanSekolah->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('kegiatan', 'public');
        } elseif ($request->boolean('hapus_gambar')) {
            if ($kegiatanSekolah->gambar) {
                Storage::disk('public')->delete($kegiatanSekolah->gambar);
            }
            $data['gambar'] = null;
        } else {
            unset($data['gambar']);
        }

        unset($data['hapus_gambar']);

        $kegiatanSekolah->update($data);

        $filters = array_filter([
            'kategori' => $request->query('kategori'),
            'aktif'    => $request->query('aktif'),
            'q'        => $request->query('q'),
        ], fn($v) => $v !== null && $v !== '');

        return redirect()
            ->route('admin.kegiatan-sekolah.index', $filters)
            ->with('success', "Kegiatan \"{$kegiatanSekolah->judul}\" berhasil diperbarui.");
    }

    public function destroy(KegiatanSekolah $kegiatanSekolah)
    {
        if ($kegiatanSekolah->gambar) {
            Storage::disk('public')->delete($kegiatanSekolah->gambar);
        }

        $judul = $kegiatanSekolah->judul;
        $kegiatanSekolah->delete();

        return redirect()
            ->route('admin.kegiatan-sekolah.index')
            ->with('success', "Kegiatan \"{$judul}\" berhasil dihapus.");
    }

    public function toggleAktif(KegiatanSekolah $kegiatanSekolah)
    {
        $kegiatanSekolah->update(['aktif' => ! $kegiatanSekolah->aktif]);

        $status = $kegiatanSekolah->aktif ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Kegiatan \"{$kegiatanSekolah->judul}\" berhasil {$status}.");
    }
}
