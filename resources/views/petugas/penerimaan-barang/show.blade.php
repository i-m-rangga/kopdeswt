@extends('layouts.petugas')

@section('title', 'Detail Penerimaan Barang')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Detail Penerimaan Barang</h2>
        <p class="text-gray-600">No. Penerimaan: #{{ $penerimaanBarang->id_penerimaan }}</p>
    </div>
    <div class="space-x-2">
        <a href="{{ route('petugas.penerimaan-barang.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Info Header -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Penerimaan</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">Tanggal</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $penerimaanBarang->tanggal->format('d F Y') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Pemasok</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $penerimaanBarang->pemasok->nama_pemasok }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Kontak Pemasok</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $penerimaanBarang->pemasok->kontak ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Alamat Pemasok</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $penerimaanBarang->pemasok->alamat ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Petugas Penerima</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $penerimaanBarang->petugas->nama }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Total Item</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $penerimaanBarang->detailPenerimaan->count() }} item</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Total Nilai</dt>
                <dd class="mt-1 text-lg text-gray-900 font-semibold text-green-600">
                    Rp {{ number_format($penerimaanBarang->detailPenerimaan->sum(fn($d) => $d->jumlah * $d->harga_beli), 0, ',', '.') }}
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Dicatat Pada</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $penerimaanBarang->created_at->format('d F Y H:i') }}</dd>
            </div>
        </dl>
    </div>

    <!-- Summary Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan</h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-500">Jenis Transaksi</span>
                <span class="font-medium text-green-600">Barang Masuk</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Status</span>
                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">Selesai</span>
            </div>
            <div class="flex justify-between border-t pt-3">
                <span class="text-gray-500">Total Item</span>
                <span class="font-semibold">{{ $penerimaanBarang->detailPenerimaan->count() }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Total Nilai</span>
                <span class="font-semibold text-green-600">Rp {{ number_format($penerimaanBarang->detailPenerimaan->sum(fn($d) => $d->jumlah * $d->harga_beli), 0, ',', '.') }}</span>
            </div>
        </div>
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
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Beli</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($penerimaanBarang->detailPenerimaan as $index => $detail)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-medium">{{ $detail->barang->nama_barang }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $detail->barang->kategori->nama_kategori ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-gray-500">{{ $detail->barang->satuan }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-gray-900">{{ number_format($detail->jumlah, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-gray-900">Rp {{ number_format($detail->harga_beli, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-gray-900 font-medium">
                        Rp {{ number_format($detail->jumlah * $detail->harga_beli, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
                <tr class="bg-gray-50 font-semibold">
                    <td colspan="6" class="px-6 py-4 text-right">TOTAL</td>
                    <td class="px-6 py-4 text-right text-green-600">
                        Rp {{ number_format($penerimaanBarang->detailPenerimaan->sum(fn($d) => $d->jumlah * $d->harga_beli), 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection