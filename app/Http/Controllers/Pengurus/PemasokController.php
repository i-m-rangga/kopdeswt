<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pemasok\StorePemasokRequest;
use App\Http\Requests\Pemasok\UpdatePemasokRequest;
use App\Models\Pemasok;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PemasokController extends Controller
{
    public function index(): View
    {
        $search = request('search');

        $pemasoks = Pemasok::query()
            ->when($search, fn($q) => $q->where('nama_pemasok', 'like', "%{$search}%")
                ->orWhere('kontak', 'like', "%{$search}%"))
            ->orderBy('nama_pemasok')
            ->paginate(10)
            ->withQueryString();

        return view('pengurus.pemasok.index', compact('pemasoks', 'search'));
    }

    public function create(): View
    {
        return view('pengurus.pemasok.create');
    }

    public function store(StorePemasokRequest $request): RedirectResponse
    {
        Pemasok::create($request->validated());
        return redirect()->route('pengurus.pemasok.index')->with('success', 'Pemasok berhasil ditambahkan.');
    }

    public function show(Pemasok $pemasok): View
    {
        $pemasok->loadCount('penerimaanBarang');
        return view('pengurus.pemasok.show', compact('pemasok'));
    }

    public function edit(Pemasok $pemasok): View
    {
        return view('pengurus.pemasok.edit', compact('pemasok'));
    }

    public function update(UpdatePemasokRequest $request, Pemasok $pemasok): RedirectResponse
    {
        $pemasok->update($request->validated());
        return redirect()->route('pengurus.pemasok.index')->with('success', 'Pemasok berhasil diperbarui.');
    }

    public function destroy(Pemasok $pemasok): RedirectResponse
    {
        if ($pemasok->penerimaanBarang()->exists()) {
            return back()->withErrors(['pemasok' => 'Tidak dapat menghapus pemasok yang sudah memiliki riwayat penerimaan barang.']);
        }
        $pemasok->delete();
        return redirect()->route('pengurus.pemasok.index')->with('success', 'Pemasok berhasil dihapus.');
    }
}