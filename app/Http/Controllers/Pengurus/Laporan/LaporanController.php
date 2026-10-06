<?php

namespace App\Http\Controllers\Pengurus\Laporan;

use App\Http\Controllers\Controller;
use App\Exports\LaporanPenjualanExport;
use App\Exports\LaporanPersediaanExport;
use App\Models\Barang;
use App\Models\DetailTransaksi;
use App\Models\MutasiPersediaan;
use App\Models\Pembayaran;
use App\Models\TransaksiPenjualan;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    /*
     * ===========================================
     * LAPORAN PENJUALAN
     * ===========================================
     */

    public function penjualan(Request $request): View
    {
        $filters = $this->getPenjualanFilters($request);
        $data = $this->getPenjualanData($filters);
        $grandTotal = $data->sum('subtotal');

        // Handle exports
        if ($request->has('export_pdf')) {
            return $this->exportPenjualanPdf($data, $filters, $grandTotal);
        }
        if ($request->has('export_excel')) {
            return $this->exportPenjualanExcel($data, $filters, $grandTotal);
        }

        // Get filter options
        $barangs = Barang::orderBy('nama_barang')->get(['id_barang', 'nama_barang']);

        return view('pengurus.laporan.penjualan', compact('data', 'filters', 'grandTotal', 'barangs'));
    }

    protected function getPenjualanFilters(Request $request): array
    {
        $tanggalAwal = $request->get('tanggal_awal', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->get('tanggal_akhir', Carbon::today()->format('Y-m-d'));
        $barangId = $request->get('barang_id');
        $sortBy = $request->get('sort_by', 'tanggal_desc');
        $search = $request->get('search');

        return compact('tanggalAwal', 'tanggalAkhir', 'barangId', 'sortBy', 'search');
    }

    protected function getPenjualanData(array $filters): \Illuminate\Support\Collection
    {
        $query = DetailTransaksi::query()
            ->select([
                'transaksi_penjualan.tanggal',
                'transaksi_penjualan.id_transaksi',
                'pengguna.nama as kasir_nama',
                'barang.nama_barang',
                'kategori_barang.nama_kategori as kategori_nama',
                'barang.satuan',
                'detail_transaksi.harga_satuan',
                'detail_transaksi.jumlah',
                'detail_transaksi.subtotal',
                'pembayaran.metode_pembayaran',
                'pembayaran.total_bayar',
            ])
            ->join('transaksi_penjualan', 'detail_transaksi.id_transaksi', '=', 'transaksi_penjualan.id_transaksi')
            ->join('pengguna', 'transaksi_penjualan.id_kasir', '=', 'pengguna.id_pengguna')
            ->join('barang', 'detail_transaksi.id_barang', '=', 'barang.id_barang')
            ->leftJoin('kategori_barang', 'barang.id_kategori', '=', 'kategori_barang.id_kategori')
            ->leftJoin('pembayaran', 'transaksi_penjualan.id_transaksi', '=', 'pembayaran.id_transaksi')
            ->whereBetween('transaksi_penjualan.tanggal', [$filters['tanggalAwal'], $filters['tanggalAkhir']]);

        // Filter barang
        if ($filters['barangId']) {
            $query->where('detail_transaksi.id_barang', $filters['barangId']);
        }

        // Search barang nama
        if ($filters['search']) {
            $query->where('barang.nama_barang', 'like', "%{$filters['search']}%");
        }

        // Sorting
        match ($filters['sortBy']) {
            'tanggal_asc' => $query->orderBy('transaksi_penjualan.tanggal', 'asc'),
            'tanggal_desc' => $query->orderBy('transaksi_penjualan.tanggal', 'desc'),
            'barang_asc' => $query->orderBy('barang.nama_barang', 'asc'),
            'barang_desc' => $query->orderBy('barang.nama_barang', 'desc'),
            'total_asc' => $query->orderBy('detail_transaksi.subtotal', 'asc'),
            'total_desc' => $query->orderBy('detail_transaksi.subtotal', 'desc'),
            default => $query->orderBy('transaksi_penjualan.tanggal', 'desc'),
        };

        return $query->get();
    }

    protected function exportPenjualanPdf(\Illuminate\Support\Collection $data, array $filters, float $grandTotal)
    {
        $pdf = Pdf::loadView('pengurus.laporan.penjualan-pdf', compact('data', 'filters', 'grandTotal'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('laporan-penjualan-' . $filters['tanggalAwal'] . '-sd-' . $filters['tanggalAkhir'] . '.pdf');
    }

    protected function exportPenjualanExcel(\Illuminate\Support\Collection $data, array $filters, float $grandTotal)
    {
        return Excel::download(new LaporanPenjualanExport($data, $filters, $grandTotal), 'laporan-penjualan-' . $filters['tanggalAwal'] . '-sd-' . $filters['tanggalAkhir'] . '.xlsx');
    }

    /*
     * ===========================================
     * LAPORAN PERSEDIAAN (MUTASI STOK)
     * ===========================================
     */

    public function persediaan(Request $request): View
    {
        $filters = $this->getPersediaanFilters($request);
        $data = $this->getPersediaanData($filters);
        $summary = $this->getPersediaanSummary($data);

        // Handle exports
        if ($request->has('export_pdf')) {
            return $this->exportPersediaanPdf($data, $filters, $summary);
        }
        if ($request->has('export_excel')) {
            return $this->exportPersediaanExcel($data, $filters, $summary);
        }

        // Get filter options
        $barangs = Barang::orderBy('nama_barang')->get(['id_barang', 'nama_barang']);

        return view('pengurus.laporan.persediaan', compact('data', 'filters', 'summary', 'barangs'));
    }

    protected function getPersediaanFilters(Request $request): array
    {
        $tanggalAwal = $request->get('tanggal_awal', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->get('tanggal_akhir', Carbon::today()->format('Y-m-d'));
        $barangId = $request->get('barang_id');

        return compact('tanggalAwal', 'tanggalAkhir', 'barangId');
    }

    protected function getPersediaanData(array $filters): \Illuminate\Support\Collection
    {
        $query = MutasiPersediaan::query()
            ->select([
                'mutasi_persediaan.tanggal',
                'mutasi_persediaan.jenis_mutasi',
                'mutasi_persediaan.jumlah',
                'mutasi_persediaan.stok_akhir',
                'mutasi_persediaan.referensi',
                'barang.nama_barang',
                'kategori_barang.nama_kategori as kategori_nama',
                'barang.satuan',
            ])
            ->join('barang', 'mutasi_persediaan.id_barang', '=', 'barang.id_barang')
            ->leftJoin('kategori_barang', 'barang.id_kategori', '=', 'kategori_barang.id_kategori')
            ->whereBetween('mutasi_persediaan.tanggal', [$filters['tanggalAwal'], $filters['tanggalAkhir']]);

        if ($filters['barangId']) {
            $query->where('mutasi_persediaan.id_barang', $filters['barangId']);
        }

        return $query->orderBy('mutasi_persediaan.tanggal', 'asc')
            ->orderBy('mutasi_persediaan.id_mutasi', 'asc')
            ->get();
    }

    protected function getPersediaanSummary(\Illuminate\Support\Collection $data): array
    {
        $totalMasuk = $data->where('jenis_mutasi', 'masuk')->sum('jumlah');
        $totalKeluar = $data->where('jenis_mutasi', 'keluar')->sum('jumlah');

        return [
            'total_masuk' => $totalMasuk,
            'total_keluar' => $totalKeluar,
            'selisih' => $totalMasuk - $totalKeluar,
            'total_mutasi' => $data->count(),
        ];
    }

    protected function exportPersediaanPdf(\Illuminate\Support\Collection $data, array $filters, array $summary)
    {
        $pdf = Pdf::loadView('pengurus.laporan.persediaan-pdf', compact('data', 'filters', 'summary'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('laporan-persediaan-' . $filters['tanggalAwal'] . '-sd-' . $filters['tanggalAkhir'] . '.pdf');
    }

    protected function exportPersediaanExcel(\Illuminate\Support\Collection $data, array $filters, array $summary)
    {
        return Excel::download(new LaporanPersediaanExport($data, $filters, $summary), 'laporan-persediaan-' . $filters['tanggalAwal'] . '-sd-' . $filters['tanggalAkhir'] . '.xlsx');
    }
}