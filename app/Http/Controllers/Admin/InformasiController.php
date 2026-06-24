<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Informasi;
use Illuminate\Http\Request;

class InformasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Informasi::query();

        // Filter tipe
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        // Filter status aktif
        if ($request->filled('aktif')) {
            $query->where('aktif', $request->aktif === '1');
        }

        // Search jenis
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('jenis', 'like', "%{$q}%")
                    ->orWhere('syarat', 'like', "%{$q}%")
                    ->orWhere('benefit', 'like', "%{$q}%");
            });
        }

        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 10;

        $informasi = $query
            ->orderBy('tipe')
            ->orderBy('urutan')
            ->orderBy('jenis')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.informasi.index', compact('informasi'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipe'    => ['required', 'in:beasiswa,promo'],
            'jenis'   => ['required', 'string', 'max:255'],
            'syarat'  => ['nullable', 'string', 'max:2000'],
            'benefit' => ['nullable', 'string', 'max:2000'],
            'urutan'  => ['nullable', 'integer', 'min:0'],
            'aktif'   => ['nullable', 'boolean'],
        ], [
            'tipe.required'  => 'Tipe informasi wajib dipilih.',
            'tipe.in'        => 'Tipe tidak valid.',
            'jenis.required' => 'Jenis wajib diisi.',
        ]);

        $data['aktif']  = $request->boolean('aktif', true);
        $data['urutan'] = (int) ($data['urutan'] ?? 0);

        $item = Informasi::create($data);

        return redirect()
            ->route('admin.informasi.index', $request->only('tipe', 'aktif', 'q'))
            ->with('success', "Informasi \"{$item->jenis}\" berhasil ditambahkan.");
    }

    public function update(Request $request, Informasi $informasi)
    {
        $data = $request->validate([
            'tipe'    => ['required', 'in:beasiswa,promo'],
            'jenis'   => ['required', 'string', 'max:255'],
            'syarat'  => ['nullable', 'string', 'max:2000'],
            'benefit' => ['nullable', 'string', 'max:2000'],
            'urutan'  => ['nullable', 'integer', 'min:0'],
            'aktif'   => ['nullable', 'boolean'],
        ], [
            'tipe.required'  => 'Tipe informasi wajib dipilih.',
            'tipe.in'        => 'Tipe tidak valid.',
            'jenis.required' => 'Jenis wajib diisi.',
        ]);

        $data['aktif']  = $request->boolean('aktif', true);
        $data['urutan'] = (int) ($data['urutan'] ?? 0);

        $informasi->update($data);

        return redirect()
            ->route('admin.informasi.index', $request->only('tipe', 'aktif', 'q'))
            ->with('success', "Informasi \"{$informasi->jenis}\" berhasil diperbarui.");
    }

    public function destroy(Informasi $informasi)
    {
        $jenis = $informasi->jenis;
        $informasi->delete();

        return redirect()
            ->route('admin.informasi.index')
            ->with('success', "Informasi \"{$jenis}\" berhasil dihapus.");
    }

    public function toggleAktif(Informasi $informasi)
    {
        $informasi->update(['aktif' => ! $informasi->aktif]);

        $status = $informasi->aktif ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Informasi \"{$informasi->jenis}\" berhasil {$status}.");
    }
}
