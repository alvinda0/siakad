<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticker;
use Illuminate\Http\Request;

class TickerController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticker::query();

        if ($request->filled('aktif')) {
            $query->where('aktif', $request->aktif === '1');
        }

        if ($request->filled('q')) {
            $query->where('konten', 'like', '%' . $request->q . '%');
        }

        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 10;

        $tickers = $query
            ->orderBy('urutan')
            ->orderBy('created_at')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.ticker.index', compact('tickers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'konten' => ['required', 'string', 'max:500'],
            'icon'   => ['nullable', 'string', 'max:10'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'aktif'  => ['nullable', 'boolean'],
        ], [
            'konten.required' => 'Teks ticker wajib diisi.',
            'konten.max'      => 'Teks ticker maksimal 500 karakter.',
        ]);

        $data['aktif']  = $request->boolean('aktif', true);
        $data['urutan'] = (int) ($data['urutan'] ?? 0);
        $data['icon']   = $data['icon'] ?: '📢';

        $item = Ticker::create($data);

        return redirect()
            ->route('admin.ticker.index', $request->only('aktif', 'q'))
            ->with('success', "Ticker berhasil ditambahkan.");
    }

    public function update(Request $request, Ticker $ticker)
    {
        $data = $request->validate([
            'konten' => ['required', 'string', 'max:500'],
            'icon'   => ['nullable', 'string', 'max:10'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'aktif'  => ['nullable', 'boolean'],
        ], [
            'konten.required' => 'Teks ticker wajib diisi.',
            'konten.max'      => 'Teks ticker maksimal 500 karakter.',
        ]);

        $data['aktif']  = $request->boolean('aktif', true);
        $data['urutan'] = (int) ($data['urutan'] ?? 0);
        $data['icon']   = $data['icon'] ?: '📢';

        $ticker->update($data);

        return redirect()
            ->route('admin.ticker.index', $request->only('aktif', 'q'))
            ->with('success', "Ticker berhasil diperbarui.");
    }

    public function destroy(Ticker $ticker)
    {
        $ticker->delete();

        return redirect()
            ->route('admin.ticker.index')
            ->with('success', "Ticker berhasil dihapus.");
    }

    public function toggleAktif(Ticker $ticker)
    {
        $ticker->update(['aktif' => ! $ticker->aktif]);

        $status = $ticker->aktif ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Ticker berhasil {$status}.");
    }
}
