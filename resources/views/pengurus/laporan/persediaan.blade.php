@extends('layouts.pengurus')

@section('title', 'Laporan Persediaan (Mutasi Stok)')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Laporan Persediaan (Mutasi Stok)</h2>
        <p class="text-gray-600">Lacak pergerakan stok barang (masuk/keluar) per periode</p>
    </div>
    <div class="flex space-x-2">
        <a href="{{ route('pengurus.laporan.persediaan', array_merge(request()->query(), ['export_excel' => true])) }}"
           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition inline-flex items-center">
            <i class="fas fa-file-excel mr-2"></i> Export Excel
        </a>
        <a href="{{ route('pengurus.laporan.persediaan', array_merge(request()->query(), ['export_pdf' => true])) }}"
           class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition inline-flex items-center">
            <i class="fas fa-file-pdf mr-2"></i> Export PDF
        </a>
    </div>
</div>

<!-- Filter Form -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" action="{{ route('pengurus.laporan.persediaan') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
        <div class="flex items-end gap-2">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition inline-flex items-center">
                <i class="fas fa-search mr-2"></i> Tampilkan
            </button>
            <a href="{{ route('pengurus.laporan.persediaan') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition inline-flex items-center">
                <i class="fas fa-undo mr-2"></i> Reset
            </a>
        </div>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 border-l-4 border-green-500">
        <p class="text-sm text-gray-500">Total Barang Masuk</p>
        <p class="text-2xl font-bold text-green-600">{{ number_format($summary['total_masuk'], 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 border-l-4 border-red-500">
        <p class="text-sm text-gray-500">Total Barang Keluar</p>
        <p class="text-2xl font-bold text-red-600">{{ number_format($summary['total_keluar'], 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Selisih (Masuk - Keluar)</p>
        <p class="text-2xl font-bold {{ $summary['selisih'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
            {{ $summary['selisih'] >= 0 ? '+' : '' }}{{ number_format($summary['selisih'], 0, ',', '.') }}
        </p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 border-l-4 border-purple-500">
        <p class="text-sm text-gray-500">Total Mutasi</p>
        <p class="text-2xl font-bold text-purple-600">{{ $summary['total_mutasi'] }} record</p>
    </div>
</div>

<!-- Data Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    @if($data->isEmpty())
        <div class="p-12 text-center text-gray-500">
            <i class="fas fa-exchange-alt text-4xl mb-2"></i>
            <p>Tidak ada data mutasi stok untuk filter ini</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Referensi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Akhir</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($data as $index => $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-900">{{ $row['tanggal']->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            @if($row['jenis_mutasi'] === 'masuk')
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">MASUK</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full font-medium">KELUAR</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-900 font-mono">{{ $row['referensi'] ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-900 font-medium">{{ $row['nama_barang'] }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-500">{{ $row['kategori_nama'] ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-gray-500">{{ $row['satuan'] }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-right font-medium
                            {{ $row['jenis_mutasi'] === 'masuk' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $row['jenis_mutasi'] === 'masuk' ? '+' : '-' }}{{ number_format($row['jumlah'], 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-gray-900">{{ number_format($row['stok_akhir'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-500">
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
        </div>
    @endif
</div>
@endsection