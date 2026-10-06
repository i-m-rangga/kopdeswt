@extends('layouts.kasir')

@section('title', 'Detail Transaksi Penjualan')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Detail Transaksi Penjualan</h2>
        <p class="text-gray-600">No. Transaksi: #{{ $transaksiPenjualan->id_transaksi }}</p>
    </div>
    <div class="space-x-2">
        <a href="{{ route('kasir.transaksi-penjualan.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Info Header -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Transaksi</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">Tanggal</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $transaksiPenjualan->tanggal->format('d F Y') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Kasir</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $transaksiPenjualan->kasir->nama }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Total Item</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $transaksiPenjualan->detailTransaksi->count() }} item</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Total Qty</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $transaksiPenjualan->detailTransaksi->sum('jumlah') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Subtotal</dt>
                <dd class="mt-1 text-lg text-gray-900">Rp {{ number_format($transaksiPenjualan->detailTransaksi->sum('subtotal'), 0, ',', '.') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Total Transaksi</dt>
                <dd class="mt-1 text-lg font-semibold text-gray-900">Rp {{ number_format($transaksiPenjualan->total_transaksi, 0, ',', '.') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Dicatat Pada</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $transaksiPenjualan->created_at->format('d F Y H:i') }}</dd>
            </div>
        </dl>
    </div>

    <!-- Payment Info -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Pembayaran</h3>
        @if($transaksiPenjualan->pembayaran)
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Metode</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $transaksiPenjualan->pembayaran->metode_pembayaran === 'tunai' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($transaksiPenjualan->pembayaran->metode_pembayaran) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Total Bayar</dt>
                    <dd class="mt-1 text-lg text-gray-900">Rp {{ number_format($transaksiPenjualan->pembayaran->total_bayar, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $transaksiPenjualan->pembayaran->status_pembayaran === 'lunas' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($transaksiPenjualan->pembayaran->status_pembayaran) }}
                        </span>
                    </dd>
                </div>
                @if($transaksiPenjualan->pembayaran->metode_pembayaran === 'tunai')
                    <div class="border-t pt-3">
                        <dt class="text-sm font-medium text-gray-500">Kembalian</dt>
                        <dd class="mt-1 text-lg font-semibold text-green-600">
                            Rp {{ number_format($transaksiPenjualan->pembayaran->total_bayar - $transaksiPenjualan->total_transaksi, 0, ',', '.') }}
                        </dd>
                    </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tanggal Bayar</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $transaksiPenjualan->pembayaran->tanggal_bayar->format('d F Y') }}</dd>
                </div>
            </dl>
        @else
            <p class="text-gray-500 text-center py-4">Belum ada data pembayaran</p>
        @endif
    </div>
</div>

<!-- Detail Items Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Detail Barang</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Jual</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($transaksiPenjualan->detailTransaksi as $index => $detail)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-medium">{{ $detail->barang->nama_barang }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $detail->barang->kategori->nama_kategori ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-gray-500">{{ $detail->barang->satuan }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-gray-900">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-gray-900">{{ number_format($detail->jumlah, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-gray-900 font-medium">
                        Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
                <tr class="bg-gray-50 font-semibold">
                    <td colspan="5" class="px-6 py-4 text-right">TOTAL</td>
                    <td class="px-6 py-4 text-center">{{ $transaksiPenjualan->detailTransaksi->sum('jumlah') }}</td>
                    <td class="px-6 py-4 text-right text-green-600">
                        Rp {{ number_format($transaksiPenjualan->detailTransaksi->sum('subtotal'), 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection