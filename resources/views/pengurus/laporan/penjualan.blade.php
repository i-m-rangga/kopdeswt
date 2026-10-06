@extends('layouts.pengurus')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Laporan Penjualan</h2>
        <p class="text-gray-600">Filter dan lihat detail penjualan per periode</p>
    </div>
    <div class="flex space-x-2">
        <a href="{{ route('pengurus.laporan.penjualan', array_merge(request()->query(), ['export_excel' => true])) }}"
           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition inline-flex items-center">
            <i class="fas fa-file-excel mr-2"></i> Export Excel
        </a>
        <a href="{{ route('pengurus.laporan.penjualan', array_merge(request()->query(), ['export_pdf' => true])) }}"
           class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition inline-flex items-center">
            <i class="fas fa-file-pdf mr-2"></i> Export PDF
        </a>
    </div>
</div>

<!-- Filter Form -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" action="{{ route('pengurus.laporan.penjualan') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div>
            <label for="tanggal_awal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Awal <span class="text-red-500">*</span></label>
            <input type="date" name="tanggal_awal" id="tanggal_awal"
                   value="{{ $filters['tanggalAwal'] }}"
                   required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir <span class="text-red-500">*</span></label>
            <input type="date" name="tanggal_akhir" id="tanggal_akhir"
                   value="{{ $filters['tanggalAkhir'] }}"
                   required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label for="barang_id" class="block text-sm font-medium text-gray-700 mb-1">Filter Barang</label>
            <select name="barang_id" id="barang_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Barang</option>
                @foreach($barangs as $barang)
                    <option value="{{ $barang->id_barang }}" {{ $filters['barangId'] == $barang->id_barang ? 'selected' : '' }}>
                        {{ $barang->nama_barang }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">Urutkan</label>
            <select name="sort_by" id="sort_by"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="tanggal_desc" {{ $filters['sortBy'] == 'tanggal_desc' ? 'selected' : '' }}>Tanggal Terbaru</option>
                <option value="tanggal_asc" {{ $filters['sortBy'] == 'tanggal_asc' ? 'selected' : '' }}>Tanggal Terlama</option>
                <option value="barang_asc" {{ $filters['sortBy'] == 'barang_asc' ? 'selected' : '' }}>Nama Barang (A-Z)</option>
                <option value="barang_desc" {{ $filters['sortBy'] == 'barang_desc' ? 'selected' : '' }}>Nama Barang (Z-A)</option>
                <option value="total_asc" {{ $filters['sortBy'] == 'total_asc' ? 'selected' : '' }}>Total Terkecil</option>
                <option value="total_desc" {{ $filters['sortBy'] == 'total_desc' ? 'selected' : '' }}>Total Terbesar</option>
            </select>
        </div>
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Nama Barang</label>
            <input type="text" name="search" id="search"
                   value="{{ $filters['search'] }}"
                   placeholder="Ketik nama barang..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="lg:col-span-5 flex gap-2 pt-2">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition inline-flex items-center">
                <i class="fas fa-search mr-2"></i> Tampilkan
            </button>
            <a href="{{ route('pengurus.laporan.penjualan') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition inline-flex items-center">
                <i class="fas fa-undo mr-2"></i> Reset
            </a>
        </div>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <p class="text-sm text-gray-500">Total Item Transaksi</p>
        <p class="text-2xl font-bold text-gray-900">{{ $data->count() }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <p class="text-sm text-gray-500">Total Qty Terjual</p>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($data->sum('jumlah'), 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 border-l-4 border-green-500">
        <p class="text-sm text-gray-500">Grand Total Penjualan</p>
        <p class="text-2xl font-bold text-green-600">Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
    </div>
</div>

<!-- Data Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    @if($data->isEmpty())
        <div class="p-12 text-center text-gray-500">
            <i class="fas fa-receipt text-4xl mb-2"></i>
            <p>Tidak ada data penjualan untuk filter ini</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Transaksi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kasir</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Jual</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Metode Bayar</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Bayar</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($data as $index => $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-900">{{ $row['tanggal']->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-900 font-mono">#{{ $row['id_transaksi'] }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-500">{{ $row['kasir_nama'] }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-900 font-medium">{{ $row['nama_barang'] }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-500">{{ $row['kategori_nama'] ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-gray-500">{{ $row['satuan'] }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-gray-900">Rp {{ number_format($row['harga_satuan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-gray-900">{{ number_format($row['jumlah'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-right font-medium text-gray-900">Rp {{ number_format($row['subtotal'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            @if($row['metode_pembayaran'])
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $row['metode_pembayaran'] === 'tunai' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($row['metode_pembayaran']) }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-gray-900">
                            @if($row['total_bayar'])
                                Rp {{ number_format($row['total_bayar'], 0, ',', '.') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    <!-- Grand Total Row -->
                    <tr class="bg-gray-50 font-bold">
                        <td colspan="9" class="px-4 py-3 text-right">GRAND TOTAL</td>
                        <td class="px-4 py-3 text-right text-green-600">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                        <td colspan="2" class="px-4 py-3"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection