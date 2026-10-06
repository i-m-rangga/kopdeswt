<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Petugas\PenerimaanBarang\StorePenerimaanBarangRequest;
use App\Models\Barang;
use App\Models\DetailPenerimaan;
use App\Models\MutasiPersediaan;
use App\Models\Pemasok;
use App\Models\PenerimaanBarang;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class PenerimaanBarangController extends Controller
{
    public function index(): View
    {
        $search = request('search');
        $pemasokId = request('pemasok_id');
        $tanggalAwal = request('tanggal_awal');
        $tanggalAkhir = request('tanggal_akhir');

        $penerimaans = PenerimaanBarang::query()
            ->with(['pemasok', 'petugas', 'detailPenerimaan.barang'])
            ->when($search, fn($q) => $q->whereHas('pemasok', fn($q2) => $q2->where('nama_pemasok', 'like', "%{$search}%")))
            ->when($pemasokId, fn($q) => $q->where('id_pemasok', $pemasokId))
            ->when($tanggalAwal, fn($q) => $q->whereDate('tanggal', '>=', $tanggalAwal))
            ->when($tanggalAkhir, fn($q) => $q->whereDate('tanggal', '<=', $tanggalAkhir))
            ->latest('tanggal')
            ->latest('id_penerimaan')
            ->paginate(10)
            ->withQueryString();

        $pemasoks = Pemasok::orderBy('nama_pemasok')->get();

        return view('petugas.penerimaan-barang.index', compact('penerimaans', 'pemasoks', 'search', 'pemasokId', 'tanggalAwal', 'tanggalAkhir'));
    }

    public function create(): View
    {
        $pemasoks = Pemasok::orderBy('nama_pemasok')->get();
        $barangs = Barang::with('kategori')->orderBy('nama_barang')->get();

        return view('petugas.penerimaan-barang.create', compact('pemasoks', 'barangs'));
    }

    public function store(StorePenerimaanBarangRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $items = $validated['items'];

        try {
            DB::transaction(function () use ($validated, $items) {
                // 1. Create header penerimaan_barang
                $penerimaan = PenerimaanBarang::create([
                    'tanggal' => $validated['tanggal'],
                    'id_pemasok' => $validated['id_pemasok'],
                    'id_petugas' => auth()->id(),
                ]);

                // 2. Loop items - create detail_penerimaan, update barang.stok, create mutasi_persediaan
                foreach ($items as $item) {
                    $barang = Barang::findOrFail($item['id_barang']);
                    $jumlah = (int) $item['jumlah'];
                    $hargaBeli = (float) $item['harga_beli'];

                    // Create detail_penerimaan
                    DetailPenerimaan::create([
                        'id_penerimaan' => $penerimaan->id_penerimaan,
                        'id_barang' => $barang->id_barang,
                        'jumlah' => $jumlah,
                        'harga_beli' => $hargaBeli,
                    ]);

                    // Update barang.stok (tambah stok)
                    $stokBaru = $barang->stok + $jumlah;
                    $barang->update(['stok' => $stokBaru]);

                    // Create mutasi_persediaan (jenis_mutasi = 'masuk')
                    MutasiPersediaan::catatBarangMasuk(
                        idBarang: $barang->id_barang,
                        jumlah: $jumlah,
                        stokAkhir: $stokBaru,
                        referensi: (string) $penerimaan->id_penerimaan,
                        tanggal: $validated['tanggal']
                    );
                }
            });

            return redirect()
                ->route('petugas.penerimaan-barang.index')
                ->with('success', 'Penerimaan barang berhasil dicatat. Stok telah diperbarui.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal mencatat penerimaan: ' . $e->getMessage()]);
        }
    }

    public function show(PenerimaanBarang $penerimaanBarang): View
    {
        $penerimaanBarang->load(['pemasok', 'petugas', 'detailPenerimaan.barang.kategori']);
        return view('petugas.penerimaan-barang.show', compact('penerimaanBarang'));
    }
}