<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class LaporanPenjualanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $data;
    protected $filters;
    protected $grandTotal;

    public function __construct(Collection $data, array $filters, float $grandTotal)
    {
        $this->data = $data;
        $this->filters = $filters;
        $this->grandTotal = $grandTotal;
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'No. Transaksi',
            'Kasir',
            'Nama Barang',
            'Kategori',
            'Satuan',
            'Harga Jual',
            'Qty',
            'Subtotal',
            'Metode Bayar',
            'Total Bayar',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row['tanggal']->format('d/m/Y'),
            $row['id_transaksi'],
            $row['kasir_nama'],
            $row['nama_barang'],
            $row['kategori_nama'] ?? '-',
            $row['satuan'],
            $row['harga_satuan'],
            $row['jumlah'],
            $row['subtotal'],
            $row['metode_pembayaran'] ?? '-',
            $row['total_bayar'] ?? 0,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->data->count() + 2; // +2 for header and grand total row

        return [
            // Header style
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']], 'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]],
            // Grand total row
            $lastRow => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']]],
        ];
    }

    public function title(): string
    {
        return 'Laporan Penjualan';
    }
}