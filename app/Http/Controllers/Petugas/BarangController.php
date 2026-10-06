<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Barang\StoreBarangRequest;
use App\Http\Requests\Barang\UpdateBarangRequest;
use App\Models\Barang;
use App\Models\KategoriBarang;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BarangController extends Controller
{
    public function index(): View
    {
        $search = request('search');
        $kategoriId = request('kategori_id');
        $stokRendah = request('stok_rendah');

        $barangs = Barang::query()
            ->with('kategori')
            ->when($search, fn($q) => $q->where('nama_barang', 'like', "%{$search}%"))
            ->when($kategoriId, fn($q) => $q->where('id_kategori', $kategoriId))
            ->when($stokRendah, fn($q) => $q->whereRaw('stok <= stok_minimum'))
            ->orderBy('nama_barang')
            ->paginate(10)
            ->withQueryString();

        $kategoris = KategoriBarang::orderBy('nama_kategori')->get();

        return view('petugas.barang.index', compact('barangs', 'search', 'kategoriId', 'kategoris', 'stokRendah'));
    }

    public function create(): View
    {
        $kategoris = KategoriBarang::orderBy('nama_kategori')->get();
        return view('petugas.barang.create', compact('kategoris'));
    }

    public function store(StoreBarangRequest $request): RedirectResponse
    {
        Barang::create($request->validated());
        return redirect()->route('petugas.barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(Barang $barang): View
    {
        $barang->load('kategori');
        return view('petugas.barang.show', compact('barang'));
    }

    public function edit(Barang $barang): View
    {
        $kategoris = KategoriBarang::orderBy('nama_kategori')->get();
        return view('petugas.barang.edit', compact('barang', 'kategoris'));
    }

    public function update(UpdateBarangRequest $request, Barang $barang): RedirectResponse
    {
        $barang->update($request->validated());
        return redirect()->route('petugas.barang.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang): RedirectResponse
    {
        // Cek apakah barang sudah digunakan di transaksi atau penerimaan
        if ($barang->detailTransaksi()->exists() || $barang->detailPenerimaan()->exists()) {
            return back()->withErrors(['barang' => 'Tidak dapat menghapus barang yang sudah memiliki transaksi atau penerimaan.']);
        }
        $barang->delete();
        return redirect()->route('petugas.barang.index')->with('success', 'Barang berhasil dihapus.');
    }
}