<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Http\Requests\KategoriBarang\StoreKategoriBarangRequest;
use App\Http\Requests\KategoriBarang\UpdateKategoriBarangRequest;
use App\Models\KategoriBarang;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class KategoriBarangController extends Controller
{
    public function index(): View
    {
        $search = request('search');
        $kategoris = KategoriBarang::query()
            ->when($search, fn($q) => $q->where('nama_kategori', 'like', "%{$search}%"))
            ->orderBy('nama_kategori')
            ->paginate(10)
            ->withQueryString();

        return view('petugas.kategori-barang.index', compact('kategoris', 'search'));
    }

    public function create(): View
    {
        return view('petugas.kategori-barang.create');
    }

    public function store(StoreKategoriBarangRequest $request): RedirectResponse
    {
        KategoriBarang::create($request->validated());
        return redirect()->route('petugas.kategori-barang.index')->with('success', 'Kategori barang berhasil ditambahkan.');
    }

    public function show(KategoriBarang $kategoriBarang): View
    {
        $kategoriBarang->loadCount('barang');
        return view('petugas.kategori-barang.show', compact('kategoriBarang'));
    }

    public function edit(KategoriBarang $kategoriBarang): View
    {
        return view('petugas.kategori-barang.edit', compact('kategoriBarang'));
    }

    public function update(UpdateKategoriBarangRequest $request, KategoriBarang $kategoriBarang): RedirectResponse
    {
        $kategoriBarang->update($request->validated());
        return redirect()->route('petugas.kategori-barang.index')->with('success', 'Kategori barang berhasil diperbarui.');
    }

    public function destroy(KategoriBarang $kategoriBarang): RedirectResponse
    {
        if ($kategoriBarang->barang()->exists()) {
            return back()->withErrors(['kategori' => 'Tidak dapat menghapus kategori yang masih memiliki barang.']);
        }
        $kategoriBarang->delete();
        return redirect()->route('petugas.kategori-barang.index')->with('success', 'Kategori barang berhasil dihapus.');
    }
}