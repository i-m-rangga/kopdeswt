<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kasir\TransaksiPenjualan\StoreTransaksiPenjualanRequest;
use App\Models\Barang;
use App\Models\DetailTransaksi;
use App\Models\MutasiPersediaan;
use App\Models\Pembayaran;
use App\Models\TransaksiPenjualan;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class TransaksiPenjualanController extends Controller
{
    public function index(): View
    {
        $search = request('search');
        $tanggalAwal = request('tanggal_awal');
        $tanggalAkhir = request('tanggal_akhir');

        $transaksis = TransaksiPenjualan::query()
            ->with(['kasir', 'detailTransaksi.barang', 'pembayaran'])
            ->when($search, fn($q) => $q->whereHas('kasir', fn($q2) => $q2->where('nama', 'like', "%{$search}%")))
            ->when($tanggalAwal, fn($q) => $q->whereDate('tanggal', '>=', $tanggalAwal))
            ->when($tanggalAkhir, fn($q) => $q->whereDate('tanggal', '<=', $tanggalAkhir))
            ->latest('tanggal')
            ->latest('id_transaksi')
            ->paginate(10)
            ->withQueryString();

        return view('kasir.transaksi-penjualan.index', compact('transaksis', 'search', 'tanggalAwal', 'tanggalAkhir'));
    }

    public function create(): View
    {
        $barangs = Barang::with('kategori')
            ->where('stok', '>', 0)
            ->orderBy('nama_barang')
            ->get();

        return view('kasir.transaksi-penjualan.create', compact('barangs'));
    }

    public function store(StoreTransaksiPenjualanRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $items = $validated['items'];
        $metodePembayaran = $validated['metode_pembayaran'];
        $totalBayar = (float) $validated['total_bayar'];

        try {
            DB::transaction(function () use ($validated, $items, $metodePembayaran, $totalBayar) {
                // 1. Hitung total transaksi
                $totalTransaksi = collect($items)->sum(fn($item) => (float) $item['harga_satuan'] * (int) $item['jumlah']);

                // 2. Create header transaksi_penjualan
                $transaksi = TransaksiPenjualan::create([
                    'tanggal' => $validated['tanggal'],
                    'id_kasir' => auth()->id(),
                    'total_transaksi' => $totalTransaksi,
                ]);

                // 3. Loop items - create detail_transaksi, update barang.stok (kurangi), create mutasi_persediaan
                foreach ($items as $item) {
                    $barang = Barang::findOrFail($item['id_barang']);
                    $jumlah = (int) $item['jumlah'];
                    $hargaSatuan = (float) $item['harga_satuan'];
                    $subtotal = $jumlah * $hargaSatuan;

                    // Create detail_transaksi
                    DetailTransaksi::create([
                        'id_transaksi' => $transaksi->id_transaksi,
                        'id_barang' => $barang->id_barang,
                        'jumlah' => $jumlah,
                        'harga_satuan' => $hargaSatuan,
                        'subtotal' => $subtotal,
                    ]);

                    // Update barang.stok (kurangi stok)
                    $stokBaru = $barang->stok - $jumlah;
                    $barang->update(['stok' => $stokBaru]);

                    // Create mutasi_persediaan (jenis_mutasi = 'keluar')
                    MutasiPersediaan::catatBarangKeluar(
                        idBarang: $barang->id_barang,
                        jumlah: $jumlah,
                        stokAkhir: $stokBaru,
                        referensi: (string) $transaksi->id_transaksi,
                        tanggal: $validated['tanggal']
                    );
                }

                // 4. Create pembayaran (1:1 dengan transaksi)
                $statusPembayaran = ($metodePembayaran === 'tunai' && $totalBayar >= $totalTransaksi) ? 'lunas' : 'lunas';
                // Untuk non-tunai otomatis lunas, tunai juga lunas karena validasi sudah pastikan total_bayar >= total_transaksi

                Pembayaran::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'total_bayar' => $totalBayar,
                    'metode_pembayaran' => $metodePembayaran,
                    'status_pembayaran' => $statusPembayaran,
                    'tanggal_bayar' => $validated['tanggal'],
                ]);
            });

            return redirect()
                ->route('kasir.transaksi-penjualan.index')
                ->with('success', 'Transaksi penjualan berhasil dicatat. Stok telah diperbarui.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal mencatat transaksi: ' . $e->getMessage()]);
        }
    }

    public function show(TransaksiPenjualan $transaksiPenjualan): View
    {
        $transaksiPenjualan->load(['kasir', 'detailTransaksi.barang.kategori', 'pembayaran']);
        return view('kasir.transaksi-penjualan.show', compact('transaksiPenjualan'));
    }
}