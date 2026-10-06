<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\DetailTransaksi;
use App\Models\TransaksiPenjualan;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = Carbon::today();
        $thirtyDaysAgo = Carbon::today()->subDays(29); // 30 days including today

        // ==================== KPI Cards ====================
        
        // 1. Total Penjualan Hari Ini
        $totalPenjualanHariIni = TransaksiPenjualan::whereDate('tanggal', $today)
            ->sum('total_transaksi');

        // 2. Jumlah Transaksi Hari Ini
        $jumlahTransaksiHariIni = TransaksiPenjualan::whereDate('tanggal', $today)
            ->count();

        // 3. Jumlah Barang Stok Rendah (stok <= stok_minimum)
        $jumlahBarangStokRendah = Barang::whereRaw('stok <= stok_minimum')
            ->count();

        // ==================== Grafik Penjualan 30 Hari Terakhir ====================
        
        $penjualan30Hari = TransaksiPenjualan::select(
                DB::raw('DATE(tanggal) as tanggal'),
                DB::raw('SUM(total_transaksi) as total'),
                DB::raw('COUNT(*) as jumlah_transaksi')
            )
            ->whereDate('tanggal', '>=', $thirtyDaysAgo)
            ->whereDate('tanggal', '<=', $today)
            ->groupBy(DB::raw('DATE(tanggal)'))
            ->orderBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        // Generate 30 days array with zero-filled data
        $labelsPenjualan = [];
        $dataPenjualan = [];
        $dataJumlahTransaksi = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $labelsPenjualan[] = Carbon::parse($date)->format('d M');
            $dataPenjualan[] = $penjualan30Hari[$date]->total ?? 0;
            $dataJumlahTransaksi[] = $penjualan30Hari[$date]->jumlah_transaksi ?? 0;
        }

        // ==================== Grafik Produk Terlaris ====================
        
        $produkTerlaris = DetailTransaksi::select(
                'barang.nama_barang',
                'barang.satuan',
                DB::raw('SUM(detail_transaksi.jumlah) as total_terjual'),
                DB::raw('SUM(detail_transaksi.subtotal) as total_pendapatan')
            )
            ->join('barang', 'detail_transaksi.id_barang', '=', 'barang.id_barang')
            ->join('transaksi_penjualan', 'detail_transaksi.id_transaksi', '=', 'transaksi_penjualan.id_transaksi')
            ->whereDate('transaksi_penjualan.tanggal', '>=', $thirtyDaysAgo)
            ->groupBy('barang.id_barang', 'barang.nama_barang', 'barang.satuan')
            ->orderByDesc('total_terjual')
            ->limit(10)
            ->get();

        $labelsProdukTerlaris = $produkTerlaris->pluck('nama_barang')->toArray();
        $dataProdukTerlaris = $produkTerlaris->pluck('total_terjual')->toArray();

        // ==================== Tabel Barang Stok Rendah ====================
        
        $barangStokRendah = Barang::with('kategori')
            ->whereRaw('stok <= stok_minimum')
            ->orderBy('stok', 'asc')
            ->get();

        return view('pengurus.dashboard', compact(
            'totalPenjualanHariIni',
            'jumlahTransaksiHariIni',
            'jumlahBarangStokRendah',
            'labelsPenjualan',
            'dataPenjualan',
            'dataJumlahTransaksi',
            'labelsProdukTerlaris',
            'dataProdukTerlaris',
            'barangStokRendah'
        ));
    }
}