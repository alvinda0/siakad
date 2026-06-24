<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FasilitasController extends Controller
{
    public function index(Request $request)
    {
        $query = Fasilitas::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('aktif')) {
            $query->where('aktif', $request->aktif === '1');
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%");
            });
        }

        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 10;

        $fasilitas = $query
            ->orderBy('urutan')
            ->orderBy('kategori')
            ->orderBy('nama')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.fasilitas.index', compact('fasilitas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'      => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'fitur'     => ['nullable', 'string'],           // textarea, baris per fitur
            'gambar'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'kategori'  => ['required', 'string', 'in:Akademik,Olahraga,Kesehatan,Keagamaan,Umum'],
            'urutan'    => ['nullable', 'integer', 'min:0'],
            'aktif'     => ['nullable', 'boolean'],
        ], [
            'nama.required'     => 'Nama fasilitas wajib diisi.',
            'kategori.required' => 'Kategori wajib dipilih.',
            'kategori.in'       => 'Kategori tidak valid.',
            'gambar.image'      => 'File harus berupa gambar.',
            'gambar.mimes'      => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'gambar.max'        => 'Ukuran gambar maksimal 2MB.',
        ]);

        $data['aktif']  = $request->boolean('aktif', true);
        $data['urutan'] = (int) ($data['urutan'] ?? 0);
        $data['fitur']  = $this->parseFitur($request->input('fitur', ''));

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('fasilitas', 'public');
        } else {
            unset($data['gambar']);
        }

        $item = Fasilitas::create($data);

        return redirect()
            ->route('admin.fasilitas.index', $this->filters($request))
            ->with('success', "Fasilitas \"{$item->nama}\" berhasil ditambahkan.");
    }

    public function update(Request $request, Fasilitas $fasilitas)
    {
        $data = $request->validate([
            'nama'         => ['required', 'string', 'max:255'],
            'deskripsi'    => ['nullable', 'string', 'max:2000'],
            'fitur'        => ['nullable', 'string'],
            'gambar'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'hapus_gambar' => ['nullable', 'boolean'],
            'kategori'     => ['required', 'string', 'in:Akademik,Olahraga,Kesehatan,Keagamaan,Umum'],
            'urutan'       => ['nullable', 'integer', 'min:0'],
            'aktif'        => ['nullable', 'boolean'],
        ], [
            'nama.required'     => 'Nama fasilitas wajib diisi.',
            'kategori.required' => 'Kategori wajib dipilih.',
            'kategori.in'       => 'Kategori tidak valid.',
            'gambar.image'      => 'File harus berupa gambar.',
            'gambar.mimes'      => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'gambar.max'        => 'Ukuran gambar maksimal 2MB.',
        ]);

        $data['aktif']  = $request->boolean('aktif', true);
        $data['urutan'] = (int) ($data['urutan'] ?? 0);
        $data['fitur']  = $this->parseFitur($request->input('fitur', ''));

        if ($request->hasFile('gambar')) {
            if ($fasilitas->gambar) {
                Storage::disk('public')->delete($fasilitas->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('fasilitas', 'public');
        } elseif ($request->boolean('hapus_gambar')) {
            if ($fasilitas->gambar) {
                Storage::disk('public')->delete($fasilitas->gambar);
            }
            $data['gambar'] = null;
        } else {
            unset($data['gambar']);
        }

        unset($data['hapus_gambar']);

        $fasilitas->update($data);

        return redirect()
            ->route('admin.fasilitas.index', $this->filters($request))
            ->with('success', "Fasilitas \"{$fasilitas->nama}\" berhasil diperbarui.");
    }

    public function destroy(Fasilitas $fasilitas)
    {
        if ($fasilitas->gambar) {
            Storage::disk('public')->delete($fasilitas->gambar);
        }

        $nama = $fasilitas->nama;
        $fasilitas->delete();

        return redirect()
            ->route('admin.fasilitas.index')
            ->with('success', "Fasilitas \"{$nama}\" berhasil dihapus.");
    }

    public function toggleAktif(Fasilitas $fasilitas)
    {
        $fasilitas->update(['aktif' => ! $fasilitas->aktif]);

        $status = $fasilitas->aktif ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Fasilitas \"{$fasilitas->nama}\" berhasil {$status}.");
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Ubah teks multi-baris menjadi array fitur yang bersih.
     */
    private function parseFitur(string $raw): array
    {
        return array_values(
            array_filter(
                array_map('trim', explode("\n", $raw)),
                fn($line) => $line !== ''
            )
        );
    }

    private function filters(Request $request): array
    {
        return array_filter([
            'kategori' => $request->query('kategori'),
            'aktif'    => $request->query('aktif'),
            'q'        => $request->query('q'),
        ], fn($v) => $v !== null && $v !== '');
    }
}
