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

class LaporanPersediaanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $data;
    protected $filters;
    protected $summary;

    public function __construct(Collection $data, array $filters, array $summary)
    {
        $this->data = $data;
        $this->filters = $filters;
        $this->summary = $summary;
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
            'Jenis Mutasi',
            'No. Referensi',
            'Nama Barang',
            'Kategori',
            'Satuan',
            'Jumlah',
            'Stok Akhir',
            'Keterangan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $jenisLabel = $row['jenis_mutasi'] === 'masuk' ? 'MASUK' : 'KELUAR';
        $keterangan = $row['jenis_mutasi'] === 'masuk' 
            ? 'Penerimaan Barang #' . $row['referensi']
            : 'Penjualan #' . $row['referensi'];

        return [
            $no,
            $row['tanggal']->format('d/m/Y'),
            $jenisLabel,
            $row['referensi'],
            $row['nama_barang'],
            $row['kategori_nama'] ?? '-',
            $row['satuan'],
            $row['jumlah'],
            $row['stok_akhir'],
            $keterangan,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->data->count() + 2;

        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '22C55E']], 'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]],
            $lastRow => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']]],
        ];
    }

    public function title(): string
    {
        return 'Laporan Persediaan';
    }
}