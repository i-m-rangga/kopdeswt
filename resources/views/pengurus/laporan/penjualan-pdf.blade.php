<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
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
        th { background: #2b6cb0; color: white; font-weight: bold; font-size: 9px; }
        tr:nth-child(even) { background: #f7fafc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .grand-total { font-weight: bold; background: #edf2f7; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; }
        .badge-tunai { background: #c6f6d5; color: #276749; }
        .badge-nontunai { background: #bee3f8; color: #2b6cb0; }
        .footer { margin-top: 20px; text-align: right; font-size: 9px; color: #718096; }
        .summary { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .summary-card { flex: 1; margin: 0 5px; padding: 10px; background: #f7fafc; border-radius: 4px; text-align: center; }
        .summary-card h3 { font-size: 10px; color: #4a5568; margin: 0 0 5px; }
        .summary-card p { font-size: 14px; font-weight: bold; margin: 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>KOPERASI DESA MERAH PUTIH</h1>
        <p>Laporan Penjualan Periode {{ \Carbon\Carbon::parse($filters['tanggalAwal'])->format('d F Y') }} s.d. {{ \Carbon\Carbon::parse($filters['tanggalAkhir'])->format('d F Y') }}</p>
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
    </div>

    <div class="filter-info">
        <table>
            <tr>
                <td><strong>Filter Barang:</strong> {{ $filters['barangId'] ? \App\Models\Barang::find($filters['barangId'])?->nama_barang : 'Semua Barang' }}</td>
                <td><strong>Urutan:</strong> {{ $filters['sortBy'] }}</td>
                <td><strong>Pencarian:</strong> {{ $filters['search'] ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="summary">
        <div class="summary-card">
            <h3>Total Item Transaksi</h3>
            <p>{{ $data->count() }}</p>
        </div>
        <div class="summary-card">
            <h3>Total Qty Terjual</h3>
            <p>{{ number_format($data->sum('jumlah'), 0, ',', '.') }}</p>
        </div>
        <div class="summary-card">
            <h3>Grand Total Penjualan</h3>
            <p style="color: #22c55e;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th style="width: 70px;">Tanggal</th>
                <th style="width: 70px;">No. Transaksi</th>
                <th style="width: 80px;">Kasir</th>
                <th>Nama Barang</th>
                <th style="width: 80px;">Kategori</th>
                <th class="text-center" style="width: 50px;">Satuan</th>
                <th class="text-right" style="width: 70px;">Harga Jual</th>
                <th class="text-center" style="width: 40px;">Qty</th>
                <th class="text-right" style="width: 80px;">Subtotal</th>
                <th class="text-center" style="width: 70px;">Metode Bayar</th>
                <th class="text-right" style="width: 80px;">Total Bayar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['tanggal']->format('d/m/Y') }}</td>
                <td>#{{ $row['id_transaksi'] }}</td>
                <td>{{ $row['kasir_nama'] }}</td>
                <td>{{ $row['nama_barang'] }}</td>
                <td>{{ $row['kategori_nama'] ?? '-' }}</td>
                <td class="text-center">{{ $row['satuan'] }}</td>
                <td class="text-right">Rp {{ number_format($row['harga_satuan'], 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($row['jumlah'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($row['subtotal'], 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($row['metode_pembayaran'])
                        <span class="badge {{ $row['metode_pembayaran'] === 'tunai' ? 'badge-tunai' : 'badge-nontunai' }}">
                            {{ ucfirst($row['metode_pembayaran']) }}
                        </span>
                    @else
                        -
                    @endif
                </td>
                <td class="text-right">
                    @if($row['total_bayar'])
                        Rp {{ number_format($row['total_bayar'], 0, ',', '.') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
            <tr class="grand-total">
                <td colspan="9" class="text-right">GRAND TOTAL</td>
                <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Koperasi Desa Merah Putih - Sistem Informasi Manajemen Penjualan Sembako</p>
        <p>Halaman ini dicetak secara otomatis dari sistem</p>
    </div>
</body>
</html>