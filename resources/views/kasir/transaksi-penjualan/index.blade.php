@extends('layouts.kasir')

@section('title', 'Transaksi Penjualan')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Transaksi Penjualan</h2>
        <p class="text-gray-600">Riwayat transaksi penjualan</p>
    </div>
    <a href="{{ route('kasir.transaksi-penjualan.create') }}"
       class="btn-primary inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
        <i class="fas fa-plus mr-2"></i> Transaksi Baru
    </a>
</div>

<!-- Search & Filter Form -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('kasir.transaksi-penjualan.index') }}" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px] max-w-md">
            <label for="search" class="sr-only">Cari kasir</label>
            <input type="text" name="search" id="search"
                   value="{{ $search }}"
                   placeholder="Cari nama kasir..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="min-w-[150px]">
            <label for="tanggal_awal" class="sr-only">Tanggal awal</label>
            <input type="date" name="tanggal_awal" id="tanggal_awal"
                   value="{{ $tanggalAwal }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="min-w-[150px]">
            <label for="tanggal_akhir" class="sr-only">Tanggal akhir</label>
            <input type="date" name="tanggal_akhir" id="tanggal_akhir"
                   value="{{ $tanggalAkhir }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-search mr-1"></i> Filter
        </button>
        @if($search || $tanggalAwal || $tanggalAkhir)
            <a href="{{ route('kasir.transaksi-penjualan.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Reset
            </a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    @if($transaksis->isEmpty())
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-receipt text-4xl mb-2"></i>
            <p>Tidak ada data transaksi penjualan</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Transaksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kasir</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembayaran</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($transaksis as $transaksi)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-mono">#{{ $transaksi->id_transaksi }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $transaksi->tanggal->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ $transaksi->kasir->nama }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-gray-900">{{ $transaksi->detailTransaksi->count() }} item</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-gray-900 font-medium">
                            Rp {{ number_format($transaksi->total_transaksi, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($transaksi->pembayaran)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $transaksi->pembayaran->metode_pembayaran === 'tunai' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($transaksi->pembayaran->metode_pembayaran) }}
                                </span>
                                <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $transaksi->pembayaran->status_pembayaran === 'lunas' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($transaksi->pembayaran->status_pembayaran) }}
                                </span>
                            @else
                                <span class="text-gray-400">Belum bayar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="{{ route('kasir.transaksi-penjualan.show', $transaksi) }}"
                               class="text-blue-600 hover:text-blue-900" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-gray-200">
            {{ $transaksis->links() }}
        </div>
    @endif
</div>
@endsection