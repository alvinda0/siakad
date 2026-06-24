<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ekstrakurikuler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EkstrakurikulerController extends Controller
{
    public function index(Request $request)
    {
        $query = Ekstrakurikuler::query();

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('aktif')) {
            $query->where('aktif', $request->aktif === '1');
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('pembina', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%");
            });
        }

        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 10;

        $ekskul = $query
            ->orderBy('urutan')
            ->orderBy('jenis')
            ->orderBy('nama')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.ekstrakurikuler.index', compact('ekskul'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'           => ['required', 'string', 'max:255'],
            'jenis'          => ['required', 'in:Wajib,Pilihan'],
            'jumlah_anggota' => ['nullable', 'integer', 'min:0'],
            'pembina'        => ['nullable', 'string', 'max:255'],
            'deskripsi'      => ['nullable', 'string', 'max:2000'],
            'jadwal'         => ['nullable', 'string', 'max:255'],
            'urutan'         => ['nullable', 'integer', 'min:0'],
            'aktif'          => ['nullable', 'boolean'],
            'gambar'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'nama.required'  => 'Nama ekstrakurikuler wajib diisi.',
            'jenis.required' => 'Jenis ekstrakurikuler wajib dipilih.',
            'jenis.in'       => 'Jenis tidak valid.',
            'gambar.image'   => 'File harus berupa gambar.',
            'gambar.mimes'   => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'gambar.max'     => 'Ukuran gambar maksimal 2MB.',
        ]);

        $data['aktif']          = $request->boolean('aktif', true);
        $data['urutan']         = (int) ($data['urutan'] ?? 0);
        $data['jumlah_anggota'] = (int) ($data['jumlah_anggota'] ?? 0);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('ekstrakurikuler', 'public');
        } else {
            unset($data['gambar']);
        }

        $item = Ekstrakurikuler::create($data);

        return redirect()
            ->route('admin.ekstrakurikuler.index', $this->filters($request))
            ->with('success', "Ekstrakurikuler \"{$item->nama}\" berhasil ditambahkan.");
    }

    public function update(Request $request, Ekstrakurikuler $ekstrakurikuler)
    {
        $data = $request->validate([
            'nama'           => ['required', 'string', 'max:255'],
            'jenis'          => ['required', 'in:Wajib,Pilihan'],
            'jumlah_anggota' => ['nullable', 'integer', 'min:0'],
            'pembina'        => ['nullable', 'string', 'max:255'],
            'deskripsi'      => ['nullable', 'string', 'max:2000'],
            'jadwal'         => ['nullable', 'string', 'max:255'],
            'urutan'         => ['nullable', 'integer', 'min:0'],
            'aktif'          => ['nullable', 'boolean'],
            'gambar'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'hapus_gambar'   => ['nullable', 'boolean'],
        ], [
            'nama.required'  => 'Nama ekstrakurikuler wajib diisi.',
            'jenis.required' => 'Jenis ekstrakurikuler wajib dipilih.',
            'jenis.in'       => 'Jenis tidak valid.',
            'gambar.image'   => 'File harus berupa gambar.',
            'gambar.mimes'   => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'gambar.max'     => 'Ukuran gambar maksimal 2MB.',
        ]);

        $data['aktif']          = $request->boolean('aktif', true);
        $data['urutan']         = (int) ($data['urutan'] ?? 0);
        $data['jumlah_anggota'] = (int) ($data['jumlah_anggota'] ?? 0);

        if ($request->hasFile('gambar')) {
            if ($ekstrakurikuler->gambar) {
                Storage::disk('public')->delete($ekstrakurikuler->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('ekstrakurikuler', 'public');
        } elseif ($request->boolean('hapus_gambar')) {
            if ($ekstrakurikuler->gambar) {
                Storage::disk('public')->delete($ekstrakurikuler->gambar);
            }
            $data['gambar'] = null;
        } else {
            unset($data['gambar']);
        }

        unset($data['hapus_gambar']);

        $ekstrakurikuler->update($data);

        return redirect()
            ->route('admin.ekstrakurikuler.index', $this->filters($request))
            ->with('success', "Ekstrakurikuler \"{$ekstrakurikuler->nama}\" berhasil diperbarui.");
    }

    public function destroy(Ekstrakurikuler $ekstrakurikuler)
    {
        if ($ekstrakurikuler->gambar) {
            Storage::disk('public')->delete($ekstrakurikuler->gambar);
        }

        $nama = $ekstrakurikuler->nama;
        $ekstrakurikuler->delete();

        return redirect()
            ->route('admin.ekstrakurikuler.index')
            ->with('success', "Ekstrakurikuler \"{$nama}\" berhasil dihapus.");
    }

    public function toggleAktif(Ekstrakurikuler $ekstrakurikuler)
    {
        $ekstrakurikuler->update(['aktif' => ! $ekstrakurikuler->aktif]);

        $status = $ekstrakurikuler->aktif ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Ekstrakurikuler \"{$ekstrakurikuler->nama}\" berhasil {$status}.");
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function filters(Request $request): array
    {
        return array_filter([
            'jenis' => $request->query('jenis'),
            'aktif' => $request->query('aktif'),
            'q'     => $request->query('q'),
        ], fn($v) => $v !== null && $v !== '');
    }
}
