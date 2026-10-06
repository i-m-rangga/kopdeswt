<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Persediaan (Mutasi Stok)</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 18px; margin: 0; color: #1e3a5f; }
        .header p { margin: 5px 0; font-size: 11px; color: #4a5568; }
        .filter-info { background: #f7fafc; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 10px; }
        .filter-info table { width: 100%; }
        .filter-info td { padding: 2px 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; }
        th { background: #276749; color: white; font-weight: bold; font-size: 9px; }
        tr:nth-child(even) { background: #f7fafc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; }
        .badge-masuk { background: #c6f6d5; color: #276749; }
        .badge-keluar { background: #fed7d7; color: #c53030; }
        .footer { margin-top: 20px; text-align: right; font-size: 9px; color: #718096; }
        .summary { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .summary-card { flex: 1; margin: 0 5px; padding: 10px; background: #f7fafc; border-radius: 4px; text-align: center; }
        .summary-card h3 { font-size: 10px; color: #4a5568; margin: 0 0 5px; }
        .summary-card p { font-size: 14px; font-weight: bold; margin: 0; }
        .text-green { color: #276749; }
        .text-red { color: #c53030; }
        .text-blue { color: #2b6cb0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>KOPERASI DESA MERAH PUTIH</h1>
        <p>Laporan Persediaan (Mutasi Stok) Periode {{ \Carbon\Carbon::parse($filters['tanggalAwal'])->format('d F Y') }} s.d. {{ \Carbon\Carbon::parse($filters['tanggalAkhir'])->format('d F Y') }}</p>
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
    </div>

    <div class="filter-info">
        <table>
            <tr>
                <td><strong>Filter Barang:</strong> {{ $filters['barangId'] ? \App\Models\Barang::find($filters['barangId'])?->nama_barang : 'Semua Barang' }}</td>
            </tr>
        </table>
    </div>

    <div class="summary">
        <div class="summary-card" style="border-left: 4px solid #38a169;">
            <h3>Total Barang Masuk</h3>
            <p class="text-green">{{ number_format($summary['total_masuk'], 0, ',', '.') }}</p>
        </div>
        <div class="summary-card" style="border-left: 4px solid #e53e3e;">
            <h3>Total Barang Keluar</h3>
            <p class="text-red">{{ number_format($summary['total_keluar'], 0, ',', '.') }}</p>
        </div>
        <div class="summary-card" style="border-left: 4px solid #3182ce;">
            <h3>Selisih (Masuk - Keluar)</h3>
            <p class="{{ $summary['selisih'] >= 0 ? 'text-blue' : 'text-red' }}">
                {{ $summary['selisih'] >= 0 ? '+' : '' }}{{ number_format($summary['selisih'], 0, ',', '.') }}
            </p>
        </div>
        <div class="summary-card" style="border-left: 4px solid #805ad5;">
            <h3>Total Mutasi</h3>
            <p class="text-purple">{{ $summary['total_mutasi'] }} record</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th style="width: 70px;">Tanggal</th>
                <th class="text-center" style="width: 55px;">Jenis</th>
                <th style="width: 80px;">No. Referensi</th>
                <th>Nama Barang</th>
                <th style="width: 80px;">Kategori</th>
                <th class="text-center" style="width: 50px;">Satuan</th>
                <th class="text-right" style="width: 60px;">Jumlah</th>
                <th class="text-right" style="width: 65px;">Stok Akhir</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['tanggal']->format('d/m/Y') }}</td>
                <td class="text-center">
                    @if($row['jenis_mutasi'] === 'masuk')
                        <span class="badge badge-masuk">MASUK</span>
                    @else
                        <span class="badge badge-keluar">KELUAR</span>
                    @endif
                </td>
                <td>{{ $row['referensi'] ?? '-' }}</td>
                <td>{{ $row['nama_barang'] }}</td>
                <td>{{ $row['kategori_nama'] ?? '-' }}</td>
                <td class="text-center">{{ $row['satuan'] }}</td>
                <td class="text-right {{ $row['jenis_mutasi'] === 'masuk' ? 'text-green' : 'text-red' }}">
                    {{ $row['jenis_mutasi'] === 'masuk' ? '+' : '-' }}{{ number_format($row['jumlah'], 0, ',', '.') }}
                </td>
                <td class="text-right">{{ number_format($row['stok_akhir'], 0, ',', '.') }}</td>
                <td>
                    @if($row['jenis_mutasi'] === 'masuk')
                        Penerimaan Barang #{{ $row['referensi'] }}
                    @else
                        Penjualan #{{ $row['referensi'] }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Koperasi Desa Merah Putih - Sistem Informasi Manajemen Penjualan Sembako</p>
        <p>Halaman ini dicetak secara otomatis dari sistem</p>
    </div>
</body>
</html>